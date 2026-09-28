<?php

namespace App\Filament\Resources\FaqQualityAtlas\Widgets;

use App\Filament\Resources\FaqQualityAtlas\FaqQualityAtlasResource;
use App\Filament\Resources\FaqQualityAtlas\Pages\ListFaqQualityAtlas;
use App\Models\FaqQualityAtlas;
use App\Services\FaqAtlas\AtlasFilters;
use App\Services\FaqAtlas\LiveStatus;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;

/**
 * Atlas üst paneli: KPI kartları, hazır filtreler, konu tablosu, batch özeti.
 * Sayılar sabit değil — sayfanın o anki filtre/anlık görüntü/arama durumundan gruplu SQL ile hesaplanır.
 */
class AtlasOverview extends Widget
{
    use InteractsWithPageTable;

    protected string $view = 'filament.faq-atlas.overview';

    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = false;

    protected function getTablePage(): string
    {
        return ListFaqQualityAtlas::class;
    }

    private function base(): Builder
    {
        return AtlasFilters::apply(FaqQualityAtlas::query(), $this->tableFilters ?? [], $this->tableSearch ?: null);
    }

    private static function sums(array $map): string
    {
        return implode(', ', array_map(fn ($alias, $cond) => "SUM(CASE WHEN {$cond} THEN 1 ELSE 0 END) AS `{$alias}`", array_keys($map), $map));
    }

    public function kpis(): array
    {
        $map = ['total' => '1 = 1', 'unresolved' => 'resolved = 0', 'chatbot' => 'chatbot_risk = 1'];
        foreach (FaqQualityAtlas::QUALITIES as $q) {
            $map["q_{$q}"] = "tr_quality = '{$q}'";
        }
        foreach (FaqQualityAtlas::RISKS as $r) {
            $map["r_{$r}"] = "risk_level = '{$r}'";
        }
        foreach ([3, 2, 1, 0] as $p) {
            $map["p_{$p}"] = "parity_score = {$p}";
        }
        foreach (['en', 'de'] as $l) {
            foreach (FaqQualityAtlas::STATUSES as $s) {
                $map["{$l}_{$s}"] = "{$l}_status = '{$s}'";
            }
        }
        $row = (array) $this->base()->toBase()->selectRaw(self::sums($map))->first();
        $row = array_map(fn ($v) => (int) $v, $row);
        $row['review'] = $this->base()->whereRaw(LiveStatus::reviewSql())->count();

        return $row;
    }

    public function topics(): array
    {
        $gaps = "('".implode("','", FaqQualityAtlas::GAP_STATUSES)."')";

        return $this->base()->toBase()->select('topic')
            ->selectRaw(self::sums([
                'total' => '1 = 1', 'p0' => "risk_level = 'P0'",
                'a' => "tr_quality = 'A'", 'b' => "tr_quality = 'B'", 'c' => "tr_quality = 'C'", 'd' => "tr_quality = 'D'",
                'en_gap' => "en_status IN {$gaps}", 'de_gap' => "de_status IN {$gaps}",
                'ready' => 'JSON_LENGTH(COALESCE(quality_ready_locales, JSON_ARRAY())) = 3',
            ]))
            ->selectRaw('ROUND(AVG(parity_score), 2) AS avg_parity')
            ->groupBy('topic')->orderByDesc('total')->orderBy('topic')
            ->get()->map(fn ($r) => (array) $r)->all();
    }

    public function batchSummary(): ?array
    {
        $batches = array_values(array_filter((array) ($this->tableFilters['batch']['values'] ?? [])));
        if (! $batches) {
            return null;
        }
        $gaps = "('".implode("','", FaqQualityAtlas::GAP_STATUSES)."')";
        $row = (array) $this->base()->toBase()->selectRaw(self::sums([
            'total' => '1 = 1', 'p0' => "risk_level = 'P0'",
            'a' => "tr_quality = 'A'", 'b' => "tr_quality = 'B'", 'c' => "tr_quality = 'C'", 'd' => "tr_quality = 'D'",
            'ready' => "tr_quality = 'A'", 'research' => "(tr_quality = 'D' OR external_source_needed = 1)",
            'en_gap' => "en_status IN {$gaps}", 'de_gap' => "de_status IN {$gaps}",
        ]))->first();

        return ['batches' => $batches] + array_map(fn ($v) => (int) $v, $row);
    }

    public function audit(): ?string
    {
        return AtlasFilters::audit($this->tableFilters ?? []);
    }

    /** Liste URL'i: seçili anlık görüntü korunur + verilen filtre. */
    public function filterUrl(array $filters): string
    {
        $all = ['audit' => ['value' => $this->audit()]] + $filters;

        return FaqQualityAtlasResource::getUrl('index').'?'.http_build_query(['filters' => $all]);
    }
}
