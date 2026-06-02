<?php

namespace App\Filament\Resources\NewsSources\Tables;

use App\Models\NewsSource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Artisan;

class NewsSourcesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ToggleColumn::make('enabled')->label('Aktif'),

                TextColumn::make('name')
                    ->label('Kaynak')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('default_category')
                    ->label('Kategori')
                    ->badge()
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('keywords')
                    ->label('Anahtar kelimeler')
                    ->badge()
                    ->placeholder('filtre yok')
                    ->limitList(4)
                    ->toggleable(),

                TextColumn::make('url')
                    ->label('Feed')
                    ->limit(40)
                    ->url(fn (NewsSource $r) => $r->url, true)
                    ->color('primary')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('last_result')
                    ->label('Son çekim')
                    ->badge()
                    ->color(fn ($state) => $state === 'okunamadı' ? 'danger' : 'success')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('last_fetched_at')
                    ->label('Son tarih')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('sort_order')->label('Sıra')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                // Tek kaynağı test/çek — düzenledikten sonra hemen dene.
                Action::make('test')
                    ->label('Bu Kaynağı Çek')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Bu kaynaktan haber çek')
                    ->modalDescription('Sadece bu kaynaktan aday çekilir (gelen kutusuna düşer, yayına çıkmaz).')
                    ->action(function (NewsSource $r) {
                        Artisan::call('news:fetch', ['--source' => $r->name]);
                        $out = trim(Artisan::output());
                        Notification::make()
                            ->title($r->name . ' — çekim bitti')
                            ->body($out ?: 'Çıktı yok.')
                            ->success()
                            ->send();
                    }),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
