<?php

namespace App\Filament\Resources\EmailMessages\Tables;

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
            ]);
    }
}
