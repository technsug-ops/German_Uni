<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\University;
use Illuminate\Console\Command;

class UniversitiesAssignCity extends Command
{
    protected $signature = 'universities:assign-city
        {--dry-run : Sadece raporla}';

    protected $description = 'Üni adından şehir tahmini → city_id NULL olanları otomatik doldur.';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $this->info($dryRun ? '🔍 DRY-RUN' : '▶ EXECUTE');

        if (!$dryRun && method_exists(University::class, 'disableSearchSyncing')) {
            University::disableSearchSyncing();
        }

        // Şehirleri uzun isimden kısaya sırala (önce "Frankfurt am Main", sonra "Frankfurt")
        $cities = City::query()
            ->select('id', 'name_de', 'name_tr', 'name_en')
            ->get()
            ->sortByDesc(fn ($c) => mb_strlen($c->name_de))
            ->values();

        $unmatched = University::whereNull('city_id')->where('is_active', true)->get();
        $this->line('Başlangıç: ' . $unmatched->count() . ' city_id NULL üni');

        $matched = 0;
        $samples = [];

        foreach ($unmatched as $uni) {
            $foundCity = null;

            foreach ($cities as $city) {
                $needles = array_filter([$city->name_de, $city->name_tr, $city->name_en]);
                foreach ($needles as $needle) {
                    // İsim minimum 4 char, "am" "an" gibi short stop word'leri atla
                    if (mb_strlen($needle) < 4) continue;

                    $pattern = '/(?:^|[\s,\-\/\(\)])' . preg_quote($needle, '/') . '(?:$|[\s,\-\/\(\)])/iu';

                    foreach ([$uni->name_de, $uni->name_en, $uni->name_tr] as $hay) {
                        if (!$hay) continue;
                        if (preg_match($pattern, $hay)) {
                            $foundCity = $city;
                            break 3;
                        }
                    }
                }
            }

            if ($foundCity) {
                $matched++;
                if (count($samples) < 12) {
                    $samples[] = [
                        'id' => $uni->id,
                        'name' => mb_substr($uni->name_de ?? '', 0, 50),
                        'city' => $foundCity->name_de,
                    ];
                }
                if (!$dryRun) {
                    $uni->update(['city_id' => $foundCity->id]);
                }
            }
        }

        $this->newLine();
        $this->info('═══ ÖZET ═══');
        $this->line(sprintf('  Eşleşen üni: %d', $matched));
        $this->line(sprintf('  Hâlâ eşleşmeyen: %d', $unmatched->count() - $matched));

        if ($samples) {
            $this->newLine();
            $this->info('═══ ÖRNEKLER ═══');
            $this->table(['ID', 'Üni', 'Atanacak şehir'], array_map(fn ($s) => [$s['id'], $s['name'], $s['city']], $samples));
        }

        return self::SUCCESS;
    }
}
