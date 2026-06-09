<?php

namespace App\Filament\Resources\HealthInsuranceProviders\Schemas;

use App\Models\HealthInsuranceProvider;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class HealthInsuranceProviderForm
{
    public static function configure(Schema $schema): Schema
    {
        $typeOptions = [];
        foreach (HealthInsuranceProvider::TYPES as $key => $meta) {
            $typeOptions[$key] = $meta['emoji'] . ' ' . $meta['label'];
        }

        return $schema->components([
            Section::make('Kimlik')->schema([
                Select::make('type')->label('Tipi')->required()
                    ->options($typeOptions),
                TextInput::make('name')->label('Marka Adı')->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state ?? ''))),
                TextInput::make('slug')->required()->unique(ignoreRecord: true),
                Select::make('best_for')->label('Kime uygun')
                    ->options([
                        'public_standard' => 'Kayıtlı öğrenci (≤30)',
                        'over_30'         => '30 yaş üstü / doktora / burslu',
                        'pre_enrollment'  => 'Dil kursu / Studienkolleg öncesi',
                        'non_eu_incoming' => 'Yeni gelenler / ilk aylar',
                    ])
                    ->helperText('Karar yardımcısı etiketinde gösterilir'),
                TextInput::make('logo_url')->label('Logo URL')->url()->columnSpanFull(),
            ])->columns(2),

            Section::make('Bağlantılar')->schema([
                TextInput::make('website_url')->label('Resmi Website')->url()->required(),
                TextInput::make('affiliate_url')->label('Affiliate / Referans URL')->url()
                    ->helperText('Boşsa CTA buton website_url\'e gider'),
            ])->columns(2),

            Section::make('Fiyatlandırma (EUR/ay)')->schema([
                TextInput::make('monthly_fee_eur')->label('Aylık (min)')->numeric()->step(0.01)->prefix('€'),
                TextInput::make('monthly_fee_max_eur')->label('Aylık (max — aralık için)')->numeric()->step(0.01)->prefix('€')
                    ->helperText('Doluysa "€30–90" gösterir'),
                TextInput::make('age_limit')->label('Yaş / dönem limiti')->numeric()
                    ->placeholder('30')->helperText('GKV öğrenci tarifesi ~30; boş = limit yok'),
            ])->columns(3),

            Section::make('Kabul & Kapsam')->schema([
                Toggle::make('accepted_for_visa')->label('Vize için kabul edilir')->default(true),
                Toggle::make('accepted_for_enrollment')->label('Üniversite kaydı için geçerli'),
                Toggle::make('covers_dental')->label('Diş'),
                Toggle::make('covers_pregnancy')->label('Hamilelik'),
                Toggle::make('covers_mental_health')->label('Ruh sağlığı'),
                Toggle::make('covers_repatriation')->label('Repatriation (ülkeye dönüş)'),
            ])->columns(3),

            Section::make('Pratik')->schema([
                Toggle::make('digital_signup')->label('Online kayıt'),
                Toggle::make('english_support')->label('İngilizce destek'),
                TagsInput::make('supported_languages')->label('Desteklenen diller')
                    ->placeholder('de, en, tr…')
                    ->helperText('ISO kodları: tr, en, de, fr, ar, ru, zh'),
            ])->columns(3),

            Section::make('Açıklama (çok dilli)')->schema([
                Textarea::make('description_tr')->label('Açıklama (TR)')->rows(2)->columnSpanFull(),
                Textarea::make('description_en')->label('Açıklama (EN)')->rows(2)->columnSpanFull(),
                Textarea::make('description_de')->label('Açıklama (DE)')->rows(2)->columnSpanFull(),
                Textarea::make('description_long')->label('Uzun açıklama (Markdown)')->rows(6)->columnSpanFull(),
                TagsInput::make('pros')->label('Avantajlar (TR)')->columnSpanFull(),
                TagsInput::make('cons')->label('Dezavantajlar (TR)')->columnSpanFull(),
                TagsInput::make('features')->label('Ek özellikler')->columnSpanFull(),
                Textarea::make('visa_recognition_note')->label('Vize tanınma notu')->rows(2)->columnSpanFull(),
                Textarea::make('turkish_students_note')->label('Türk öğrenciler için özel not')->rows(3)->columnSpanFull(),
            ]),

            Section::make('Yönetim')->schema([
                Toggle::make('is_published')->label('Yayında')
                    ->helperText('Sadece yayındaki sağlayıcılar frontend\'de görünür'),
                Toggle::make('is_featured')->label('Öne Çıkan'),
                TextInput::make('sort_order')->numeric()->default(0),
                DateTimePicker::make('last_verified_at')->label('Son doğrulama'),
            ])->columns(2)->collapsed(),
        ]);
    }
}
