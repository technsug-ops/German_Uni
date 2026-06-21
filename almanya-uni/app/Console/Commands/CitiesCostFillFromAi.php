<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\CityCostData;
use Illuminate\Console\Command;

/**
 * 3-katmanlı maliyet doldurma:
 *  Katman 1 — Manuel veri (city_cost_data zaten dolu) → ATLA
 *  Katman 2 — AI content_blocks içindeki cost_of_living block → parse + structured
 *  Katman 3 — Eyalet+nüfus interpolasyon → AI'sı olmayan küçük şehirler
 */
class CitiesCostFillFromAi extends Command
{
    protected $signature = 'cities:cost-fill
        {--force : Mevcut city_cost_data\'yı da üzerine yaz}
        {--skip-ai : Sadece interpolation yap}
        {--dry-run : Raporla, yazma}';

    protected $description = 'Tüm üni şehirleri için maliyet verisini doldur (AI parse + eyalet interpolation)';

    /**
     * Eyalet baseline (WG kira, yemek, eğlence, misc).
     * Tablo kullanıcının verdiği aralıklardan ortalama alındı.
     */
    private array $stateBaseline = [
        // Pahalı eyaletler
        'Bayern'                  => ['rent' => 800, 'food' => 310, 'ent' => 90, 'misc' => 60, 'tier' => 'expensive'],
        'Hessen'                  => ['rent' => 650, 'food' => 290, 'ent' => 80, 'misc' => 50, 'tier' => 'expensive'],
        'Hamburg'                 => ['rent' => 720, 'food' => 310, 'ent' => 90, 'misc' => 60, 'tier' => 'expensive'],
        'Baden-Württemberg'       => ['rent' => 680, 'food' => 300, 'ent' => 85, 'misc' => 55, 'tier' => 'expensive'],
        'Berlin'                  => ['rent' => 750, 'food' => 320, 'ent' => 95, 'misc' => 65, 'tier' => 'expensive'],
        // Orta
        'Nordrhein-Westfalen'     => ['rent' => 550, 'food' => 285, 'ent' => 70, 'misc' => 45, 'tier' => 'mid'],
        'Niedersachsen'           => ['rent' => 540, 'food' => 280, 'ent' => 70, 'misc' => 40, 'tier' => 'mid'],
        'Schleswig-Holstein'      => ['rent' => 530, 'food' => 275, 'ent' => 65, 'misc' => 40, 'tier' => 'mid'],
        'Rheinland-Pfalz'         => ['rent' => 530, 'food' => 275, 'ent' => 65, 'misc' => 40, 'tier' => 'mid'],
        'Saarland'                => ['rent' => 500, 'food' => 270, 'ent' => 60, 'misc' => 35, 'tier' => 'affordable'],
        'Freie Hansestadt Bremen' => ['rent' => 580, 'food' => 280, 'ent' => 70, 'misc' => 45, 'tier' => 'mid'],
        // Doğu (genelde uygun)
        'Sachsen'                 => ['rent' => 500, 'food' => 265, 'ent' => 60, 'misc' => 35, 'tier' => 'affordable'],
        'Sachsen-Anhalt'          => ['rent' => 470, 'food' => 260, 'ent' => 55, 'misc' => 30, 'tier' => 'affordable'],
        'Thüringen'               => ['rent' => 480, 'food' => 260, 'ent' => 60, 'misc' => 30, 'tier' => 'affordable'],
        'Brandenburg'             => ['rent' => 520, 'food' => 270, 'ent' => 65, 'misc' => 35, 'tier' => 'affordable'],
        'Mecklenburg-Vorpommern'  => ['rent' => 460, 'food' => 255, 'ent' => 55, 'misc' => 30, 'tier' => 'affordable'],
    ];

    public function handle(): int
    {
        $cities = City::query()
            ->whereHas('universities', fn ($q) => $q->where('is_active', 1))
            ->with('state:id,name_de')
            ->get(['id', 'name_de', 'state_id', 'population', 'content_blocks']);

        $this->info("📍 {$cities->count()} üni-içerikli şehir taranıyor");

        $skipped = 0; $aiFilled = 0; $interpFilled = 0; $manualExists = 0;
        $bar = $this->output->createProgressBar($cities->count());
        $bar->start();

        foreach ($cities as $city) {
            $existing = CityCostData::where('city_id', $city->id)->first();
            if ($existing && !$this->option('force')) {
                $manualExists++;
                $bar->advance();
                continue;
            }

            // Katman 2: AI cost_of_living block parse
            $data = null;
            $source = null;
            if (!$this->option('skip-ai')) {
                $data = $this->parseAiCostBlock($city);
                if ($data) $source = 'ai';
            }

            // Katman 3: Eyalet interpolation
            if (!$data) {
                $data = $this->interpolate($city);
                if ($data) $source = 'interp';
            }

            if (!$data) {
                $skipped++;
                $bar->advance();
                continue;
            }

            if (!$this->option('dry-run')) {
                CityCostData::updateOrCreate(['city_id' => $city->id], $data);
            }

            $source === 'ai' ? $aiFilled++ : $interpFilled++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ AI'dan doldurulan: {$aiFilled}");
        $this->info("✅ Eyalet interpolation: {$interpFilled}");
        $this->line("⏭️  Mevcut manuel veri (atlandı): {$manualExists}");
        if ($skipped > 0) $this->warn("❌ Atlandı (hiç veri yok): {$skipped}");
        if ($this->option('dry-run')) $this->warn('Dry-run — değişiklik yapılmadı');

        return self::SUCCESS;
    }

    /**
     * Şehrin content_blocks'undaki cost_of_living block'unu parse et.
     */
    private function parseAiCostBlock(City $city): ?array
    {
        $block = collect($city->content_blocks ?? [])->firstWhere('type', 'cost_of_living');
        if (!$block || empty($block['items'])) return null;

        $parseRange = function (string $amount): float {
            $clean = trim(str_replace([',', ' ', '€', 'EUR'], ['.', '', '', ''], $amount));
            if (preg_match('/(\d+(?:\.\d+)?)\s*[-–]\s*(\d+(?:\.\d+)?)/', $clean, $m)) {
                return ((float) $m[1] + (float) $m[2]) / 2;
            }
            if (preg_match('/(\d+(?:\.\d+)?)/', $clean, $m)) return (float) $m[1];
            return 0;
        };

        $rent = $food = $transport = $entertainment = $misc = 0;

        foreach ($block['items'] as $item) {
            $label = mb_strtolower($item['label'] ?? '');
            $value = $parseRange($item['amount'] ?? '');
            if ($value <= 0) continue;

            if (preg_match('/kira|wohn|miete|wg/i', $label) && !$rent) {
                $rent = $value;
            } elseif (preg_match('/yemek|food|essen|nahrung/i', $label) && !$food) {
                $food = $value;
            } elseif (preg_match('/ulaşım|transport|verkehr|semester/i', $label) && !$transport) {
                $transport = $value;
            } elseif (preg_match('/sigorta|insurance|kranken/i', $label)) {
                // Sağlık zaten sabit, atla
            } elseif (preg_match('/eğlen|kişisel|other|diğer|sosyal|leisure/i', $label)) {
                $misc += $value;
            }
        }

        if ($rent < 200) return null; // Anlamsız veri

        return [
            'tier' => $this->tier($rent + $food),
            'rent_wg' => (int) round($rent),
            'rent_studio' => (int) round($rent * 1.6),
            'rent_apartment' => (int) round($rent * 2.2),
            'food' => (int) round($food ?: 280),
            'transport' => (int) round($transport ?: 63),
            'utilities' => 70,
            'health_insurance' => 100,
            'entertainment' => (int) round(max(50, $misc * 0.5)),
            'misc' => (int) round(max(40, $misc * 0.5)),
        ];
    }

    /**
     * Eyalet + nüfus baseline.
     */
    private function interpolate(City $city): ?array
    {
        $stateName = $city->state?->name_de;
        if (!$stateName) return null;

        $base = $this->stateBaseline[$stateName] ?? null;
        if (!$base) return null;

        // Nüfus ayarlaması: büyük şehir +10%, küçük -5%
        $popMult = match (true) {
            $city->population >= 500_000 => 1.10,
            $city->population >= 200_000 => 1.05,
            $city->population <= 50_000  => 0.95,
            default                       => 1.0,
        };

        $rent = (int) round($base['rent'] * $popMult);
        return [
            'tier' => $base['tier'],
            'rent_wg' => $rent,
            'rent_studio' => (int) round($rent * 1.6),
            'rent_apartment' => (int) round($rent * 2.2),
            'food' => $base['food'],
            'transport' => 63,
            'utilities' => 70,
            'health_insurance' => 100,
            'entertainment' => $base['ent'],
            'misc' => $base['misc'],
        ];
    }

    private function tier(int $base): string
    {
        return match (true) {
            $base >= 1100 => 'very_expensive',
            $base >= 900  => 'expensive',
            $base >= 750  => 'mid',
            default       => 'affordable',
        };
    }
}
