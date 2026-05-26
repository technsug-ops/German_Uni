<?php

namespace App\Services\Analytics;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Self-hosted analytics — page_views tablosundan aggregations.
 * Tüm sorgular cache'li (default 3 dakika).
 */
class VisitorStats
{
    private const CACHE_TTL_SECONDS = 180; // 3 dk

    public function overview(): array
    {
        return Cache::remember('analytics:overview', self::CACHE_TTL_SECONDS, function () {
            $today = now()->startOfDay();
            $weekAgo = now()->subDays(7);
            $monthAgo = now()->subDays(30);
            $now5min = now()->subMinutes(5);

            $row = DB::selectOne("
                SELECT
                    SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) AS pv_today,
                    COUNT(DISTINCT CASE WHEN created_at >= ? THEN session_id END) AS uv_today,
                    SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) AS pv_week,
                    COUNT(DISTINCT CASE WHEN created_at >= ? THEN session_id END) AS uv_week,
                    SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) AS pv_month,
                    COUNT(DISTINCT CASE WHEN created_at >= ? THEN session_id END) AS uv_month,
                    COUNT(DISTINCT CASE WHEN created_at >= ? THEN session_id END) AS online_now
                FROM page_views
                WHERE is_bot = 0
            ", [$today, $today, $weekAgo, $weekAgo, $monthAgo, $monthAgo, $now5min]);

            return [
                'pv_today' => (int) ($row->pv_today ?? 0),
                'uv_today' => (int) ($row->uv_today ?? 0),
                'pv_week' => (int) ($row->pv_week ?? 0),
                'uv_week' => (int) ($row->uv_week ?? 0),
                'pv_month' => (int) ($row->pv_month ?? 0),
                'uv_month' => (int) ($row->uv_month ?? 0),
                'online_now' => (int) ($row->online_now ?? 0),
            ];
        });
    }

    /**
     * Son N gün için günlük PV/UV — line chart için.
     */
    public function dailyTrend(int $days = 14): array
    {
        return Cache::remember("analytics:trend:{$days}", self::CACHE_TTL_SECONDS, function () use ($days) {
            $rows = DB::select("
                SELECT
                    DATE(created_at) AS day,
                    COUNT(*) AS pv,
                    COUNT(DISTINCT session_id) AS uv
                FROM page_views
                WHERE created_at >= ? AND is_bot = 0
                GROUP BY DATE(created_at)
                ORDER BY day ASC
            ", [now()->subDays($days)]);

            $byDay = [];
            foreach ($rows as $r) {
                $byDay[(string) $r->day] = ['pv' => (int) $r->pv, 'uv' => (int) $r->uv];
            }

            $days_arr = [];
            $pv_series = [];
            $uv_series = [];
            for ($i = $days - 1; $i >= 0; $i--) {
                $d = now()->subDays($i)->toDateString();
                $days_arr[] = now()->subDays($i)->format('d.m');
                $pv_series[] = $byDay[$d]['pv'] ?? 0;
                $uv_series[] = $byDay[$d]['uv'] ?? 0;
            }
            return ['days' => $days_arr, 'pv' => $pv_series, 'uv' => $uv_series];
        });
    }

    /**
     * Son 7 günde en çok ziyaret edilen sayfalar.
     */
    public function topPages(int $limit = 10, int $days = 7): array
    {
        return Cache::remember("analytics:top_pages:{$days}:{$limit}", self::CACHE_TTL_SECONDS, function () use ($limit, $days) {
            $rows = DB::select("
                SELECT
                    path,
                    COUNT(*) AS pv,
                    COUNT(DISTINCT session_id) AS uv,
                    AVG(response_ms) AS avg_ms
                FROM page_views
                WHERE created_at >= ? AND is_bot = 0
                GROUP BY path
                ORDER BY pv DESC
                LIMIT ?
            ", [now()->subDays($days), $limit]);

            return array_map(fn ($r) => [
                'path' => $r->path,
                'pv' => (int) $r->pv,
                'uv' => (int) $r->uv,
                'avg_ms' => (int) ($r->avg_ms ?? 0),
            ], $rows);
        });
    }

    /**
     * Cihaz dağılımı — user_agent SQL regex ile mobile/tablet/desktop tespiti.
     */
    public function deviceBreakdown(int $days = 7): array
    {
        return Cache::remember("analytics:devices:{$days}", self::CACHE_TTL_SECONDS, function () use ($days) {
            $rows = DB::select("
                SELECT
                    SUM(CASE WHEN user_agent REGEXP 'iPhone|Android.*Mobile|Mobile|iemobile|opera mini' THEN 1 ELSE 0 END) AS mobile,
                    SUM(CASE WHEN user_agent REGEXP 'iPad|Tablet|Android(?!.*Mobile)' THEN 1 ELSE 0 END) AS tablet,
                    SUM(CASE
                        WHEN user_agent REGEXP 'iPhone|Android.*Mobile|Mobile|iPad|Tablet|Android(?!.*Mobile)' THEN 0
                        ELSE 1
                    END) AS desktop,
                    COUNT(*) AS total
                FROM page_views
                WHERE created_at >= ? AND is_bot = 0
            ", [now()->subDays($days)]);

            $r = $rows[0] ?? null;
            return [
                'mobile' => (int) ($r->mobile ?? 0),
                'tablet' => (int) ($r->tablet ?? 0),
                'desktop' => (int) ($r->desktop ?? 0),
                'total' => (int) ($r->total ?? 0),
            ];
        });
    }

    /**
     * Saatlik trafik dağılımı (0-23) — son N gün ortalaması.
     */
    public function hourlyTraffic(int $days = 14): array
    {
        return Cache::remember("analytics:hourly:{$days}", self::CACHE_TTL_SECONDS, function () use ($days) {
            $rows = DB::select("
                SELECT HOUR(created_at) AS h, COUNT(*) AS cnt, COUNT(DISTINCT session_id) AS uv
                FROM page_views
                WHERE created_at >= ? AND is_bot = 0
                GROUP BY HOUR(created_at)
                ORDER BY h ASC
            ", [now()->subDays($days)]);

            $byHour = [];
            foreach ($rows as $r) {
                $byHour[(int) $r->h] = ['pv' => (int) $r->cnt, 'uv' => (int) $r->uv];
            }

            $hours = [];
            $pv = [];
            $uv = [];
            for ($h = 0; $h < 24; $h++) {
                $hours[] = sprintf('%02d:00', $h);
                $pv[] = $byHour[$h]['pv'] ?? 0;
                $uv[] = $byHour[$h]['uv'] ?? 0;
            }
            return ['hours' => $hours, 'pv' => $pv, 'uv' => $uv];
        });
    }

    /**
     * Referrer kaynakları — son 7 gün, top 8.
     */
    public function topReferrers(int $limit = 8, int $days = 7): array
    {
        return Cache::remember("analytics:referrers:{$days}:{$limit}", self::CACHE_TTL_SECONDS, function () use ($limit, $days) {
            $rows = DB::select("
                SELECT
                    referrer_host AS host,
                    COUNT(*) AS pv,
                    COUNT(DISTINCT session_id) AS uv
                FROM page_views
                WHERE created_at >= ? AND is_bot = 0 AND referrer_host IS NOT NULL
                GROUP BY referrer_host
                ORDER BY uv DESC
                LIMIT ?
            ", [now()->subDays($days), $limit]);

            return array_map(fn ($r) => [
                'host' => $r->host,
                'pv' => (int) $r->pv,
                'uv' => (int) $r->uv,
            ], $rows);
        });
    }
}
