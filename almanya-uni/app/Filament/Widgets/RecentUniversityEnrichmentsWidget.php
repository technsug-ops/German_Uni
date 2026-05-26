<?php

namespace App\Filament\Widgets;

use App\Models\University;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentUniversityEnrichmentsWidget extends TableWidget
{
    protected static ?string $heading = 'Son Enrich Edilen Üniversiteler';
    protected static ?int $sort = 21;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                University::query()
                    ->where('is_active', 1)
                    ->whereNotNull('content_blocks')
                    ->with('city:id,name_de,slug')
            )
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('')
                    ->square()
                    ->size(40),

                Tables\Columns\TextColumn::make('name_de')
                    ->label('Üniversite')
                    ->weight('semibold')
                    ->searchable()
                    ->limit(50)
                    ->url(fn (University $r) => "/universities/{$r->slug}")
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('city.name_de')
                    ->label('Şehir')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tür')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'public' => 'success',
                        'private' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'public' => 'Devlet',
                        'private' => 'Özel',
                        'applied_sciences' => 'HAW',
                        'art' => 'Sanat',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('blocks_count')
                    ->label('Bloklar')
                    ->getStateUsing(fn (University $r) => count($r->content_blocks ?? []))
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('last_enriched_at')
                    ->label('Son üretim')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->since(),
            ])
            ->defaultSort('last_enriched_at', 'desc')
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(10);
    }
}
