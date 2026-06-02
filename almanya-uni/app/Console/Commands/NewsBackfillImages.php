<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Services\Content\NewsService;
use Illuminate\Console\Command;

/**
 * Yayınlanmış ama görseli OLMAYAN haberlere AI illüstrasyon üretir.
 * translation_group bazında TEK görsel üretir, gruptaki tüm dillere yazar
 * (tr/en/de aynı görseli paylaşır). Idempotent: zaten görseli olanı atlar.
 *
 *   php artisan news:backfill-images [--limit=N] [--dry-run]
 */
class NewsBackfillImages extends Command
{
    protected $signature = 'news:backfill-images {--limit=0 : Maks grup} {--dry-run} {--force : Görseli OLANLARI da yeniden üret (metinli/eski görselleri yenile)}';
    protected $description = 'Yayınlanmış haberlere AI illüstrasyon üretir (grup bazında). --force ile mevcutları da yeniler.';

    public function handle(): int
    {
        $svc = app(NewsService::class);
        $force = (bool) $this->option('force');

        $groups = Post::news()
            ->when(! $force, fn ($q) => $q->where(fn ($w) => $w->whereNull('featured_image')->orWhere('featured_image', '')))
            ->whereNotNull('translation_group_id')
            ->orderByDesc('published_at')
            ->get(['id', 'translation_group_id', 'title', 'category_id'])
            ->groupBy('translation_group_id');

        $limit = (int) $this->option('limit');
        if ($limit > 0) $groups = $groups->take($limit);

        if ($groups->isEmpty()) {
            $this->info('Görseli eksik haber yok.');
            return self::SUCCESS;
        }

        $this->info("🖼️ {$groups->count()} haber grubuna görsel üretilecek");
        $done = 0; $failed = 0;

        foreach ($groups as $group => $posts) {
            $primary = $posts->first();
            $this->line('  → ' . mb_substr($primary->title, 0, 60));

            if ($this->option('dry-run')) continue;

            $primary->loadMissing('category');
            $path = $svc->generatePostImage($primary);
            if (! $path) {
                $failed++;
                $this->warn('     görsel üretilemedi (log)');
                continue;
            }
            // Gruptaki TÜM diller aynı görseli alır
            Post::where('translation_group_id', $group)->update(['featured_image' => $path]);
            $done++;
            $this->info('     ✅ ' . $path);
        }

        $this->newLine();
        $this->info("Tamam: {$done} grup, başarısız: {$failed}");
        return self::SUCCESS;
    }
}
