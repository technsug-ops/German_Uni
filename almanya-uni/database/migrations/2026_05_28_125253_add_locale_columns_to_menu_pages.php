<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds per-locale label + description columns to menu_pages.
 *
 * Strategy: NON-destructive — the legacy `label` and `description` columns
 * stay as the "source / default" value. The new locale-specific columns are
 * OPTIONAL overrides. The model accessor reads label_{currentLocale} first;
 * if NULL, falls back to __($label), which uses lang/{locale}.json.
 *
 * This way:
 *   - Existing rows keep working (nothing copied or moved)
 *   - Admin can fill per-locale overrides for specific items as needed
 *   - lang JSON fallback still provides translations for ones that aren't overridden
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_pages', function (Blueprint $table) {
            $table->string('label_tr', 100)->nullable()->after('label');
            $table->string('label_en', 100)->nullable()->after('label_tr');
            $table->string('label_de', 100)->nullable()->after('label_en');

            $table->string('description_tr', 200)->nullable()->after('description');
            $table->string('description_en', 200)->nullable()->after('description_tr');
            $table->string('description_de', 200)->nullable()->after('description_en');
        });
    }

    public function down(): void
    {
        Schema::table('menu_pages', function (Blueprint $table) {
            $table->dropColumn([
                'label_tr', 'label_en', 'label_de',
                'description_tr', 'description_en', 'description_de',
            ]);
        });
    }
};
