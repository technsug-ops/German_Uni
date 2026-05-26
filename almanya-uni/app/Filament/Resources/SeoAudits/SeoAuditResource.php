<?php

namespace App\Filament\Resources\SeoAudits;

use App\Filament\Resources\SeoAudits\Pages\ListSeoAudits;
use App\Filament\Resources\SeoAudits\Pages\ViewSeoAudit;
use App\Models\SeoAudit;
use App\Services\Seo\SeoAuditorService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SeoAuditResource extends Resource
{
    protected static ?string $model = SeoAudit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlassCircle;
    protected static ?string $navigationLabel = '🔍 SEO Audit';
    protected static ?string $modelLabel = 'SEO Audit';
    protected static ?string $pluralModelLabel = 'SEO Audit';
    protected static ?int $navigationSort = 32;
    protected static string|\UnitEnum|null $navigationGroup = 'İçerik';

    public static function form(Schema $schema): Schema
    {
        return $schema; // read-only resource (yalnız liste + detay)
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('template')
                    ->label('Template')
                    ->formatStateUsing(fn (string $state) => SeoAudit::TEMPLATES[$state] ?? $state)
                    ->searchable()
                    ->badge(),
                TextColumn::make('page_title')
                    ->label('Sayfa Başlık')
                    ->limit(40)
                    ->wrap(),
                TextColumn::make('opportunity_score')
                    ->label('Fırsat')
                    ->badge()
                    ->color(fn (int $state) => match (true) {
                        $state >= 90 => 'danger',
                        $state >= 70 => 'warning',
                        $state >= 50 => 'info',
                        default => 'success',
                    })
                    ->formatStateUsing(fn (int $state) => $state . '/100')
                    ->sortable(),
                TextColumn::make('keywords_found_count')
                    ->label('✓ Found')
                    ->state(fn ($record) => is_array($record->keywords_found) ? count($record->keywords_found) : 0)
                    ->badge()
                    ->color('success'),
                TextColumn::make('keywords_missing_count')
                    ->label('✗ Missing')
                    ->state(fn ($record) => is_array($record->keywords_missing) ? count($record->keywords_missing) : 0)
                    ->badge()
                    ->color('danger'),
                TextColumn::make('content_length')
                    ->label('Karakter')
                    ->numeric()
                    ->color(fn (int $state) => $state < 1000 ? 'danger' : ($state < 2500 ? 'warning' : 'success')),
                TextColumn::make('h2_count')->label('H2')->color(fn (int $state) => $state < 3 ? 'danger' : 'success'),
                TextColumn::make('last_audited_at')->label('Audit')->dateTime('d.m H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('template')->options(SeoAudit::TEMPLATES),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('🔍 Detay')
                    ->url(fn (SeoAudit $r) => static::getUrl('view', ['record' => $r])),
                Action::make('reaudit')
                    ->label('🔄 Yeniden Audit')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (SeoAudit $record) {
                        $svc = app(SeoAuditorService::class);
                        $svc->audit($record->template, $record->sample_url, false);
                        Notification::make()->title('🔄 Audit yenilendi')->success()->send();
                    }),
                Action::make('aiSuggest')
                    ->label('🪄 AI Öneri Üret')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('Eksik keyword\'lere göre Gemini ile bu sayfaya eklenecek bölümler önerilir.')
                    ->action(function (SeoAudit $record) {
                        $svc = app(SeoAuditorService::class);
                        $svc->audit($record->template, $record->sample_url, true);
                        Notification::make()->title('🪄 AI önerisi üretildi — Detay\'a bak')->success()->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([]),
            ])
            ->defaultSort('opportunity_score', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSeoAudits::route('/'),
            'view' => ViewSeoAudit::route('/{record}'),
        ];
    }
}
