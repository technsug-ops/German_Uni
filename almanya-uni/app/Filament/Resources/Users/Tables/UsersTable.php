<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label('')
                    ->circular()
                    ->disk('public')
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name ?? '?') . '&background=1E40AF&color=fff&size=80')
                    ->size(40),

                TextColumn::make('name')
                    ->label('Ad')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn ($record) => $record->role_label),

                TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                IconColumn::make('is_admin')
                    ->label('Admin')
                    ->boolean(),

                IconColumn::make('is_editor')
                    ->label('Editör')
                    ->boolean(),

                IconColumn::make('is_author')
                    ->label('Yazar')
                    ->boolean(),

                IconColumn::make('is_contributor')
                    ->label('🌱 Katkıcı')
                    ->boolean(),

                TextColumn::make('contributions_count')
                    ->label('Katkı')
                    ->counts(['contributions' => fn ($q) => $q->where('status', 'approved')])
                    ->badge()
                    ->color('success')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Kayıt')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_admin')->label('Admin'),
                TernaryFilter::make('is_editor')->label('Editör'),
                TernaryFilter::make('is_author')->label('Yazar'),
                TernaryFilter::make('is_contributor')->label('🌱 Topluluk Katkıcısı'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
