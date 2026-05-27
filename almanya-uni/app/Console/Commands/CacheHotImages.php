<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\University;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CacheHotImages extends Command
{
    /**
     * Downloads Wikimedia images (cities + featured universities), converts to WebP,
     * stores in public/img/cache/. Accessors then serve local URLs → Lighthouse LCP boost
     * + Wikimedia rate-limit (429) immunity.
     */
    protected $signature = 'images:cache-hot
                            {--width=500 : Target width for city/uni photos}
                            {--logo-width=120 : Target width for uni logos}
                            {--quality=80 : WebP quality 1-100}
                            {--force : Re-download even if local cache exists}
                            {--limit= : Limit count for testing}';

    protected $description = 'Cache Wikimedia hot images locally as WebP (cities + featured unis)';

    public function handle(): int
    {
        if (! extension_loaded('gd') || ! function_exists('imagewebp')) {
            $this->error('GD extension with WebP support is required.');
            return self::FAILURE;
        }

        $cacheRoot = public_path('img/cache');
        foreach (['cities', 'unis', 'uni-logos'] as $sub) {
            $dir = $cacheRoot . '/' . $sub;
            if (! is_dir($dir)) mkdir($dir, 0775, true);
        }

        $width = (int) $this->option('width');
        $logoWidth = (int) $this->option('logo-width');
        $quality = max(1, min(100, (int) $this->option('quality')));
        $force = (bool) $this->option('force');
        $limit = $this->option('limit') !== null ? (int) $this->option('limit') : null;

        $stats = ['cities' => 0, 'unis' => 0, 'logos' => 0, 'skipped' => 0, 'failed' => 0];

        // Cities — all active cities with image_url
        $cityQuery = City::where('is_active', 1)->whereNotNull('image_url')->orderBy('id');
        if ($limit) $cityQuery->limit($limit);
        $cities = $cityQuery->get(['id', 'slug', 'image_url']);

        $this->info("Caching {$cities->count()} city images...");
        $bar = $this->output->createProgressBar($cities->count());
        foreach ($cities as $c) {
            $localPath = "{$cacheRoot}/cities/{$c->slug}.webp";
            if (! $force && file_exists($localPath)) {
                $stats['skipped']++;
            } else {
                $remoteUrl = wikimedia_thumb($c->getRawOriginal('image_url'), $width);
                $remoteUrl = preg_replace('#^http://#i', 'https://', $remoteUrl);
                if ($this->downloadAndConvert($remoteUrl, $localPath, $quality)) {
                    $stats['cities']++;
                } else {
                    $stats['failed']++;
                }
            }
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();

        // Universities — all active unis (image + logo)
        $uniQuery = University::where('is_active', 1)->orderBy('id');
        if ($limit) $uniQuery->limit($limit);
        $unis = $uniQuery->get(['id', 'slug', 'image_url', 'logo_url']);

        $this->info("Caching {$unis->count()} uni images + logos...");
        $bar = $this->output->createProgressBar($unis->count());
        foreach ($unis as $u) {
            // Building photo
            if ($u->getRawOriginal('image_url')) {
                $localPath = "{$cacheRoot}/unis/{$u->slug}.webp";
                if (! $force && file_exists($localPath)) {
                    $stats['skipped']++;
                } else {
                    $remoteUrl = wikimedia_thumb($u->getRawOriginal('image_url'), $width);
                    $remoteUrl = preg_replace('#^http://#i', 'https://', $remoteUrl);
                    if ($this->downloadAndConvert($remoteUrl, $localPath, $quality)) {
                        $stats['unis']++;
                    } else {
                        $stats['failed']++;
                    }
                }
            }

            // Logo
            if ($u->getRawOriginal('logo_url')) {
                $localPath = "{$cacheRoot}/uni-logos/{$u->slug}.webp";
                if (! $force && file_exists($localPath)) {
                    $stats['skipped']++;
                } else {
                    $remoteUrl = wikimedia_thumb($u->getRawOriginal('logo_url'), $logoWidth);
                    $remoteUrl = preg_replace('#^http://#i', 'https://', $remoteUrl);
                    if ($this->downloadAndConvert($remoteUrl, $localPath, $quality)) {
                        $stats['logos']++;
                    } else {
                        $stats['failed']++;
                    }
                }
            }

            $bar->advance();
        }
        $bar->finish();
        $this->newLine();

        $this->info(sprintf(
            'Done: %d cities, %d uni photos, %d logos, %d skipped (already cached), %d failed.',
            $stats['cities'],
            $stats['unis'],
            $stats['logos'],
            $stats['skipped'],
            $stats['failed']
        ));

        return self::SUCCESS;
    }

    private function downloadAndConvert(string $url, string $destPath, int $quality): bool
    {
        try {
            $response = Http::timeout(20)
                ->withHeaders(['User-Agent' => 'AlmanyaUni/1.0 (image cache; tech@applytogerman.com)'])
                ->retry(2, 500)
                ->get($url);

            if (! $response->successful()) {
                return false;
            }

            $bytes = $response->body();
            if (strlen($bytes) < 100) {
                return false;
            }

            $img = @imagecreatefromstring($bytes);
            if (! $img) {
                return false;
            }

            // Preserve transparency for PNG-origin
            imagepalettetotruecolor($img);
            imagealphablending($img, true);
            imagesavealpha($img, true);

            $ok = imagewebp($img, $destPath, $quality);
            imagedestroy($img);

            return $ok;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
