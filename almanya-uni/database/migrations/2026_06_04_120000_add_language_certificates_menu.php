<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Dil Sertifikaları rehberini (/tools/language-certificates) Araçlar mega menüsüne ekler.
 * Evrensel içerik (tüm diller). 3-dil label baştan set (partner sızıntı dersini uygula).
 * Not: iData randevu rehberi BİLEREK menüye eklenmedi — TR-only (menu_pages locale-aware değil).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('menu_pages')) {
            return;
        }

        $now = now();
        DB::table('menu_pages')->updateOrInsert(
            ['key' => 'tools.language-certificates'],
            [
                'label'          => 'Language Certificates',
                'label_tr'       => 'Dil Sertifikaları',
                'label_en'       => 'Language Certificates',
                'label_de'       => 'Sprachzertifikate',
                'description'    => 'TestDaF, DSH, telc, Goethe — which one?',
                'description_tr' => 'TestDaF, DSH, telc, Goethe — hangisi?',
                'description_en' => 'TestDaF, DSH, telc, Goethe — which one?',
                'description_de' => 'TestDaF, DSH, telc, Goethe — welches?',
                'icon'           => '🎓',
                'group'          => 'araclar',
                'sort_order'     => 70,
                'link_type'      => 'route',
                'is_enabled'     => true,
                'protect_route'  => true,
                'updated_at'     => $now,
                'created_at'     => $now,
            ]
        );

        \App\Models\MenuPage::flushCache();
    }

    public function down(): void
    {
        if (Schema::hasTable('menu_pages')) {
            DB::table('menu_pages')->where('key', 'tools.language-certificates')->delete();
            \App\Models\MenuPage::flushCache();
        }
    }
};
