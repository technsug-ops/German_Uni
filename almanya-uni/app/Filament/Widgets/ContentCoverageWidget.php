<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ContentCoverageWidget extends ChartWidget
{
    protected ?string $heading = 'İçerik Kapsama Dağılımı';

    protected static ?int $sort = 11;

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $stats = Cache::remember('dashboard:coverage', now()->addMinutes(5), function () {
            $city = DB::selectOne("
                SELECT
                    SUM(CASE WHEN content_blocks IS NOT NULL THEN 1 ELSE 0 END) AS enriched,
                    SUM(CASE WHEN content_blocks IS NULL THEN 1 ELSE 0 END) AS missing
                FROM cities
                WHERE id IN (SELECT DISTINCT city_id FROM universities WHERE is_active = 1)
            ");
            $uni = DB::selectOne("
                SELECT
                    SUM(CASE WHEN content_blocks IS NOT NULL THEN 1 ELSE 0 END) AS enriched,
                    SUM(CASE WHEN content_blocks IS NULL THEN 1 ELSE 0 END) AS missing
                FROM universities
                WHERE is_active = 1
            ");
            return [
                'city_e' => (int) $city->enriched,
                'city_m' => (int) $city->missing,
                'uni_e' => (int) $uni->enriched,
                'uni_m' => (int) $uni->missing,
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Adet',
                    'data' => [$stats['city_e'], $stats['city_m'], $stats['uni_e'], $stats['uni_m']],
                    'backgroundColor' => ['#10b981', '#fca5a5', '#3b82f6', '#bfdbfe'],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => [
                'Şehir (içerik var)',
                'Şehir (eksik)',
                'Üni (içerik var)',
                'Üni (eksik)',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array|\Filament\Support\RawJs|null
    {
        return [
            'plugins' => [
                'legend' => ['position' => 'right'],
            ],
        ];
    }
}
