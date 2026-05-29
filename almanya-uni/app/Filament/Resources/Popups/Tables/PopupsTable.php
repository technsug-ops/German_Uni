<?php

namespace App\Filament\Resources\Popups\Tables;

use App\Models\Popup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PopupsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')->label('Anahtar')->searchable()->copyable(),
                TextColumn::make('title_tr')->label('Başlık (TR)')->limit(40)->wrap(),
                TextColumn::make('theme')
                    ->label('Tema')
                    ->formatStateUsing(fn ($state) => Popup::THEMES[$state] ?? $state)
                    ->badge(),
                TextColumn::make('trigger')
                    ->label('Tetik')
                    ->formatStateUsing(fn ($state) => Popup::TRIGGERS[$state] ?? $state)
                    ->badge(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
                TextColumn::make('view_count')->label('👁️')->sortable(),
                TextColumn::make('click_count')->label('🖱️')->sortable(),
                TextColumn::make('dismiss_count')->label('✕')->sortable()->toggleable(),
                TextColumn::make('starts_at')->date()->toggleable()->label('Başlangıç'),
                TextColumn::make('ends_at')->date()->toggleable()->label('Bitiş'),
            ])
            ->defaultSort('priority', 'asc')
            ->filters([
                SelectFilter::make('theme')->options(Popup::THEMES),
                SelectFilter::make('trigger')->options(Popup::TRIGGERS),
                TernaryFilter::make('is_active')->label('Aktif'),
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
