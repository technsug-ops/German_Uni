<?php

use App\Models\City;
use Illuminate\Database\Migrations\Migration;

/** MLP Studentenwohnreport 2025, Tabelle 5-1: Kaltmiete(30m²) + WG-Zimmer(warm/kalt) + 3-yıl endeks. */
return new class extends Migration
{
    public function up(): void
    {
        // city => [kalt30, wg_warm, wg_kalt, idx3yr]
        $d = [
            'Aachen' => [411, 483, 410, 4.7], 'Berlin' => [547, 624, 546, 5.3], 'Bielefeld' => [342, 398, 331, 4.8],
            'Bochum' => [254, 322, 246, 5.4], 'Bonn' => [461, 540, 459, 4.8], 'Bremen' => [398, 484, 413, 4.4],
            'Chemnitz' => [215, 274, 220, 2.3], 'Darmstadt' => [437, 483, 404, 5.8], 'Dresden' => [384, 435, 359, 5.5],
            'Düsseldorf' => [445, 517, 443, 6.0], 'Frankfurt am Main' => [609, 702, 619, 5.2], 'Freiburg im Breisgau' => [536, 615, 543, 6.8],
            'Greifswald' => [299, 340, 272, 4.9], 'Göttingen' => [408, 448, 382, 5.1], 'Hamburg' => [515, 678, 604, 5.6],
            'Hannover' => [366, 451, 377, 4.8], 'Heidelberg' => [552, 633, 554, 5.2], 'Jena' => [365, 452, 385, 5.7],
            'Karlsruhe' => [464, 526, 456, 4.9], 'Kiel' => [348, 419, 345, 5.7], 'Konstanz' => [463, 580, 508, 6.6],
            'Köln' => [571, 641, 563, 4.7], 'Leipzig' => [344, 434, 369, 6.9], 'Magdeburg' => [280, 339, 276, 4.5],
            'Mainz' => [438, 495, 421, 5.0], 'Mannheim' => [396, 455, 384, 5.3], 'München' => [715, 790, 709, 4.2],
            'Münster' => [540, 608, 535, 6.0], 'Nürnberg' => [452, 535, 465, 5.8], 'Oldenburg' => [420, 495, 424, 5.2],
            'Regensburg' => [440, 510, 442, 4.5], 'Rostock' => [394, 502, 434, 5.4], 'Saarbrücken' => [400, 480, 416, 5.6],
            'Stuttgart' => [534, 593, 522, 3.6], 'Trier' => [412, 435, 370, 3.4], 'Tübingen' => [401, 464, 381, 3.9],
            'Ulm' => [432, 480, 415, 3.9], 'Würzburg' => [482, 547, 480, 4.0],
        ];
        $alias = [
            'Frankfurt am Main' => ['Frankfurt am Main', 'Frankfurt'],
            'Freiburg im Breisgau' => ['Freiburg im Breisgau', 'Freiburg'],
        ];
        foreach ($d as $name => [$kalt, $wgW, $wgK, $i3]) {
            $city = null;
            foreach (($alias[$name] ?? [$name]) as $cand) {
                $city = City::where('name_de', $cand)->orWhere('name_tr', $cand)->orWhere('name_en', $cand)->first();
                if ($city) break;
            }
            if (! $city) continue;
            $city->forceFill([
                'student_rent_kalt30' => $kalt,
                'student_rent_wg_warm' => $wgW,
                'student_rent_wg_kalt' => $wgK,
                'student_rent_index_3yr' => $i3,
            ])->saveQuietly();
        }
    }

    public function down(): void
    {
        City::query()->update([
            'student_rent_kalt30' => null, 'student_rent_wg_warm' => null,
            'student_rent_wg_kalt' => null, 'student_rent_index_3yr' => null,
        ]);
    }
};
