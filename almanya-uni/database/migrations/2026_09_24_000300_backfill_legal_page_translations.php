<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Legacy `legal_pages.{titles,descriptions,bodies}` JSON'larını
 * `legal_page_translations` satırlarına taşır.
 *
 * KURALLAR (hepsi test altında, LegalPageTranslationsTest)
 *   - İçerik birebir taşınır; HTML'e dokunulmaz, whitespace normalize edilmez.
 *   - Eksik locale için SAHTE çeviri üretilmez. TR gövdesi EN/DE'ye kopyalanmaz.
 *     Bir dil yoksa o dilde kayıt da olmaz — sayfa o dilde 404 verir (bilinçli).
 *   - Idempotent: var olan (legal_page_id, locale) kaydına DOKUNULMAZ; böylece
 *     ikinci koşu ne çoğaltır ne de panelden yapılmış düzenlemeyi ezer.
 *   - Yalnızca eksik satırlar eklenir → UNIQUE ihlali oluşmaz.
 *   - Yalnız cookies/privacy değil, tablodaki TÜM hukuki sayfalar taşınır.
 *
 * Gövde/başlık boşsa kayıt açılmaz: boş bir hukuki metin yayımlamak, o dilde
 * içerik yokmuş gibi davranmaktan daha kötüdür.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('legal_pages') || ! Schema::hasTable('legal_page_translations')) {
            Log::warning('legal backfill: gerekli tablolar yok, atlandı');

            return;
        }

        $report = [];
        $inserted = 0;

        foreach (DB::table('legal_pages')->orderBy('id')->get() as $page) {
            $titles = $this->decode($page->titles);
            $descriptions = $this->decode($page->descriptions);
            $bodies = $this->decode($page->bodies);

            // Hangi diller var? Gövdesi olan diller belirler — başlığı olup
            // gövdesi olmayan bir dil yayımlanabilir bir hukuki sayfa değil.
            $locales = array_keys(array_filter($bodies, fn ($b) => is_string($b) && trim($b) !== ''));

            if ($locales === []) {
                $report[] = "{$page->key}: taşınacak gövde yok";
                Log::warning("legal backfill: '{$page->key}' için hiç gövde yok");

                continue;
            }

            $existing = DB::table('legal_page_translations')
                ->where('legal_page_id', $page->id)
                ->pluck('locale')
                ->all();

            $added = [];
            $kept = [];

            foreach ($locales as $locale) {
                if (in_array($locale, $existing, true)) {
                    // Panelden düzenlenmiş ya da önceki koşuda taşınmış olabilir.
                    $kept[] = $locale;

                    continue;
                }

                $title = $titles[$locale] ?? null;

                DB::table('legal_page_translations')->insert([
                    'legal_page_id' => $page->id,
                    'locale'        => $locale,
                    // Başlık yoksa key'den türet — başka dilin başlığı KULLANILMAZ.
                    'title'         => is_string($title) && trim($title) !== '' ? $title : ucfirst($page->key),
                    'description'   => $descriptions[$locale] ?? null,
                    'body'          => $bodies[$locale],
                    'created_at'    => $page->created_at ?? now(),
                    'updated_at'    => $page->updated_at ?? now(),
                ]);

                $added[] = $locale;
                $inserted++;
            }

            $report[] = "{$page->key}: eklendi [" . (implode(',', $added) ?: '-') . ']'
                . ', korundu [' . (implode(',', $kept) ?: '-') . ']';
        }

        echo "legal backfill: {$inserted} çeviri kaydı eklendi\n  " . implode("\n  ", $report) . "\n";
    }

    public function down(): void
    {
        // Çeviri satırlarını silmek, o anda tek gerçek kaynak onlarsa hukuki
        // sayfaları boşaltmak demek. Tablonun kendisi 000200'ün down()'ında
        // zaten düşüyor; burada veri silmiyoruz.
    }

    /** @return array<string, string> */
    private function decode(?string $json): array
    {
        $value = json_decode($json ?? '', true);

        return is_array($value) ? $value : [];
    }
};
