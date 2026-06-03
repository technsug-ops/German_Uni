<?php

namespace App\Filament\Resources\TranslationOffices\Tables;

use App\Models\TranslationOffice;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TranslationOfficesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('logo_url')->label('Logo')->square()->defaultImageUrl(fn () => null),
                TextColumn::make('name')->label('Ad')->searchable()->sortable()->weight('bold'),
                TextColumn::make('type')->label('Tip')->badge()
                    ->formatStateUsing(fn ($state) => (TranslationOffice::TYPES[$state]['emoji'] ?? '') . ' ' . (TranslationOffice::TYPES[$state]['label'] ?? $state))
                    ->color(fn ($state) => TranslationOffice::TYPES[$state]['color'] ?? 'gray'),
                IconColumn::make('is_sworn')->label('Yeminli')->boolean()->toggleable(),
                TextColumn::make('languages')->label('Diller')->badge()->limitList(3)->toggleable(),
                TextColumn::make('cities')->label('Şehirler')->badge()->limitList(3)->toggleable(),
                TextColumn::make('click_count')->label('Tık')->numeric()->sortable()->toggleable(),
                ToggleColumn::make('is_featured')->label('Öne çıkan')->toggleable(),
                ToggleColumn::make('is_active')->label('Aktif'),
            ])
            ->filters([
                SelectFilter::make('type')->label('Tip')
                    ->options(collect(TranslationOffice::TYPES)->mapWithKeys(fn ($m, $k) => [$k => $m['label']])->all()),
                TernaryFilter::make('is_sworn')->label('Yeminli'),
                TernaryFilter::make('is_active')->label('Aktif'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
