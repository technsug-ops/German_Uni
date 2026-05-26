<?php

namespace App\Filament\Widgets;

use App\Services\Analytics\VisitorStats;
use Filament\Widgets\ChartWidget;

class HourlyTrafficWidget extends ChartWidget
{
    protected ?string $heading = 'Saatlik Trafik (son 14 gün toplamı)';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $d = app(VisitorStats::class)->hourlyTraffic(14);

        return [
            'datasets' => [
                [
                    'label' => 'Sayfa görüntülemesi',
                    'data' => $d['pv'],
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#1e40af',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Tekil ziyaretçi',
                    'data' => $d['uv'],
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#047857',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $d['hours'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
