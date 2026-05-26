<?php

namespace App\Services\Scraping\Adapters;

use App\Models\ScrapeSource;
use App\Services\Scraping\ScraperService;
use Symfony\Component\DomCrawler\Crawler;

/**
 * CSS selector tabanlı HTML scraper.
 *
 * Config örneği (ScrapeSource.config):
 * {
 *   "item_selector": "li.program-item",
 *   "name_selector": "h3.title",
 *   "url_selector": "a@href",
 *   "degree_selector": ".degree-badge",
 *   "language_selector": ".language",
 *   "pagination_next_selector": "a.next@href",
 *   "max_pages": 20,
 *   "degree_map": {"B.Sc.": "bachelor", "M.Sc.": "master"}
 * }
 */
class GenericHtmlAdapter implements ScraperAdapter
{
    public function __construct(private ScraperService $http) {}

    private int $httpRequests = 0;

    public function scrape(ScrapeSource $source): array
    {
        $config = $source->config ?? [];
        $itemSel = $config['item_selector'] ?? null;
        if (!$itemSel) {
            throw new \InvalidArgumentException('config.item_selector zorunlu');
        }

        $results = [];
        $url = $source->list_url;
        $maxPages = (int) ($config['max_pages'] ?? 1);
        $page = 0;
        $visited = [];

        while ($url && $page < $maxPages && !isset($visited[$url])) {
            $visited[$url] = true;
            $page++;

            $resp = $this->http->fetch(
                $url,
                $page === 1 ? $source->etag : null,
                $page === 1 ? $source->last_modified_header : null,
                $source->throttle_ms,
                $source->respect_robots,
            );
            $this->httpRequests++;

            if (!empty($resp['blocked'])) {
                throw new \RuntimeException('robots.txt tarafından engellendi: ' . $url);
            }
            if ($resp['status'] === 304) {
                break;
            }
            if (!$resp['body']) {
                throw new \RuntimeException("HTTP {$resp['status']} — $url");
            }

            if ($page === 1) {
                $source->etag = $resp['etag'];
                $source->last_modified_header = $resp['last_modified'];
            }

            $crawler = new Crawler($resp['body'], $url);
            $items = $crawler->filter($itemSel);

            $minSegments = (int) ($config['min_url_segments'] ?? 0);
            $items->each(function (Crawler $node) use (&$results, $config, $url, $minSegments) {
                $item = $this->parseItem($node, $config, $url);
                if ($minSegments && $item['source_url']) {
                    $segs = count(array_filter(explode('/', parse_url($item['source_url'], PHP_URL_PATH) ?? '')));
                    if ($segs < $minSegments) return;
                }
                if (empty($item['name_de']) && empty($item['source_url'])) return;
                $results[] = $item;
            });

            $url = $this->resolveNext($crawler, $config, $url);
        }

        if (!empty($config['detail']['enabled']) && !empty($results)) {
            $results = $this->enrichWithDetails($results, $source, $config['detail']);
        }

        return $results;
    }

    /**
     * Her item'ın source_url'sini ayrıca fetch edip detay sayfasından zenginleştirir.
     * config.detail örneği:
     * {
     *   "enabled": true,
     *   "title_selector": "h1",
     *   "description_selector": "main p:first-of-type",
     *   "metadata_table_selector": "table",
     *   "metadata_label_map": {
     *     "Abschluss": "degree_specification",
     *     "Regelstudienzeit": "duration_raw",
     *     "Studienform": "study_form",
     *     "Bewerbung": "deadline_raw",
     *     "Studiengebühren": "tuition_raw",
     *     "Semesterbeitrag": "semester_fee_raw"
     *   }
     * }
     */
    private function enrichWithDetails(array $items, ScrapeSource $source, array $cfg): array
    {
        $titleSel = $cfg['title_selector'] ?? 'h1';
        $descSel = $cfg['description_selector'] ?? 'main p, article p';
        $tableSel = $cfg['metadata_table_selector'] ?? 'table';
        $labelMap = $cfg['metadata_label_map'] ?? [];

        foreach ($items as &$item) {
            if (empty($item['source_url'])) continue;
            try {
                $resp = $this->http->fetch(
                    $item['source_url'],
                    null, null,
                    $source->throttle_ms,
                    $source->respect_robots,
                );
                $this->httpRequests++;
                if (!$resp['body']) continue;

                $crawler = new Crawler($resp['body'], $item['source_url']);

                $title = $this->extract($crawler, $titleSel);
                if ($title) {
                    $item['name_de'] = $title;
                }

                $description = $this->extract($crawler, $descSel);
                if ($description) {
                    $item['description_de'] = substr($description, 0, 5000);
                }

                $metaPairs = $this->parseMetaTable($crawler, $tableSel);
                $item['raw'] = array_merge($item['raw'] ?? [], ['meta_pairs' => $metaPairs]);

                foreach ($labelMap as $label => $fieldKey) {
                    $value = $metaPairs[$label] ?? null;
                    if (!$value) continue;

                    match ($fieldKey) {
                        'degree_specification' => $item['degree'] = $item['degree'] ?: $this->mapDegree($value, []),
                        'duration_raw' => $item['duration_semesters'] = $this->parseDuration($value),
                        'study_form' => $item['raw']['study_form'] = $value,
                        'deadline_raw' => $item['raw']['deadline_raw'] = $value,
                        'tuition_raw' => $item['raw']['tuition_raw'] = $value,
                        'semester_fee_raw' => $item['raw']['semester_fee_raw'] = $value,
                        default => $item['raw'][$fieldKey] = $value,
                    };
                }
            } catch (\Throwable $e) {
                $item['raw']['detail_fetch_error'] = $e->getMessage();
            }
        }
        unset($item);

        return $items;
    }

