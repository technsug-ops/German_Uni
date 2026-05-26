<?php

namespace App\Filament\Widgets;

use App\Services\Analytics\VisitorStats;
use Filament\Widgets\ChartWidget;

class TrafficTrendWidget extends ChartWidget
{
    protected ?string $heading = 'Trafik Trendi (son 14 gün)';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $d = app(VisitorStats::class)->dailyTrend(14);

        return [
            'datasets' => [
                [
                    'label' => 'Sayfa görüntülemesi',
                    'data' => $d['pv'],
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.15)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
                [
                    'label' => 'Tekil ziyaretçi',
                    'data' => $d['uv'],
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.15)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $d['days'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
