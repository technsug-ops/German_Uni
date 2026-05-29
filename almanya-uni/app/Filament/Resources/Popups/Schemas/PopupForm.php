<?php

namespace App\Filament\Resources\Popups\Schemas;

use App\Models\Popup;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PopupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Temel Ayarlar')->schema([
                TextInput::make('key')
                    ->label('Anahtar (cookie key)')
                    ->required()
                    ->maxLength(60)
                    ->helperText('Stabil cookie anahtarı. Değiştirirsen kullanıcı popup\'ı tekrar görür. Boş bırakırsan otomatik üretilir.')
                    ->default(fn () => 'popup-' . now()->format('Ymd-Hi')),

                Select::make('theme')
                    ->label('Tema')
                    ->options(Popup::THEMES)
                    ->default('gradient')
                    ->required(),

                Select::make('position')
                    ->label('Pozisyon')
                    ->options(Popup::POSITIONS)
                    ->default('center')
                    ->required(),

                Toggle::make('is_active')
                    ->label('Aktif (canlı göster)')
                    ->default(false)
                    ->helperText('Açık olunca aşağıdaki tüm kriterlere uyan ziyaretçilere görünür.'),
            ])->columns(2),

            Section::make('İçerik (3 dilde)')->schema([
                TextInput::make('title_tr')->label('Başlık (TR)')->maxLength(150),
                TextInput::make('title_en')->label('Başlık (EN)')->maxLength(150),
                TextInput::make('title_de')->label('Başlık (DE)')->maxLength(150),

                Textarea::make('body_tr')->label('Metin (TR)')->rows(3)->maxLength(600),
                Textarea::make('body_en')->label('Metin (EN)')->rows(3)->maxLength(600),
                Textarea::make('body_de')->label('Metin (DE)')->rows(3)->maxLength(600),
            ])->columns(3),

            Section::make('Medya Tipi')->schema([
                Select::make('media_type')
                    ->label('Popup içeriği')
                    ->options(Popup::MEDIA_TYPES)
                    ->default('text')
                    ->required()
                    ->live()
                    ->helperText('İçeriği değiştir, ilgili alanlar açılır.'),
            ])->columns(1),

            Section::make('🖼️ Görsel (image type için)')
                ->schema([
                    TextInput::make('image_url')
                        ->label('Görsel URL')
                        ->url()
                        ->placeholder('https://...')
                        ->maxLength(500)
                        ->columnSpanFull()
                        ->helperText('Önerilen oran: 16:9 veya 4:3, max ~600 KB. CDN kullan.'),
                ])
                ->collapsible()
                ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get) => in_array($get('media_type'), ['image', 'video'], true)),

            Section::make('🎬 Video (video type için)')
                ->schema([
                    TextInput::make('video_url')
                        ->label('Video URL')
                        ->url()
                        ->maxLength(500)
                        ->columnSpanFull()
                        ->placeholder('https://www.youtube.com/watch?v=...  ya da .mp4 dosyası')
                        ->helperText('YouTube watch link, Vimeo link ya da direkt .mp4 URL. Embed URL\'ye otomatik dönüştürülür.'),
                    Toggle::make('video_autoplay')
                        ->label('Otomatik oynat')
                        ->default(false)
                        ->helperText('Modern tarayıcılar autoplay için "muted" zorunlu kılar.'),
                    Toggle::make('video_muted')
                        ->label('Sessiz başlat')
                        ->default(true),
                ])
                ->columns(2)
                ->collapsible()
                ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('media_type') === 'video'),

            Section::make('Süsleme')->schema([
                TextInput::make('emoji')
                    ->label('Emoji')
                    ->maxLength(20)
                    ->placeholder('🎉')
                    ->helperText('Başlık yanında küçük emoji süsleme'),
                ColorPicker::make('accent_color')
                    ->label('Vurgu Rengi (hex)')
                    ->helperText('Tema renginin üstüne yazar. Boş = tema varsayılanı.'),
            ])->columns(2)->collapsed(),

            Section::make('CTA Buton')->schema([
                TextInput::make('cta_label_tr')->label('Buton metni (TR)')->maxLength(60)->placeholder('Hemen başla'),
                TextInput::make('cta_label_en')->label('Buton metni (EN)')->maxLength(60)->placeholder('Get started'),
                TextInput::make('cta_label_de')->label('Buton metni (DE)')->maxLength(60)->placeholder('Jetzt loslegen'),

                TextInput::make('cta_url')
                    ->label('Buton URL')
                    ->url()
                    ->maxLength(500)
                    ->columnSpan(2)
                    ->placeholder('https://applytogerman.com/tools/...'),
                Toggle::make('cta_external')
                    ->label('Yeni sekmede aç')
                    ->default(false),
            ])->columns(3),

            Section::make('İkincil "kapat" butonu (opsiyonel)')->schema([
                TextInput::make('secondary_label_tr')->label('Metin (TR)')->maxLength(60)->placeholder('Belki sonra'),
                TextInput::make('secondary_label_en')->label('Metin (EN)')->maxLength(60),
                TextInput::make('secondary_label_de')->label('Metin (DE)')->maxLength(60),
            ])->columns(3)->collapsed(),

            Section::make('Hedefleme (Targeting)')->schema([
                TagsInput::make('target_pages')
                    ->label('Hangi sayfalarda göster?')
                    ->placeholder('/blog/*  veya  scholarships.index')
                    ->helperText('Boş = TÜM sayfalar. Route adı (örn. scholarships.index) ya da URL pattern (/blog/*) kullan.'),
                TagsInput::make('exclude_pages')
                    ->label('Hangi sayfalarda gösterme?')
                    ->placeholder('/admin/*  veya  legal.privacy'),
                Select::make('locales')
                    ->label('Hangi dillerde?')
                    ->multiple()
                    ->options(['tr' => '🇹🇷 Türkçe', 'en' => '🇬🇧 English', 'de' => '🇩🇪 Deutsch'])
                    ->placeholder('Boş = tüm diller'),
            ])->columns(1),

            Section::make('Tetikleme + Kapama')->schema([
                Select::make('trigger')
                    ->label('Ne zaman göstersin?')
                    ->options(Popup::TRIGGERS)
                    ->default('time_5s')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $defaults = ['page_load' => 0, 'time_5s' => 5000, 'time_15s' => 15000, 'scroll_50' => 0, 'exit_intent' => 0];
                        if (isset($defaults[$state])) $set('delay_ms', $defaults[$state]);
                    }),
                TextInput::make('delay_ms')
                    ->label('Gecikme (ms)')
                    ->numeric()
                    ->default(5000)
                    ->helperText('Sadece "saniye sonra" tetikleyiciler için. 1000ms = 1 saniye.'),
                TextInput::make('dismiss_days')
                    ->label('Kapatıldıktan sonra kaç gün gizlensin?')
                    ->numeric()
                    ->default(7)
                    ->minValue(0)
                    ->helperText('0 = her sayfa açılışta yine göster (agresif). Önerilen 7-14.'),
                Toggle::make('show_dismiss_button')
                    ->label('× kapat butonu göster')
                    ->default(true),
            ])->columns(2),

            Section::make('Zamanlama (opsiyonel)')->schema([
                DateTimePicker::make('starts_at')->label('Başlangıç tarihi')->seconds(false),
                DateTimePicker::make('ends_at')->label('Bitiş tarihi')->seconds(false),
                TextInput::make('priority')
                    ->label('Öncelik (düşük = önce gösterilir)')
                    ->numeric()
                    ->default(5)
                    ->minValue(1)
                    ->maxValue(100)
                    ->helperText('Birden fazla popup eşleşirse en düşük priority kazanır.'),
            ])->columns(3)->collapsed(),

            Section::make('Performans (sadece-okunur)')->schema([
                TextInput::make('view_count')->label('Görüntülenme')->disabled(),
                TextInput::make('click_count')->label('CTA tıklanma')->disabled(),
                TextInput::make('dismiss_count')->label('Kapatma')->disabled(),
            ])->columns(3)->collapsed()->hiddenOn('create'),
        ]);
    }
}
