<?php

namespace App\Filament\Pages;

use App\Services\Analytics\VisitorStats;
use BackedEnum;
use Filament\Pages\Page;

/**
 * Detaylı analitik — panel widget'larının (Top Pages / Referrers / Device) tam,
 * tarih-aralığı seçili, tam-yollu ve çok-satırlı hali. VisitorStats'ı paylaşır.
 */
class Analytics extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = '📊 Analitik';
    protected static ?string $title = 'Analitik — Detaylı Trafik';
    protected static ?int $navigationSort = 2;
    protected static string|\UnitEnum|null $navigationGroup = 'Sistem';

    protected string $view = 'filament.pages.analytics';

    /** Seçili tarih aralığı (gün). */
    public int $days = 7;

    public array $overview = [];
    public array $pages = [];
    public array $referrers = [];
    public array $devices = [];
    public array $trend = [];

    public function mount(): void
    {
        $this->loadData();
    }

    /** Tarih aralığı butonları çağırır. */
    public function setDays(int $days): void
    {
        $this->days = in_array($days, [1, 7, 30, 90], true) ? $days : 7;
        $this->loadData();
    }

    protected function loadData(): void
    {
        $s = app(VisitorStats::class);
        $this->overview  = $s->overview();
        $this->pages     = $s->topPages(50, $this->days);
        $this->referrers = $s->topReferrers(30, $this->days);
        $this->devices   = $s->deviceBreakdown($this->days);
        $this->trend     = $s->dailyTrend(min($this->days, 30));
    }

    public function getRangeLabel(): string
    {
        return match ($this->days) {
            1       => 'bugün',
            30      => 'son 30 gün',
            90      => 'son 90 gün',
            default => 'son 7 gün',
        };
    }
}
