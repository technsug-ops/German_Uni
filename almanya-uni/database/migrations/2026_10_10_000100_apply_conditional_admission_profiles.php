<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Şartlı kabul paketi: 9 üniversite profilinin (Bremen, Duisburg-Essen, Marburg, TU Clausthal, TU Dortmund, Hamburg,
 * TU Braunschweig, Paderborn, RWTH Aachen) TR/EN/DE içerik bloklarını resmî kaynaklarla doğrulanmış metne çevirir.
 *
 * Neden: profiller ve /universities/collections/conditional-admission-universities koleksiyonu resmî sayfalarla
 * çelişiyordu (ör. Bremen "neredeyse herkese şartlı kabul" — resmî FAQ: "nicht möglich"; RWTH "şartlı kabul verebilir" —
 * resmî: "derzeit keine studienvorbereitende Deutschkurse"; Paderborn "uni-assist üyesi değil" — resmî: portal veya
 * uni-assist; TU Dortmund vize SSS'i bozuk). Kaynaklar ve kontrol tarihi (2026-10-10) veri dosyasında.
 *
 * Dönüşüm (her dil aynı): intro → yeni metin + seo_title/seo_description + editorial_lock; quick_facts → program sayısı ve
 * uni-assist satırları çıkar, doğrulanmış 2 satır eklenir, ardından 4 yeni bölüm; faq ve cta yenilenir; doğrulanmamış
 * katalog sayıları (programs_summary, Braunschweig tablosu) ve resmîyle çelişen bölümler kaldırılır; kalan bölümlerden
 * "şartlı kabul" varsayan cümleler (yurt cümleleri) silinir; schema_jsonld açıklaması yeni girişe eşitlenir.
 *
 * Güvenlik: üç dil kolonunun blok tipi dizisi veri dosyasındaki beklenenle birebir aynı değilse o üniversite atlanır
 * (log). Herhangi bir blok editorial_lock taşıyorsa zaten uygulanmıştır → no-op. Her üniversite tek transaction.
 */
return new class extends Migration
{
    private const FILE = 'database/data/university-content/conditional-admission-profiles-2026-10-10.json';

    private const COLUMNS = ['tr' => 'content_blocks', 'en' => 'content_blocks_en', 'de' => 'content_blocks_de'];

    private const FACT_DROP = '/program|studieng|uni-?assist|almanyauni/iu';

    private const CONDITIONAL_SENTENCE = '/şartlı kabul|conditional admission|bedingte[nrm]?\s+zulassung/iu';

    public function up(): void
    {
        if (! Schema::hasTable('universities') || ! is_file(base_path(self::FILE))) {
            return;
        }
        $data = json_decode(file_get_contents(base_path(self::FILE)), true);

        foreach ($data['universities'] as $slug => $spec) {
            $row = DB::table('universities')->where('slug', $slug)->first();
            if (! $row) {
                Log::warning("conditional-admission-profiles: $slug bulunamadı, atlandı");
                continue;
            }

            $new = [];
            foreach (self::COLUMNS as $loc => $col) {
                $blocks = json_decode((string) $row->{$col}, true);
                if (! is_array($blocks)) {
                    Log::warning("conditional-admission-profiles: $slug $col boş, atlandı");
                    continue 2;
                }
                if (collect($blocks)->contains(fn ($b) => is_array($b) && ! empty($b['editorial_lock']))) {
                    continue 2; // zaten uygulanmış
                }
                if (array_map(fn ($b) => $b['type'] ?? null, $blocks) !== $spec['expected_types']) {
                    Log::warning("conditional-admission-profiles: $slug $col blok yapısı beklenenden farklı, atlandı");
                    continue 2;
                }
                $new[$col] = $this->transform($blocks, $spec, $spec['locales'][$loc], $data['checked'], $loc);
            }

            DB::transaction(function () use ($row, $new) {
                $update = ['updated_at' => now()];
                foreach ($new as $col => $blocks) {
                    $update[$col] = json_encode($blocks, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
                DB::table('universities')->where('id', $row->id)->update($update);
            });
        }
    }

    private function transform(array $blocks, array $spec, array $t, string $checked, string $loc): array
    {
        $out = [];
        foreach ($blocks as $i => $b) {
            $type = $b['type'] ?? null;
            if (in_array($i, $spec['remove_indexes'], true) || $type === 'programs_summary') {
                continue;
            }

            switch ($type) {
                case 'intro':
                    $b['body_md'] = $t['intro'];
                    $b['seo_title'] = $t['seo_title'];
                    $b['seo_description'] = $t['seo_description'];
                    $b['editorial_lock'] = true;
                    $b['verified_at'] = $checked;
                    $b['sources'] = $spec['sources'];
                    $out[] = $b;
                    break;

                case 'quick_facts':
                    $items = array_values(array_filter($b['items'] ?? [], fn ($it) => ! preg_match(self::FACT_DROP, (string) ($it['label'] ?? ''))));
                    foreach ($t['facts'] as [$label, $value]) {
                        $items[] = ['label' => $label, 'value' => $value];
                    }
                    $b['items'] = $items;
                    $out[] = $b;
                    foreach ($t['sections'] as [$h, $body]) {
                        $out[] = ['type' => 'section', 'h' => $h, 'body_md' => $body, 'verified_at' => $checked];
                    }
                    break;

                case 'section':
                    $b['body_md'] = $this->dropConditionalSentences((string) ($b['body_md'] ?? ''));
                    $out[] = $b;
                    break;

                case 'faq':
                    $b['h'] = $t['faq_h'];
                    $b['items'] = array_map(fn ($qa) => ['q' => $qa[0], 'a' => $qa[1]], $t['faq']);
                    $out[] = $b;
                    break;

                case 'cta':
                    $b['h'] = null;
                    $b['body_md'] = $t['cta'];
                    $out[] = $b;
                    break;

                case 'external_links':
                    $urls = array_column($b['items'] ?? [], 'url');
                    if (! in_array($spec['official']['url'], $urls, true)) {
                        $b['items'][] = ['url' => $spec['official']['url'], 'type' => 'official', 'label' => $spec['official']['label'][$loc]];
                    }
                    $out[] = $b;
                    break;

                case 'schema_jsonld':
                    if (isset($b['data']) && is_array($b['data'])) {
                        $b['data']['description'] = $this->plain($t['intro']);
                    }
                    $out[] = $b;
                    break;

                default:
                    $out[] = $b;
            }
        }

        return $out;
    }

    private function dropConditionalSentences(string $md): string
    {
        $paras = preg_split('/\n{2,}/u', $md);
        $kept = [];
        foreach ($paras as $p) {
            $sentences = preg_split('/(?<=[.!?])\s+/u', $p);
            $sentences = array_filter($sentences, fn ($s) => ! preg_match(self::CONDITIONAL_SENTENCE, $s));
            if ($sentences) {
                $kept[] = implode(' ', $sentences);
            }
        }

        return implode("\n\n", $kept);
    }

    private function plain(string $md): string
    {
        $s = preg_replace('/\[([^\]]+)\]\([^)]+\)/u', '$1', $md);
        $s = str_replace(['**', '*'], '', $s);

        return trim(preg_replace('/\s+/u', ' ', $s));
    }

    public function down(): void
    {
        // İçerik verisi; geri dönüş için önceki yedekler kullanılır.
    }
};
