<?php

namespace App\Filament\Resources\LegalPages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LegalPagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label('Sayfa')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'privacy' => '🔒 Privacy',
                        'terms' => '📜 Terms',
                        'cookies' => '🍪 Cookies',
                        'impressum' => '🏛️ Impressum',
                        'disclaimer' => '⚠️ Disclaimer',
                        default => $state,
                    }),
                TextColumn::make('titles.tr')
                    ->label('TR Başlık')
                    ->limit(40)
                    ->getStateUsing(fn ($record) => is_array($record->titles ?? null) ? ($record->titles['tr'] ?? '—') : '—'),
                IconColumn::make('is_published')
                    ->label('Yayında')
                    ->boolean(),
                TextColumn::make('effective_date')
                    ->label('Yürürlük')
                    ->date('d.m.Y')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Son Güncelleme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->since(),
            ])
            ->defaultSort('sort_order', 'asc')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
