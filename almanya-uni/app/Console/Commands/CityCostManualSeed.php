<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\CityCostData;
use Illuminate\Console\Command;

/**
 * Kullanıcı tarafından manuel olarak hazırlanan 26 şehir için yaşam maliyeti verisi.
 * WG odası + öğrenci yaşamı + temel sosyal giderler baz alındı (2025-2026 piyasa).
 * Kaynak: Yapra manuel curasyon (DAAD + güncel piyasa).
 */
class CityCostManualSeed extends Command
{
    protected $signature = 'cities:cost-seed {--force : Üzerine yaz}';

    protected $description = '26 şehir için manuel curated yaşam maliyeti verisini upsert et';

    private array $data = [
        ['city' => 'Berlin',     'rent' => 750, 'food' => 320, 'transport' => 63, 'other' => 260],
        ['city' => 'München',    'rent' => 950, 'food' => 340, 'transport' => 63, 'other' => 320],
        ['city' => 'Hamburg',    'rent' => 720, 'food' => 310, 'transport' => 63, 'other' => 250],
        ['city' => 'Köln',       'rent' => 680, 'food' => 300, 'transport' => 63, 'other' => 240],
        ['city' => 'Frankfurt am Main', 'rent' => 780, 'food' => 320, 'transport' => 63, 'other' => 280, 'aliases' => ['Frankfurt']],
        ['city' => 'Stuttgart',  'rent' => 760, 'food' => 310, 'transport' => 63, 'other' => 260],
        ['city' => 'Düsseldorf', 'rent' => 700, 'food' => 300, 'transport' => 63, 'other' => 250],
        ['city' => 'Heidelberg', 'rent' => 720, 'food' => 300, 'transport' => 63, 'other' => 250],
        ['city' => 'Freiburg im Breisgau', 'rent' => 700, 'food' => 300, 'transport' => 63, 'other' => 240, 'aliases' => ['Freiburg']],
        ['city' => 'Ulm',        'rent' => 620, 'food' => 290, 'transport' => 63, 'other' => 220],
        ['city' => 'Nürnberg',   'rent' => 620, 'food' => 290, 'transport' => 63, 'other' => 220],
        ['city' => 'Hannover',   'rent' => 600, 'food' => 290, 'transport' => 63, 'other' => 220],
        ['city' => 'Bremen',     'rent' => 580, 'food' => 280, 'transport' => 63, 'other' => 210],
        ['city' => 'Dortmund',   'rent' => 520, 'food' => 280, 'transport' => 63, 'other' => 210],
        ['city' => 'Essen',      'rent' => 520, 'food' => 280, 'transport' => 63, 'other' => 210],
        ['city' => 'Duisburg',   'rent' => 500, 'food' => 270, 'transport' => 63, 'other' => 200],
        ['city' => 'Bochum',     'rent' => 500, 'food' => 270, 'transport' => 63, 'other' => 200],
        ['city' => 'Aachen',     'rent' => 580, 'food' => 280, 'transport' => 63, 'other' => 220],
        ['city' => 'Wiesbaden',  'rent' => 650, 'food' => 290, 'transport' => 63, 'other' => 230],
        ['city' => 'Kassel',     'rent' => 500, 'food' => 270, 'transport' => 63, 'other' => 200],
        ['city' => 'Marburg',    'rent' => 520, 'food' => 270, 'transport' => 63, 'other' => 210],
        ['city' => 'Gießen',     'rent' => 500, 'food' => 270, 'transport' => 63, 'other' => 200],
        ['city' => 'Dresden',    'rent' => 520, 'food' => 270, 'transport' => 63, 'other' => 210],
        ['city' => 'Leipzig',    'rent' => 550, 'food' => 280, 'transport' => 63, 'other' => 220],
        ['city' => 'Erfurt',     'rent' => 480, 'food' => 260, 'transport' => 63, 'other' => 190],
        ['city' => 'Bonn',       'rent' => 650, 'food' => 290, 'transport' => 63, 'other' => 230],
    ];

    public function handle(): int
    {
        $upserted = 0;
        $notFound = 0;

        foreach ($this->data as $row) {
            $names = array_merge([$row['city']], $row['aliases'] ?? []);
            $city = City::whereIn('name_de', $names)->first();

            if (!$city) {
                $this->warn("❌ Şehir bulunamadı: {$row['city']}");
                $notFound++;
                continue;
            }

            $total = $row['rent'] + $row['food'] + $row['transport'] + $row['other'];

            // "Diğer" 4 alt-kaleme böl:
            // health_insurance = 100 € (sabit, öğrenci 2025 tarife)
            // utilities = 70 € (internet+tel+elektrik approx)
            // entertainment ve misc kalanı paylaşır (yarı yarıya)
            $healthIns = 100;
            $utilities = 70;
            $remaining = max(0, $row['other'] - $healthIns - $utilities);
            $entertainment = (int) round($remaining * 0.5);
            $misc = $remaining - $entertainment;

            // rent_studio / rent_apartment yaklaşımı
            $rentWg = $row['rent'];
            $rentStudio = (int) round($rentWg * 1.6);
            $rentApartment = (int) round($rentWg * 2.2);

            // Tier:
            $tier = match (true) {
                $total >= 1500 => 'very_expensive',
                $total >= 1250 => 'expensive',
                $total >= 1050 => 'mid',
                default        => 'affordable',
            };

            $existing = CityCostData::where('city_id', $city->id)->first();
            if ($existing && !$this->option('force')) {
                $this->line("  • {$city->name_de}: zaten var (--force ile üzerine yaz)");
                continue;
            }

            CityCostData::updateOrCreate(
                ['city_id' => $city->id],
                [
                    'tier' => $tier,
                    'rent_wg' => $rentWg,
                    'rent_studio' => $rentStudio,
                    'rent_apartment' => $rentApartment,
                    'food' => $row['food'],
                    'transport' => $row['transport'],
                    'utilities' => $utilities,
                    'health_insurance' => $healthIns,
                    'entertainment' => $entertainment,
                    'misc' => $misc,
                ]
            );

            $this->info("  ✅ {$city->name_de}: {$total}€ ({$tier})");
            $upserted++;
        }

        $this->newLine();
        $this->info("✅ {$upserted} şehir upsert edildi");
        if ($notFound > 0) $this->warn("❌ {$notFound} şehir bulunamadı");
        return self::SUCCESS;
    }
}
