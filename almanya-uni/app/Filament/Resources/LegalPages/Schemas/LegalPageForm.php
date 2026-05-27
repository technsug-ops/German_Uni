<?php

namespace App\Filament\Resources\LegalPages\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class LegalPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Anahtar ve Yayım')
                    ->schema([
                        Select::make('key')
                            ->label('Sayfa türü (key)')
                            ->options([
                                'privacy' => 'Gizlilik / Privacy / Datenschutz',
                                'terms' => 'Koşullar / Terms / Nutzungsbedingungen',
                                'cookies' => 'Çerez / Cookies',
                                'impressum' => 'Künye / Imprint / Impressum',
                                'disclaimer' => 'Yasal Uyarı / Disclaimer / Haftungsausschluss',
                            ])
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabled(fn ($record) => filled($record))
                            ->helperText('Bir kez seçildikten sonra değiştirilemez. Her sayfa türü için tek kayıt olmalıdır.'),
                        Toggle::make('is_published')
                            ->label('Yayında')
                            ->default(true)
                            ->inline(false),
                        DatePicker::make('effective_date')
                            ->label('Yürürlük tarihi')
                            ->helperText('Sayfa başlığında "Son güncelleme" olarak görünür.')
                            ->default(now()),
                    ])
                    ->columns(3),

                Tabs::make('Diller')
                    ->tabs([
                        Tab::make('🇹🇷 Türkçe')
                            ->schema([
                                TextInput::make('titles.tr')
                                    ->label('Başlık')
                                    ->required()
                                    ->maxLength(150),
                                Textarea::make('descriptions.tr')
                                    ->label('Meta Description (SEO)')
                                    ->rows(2)
                                    ->maxLength(300)
                                    ->helperText('Arama motorlarında görünecek özet (≤300 karakter).'),
                                Textarea::make('bodies.tr')
                                    ->label('İçerik (Markdown)')
                                    ->rows(25)
                                    ->required()
                                    ->helperText('Markdown formatında. Başlık: ## H2, ### H3. Liste: - / 1. Vurgular: **kalın**, *italik*. Bağlantı: [metin](url). Tablo Markdown standardı.')
                                    ->extraInputAttributes(['style' => 'font-family: ui-monospace, monospace; font-size: 13px;']),
                            ]),
                        Tab::make('🇬🇧 English')
                            ->schema([
                                TextInput::make('titles.en')
                                    ->label('Title')
                                    ->required()
                                    ->maxLength(150),
                                Textarea::make('descriptions.en')
                                    ->label('Meta Description')
                                    ->rows(2)
                                    ->maxLength(300),
                                Textarea::make('bodies.en')
                                    ->label('Body (Markdown)')
                                    ->rows(25)
                                    ->required()
                                    ->extraInputAttributes(['style' => 'font-family: ui-monospace, monospace; font-size: 13px;']),
                            ]),
                        Tab::make('🇩🇪 Deutsch')
                            ->schema([
                                TextInput::make('titles.de')
                                    ->label('Titel')
                                    ->required()
                                    ->maxLength(150),
                                Textarea::make('descriptions.de')
                                    ->label('Meta-Beschreibung')
                                    ->rows(2)
                                    ->maxLength(300),
                                Textarea::make('bodies.de')
                                    ->label('Inhalt (Markdown)')
                                    ->rows(25)
                                    ->required()
                                    ->extraInputAttributes(['style' => 'font-family: ui-monospace, monospace; font-size: 13px;']),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
