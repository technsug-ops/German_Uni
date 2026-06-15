<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * İçerik denetimi düzeltmeleri (2026-06-16):
 *  1) Üni adları — junk-ek / eski / kısaltılmış / yanlış adları otoriter resmi adla düzeltir.
 *     (Wikidata + resmi site + Hochschulkompass ile tek tek doğrulandı; çoğu uyuşmazlık
 *      YANLIŞ ALARM çıktı — Wikidata eski/kısaltma — onlara DOKUNULMADI.)
 *  2) Kırık üni website_url'leri — doğrulanmış güncel adrese.
 *  3) Blog içindeki (content_md) kırık dış linkler — ölü almanyauni.de domaini ve 404 referanslar.
 *
 * Idempotent: WHERE eşleşmezse no-op. Geri alınamaz (down boş — eski yanlış veriyi geri yazmayız).
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1) Üni adı düzeltmeleri (id => doğru resmi ad)
        $names = [
            985 => 'AKAD Hochschule Stuttgart',
            972 => 'Karlshochschule International University',
            977 => 'DIPLOMA Hochschule',
            970 => 'HFH · Hamburger Fern-Hochschule',
            980 => 'Deutsche Hochschule für angewandte Wissenschaften',
            743 => 'Allensbach Hochschule',
            954 => 'ESCP Business School Wirtschaftshochschule Berlin e.V.',
            698 => 'Alanus Hochschule für Kunst und Gesellschaft',
            951 => 'Akkon-Hochschule für Humanwissenschaften',
            725 => 'Hertie School',
            642 => 'PFH Private Hochschule Göttingen',
            671 => 'Whitecliffe University of Applied Sciences',
        ];
        foreach ($names as $id => $name) {
            DB::table('universities')->where('id', $id)->update(['name_de' => $name]);
        }

        // 2) Kırık üni website_url → doğrulanmış güncel adres
        $sites = [
            'http://www.hs-emden-leer.de/'                  => 'https://www.hs-emden-leer.de/',
            'http://www.htwg-konstanz.de/English.20.0.html' => 'https://www.htwg-konstanz.de/',
            'http://www.apollon-hochschule.de/redirect.php?id=32' => 'https://www.apollon-hochschule.de/',
        ];
        foreach ($sites as $old => $new) {
            DB::table('universities')->where('website_url', $old)->update(['website_url' => $new]);
        }

        // 3) Blog content_md kırık dış linkler — DB-geneli REPLACE
        $replacements = [
            // Ölü domain almanyauni.de → çalışan applytogerman.com (universities/faq linkleri + görsel ref domaini)
            'almanyauni.de' => 'applytogerman.com',
            // KMK ZAB 404 → güncel ZAB sayfası
            'https://www.kmk.org/zab/zeugnisbewertung.html'
                => 'https://www.kmk.org/zab/zentralstelle-fuer-auslaendisches-bildungswesen.html',
            // anerkennung-in-deutschland 404 → index
            'https://www.anerkennung-in-deutschland.de/html/de/290.php'
                => 'https://www.anerkennung-in-deutschland.de/html/de/index.php',
            // DAAD scholarship 404 → çalışan scholarships kökü
            'https://www.daad.de/en/study-and-research-in-germany/scholarships/find-a-scholarship/'
                => 'https://www.daad.de/en/study-and-research-in-germany/scholarships/',
            // testdaf /en/ 500 → /de/
            'https://www.testdaf.de/en/' => 'https://www.testdaf.de/de/',
        ];
        foreach ($replacements as $old => $new) {
            DB::statement(
                'UPDATE posts SET content_md = REPLACE(content_md, ?, ?) WHERE content_md LIKE ?',
                [$old, $new, '%' . $old . '%']
            );
        }
    }

    public function down(): void
    {
        // İçerik düzeltmesi — geri alınmaz.
    }
};
