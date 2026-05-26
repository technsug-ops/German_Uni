<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Ad')
                    ->formatStateUsing(fn ($state, $record) => $record->parent_id ? '↳ ' . $state : $state)
                    ->weight(fn ($record) => $record->parent_id ? null : 'bold')
                    ->searchable(),
                TextColumn::make('parent.name')
                    ->label('Üst Kategori')
                    ->badge()
                    ->color('gray')
                    ->placeholder('— ana —'),
                TextColumn::make('slug')->searchable()->toggleable(),
                TextColumn::make('posts_count')
                    ->label('Yazı')
                    ->counts('posts')
                    ->badge(),
                TextColumn::make('color')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                TernaryFilter::make('parent_id')
                    ->label('Tip')
                    ->placeholder('Hepsi')
                    ->trueLabel('Sadece alt kategoriler')
                    ->falseLabel('Sadece ana kategoriler')
                    ->queries(
                        true: fn ($q) => $q->whereNotNull('parent_id'),
                        false: fn ($q) => $q->whereNull('parent_id'),
                        blank: fn ($q) => $q,
                    ),
                TernaryFilter::make('is_active')->label('Aktif')->default(true),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }
}
