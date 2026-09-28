<?php

namespace App\Filament\Resources\FaqQualityAtlas;

use App\Filament\Resources\FaqQualityAtlas\Pages\ListFaqQualityAtlas;
use App\Filament\Resources\FaqQualityAtlas\Pages\ViewFaqQualityAtlas;
use App\Models\FaqQualityAtlas;
use App\Services\FaqAtlas\AtlasFilters;
use App\Services\FaqAtlas\LiveStatus;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * 🧭 SSS Kalite Atlası — salt okunur operasyon panosu (V1). Yalnız admin (editörler dahil değil).
 * Atlas satırları değişmez denetim kaydıdır; canlı SSS durumu ayrı hesaplanır (LiveStatus).
 * Bu kaynakta HİÇBİR yazma işlemi yoktur (oluştur/düzenle/sil/toplu işlem yok).
 */
class FaqQualityAtlasResource extends Resource
{
    protected static ?string $model = FaqQualityAtlas::class;

    protected static ?string $slug = 'ops/faq-atlas';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationLabel = '🧭 SSS Kalite Atlası';
    protected static ?string $modelLabel = 'SSS kümesi';
    protected static ?string $pluralModelLabel = 'SSS Kalite Atlası';
    protected static ?int $navigationSort = 5;
    protected static string|\UnitEnum|null $navigationGroup = 'Sistem';

