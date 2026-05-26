<?php

namespace App\Filament\Resources\ScrapeSources;

use App\Filament\Resources\ScrapeSources\Pages\CreateScrapeSource;
use App\Filament\Resources\ScrapeSources\Pages\EditScrapeSource;
use App\Filament\Resources\ScrapeSources\Pages\ListScrapeSources;
use App\Models\ScrapeSource;
use BackedEnum;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ScrapeSourceResource extends Resource
{
    /** Hassas kaynak — yalnızca tam admin (editör göremez). */
    public static function canViewAny(): bool
    {
        return auth()->user()?->isFullAdmin() ?? false;
    }

    protected static ?string $model = ScrapeSource::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;
    protected static ?string $navigationLabel = 'Scrape Sources';
    protected static ?string $modelLabel = 'Scrape Source';
    protected static ?string $pluralModelLabel = 'Scrape Sources';
    protected static ?int $navigationSort = 70;
    protected static string|\UnitEnum|null $navigationGroup = 'Entegrasyonlar';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('university_id')
                ->relationship('university', 'name_de')
                ->searchable()
                ->required(),
            TextInput::make('name')->label('Etiket (opsiyonel)')->maxLength(120),
            TextInput::make('list_url')->label('Program listesi URL')->required()->url()->maxLength(500),
            Select::make('adapter')->required()->default('generic_html')->options([
                'generic_html' => 'Generic HTML (CSS selectors)',
                'playwright' => 'Playwright (JS-rendered) — yakında',
                'sitemap' => 'Sitemap.xml — yakında',
                'custom_php' => 'Custom PHP class — yakında',
            ]),
            Textarea::make('config')
                ->label('Config (JSON)')
                ->helperText('item_selector, name_selector, url_selector, degree_selector, language_selector, pagination_next_selector, max_pages, degree_map')
                ->rows(10)
                ->dehydrateStateUsing(fn ($state) => is_string($state) ? json_decode($state, true) : $state)
                ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $state)
                ->columnSpanFull(),
            TextInput::make('throttle_ms')->numeric()->default(3000)->required()->minValue(500)->maxValue(60000),
            Toggle::make('respect_robots')->label('robots.txt\'ye uy')->default(true),
            Toggle::make('is_enabled')->label('Aktif')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('university.name_de')->label('Üni')->searchable()->limit(40),
                TextColumn::make('adapter')->badge(),
                TextColumn::make('list_url')->limit(45)->copyable()->tooltip(fn ($record) => $record->list_url),
                TextColumn::make('last_status')
                    ->badge()
                    ->color(fn (?string $state) => match ($state) {
                        'ok', 'dry_run_ok' => 'success',
                        'fail', 'blocked' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('last_found_count')->label('Son bulunan')->numeric(),
                TextColumn::make('last_run_at')->dateTime('d.m H:i')->placeholder('—'),
                IconColumn::make('is_enabled')->boolean(),
            ])
            ->filters([
                SelectFilter::make('adapter')->options([
                    'generic_html' => 'Generic HTML',
                    'playwright' => 'Playwright',
                    'sitemap' => 'Sitemap',
                    'custom_php' => 'Custom PHP',
                ]),
                TernaryFilter::make('is_enabled'),
            ])
            ->recordActions([
                Action::make('runNow')
                    ->label('▶ Çalıştır')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (ScrapeSource $record) {
                        \Artisan::call('scrape:run', ['--source' => $record->id]);
                        Notification::make()->title('Scrape tamamlandı')->success()
                            ->body(\Artisan::output())->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort('last_run_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListScrapeSources::route('/'),
            'create' => CreateScrapeSource::route('/create'),
            'edit' => EditScrapeSource::route('/{record}/edit'),
        ];
    }
}
