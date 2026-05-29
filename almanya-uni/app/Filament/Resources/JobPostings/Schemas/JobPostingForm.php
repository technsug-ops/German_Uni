<?php

namespace App\Filament\Resources\JobPostings\Schemas;

use App\Models\City;
use App\Models\FieldOfStudy;
use App\Models\JobPosting;
use App\Models\University;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class JobPostingForm
{
    public static function configure(Schema $schema): Schema
    {
        $positionOptions = [];
        foreach (JobPosting::POSITION_TYPES as $key => $meta) {
            $positionOptions[$key] = $meta['icon'] . ' ' . $meta['label_tr'];
        }

        return $schema->components([
            Section::make('Temel Bilgi')->schema([
                TextInput::make('title')
                    ->label('İlan Başlığı')
                    ->required()
                    ->maxLength(200)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state ?? '') . '-' . now()->format('ymdHi'))),

                TextInput::make('slug')
                    ->required()
                    ->maxLength(220)
                    ->unique(ignoreRecord: true)
                    ->helperText('Otomatik oluşur — değiştirebilirsin'),

                Select::make('position_type')
                    ->label('Pozisyon Tipi')
                    ->options($positionOptions)
                    ->required(),

                Select::make('employment_type')
                    ->label('İstihdam Tipi')
                    ->options([
                        'full_time'  => '⏰ Tam zamanlı',
                        'part_time'  => '⏱️ Yarı zamanlı',
                        'fixed_term' => '📅 Süreli',
                        'permanent'  => '♾️ Daimi',
                    ])
                    ->default('fixed_term')
                    ->required(),

                Select::make('language')
                    ->label('Çalışma Dili')
                    ->options([
                        'en'   => '🇬🇧 İngilizce',
                        'de'   => '🇩🇪 Almanca',
                        'both' => '🌍 İkisi de',
                    ])
                    ->default('en')
                    ->required(),

                Textarea::make('excerpt')
                    ->label('Kısa Özet (kart için, max 240)')
                    ->rows(2)
                    ->maxLength(240)
                    ->columnSpanFull(),
            ])->columns(2),

            Section::make('Kurum + Lokasyon + Alan')->schema([
                Select::make('university_id')
                    ->label('Üniversite')
                    ->options(University::where('is_active', 1)->orderBy('name_de')->pluck('name_de', 'id'))
                    ->searchable(),

                Select::make('city_id')
                    ->label('Şehir')
                    ->options(City::where('is_active', 1)->orderBy('name_de')->pluck('name_de', 'id'))
                    ->searchable(),

                Select::make('field_of_study_id')
                    ->label('Alan')
                    ->options(FieldOfStudy::active()->orderBy('name_tr')->pluck('name_tr', 'id'))
                    ->searchable(),

                Toggle::make('is_remote')
                    ->label('Remote/Hybrid uygun mu?')
                    ->default(false),
            ])->columns(2)->collapsed(),

            Section::make('Maaş + Tarih')->schema([
                TextInput::make('salary_band')
                    ->label('Maaş Bandı (TV-L E13, TV-L E14, vb.)')
                    ->maxLength(60)
                    ->placeholder('TV-L E13 / 100%'),
                TextInput::make('salary_min_eur')->label('Min Maaş (€/yıl)')->numeric(),
                TextInput::make('salary_max_eur')->label('Max Maaş (€/yıl)')->numeric(),
                DatePicker::make('posted_at')->label('Yayın Tarihi')->default(now()),
                DatePicker::make('deadline_at')->label('Son Başvuru'),
            ])->columns(3)->collapsed(),

            Section::make('İçerik (Markdown)')->schema([
                Textarea::make('description')
                    ->label('Detaylı Açıklama')
                    ->rows(10)
                    ->helperText('Markdown destekli'),
                Textarea::make('requirements')
                    ->label('Gereksinimler (bullet liste)')
                    ->rows(6)
                    ->helperText('Markdown destekli — - madde madde yaz'),
            ])->columns(1),

            Section::make('Başvuru + Kaynak')->schema([
                TextInput::make('application_url')->label('Başvuru URL')->url()->maxLength(500),
                TextInput::make('source_url')->label('Orijinal İlan URL')->url()->maxLength(500),
                TextInput::make('source_name')->label('Kaynak (örn. THE Jobs, Academic Positions)')->maxLength(80),
            ])->columns(1)->collapsed(),

            Section::make('Görünürlük')->schema([
                Toggle::make('is_featured')->label('Öne çıkar')->default(false),
                Toggle::make('is_active')->label('Aktif')->default(true),
            ])->columns(2),
        ]);
    }
}
