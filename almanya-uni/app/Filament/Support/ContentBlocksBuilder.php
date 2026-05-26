<?php

namespace App\Filament\Support;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;

/**
 * Şehir/üniversite content_blocks JSON'unu Filament Builder ile düzenleme.
 *
 * Bizim format: [{type:"hero", image_url:"...", alt:"..."}, ...] (flat)
 * Builder format: [{type:"hero", data:{image_url:"...", alt:"..."}}, ...] (nested)
 *
 * formatStateUsing → load: flat'i nested'e çevirir
 * mutateDehydratedStateUsing → save: nested'i flat'e çevirir
 */
class ContentBlocksBuilder
{
    public static function make(string $field = 'content_blocks'): Builder
    {
        return Builder::make($field)
            ->label('İçerik Blokları')
            ->helperText('AI ile üretilen blokları elle düzenleyebilir, sıralayabilir, silebilirsin. Yeni blok eklemek için en alta tıkla.')
            ->blocks(self::blocks())
            ->collapsible()
            ->collapsed()
            ->cloneable()
            ->blockNumbers(false)
            ->addActionLabel('+ Yeni Blok Ekle')
            ->columnSpanFull()
            ->formatStateUsing(fn ($state) => self::flatToNested(is_array($state) ? $state : []))
            ->mutateDehydratedStateUsing(fn ($state) => self::nestedToFlat(is_array($state) ? $state : []));
    }

    /**
     * Veritabanındaki flat formattan ({type, ...flat fields}) Filament Builder'ın
     * beklediği nested formata ({type, data: {...flat fields}}) çevir.
     */
    private static function flatToNested(array $blocks): array
    {
        return array_map(function ($b) {
            if (!is_array($b) || empty($b['type'])) return $b;
            $type = $b['type'];
            $data = $b;
            unset($data['type']);
            return ['type' => $type, 'data' => $data];
        }, $blocks);
    }

    /**
     * Builder'dan gelen nested format'ı veritabanı flat'ine çevir.
     */
    private static function nestedToFlat(array $blocks): array
    {
        return array_map(function ($b) {
            if (!is_array($b) || empty($b['type'])) return $b;
            $type = $b['type'];
            $data = is_array($b['data'] ?? null) ? $b['data'] : [];
            return ['type' => $type] + $data;
        }, $blocks);
    }

