<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Wipe accumulated visitor + engagement statistics so the live site starts
 * counters from zero. Does NOT touch:
 *   - content (posts, universities, programs, blog text)
 *   - user accounts or favorites
 *   - reviews (those are real UGC)
 *   - community_mention_score (computed from forum/telegram, not visit-based)
 */
class StatsReset extends Command
{
    protected $signature = 'stats:reset
                            {--dry-run : Show what would be deleted without doing it}
                            {--keep-engagements : Keep post_engagements (scroll/time on blog), only wipe view counters}';

    protected $description = 'Reset visitor traffic + content view stats to zero (page_views, view_count, helpful_count)';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $keepEngagements = (bool) $this->option('keep-engagements');

        $this->info($dryRun ? 'DRY RUN — no changes will be made' : 'Resetting stats…');
        $this->newLine();

        $report = [];

        // 1) page_views — self-hosted analytics middleware data
        if (Schema::hasTable('page_views')) {
            $count = DB::table('page_views')->count();
            $report['page_views'] = $count;
            if (! $dryRun && $count > 0) {
                DB::table('page_views')->truncate();
            }
        }

        // 2) post_engagements — blog scroll/time tracking
        if (! $keepEngagements && Schema::hasTable('post_engagements')) {
            $count = DB::table('post_engagements')->count();
            $report['post_engagements'] = $count;
            if (! $dryRun && $count > 0) {
                DB::table('post_engagements')->truncate();
            }
        }

        // 3) posts.view_count / helpful_count / unhelpful_count
        if (Schema::hasTable('posts')) {
            $row = DB::table('posts')->selectRaw('SUM(view_count) as v, SUM(helpful_count) as h, SUM(unhelpful_count) as u')->first();
            $report['posts.view_count'] = (int) ($row->v ?? 0);
            $report['posts.helpful_count'] = (int) ($row->h ?? 0);
            $report['posts.unhelpful_count'] = (int) ($row->u ?? 0);
            if (! $dryRun) {
                DB::table('posts')->update([
                    'view_count' => 0,
                    'helpful_count' => 0,
                    'unhelpful_count' => 0,
                ]);
            }
        }

        // 4) faqs.view_count
        if (Schema::hasTable('faqs')) {
            $sum = (int) DB::table('faqs')->sum('view_count');
            $report['faqs.view_count'] = $sum;
            if (! $dryRun && $sum > 0) {
                DB::table('faqs')->update(['view_count' => 0]);
            }
        }

        // Output table
        $rows = [];
        foreach ($report as $k => $v) {
            $rows[] = [$k, number_format($v, 0, ',', '.')];
        }
        $this->table(['Tablo / Sahası', $dryRun ? 'Şu an' : 'Sıfırlanan'], $rows);

        if ($dryRun) {
            $this->warn('DRY RUN: hiçbir değişiklik yapılmadı. Gerçek çalıştırmak için --dry-run parametresini kaldırın.');
        } else {
            $this->info('✓ Reset tamamlandı. Bundan sonraki ziyaretler gerçek sayılır.');
        }

        return self::SUCCESS;
    }
}
