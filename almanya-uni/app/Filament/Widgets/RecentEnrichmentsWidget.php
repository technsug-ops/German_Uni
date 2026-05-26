<?php

namespace App\Filament\Widgets;

use App\Models\City;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentEnrichmentsWidget extends TableWidget
{
    protected static ?string $heading = 'Son Enrich Edilen Şehirler';

    protected static ?int $sort = 20;

    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = true;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                City::query()
                    ->whereNotNull('content_blocks')
                    ->with('state:id,name_de')
                    ->select([
                        'id', 'slug', 'name_de', 'name_tr', 'image_url',
                        'state_id', 'last_enriched_at',
                        // content_blocks YOK — JSON_LENGTH virtual column
                    ])
                    ->selectRaw('JSON_LENGTH(content_blocks) as blocks_count')
            )
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('')
                    ->square()
                    ->size(40),

                Tables\Columns\TextColumn::make('name_de')
                    ->label('Şehir')
                    ->weight('semibold')
                    ->searchable()
                    ->url(fn (City $r) => "/cities/{$r->slug}")
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('state.name_de')
                    ->label('Eyalet')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('blocks_count')
                    ->label('Bloklar')
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
            ->defaultPaginationPageOption(5)
            ->deferLoading();
    }
}
