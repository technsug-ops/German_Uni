<?php

namespace App\Filament\Resources\NewsCandidates\Schemas;

use App\Models\Category;
use App\Services\Content\ContentVoice;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NewsCandidateForm
{
    public static function configure(Schema $schema): Schema
    {
        $localeOptions = [];
        foreach (ContentVoice::contentLocales() as $l) {
            $localeOptions[$l] = strtoupper($l) . ' · ' . ContentVoice::languageName($l);
        }

        return $schema->components([
            Section::make('Mod & Sınıflandırma')->schema([
                Select::make('origin')
                    ->label('Mod')
                    ->options([
                        'manual' => '3️⃣ Manuel — görsel + yazı + kaynak elle',
                        'link'   => '2️⃣ Link — URL ver, içeriği çeksin',
                        'auto'   => '1️⃣ Otomatik — kaynaktan çekilmiş',
                    ])
                    ->default('manual')
                    ->required()
                    ->helperText('Link/Otomatik: kaydet → tabloda "İçeriği Çek" + "AI Taslak Üret". Manuel: taslağı aşağıda elle yaz.'),

                Select::make('suggested_category_id')
                    ->label('Kategori')
                    ->options(Category::where('kind', 'news')->orderBy('sort_order')->pluck('name_tr', 'id'))
                    ->searchable()
                    ->helperText('Boşsa "Pratik & Takvim" varsayılır.'),

                Select::make('primary_locale')
                    ->label('Birincil dil (önce bu yazılır, diğerlerine çevrilir)')
                    ->options($localeOptions)
                    ->default('tr')
                    ->required(),

                TextInput::make('priority')
                    ->label('Öncelik (0 = öncelik yok → en yeni en önde)')
                    ->numeric()
                    ->default(0)
                    ->helperText('Yüksek değer akışta öne sabitler. Boş bırak → tarihe göre sıralanır.'),
            ])->columns(2),

            Section::make('Kaynak')->schema([
                TextInput::make('source_url')
                    ->label('Kaynak URL (Link modunda zorunlu)')
                    ->url()
                    ->maxLength(600)
                    ->columnSpanFull(),
                TextInput::make('source_name')
                    ->label('Kaynak adı (örn. DAAD, Make-it-in-Germany)')
                    ->maxLength(120),
                DatePicker::make('event_date')->label('Olay/yayın tarihi'),
                TextInput::make('image_url')
                    ->label('Görsel URL')
                    ->url()
                    ->maxLength(600)
                    ->columnSpanFull(),
            ])->columns(2),

            Section::make('Orijinal (çekilen / ham)')->schema([
                TextInput::make('orig_title')->label('Orijinal başlık')->maxLength(300)->columnSpanFull(),
                Textarea::make('raw_excerpt')->label('Ham özet')->rows(2)->columnSpanFull(),
                Textarea::make('fetched_content')->label('Çekilen içerik (AI taslağın kaynağı)')->rows(4)->columnSpanFull(),
            ])->collapsed(),

            Section::make('Editöryel Taslak (yayınlanacak — birincil dil)')->schema([
                TextInput::make('draft_title')->label('Başlık')->maxLength(300)->columnSpanFull(),
                Textarea::make('draft_excerpt')->label('Özet (2 cümle)')->rows(2)->maxLength(240)->columnSpanFull(),
                Textarea::make('draft_md')
                    ->label('Gövde (Markdown)')
                    ->rows(12)
                    ->helperText('Manuel modda elle yaz. Link/Otomatik modda "AI Taslak Üret" doldurur. ÖZGÜN olmalı — kaynağı kopyalama, atıf ver.')
                    ->columnSpanFull(),
            ]),
        ]);
    }
}
