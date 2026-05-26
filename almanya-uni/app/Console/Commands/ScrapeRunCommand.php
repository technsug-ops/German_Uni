<?php

namespace App\Console\Commands;

use App\Models\ScrapedProgram;
use App\Models\ScrapeRun as ScrapeRunModel;
use App\Models\ScrapeSource;
use App\Services\Scraping\Adapters\GenericHtmlAdapter;
use App\Services\Scraping\Adapters\ScraperAdapter;
use App\Services\Scraping\ScraperService;
use Illuminate\Console\Command;

class ScrapeRunCommand extends Command
{
    protected $signature = 'scrape:run
        {--source= : Belirli scrape_source ID}
        {--uni= : Belirli university ID (tüm enabled source\'larını çalıştırır)}
        {--all : Tüm enabled source\'lar}
        {--dry-run : Sadece bul, DB\'ye yazma}';

    protected $description = 'Üni resmi sitelerinden program scrape eder. Sonuçlar staging tablosuna (scraped_programs) düşer.';

    public function handle(ScraperService $http): int
    {
        $sources = $this->resolveSources();
        if ($sources->isEmpty()) {
            $this->error('Hiç scrape source bulunamadı.');
            return self::FAILURE;
        }

        $dryRun = $this->option('dry-run');
        $this->info(($dryRun ? '🔍 DRY-RUN — ' : '▶ ') . $sources->count() . ' source çalıştırılıyor');

        $totalNew = 0;
        $totalUpdated = 0;
        foreach ($sources as $source) {
            [$new, $upd] = $this->runOne($source, $http, $dryRun);
            $totalNew += $new;
            $totalUpdated += $upd;
        }

        $this->newLine();
        $this->info("✅ Bitti — toplam yeni: $totalNew, güncellenen: $totalUpdated");
        return self::SUCCESS;
    }

    private function resolveSources()
    {
        if ($id = $this->option('source')) {
            return ScrapeSource::whereKey($id)->get();
        }
        if ($uniId = $this->option('uni')) {
            return ScrapeSource::where('university_id', $uniId)->where('is_enabled', true)->get();
        }
        if ($this->option('all')) {
            return ScrapeSource::where('is_enabled', true)->get();
        }
        $this->error('--source / --uni / --all parametrelerinden biri gerekli.');
        return collect();
    }

    private function runOne(ScrapeSource $source, ScraperService $http, bool $dryRun): array
    {
        $this->newLine();
        $this->line("📡 Source #{$source->id} → " . $source->university->name_de . ' (' . $source->adapter . ')');
        $this->line("   URL: {$source->list_url}");

        $run = ScrapeRunModel::create([
            'scrape_source_id' => $source->id,
            'started_at' => now(),
            'status' => 'running',
        ]);

        $started = microtime(true);
        $adapter = $this->buildAdapter($source, $http);
        $newCount = 0;
        $updatedCount = 0;

        try {
            $items = $adapter->scrape($source);

            $this->line("   ↳ Bulunan: " . count($items));

            if (!$dryRun) {
                foreach ($items as $item) {
                    $hash = $this->contentHash($item);
                    $key = $item['external_key'] ?: substr(($item['name_de'] ?? 'noname') . '-' . ($item['source_url'] ?? ''), 0, 191);

                    $existing = ScrapedProgram::where('scrape_source_id', $source->id)
                        ->where('external_key', $key)
                        ->first();

                    $raw = $item['raw'] ?? [];
                    $payload = array_merge($item, [
                        'scrape_source_id' => $source->id,
                        'university_id' => $source->university_id,
                        'content_hash' => $hash,
                        'last_seen_at' => now(),
                        'external_key' => $key,
                        'raw' => $raw,
                        'study_form' => $raw['study_form'] ?? null,
                        'deadline_raw' => $raw['deadline_raw'] ?? null,
                        'tuition_raw' => $raw['tuition_raw'] ?? null,
                        'semester_fee_raw' => $raw['semester_fee_raw'] ?? null,
                        'ects_credits' => isset($raw['ects_credits']) ? (int) preg_replace('/\D/', '', (string) $raw['ects_credits']) : null,
                    ]);

                    if (!$existing) {
                        $payload['first_seen_at'] = now();
                        $payload['review_status'] = 'pending';
                        ScrapedProgram::create($payload);
                        $newCount++;
                    } elseif ($existing->content_hash !== $hash) {
                        $existing->update($payload + ['review_status' => 'pending']);
                        $updatedCount++;
                    } else {
                        $existing->touch();
                    }
                }
            }

            $source->update([
                'last_run_at' => now(),
                'last_found_count' => count($items),
                'last_status' => $dryRun ? 'dry_run_ok' : 'ok',
                'last_error' => null,
            ]);

            $run->update([
                'finished_at' => now(),
                'duration_ms' => (int) ((microtime(true) - $started) * 1000),
                'status' => $dryRun ? 'dry_run_ok' : 'ok',
                'http_requests' => $adapter instanceof GenericHtmlAdapter ? $adapter->httpRequests() : 0,
                'items_found' => count($items),
                'items_new' => $newCount,
                'items_updated' => $updatedCount,
            ]);

            $this->info("   ✓ yeni=$newCount, güncellenen=$updatedCount");
        } catch (\Throwable $e) {
            $source->update(['last_status' => 'fail', 'last_error' => $e->getMessage()]);
            $run->update([
                'finished_at' => now(),
                'duration_ms' => (int) ((microtime(true) - $started) * 1000),
                'status' => 'fail',
                'error' => $e->getMessage(),
            ]);
            $this->error('   ✗ ' . $e->getMessage());
        }

        return [$newCount, $updatedCount];
    }

    private function buildAdapter(ScrapeSource $source, ScraperService $http): ScraperAdapter
    {
        return match ($source->adapter) {
            'generic_html' => new GenericHtmlAdapter($http),
            default => throw new \RuntimeException("Adapter '{$source->adapter}' henüz desteklenmiyor (bu seansta sadece generic_html)"),
        };
    }

    private function contentHash(array $item): string
    {
        $hashable = collect($item)->only(['name_de', 'name_en', 'degree', 'language', 'duration_semesters', 'source_url'])->toArray();
        return hash('sha256', json_encode($hashable, JSON_UNESCAPED_UNICODE));
    }
}
