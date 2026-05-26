<?php

namespace App\Filament\Resources\ContentBriefs\Schemas;

use App\Models\ContentBrief;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContentBriefForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('📝 Yeni Brief')
                ->description('Yalnızca başlık zorunlu. Diğer her şeyi AI 60K+ topluluk sorusundan damıtarak otomatik üretecek.')
                ->columns(2)
                ->components([
                    TextInput::make('title')
                        ->label('Başlık')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull()
                        ->autofocus()
                        ->placeholder('Örn: Almanya\'da Werkstudent olarak çalışmak — saatlik ücret, vergi, sigorta')
                        ->helperText('💡 Sadece bunu doldur. Topic+audience opsiyonel — AI tahmin eder. Diğer her şey topluluk verisinden otomatik üretilir.'),
                    Select::make('topic')
                        ->label('Topic (opsiyonel — AI tahmin eder)')
                        ->options([
                            'vize' => 'Vize (8.5K mesaj)',
                            'dil' => 'Dil (7.7K)',
                            'para' => 'Para & Finansman (2.8K)',
                            'randevu' => 'Randevu (2.4K)',
                            'uni_assist' => 'Uni-Assist (1.7K)',
                            'yurt' => 'Yurt (1.2K)',
                            'sehir' => 'Şehir (1K)',
                            'master' => 'Master (960)',
                            'sigorta' => 'Sigorta (734)',
                            'studienkolleg' => 'Studienkolleg (433)',
                            'denklik' => 'Denklik (432)',
                            'is' => 'İş & Werkstudent (430)',
                            'anmeldung' => 'Anmeldung (407)',
                            'burs' => 'Burs (264)',
                        ])
                        ->searchable(),
                    Select::make('audience')
                        ->label('Hedef kitle (opsiyonel)')
                        ->options(ContentBrief::AUDIENCES)
                        ->default('aday_ogrenci'),
                ]),

            // EDIT modunda görünür — AI ürettikten sonra kullanıcı düzeltebilir
            Section::make('⚙️ AI çıktısı (düzenlenebilir)')
                ->description('AI dolduruyor. Beğenmediğin alanları değiştirebilirsin.')
                ->visibleOn(['edit'])
                ->columns(2)
                ->components([
                    Select::make('brand_tone')
                        ->label('Tonlama')
                        ->options(ContentBrief::TONES),
                    TextInput::make('target_word_count')
                        ->label('Hedef kelime')
                        ->numeric()
                        ->minValue(300)
                        ->maxValue(5000),
                    Select::make('status')
                        ->label('Durum')
                        ->options(ContentBrief::STATUSES)
                        ->default('draft'),
                    TextInput::make('primary_keyword')
                        ->label('Ana anahtar kelime (AI üretti)')
                        ->maxLength(200),
                    Textarea::make('secondary_keywords')
                        ->label('İkincil keyword\'ler (AI üretti)')
                        ->rows(2)
                        ->columnSpanFull()
                        ->dehydrateStateUsing(fn ($state) => is_string($state) ? array_filter(array_map('trim', explode(',', $state))) : $state)
                        ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state),
                    Textarea::make('pain_point')
                        ->label('Pain point (AI üretti)')
                        ->rows(2)
                        ->columnSpanFull(),
                ]),

            Section::make('💬 Kaynak Sorular (topluluk\'tan)')
                ->description('AI bu soruları telegram/forum havuzundan seçti. Yeni eklenebilir.')
                ->visibleOn(['edit'])
                ->collapsed()
                ->components([
                    Textarea::make('source_questions')
                        ->label('Her satıra 1 soru')
                        ->rows(8)
                        ->dehydrateStateUsing(fn ($state) => is_string($state) ? array_filter(array_map('trim', explode("\n", $state))) : $state)
                        ->formatStateUsing(fn ($state) => is_array($state) ? implode("\n", $state) : $state),
                ]),

            Section::make('Notlar')
                ->visibleOn(['edit'])
                ->collapsed()
                ->components([
                    Textarea::make('notes')->rows(3)->columnSpanFull(),
                ]),
        ]);
    }
}
