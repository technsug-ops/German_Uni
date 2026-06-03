<?php

namespace App\Filament\Resources\TranslationOffices\Schemas;

use App\Models\TranslationOffice;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TranslationOfficeForm
{
    public static function configure(Schema $schema): Schema
    {
        $typeOptions = [];
        foreach (TranslationOffice::TYPES as $key => $meta) {
            $typeOptions[$key] = $meta['emoji'] . ' ' . $meta['label'];
        }

        return $schema->components([
            Section::make('Kimlik')->schema([
                Select::make('type')->label('Tip')->required()->options($typeOptions),
                TextInput::make('name')->label('Büro / Tercüman Adı')->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state ?? ''))),
                TextInput::make('slug')->required()->unique(ignoreRecord: true),
                Toggle::make('is_sworn')->label('Yeminli mi?')->default(true)
                    ->helperText('Resmî yeminli tercüme (beglaubigte Übersetzung) yapıyor mu?'),
            ])->columns(2),

            Section::make('Görsel & Affiliate')->schema([
                TextInput::make('logo_url')->label('Logo URL')->url(),
                FileUpload::make('image_path')->label('Banner / Görsel (yükle)')
                    ->image()->disk('public')->directory('partners/translation-offices')->visibility('public')
                    ->helperText('Affiliate firmanın istediği görsel. Yüklenemezse logo URL kullanılır.'),
                TextInput::make('affiliate_url')->label('Affiliate URL (opsiyonel)')->url()
                    ->helperText('Doluysa "Web sitesi" buraya yönlenir (tık takipli).')->columnSpanFull(),
            ])->columns(2),

            Section::make('İletişim')->schema([
                TextInput::make('website')->label('Website')->url(),
                TextInput::make('email')->label('E-posta')->email(),
                TextInput::make('phone')->label('Telefon'),
            ])->columns(3),

            Section::make('Şehir · Dil · Hizmet')->schema([
                TagsInput::make('cities')->label('Şehirler')
                    ->placeholder('İstanbul, Berlin, Online…')->columnSpanFull(),
                TagsInput::make('languages')->label('Dil çiftleri')
                    ->placeholder('TR-DE, DE-TR, EN-DE…')->columnSpanFull(),
                TagsInput::make('features')->label('Hizmetler')
                    ->placeholder('apostil, noter_onayi, ekspres, kargo, online_teslim…')->columnSpanFull(),
            ]),

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
