<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['posts', 'faqs'] as $table) {
            Schema::table($table, function (Blueprint $t) use ($table) {
                $t->string('locale', 5)->default('tr')->index()->after('id');
                $t->uuid('translation_group_id')->nullable()->index()->after('locale');
            });

            // Slug unique → composite (slug, locale) unique
            DB::statement("ALTER TABLE {$table} DROP INDEX {$table}_slug_unique");
            DB::statement("ALTER TABLE {$table} ADD UNIQUE {$table}_slug_locale_unique (slug, locale)");

            // Existing rows: locale='tr' + her satıra UUID
            DB::table($table)->whereNull('translation_group_id')->orderBy('id')->chunk(200, function ($rows) use ($table) {
                foreach ($rows as $row) {
                    DB::table($table)->where('id', $row->id)->update([
                        'locale' => 'tr',
                        'translation_group_id' => (string) Str::uuid(),
                    ]);
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['posts', 'faqs'] as $table) {
            DB::statement("ALTER TABLE {$table} DROP INDEX {$table}_slug_locale_unique");
            DB::statement("ALTER TABLE {$table} ADD UNIQUE {$table}_slug_unique (slug)");
            Schema::table($table, function (Blueprint $t) {
                $t->dropColumn(['locale', 'translation_group_id']);
            });
        }
    }
};
