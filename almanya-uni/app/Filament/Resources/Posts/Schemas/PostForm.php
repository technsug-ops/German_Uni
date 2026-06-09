<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Temel Bilgiler')
                    ->columns(2)
                    ->components([
                        TextInput::make('title')
                            ->label('Başlık')
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('slug')
                            ->label('URL slug')
                            ->required()
                            ->helperText('Örn: almanyada-yuksek-lisans-2026'),

                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name'),

                        Select::make('user_id')
                            ->label('Yazar')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),

                        Textarea::make('excerpt')
                            ->label('Özet (kart + meta için)')
                            ->rows(3)
                            ->maxLength(300)
                            ->columnSpanFull(),
                    ]),

                Section::make('İçerik')
                    ->columns(1)
                    ->components([
                        Textarea::make('content_md')
                            ->label('Markdown İçerik')
                            ->required()
                            ->rows(25)
                            ->helperText('Markdown: ![alt](resim-url) · [link](url) · # H1 · ## H2 · **bold** · | tablo | · > alıntı'),
                    ]),

                Section::make('Medya')
                    ->columns(2)
                    ->components([
                        FileUpload::make('featured_image')
                            ->label('Öne Çıkan Görsel')
                            ->image()
                            ->disk('public')
                            ->directory('blog/featured')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios(['16:9', '4:3'])
                            ->maxSize(4096)
                            ->helperText('16:9 önerilir · JPG/PNG/WebP · 4 MB max · BOŞ BIRAKILABİLİR (kutu görünmez)')
                            ->columnSpanFull(),

                        TextInput::make('featured_image_caption')
                            ->label('Görsel Açıklaması')
                            ->maxLength(255)
                            ->helperText('Görselin altında küçük yazı (opsiyonel)')
                            ->columnSpanFull(),

                        FileUpload::make('audio_url')
                            ->label('Podcast / Sesli Anlatım')
                            ->disk('public')
                            ->directory('blog/audio')
                            ->visibility('public')
                            ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/m4a', 'audio/x-m4a', 'audio/aac', 'audio/ogg'])
                            ->maxSize(51200) // 50 MB
                            ->helperText('MP3/M4A/WAV · 50 MB max · BOŞ → player görünmez')
                            ->columnSpanFull(),

                        TextInput::make('audio_duration_seconds')
                            ->label('Ses Süresi (saniye)')
                            ->numeric()
                            ->placeholder('Örn: 720 = 12 dakika')
                            ->helperText('Opsiyonel. Player altında "12:00" olarak gösterilir.'),

                        TextInput::make('video_url')
                            ->label('Video URL (YouTube/Vimeo)')
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://www.youtube.com/watch?v=...')
                            ->helperText('Embed gömülü oynatıcı olarak yazının başında görünür. BOŞ → görünmez'),

                        FileUpload::make('gallery_images')
                            ->label('Galeri Görselleri')
                            ->multiple()
                            ->image()
                            ->reorderable()
                            ->disk('public')
                            ->directory('blog/gallery')
                            ->visibility('public')
                            ->maxFiles(12)
                            ->maxSize(2048)
                            ->helperText('Birden çok görsel · yazının sonunda galeri olarak görünür · BOŞ → galeri görünmez')
                            ->columnSpanFull(),
                    ]),

                Section::make('SEO')
                    ->columns(1)
                    ->collapsed()
                    ->components([
                        TextInput::make('meta_title')
                            ->label('Meta Title (60 char max)')
                            ->maxLength(70),

                        Textarea::make('meta_description')
                            ->label('Meta Description (155 char ideal)')
                            ->helperText('Daha uzun girersen KAYDEDERKEN otomatik ~165 karaktere kısaltılır — kaydetmeyi engellemez.')
                            ->rows(2)
                            // Hard maxLength(170) mevcut uzun açıklamalarda kaydetmeyi BLOKE ediyordu
                            // ("işlem yapılmıyor"). Auto-truncate: blokemez + SEO-temiz kalır.
                            ->dehydrateStateUsing(fn (?string $state) => $state ? \Illuminate\Support\Str::limit(trim($state), 165, '…') : $state),
                    ]),

                Section::make('Yayın')
                    ->columns(2)
                    ->components([
                        Toggle::make('is_published')
                            ->label('Yayında')
                            ->default(true),

                        DateTimePicker::make('published_at')
                            ->label('Yayın Tarihi')
                            ->default(now()),

                        TextInput::make('reading_minutes')
                            ->label('Okuma Süresi (dk) — otomatik hesap')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('view_count')
                            ->label('Görüntülenme')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),
                    ]),
            ]);
    }
}
