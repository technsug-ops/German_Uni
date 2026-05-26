<?php

namespace App\Filament\Widgets;

use App\Services\Analytics\VisitorStats;
use Filament\Widgets\Widget;

class TopPagesWidget extends Widget
{
    protected string $view = 'filament.widgets.top-pages-widget';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    public function getViewData(): array
    {
        $stats = app(VisitorStats::class);
        return [
            'pages' => $stats->topPages(10, 7),
            'referrers' => $stats->topReferrers(8, 7),
        ];
    }
}
