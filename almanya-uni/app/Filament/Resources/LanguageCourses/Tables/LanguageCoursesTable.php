<?php

namespace App\Filament\Resources\LanguageCourses\Tables;

use App\Models\LanguageCourse;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class LanguageCoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('logo_url')->label('Logo')->square()->defaultImageUrl(fn () => null),
                TextColumn::make('name')->label('Ad')->searchable()->sortable()->weight('bold'),
                TextColumn::make('type')->label('Kategori')->badge()
                    ->formatStateUsing(fn ($state) => (LanguageCourse::TYPES[$state]['emoji'] ?? '') . ' ' . (LanguageCourse::TYPES[$state]['label'] ?? $state))
                    ->color(fn ($state) => LanguageCourse::TYPES[$state]['color'] ?? 'gray'),
                TextColumn::make('cities')->label('Şehirler')->badge()->limitList(3)->toggleable(),
                TextColumn::make('levels')->label('Seviye')->badge()->limitList(6)->toggleable(),
                TextColumn::make('click_count')->label('Tık')->numeric()->sortable()->toggleable(),
                ToggleColumn::make('is_featured')->label('Öne çıkan')->toggleable(),
                ToggleColumn::make('is_active')->label('Aktif'),
                TextColumn::make('sort_order')->label('Sıra')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')->label('Kategori')
                    ->options(collect(LanguageCourse::TYPES)->mapWithKeys(fn ($m, $k) => [$k => $m['label']])->all()),
                TernaryFilter::make('is_active')->label('Aktif'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
