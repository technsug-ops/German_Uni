<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * menu_pages.badge ham "YENİ" (TR) olarak basılıyordu → EN/DE menüde de
 * Türkçe "YENİ" sızıyordu. Kanonik anahtar "New"e çevir; render artık
 * __() ile locale'e göre Yeni/New/Neu basacak (CSS uppercase → YENİ/NEW/NEU).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('menu_pages')->where('badge', 'YENİ')->update(['badge' => 'New']);

        if (method_exists(\App\Models\MenuPage::class, 'flushCache')) {
            \App\Models\MenuPage::flushCache();
        }
    }

    public function down(): void
    {
        DB::table('menu_pages')->where('badge', 'New')->update(['badge' => 'YENİ']);

        if (method_exists(\App\Models\MenuPage::class, 'flushCache')) {
            \App\Models\MenuPage::flushCache();
        }
    }
};
