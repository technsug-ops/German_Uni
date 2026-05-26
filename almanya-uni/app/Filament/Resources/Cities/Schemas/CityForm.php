<?php

namespace App\Filament\Resources\Cities\Schemas;

use App\Filament\Support\ContentBlocksBuilder;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Temel Bilgiler')
                    ->columns(2)
                    ->components([
                        TextInput::make('name_de')->label('Ad (Almanca)')->required(),
                        TextInput::make('name_tr')->label('Ad (Türkçe)')->required(),
                        TextInput::make('name_en')->label('Ad (İngilizce)'),
                        TextInput::make('slug')->label('Slug')->required(),
                        Select::make('state_id')
                            ->label('Eyalet')
                            ->relationship('state', 'name_de')
                            ->searchable()
                            ->preload(),
                        TextInput::make('wikidata_id')->label('Wikidata ID'),
                        TextInput::make('population')->label('Nüfus')->numeric(),
                        Toggle::make('is_active')->label('Aktif')->default(true),
                    ]),

                Section::make('Görsel & Lokasyon')
                    ->columns(3)
                    ->collapsible()
                    ->components([
                        TextInput::make('image_url')
                            ->label('Kapak görseli URL')
                            ->url()
                            ->columnSpanFull()
                            ->helperText('Şehir liste sayfasındaki kart görseli ve detay hero arka planı'),
                        TextInput::make('latitude')->label('Enlem')->numeric()->step(0.0000001),
                        TextInput::make('longitude')->label('Boylam')->numeric()->step(0.0000001),
                        DateTimePicker::make('last_enriched_at')->label('Son enrich')->disabled(),
                    ]),

                Section::make('İçerik Blokları (AI üretimi + manuel düzen)')
                    ->description('Bloklar drag-drop ile sıralanabilir, eklenebilir, silinebilir. "🪄 Sayfa Üret" butonu mevcudu yeniden üretir.')
                    ->collapsible()
                    ->collapsed()
                    ->components([
                        ContentBlocksBuilder::make('content_blocks'),
                    ]),
            ]);
    }
}
