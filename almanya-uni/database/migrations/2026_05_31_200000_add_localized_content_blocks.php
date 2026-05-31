<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds per-locale content_blocks_en / content_blocks_de JSON columns to the
 * enriched entities. The base `content_blocks` stays TR (source). EN/DE pages
 * render content_blocks_{locale} when present; until translated, the blade
 * gates (hides) the TR blocks so nothing leaks. (Enrichment-B, see
 * doc/MULTILANG-PLAN.)
 */
return new class extends Migration
{
    private array $tables = ['cities', 'universities', 'fields_of_study', 'states'];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table)) continue;
            Schema::table($table, function (Blueprint $t) use ($table) {
                if (! Schema::hasColumn($table, 'content_blocks_en')) $t->json('content_blocks_en')->nullable()->after('content_blocks');
                if (! Schema::hasColumn($table, 'content_blocks_de')) $t->json('content_blocks_de')->nullable()->after('content_blocks_en');
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table)) continue;
            Schema::table($table, function (Blueprint $t) use ($table) {
                foreach (['content_blocks_en', 'content_blocks_de'] as $col) {
                    if (Schema::hasColumn($table, $col)) $t->dropColumn($col);
                }
            });
        }
    }
};
