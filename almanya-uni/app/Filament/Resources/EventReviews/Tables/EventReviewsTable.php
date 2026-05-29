<?php

namespace App\Filament\Resources\EventReviews\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class EventReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => 'spam',
                        'gray'    => 'rejected',
                    ])
                    ->sortable(),
                TextColumn::make('rating')
                    ->label('★')
                    ->formatStateUsing(fn ($state) => str_repeat('★', $state) . str_repeat('☆', 5 - $state))
                    ->sortable(),
                TextColumn::make('display_name')
                    ->label('Yorumcu')
                    ->searchable(['attendee_name', 'attendee_email']),
                TextColumn::make('body')
                    ->label('Yorum')
                    ->limit(80)
                    ->wrap()
                    ->searchable(),
                TextColumn::make('event.title_tr')
                    ->label('Etkinlik')
                    ->limit(35)
                    ->searchable()
                    ->url(fn ($record) => $record->event ? route('events.show', $record->event->slug) : null, true),
                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending'  => '⏳ Beklemede',
                        'approved' => '✅ Onaylı',
                        'spam'     => '🚫 Spam',
                        'rejected' => '❌ Reddedildi',
                    ])
                    ->default('pending'),
                SelectFilter::make('rating')
                    ->options([1 => '★', 2 => '★★', 3 => '★★★', 4 => '★★★★', 5 => '★★★★★'])
                    ->label('Puan'),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Onayla')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'approved')
                    ->action(fn ($record) => $record->update([
                        'status'      => 'approved',
                        'approved_at' => now(),
                        'approved_by' => auth()->id(),
                    ])),
                Action::make('spam')
                    ->label('Spam')
                    ->icon('heroicon-o-shield-exclamation')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status !== 'spam')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'spam'])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approveAll')
                        ->label('Onayla')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each(fn ($r) => $r->update([
                            'status'      => 'approved',
                            'approved_at' => now(),
                            'approved_by' => auth()->id(),
                        ]))),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
