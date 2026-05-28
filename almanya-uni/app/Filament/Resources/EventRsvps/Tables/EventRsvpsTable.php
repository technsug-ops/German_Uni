<?php

namespace App\Filament\Resources\EventRsvps\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EventRsvpsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'going',
                        'warning' => 'maybe',
                        'gray'    => 'cancelled',
                    ])
                    ->sortable(),
                TextColumn::make('display_name')
                    ->label('Katılımcı')
                    ->searchable(['attendee_name', 'attendee_email'])
                    ->description(fn ($record) => $record->attendee_email ?: $record->user?->email),
                TextColumn::make('event.title_tr')
                    ->label('Etkinlik')
                    ->limit(40)
                    ->searchable()
                    ->url(fn ($record) => $record->event ? route('events.show', $record->event->slug) : null, true),
                TextColumn::make('event.starts_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('note')
                    ->label('Not')
                    ->limit(60)
                    ->wrap()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Kayıt')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'going'     => '✅ Katılıyor',
                        'maybe'     => '🤔 Belki',
                        'cancelled' => '❌ İptal',
                    ]),
            ])
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
