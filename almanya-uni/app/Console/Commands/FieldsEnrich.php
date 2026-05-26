<?php

namespace App\Console\Commands;

use App\Models\FieldOfStudy;
use App\Services\Enrichment\FieldEnrichmentService;
use Illuminate\Console\Command;

class FieldsEnrich extends Command
{
    protected $signature = 'fields:enrich
        {--force : Yakın zamanda enrich edilenleri de yeniden işle}
        {--slug= : Tek bir alan (slug)}
        {--sleep=2 : Gemini API rate-limit için bekleme}';

    protected $description = 'Eğitim alanları için Wikipedia + AI + community content_blocks üret';

    public function handle(FieldEnrichmentService $svc): int
    {
        $query = FieldOfStudy::active();
        if ($slug = $this->option('slug')) {
            $query->where('slug', $slug);
        }
        $fields = $query->orderBy('sort_order')->get();
        $total = $fields->count();
        if ($total === 0) {
            $this->warn('Alan bulunamadı.');
            return self::SUCCESS;
        }

        $this->info("📚 {$total} alan enrich edilecek");
        $this->newLine();

        $success = 0; $skipped = 0; $failed = 0;
        $start = now();

        foreach ($fields as $i => $field) {
            $this->line(sprintf('[%d/%d] %s …', $i + 1, $total, $field->name_tr));
            try {
                $r = $svc->enrich($field, (bool) $this->option('force'));
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
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("✅ {$success} · ⏭️ {$skipped} · ❌ {$failed} · ⏱️ {$duration}s");
        return self::SUCCESS;
    }
}
