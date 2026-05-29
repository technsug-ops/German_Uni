<?php

namespace App\Filament\Resources\JobPostings\Tables;

use App\Models\JobPosting;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class JobPostingsTable
{
    public static function configure(Table $table): Table
    {
        $positionOptions = [];
        foreach (JobPosting::POSITION_TYPES as $key => $meta) {
            $positionOptions[$key] = $meta['icon'] . ' ' . $meta['label_tr'];
        }

        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->limit(60)
                    ->wrap()
                    ->weight('bold'),
                TextColumn::make('position_type')
                    ->label('Pozisyon')
                    ->formatStateUsing(fn ($state) => (JobPosting::POSITION_TYPES[$state]['icon'] ?? '') . ' ' . (JobPosting::POSITION_TYPES[$state]['label_tr'] ?? $state))
                    ->badge(),
                TextColumn::make('university.name_de')->label('Üniversite')->limit(30)->wrap()->toggleable(),
                TextColumn::make('city.name_de')->label('Şehir')->toggleable(),
                TextColumn::make('language')
                    ->label('Dil')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ['en' => '🇬🇧', 'de' => '🇩🇪', 'both' => '🌍'][$state] ?? $state),
                TextColumn::make('deadline_at')
                    ->label('Son Başvuru')
                    ->date()
                    ->sortable()
                    ->color(fn ($state) => $state && $state->isPast() ? 'danger' : ($state && $state->lte(now()->addDays(14)) ? 'warning' : 'success')),
                IconColumn::make('is_featured')->label('Öne')->boolean()->toggleable(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->defaultSort('posted_at', 'desc')
            ->filters([
                SelectFilter::make('position_type')->label('Pozisyon Tipi')->options($positionOptions),
                SelectFilter::make('language')->label('Dil')->options(['en' => 'EN', 'de' => 'DE', 'both' => 'Both']),
                TernaryFilter::make('is_active')->label('Aktif'),
                TernaryFilter::make('is_featured')->label('Öne çıkar'),
                Filter::make('expired')
                    ->label('Süresi geçmiş')
                    ->query(fn ($q) => $q->whereNotNull('deadline_at')->where('deadline_at', '<', now())),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
