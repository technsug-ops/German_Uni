<?php

namespace App\Filament\Resources\Posts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('featured_image')
                    ->label('')
                    ->height(40)
                    ->width(64),

                TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->limit(40)
                    ->weight('bold')
                    ->description(fn ($record) => $record->category?->name),

                TextColumn::make('author.name')
                    ->label('Yazar')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                // Kategori — içine girmeden gör + inline değiştir (kaydetme otomatik)
                SelectColumn::make('category_id')
                    ->label('Kategori')
                    ->options(fn () => \App\Models\Category::query()->orderBy('name')->pluck('name', 'id')->toArray())
                    ->selectablePlaceholder(false)
                    ->rules(['required']),

                // ── İSTATİSTİKLER ──
                TextColumn::make('view_count')
                    ->label('👁 Görüntüleme')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('avg_scroll')
                    ->label('📖 Okunma %')
                    ->state(fn ($record) => '%' . $record->avg_scroll)
                    ->badge()
                    ->color(fn ($record) => $record->avg_scroll >= 70 ? 'success' : ($record->avg_scroll >= 40 ? 'warning' : 'danger'))
                    ->tooltip('Ortalama scroll derinliği — yazının ne kadarı okunuyor'),

                TextColumn::make('avg_seconds')
                    ->label('⏱ Süre')
                    ->state(fn ($record) => $record->avg_seconds >= 60
                        ? floor($record->avg_seconds / 60) . 'dk ' . ($record->avg_seconds % 60) . 'sn'
                        : $record->avg_seconds . 'sn')
                    ->tooltip('Ortalama sayfada kalış süresi'),

                TextColumn::make('completion_rate')
                    ->label('✅ Tamamlama')
                    ->state(fn ($record) => '%' . $record->completion_rate)
                    ->badge()
                    ->color(fn ($record) => $record->completion_rate >= 50 ? 'success' : 'gray')
                    ->tooltip('Yazıyı sonuna kadar okuyanların oranı'),

                TextColumn::make('feedback')
                    ->label('👍 / 👎')
                    ->state(fn ($record) => "👍 {$record->helpful_count}  👎 {$record->unhelpful_count}")
                    ->toggleable(),

                TextColumn::make('published_at')
                    ->label('Yayın')
                    ->date('d.m.Y')
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_published')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name'),
                SelectFilter::make('user_id')
                    ->label('Yazar')
                    ->relationship('author', 'name'),
                TernaryFilter::make('is_published')->label('Yayında')->default(true),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('view_count', 'desc');
    }
}
