<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\State;
use App\Models\University;
use App\Services\WikidataService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportFromWikidata extends Command
{
    protected $signature = 'wikidata:import
        {--fresh : Mevcut Wikidata verilerini sil}
        {--type=all : Türü seç (all, universities, states)}
        {--dry-run : DB\'ye yazmadan test et}';

    protected $description = 'Wikidata\'dan Alman eyaletleri ve üniversiteleri çek';

    public function __construct(private WikidataService $wikidata)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('🚀 Wikidata Import Başlıyor!');
        $this->newLine();

        $startTime = now();
        $type = $this->option('type');
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->warn('⚠️  DRY-RUN MODU: Veritabanına yazılmayacak');
            $this->newLine();
        }

        if ($this->option('fresh')) {
            if (!$this->confirm('TÜM Wikidata verileri silinecek. Emin misin?')) {
                return self::FAILURE;
            }
            $this->warn('Mevcut Wikidata verileri siliniyor...');
            University::where('data_source', 'wikidata')->delete();
            $this->info('✓ Silindi');
            $this->newLine();
        }

        if ($type === 'all' || $type === 'states') {
            $this->importStates($dryRun);
        }

        if ($type === 'all' || $type === 'universities') {
            $this->importUniversities($dryRun);
        }

        $duration = $startTime->diffInSeconds(now());
        $this->newLine();
        $this->info('═══════════════════════════════════');
        $this->info("✅ IMPORT TAMAMLANDI ({$duration} saniye)");
        $this->info('═══════════════════════════════════');

        return self::SUCCESS;
    }

    private function importStates(bool $dryRun): void
    {
        $this->info('📍 EYALETLER (BUNDESLÄNDER)');
        $this->info('─────────────────────────────────────');

        $rows = $this->wikidata->getGermanStates();
        if (empty($rows)) {
            $this->error('❌ Eyalet verisi alınamadı!');
            return;
        }

        $unique = $this->dedupeBy($rows, 'state');
        $this->info('Toplam ' . count($unique) . ' eyalet bulundu.');

        $imported = 0;
        $updated = 0;
        $skipped = 0;

        $bar = $this->output->createProgressBar(count($unique));
        $bar->start();

        foreach ($unique as $row) {
            try {
                $wikidataId = $this->wikidata->extractWikidataId(
                    $this->wikidata->extractValue($row, 'state')
                );
                $label = $this->wikidata->extractValue($row, 'stateLabel');

                if (!$wikidataId || !$this->wikidata->isUsableLabel($label)) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                $coords = $this->wikidata->parseCoordinate(
                    $this->wikidata->extractValue($row, 'coordinate')
                );

                $data = [
                    'wikidata_id' => $wikidataId,
                    'name_tr' => $label,
                    'name_de' => $label,
                    'name_en' => $label,
                    'slug' => Str::slug($label),
                    'latitude' => $coords['latitude'],
                    'longitude' => $coords['longitude'],
                    'is_active' => true,
                ];

                if (!$dryRun) {
                    $existing = State::where('wikidata_id', $wikidataId)->first();
                    if ($existing) {
                        $existing->update($data);
                        $updated++;
                    } else {
                        State::create($data);
                        $imported++;
                    }
                }

                $bar->advance();
            } catch (\Exception $e) {
                $this->error("\nEyalet hatası: " . $e->getMessage());
                $skipped++;
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Eklendi', 'Güncellendi', 'Atlandı', 'Toplam'],
            [[$imported, $updated, $skipped, $imported + $updated + $skipped]]
        );
        $this->newLine();
    }

    private function importUniversities(bool $dryRun): void
    {
        $this->info('🏛️ ÜNİVERSİTELER');
        $this->info('─────────────────────────────────────');
        $this->info('Wikidata\'dan veri çekiliyor... (10-60 saniye)');

        $rows = $this->wikidata->getGermanUniversities();
        if (empty($rows)) {
            $this->error('❌ Üniversite verisi alınamadı!');
            return;
        }

        // The SPARQL result has one row per university+type pairing, plus possible duplicates
        // when multiple types apply. Collapse rows to one record per university URI, merging
        // optional fields so we keep the most complete data.
        $merged = $this->mergeByEntity($rows, 'university');
        $this->info('Toplam ' . count($merged) . ' unique üniversite bulundu.');
        $this->newLine();

        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $cityCreated = 0;

        $bar = $this->output->createProgressBar(count($merged));
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% | %elapsed:6s%/%estimated:-6s% | %memory:6s%');
        $bar->start();

        foreach ($merged as $row) {
            try {
                $wikidataId = $this->wikidata->extractWikidataId(
                    $this->wikidata->extractValue($row, 'university')
                );
                $label = $this->wikidata->extractValue($row, 'universityLabel');

                if (!$wikidataId || !$this->wikidata->isUsableLabel($label)) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                $cityId = $this->resolveCity($row, $dryRun, $cityCreated);

                $coords = $this->wikidata->parseCoordinate(
                    $this->wikidata->extractValue($row, 'coordinate')
                );

                $inception = $this->wikidata->extractValue($row, 'inception');
                $foundedYear = $inception ? (int) substr($inception, 0, 4) : null;
                if ($foundedYear !== null && ($foundedYear < 800 || $foundedYear > (int) date('Y'))) {
                    $foundedYear = null;
                }

                $studentCount = $this->wikidata->extractValue($row, 'students');
                $studentCount = is_numeric($studentCount) ? (int) $studentCount : null;

                $type = $this->detectUniversityType($row);

                $slugBase = Str::slug($label);
                $slug = $slugBase !== ''
                    ? $slugBase . '-' . strtolower($wikidataId)
                    : strtolower($wikidataId);

                $data = [
                    'wikidata_id' => $wikidataId,
                    'name_tr' => $label,
                    'name_de' => $label,
                    'name_en' => $label,
                    'slug' => $slug,
                    'short_name' => $this->generateShortName($label),
                    'city_id' => $cityId,
                    'latitude' => $coords['latitude'],
                    'longitude' => $coords['longitude'],
                    'website_url' => $this->wikidata->extractValue($row, 'website'),
                    'type' => $type,
                    'founded_year' => $foundedYear,
                    'student_count' => $studentCount,
                    'logo_url' => $this->processLogoUrl($this->wikidata->extractValue($row, 'logo')),
                    'data_source' => 'wikidata',
                    'last_synced_at' => now(),
                    'is_active' => true,
                ];

                if (!$dryRun) {
                    $existing = University::where('wikidata_id', $wikidataId)->first();
                    if ($existing) {
                        $updateData = array_filter(
                            $data,
                            fn($v, $k) => empty($existing->{$k}) && !empty($v),
                            ARRAY_FILTER_USE_BOTH
                        );
                        if (!empty($updateData)) {
                            $existing->update($updateData);
                            $updated++;
                        }
                    } else {
                        University::create($data);
                        $imported++;
                    }
                }

                $bar->advance();
            } catch (\Exception $e) {
                $this->error("\nUniversite hatası: " . $e->getMessage());
                $skipped++;
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Eklendi', 'Güncellendi', 'Atlandı', 'Yeni Şehir'],
            [[$imported, $updated, $skipped, $cityCreated]]
        );
        $this->newLine();
    }

    private function resolveCity(array $row, bool $dryRun, int &$cityCreated): ?int
    {
        $cityUrl = $this->wikidata->extractValue($row, 'city');
        if (!$cityUrl) {
            return null;
        }

        $cityWikidataId = $this->wikidata->extractWikidataId($cityUrl);
        $cityLabel = $this->wikidata->extractValue($row, 'cityLabel');
        if (!$cityWikidataId || !$this->wikidata->isUsableLabel($cityLabel)) {
            return null;
        }

        if ($dryRun) {
            return null;
        }

        $city = City::firstOrCreate(
            ['wikidata_id' => $cityWikidataId],
            [
                'name_tr' => $cityLabel,
                'name_de' => $cityLabel,
                'name_en' => $cityLabel,
                'slug' => Str::slug($cityLabel) . '-' . strtolower($cityWikidataId),
                'state_id' => null,
                'is_active' => true,
            ]
        );

        if ($city->wasRecentlyCreated) {
            $cityCreated++;
        }

        return $city->id;
    }

    private function dedupeBy(array $rows, string $uriKey): array
    {
        $unique = [];
        foreach ($rows as $row) {
            $uri = $this->wikidata->extractValue($row, $uriKey);
            if ($uri && !isset($unique[$uri])) {
                $unique[$uri] = $row;
            }
        }
        return array_values($unique);
    }

    private function mergeByEntity(array $rows, string $uriKey): array
    {
        $merged = [];
        foreach ($rows as $row) {
            $uri = $this->wikidata->extractValue($row, $uriKey);
            if (!$uri) {
                continue;
            }
            if (!isset($merged[$uri])) {
                $merged[$uri] = $row;
                continue;
            }
            // Fill in any keys that were null/empty in the previous version.
            foreach ($row as $k => $v) {
                if (!isset($merged[$uri][$k]) || ($merged[$uri][$k]['value'] ?? '') === '') {
                    $merged[$uri][$k] = $v;
                }
            }
        }
        return array_values($merged);
    }

    private function detectUniversityType(array $row): string
    {
        $label = strtolower((string) $this->wikidata->extractValue($row, 'typeLabel', ''));
        $typeUri = (string) $this->wikidata->extractValue($row, 'type', '');

        if (str_contains($typeUri, 'Q1664720') || str_contains($label, 'private')) {
            return 'private';
        }
        if (str_contains($typeUri, 'Q1364732') || str_contains($label, 'applied') || str_contains($label, 'fachhochschule')) {
            return 'applied_sciences';
        }
        if (str_contains($typeUri, 'Q4187951') || str_contains($label, 'art') || str_contains($label, 'music') || str_contains($label, 'kunst')) {
            return 'art';
        }
        if (str_contains($label, 'theology') || str_contains($label, 'religion')) {
            return 'religion';
        }
        return 'public';
    }

    private function generateShortName(string $name): ?string
    {
        $words = preg_split('/\s+/', trim($name));
        if (count($words) <= 2) {
            return null;
        }
        $stop = ['der', 'die', 'das', 'des', 'an', 'für', 'zu', 'in', 'of', 'the', 'und', 'and', 'fur'];

        $short = '';
        foreach ($words as $word) {
            $clean = preg_replace('/[^A-Za-zÄÖÜäöüß]/u', '', $word);
            if ($clean === '' || in_array(strtolower($clean), $stop, true)) {
                continue;
            }
            $short .= strtoupper(mb_substr($clean, 0, 1));
        }

        $len = strlen($short);
        return ($len >= 2 && $len <= 6) ? $short : null;
    }

    private function processLogoUrl(?string $url): ?string
    {
        if (!$url) {
            return null;
        }
        if (str_contains($url, 'commons.wikimedia.org/wiki/Special:FilePath/')) {
            return $url . '?width=300';
        }
        return $url;
    }
}
