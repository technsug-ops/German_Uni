<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Partner (kardeş kuruluş) REST API HTTP client.
 *
 * Endpoint pattern (manifest.json'a göre):
 *   GET {base}/universities?page={n}&limit={m}
 *   GET {base}/programs?page={n}&limit={m}&updated_since={iso8601}
 *   GET {base}/programs/{id}
 *   GET {base}/states
 *   GET {base}/study-fields
 *
 * Auth: HTTP header (default: X-API-Key)
 */
class PartnerApiClient
{
    private string $baseUrl;
    private ?string $apiKey;
    private string $authHeader;
    private int $timeout;
    private int $pageSize;

    public function __construct()
    {
        $this->baseUrl    = rtrim((string) config('services.partner.base_url'), '/');
        $this->apiKey     = config('services.partner.api_key');
        $this->authHeader = config('services.partner.auth_header', 'X-API-Key');
        $this->timeout    = (int) config('services.partner.timeout', 60);
        $this->pageSize   = (int) config('services.partner.page_size', 200);
    }

    public function isConfigured(): bool
    {
        return $this->baseUrl !== '' && ! empty($this->apiKey);
    }

    /**
     * Sayfalı endpoint'lerden tüm kayıtları çek. Generator döner, her yield bir sayfa.
     *
     * @param string $path Örn: '/programs', '/universities'
     * @param array $query Ek query parametreleri (örn: ['updated_since' => '2026-05-13T00:00:00Z'])
     * @return \Generator<int, array<int, array>>
     */
    public function paginate(string $path, array $query = []): \Generator
    {
        $this->ensureConfigured();

        $page = 1;
        while (true) {
            $resp = $this->request($path, array_merge($query, [
                'page'  => $page,
                'limit' => $this->pageSize,
            ]));

            $payload = $resp->json();
            $items   = $this->extractItems($payload);

            if (empty($items)) {
                break;
            }

            yield $items;

            // Meta'da hasMore / pagination kontrol et
            $hasMore = $this->hasMorePages($payload, count($items));
            if (! $hasMore) {
                break;
            }

            $page++;
        }
    }

    public function fetchUniversities(?string $updatedSince = null): \Generator
    {
        return $this->paginate('/universities', $updatedSince ? ['updated_since' => $updatedSince] : []);
    }

    public function fetchPrograms(?string $updatedSince = null): \Generator
    {
        return $this->paginate('/programs', $updatedSince ? ['updated_since' => $updatedSince] : []);
    }

    public function fetchProgram(string $id): ?array
    {
        $this->ensureConfigured();
        $resp = $this->request("/programs/{$id}");
        return $resp->json('data') ?? $resp->json();
    }

    public function fetchStates(): array
    {
        $this->ensureConfigured();
        $resp = $this->request('/states');
        return $this->extractItems($resp->json());
    }

    public function fetchStudyFields(): array
    {
        $this->ensureConfigured();
        $resp = $this->request('/study-fields');
        return $this->extractItems($resp->json());
    }

    private function request(string $path, array $query = []): Response
    {
        $url = $this->baseUrl . '/' . ltrim($path, '/');

        $client = Http::timeout($this->timeout)
            ->withHeaders([$this->authHeader => $this->apiKey])
            ->acceptJson()
            ->retry(3, 500, function ($exception, $request) {
                return $exception instanceof \Illuminate\Http\Client\ConnectionException;
            });

        $resp = $client->get($url, $query);

        if (! $resp->successful()) {
            Log::error('PartnerApi HTTP error', [
                'url'    => $url,
                'status' => $resp->status(),
                'body'   => mb_substr($resp->body(), 0, 500),
            ]);
            throw new RuntimeException("Partner API HTTP {$resp->status()} at {$path}");
        }

        return $resp;
    }

    /**
     * Çeşitli pagination şemalarını kapsa: {data:[]} / {items:[]} / [...] / {results:[]}.
     */
    private function extractItems($payload): array
    {
        if (! is_array($payload)) return [];

        foreach (['data', 'items', 'results', 'records'] as $key) {
            if (isset($payload[$key]) && is_array($payload[$key])) {
                return $payload[$key];
            }
        }

        // Düz array
        if (array_is_list($payload)) {
            return $payload;
        }

        return [];
    }

    private function hasMorePages($payload, int $currentCount): bool
    {
        if (! is_array($payload)) return false;

        // {meta: {has_more: true}} / {pagination: {has_next: true}}
        foreach (['meta', 'pagination', '_meta'] as $key) {
            if (isset($payload[$key]) && is_array($payload[$key])) {
                $meta = $payload[$key];
                if (isset($meta['has_more']))     return (bool) $meta['has_more'];
                if (isset($meta['has_next']))     return (bool) $meta['has_next'];
                if (isset($meta['next_page']))    return ! empty($meta['next_page']);
                if (isset($meta['total_pages'], $meta['current_page'])) {
                    return $meta['current_page'] < $meta['total_pages'];
                }
            }
        }

        // Fallback: sayfa dolu geldiyse muhtemelen sıradaki var
        return $currentCount >= $this->pageSize;
    }

    private function ensureConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException(
                'Partner API yapılandırılmamış. .env\'de PARTNER_API_BASE_URL ve PARTNER_API_KEY ayarla.'
            );
        }
    }
}
