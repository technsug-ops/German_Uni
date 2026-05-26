<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\CityCostData;
use Illuminate\Database\Seeder;

class CityCostDataSeeder extends Seeder
{
    /**
     * Yaklaşık 2025-2026 öğrenci maliyetleri (EUR/ay).
     * Kaynak: DAAD, Studierendenwerk, Numbeo, WG-Gesucht.
     * Tier: very_expensive | expensive | mid | affordable
     */
    public function run(): void
    {
        $rows = [
            // Very expensive
            ['munchen-q1726',              'very_expensive', 720, 1250, 1700, 360, 49,  105, 130, 160, 110],

            // Expensive
            ['frankfurt-am-main-q1794',    'expensive',      620, 1100, 1500, 340, 49,   95, 130, 150, 100],
            ['hamburg-q1055',              'expensive',      590, 1000, 1400, 330, 40,   90, 130, 140, 100],
            ['berlin-q64',                 'expensive',      560,  950, 1350, 330, 29,   85, 130, 150, 100],
            ['stuttgart-q1022',            'expensive',      590,  980, 1380, 320, 35,   90, 130, 130,  95],
            ['koln-q365',                  'expensive',      550,  900, 1300, 320, 49,   85, 130, 135,  95],
            ['heidelberg-q2966',           'expensive',      540,  880, 1250, 310,  0,   80, 130, 130,  90],

            // Mid
            ['bonn-q586',                  'mid',            500,  820, 1180, 310, 49,   80, 130, 125,  90],
            ['mainz-q1720',                'mid',            490,  800, 1150, 310, 49,   80, 130, 120,  90],
            ['freiburg-im-breisgau-q2833', 'mid',            480,  780, 1100, 300,  0,   78, 130, 120,  85],
            ['tubingen-q3806',             'mid',            470,  760, 1080, 300,  0,   75, 130, 115,  85],
            ['karlsruhe-q1040',            'mid',            420,  680,  980, 295,  0,   75, 130, 115,  85],
            ['munster-q2742',              'mid',            420,  680,  980, 290,  0,   75, 130, 110,  80],
            ['hannover-q1715',             'mid',            400,  660,  940, 290, 49,   75, 130, 110,  80],
            ['bremen-q24879',              'mid',            390,  640,  920, 290,  0,   75, 130, 105,  80],

            // Affordable
            ['leipzig-q2079',              'affordable',     360,  580,  830, 280,  0,   70, 130, 100,  75],
            ['dresden-q1731',              'affordable',     350,  570,  820, 280,  0,   70, 130,  95,  75],
            ['gottingen-q3033',            'affordable',     360,  580,  840, 285,  0,   72, 130,  95,  75],
            ['wurzburg-q2999',             'affordable',     370,  600,  860, 285,  0,   72, 130, 100,  75],
            ['braunschweig-q2773',         'affordable',     340,  560,  800, 280,  0,   70, 130,  95,  75],
        ];

        foreach ($rows as $r) {
            $city = City::where('slug', $r[0])->first();

            if (! $city) {
                $this->command?->warn("City not found: {$r[0]}");
                continue;
            }

            CityCostData::updateOrCreate(
                ['city_id' => $city->id],
                [
                    'tier'             => $r[1],
                    'rent_wg'          => $r[2],
                    'rent_studio'      => $r[3],
                    'rent_apartment'   => $r[4],
                    'food'             => $r[5],
                    'transport'        => $r[6],
                    'utilities'        => $r[7],
                    'health_insurance' => $r[8],
                    'entertainment'    => $r[9],
                    'misc'             => $r[10],
                ]
            );
        }

        $this->command?->info('CityCostData seeded: ' . CityCostData::count() . ' rows.');
    }
}
