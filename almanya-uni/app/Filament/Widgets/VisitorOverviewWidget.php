<?php

namespace App\Filament\Widgets;

use App\Services\Analytics\VisitorStats;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VisitorOverviewWidget extends StatsOverviewWidget
{
    protected ?string $heading = 'Ziyaretçi Trafiği (self-hosted)';

    protected static ?int $sort = 0;

    /**
     * 60 saniyede bir "şu an online" yenilensin.
     */
    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $s = app(VisitorStats::class)->overview();

        return [
            Stat::make('Şu an online', $s['online_now'])
                ->description('Son 5 dakikada aktif')
                ->descriptionIcon('heroicon-m-signal')
                ->color($s['online_now'] > 0 ? 'success' : 'gray'),

            Stat::make('Bugün', "{$s['uv_today']} ziyaretçi")
                ->description("{$s['pv_today']} sayfa görüntülemesi")
                ->descriptionIcon('heroicon-m-sun')
                ->color('primary'),

            Stat::make('Son 7 gün', "{$s['uv_week']} ziyaretçi")
                ->description("{$s['pv_week']} sayfa görüntülemesi")
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),

            Stat::make('Son 30 gün', "{$s['uv_month']} ziyaretçi")
                ->description("{$s['pv_month']} sayfa görüntülemesi")
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),
        ];
    }
}
