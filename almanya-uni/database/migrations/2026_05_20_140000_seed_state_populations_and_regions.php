<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * 16 Alman eyaletinin nüfusunu (Destatis 2024) ve coğrafi bölgesini doldur.
     * region: 'nord' | 'sued' | 'west' | 'ost' (Türkçe filter chip'i için)
     */
    public function up(): void
    {
        // region kolonu yoksa ekle
        if (! Schema::hasColumn('states', 'region')) {
            Schema::table('states', function (Blueprint $table) {
                $table->string('region', 16)->nullable()->after('capital');
            });
        }

        $data = [
            'Baden-Württemberg'        => ['pop' => 11_280_000, 'region' => 'sued'],
            'Bayern'                   => ['pop' => 13_177_000, 'region' => 'sued'],
            'Berlin'                   => ['pop' => 3_755_000,  'region' => 'ost'],
            'Brandenburg'              => ['pop' => 2_573_000,  'region' => 'ost'],
            'Freie Hansestadt Bremen'  => ['pop' => 685_000,    'region' => 'nord'],
            'Bremen'                   => ['pop' => 685_000,    'region' => 'nord'],
            'Hamburg'                  => ['pop' => 1_910_000,  'region' => 'nord'],
            'Hessen'                   => ['pop' => 6_318_000,  'region' => 'west'],
            'Mecklenburg-Vorpommern'   => ['pop' => 1_628_000,  'region' => 'nord'],
            'Niedersachsen'            => ['pop' => 8_140_000,  'region' => 'nord'],
            'Nordrhein-Westfalen'      => ['pop' => 17_926_000, 'region' => 'west'],
            'Rheinland-Pfalz'          => ['pop' => 4_158_000,  'region' => 'west'],
            'Saarland'                 => ['pop' => 994_000,    'region' => 'west'],
            'Sachsen'                  => ['pop' => 4_086_000,  'region' => 'ost'],
            'Sachsen-Anhalt'           => ['pop' => 2_186_000,  'region' => 'ost'],
            'Schleswig-Holstein'       => ['pop' => 2_953_000,  'region' => 'nord'],
            'Thüringen'                => ['pop' => 2_120_000,  'region' => 'ost'],
        ];

        foreach ($data as $name => $row) {
            DB::table('states')
                ->where('name_de', $name)
                ->update([
                    'population' => $row['pop'],
                    'region'     => $row['region'],
                ]);
        }
    }

    public function down(): void
    {
        DB::table('states')->update(['population' => null, 'region' => null]);

        if (Schema::hasColumn('states', 'region')) {
            Schema::table('states', function (Blueprint $table) {
                $table->dropColumn('region');
            });
        }
    }
};
