<?php

namespace App\Filament\Widgets;

use App\Services\Analytics\VisitorStats;
use Filament\Widgets\ChartWidget;

class DeviceBreakdownWidget extends ChartWidget
{
    protected ?string $heading = 'Cihaz Dağılımı (son 7 gün)';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $d = app(VisitorStats::class)->deviceBreakdown(7);

        return [
            'datasets' => [
                [
                    'label' => 'Sayfa görüntülemesi',
                    'data' => [$d['mobile'], $d['desktop'], $d['tablet']],
                    'backgroundColor' => ['#10b981', '#3b82f6', '#f59e0b'],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => ['📱 Mobil', '💻 Desktop', '📲 Tablet'],
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
