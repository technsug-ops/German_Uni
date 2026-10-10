<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * SEO Growth Batch 2: TU Berlin, TUM, Universität Heidelberg, Universität Freiburg ve TU Darmstadt profillerinin
 * TR/EN/DE içerik bloklarını resmî kaynaklarla doğrulanmış başvuru içeriğine çevirir (veri: database/data/
 * university-content/university-profiles-batch2-2026-10-10.json; kaynaklar ve kontrol tarihi dosyada).
 *
 * Dönüşüm Batch 1 (2026_10_10_000100) ile aynı: intro → doğrulanmış giriş + seo_title/seo_description +
 * editorial_lock; quick_facts → katalog sayıları/uni-assist satırları çıkar, doğrulanmış satırlar + yeni bölümler;
 * faq/cta yenilenir; programs_summary ve çelişen bölümler kaldırılır; kalan bölümlerden şartlı kabul varsayan cümleler
 * silinir; schema_jsonld açıklaması güncellenir. Prod'da içerik bloğu olmayan dil kolonu (TU Berlin TR) için bloklar
 * sıfırdan kurulur.
 *
 * Güvenlik (Batch 1'den sıkı): önce 5 kurum × 3 dil doğrulanır. Beklenmeyen durum (kurumlardan biri yok, blok yapısı
 * farklı, kısmen uygulanmış) varsa RuntimeException → hiçbir şey yazılmaz, migration "Pending" kalır. Beş kurumun
 * hiçbirinin olmaması yalnız testing ortamında (APP_ENV=testing; CI testleri dahil) "iş yok" sayılır; production'da hatadır. Tümü zaten kilitliyse
 * no-op (updated_at değişmez). Yazma tek transaction'da; hata olursa hepsi geri alınır.
 */