    public static function canAccess(): bool
    {
        return auth()->user()?->is_admin === true;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        $noop = fn (Builder $q) => $q;   // filtreler AtlasFilters'ta tek kaynaktan uygulanır
        $opts = fn (array $values) => array_combine($values, $values);
        $statusBadge = fn (string $locale) => TextColumn::make("{$locale}_status")
            ->label(strtoupper($locale))
            ->badge()
            ->color(fn (string $state) => self::statusColor($state))
            ->description(function (FaqQualityAtlas $record) use ($locale) {
                $live = $record->{"{$locale}_live"} ?? null;

                return $live && LiveStatus::materiallyDiffers($record->{"{$locale}_status"}, $live) ? "canlı: {$live}" : null;
            });

        return $table
            ->query(fn () => FaqQualityAtlas::query()->withLive()->with([
                'tr' => fn ($q) => $q->select(['faqs.id', 'faqs.translation_group_id', 'faqs.locale', 'faqs.slug', 'faqs.question', 'faqs.faq_topic_id']),
            ]))
            ->modifyQueryUsing(fn (Builder $query) => AtlasFilters::apply($query, $table->getLivewire()->tableFilters ?? []))
            ->defaultSort('priority_score', 'desc')
            ->paginated([25, 50, 100])
            ->defaultPaginationPageOption(50)
            ->recordUrl(fn (FaqQualityAtlas $record) => static::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('priority_score')->label('Öncelik')->sortable()->numeric(),
                TextColumn::make('risk_level')->label('Risk')->badge()->sortable()
                    ->color(fn (string $state) => ['P0' => 'danger', 'P1' => 'warning'][$state] ?? 'gray'),
                TextColumn::make('topic')->label('Konu')->sortable()->wrap(),
                TextColumn::make('tr.question')->label('TR Soru')->wrap()->limit(90)
                    ->default(fn (FaqQualityAtlas $record) => $record->tr_question_at_audit)
                    ->searchable(query: fn (Builder $query, string $search) => AtlasFilters::search($query, $search)),
                TextColumn::make('tr_quality')->label('TR kalite')->badge()->sortable()
                    ->color(fn (string $state) => ['A' => 'success', 'B' => 'warning', 'C' => 'gray', 'D' => 'danger'][$state] ?? 'gray'),
                $statusBadge('tr'),
                $statusBadge('en'),
                $statusBadge('de'),
                TextColumn::make('parity_score')->label('Parite')->sortable()->formatStateUsing(fn ($state) => "{$state}/3"),
                TextColumn::make('translation_strategy')->label('Strateji')->badge()->color('gray')
                    ->formatStateUsing(fn (?string $state) => $state ? str_replace('_', ' ', $state) : '—'),
                TextColumn::make('proposed_batch')->label('Batch')->badge()->sortable()
                    ->color(fn (string $state) => $state === 'A' ? 'primary' : 'gray')
                    ->formatStateUsing(fn (string $state) => $state === 'OUTSIDE_BATCH' ? '—' : $state),
                TextColumn::make('authoritative_internal_source')->label('Rehber')
                    ->formatStateUsing(fn (?string $state) => $state ? '📘 '.basename($state) : '—')->limit(28)
                    ->url(fn (FaqQualityAtlas $record) => $record->authoritative_internal_source, shouldOpenInNewTab: true),
                IconColumn::make('review_needed')->label('Review')->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')->falseColor('gray'),
                TextColumn::make('recommended_action')->label('Aksiyon')->limit(60)->wrap()
                    ->tooltip(fn (FaqQualityAtlas $record) => $record->recommended_action),
            ])
            ->filters([
                SelectFilter::make('audit')->label('Denetim anlık görüntüsü')
                    ->options(fn () => FaqQualityAtlas::query()->distinct()->orderByDesc('audit_label')->pluck('audit_label', 'audit_label')->all())
                    ->default(fn () => FaqQualityAtlas::latestAuditLabel())->query($noop),
                SelectFilter::make('preset')->label('Hazır filtre')->options(AtlasFilters::PRESETS)->query($noop),
                SelectFilter::make('risk')->label('Risk')->multiple()->options($opts(FaqQualityAtlas::RISKS))->query($noop),
                SelectFilter::make('quality')->label('TR kalite')->multiple()
                    ->options(['A' => 'A — Translation ready', 'B' => 'B — Fix then translate', 'C' => 'C — Merge / replace', 'D' => 'D — Research required'])->query($noop),
                SelectFilter::make('topic')->label('Konu')->searchable()
                    ->options(fn () => FaqQualityAtlas::query()->distinct()->orderBy('topic')->pluck('topic', 'topic')->all())->query($noop),
                SelectFilter::make('en_status')->label('EN durum')->multiple()->options($opts(FaqQualityAtlas::STATUSES))->query($noop),
                SelectFilter::make('de_status')->label('DE durum')->multiple()->options($opts(FaqQualityAtlas::STATUSES))->query($noop),
                SelectFilter::make('parity')->label('Parite')->multiple()->options(['3' => '3/3', '2' => '2/3', '1' => '1/3', '0' => '0/3'])->query($noop),
                SelectFilter::make('strategy')->label('Strateji')->multiple()
                    ->options(array_combine(FaqQualityAtlas::STRATEGIES, array_map(fn ($s) => str_replace('_', ' ', $s), FaqQualityAtlas::STRATEGIES)))->query($noop),
                SelectFilter::make('batch')->label('Batch')->multiple()->options($opts(FaqQualityAtlas::BATCHES))->query($noop),
                self::ternary('guide', 'Otoriter rehber'),
                self::ternary('research', 'Dış araştırma gerekli'),
                self::ternary('review', 'Review needed'),
                self::ternary('resolved', 'Eşleşti (resolved)'),
                self::ternary('chatbot', 'Chatbot riski'),
            ])
            ->filtersFormColumns(3)
            ->recordActions([])
            ->toolbarActions([]);
    }

    private static function ternary(string $key, string $label): TernaryFilter
    {
        $noop = fn (Builder $q) => $q;

        return TernaryFilter::make($key)->label($label)->queries(true: $noop, false: $noop, blank: $noop);
    }

    public static function statusColor(?string $state): string
    {
        return match ($state) {
            'COMPLETE', 'CONTENT' => 'success',
            'EMPTY', 'MISSING' => 'danger',
            'BROKEN', 'STALE', 'UNPUBLISHED' => 'warning',
            'PARTIAL', 'THIN' => 'info',
            default => 'gray',
        };
    }

    public static function getWidgets(): array
    {
        return [\App\Filament\Resources\FaqQualityAtlas\Widgets\AtlasOverview::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFaqQualityAtlas::route('/'),
            'view' => ViewFaqQualityAtlas::route('/cluster/{record}'),
        ];
    }
}
