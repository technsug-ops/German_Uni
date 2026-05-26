<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EnrichmentStatsWidget extends StatsOverviewWidget
{
    protected ?string $heading = 'İçerik Üretimi';

    protected static ?int $sort = 10;

    /**
     * 5 dakika cache — dashboard refresh'lerinde DB'yi yormaz.
     */
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $data = Cache::remember('dashboard:enrichment_stats_v2', now()->addMinutes(5), function () {
            // Tek sorgu — şehir özet (counts + last 24h + chart için son 7 gün)
            $cityStats = DB::selectOne("
                SELECT
                    COUNT(*) AS total,
                    SUM(CASE WHEN content_blocks IS NOT NULL THEN 1 ELSE 0 END) AS enriched,
                    SUM(CASE WHEN image_url IS NOT NULL THEN 1 ELSE 0 END) AS with_image,
                    SUM(CASE WHEN last_enriched_at >= ? THEN 1 ELSE 0 END) AS last_24h
                FROM cities
                WHERE id IN (SELECT DISTINCT city_id FROM universities WHERE is_active = 1)
            ", [now()->subDay()]);

            $uniStats = DB::selectOne("
                SELECT
                    COUNT(*) AS total,
                    SUM(CASE WHEN content_blocks IS NOT NULL THEN 1 ELSE 0 END) AS enriched,
                    SUM(CASE WHEN image_url IS NOT NULL THEN 1 ELSE 0 END) AS with_image,
                    SUM(CASE WHEN last_enriched_at >= ? THEN 1 ELSE 0 END) AS last_24h
                FROM universities
                WHERE is_active = 1
            ", [now()->subDay()]);

            // Son 7 gün sparkline — tek sorgu GROUP BY
            $cityChart = DB::select("
                SELECT DATE(last_enriched_at) AS d, COUNT(*) AS c
                FROM cities
                WHERE last_enriched_at >= ?
                GROUP BY DATE(last_enriched_at)
            ", [now()->subDays(7)]);

            $uniChart = DB::select("
                SELECT DATE(last_enriched_at) AS d, COUNT(*) AS c
                FROM universities
                WHERE last_enriched_at >= ? AND is_active = 1
                GROUP BY DATE(last_enriched_at)
            ", [now()->subDays(7)]);

            // stdClass değil — array dön ki cache serialize'da __PHP_Incomplete_Class olmasın
            return [
                'city'       => (array) ($cityStats ?: (object) ['total' => 0, 'enriched' => 0, 'with_image' => 0, 'last_24h' => 0]),
                'uni'        => (array) ($uniStats ?: (object) ['total' => 0, 'enriched' => 0, 'with_image' => 0, 'last_24h' => 0]),
                'city_chart' => $this->fillDailyGaps($cityChart),
                'uni_chart'  => $this->fillDailyGaps($uniChart),
            ];
        });

        $city = $data['city'];
        $uni  = $data['uni'];

        $cityTotal = (int) ($city['total'] ?? 0);
        $cityEnriched = (int) ($city['enriched'] ?? 0);
        $cityImage = (int) ($city['with_image'] ?? 0);
        $uniTotal = (int) ($uni['total'] ?? 0);
        $uniEnriched = (int) ($uni['enriched'] ?? 0);
        $uniImage = (int) ($uni['with_image'] ?? 0);

        $cityPct = $cityTotal > 0 ? round(($cityEnriched / $cityTotal) * 100) : 0;
        $uniPct  = $uniTotal > 0 ? round(($uniEnriched / $uniTotal) * 100) : 0;
        $last24h = (int) ($city['last_24h'] ?? 0) + (int) ($uni['last_24h'] ?? 0);

        return [
            Stat::make('Enrich edilmiş şehir', "{$cityEnriched} / {$cityTotal}")
                ->description("%{$cityPct} kapsama · {$cityImage} görselli")
                ->descriptionIcon('heroicon-m-photo')
                ->color($cityPct >= 80 ? 'success' : ($cityPct >= 40 ? 'warning' : 'danger'))
                ->chart($data['city_chart']),

            Stat::make('Enrich edilmiş üniversite', "{$uniEnriched} / {$uniTotal}")
                ->description("%{$uniPct} kapsama · {$uniImage} görselli")
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color($uniPct >= 30 ? 'success' : ($uniPct >= 10 ? 'warning' : 'danger'))
                ->chart($data['uni_chart']),

            Stat::make('Son 24 saat', $last24h . ' yeni')
                ->description('Şehir + üni toplam enrich aktivitesi')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('primary'),
        ];
    }

    /**
     * GROUP BY çıktısını 7-elemanlı array'e doldur (sparkline için).
     */
    private function fillDailyGaps(array $rows): array
    {
        $byDay = [];
        foreach ($rows as $r) {
            $byDay[(string) $r->d] = (int) $r->c;
        }

        $out = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();
            $out[] = $byDay[$day] ?? 0;
        }
        return $out;
    }
}
