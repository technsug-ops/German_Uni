<?php

use App\Models\MenuPage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * "Başvuru Şablonları" (templates.index) öğesini mega menüye (Araçlar/Tools grubu)
 * ekler → kullanıcılar şablonlara üst menüden ulaşır. DB-driven, admin'den
 * aç/kapat edilebilir. Idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('menu_pages')) return;
        if (DB::table('menu_pages')->where('key', 'templates.index')->exists()) return;

        DB::table('menu_pages')->insert([
            'key'            => 'templates.index',
            'link_type'      => 'route',
            'label'          => 'Başvuru Şablonları',
            'label_tr'       => 'Başvuru Şablonları',
            'label_en'       => 'Application Templates',
            'label_de'       => 'Bewerbungsvorlagen',
            'icon'           => '📝',
            'description'    => 'Lebenslauf, Motivationsschreiben, tavsiye mektubu…',
            'description_tr' => 'Lebenslauf, Motivationsschreiben, tavsiye mektubu…',
            'description_en' => 'CV, motivation & recommendation letters, emails',
            'description_de' => 'Lebenslauf, Motivations- & Empfehlungsschreiben',
            'badge'          => 'YENİ',
            'group'          => 'araclar',
            'is_enabled'     => true,
            'protect_route'  => false,
            'sort_order'     => 1,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        if (class_exists(MenuPage::class)) {
            MenuPage::flushCache();
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('menu_pages')) {
            DB::table('menu_pages')->where('key', 'templates.index')->delete();
            if (class_exists(MenuPage::class)) {
                MenuPage::flushCache();
            }
        }
    }
};
