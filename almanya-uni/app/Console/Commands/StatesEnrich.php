<?php

namespace App\Console\Commands;

use App\Models\State;
use App\Services\Enrichment\StateEnrichmentService;
use Illuminate\Console\Command;

class StatesEnrich extends Command
{
    protected $signature = 'states:enrich
        {--force : Yakın zamanda enrich edilenleri de yeniden işle}
        {--slug= : Tek bir eyalet (slug)}
        {--sleep=2 : Gemini API rate-limit için bekleme}';

    protected $description = '16 Alman eyaleti için Wikipedia + AI + community content_blocks üret';

    public function handle(StateEnrichmentService $svc): int
    {
        $query = State::query();
        if ($slug = $this->option('slug')) {
            $query->where('slug', $slug);
        }
        $states = $query->orderBy('name_de')->get();
        $total = $states->count();

        if ($total === 0) {
            $this->warn('Eyalet bulunamadı.');
            return self::SUCCESS;
        }

        $this->info("🗺️ {$total} eyalet enrich edilecek");
        $this->newLine();

        $success = 0; $failed = 0; $skipped = 0;
        $start = now();

        foreach ($states as $i => $state) {
            $this->line(sprintf('[%d/%d] %s …', $i + 1, $total, $state->name_de));
            try {
                $r = $svc->enrich($state, (bool) $this->option('force'));
                if ($r['success']) {
                    $this->info("  ✅ {$r['blocks_count']} blok · " . ($r['tokens']['output'] ?? 0) . ' token');
                    $success++;
                } else {
                    $this->warn('  ⏭️ ' . ($r['error'] ?? '?'));
                    if (str_contains((string) ($r['error'] ?? ''), 'Yakın zamanda')) {
                        $skipped++;
                    } else {
                        $failed++;
                    }
                }
            } catch (\Throwable $e) {
                $this->error('  ❌ ' . substr($e->getMessage(), 0, 200));
                $failed++;
            }
            if ($i < $total - 1) sleep((int) $this->option('sleep'));
        }

        $duration = $start->diffInSeconds(now());
        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info("✅ {$success} · ⏭️ {$skipped} · ❌ {$failed} · ⏱️ {$duration}s");
        return self::SUCCESS;
    }
}
