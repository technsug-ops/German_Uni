<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Yurt/konut sağlayıcılarına affiliate_url — tıklama takibi (/go/housing/{slug})
 * için. cta_url accessor affiliate_url ?: website döner. Admin/data'dan doldurulur.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('housing_providers') && ! Schema::hasColumn('housing_providers', 'affiliate_url')) {
            Schema::table('housing_providers', function (Blueprint $table) {
                $table->string('affiliate_url')->nullable()->after('website');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('housing_providers') && Schema::hasColumn('housing_providers', 'affiliate_url')) {
            Schema::table('housing_providers', function (Blueprint $table) {
                $table->dropColumn('affiliate_url');
            });
        }
    }
};
