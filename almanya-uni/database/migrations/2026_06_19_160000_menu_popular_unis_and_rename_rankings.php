<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Menü (menu_pages) düzenlemesi:
 *  1) "Popüler Üniversiteler" (/popular-universities) → Keşfet grubuna, Programlar'dan
 *     hemen sonra (sort_order 25). Etkileşimli üni gezgini + en çok tercih edilenler.
 *  2) "Sıralamalar" belirsizdi → "Üniversite Sıralamaları" (ne sıralaması olduğu net).
 *
 * Data migration (seeder prod'da çalışmaz). Idempotent: updateOrInsert / update.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('menu_pages')) {
            return;
        }
        $now = now();

        // 1) Yeni menü öğesi — Programlar'dan (20) sonra, Cities'ten (30) önce.
        DB::table('menu_pages')->updateOrInsert(
            ['key' => 'popular-universities'],
            [
                'link_type'      => 'route',
                'url'            => null,
                'label'          => 'Popular Universities',
                'label_tr'       => 'Popüler Üniversiteler',
                'label_en'       => 'Popular Universities',
                'label_de'       => 'Beliebte Universitäten',
                'icon'           => '⭐',
                'description'    => 'Most preferred fields & universities + explorer',
                'description_tr' => 'En çok tercih edilen bölümler & üniversiteler + gezgin',
                'description_en' => 'Most preferred fields & universities + explorer',
                'description_de' => 'Beliebteste Fächer & Universitäten + Explorer',
                'badge'          => null,
                'group'          => 'kesfet',
                'is_enabled'     => 1,
                'protect_route'  => 1,
                'sort_order'     => 25,
                'updated_at'     => $now,
                'created_at'     => $now,
            ]
        );

        // 2) "Sıralamalar" → "Üniversite Sıralamaları" (ne sıralaması belli olsun).
        DB::table('menu_pages')->where('key', 'rankings.index')->update([
            'label'    => 'University Rankings',
            'label_tr' => 'Üniversite Sıralamaları',
            'label_en' => 'University Rankings',
            'label_de' => 'Universitäts-Rankings',
            'updated_at' => $now,
        ]);

        // DB::table model event tetiklemez → menü cache'ini (rememberForever) elle temizle.
        \App\Models\MenuPage::flushCache();
    }

    public function down(): void
    {
        if (! Schema::hasTable('menu_pages')) {
            return;
        }
        DB::table('menu_pages')->where('key', 'popular-universities')->delete();
        DB::table('menu_pages')->where('key', 'rankings.index')->update([
            'label'    => 'Rankings',
            'label_tr' => 'Sıralamalar',
            'label_en' => 'Rankings',
            'label_de' => 'Rankings',
        ]);
        \App\Models\MenuPage::flushCache();
    }
};
