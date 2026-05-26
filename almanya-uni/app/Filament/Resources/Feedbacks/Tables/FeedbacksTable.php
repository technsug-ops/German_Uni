<?php

namespace App\Filament\Resources\Feedbacks\Tables;

use App\Models\Feedback;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class FeedbacksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('type')
                    ->label('Tip')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Feedback::TYPES[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'bug' => 'danger',
                        'suggestion' => 'success',
                        'content' => 'warning',
                        'partnership' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('name')
                    ->label('Kim')
                    ->placeholder('— Anonim —')
                    ->description(fn (Feedback $r) => $r->email),

                TextColumn::make('message')
                    ->label('Mesaj')
                    ->limit(80)
                    ->wrap(),

                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Feedback::STATUSES[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'new' => 'danger',
                        'in_progress' => 'warning',
                        'resolved' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('page_url')
                    ->label('Sayfa')
                    ->limit(30)
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tip')
                    ->options(Feedback::TYPES),
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options(Feedback::STATUSES)
                    ->default('new'),
                TernaryFilter::make('has_email')
                    ->label('E-posta var')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('email'),
                        false: fn ($query) => $query->whereNull('email'),
                    ),
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
