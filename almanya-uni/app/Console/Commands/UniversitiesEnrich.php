<?php

namespace App\Console\Commands;

use App\Models\University;
use App\Services\Enrichment\UniversityEnrichmentService;
use Illuminate\Console\Command;

class UniversitiesEnrich extends Command
{
    protected $signature = 'universities:enrich
        {--limit=0 : Sadece N üniversite (0 = hepsi)}
        {--force : Yakın zamanda enrich edilenleri de yeniden işle}
        {--only-without : Sadece content_blocks NULL olanlar}
        {--top-by-students=0 : Sadece öğrenci sayısına göre en büyük N üni}
        {--slug= : Sadece tek bir üni (slug ile)}
        {--type= : Sadece belirli tipte (public, private, applied_sciences, art, religion)}
        {--sleep=2 : Her enrich arasında bekleme (saniye)}';

    protected $description = 'Birden fazla üniversite için Wikipedia + AI ile zengin content_blocks üret';

    public function handle(UniversityEnrichmentService $svc): int
    {
        $query = University::query()->where('is_active', 1);

        if ($slug = $this->option('slug')) {
            $query->where('slug', $slug);
        } else {
            if ($this->option('only-without')) {
                $query->whereNull('content_blocks');
            }
            if ($type = $this->option('type')) {
                $query->where('type', $type);
            }
            if ((int) $this->option('top-by-students') > 0) {
                $query->whereNotNull('student_count')
                    ->orderByDesc('student_count')
                    ->limit((int) $this->option('top-by-students'));
            } else {
                $query->orderByDesc('student_count');
            }
        }

        if ($this->option('limit') > 0 && (int) $this->option('top-by-students') === 0) {
            $query->limit((int) $this->option('limit'));
        }

        $unis = $query->get();
        $total = $unis->count();

        if ($total === 0) {
            $this->warn('Kriterlere uyan üniversite bulunamadı.');
            return self::SUCCESS;
        }

        $this->info("🎓 {$total} üniversite enrich edilecek (sleep: {$this->option('sleep')}s)");
        $this->newLine();

        $success = 0;
        $skipped = 0;
        $failed = 0;
        $start = now();

        foreach ($unis as $i => $uni) {
            $label = sprintf('[%d/%d] %s', $i + 1, $total, $uni->name_de);
            $this->line($label . ' …');

            try {
                $result = $svc->enrich($uni, (bool) $this->option('force'));
                if ($result['success']) {
                    $this->info("  ✅ {$result['blocks_count']} blok · " . ($result['tokens']['output'] ?? 0) . ' token');
                    $success++;
                } else {
                    $this->warn('  ⏭️  ' . ($result['error'] ?? 'Bilinmeyen hata'));
                    if (str_contains((string) ($result['error'] ?? ''), 'Yakın zamanda')) {
                        $skipped++;
                    } else {
                        $failed++;
                    }
                }
            } catch (\Throwable $e) {
                $this->error('  ❌ Exception: ' . substr($e->getMessage(), 0, 200));
                $failed++;
            }

            if ($i < $total - 1) {
                sleep((int) $this->option('sleep'));
            }
        }

        $duration = $start->diffInSeconds(now());
        $this->newLine();
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("✅ Başarılı: {$success}");
        $this->line("⏭️  Atlandı (yeni): {$skipped}");
        $this->line("❌ Başarısız: {$failed}");
        $this->info("⏱️  Süre: {$duration}s");
        $this->newLine();

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
