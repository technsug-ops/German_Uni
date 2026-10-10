<?php

use App\Models\Faq;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Dil SSS uyum düzeltmesi (SEO Growth Batch 3 sonrası, 2026-10-10). İki SSS kümesi (TR/EN/DE, 6 kayıt):
 * "Goethe vs telc — üniversite başvurusu için fark var mı?" ve "Almanca C1 hangi sınav ile kanıtlanır?".
 *
 * Eski cevaplar kaynaksız "üniversite kabul oranı" tabloları (%100 / %95+ / %85+ / %60-70) ve mutlak ifadeler
 * ("TestDaF her yere geçer", "DSH-2 sadece o üniversite için") içeriyordu; chatbot bu oranları tekrarlıyordu.
 * Yeni cevaplar Batch 3 dil rehberinin resmî kaynaklı olgu setine dayanır (HRK/KMK RO-DT Kasım 2025, kontrol
 * 2026-10-10) ve aynı dildeki rehbere bağlanır (veri: database/data/faq-language-2026-10-10/*.md). Sorular, slug'lar,
 * yayın durumu ve çeviri grupları değişmez.
 *
 * Güvenlik: önce altı kayıt doğrulanır — slug ile tek kayıt, beklenen soru metni, kümenin üç kaydının aynı çeviri
 * grubunda olması ve eski cevabın oran parmak izini taşıması. Beklenmeyen ya da kısmi durumda RuntimeException →
 * hiçbir şey yazılmaz. Zaten uygulanmış kayıt atlanır (rerun no-op, updated_at dahil). Kayıtların hiçbirinin olmaması
 * yalnız testing ortamında "iş yok" sayılır. Faq::saving answer_html'i answer_md'den yeniden üretir (prod'da boş olan
 * EN/DE answer_html da böylece dolar).
 */
return new class extends Migration
{
    private const DIR = 'database/data/faq-language-2026-10-10';

    private const TARGETS = [
        // küme => [locale => [slug, soru, dosya]]
        'goethe-telc' => [
            'tr' => ['goethe-vs-telc-sertifikasi-uni-basvurusu-icin-fark-var-mi', 'Goethe vs Telc sertifikası — uni başvurusu için fark var mı?'],
            'en' => ['goethe-vs-telc-sertifikasi-uni-basvurusu-icin-fark-var-mi-en', 'Is there a difference between Goethe vs Telc certificates for university applications?'],
            'de' => ['goethe-vs-telc-sertifikasi-uni-basvurusu-icin-fark-var-mi-de', 'Goethe vs. Telc Zertifikat – gibt es einen Unterschied für deine Hochschulbewerbung?'],
        ],
        'c1-proof' => [
            'tr' => ['almanca-c1-hangi-sinav-ile-kanitlanir', 'Almanca C1 hangi sınav ile kanıtlanır?'],
            'en' => ['almanca-c1-hangi-sinav-ile-kanitlanir-en', 'What exams prove your C1 German level?'],
            'de' => ['almanca-c1-hangi-sinav-ile-kanitlanir-de', 'Mit welcher Prüfung kann ich mein Deutsch C1-Niveau nachweisen?'],
        ],
    ];

    /** Eski cevabın parmak izi: kaynaksız "Goethe C1 %95+" kabul oranı. */
    private const OLD_FINGERPRINT = '/95\s?%\s?\+|%\s?95\s?\+/u';

    /** Yeni metinde olmaması gereken kabul oranı / mutlak kabul ifadeleri. */
    public const FORBIDDEN = '/\d\s?%|%\s?\d|kabul oran|acceptance rate|Akzeptanzrate|her yere geçer|herkes kabul|valid everywhere|universally accepted|überall anerkannt|von allen akzeptiert|Sadece o uni|Only for that university|Nur für diese Uni/iu';

    public function up(): void
    {
        if (! Schema::hasTable('faqs')) {
            return;
        }
        $slugs = collect(self::TARGETS)->flatMap(fn ($c) => array_column($c, 0))->all();
        if (app()->environment('testing') && ! DB::table('faqs')->whereIn('slug', $slugs)->exists()) {
            return;
        }

        $plan = [];
        $problems = [];
        foreach (self::TARGETS as $cluster => $locales) {
            $groups = [];
            foreach ($locales as $locale => [$slug, $question]) {
                $label = "$locale/$slug";
                $found = Faq::where('locale', $locale)->where('slug', $slug)->get();
                if ($found->count() !== 1) {
                    $problems[] = "$label: " . ($found->isEmpty() ? 'bulunamadı' : 'birden fazla kayıt');
                    continue;
                }
                $faq = $found->first();
                $groups[] = $faq->translation_group_id;
                if ($faq->question !== $question) {
                    $problems[] = "$label: beklenmeyen soru metni";
                    continue;
                }
                $new = rtrim(file_get_contents(base_path(self::DIR . "/$cluster.$locale.md"))) . "\n";
                if (preg_match(self::FORBIDDEN, $new, $m)) {
                    $problems[] = "$label: yeni metinde '{$m[0]}'";
                    continue;
                }
                if ((string) $faq->answer_md === $new) {
                    continue; // zaten uygulanmış → no-op
                }
                if (! preg_match(self::OLD_FINGERPRINT, (string) $faq->answer_md)) {
                    $problems[] = "$label: beklenen eski cevap değil (parmak izi yok)";
                    continue;
                }
                $faq->answer_md = $new;
                $plan[] = $faq;
            }
            if (count($groups) === 3 && (in_array(null, $groups, true) || count(array_unique($groups)) !== 1)) {
                $problems[] = "$cluster: üç kayıt aynı çeviri grubunda değil";
            }
        }

        if ($problems) {
            throw new RuntimeException('Dil SSS düzeltmesi uygulanmadı, hiçbir şey yazılmadı: ' . implode('; ', $problems));
        }

        DB::transaction(function () use ($plan) {
            foreach ($plan as $faq) {
                $faq->save();
            }
        });
    }

    public function down(): void
    {
        // İçerik verisi.
    }
};
