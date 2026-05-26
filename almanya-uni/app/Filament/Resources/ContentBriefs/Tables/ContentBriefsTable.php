<?php

namespace App\Filament\Resources\ContentBriefs\Tables;

use App\Models\ContentBrief;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContentBriefsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->limit(45)->wrap(),
                TextColumn::make('topic')->badge()->color('info'),
                TextColumn::make('audience')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => ContentBrief::AUDIENCES[$state] ?? $state),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'draft' => 'gray',
                        'in_progress' => 'warning',
                        'ready' => 'info',
                        'published' => 'success',
                        'archived' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state) => ContentBrief::STATUSES[$state] ?? $state),
                TextColumn::make('assets_count')
                    ->label('Asset')
                    ->counts('assets')
                    ->badge()
                    ->color('success'),
                TextColumn::make('target_word_count')->label('Kelime')->numeric(),
                TextColumn::make('created_at')->dateTime('d.m H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('audience')->options(ContentBrief::AUDIENCES),
                SelectFilter::make('status')->options(ContentBrief::STATUSES),
                SelectFilter::make('topic')->options([
                    'vize' => 'Vize', 'dil' => 'Dil', 'para' => 'Para', 'randevu' => 'Randevu',
                    'uni_assist' => 'Uni-Assist', 'yurt' => 'Yurt', 'sehir' => 'Şehir',
                    'master' => 'Master', 'sigorta' => 'Sigorta', 'studienkolleg' => 'Studienkolleg',
                    'denklik' => 'Denklik', 'is' => 'İş', 'anmeldung' => 'Anmeldung', 'burs' => 'Burs',
                ]),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
