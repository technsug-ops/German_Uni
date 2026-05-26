<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Seeder;

/**
 * Yaygın Alman şehir ve eyalet isimlerinin Türkçe karşılıkları.
 * Çoğu Alman şehri Türkçe'de aynı yazılıyor (Berlin, Hamburg, Frankfurt...)
 * — sadece farklı olanları burada saklıyoruz.
 */
class TurkishLocalizationSeeder extends Seeder
{
    private array $cityTr = [
        'München' => 'Münih',
        'Köln' => 'Köln',           // aynı ama emin olalım
        'Nürnberg' => 'Nürnberg',   // bazen Nuremberg / Nürnberg
        'Bayern' => 'Bavyera',
    ];

    private array $stateTr = [
        'Bayern' => 'Bavyera',
        'Niedersachsen' => 'Aşağı Saksonya',
        'Sachsen' => 'Saksonya',
        'Sachsen-Anhalt' => 'Saksonya-Anhalt',
        'Nordrhein-Westfalen' => 'Kuzey Ren-Vestfalya',
        'Rheinland-Pfalz' => 'Renanya-Pfalz',
        'Baden-Württemberg' => 'Baden-Württemberg',
        'Hessen' => 'Hessen',
        'Thüringen' => 'Türingen',
        'Schleswig-Holstein' => 'Schleswig-Holstein',
        'Mecklenburg-Vorpommern' => 'Mecklenburg-Ön Pomeranya',
        'Brandenburg' => 'Brandenburg',
        'Saarland' => 'Saar',
        'Berlin' => 'Berlin',
        'Hamburg' => 'Hamburg',
        'Freie Hansestadt Bremen' => 'Bremen',
    ];

    public function run(): void
    {
        $cityUpdates = 0;
        foreach ($this->cityTr as $de => $tr) {
            $updated = City::where('name_de', $de)->update(['name_tr' => $tr]);
            $cityUpdates += $updated;
        }

        $stateUpdates = 0;
        foreach ($this->stateTr as $de => $tr) {
            $updated = State::where('name_de', $de)->update(['name_tr' => $tr]);
            $stateUpdates += $updated;
        }

        $this->command?->info("Cities updated: {$cityUpdates}");
        $this->command?->info("States updated: {$stateUpdates}");
    }
}
