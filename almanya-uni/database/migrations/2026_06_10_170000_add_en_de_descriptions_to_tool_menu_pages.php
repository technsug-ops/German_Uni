<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Bu oturumda eklenen 5 araç menü satırına EN/DE açıklama. label_en/de vardı
 * ama description_en/de yoktu → EN/DE menüde TR açıklama sızıyordu
 * ("Sana en uygun yol", "kurs eşleştirme" vb.). DE register = du.
 * Idempotent UPDATE (sadece en/de kolonları; TR description korunur).
 */
return new class extends Migration
{
    private array $rows = [
        ['key' => 'tools.health-insurance', 'en' => 'GKV / PKV / expat comparison', 'de' => 'GKV / PKV / Expat-Vergleich'],
        ['key' => 'tools.visa-appointment', 'en' => 'iData appointment guide',     'de' => 'iData-Terminleitfaden'],
        ['key' => 'tools.studienkolleg',    'en' => 'T/M/W/G/S course matching',   'de' => 'T/M/W/G/S-Kurs-Zuordnung'],
        ['key' => 'tools.pathway-finder',   'en' => 'Your best route',             'de' => 'Dein bester Weg'],
        ['key' => 'tools.inspire-me',       'en' => 'Discover by interest',        'de' => 'Nach Interesse entdecken'],
    ];

    public function up(): void
    {
        foreach ($this->rows as $row) {
            DB::table('menu_pages')->where('key', $row['key'])->update([
                'description_en' => $row['en'],
                'description_de' => $row['de'],
                'updated_at'     => now(),
            ]);
        }

        if (method_exists(\App\Models\MenuPage::class, 'flushCache')) {
            \App\Models\MenuPage::flushCache();
        }
    }

    public function down(): void
    {
        foreach ($this->rows as $row) {
            DB::table('menu_pages')->where('key', $row['key'])->update([
                'description_en' => null,
                'description_de' => null,
            ]);
        }

        if (method_exists(\App\Models\MenuPage::class, 'flushCache')) {
            \App\Models\MenuPage::flushCache();
        }
    }
};
