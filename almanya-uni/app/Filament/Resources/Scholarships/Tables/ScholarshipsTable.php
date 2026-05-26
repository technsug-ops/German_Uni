<?php

namespace App\Filament\Resources\Scholarships\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ScholarshipsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('sap_objid')
                    ->label('SAP ObjID')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('name_en')
                    ->label('Name (EN)')
                    ->searchable()
                    ->limit(60)
                    ->wrap()
                    ->weight('semibold'),

                TextColumn::make('programmname_en')
                    ->label('Programmname')
                    ->limit(40)
                    ->toggleable()
                    ->color('gray'),

                IconColumn::make('is_daad')
                    ->label('DAAD')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->trueColor('info')
                    ->falseIcon('heroicon-o-x-mark')
                    ->falseColor('gray'),

                TextColumn::make('origins_count')
                    ->label('Ülke')
                    ->counts('origins')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('subjects_count')
                    ->label('Konu')
                    ->counts('subjects')
                    ->numeric()
                    ->toggleable(),

                TextColumn::make('statuses_count')
                    ->label('Hedef grup')
                    ->counts('statuses')
                    ->numeric()
                    ->toggleable(),

                TextColumn::make('last_seen_at')
                    ->label('Son sync')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('removed_at')
                    ->label('Kaldırıldı')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->placeholder('Aktif')
                    ->color(fn ($state) => $state ? 'danger' : 'success'),
            ])
            ->filters([
                TernaryFilter::make('is_daad')
                    ->label('DAAD bursu')
                    ->placeholder('Tümü')
                    ->trueLabel('Sadece DAAD')
                    ->falseLabel('Sadece partner'),

                Filter::make('active')
                    ->label('Sadece aktif')
                    ->query(fn (Builder $q) => $q->whereNull('removed_at'))
                    ->default(),

                Filter::make('removed')
                    ->label('Sadece kaldırılanlar')
                    ->query(fn (Builder $q) => $q->whereNotNull('removed_at')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
