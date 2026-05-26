<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Studienkolleg;
use Illuminate\Database\Seeder;

/**
 * 30+ resmi Studienkolleg (Almanya). Listede staatlich (devlet) + privat (özel) ayrımı yapılır.
 * Kaynak: https://www.studienkollegs.de + https://www.daad.de (resmi listeler).
 */
class StudienkollegSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // ===== STAATLICH (Devlet — ücretsiz) =====
            ['name' => 'Studienkolleg an der TU Berlin',           'city' => 'Berlin',          'tracks' => ['T','M','W','G','S'], 'website' => 'https://www.tu.berlin/skb', 'university' => 'Technische Universität Berlin'],
            ['name' => 'Studienkolleg an der FU Berlin',           'city' => 'Berlin',          'tracks' => ['G','S'],             'website' => 'https://www.fu-berlin.de/studium/international/studkoll'],
            ['name' => 'Studienkolleg München (LMU)',              'city' => 'München',         'tracks' => ['T','M','W','G','S'], 'website' => 'https://www.studienkolleg-muenchen.de', 'university' => 'Ludwig-Maximilians-Universität München'],
            ['name' => 'Studienkolleg an der Universität Hamburg', 'city' => 'Hamburg',         'tracks' => ['T','M','W','G','S'], 'website' => 'https://www.uni-hamburg.de/studienkolleg'],
            ['name' => 'Studienkolleg an der Universität Köln',    'city' => 'Köln',            'tracks' => ['T','M','W','G','S'], 'website' => 'https://www.studienkolleg.uni-koeln.de'],
            ['name' => 'Studienkolleg Frankfurt am Main',          'city' => 'Frankfurt am Main','tracks' => ['T','M','W','G','S'], 'website' => 'https://www.uni-frankfurt.de/studienkolleg'],
            ['name' => 'Niedersächsisches Studienkolleg Hannover', 'city' => 'Hannover',        'tracks' => ['T','M','W','G','S'], 'website' => 'https://www.studienkolleg-hannover.de'],
            ['name' => 'Studienkolleg Mittelhessen (Marburg)',     'city' => 'Marburg',         'tracks' => ['T','M','W','G','S'], 'website' => 'https://www.uni-marburg.de/de/studienkolleg'],
            ['name' => 'Studienkolleg Heidelberg',                 'city' => 'Heidelberg',      'tracks' => ['T','M','W','G','S'], 'website' => 'https://www.isz.uni-heidelberg.de/skd'],
            ['name' => 'Studienkolleg Karlsruhe (KIT)',            'city' => 'Karlsruhe',       'tracks' => ['T','W'],             'website' => 'https://www.stk.kit.edu'],
            ['name' => 'Studienkolleg Konstanz',                   'city' => 'Konstanz',        'tracks' => ['T','M','W','G','S'], 'website' => 'https://www.studienkolleg-konstanz.de'],
            ['name' => 'Studienkolleg Mainz',                      'city' => 'Mainz',           'tracks' => ['T','M','W','G','S'], 'website' => 'https://www.studkoll.uni-mainz.de'],
            ['name' => 'Studienkolleg Halle (Saale)',              'city' => 'Halle (Saale)',   'tracks' => ['T','M','W','G','S'], 'website' => 'https://www.studienkolleg-halle.de'],
            ['name' => 'Studienkolleg Sachsen (Leipzig)',          'city' => 'Leipzig',         'tracks' => ['T','M','W','G','S'], 'website' => 'https://www.studienkolleg-sachsen.de'],
            ['name' => 'Studienkolleg Greifswald',                 'city' => 'Greifswald',      'tracks' => ['T','M','W','G','S'], 'website' => 'https://www.studienkolleg.uni-greifswald.de'],
            ['name' => 'Studienkolleg Bochum',                     'city' => 'Bochum',          'tracks' => ['T','M','W','G','S'], 'website' => 'https://www.studienkolleg.rub.de'],
            ['name' => 'Studienkolleg Düsseldorf',                 'city' => 'Düsseldorf',      'tracks' => ['T','M','W','G','S'], 'website' => 'https://www.uni-duesseldorf.de/home/studium/studienkolleg.html'],
            ['name' => 'Studienkolleg Münster',                    'city' => 'Münster',         'tracks' => ['T','M','W','G','S'], 'website' => 'https://www.uni-muenster.de/StudienkollegMuenster'],
            ['name' => 'Studienkolleg Paderborn',                  'city' => 'Paderborn',       'tracks' => ['T','W'],             'website' => 'https://www.uni-paderborn.de/studienkolleg'],
            ['name' => 'Studienkolleg Coburg (HS Coburg)',         'city' => 'Coburg',          'tracks' => ['T','W'],             'website' => 'https://www.hs-coburg.de/studienkolleg'],
            ['name' => 'Studienkolleg Nordhausen',                 'city' => 'Nordhausen',      'tracks' => ['T','W'],             'website' => 'https://www.hs-nordhausen.de/studienkolleg'],
            ['name' => 'Studienkolleg Wismar',                     'city' => 'Wismar',          'tracks' => ['T','W'],             'website' => 'https://www.hs-wismar.de/studienkolleg'],
            ['name' => 'Studienkolleg Zittau/Görlitz',             'city' => 'Zittau',          'tracks' => ['T','W'],             'website' => 'https://www.hszg.de/studienkolleg'],
            ['name' => 'Studienkolleg Anhalt (Köthen)',            'city' => 'Köthen (Anhalt)', 'tracks' => ['T','W'],             'website' => 'https://www.hs-anhalt.de/studienkolleg'],
            ['name' => 'Studienkolleg Glauchau',                   'city' => 'Glauchau',        'tracks' => ['T','W'],             'website' => 'https://www.ba-sachsen.de/studienkolleg-glauchau'],

            // ===== PRIVAT (Ücretli, hızlı kabul) =====
            ['name' => 'FIBAA-akkreditiertes Studienkolleg Hamburg (priv.)', 'city' => 'Hamburg', 'tracks' => ['T','M','W','G','S'], 'website' => 'https://www.studienkolleg-hamburg.de', 'type' => 'privat', 'fee' => 12500],
            ['name' => 'Studienkolleg Bonn (priv.)',               'city' => 'Bonn',            'tracks' => ['T','M','W','G','S'], 'website' => 'https://www.studienkolleg-bonn.de', 'type' => 'privat', 'fee' => 9500],
            ['name' => 'Studienkolleg Düsseldorf (priv.)',         'city' => 'Düsseldorf',      'tracks' => ['T','M','W','G','S'], 'website' => 'https://www.studienkolleg-duesseldorf.de', 'type' => 'privat', 'fee' => 10000],
            ['name' => 'IB Studienkolleg Friedrichshafen',         'city' => 'Friedrichshafen', 'tracks' => ['T','W'],             'website' => 'https://www.ib-studienkolleg.de', 'type' => 'privat', 'fee' => 11000],
            ['name' => 'Studienkolleg Berlin (priv. AfBB)',        'city' => 'Berlin',          'tracks' => ['T','M','W','G','S'], 'website' => 'https://www.studienkolleg-berlin.de', 'type' => 'privat', 'fee' => 12000],
        ];

        $sort = 0;
        foreach ($items as $row) {
            $slug = \Illuminate\Support\Str::slug($row['name']);
            // Şehir bağla — eşleşmezse name_cache'e koy
            $city = isset($row['city']) ? City::query()
                ->where('name_de', $row['city'])
                ->orWhere('name_en', $row['city'])
                ->orWhere('name_tr', $row['city'])
                ->first() : null;

            Studienkolleg::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $row['name'],
                    'type' => $row['type'] ?? 'staatlich',
                    'city_id' => $city?->id,
                    'city_name_cache' => $row['city'] ?? null,
                    'state_id' => $city?->state_id,
                    'tracks' => $row['tracks'],
                    'website_url' => $row['website'],
                    'semester_fee_eur' => $row['fee'] ?? 0,
                    'entrance_exam' => 'aufnahmetest',
                    'description' => [
                        'en' => "Foundation year programme (Studienkolleg) preparing international students for admission to German universities. Located in {$row['city']}.",
                        'tr' => "Uluslararası öğrencileri Alman üniversitelerine kabule hazırlayan Studienkolleg (hazırlık) programı. {$row['city']} şehrinde.",
                        'de' => "Studienkolleg zur Vorbereitung internationaler Studierender auf das Studium an deutschen Hochschulen. Standort: {$row['city']}.",
                    ],
                    'is_active' => true,
                    'sort_order' => $sort++,
                ]
            );
        }

        $this->command->info("Seeded " . count($items) . " Studienkollegs.");
    }
}
