<?php

namespace App\Filament\Resources\FaqQualityAtlas\Pages;

use App\Filament\Resources\FaqQualityAtlas\FaqQualityAtlasResource;
use App\Filament\Resources\FaqQualityAtlas\Widgets\AtlasOverview;
use App\Services\FaqAtlas\AtlasFilters;
use Filament\Actions\Action;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;

class ListFaqQualityAtlas extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = FaqQualityAtlasResource::class;

    /** Kısayollar: ?batch=A, ?risk=P0, ?preset=chatbot_risk, ?topic=Visa, ?audit=2026-09-28 → tablo filtresine çevrilir. */
    public function mount(): void
    {
        $q = request()->query();
        foreach (['batch' => 'batch', 'risk' => 'risk', 'quality' => 'quality'] as $param => $filter) {
            if (! empty($q[$param]) && is_string($q[$param])) {
                $this->tableFilters[$filter] = ['values' => [strtoupper($q[$param])]];
            }
        }
        foreach (['preset', 'topic', 'audit'] as $single) {
            if (! empty($q[$single]) && is_string($q[$single])) {
                $this->tableFilters[$single] = ['value' => $q[$single]];
            }
        }

        parent::mount();
    }

    protected function getHeaderWidgets(): array
    {
        return [AtlasOverview::class];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')->label('CSV dışa aktar')->icon('heroicon-o-arrow-down-tray')->color('gray')
                ->url(fn () => route('admin.faq-atlas.export', array_filter([
                    'filters' => AtlasFilters::compact($this->tableFilters ?? []),
                    'search' => $this->tableSearch ?: null,
                ]))),
        ];
    }
}
