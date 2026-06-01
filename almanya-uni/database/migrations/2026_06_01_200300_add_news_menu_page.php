<?php

use App\Models\MenuPage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * "Haberler" (news.index) öğesini mega menüye (İçerik grubu) ekler.
 * DB-driven menü → admin Filament'tan aç/kapat edebilir. Idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('menu_pages')) return;
        if (DB::table('menu_pages')->where('key', 'news.index')->exists()) return;

        DB::table('menu_pages')->insert([
            'key'            => 'news.index',
            'link_type'      => 'route',
            'label'          => 'Haberler',
            'label_tr'       => 'Haberler',
            'label_en'       => 'News',
            'label_de'       => 'Aktuelles',
            'icon'           => '📰',
            'description'    => 'Almanya\'dan eğitim & göç haberleri',
            'description_tr' => 'Almanya\'dan eğitim & göç haberleri',
            'description_en' => 'Study & migration news from Germany',
            'description_de' => 'Studien- & Migrationsnews aus Deutschland',
            'badge'          => 'YENİ',
            'group'          => 'icerik',
            'is_enabled'     => true,
            'protect_route'  => false,
            'sort_order'     => 8,
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
            DB::table('menu_pages')->where('key', 'news.index')->delete();
            if (class_exists(MenuPage::class)) {
                MenuPage::flushCache();
            }
        }
    }
};