    private function parseMetaTable(Crawler $crawler, string $selector): array
    {
        $out = [];
        try {
            $crawler->filter($selector)->each(function (Crawler $t) use (&$out) {
                $t->filter('tr')->each(function (Crawler $tr) use (&$out) {
                    $cells = $tr->filter('th, td');
                    if ($cells->count() !== 2) return;
                    $label = trim(preg_replace('/\s+/u', ' ', $cells->eq(0)->text()));
                    $value = trim(preg_replace('/\s+/u', ' ', $cells->eq(1)->text()));
                    if ($label && $value) $out[$label] = $value;
                });
            });
        } catch (\Throwable $e) {
            // ignore
        }
        return $out;
    }

    public function httpRequests(): int
    {
        return $this->httpRequests;
    }

    private function parseItem(Crawler $node, array $config, string $baseUrl): array
    {
        $name = $this->extract($node, $config['name_selector'] ?? null);
        $sourceUrl = $this->extract($node, $config['url_selector'] ?? null);
        if ($sourceUrl && !preg_match('#^https?://#', $sourceUrl)) {
            $sourceUrl = $this->absoluteUrl($baseUrl, $sourceUrl);
        }

        $degreeRaw = $this->extract($node, $config['degree_selector'] ?? null);
        $degree = $this->mapDegree($degreeRaw, $config['degree_map'] ?? []);

        $language = $this->extract($node, $config['language_selector'] ?? null);
        $duration = $this->extract($node, $config['duration_selector'] ?? null);
        $description = $this->extract($node, $config['description_selector'] ?? null);

        $externalKey = $sourceUrl ?: ($name ? md5($name) : null);

        return [
            'external_key' => $externalKey ? substr($externalKey, 0, 191) : null,
            'source_url' => $sourceUrl,
            'name_de' => $name,
            'name_en' => null,
            'degree' => $degree,
            'language' => $this->normalizeLanguage($language),
            'duration_semesters' => $this->parseDuration($duration),
            'description_de' => $description,
            'raw' => [
                'degree_raw' => $degreeRaw,
                'language_raw' => $language,
                'duration_raw' => $duration,
                'html_snippet' => substr($node->html(), 0, 800),
            ],
        ];
    }

    private function extract(Crawler $node, ?string $selector): ?string
    {
        if ($selector === null) return null;

        $attr = null;
        if (str_contains($selector, '@')) {
            [$selector, $attr] = explode('@', $selector, 2);
        }

        try {
            $found = trim((string) $selector) === '' ? $node : $node->filter($selector);
            if ($found->count() === 0) return null;
            $val = $attr ? $found->first()->attr($attr) : $found->first()->text();
            $val = preg_replace('/\s+/u', ' ', (string) $val);
            return trim($val) ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function mapDegree(?string $raw, array $map): ?string
    {
        if (!$raw) return null;
        foreach ($map as $needle => $value) {
            if (stripos($raw, $needle) !== false) {
                return $value;
            }
        }
        $lower = mb_strtolower($raw);
        return match (true) {
            str_contains($lower, 'bachelor') || str_contains($lower, 'b.sc') || str_contains($lower, 'b.a.') => 'bachelor',
            str_contains($lower, 'master') || str_contains($lower, 'm.sc') || str_contains($lower, 'm.a.') => 'master',
            str_contains($lower, 'phd') || str_contains($lower, 'doktor') || str_contains($lower, 'promotion') => 'phd',
            default => null,
        };
    }

    private function normalizeLanguage(?string $raw): ?string
    {
        if (!$raw) return null;
        $l = mb_strtolower($raw);
        return match (true) {
            str_contains($l, 'englisch') || str_contains($l, 'english') => 'en',
            str_contains($l, 'deutsch') || str_contains($l, 'german') => 'de',
            str_contains($l, 'bilingu') || str_contains($l, 'both') => 'both',
            default => null,
        };
    }

    private function parseDuration(?string $raw): ?int
    {
        if (!$raw) return null;
        if (preg_match('/(\d+)\s*(semester|sem\.?)/iu', $raw, $m)) {
            return (int) $m[1];
        }
        if (preg_match('/(\d+)/', $raw, $m)) {
            return (int) $m[1];
        }
        return null;
    }

    private function resolveNext(Crawler $crawler, array $config, string $current): ?string
    {
        $sel = $config['pagination_next_selector'] ?? null;
        if (!$sel) return null;

        $attr = 'href';
        if (str_contains($sel, '@')) {
            [$sel, $attr] = explode('@', $sel, 2);
        }
        try {
            $next = $crawler->filter($sel);
            if ($next->count() === 0) return null;
            $href = $next->first()->attr($attr);
            return $href ? $this->absoluteUrl($current, $href) : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function absoluteUrl(string $base, string $rel): string
    {
        if (preg_match('#^https?://#', $rel)) return $rel;
        $b = parse_url($base);
        $scheme = $b['scheme'] ?? 'https';
        $host = $b['host'] ?? '';
        $port = isset($b['port']) ? ':' . $b['port'] : '';
        if (str_starts_with($rel, '//')) {
            return $scheme . ':' . $rel;
        }
        if (str_starts_with($rel, '/')) {
            return "$scheme://$host$port$rel";
        }
        $basePath = isset($b['path']) ? rtrim(dirname($b['path']), '/') : '';
        return "$scheme://$host$port$basePath/$rel";
    }
}
