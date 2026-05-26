<?php

namespace App\Services\Scraping;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ScraperService
{
    public const USER_AGENT = 'AlmanyaUniBot/1.0 (+https://almanyauni.de/bot)';

    /** Domain başına son request timestamp'i (in-memory throttle) */
    private array $lastRequestAt = [];

    /**
     * GET isteği. Robots.txt + throttle + conditional GET (ETag/Last-Modified).
     *
     * @return array{status:int, body:?string, etag:?string, last_modified:?string, modified:bool}
     */
    public function fetch(string $url, ?string $etag = null, ?string $lastModified = null, int $throttleMs = 3000, bool $respectRobots = true): array
    {
        if ($respectRobots && !$this->allowedByRobots($url)) {
            return ['status' => 0, 'body' => null, 'etag' => null, 'last_modified' => null, 'modified' => false, 'blocked' => true];
        }

        $this->throttle($url, $throttleMs);

        $headers = ['User-Agent' => self::USER_AGENT, 'Accept' => 'text/html,application/xhtml+xml'];
        if ($etag) {
            $headers['If-None-Match'] = $etag;
        }
        if ($lastModified) {
            $headers['If-Modified-Since'] = $lastModified;
        }

        $response = Http::withHeaders($headers)
            ->timeout(20)
            ->withOptions(['allow_redirects' => true, 'verify' => true])
            ->get($url);

        if ($response->status() === 304) {
            return [
                'status' => 304,
                'body' => null,
                'etag' => $etag,
                'last_modified' => $lastModified,
                'modified' => false,
            ];
        }

        return [
            'status' => $response->status(),
            'body' => $response->successful() ? $response->body() : null,
            'etag' => $response->header('ETag') ?: null,
            'last_modified' => $response->header('Last-Modified') ?: null,
            'modified' => true,
        ];
    }

    /**
     * robots.txt User-Agent: * Disallow: <path> kurallarını yorumla.
     * Sadece basit Disallow desteği — Allow / Sitemap parse edilmiyor.
     */
    public function allowedByRobots(string $url): bool
    {
        $parts = parse_url($url);
        if (!$parts || empty($parts['host'])) {
            return false;
        }
        $robotsUrl = ($parts['scheme'] ?? 'https') . '://' . $parts['host'] . '/robots.txt';
        $path = $parts['path'] ?? '/';

        $disallows = Cache::remember('robots:' . $parts['host'], 86400, function () use ($robotsUrl) {
            try {
                $r = Http::withHeaders(['User-Agent' => self::USER_AGENT])->timeout(8)->get($robotsUrl);
                if (!$r->successful()) {
                    return [];
                }
                return $this->parseRobots($r->body());
            } catch (\Throwable $e) {
                Log::warning("robots.txt fetch fail: $robotsUrl — " . $e->getMessage());
                return [];
            }
        });

        foreach ($disallows as $pattern) {
            if ($pattern === '' || $pattern === '/') {
                if ($pattern === '/') return false;
                continue;
            }
            if (str_starts_with($path, $pattern)) {
                return false;
            }
        }
        return true;
    }

    private function parseRobots(string $body): array
    {
        $lines = preg_split('/\r?\n/', $body);
        $inOurGroup = false;
        $disallows = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) continue;
            if (preg_match('/^User-agent:\s*(.+)$/i', $line, $m)) {
                $agent = trim($m[1]);
                $inOurGroup = ($agent === '*' || stripos(self::USER_AGENT, $agent) !== false);
                continue;
            }
            if ($inOurGroup && preg_match('/^Disallow:\s*(.*)$/i', $line, $m)) {
                $disallows[] = trim($m[1]);
            }
        }
        return $disallows;
    }

    private function throttle(string $url, int $ms): void
    {
        $host = parse_url($url, PHP_URL_HOST) ?: 'default';
        $now = microtime(true) * 1000;
        $last = $this->lastRequestAt[$host] ?? 0;
        $wait = $ms - ($now - $last);
        if ($wait > 0) {
            usleep((int) ($wait * 1000));
        }
        $this->lastRequestAt[$host] = microtime(true) * 1000;
    }
}
