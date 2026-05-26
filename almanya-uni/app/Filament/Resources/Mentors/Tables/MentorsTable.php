<?php

namespace App\Filament\Resources\Mentors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class MentorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=?&background=10b981&color=fff'),

                TextColumn::make('name')
                    ->label('Ad Soyad')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('headline')
                    ->label('Tanıtım')
                    ->limit(40)
                    ->wrap()
                    ->toggleable(),

                TextColumn::make('current_company')
                    ->label('Şirket')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('city')
                    ->label('Şehir')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('topics')
                    ->label('Konular')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', array_slice($state, 0, 3)) : '')
                    ->limit(40)
                    ->toggleable(),

                TextColumn::make('rate_eur')
                    ->label('Ücret')
                    ->formatStateUsing(fn ($state) => ((float) $state) === 0.0 ? '🎁 Ücretsiz' : number_format($state, 0, ',', '.') . ' €')
                    ->sortable(),

                TextColumn::make('sessions_count')
                    ->label('Seans')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('rating_avg')
                    ->label('Puan')
                    ->formatStateUsing(fn ($state, $record) => $state ? '⭐ ' . number_format($state, 1) . " ($record->rating_count)" : '—')
                    ->toggleable(),

                IconColumn::make('is_featured')->label('Öne Çıkan')->boolean(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),

                TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_featured')->label('Öne Çıkan'),
                TernaryFilter::make('is_active')->label('Aktif')->default(true),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('is_featured', 'desc');
    }
}
