<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * RSS kaynaklarını doğrulanmış sete çeker:
 *  - 3 ölü kaynağı kapat (DAAD / Make-it-in-Germany / Mediendienst — hepsi 404, public RSS yok).
 *  - 4 çalışan kaynak ekle (curl ile 200 + canlı item doğrulandı 2026-06-03):
 *      The Local Almanya (vize/oturum/yaşam), The PIE News (uluslararası eğitim),
 *      DW Almanya EN (RDF — parser'a RDF desteği eklendi), Tagesschau (geniş → sıkı DE keyword).
 * Idempotent: name'e göre updateOrInsert.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('news_sources')) return;

        $now = now();

        // 1) Ölü kaynakları kapat (silme — kayıt kalsın, sadece pasif).
        DB::table('news_sources')
            ->whereIn('name', ['DAAD', 'Make-it-in-Germany', 'Mediendienst Integration'])
            ->update(['enabled' => false, 'updated_at' => $now]);

        // 2) Doğrulanmış kaynakları ekle/güncelle.
        // [name, url, default_category, keywords[], max_per_source, sort_order]
        $verified = [
            ['The Local · Almanya',
             'https://www.thelocal.de/feeds/rss.php',
             'visa-residence',
             ['visa', 'residence', 'permit', 'student', 'study', 'university', 'immigration',
              'citizenship', 'Chancenkarte', 'Ausbildung', 'Ausländerbehörde', 'foreigner', 'asylum'],
             4, 35],

            ['The PIE News',
             'https://thepienews.com/feed/',
             'universities',
             ['Germany', 'German', 'Deutschland', 'DAAD'],
             4, 32],

            ['DW · Almanya (EN)',
             'https://rss.dw.com/rdf/rss-en-ger',
             'visa-residence',
             ['study', 'student', 'university', 'visa', 'immigration', 'scholarship',
              'education', 'foreign', 'skilled', 'Ausbildung', 'Fachkräfte'],
             3, 45],

            ['Tagesschau',
             'https://www.tagesschau.de/index~rss2.xml',
             'law-policy',
             ['Studium', 'Studierende', 'Hochschule', 'Visum', 'Einwanderung', 'Fachkräfte',
              'Aufenthalt', 'Ausländer', 'Migration', 'Chancenkarte', 'Stipendium'],
             2, 70],
        ];

        foreach ($verified as [$name, $url, $cat, $keywords, $max, $sort]) {
            DB::table('news_sources')->updateOrInsert(
                ['name' => $name],
                [
                    'url'              => $url,
                    'default_category' => $cat,
                    'keywords'         => json_encode($keywords),
                    'max_per_source'   => $max,
                    'enabled'          => true,
                    'sort_order'       => $sort,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]
            );
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('news_sources')) return;

        DB::table('news_sources')
            ->whereIn('name', ['The Local · Almanya', 'The PIE News', 'DW · Almanya (EN)', 'Tagesschau'])
            ->delete();

        DB::table('news_sources')
            ->whereIn('name', ['DAAD', 'Make-it-in-Germany', 'Mediendienst Integration'])
            ->update(['enabled' => true]);
    }
};
