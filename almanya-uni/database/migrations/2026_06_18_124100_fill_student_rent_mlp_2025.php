<?php

use App\Models\City;
use Illuminate\Database\Migrations\Migration;

/** MLP Studentenwohnreport 2025 (Value AG): 38 üni şehri için Warmmiete(30m²) + fiyat endeksi. */
return new class extends Migration
{
    public function up(): void
    {
        // [Warmmiete 30m² €, Studentenwohnpreisindex yıllık %], kaynak: MLP Studentenwohnreport 2025
        $data = [
            'München' => [837, 2.9], 'Frankfurt am Main' => [734, 2.4], 'Köln' => [688, 3.3],
            'Heidelberg' => [670, 2.7], 'Berlin' => [664, -0.8], 'Münster' => [649, 3.6],
            'Freiburg im Breisgau' => [644, 5.9], 'Stuttgart' => [640, 1.4], 'Hamburg' => [626, 4.3],
            'Würzburg' => [582, 4.2], 'Bonn' => [582, 1.9], 'Konstanz' => [571, 6.9],
            'Karlsruhe' => [570, 2.5], 'Nürnberg' => [558, 5.7], 'Düsseldorf' => [557, 3.8],
            'Darmstadt' => [555, 4.6], 'Mainz' => [549, 4.4], 'Regensburg' => [542, 2.4],
            'Ulm' => [530, -3.0], 'Oldenburg' => [526, 6.9], 'Tübingen' => [526, 1.7],
            'Aachen' => [521, 1.6], 'Trier' => [510, 0.1], 'Göttingen' => [508, 5.2],
            'Bremen' => [504, 3.0], 'Mannheim' => [502, 4.0], 'Dresden' => [499, 4.0],
            'Saarbrücken' => [496, 7.7], 'Rostock' => [496, 9.1], 'Hannover' => [477, 2.3],
            'Jena' => [466, 6.1], 'Kiel' => [460, 7.1], 'Bielefeld' => [443, 4.6],
            'Leipzig' => [442, 5.5], 'Greifswald' => [402, 1.7], 'Magdeburg' => [374, 2.5],
            'Bochum' => [368, 2.3], 'Chemnitz' => [296, 1.2],
        ];
        // MLP adı → DB name_de alias (gerekirse)
        $alias = [
            'Frankfurt am Main' => ['Frankfurt am Main', 'Frankfurt'],
            'Freiburg im Breisgau' => ['Freiburg im Breisgau', 'Freiburg'],
        ];

        foreach ($data as $name => [$rent, $idx]) {
            $candidates = $alias[$name] ?? [$name];
            $city = null;
            foreach ($candidates as $cand) {
                $city = City::where('name_de', $cand)->orWhere('name_tr', $cand)->orWhere('name_en', $cand)->first();
                if ($city) break;
            }
            if (! $city) continue;
            $city->forceFill([
                'student_rent_warm30' => $rent,
                'student_rent_index' => $idx,
                'student_rent_source' => 'MLP Studentenwohnreport 2025 / Value AG',
                'student_rent_year' => 2025,
            ])->saveQuietly();
        }
    }

    public function down(): void
    {
        City::whereNotNull('student_rent_warm30')->update([
            'student_rent_warm30' => null, 'student_rent_index' => null,
            'student_rent_source' => null, 'student_rent_year' => null,
        ]);
    }
};
