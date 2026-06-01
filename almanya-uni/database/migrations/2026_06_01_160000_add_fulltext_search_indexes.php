<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * FULLTEXT arama indexleri — LIKE %...% yerine MATCH...AGAINST (boolean prefix).
 * 18k program + 3.5k meslek + 488 üni'de büyük hız + alaka sıçraması, ek
 * altyapı/maliyet yok (KAS uyumlu). Kısa sorgu (<3 char) için kod LIKE'a düşer.
 *
 * İçerik aramasında SearchController kullanır (FulltextSearch trait).
 */
return new class extends Migration
{
    /** tablo => [fulltext index için kolonlar] */
    private array $indexes = [
        'universities' => ['name_de', 'name_en', 'name_tr', 'short_name'],
        'programs'     => ['name_de', 'description_tr', 'description_en'],
        'professions'  => ['name_de', 'name_tr', 'description_tr', 'description_de'],
        'cities'       => ['name_de', 'name_tr', 'name_en'],
    ];

    public function up(): void
    {
        foreach ($this->indexes as $table => $cols) {
            if (! Schema::hasTable($table)) continue;
            // kolonların hepsi var mı?
            foreach ($cols as $c) {
                if (! Schema::hasColumn($table, $c)) continue 2;
            }
            $indexName = "ft_{$table}_search";
            if ($this->indexExists($table, $indexName)) continue;
            try {
                Schema::table($table, function ($t) use ($cols, $indexName) {
                    $t->fullText($cols, $indexName);
                });
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("fulltext index skip {$table}: " . $e->getMessage());
            }
        }
    }

    public function down(): void
    {
        foreach ($this->indexes as $table => $cols) {
            $indexName = "ft_{$table}_search";
            if (Schema::hasTable($table) && $this->indexExists($table, $indexName)) {
                Schema::table($table, fn ($t) => $t->dropFullText($indexName));
            }
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        try {
            return count(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$index])) > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }
};
