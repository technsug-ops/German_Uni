<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Önceki migration kırık linkleri posts.content_md'de düzeltti; ancak blog sayfaları
 * önceden render edilmiş posts.content_html'den gösteriliyor. Aynı düzeltmeleri
 * content_html'e de uygular (yoksa kullanıcı hâlâ ölü almanyauni.de linklerini görür).
 */
return new class extends Migration
{
    public function up(): void
    {
        $replacements = [
            'almanyauni.de' => 'applytogerman.com',
            'https://www.kmk.org/zab/zeugnisbewertung.html'
                => 'https://www.kmk.org/zab/zentralstelle-fuer-auslaendisches-bildungswesen.html',
            'https://www.anerkennung-in-deutschland.de/html/de/290.php'
                => 'https://www.anerkennung-in-deutschland.de/html/de/index.php',
            'https://www.daad.de/en/study-and-research-in-germany/scholarships/find-a-scholarship/'
                => 'https://www.daad.de/en/study-and-research-in-germany/scholarships/',
            'https://www.testdaf.de/en/' => 'https://www.testdaf.de/de/',
        ];
        foreach ($replacements as $old => $new) {
            DB::statement(
                'UPDATE posts SET content_html = REPLACE(content_html, ?, ?) WHERE content_html LIKE ?',
                [$old, $new, '%' . $old . '%']
            );
        }
    }

    public function down(): void
    {
        // İçerik düzeltmesi — geri alınmaz.
    }
};
