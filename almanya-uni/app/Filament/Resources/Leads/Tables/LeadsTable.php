<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Models\Lead;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Tarih')->dateTime('d.m.Y H:i')->sortable(),
                TextColumn::make('source_type')->label('Tür')->badge()
                    ->formatStateUsing(fn ($state) => Lead::SOURCES[$state] ?? $state)
                    ->color(fn ($state) => $state === 'language_course' ? 'info' : 'warning'),
                TextColumn::make('source_name')->label('Firma')->searchable(),
                TextColumn::make('name')->label('Ad')->searchable(),
                TextColumn::make('email')->label('E-posta')->searchable()->copyable(),
                TextColumn::make('phone')->label('Telefon')->copyable()->toggleable(),
                TextColumn::make('message')->label('Mesaj')->limit(40)->wrap()->tooltip(fn (Lead $r) => $r->message),
                TextColumn::make('status')->label('Durum')->badge()
                    ->formatStateUsing(fn ($state) => Lead::STATUSES[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'new' => 'warning', 'contacted' => 'info', 'converted' => 'success', default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('source_type')->label('Tür')->options(Lead::SOURCES),
                SelectFilter::make('status')->label('Durum')->options(Lead::STATUSES),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
