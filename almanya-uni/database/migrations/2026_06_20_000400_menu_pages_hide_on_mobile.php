<?php

use App\Models\MenuPage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Mobil menü kısaltma: menu_pages'e hide_on_mobile bayrağı + çekirdek-dışı
 * öğeleri mobilde gizle. Masaüstü mega-menü, footer ve "Tüm Araçlar →" listesi
 * AYNI kalır — hiçbir özellik kaybolmaz, sadece mobil drawer sadeleşir.
 *
 * Çekirdek (mobilde kalan): Keşfet 6, Araçlar 8 (+ "Tüm Araçlar" linki).
 * Bayrak admin panelinden (MenuPage formu) her an değiştirilebilir.
 */
return new class extends Migration
{
    /** Mobilde GİZLENECEK menü key'leri (çekirdek-dışı). */
    private array $hide = [
        // Keşfet
        'states.index', 'fields.index', 'professions.index',
        'map.index', 'language-courses.index', 'translation-offices.index',
        // Araçlar
        'tools.pathway-finder', 'tools.inspire-me', 'tools.career-compass',
        'map.rents', 'tools.budget-planner', 'tools.visa-cost',
        'tools.visa-appointment', 'tools.health-insurance', 'tools.studienkolleg',
        'tools.language-certificates', 'housing.index',
    ];

    public function up(): void
    {
        if (! Schema::hasColumn('menu_pages', 'hide_on_mobile')) {
            Schema::table('menu_pages', function (Blueprint $table) {
                $table->boolean('hide_on_mobile')->default(false)->after('sort_order');
            });
        }

        DB::table('menu_pages')->whereIn('key', $this->hide)->update(['hide_on_mobile' => true]);

        if (method_exists(MenuPage::class, 'flushCache')) {
            MenuPage::flushCache();
        }
    }

    public function down(): void
    {
        DB::table('menu_pages')->whereIn('key', $this->hide)->update(['hide_on_mobile' => false]);

        if (Schema::hasColumn('menu_pages', 'hide_on_mobile')) {
            Schema::table('menu_pages', function (Blueprint $table) {
                $table->dropColumn('hide_on_mobile');
            });
        }

        if (method_exists(MenuPage::class, 'flushCache')) {
            MenuPage::flushCache();
        }
    }
};
