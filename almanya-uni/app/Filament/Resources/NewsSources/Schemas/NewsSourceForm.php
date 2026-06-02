<?php

namespace App\Filament\Resources\NewsSources\Schemas;

use App\Models\Category;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NewsSourceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Kaynak')->schema([
                TextInput::make('name')
                    ->label('Kaynak adı')
                    ->required()
                    ->maxLength(120)
                    ->helperText('Adaylarda "kaynak" olarak görünür. Örn. "Google News · Vize".'),

                TextInput::make('url')
                    ->label('RSS / Atom feed URL')
                    ->required()
                    ->url()
                    ->maxLength(600)
                    ->columnSpanFull()
                    ->helperText('RSS veya Atom besleme adresi. Resmi/güvenilir kaynak olmalı (YMYL).'),

                Select::make('default_category')
                    ->label('Varsayılan kategori')
                    ->options(Category::where('kind', 'news')->orderBy('sort_order')->pluck('name_tr', 'slug'))
                    ->searchable()
                    ->helperText('Bu kaynaktan gelen adaylara önerilen kategori. Boşsa "Pratik & Takvim".'),

                TagsInput::make('keywords')
                    ->label('Anahtar kelime filtresi')
                    ->placeholder('Germany, DAAD, visa…')
                    ->helperText('Sadece bu kelimelerden birini içeren haberler alınır. BOŞ bırakırsan kaynaktaki TÜM haberler gelir (Google News gibi geniş kaynaklarda filtre önerilir).')
                    ->columnSpanFull(),
            ])->columns(2),

            Section::make('Ayarlar')->schema([
                Toggle::make('enabled')
                    ->label('Aktif')
                    ->default(true)
                    ->helperText('Kapalıyken otomatik çekimde atlanır.'),

                TextInput::make('max_per_source')
                    ->label('Bu kaynaktan maks. aday / çekim')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(50)
                    ->placeholder('Global varsayılan (6)')
                    ->helperText('Boş bırak → global varsayılan kullanılır.'),

                TextInput::make('sort_order')
                    ->label('Sıra')
                    ->numeric()
                    ->default(0)
                    ->helperText('Küçük önce çekilir.'),
            ])->columns(3),
        ]);
    }
}
