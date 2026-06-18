<?php

namespace App\Filament\Resources\EmailMessages\Tables;

use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmailMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('sent_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('to_email')
                    ->searchable(),
                TextColumn::make('subject')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sent' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('template_key')
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'queued' => 'queued',
                        'sent' => 'sent',
                        'failed' => 'failed',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->schema([
                        TextEntry::make('subject')->label('Konu'),
                        TextEntry::make('to_email')->label('Alıcı'),
                        TextEntry::make('sent_at')->label('Gönderim')->dateTime('d.m.Y H:i')->placeholder('—'),
                        TextEntry::make('status')->label('Durum')->badge(),
                        TextEntry::make('template_key')->label('Şablon')->placeholder('—'),
                        TextEntry::make('body')->label('İçerik')->prose()->columnSpanFull(),
                    ]),
            ]);
    }
}
