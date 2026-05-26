<?php

namespace App\Filament\Resources\HousingProviders\Tables;

use App\Models\HousingProvider;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class HousingProvidersTable
{
    public static function configure(Table $table): Table
    {
        $typeOptions = [];
        foreach (HousingProvider::TYPES as $key => $meta) {
            $typeOptions[$key] = $meta['emoji'] . ' ' . $meta['label'];
        }

        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Tip')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => HousingProvider::TYPES[$state]['emoji'] . ' ' . (HousingProvider::TYPES[$state]['label'] ?? $state))
                    ->color(fn (?string $state) => match ($state) {
                        'studierendenwerk' => 'success',
                        'private_chain'    => 'info',
                        'platform'         => 'warning',
                        default            => 'gray',
                    }),

                TextColumn::make('name')->label('Ad')->searchable()->sortable()->weight('bold'),

                TextColumn::make('cities')
                    ->label('Şehir(ler)')
                    ->formatStateUsing(fn ($state) => is_array($state) ? (count($state) > 3 ? implode(', ', array_slice($state, 0, 3)).' +'.(count($state)-3) : implode(', ', $state)) : '')
                    ->limit(40)
                    ->wrap(),

                TextColumn::make('price_min')->label('Fiyat')
                    ->formatStateUsing(fn ($state, $record) => $record->price_range)
                    ->sortable(),

                TextColumn::make('total_capacity')->label('Kapasite')->numeric()->sortable()
                    ->toggleable(),

                TextColumn::make('waiting_period')->label('Bekleme')->badge()->color('gray')
                    ->toggleable(),

                IconColumn::make('is_featured')->label('Öne')->boolean(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),

                TextColumn::make('sort_order')->label('Sıra')->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')->options($typeOptions),
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
            ->defaultSort('sort_order', 'asc');
    }
}
