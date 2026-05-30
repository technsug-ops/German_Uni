<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Anti-scraping throttle for PUBLIC WEB pages (not the /api/* layer — that has
 * ApiThrottleAndLog). Goal: a normal human reads a handful of pages per minute;
 * a scraper hammers hundreds. We let humans through untouched and slow down only
 * sustained high-rate IPs.
 *
 * Design choices (deliberate):
 *  - Counts only GET HTML page loads (not assets, not POST forms).
 *  - Verified good bots (Googlebot, Bingbot, AI crawlers we allow in robots.txt)
 *    are EXEMPT — we want SEO/AIO indexing, never block it.
 *  - Soft ceiling: a generous per-minute budget. Crossing it returns 429 with a
 *    Retry-After, not a ban. Self-corrects when the burst stops.
 *  - Cache-backed sliding window, same pattern as ApiThrottleAndLog, so it works
 *    on the DB cache driver in production (no Redis dependency).
 *  - Fails OPEN: any cache error lets the request through (never take the site
 *    down to stop a scraper).
 */
class ThrottleScrapers
{
    /** Max page loads per IP per minute before throttling kicks in. */
    private const MAX_PER_MINUTE = 90;

    /** Once over the limit, how long (sec) the 429 stays before the window rolls. */
    private const WINDOW = 60;

    /**
     * User-agents we NEVER throttle — legitimate crawlers we actively want.
     * Matches robots.txt policy (Google, Bing, plus allowed AI bots).
     */
    private const GOOD_BOTS = [
        'googlebot', 'bingbot', 'slurp', 'duckduckbot', 'baiduspider',
        'yandexbot', 'applebot', 'gptbot', 'claudebot', 'claude-web',
        'google-extended', 'oai-searchbot', 'chatgpt-user', 'perplexitybot',
        'facebookexternalhit', 'twitterbot', 'linkedinbot', 'whatsapp',
        'telegrambot', 'pinterest', 'google-inspectiontool',
    ];

    /** Paths that should never be throttled (health, SEO, static-ish). */
    private const EXEMPT_PREFIXES = [
        'up', 'sitemap', 'robots.txt', 'llms.txt', 'rss.xml', 'feed',
        'favicon.ico', 'build', 'storage', 'img', 'css', 'js', 'fonts',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Only police GET page loads — POSTs already have per-route throttles.
        if ($request->method() !== 'GET') {
            return $next($request);
        }

        $path = ltrim($request->path(), '/');
        foreach (self::EXEMPT_PREFIXES as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix . '/') || str_starts_with($path, $prefix . '.')) {
                return $next($request);
            }
        }

        // Never throttle verified good bots — SEO/AIO indexing must flow freely.
        $ua = mb_strtolower((string) $request->userAgent());
        foreach (self::GOOD_BOTS as $bot) {
            if (str_contains($ua, $bot)) {
                return $next($request);
            }
        }

        try {
            $bucket = 'scrape_rate:' . md5(($request->ip() ?? 'unknown')) . ':' . floor(time() / self::WINDOW);
            Cache::add($bucket, 0, self::WINDOW + 5);
            $hits = (int) Cache::increment($bucket);

            if ($hits > self::MAX_PER_MINUTE) {
                $resetIn = self::WINDOW - (time() % self::WINDOW);

                return response(
                    "Too many requests. Please slow down.\n",
                    429,
                    [
                        'Content-Type' => 'text/plain; charset=utf-8',
                        'Retry-After'  => $resetIn,
                        'X-Robots-Tag' => 'noindex',
                    ]
                );
            }
        } catch (\Throwable $e) {
            // Fail open — a cache hiccup must never block real visitors.
        }

        return $next($request);
    }
}