return new class extends Migration
{
    private const FILE = 'database/data/university-content/university-profiles-batch2-2026-10-10.json';

    private const COLUMNS = ['tr' => 'content_blocks', 'en' => 'content_blocks_en', 'de' => 'content_blocks_de'];

    private const FACT_DROP = '/program|studieng|uni-?assist|almanyauni/iu';

    private const CONDITIONAL_SENTENCE = '/şartlı kabul|conditional admission|bedingte[nrm]?\s+zulassung/iu';

    public function up(): void
    {
        if (! Schema::hasTable('universities') || ! is_file(base_path(self::FILE))) {
            return;
        }
        $data = json_decode(file_get_contents(base_path(self::FILE)), true, 512, JSON_THROW_ON_ERROR);

        // Hedef kayıtların hiçbiri yoksa: yalnız testing ortamında (yerel ve GitHub CI testleri, phpunit.xml
        // APP_ENV=testing) "iş yok". Production'da ya da bir kısmı eksikse her eksik kayıt aşağıda hata sayılır.
        if (app()->environment('testing') && ! DB::table('universities')->whereIn('slug', array_keys($data['universities']))->exists()) {
            return;
        }

        $plan = [];
        $problems = [];
        foreach ($data['universities'] as $slug => $spec) {
            $row = DB::table('universities')->where('slug', $slug)->first();
            if (! $row) {
                $problems[] = "$slug: kayıt yok";
                continue;
            }

            $locked = [];
            $decoded = [];
            foreach (self::COLUMNS as $loc => $col) {
                $blocks = json_decode((string) $row->{$col}, true);
                $decoded[$loc] = is_array($blocks) ? $blocks : [];
                $locked[$loc] = collect($decoded[$loc])->contains(fn ($b) => is_array($b) && ! empty($b['editorial_lock']));
            }
            if (count(array_filter($locked)) === 3) {
                continue; // zaten uygulanmış → no-op
            }
            if (array_filter($locked)) {
                $problems[] = "$slug: kısmen uygulanmış (" . implode(',', array_keys(array_filter($locked))) . ')';
                continue;
            }

            foreach (self::COLUMNS as $loc => $col) {
                $expected = $spec['expected'][$loc];
                $types = array_map(fn ($b) => $b['type'] ?? null, $decoded[$loc]);
                if ($expected === null ? $types !== [] : $types !== $expected) {
                    $problems[] = "$slug $col: blok yapısı beklenenden farklı";
                }
            }
            $plan[$slug] = [$row->id, $decoded, $spec];
        }

        if ($problems) {
            throw new RuntimeException('Batch 2 profilleri uygulanmadı, hiçbir şey yazılmadı: ' . implode('; ', $problems));
        }

        DB::transaction(function () use ($plan, $data) {
            foreach ($plan as [$id, $decoded, $spec]) {
                $update = ['updated_at' => now()];
                foreach (self::COLUMNS as $loc => $col) {
                    $t = $spec['locales'][$loc];
                    $blocks = $spec['expected'][$loc] === null
                        ? $this->fresh($spec, $t, $data['checked'], $loc)
                        : $this->transform($decoded[$loc], $spec, $t, $data['checked'], $loc);
                    $update[$col] = json_encode($blocks, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
                DB::table('universities')->where('id', $id)->update($update);
            }
        });
    }

    private function introBlock(array $spec, array $t, string $checked): array
    {
        return ['type' => 'intro', 'body_md' => $t['intro'], 'seo_title' => $t['seo_title'], 'seo_description' => $t['seo_description'],
            'editorial_lock' => true, 'verified_at' => $checked, 'sources' => $spec['sources']];
    }

    private function sectionBlocks(array $t, string $checked): array
    {
        return array_map(fn ($s) => ['type' => 'section', 'h' => $s[0], 'body_md' => $s[1], 'verified_at' => $checked], $t['sections']);
    }

    private function faqBlock(array $t): array
    {
        return ['type' => 'faq', 'h' => $t['faq_h'], 'items' => array_map(fn ($qa) => ['q' => $qa[0], 'a' => $qa[1]], $t['faq'])];
    }

    /** İçerik bloğu olmayan dil için temiz yapı: giriş, hızlı bilgi, doğrulanmış bölümler, SSS, sonraki adım, resmî link. */
    private function fresh(array $spec, array $t, string $checked, string $loc): array
    {
        return array_merge(
            [$this->introBlock($spec, $t, $checked)],
            [['type' => 'quick_facts', 'h' => $t['facts_h'], 'items' => array_map(fn ($f) => ['label' => $f[0], 'value' => $f[1]], $t['facts'])]],
            $this->sectionBlocks($t, $checked),
            [$this->faqBlock($t), ['type' => 'cta', 'h' => null, 'body_md' => $t['cta']],
             ['type' => 'external_links', 'h' => $t['links_h'], 'items' => [['url' => $spec['official']['url'], 'type' => 'official', 'label' => $spec['official']['label'][$loc]]]]],
        );
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
                    $out[] = array_merge($b, $this->introBlock($spec, $t, $checked));
                    break;
                case 'quick_facts':
                    $items = array_values(array_filter($b['items'] ?? [], fn ($it) => ! preg_match(self::FACT_DROP, (string) ($it['label'] ?? ''))));
                    foreach ($t['facts'] as [$label, $value]) {
                        $items[] = ['label' => $label, 'value' => $value];
                    }
                    $b['items'] = $items;
                    $out[] = $b;
                    array_push($out, ...$this->sectionBlocks($t, $checked));
                    break;
                case 'section':
                    $b['body_md'] = $this->dropConditionalSentences((string) ($b['body_md'] ?? ''));
                    $out[] = $b;
                    break;
                case 'faq':
                    $out[] = array_merge($b, $this->faqBlock($t));
                    break;
                case 'cta':
                    $b['h'] = null;
                    $b['body_md'] = $t['cta'];
                    $out[] = $b;
                    break;
                case 'external_links':
                    if (! in_array($spec['official']['url'], array_column($b['items'] ?? [], 'url'), true)) {
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
        $kept = [];
        foreach (preg_split('/\n{2,}/u', $md) as $p) {
            $sentences = array_filter(preg_split('/(?<=[.!?])\s+/u', $p), fn ($s) => ! preg_match(self::CONDITIONAL_SENTENCE, $s));
            if ($sentences) {
                $kept[] = implode(' ', $sentences);
            }
        }

        return implode("\n\n", $kept);
    }

    private function plain(string $md): string
    {
        $s = preg_replace('/\[([^\]]+)\]\([^)]+\)/u', '$1', $md);

        return trim(preg_replace('/\s+/u', ' ', str_replace(['**', '*'], '', $s)));
    }

    public function down(): void
    {
        // İçerik verisi.
    }
};
