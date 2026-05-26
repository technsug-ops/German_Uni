<?php

namespace App\Filament\Resources\BlockedAccountProviders\Tables;

use App\Models\BlockedAccountProvider;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BlockedAccountProvidersTable
{
    public static function configure(Table $table): Table
    {
        $typeOptions = [];
        foreach (BlockedAccountProvider::TYPES as $key => $meta) {
            $typeOptions[$key] = $meta['emoji'] . ' ' . $meta['label'];
        }

        return $table
            ->columns([
                TextColumn::make('name')->label('Marka')->searchable()->sortable()->weight('bold'),

                TextColumn::make('type')->label('Tip')->badge()
                    ->formatStateUsing(fn (?string $state) => BlockedAccountProvider::TYPES[$state]['emoji'] . ' ' . (BlockedAccountProvider::TYPES[$state]['label'] ?? $state))
                    ->color(fn (?string $state) => match ($state) {
                        'fintech'          => 'info',
                        'traditional_bank' => 'warning',
                        default            => 'gray',
                    }),

                TextColumn::make('setup_fee_eur')->label('Açılış')
                    ->formatStateUsing(fn ($state) => $state ? '€' . number_format((float)$state, 2) : '—')
                    ->sortable(),

                TextColumn::make('monthly_fee_eur')->label('Aylık')
                    ->formatStateUsing(fn ($state) => $state ? '€' . number_format((float)$state, 2) : '—')
                    ->sortable(),

                TextColumn::make('first_year_cost_eur')->label('1. yıl toplam')
                    ->formatStateUsing(fn ($state) => $state ? '€' . number_format((float)$state, 2) : '—')
                    ->sortable(false),

                TextColumn::make('activation_range')->label('Aktivasyon')->badge()->color('gray'),

                IconColumn::make('combo_insurance')->label('Sigorta')->boolean(),
                IconColumn::make('has_mobile_app')->label('App')->boolean()->toggleable(),
                IconColumn::make('bafin_licensed')->label('BaFin')->boolean()->toggleable(),

                IconColumn::make('is_published')->label('Yayın')->boolean(),
                IconColumn::make('is_featured')->label('Öne')->boolean()->toggleable(),

                TextColumn::make('last_verified_at')->label('Son doğr.')->date('Y-m-d')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('sort_order')->label('Sıra')->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')->options($typeOptions),
                TernaryFilter::make('is_published')->label('Yayında'),
                TernaryFilter::make('combo_insurance')->label('Sigorta combo'),
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
