<?php

namespace App\Filament\Resources\IncomingMails\Tables;

use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IncomingMailsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sent_at', 'desc')
            ->columns([
                TextColumn::make('sent_at')
                    ->label('Tarih')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('from_email')
                    ->label('Gönderen')
                    ->searchable(),
                TextColumn::make('to_name')
                    ->label('Ad')
                    ->toggleable(),
                TextColumn::make('subject')
                    ->limit(60)
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->label(fn (string $state): string => $state === 'queued' ? 'okunmadı' : 'okundu')
                    ->color(fn (string $state): string => $state === 'queued' ? 'warning' : 'gray'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->schema([
                        TextEntry::make('from_email')->label('Gönderen'),
                        TextEntry::make('to_name')->label('Ad')->placeholder('—'),
                        TextEntry::make('sent_at')->label('Tarih')->dateTime('d.m.Y H:i')->placeholder('—'),
                        TextEntry::make('subject')->label('Konu'),
                        TextEntry::make('body')->label('İçerik')->prose()->columnSpanFull(),
                    ]),
            ]);
    }
}
