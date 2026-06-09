<?php

namespace App\Filament\Resources\HealthInsuranceProviders\Tables;

use App\Models\HealthInsuranceProvider;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class HealthInsuranceProvidersTable
{
    public static function configure(Table $table): Table
    {
        $typeOptions = [];
        foreach (HealthInsuranceProvider::TYPES as $key => $meta) {
            $typeOptions[$key] = $meta['emoji'] . ' ' . $meta['label'];
        }

        return $table
            ->columns([
                TextColumn::make('name')->label('Marka')->searchable()->sortable()->weight('bold'),

                TextColumn::make('type')->label('Tip')->badge()
                    ->formatStateUsing(fn (?string $state) => (HealthInsuranceProvider::TYPES[$state]['emoji'] ?? '') . ' ' . (HealthInsuranceProvider::TYPES[$state]['label'] ?? $state))
                    ->color(fn (?string $state) => match ($state) {
                        'public'  => 'success',
                        'private' => 'info',
                        'expat'   => 'warning',
                        default   => 'gray',
                    }),

                TextColumn::make('monthly_range')->label('Aylık')->sortable(false),

                IconColumn::make('accepted_for_visa')->label('Vize')->boolean(),
                IconColumn::make('accepted_for_enrollment')->label('Kayıt')->boolean(),
                IconColumn::make('covers_dental')->label('Diş')->boolean()->toggleable(),
                IconColumn::make('english_support')->label('EN')->boolean()->toggleable(),

                TextColumn::make('age_limit')->label('Yaş lim.')->numeric()->toggleable(),

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
                TernaryFilter::make('accepted_for_enrollment')->label('Kayıt için geçerli'),
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
