<?php

namespace App\Filament\Pages;

use App\Services\Analytics\VisitorStats;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

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
    public array $affiliate = [];

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
        $this->affiliate = $this->affiliateStats($this->days);
    }

    /**
     * Affiliate tıklama istatistikleri (gelir sinyali) — seçili aralıkta hangi
     * Sperrkonto/sigorta sağlayıcısı kaç kez tıklandı + bağlam (index/show/karşılaştırma) dağılımı.
     */
    protected function affiliateStats(int $days): array
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('affiliate_clicks')) {
            return ['total' => 0, 'by_provider' => [], 'by_context' => []];
        }

        $since = now()->subDays($days);
        $base = fn () => DB::table('affiliate_clicks')->where('created_at', '>=', $since);

        return [
            'total' => $base()->count(),
            'by_provider' => $base()
                ->select('provider_type', 'provider_slug', DB::raw('count(*) as c'))
                ->groupBy('provider_type', 'provider_slug')
                ->orderByDesc('c')->limit(20)->get()
                ->map(fn ($r) => ['type' => $r->provider_type, 'slug' => $r->provider_slug, 'c' => (int) $r->c])->all(),
            'by_context' => $base()
                ->select('context', DB::raw('count(*) as c'))
                ->groupBy('context')->orderByDesc('c')->get()
                ->map(fn ($r) => ['context' => $r->context ?: '—', 'c' => (int) $r->c])->all(),
        ];
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
