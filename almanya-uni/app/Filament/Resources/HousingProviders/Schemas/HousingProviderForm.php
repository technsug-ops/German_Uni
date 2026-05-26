<?php

namespace App\Filament\Resources\HousingProviders\Schemas;

use App\Models\HousingProvider;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class HousingProviderForm
{
    public static function configure(Schema $schema): Schema
    {
        $typeOptions = [];
        foreach (HousingProvider::TYPES as $key => $meta) {
            $typeOptions[$key] = $meta['emoji'] . ' ' . $meta['label'];
        }

        return $schema->components([
            Section::make('Kimlik')->schema([
                Select::make('type')->label('Tipi')->required()
                    ->options($typeOptions)
                    ->helperText('Studierendenwerk = devlet yurdu · Özel Şirket = The Fizz/YouniQ vb. · Portal = WG-Gesucht'),
                TextInput::make('name')->label('Kurum / Marka Adı')->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state ?? ''))),
                TextInput::make('slug')->required()->unique(ignoreRecord: true),
                TextInput::make('logo_url')->label('Logo URL')->url(),
            ])->columns(2),

            Section::make('İletişim')->schema([
                TextInput::make('website')->label('Website')->url()->required(),
                TextInput::make('email')->label('E-posta')->email(),
                TextInput::make('phone')->label('Telefon'),
            ])->columns(3),

            Section::make('Fiyat & Kapasite')->schema([
                TextInput::make('price_min')->label('Fiyat min (€/ay)')->numeric(),
                TextInput::make('price_max')->label('Fiyat max (€/ay)')->numeric(),
                TextInput::make('total_capacity')->label('Toplam kapasite (yer)')->numeric()
                    ->helperText('Studierendenwerk için: yurt yatağı sayısı'),
                TextInput::make('waiting_period')->label('Bekleme süresi')
                    ->placeholder('Örn: 1-2 sem, 3-7 sem')
                    ->helperText('Studierendenwerk için bekleme listesi süresi'),
            ])->columns(2),

            Section::make('Şehir & Özellikler')->schema([
                TagsInput::make('cities')->label('Şehirler')
                    ->placeholder('Berlin, München, Köln…')
                    ->helperText('Bu sağlayıcının bulunduğu şehirler (özel şirketler için çoğul)')
                    ->columnSpanFull(),
                TagsInput::make('features')->label('Özellikler')
                    ->placeholder('mobliyali, fitness, internet_dahil, ucuz, hizli_basvuru…')
                    ->columnSpanFull(),
            ]),

            Section::make('Açıklama')->schema([
                Textarea::make('description')->label('Açıklama (markdown destekli)')
                    ->rows(6)->columnSpanFull(),
            ]),

            Section::make('Yönetim')->schema([
                Toggle::make('is_featured')->label('Öne Çıkan')->helperText('Liste başında ve city sayfasında ön plana çıkar'),
                Toggle::make('is_active')->label('Aktif')->default(true),
                TextInput::make('sort_order')->numeric()->default(0),
            ])->columns(3)->collapsed(),
        ]);
    }
}
