<?php

namespace App\Filament\Resources\Universities\Schemas;

use App\Filament\Support\ContentBlocksBuilder;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UniversityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Temel Bilgiler')
                    ->columns(2)
                    ->components([
                        TextInput::make('name_de')->label('Ad (Almanca)')->required()->columnSpan(2),
                        TextInput::make('name_tr')->label('Ad (Türkçe)')->required(),
                        TextInput::make('name_en')->label('Ad (İngilizce)'),
                        TextInput::make('short_name')->label('Kısaltma (TUM, LMU vs.)'),
                        TextInput::make('slug')->label('Slug')->required()
                            ->helperText('URL\'de kullanılır; eşsiz olmalı'),
                        Select::make('type')
                            ->label('Tür')
                            ->options([
                                'public'   => 'Devlet',
                                'private'  => 'Özel',
                                'religion' => 'Dini',
                                'applied_sciences' => 'Uygulamalı Bilimler',
                                'art'      => 'Sanat',
                            ])
                            ->required()
                            ->native(false),
                        Toggle::make('is_active')->label('Aktif')->default(true),
                    ]),

                Section::make('Açıklama')
                    ->collapsible()
                    ->columns(1)
                    ->components([
                        Textarea::make('description_tr')->label('Türkçe açıklama')->rows(4),
                        Textarea::make('description_de')->label('Almanca açıklama')->rows(4),
                        Textarea::make('description_en')->label('İngilizce açıklama')->rows(4),
                    ]),

                Section::make('Lokasyon')
                    ->columns(3)
                    ->components([
                        Select::make('city_id')
                            ->label('Şehir')
                            ->relationship('city', 'name_de')
                            ->searchable()
                            ->preload()
                            ->columnSpan(3),
                        TextInput::make('latitude')->label('Enlem')->numeric()->step(0.0000001),
                        TextInput::make('longitude')->label('Boylam')->numeric()->step(0.0000001),
                    ]),

                Section::make('Görsel & Web')
                    ->columns(2)
                    ->components([
                        \Filament\Forms\Components\FileUpload::make('logo_upload')
                            ->label('Logo yükle')
                            ->image()
                            ->imageEditor()
                            ->maxSize(2048)
                            ->disk('public')
                            ->directory('university-logos')
                            ->visibility('public')
                            ->columnSpanFull()
                            ->helperText('Dosya yükle (max 2MB) → otomatik public storage')
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) $set('logo_url', '/storage/' . $state);
                            })
                            ->dehydrated(false),
                        TextInput::make('logo_url')
                            ->label('Logo URL (yüklediysen otomatik dolar)')
                            ->url()
                            ->columnSpanFull(),

                        \Filament\Forms\Components\FileUpload::make('image_upload')
                            ->label('Kapak görseli yükle (kampüs fotoğrafı)')
                            ->image()
                            ->imageEditor()
                            ->maxSize(5120)
                            ->disk('public')
                            ->directory('university-images')
                            ->visibility('public')
                            ->columnSpanFull()
                            ->helperText('Dosya yükle (max 5MB). Yüklemezsen yan kutuya URL yapıştırabilirsin.')
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) $set('image_url', '/storage/' . $state);
                            })
                            ->dehydrated(false),
                        TextInput::make('image_url')
                            ->label('Kapak görseli URL (yüklediysen otomatik dolar)')
                            ->url()
                            ->columnSpanFull()
                            ->helperText('Liste sayfasındaki kart görseli ve detay hero arka planı. Boşsa şehir görseli fallback olarak kullanılır.'),

                        TextInput::make('website_url')->label('Web sitesi')->url(),
                        TextInput::make('founded_year')->label('Kuruluş yılı')->numeric()->minValue(1000)->maxValue(2100),
                        TextInput::make('student_count')->label('Öğrenci sayısı')->numeric()->minValue(0),
                    ]),

                Section::make('Sıralamalar (Dünya & Topluluk)')
                    ->description('Dünya sıralama pozisyonları (boşsa o sıralama listesinde görünmez). Topluluk skoru komuttan otomatik hesaplanır.')
                    ->columns(3)
                    ->collapsed()
                    ->components([
                        TextInput::make('qs_world_rank')->label('QS World Rank')->numeric()->minValue(1)->maxValue(2000)
                            ->helperText('topuniversities.com/world-university-rankings/2026'),
                        TextInput::make('the_world_rank')->label('THE World Rank')->numeric()->minValue(1)->maxValue(2000)
                            ->helperText('timeshighereducation.com'),
                        TextInput::make('arwu_world_rank')->label('ARWU / Shanghai Rank')->numeric()->minValue(1)->maxValue(2000)
                            ->helperText('shanghairanking.com (ARWU)'),
                        TextInput::make('community_mention_score')->label('Topluluk skoru')->numeric()->minValue(0)
                            ->disabled()
                            ->helperText('Otomatik: `php artisan mentions:compute` komutu doldurur'),
                    ]),

                Section::make('QS Indicator Breakdown (0-100, opsiyonel)')
                    ->description('QS World University Rankings 9 metric. Boş bırakırsan o indicator gösterilmez. topuniversities.com → üni detay sayfasından alınır.')
                    ->columns(3)
                    ->collapsed()
                    ->components([
                        TextInput::make('qs_overall_score')->label('Overall Score')->numeric()->step(0.01)->minValue(0)->maxValue(100)
                            ->helperText('Toplam QS skor — büyükten gizler'),
                        TextInput::make('qs_academic_reputation')->label('Academic Reputation (30%)')->numeric()->step(0.01)->minValue(0)->maxValue(100),
                        TextInput::make('qs_employer_reputation')->label('Employer Reputation (15%)')->numeric()->step(0.01)->minValue(0)->maxValue(100),
                        TextInput::make('qs_citations_per_faculty')->label('Citations per Faculty (20%)')->numeric()->step(0.01)->minValue(0)->maxValue(100),
                        TextInput::make('qs_faculty_student_ratio')->label('Faculty/Student Ratio (10%)')->numeric()->step(0.01)->minValue(0)->maxValue(100),
                        TextInput::make('qs_international_faculty')->label('International Faculty (5%)')->numeric()->step(0.01)->minValue(0)->maxValue(100),
                        TextInput::make('qs_international_students')->label('International Students (5%)')->numeric()->step(0.01)->minValue(0)->maxValue(100),
                        TextInput::make('qs_international_research')->label('International Research (5%)')->numeric()->step(0.01)->minValue(0)->maxValue(100),
                        TextInput::make('qs_employment_outcomes')->label('Employment Outcomes (5%)')->numeric()->step(0.01)->minValue(0)->maxValue(100),
                        TextInput::make('qs_sustainability')->label('Sustainability (5%)')->numeric()->step(0.01)->minValue(0)->maxValue(100),
                    ]),

                Section::make('İçerik Blokları (AI üretimi + manuel düzen)')
                    ->description('Bloklar drag-drop ile sıralanabilir, eklenebilir, silinebilir. "🪄 Sayfa Üret" butonu mevcudu yeniden üretir.')
                    ->collapsible()
                    ->collapsed()
                    ->components([
                        ContentBlocksBuilder::make('content_blocks'),
                    ]),

                Section::make('Kaynaklar & Kimlikler')
                    ->collapsed()
                    ->columns(2)
                    ->components([
                        TextInput::make('wikidata_id')->label('Wikidata ID'),
                        TextInput::make('partner_id')->label('Partner ID (UUID)'),
                        Toggle::make('is_uni_assist_member')->label('Uni-Assist üyesi'),
                        TextInput::make('uni_assist_id')->label('Uni-Assist ID')->numeric(),
                        TextInput::make('wikipedia_url_de')->label('Wikipedia DE')->url(),
                        TextInput::make('wikipedia_url_en')->label('Wikipedia EN')->url(),
                        TextInput::make('wikipedia_url_tr')->label('Wikipedia TR')->url(),
                        TextInput::make('data_source')->label('Veri kaynağı')->default('manual'),
                        DateTimePicker::make('last_synced_at')->label('Son senkronizasyon'),
                    ]),
            ]);
    }
}
