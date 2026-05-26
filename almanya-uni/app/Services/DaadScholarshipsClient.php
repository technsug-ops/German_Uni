<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Fetches the DAAD scholarship database static JS bundles and parses the TAFFY(...) payloads.
 * NOT to be confused with [[DaadApiClient]] (Solr API for International Programmes — degree-granting courses).
 * Source: https://www2.daad.de/bundles/daadstipendiendatenbanklsh/data/a/js/
 */
class DaadScholarshipsClient
{
    public const BASE = 'https://www2.daad.de/bundles/daadstipendiendatenbanklsh/data/a/js';

    public const FILES = [
        'scholarships'   => '/scholarships.js',
        'origins'        => '/origin.js',
        'statuses'       => '/status.js',
        'subjectGroups'  => '/subjectgroups.js',
        'intentions'     => '/intentions.js',
        'deadlines'      => '/deadlines.js',
    ];

    public const VAR_NAMES = [
        'scholarships'   => 'scholarships',
        'origins'        => 'origin',
        'statuses'       => 'persStatus',
        'subjectGroups'  => 'subjectGrps',
        'intentions'     => 'persIntentions',
        'deadlines'      => 'listDeadlines',
    ];

    /**
     * Fetch + parse all six files. Returns ['scholarships' => [...], 'origins' => [...], ...].
     *
     * @return array<string, array<int, array>>
     */
    public function fetchAll(int $sleepMs = 500): array
    {
        $out = [];
        foreach (self::FILES as $key => $path) {
            $out[$key] = $this->fetchOne($key, $path);
            if ($sleepMs > 0) usleep($sleepMs * 1000);
        }
        return $out;
    }

    private function fetchOne(string $key, string $path): array
    {
        $url = self::BASE . $path;

        $resp = Http::withHeaders(['User-Agent' => 'AlmanyaUniBot/1.0 (+https://almanyauni.de/bot)'])
            ->timeout(30)
            ->retry(2, 1000)
            ->get($url);

        if (!$resp->successful()) {
            throw new \RuntimeException("DAAD scholarships HTTP {$resp->status()} @ {$url}");
        }

        $body = $resp->body();
        $varName = self::VAR_NAMES[$key];
        return $this->parseTaffy($body, $varName);
    }

    /**
     * Parse `var X = TAFFY([...JSON...]);` or `var X = [...JSON...];`.
     * Some smaller files (status, intentions) may omit the TAFFY() wrapper.
     */
    public function parseTaffy(string $js, string $varName): array
    {
        // First try the TAFFY-wrapped form.
        $pattern = '/var\s+' . preg_quote($varName, '/') . '\s*=\s*TAFFY\(\s*(\[.*?\])\s*\)\s*;?/s';
        if (preg_match($pattern, $js, $m)) {
            $decoded = json_decode($m[1], true);
            if (is_array($decoded)) return $decoded;
        }

        // Fall back to bare-array assignment.
        $pattern = '/var\s+' . preg_quote($varName, '/') . '\s*=\s*(\[.*?\])\s*;?/s';
        if (preg_match($pattern, $js, $m)) {
            $decoded = json_decode($m[1], true);
            if (is_array($decoded)) return $decoded;
        }

        throw new \RuntimeException("DAAD: could not parse var '{$varName}' from JS payload");
    }
}
