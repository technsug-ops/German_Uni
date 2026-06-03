<?php

namespace App\Filament\Resources\LanguageCourses\Schemas;

use App\Models\LanguageCourse;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class LanguageCourseForm
{
    public static function configure(Schema $schema): Schema
    {
        $typeOptions = [];
        foreach (LanguageCourse::TYPES as $key => $meta) {
            $typeOptions[$key] = $meta['emoji'] . ' ' . $meta['label'];
        }

        return $schema->components([
            Section::make('Kimlik')->schema([
                Select::make('type')->label('Kategori')->required()
                    ->options($typeOptions)
                    ->helperText('Üniversite (uni dil merkezi) · Özel (Goethe, telc okulları) · Online'),
                TextInput::make('name')->label('Kurum / Marka Adı')->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state ?? ''))),
                TextInput::make('slug')->required()->unique(ignoreRecord: true),
            ])->columns(3),

            Section::make('Görsel & Affiliate')->schema([
                TextInput::make('logo_url')->label('Logo URL')->url()
                    ->helperText('Logonun URL\'i (en kolay yol). Alternatif: aşağıdan görsel yükle.'),
                FileUpload::make('image_path')->label('Banner / Görsel (yükle)')
                    ->image()->disk('public')->directory('partners/language-courses')->visibility('public')
                    ->helperText('Affiliate firmanın istediği görsel. Yüklenemezse logo URL kullanılır.'),
                TextInput::make('affiliate_url')->label('Affiliate URL (opsiyonel)')->url()
                    ->helperText('Doluysa "Web sitesi" butonu buraya yönlenir (tık takipli). Boşsa website kullanılır.')
                    ->columnSpanFull(),
            ])->columns(2),

            Section::make('İletişim')->schema([
                TextInput::make('website')->label('Website')->url(),
                TextInput::make('email')->label('E-posta')->email(),
                TextInput::make('phone')->label('Telefon'),
            ])->columns(3),

            Section::make('Şehir · Seviye · Özellik')->schema([
                TagsInput::make('cities')->label('Şehirler')
                    ->placeholder('Berlin, München, Online…')->columnSpanFull(),
                CheckboxList::make('levels')->label('Seviyeler (CEFR)')
                    ->options(array_combine(LanguageCourse::LEVELS, LanguageCourse::LEVELS))
                    ->columns(6)->gridDirection('row'),
                TagsInput::make('features')->label('Özellikler')
                    ->placeholder('telc_sinav_merkezi, goethe, sertifikali, hizli_kayit…')->columnSpanFull(),
            ]),

            Section::make('Fiyat')->schema([
                TextInput::make('price_min')->label('Fiyat min (€)')->numeric(),
                TextInput::make('price_max')->label('Fiyat max (€)')->numeric(),
                TextInput::make('price_note')->label('Fiyat notu')->placeholder('Örn: kur başına, aylık…'),
            ])->columns(3)->collapsed(),

            Section::make('Açıklama (dile göre)')->schema([
                Textarea::make('description_tr')->label('Açıklama (TR)')->rows(4)->columnSpanFull(),
                Textarea::make('description_en')->label('Açıklama (EN)')->rows(3)->columnSpanFull(),
                Textarea::make('description_de')->label('Açıklama (DE)')->rows(3)->columnSpanFull(),
            ])->collapsed(),

            Section::make('Yönetim')->schema([
                Toggle::make('is_featured')->label('Öne Çıkan'),
                Toggle::make('is_active')->label('Aktif')->default(true),
                TextInput::make('sort_order')->numeric()->default(0),
            ])->columns(3)->collapsed(),
        ]);
    }
}
