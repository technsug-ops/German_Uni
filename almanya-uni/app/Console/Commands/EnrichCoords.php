<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\University;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class EnrichCoords extends Command
{
    protected $signature = 'coords:enrich
        {--dry-run : Sadece raporla}
        {--skip-cities : Sadece üni koordinatları}
        {--skip-unis : Sadece şehir koordinatları}';

    protected $description = 'Wikidata SPARQL + city center fallback ile latitude/longitude doldur.';

    private const SPARQL = 'https://query.wikidata.org/sparql';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $this->info($dryRun ? '🔍 DRY-RUN' : '▶ EXECUTE');

        if (!$dryRun) {
            if (method_exists(University::class, 'disableSearchSyncing')) {
                University::disableSearchSyncing();
            }
        }

        if (!$this->option('skip-cities')) {
            $this->phase1Cities($dryRun);
        }
        if (!$this->option('skip-unis')) {
            $this->phase2UnisFromWikidata($dryRun);
            $this->phase3UnisCityFallback($dryRun);
        }

        $this->newLine();
        $this->info('═══ SONUÇ ═══');
        $this->line('Üni koordinatlı: ' . University::whereNotNull('latitude')->count() . ' / ' . University::count());
        $this->line('Üni haritada YOK: ' . University::whereNull('latitude')->count());
        $this->line('Şehir koordinatlı: ' . City::whereNotNull('latitude')->count() . ' / ' . City::count());

        return self::SUCCESS;
    }

    private function phase1Cities(bool $dryRun): void
    {
        $this->newLine();
        $this->line('━━━ Phase 1: 180 şehir Wikidata\'dan koordinat ━━━');

        $cities = City::whereNotNull('wikidata_id')->whereNull('latitude')->get(['id', 'wikidata_id', 'name_de']);
        $this->line('Hedef: ' . $cities->count() . ' şehir');

        $coords = $this->fetchCoords($cities->pluck('wikidata_id')->all());
        $updated = 0;
        foreach ($cities as $c) {
            if (!isset($coords[$c->wikidata_id])) continue;
            [$lat, $lon] = $coords[$c->wikidata_id];
            if (!$dryRun) {
                $c->update(['latitude' => $lat, 'longitude' => $lon]);
            }
            $updated++;
        }
        $this->info("  ✓ $updated şehre koordinat eklendi");
    }

    private function phase2UnisFromWikidata(bool $dryRun): void
    {
        $this->newLine();
        $this->line('━━━ Phase 2: Wikidata Q-id\'li üniler ━━━');

        $unis = University::whereNotNull('wikidata_id')->whereNull('latitude')->get(['id', 'wikidata_id', 'name_de']);
        $this->line('Hedef: ' . $unis->count() . ' üni');

        $coords = $this->fetchCoords($unis->pluck('wikidata_id')->all());
        $updated = 0;
        foreach ($unis as $u) {
            if (!isset($coords[$u->wikidata_id])) continue;
            [$lat, $lon] = $coords[$u->wikidata_id];
            if (!$dryRun) {
                $u->update(['latitude' => $lat, 'longitude' => $lon]);
            }
            $updated++;
        }
        $this->info("  ✓ $updated üniye Wikidata koordinatı eklendi");
    }

    private function phase3UnisCityFallback(bool $dryRun): void
    {
        $this->newLine();
        $this->line('━━━ Phase 3: city_id\'li ünilere şehir merkez fallback ━━━');

        $unis = University::whereNotNull('city_id')
            ->whereNull('latitude')
            ->with('city:id,latitude,longitude')
            ->get();
        $this->line('Hedef: ' . $unis->count() . ' üni');

        $updated = 0;
        $skipped = 0;
        foreach ($unis as $u) {
            if (!$u->city || $u->city->latitude === null) {
                $skipped++;
                continue;
            }
            if (!$dryRun) {
                $u->update([
                    'latitude' => $u->city->latitude,
                    'longitude' => $u->city->longitude,
                ]);
            }
            $updated++;
        }
        $this->info("  ✓ $updated üniye şehir merkez koordinatı atandı ($skipped şehir koordinatsız)");
    }

    /**
     * @param  array<int, string>  $qids
     * @return array<string, array{0: float, 1: float}>
     */
    private function fetchCoords(array $qids): array
    {
        $qids = array_values(array_filter(array_unique($qids)));
        if (empty($qids)) return [];

        $coords = [];
        // Batch 50 per query (URL length limit)
        foreach (array_chunk($qids, 50) as $batch) {
            $values = implode(' ', array_map(fn ($q) => 'wd:' . $q, $batch));
            $query = "SELECT ?item ?lat ?lon WHERE {\n  VALUES ?item { $values }\n  ?item p:P625 ?statement .\n  ?statement psv:P625 ?coordValue .\n  ?coordValue wikibase:geoLatitude ?lat .\n  ?coordValue wikibase:geoLongitude ?lon .\n}";

            $bar = $this->output->createProgressBar(1);
            $bar->setFormat('  fetching batch...');
            try {
                $resp = Http::withHeaders([
                    'User-Agent' => 'AlmanyaUniBot/1.0 (+https://almanyauni.de/bot)',
                    'Accept' => 'application/sparql-results+json',
                ])->timeout(60)->get(self::SPARQL, ['query' => $query]);

                if ($resp->successful()) {
                    $bindings = $resp->json('results.bindings', []);
                    foreach ($bindings as $b) {
                        $uri = $b['item']['value'] ?? '';
                        if (preg_match('/Q\d+$/', $uri, $m)) {
                            $coords[$m[0]] = [(float) $b['lat']['value'], (float) $b['lon']['value']];
                        }
                    }
                } else {
                    $this->warn('  SPARQL batch FAIL HTTP ' . $resp->status());
                }
            } catch (\Throwable $e) {
                $this->warn('  SPARQL batch exception: ' . substr($e->getMessage(), 0, 100));
            }
            $bar->finish();
            $this->newLine();
            usleep(800 * 1000);
        }

        return $coords;
    }
}
