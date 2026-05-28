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
                $remoteUrl = wikimedia_original($c->getRawOriginal('image_url'));
                $remoteUrl = preg_replace('#^http://#i', 'https://', $remoteUrl);
                if ($this->downloadAndConvert($remoteUrl, $localPath, $quality, $width)) {
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
                    $remoteUrl = wikimedia_original($u->getRawOriginal('image_url'));
                    $remoteUrl = preg_replace('#^http://#i', 'https://', $remoteUrl);
                    if ($this->downloadAndConvert($remoteUrl, $localPath, $quality, $width)) {
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
                    $remoteUrl = wikimedia_original($u->getRawOriginal('logo_url'));
                    $remoteUrl = preg_replace('#^http://#i', 'https://', $remoteUrl);
                    if ($this->downloadAndConvert($remoteUrl, $localPath, $quality, $logoWidth)) {
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

    /**
     * Download Wikipedia thumb (or original as fallback), decode via GD, resize down to
     * target width (preserving aspect ratio), encode as WebP. Skips upscaling.
     *
     * Strategy: most Wikipedia originals are 5-15 MB JPGs which exceed shared-host PHP
     * memory limits. Try a Wikipedia thumbnail at a standard cached width first — much
     * smaller payload, much smaller GD decode footprint. Fall back to original only if
     * all thumb sizes return 400.
     */
    private function downloadAndConvert(string $originalUrl, string $destPath, int $quality, int $targetWidth): bool
    {
        try {
            // Build candidate URL chain: thumbs at standard sizes first, then original
            $candidates = $this->candidateUrls($originalUrl, $targetWidth);
            $bytes = null;
            foreach ($candidates as $url) {
                $bytes = $this->fetchBytes($url);
                if ($bytes !== null) break;
            }
            if ($bytes === null || strlen($bytes) < 100) return false;

            // Defensive: skip giant payloads (>8 MB) — GD decode would blow PHP memory on shared host
            if (strlen($bytes) > 8 * 1024 * 1024) return false;

            $src = @imagecreatefromstring($bytes);
            if (! $src) return false;

            $srcW = imagesx($src);
            $srcH = imagesy($src);
            if ($srcW < 1 || $srcH < 1) {
                imagedestroy($src);
                return false;
            }

            $newW = min($targetWidth, $srcW);
            $newH = (int) round($srcH * ($newW / $srcW));

            if ($newW === $srcW && $newH === $srcH) {
                imagepalettetotruecolor($src);
                imagealphablending($src, true);
                imagesavealpha($src, true);
                $ok = imagewebp($src, $destPath, $quality);
                imagedestroy($src);
                return $ok;
            }

            $dst = imagecreatetruecolor($newW, $newH);
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
            imagefilledrectangle($dst, 0, 0, $newW, $newH, $transparent);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $srcW, $srcH);

            $ok = imagewebp($dst, $destPath, $quality);
            imagedestroy($src);
            imagedestroy($dst);
            return $ok;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Try several Wikipedia thumbnail widths before falling back to original.
     * Standard widths that Wikipedia caches eagerly tend to succeed; arbitrary widths
     * (and uncached files) return HTTP 400.
     */
    private function candidateUrls(string $originalUrl, int $targetWidth): array
    {
        $list = [];
        // For Wikipedia URLs, build thumb candidates at common cached sizes near target
        if (preg_match('#upload\.wikimedia\.org/wikipedia/commons/([0-9a-f]/[0-9a-f]{2})/([^/?]+)$#i', $originalUrl, $m)) {
            $hashPath = $m[1];
            $filename = $m[2];
            $base = 'https://upload.wikimedia.org/wikipedia/commons/thumb/' . $hashPath . '/' . $filename;
            $widthCandidates = $this->thumbWidthCandidates($targetWidth);
            foreach ($widthCandidates as $w) {
                $thumbName = $w . 'px-' . $filename;
                if (preg_match('/\.svg$/i', $filename)) $thumbName .= '.png';
                $list[] = $base . '/' . $thumbName;
            }
        }
        // Always include the original as last resort
        $list[] = $originalUrl;
        return $list;
    }

    /** Order of widths to try: closest to target first, then common cached sizes. */
    private function thumbWidthCandidates(int $target): array
    {
        $common = [120, 200, 250, 320, 500, 640, 800, 1024, 1280];
        // Sort ascending by distance from target, but never request smaller than 80% of target
        $minAcceptable = max(80, (int) round($target * 0.8));
        $filtered = array_filter($common, fn ($w) => $w >= $minAcceptable);
        usort($filtered, fn ($a, $b) => abs($a - $target) <=> abs($b - $target));
        return array_values($filtered);
    }

    /** Fetch URL bytes; null on non-2xx or transport failure. */
    private function fetchBytes(string $url): ?string
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders(['User-Agent' => 'AlmanyaUni/1.0 (image cache; tech@applytogerman.com)'])
                ->retry(1, 300)
                ->get($url);
            return $response->successful() ? $response->body() : null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
