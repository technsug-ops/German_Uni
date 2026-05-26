<?php

namespace App\Http\Middleware;

use App\Models\ApiClient;
use App\Models\ApiUsageLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ApiThrottleAndLog
{
    public function handle(Request $request, Closure $next): Response
    {
        $started = microtime(true);
        $client = $this->resolveApiClient($request);

        [$limit, $bucket] = $this->limitFor($client, $request);
        $cacheKey = 'api_rate:' . $bucket . ':' . floor(time() / 60);

        Cache::add($cacheKey, 0, 65);
        $hits = (int) Cache::increment($cacheKey);

        $remaining = max(0, $limit - $hits);
        $resetIn = 60 - (time() % 60);

        if ($hits > $limit) {
            $this->logUsage($request, $client, 429, (int) ((microtime(true) - $started) * 1000));

            return response()->json([
                'message' => 'Rate limit aşıldı.',
                'limit_per_minute' => $limit,
                'retry_after_seconds' => $resetIn,
            ], 429)
                ->header('X-RateLimit-Limit', $limit)
                ->header('X-RateLimit-Remaining', 0)
                ->header('X-RateLimit-Reset', $resetIn)
                ->header('Retry-After', $resetIn);
        }

        /** @var Response $response */
        $response = $next($request);

        $duration = (int) ((microtime(true) - $started) * 1000);
        $this->logUsage($request, $client, $response->getStatusCode(), $duration);

        if ($client) {
            $client->forceFill(['last_used_at' => now()])->saveQuietly();
        }

        return $response
            ->header('X-RateLimit-Limit', $limit)
            ->header('X-RateLimit-Remaining', $remaining)
            ->header('X-RateLimit-Reset', $resetIn);
    }

    private function resolveApiClient(Request $request): ?ApiClient
    {
        $bearer = $request->bearerToken();
        if (!$bearer) {
            return null;
        }

        $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($bearer);
        if (!$accessToken) {
            return null;
        }

        $tokenable = $accessToken->tokenable;
        if ($tokenable instanceof ApiClient && $tokenable->is_active) {
            return $tokenable;
        }

        return null;
    }

    private function limitFor(?ApiClient $client, Request $request): array
    {
        if ($client) {
            return [$client->effectiveRateLimit(), 'client:' . $client->id];
        }

        return [60, 'ip:' . $request->ip()];
    }

    private function logUsage(Request $request, ?ApiClient $client, int $status, int $duration): void
    {
        try {
            ApiUsageLog::create([
                'api_client_id' => $client?->id,
                'ip' => $request->ip(),
                'method' => $request->method(),
                'path' => substr($request->path(), 0, 250),
                'status' => $status,
                'duration_ms' => $duration,
                'user_agent' => substr((string) $request->userAgent(), 0, 250),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // log yazımı API'yi düşürmesin
        }
    }
}
