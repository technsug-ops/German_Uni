<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Program araması RESMİ isim (name_de) + sayfa-dili karşılığı (name_tr/name_en)
 * üzerinden de yapılabilsin diye programs FULLTEXT index'ini genişletir.
 * Örn: TR sayfada "İşletme" VEYA "Betriebswirtschaftslehre" → aynı program.
 * (name_tr/name_en programs:translate-names ile dolar.)
 */
return new class extends Migration
{
    private string $table = 'programs';
    private string $index = 'ft_programs_search';
    private array $newCols = ['name_de', 'name_en', 'name_tr', 'description_tr', 'description_en'];

    public function up(): void
    {
        if (! Schema::hasTable($this->table)) return;
        foreach ($this->newCols as $c) {
            if (! Schema::hasColumn($this->table, $c)) return;
        }

        try {
            if ($this->indexExists()) {
                Schema::table($this->table, fn ($t) => $t->dropFullText($this->index));
            }
            Schema::table($this->table, fn ($t) => $t->fullText($this->newCols, $this->index));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('extend programs fulltext skip: ' . $e->getMessage());
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable($this->table) || ! $this->indexExists()) return;
        try {
            Schema::table($this->table, fn ($t) => $t->dropFullText($this->index));
            // eski (dar) index'e geri dön
            Schema::table($this->table, fn ($t) => $t->fullText(['name_de', 'description_tr', 'description_en'], $this->index));
        } catch (\Throwable $e) {
            //
        }
    }

    private function indexExists(): bool
    {
        try {
            return count(DB::select("SHOW INDEX FROM `{$this->table}` WHERE Key_name = ?", [$this->index])) > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }
};
