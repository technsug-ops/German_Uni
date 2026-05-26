<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\State;
use App\Services\WikidataService;
use Illuminate\Console\Command;

class AssignCityStates extends Command
{
    protected $signature = 'cities:assign-states
        {--dry-run : DB\'ye yazmadan test et}
        {--only-empty : Sadece state_id boş olan şehirler için çalıştır}';

    protected $description = 'Her şehrin Alman eyaletini (Bundesland) Wikidata üzerinden bul ve state_id alanını doldur.';

    public function __construct(private WikidataService $wikidata)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('🗺️  Şehir → Eyalet eşleştirme başlıyor');
        $this->newLine();

        $dryRun = (bool) $this->option('dry-run');
        $onlyEmpty = (bool) $this->option('only-empty');

        $stateLookup = State::whereNotNull('wikidata_id')->pluck('id', 'wikidata_id')->all();
        if (empty($stateLookup)) {
            $this->error('❌ Hiç eyalet bulunamadı, önce wikidata:import çalıştır.');
            return self::FAILURE;
        }

        $citiesQuery = City::whereNotNull('wikidata_id');
        if ($onlyEmpty) {
            $citiesQuery->whereNull('state_id');
        }
        $cities = $citiesQuery->get(['id', 'wikidata_id', 'state_id', 'name_de']);

        if ($cities->isEmpty()) {
            $this->warn('Eşleştirilecek şehir yok.');
            return self::SUCCESS;
        }

        $this->info("Wikidata'dan {$cities->count()} şehir için eyalet bilgisi çekiliyor...");
        $mapping = $this->wikidata->getCityStateMapping($cities->pluck('wikidata_id')->all());
        $this->info('Wikidata yanıt: ' . count($mapping) . ' eşleşme bulundu.');
        $this->newLine();

        $updated = 0;
        $unchanged = 0;
        $unresolved = [];

        $bar = $this->output->createProgressBar($cities->count());
        $bar->start();

        foreach ($cities as $city) {
            $stateQid = $mapping[$city->wikidata_id] ?? null;
            $stateId = $stateQid ? ($stateLookup[$stateQid] ?? null) : null;

            if ($stateId === null) {
                $unresolved[] = "{$city->name_de} ({$city->wikidata_id})";
                $bar->advance();
                continue;
            }

            if ($city->state_id === $stateId) {
                $unchanged++;
                $bar->advance();
                continue;
            }

            if (!$dryRun) {
                $city->state_id = $stateId;
                $city->save();
            }
            $updated++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Güncellendi', 'Aynı', 'Eyalet bulunamadı'],
            [[$updated, $unchanged, count($unresolved)]]
        );

        if (!empty($unresolved)) {
            $this->newLine();
            $this->warn('Eyalet bulunamayan şehirler (' . count($unresolved) . '):');
            foreach (array_slice($unresolved, 0, 20) as $name) {
                $this->line("  - {$name}");
            }
            if (count($unresolved) > 20) {
                $this->line('  ... ve ' . (count($unresolved) - 20) . ' tane daha');
            }
        }

        if ($dryRun) {
            $this->newLine();
            $this->warn('⚠️  DRY-RUN modu: değişiklik yapılmadı.');
        }

        return self::SUCCESS;
    }
}