    /**
     * 17 blok tipi için Builder\Block tanımları.
     */
    private static function blocks(): array
    {
        return [
            Builder\Block::make('hero')
                ->label('🖼️ Hero (Kapak Görseli)')
                ->schema([
                    TextInput::make('image_url')->label('Görsel URL')->url()->required()->columnSpanFull(),
                    TextInput::make('alt')->label('Alt metin'),
                ]),

            Builder\Block::make('intro')
                ->label('📝 Giriş Paragrafı')
                ->schema([
                    Textarea::make('body_md')->label('Markdown')->rows(6)->required()->columnSpanFull(),
                ]),

            Builder\Block::make('section')
                ->label('📄 Bölüm (H2 + İçerik)')
                ->schema([
                    TextInput::make('h')->label('Başlık (H2)')->required(),
                    Textarea::make('body_md')->label('Markdown')->rows(8)->required()->columnSpanFull(),
                ]),

            Builder\Block::make('quick_facts')
                ->label('⚡ Hızlı Bakış (Kart Grid)')
                ->schema([
                    TextInput::make('h')->label('Başlık')->default('Hızlı Bakış'),
                    Repeater::make('items')
                        ->label('Öğeler')
                        ->schema([
                            TextInput::make('label')->required(),
                            TextInput::make('value')->required(),
                        ])
                        ->columns(2)
                        ->columnSpanFull()
                        ->defaultItems(0)
                        ->reorderable(),
                ]),

            Builder\Block::make('image')
                ->label('🖼️ Tek Görsel')
                ->schema([
                    TextInput::make('url')->label('Görsel URL')->url()->required()->columnSpanFull(),
                    TextInput::make('alt')->label('Alt'),
                    TextInput::make('caption')->label('Açıklama (caption)'),
                ]),

            Builder\Block::make('gallery')
                ->label('🖼️🖼️ Galeri (Çok Görsel)')
                ->schema([
                    TextInput::make('h')->label('Başlık'),
                    Repeater::make('items')
                        ->label('Görseller')
                        ->schema([
                            TextInput::make('url')->label('URL')->url()->required()->columnSpanFull(),
                            TextInput::make('alt')->label('Alt'),
                            TextInput::make('source_url')->label('Kaynak URL (opsiyonel)')->url(),
                        ])
                        ->columnSpanFull()
                        ->defaultItems(0)
                        ->reorderable(),
                ]),

            Builder\Block::make('video')
                ->label('🎬 Video (YouTube/Vimeo)')
                ->schema([
                    TextInput::make('h')->label('Başlık')->default('Video'),
                    TextInput::make('url')->label('Video URL')
                        ->placeholder('https://www.youtube.com/watch?v=... veya https://vimeo.com/...')
                        ->url()->columnSpanFull(),
                    TextInput::make('title')->label('Video başlığı (placeholder)')->columnSpanFull(),
                    Textarea::make('description')->label('Açıklama')->rows(2)->columnSpanFull(),
                    TextInput::make('caption')->label('Caption (video altı)'),
                    Select::make('platform')->label('Platform')->options([
                        'youtube' => 'YouTube',
                        'vimeo'   => 'Vimeo',
                    ])->default('youtube'),
                ]),

            Builder\Block::make('table')
                ->label('🧮 Tablo')
                ->schema([
                    TextInput::make('h')->label('Başlık'),
                    TextInput::make('caption')->label('Caption (altyazı)'),
                    Repeater::make('headers')
                        ->label('Sütun başlıkları')
                        ->schema([TextInput::make('label')->hiddenLabel()])
                        ->columnSpanFull()
                        ->defaultItems(0),
                    Textarea::make('rows_json')->label('Satırlar (JSON array of arrays)')
                        ->helperText('[["A1","B1"], ["A2","B2"]]')
                        ->rows(4)->columnSpanFull(),
                ]),

            Builder\Block::make('cost_of_living')
                ->label('💶 Yaşam Maliyeti')
                ->schema([
                    TextInput::make('h')->label('Başlık')->default('Aylık Yaşam Maliyeti'),
                    TextInput::make('currency')->label('Para birimi')->default('EUR'),
                    TextInput::make('total')->label('Toplam (örn: 920-1480)'),
                    Repeater::make('items')
                        ->label('Kalemler')
                        ->schema([
                            TextInput::make('label')->required(),
                            TextInput::make('amount')->required()->placeholder('450-700'),
                            TextInput::make('note')->label('Not'),
                        ])
                        ->columns(3)
                        ->columnSpanFull()
                        ->defaultItems(0),
                    Textarea::make('note')->label('Genel not')->rows(2)->columnSpanFull(),
                ]),

            Builder\Block::make('places')
                ->label('📍 Gezilecek Yerler')
                ->schema([
                    TextInput::make('h')->label('Başlık'),
                    Repeater::make('items')
                        ->label('Yerler')
                        ->schema([
                            TextInput::make('name')->label('İsim')->required(),
                            Select::make('type')->label('Tür')->options([
                                'library' => '📚 Kütüphane',
                                'museum' => '🏛️ Müze',
                                'square' => '🏙️ Meydan',
                                'park' => '🌳 Park',
                                'landmark' => '🗿 Tarihi yer',
                                'cafe' => '☕ Kafe',
                                'restaurant' => '🍽️ Restoran',
                                'university' => '🎓 Üniversite',
                            ]),
                            Textarea::make('description')->rows(2)->columnSpanFull(),
                            TextInput::make('url')->label('Detay URL')->url()->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->columnSpanFull()
                        ->defaultItems(0)
                        ->reorderable(),
                ]),

            Builder\Block::make('student_culture')
                ->label('🎉 Öğrenci Kültürü')
                ->schema([
                    TextInput::make('h')->label('Başlık')->default('Öğrenci Yaşamı ve Kültür'),
                    Textarea::make('body_md')->label('Markdown')->rows(6)->columnSpanFull(),
                    Repeater::make('highlights')
                        ->label('Vurgu noktaları (bullet)')
                        ->schema([TextInput::make('text')->hiddenLabel()])
                        ->columnSpanFull()
                        ->defaultItems(0),
                ]),

            Builder\Block::make('faq')
                ->label('❓ SSS (Soru-Cevap)')
                ->schema([
                    TextInput::make('h')->label('Başlık')->default('Sıkça Sorulanlar'),
                    Repeater::make('items')
                        ->label('Sorular')
                        ->schema([
                            TextInput::make('q')->label('Soru')->required()->columnSpanFull(),
                            Textarea::make('a')->label('Cevap (markdown)')->rows(3)->required()->columnSpanFull(),
                        ])
                        ->columnSpanFull()
                        ->defaultItems(0)
                        ->reorderable(),
                ]),

            Builder\Block::make('cta')
                ->label('📣 CTA (Çağrı)')
                ->schema([
                    TextInput::make('h')->label('Başlık'),
                    Textarea::make('body_md')->label('İçerik (markdown)')->rows(3)->columnSpanFull(),
                ]),

            Builder\Block::make('universities_in_city')
                ->label('🎓 Şehirdeki Üniler (Programatik)')
                ->schema([
                    TextInput::make('h')->label('Başlık'),
                    TextInput::make('total')->label('Toplam')->numeric(),
                    TextInput::make('public')->label('Devlet')->numeric(),
                    TextInput::make('private')->label('Özel')->numeric(),
                    Repeater::make('top_unis')
                        ->label('Top üniler')
                        ->schema([TextInput::make('name')->hiddenLabel()])
                        ->columnSpanFull()
                        ->defaultItems(0),
                ]),

            Builder\Block::make('programs_summary')
                ->label('🎓 Programlar Özeti (Programatik)')
                ->schema([
                    TextInput::make('h')->label('Başlık'),
                    TextInput::make('total')->label('Toplam')->numeric(),
                    TextInput::make('bachelor')->label('Bachelor')->numeric(),
                    TextInput::make('master')->label('Master')->numeric(),
                    TextInput::make('phd')->label('PhD')->numeric(),
                ]),

            Builder\Block::make('almanyauni_forum_topics')
                ->label('💬 AlmanyaUni Forum Konuları')
                ->schema([
                    TextInput::make('h')->label('Başlık'),
                    TextInput::make('cta_url')->label('CTA URL')->default('/forum/'),
                    Repeater::make('items')
                        ->label('Forum konuları')
                        ->schema([
                            TextInput::make('title')->label('Başlık')->required()->columnSpanFull(),
                            TextInput::make('url')->label('URL')->required()->columnSpanFull(),
                            TextInput::make('views')->label('Görüntülenme')->numeric(),
                            TextInput::make('replies')->label('Cevap')->numeric(),
                            TextInput::make('last_post')->label('Son aktivite (YYYY-MM-DD)'),
                        ])
                        ->columns(2)
                        ->columnSpanFull()
                        ->defaultItems(0)
                        ->reorderable(),
                ]),

            Builder\Block::make('related_forum_topics')
                ->label('💬 İlgili Forum Konuları')
                ->schema([
                    TextInput::make('h')->label('Başlık'),
                    TextInput::make('source')->label('Kaynak (örn: DeutschStudent)'),
                    Repeater::make('items')
                        ->label('Forum konuları')
                        ->schema([
                            TextInput::make('title')->label('Başlık')->required()->columnSpanFull(),
                            TextInput::make('url')->label('URL')->url()->required()->columnSpanFull(),
                            TextInput::make('views')->label('Görüntülenme')->numeric(),
                            TextInput::make('replies')->label('Cevap')->numeric(),
                            TextInput::make('category')->label('Kategori'),
                        ])
                        ->columns(2)
                        ->columnSpanFull()
                        ->defaultItems(0)
                        ->reorderable(),
                ]),

            Builder\Block::make('external_links')
                ->label('🔗 Dış Bağlantılar')
                ->schema([
                    TextInput::make('h')->label('Başlık')->default('Faydalı Linkler'),
                    Repeater::make('items')
                        ->label('Linkler')
                        ->schema([
                            TextInput::make('label')->label('Etiket')->required(),
                            TextInput::make('url')->label('URL')->url()->required(),
                            Select::make('type')->options([
                                'wikipedia' => 'Wikipedia',
                                'wikidata' => 'Wikidata',
                                'official' => 'Resmi',
                                'other' => 'Diğer',
                            ]),
                        ])
                        ->columns(3)
                        ->columnSpanFull()
                        ->defaultItems(0),
                ]),

            Builder\Block::make('schema_jsonld')
                ->label('🤖 Schema.org JSON-LD (Hidden)')
                ->schema([
                    Textarea::make('data_json')
                        ->label('JSON (otomatik üretilir, dokunma)')
                        ->rows(8)
                        ->columnSpanFull()
                        ->disabled()
                        ->dehydrated(false),
                ]),
        ];
    }
}
