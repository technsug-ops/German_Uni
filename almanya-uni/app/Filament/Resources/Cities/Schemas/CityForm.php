<?php

namespace App\Filament\Resources\Cities\Schemas;

use App\Filament\Support\ContentBlocksBuilder;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
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

                Section::make('Medya (foto & video)')
                    ->description('Yüklediğin İLK foto detay sayfasının hero arka planı olur; tümü galeri olarak görünür. Boşsa hero temiz gradient gösterir.')
                    ->collapsible()
                    ->components([
                        FileUpload::make('gallery_images')
                            ->label('Şehir fotoğrafları (yükle)')
                            ->multiple()
                            ->image()
                            ->reorderable()
                            ->disk('public')
                            ->directory('cities')
                            ->visibility('public')
                            ->maxFiles(12)
                            ->maxSize(4096)
                            ->columnSpanFull()
                            ->helperText('Birden çok görsel · sürükle-bırak sırala · İLK = hero · BOŞ → gradient hero'),
                        Repeater::make('gallery_image_urls')
                            ->label('Resim linkleri (dış URL)')
                            ->simple(
                                TextInput::make('url')
                                    ->label('Resim URL')
                                    ->url()
                                    ->placeholder('https://…/foto.jpg')
                            )
                            ->reorderable()
                            ->addActionLabel('Resim linki ekle')
                            ->columnSpanFull()
                            ->helperText('Yüklemek yerine dış resim adresi yapıştır · yüklenenlerden SONRA galeriye eklenir'),
                        TextInput::make('video_url')
                            ->label('Tanıtım videosu (YouTube URL)')
                            ->url()
                            ->columnSpanFull()
                            ->helperText('Opsiyonel · youtube.com/watch?v=… veya youtu.be/… · şehir sayfasında gömülü oynatılır'),
                    ]),

                Section::make('Görsel (eski) & Lokasyon')
                    ->columns(3)
                    ->collapsible()
                    ->collapsed()
                    ->components([
                        TextInput::make('image_url')
                            ->label('Arma / kapak URL (eski alan)')
                            ->url()
                            ->columnSpanFull()
                            ->helperText('Galeri fotoğrafı yoksa kullanılan eski URL alanı (genelde şehir arması). Yeni: yukarıdan foto yükle.'),
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
