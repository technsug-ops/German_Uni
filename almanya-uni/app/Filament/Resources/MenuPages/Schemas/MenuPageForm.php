<?php

namespace App\Filament\Resources\MenuPages\Schemas;

use App\Models\MenuPage;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class MenuPageForm
{
    public static function configure(Schema $schema): Schema
    {
        $groupOptions = [];
        foreach (MenuPage::GROUPS as $key => $meta) {
            $groupOptions[$key] = $meta['emoji'] . ' ' . $meta['label'];
        }

        return $schema->components([
            Section::make('Görüntü — Genel')->schema([
                TextInput::make('label')->label('Görünen ad (varsayılan)')->required()
                    ->helperText('Locale-specific override yoksa kullanılır + lang/{locale}.json çevirisine düşer'),
                TextInput::make('icon')->label('İkon (emoji)')->maxLength(16),
                TextInput::make('description')->label('Kısa açıklama (varsayılan)'),
                TextInput::make('badge')->label('Rozet')->placeholder('YENİ, BETA, vs.')->maxLength(32),
            ])->columns(2),

            Tabs::make('Locale Overrides (opsiyonel)')->tabs([
                Tab::make('🇹🇷 Türkçe')->schema([
                    TextInput::make('label_tr')->label('Görünen ad')->maxLength(100)
                        ->placeholder('Boş = varsayılan + JSON çevirisi kullanılır'),
                    TextInput::make('description_tr')->label('Açıklama')->maxLength(200),
                ]),
                Tab::make('🇬🇧 English')->schema([
                    TextInput::make('label_en')->label('Display name')->maxLength(100)
                        ->placeholder('Empty = use default + JSON translation'),
                    TextInput::make('description_en')->label('Description')->maxLength(200),
                ]),
                Tab::make('🇩🇪 Deutsch')->schema([
                    TextInput::make('label_de')->label('Anzeigename')->maxLength(100)
                        ->placeholder('Leer = Standard + JSON-Übersetzung'),
                    TextInput::make('description_de')->label('Beschreibung')->maxLength(200),
                ]),
            ])->columnSpanFull(),

            Section::make('Bağlantı')->schema([
                Select::make('link_type')->label('Bağlantı tipi')->options([
                    'route' => 'Laravel Route',
                    'url'   => 'Statik URL (örn. /forum/)',
                ])->required()->live(),
                TextInput::make('key')->label('Route name / anahtar')->required()
                    ->disabled()
                    ->helperText('Bu alan değiştirilemez — kod bağlılığı var')
                    ->dehydrated(),
                TextInput::make('url')->label('Statik URL')->url()
                    ->visible(fn ($get) => $get('link_type') === 'url')
                    ->helperText('Sadece statik link tipi seçildiğinde gerekli'),
            ])->columns(2),

            Section::make('Konum')->schema([
                Select::make('group')->label('Menü grubu')->options($groupOptions)->required(),
                TextInput::make('sort_order')->label('Sıralama')->numeric()->default(0)
                    ->helperText('Düşük → önce gelir'),
            ])->columns(2),

            Section::make('Yayın Durumu')->schema([
                Toggle::make('is_enabled')->label('🟢 Yayında')
                    ->helperText('Kapalı → menüden gizlenir + URL\'ye girilirse 404')
                    ->inline(false)
                    ->onColor('success')
                    ->offColor('danger'),
                Toggle::make('protect_route')->label('URL\'yi de kilitle')
                    ->helperText('Açık (önerilen): kapatınca URL\'ye direkt giriş 404. Kapalı: sadece menüden gizler, URL hâlâ açık.')
                    ->inline(false),
                Toggle::make('hide_on_mobile')->label('📱 Mobil menüde gizle')
                    ->helperText('Açık → mobil drawer\'da gizlenir (masaüstü menü, footer ve "Tümünü gör" listesinde kalır).')
                    ->inline(false),
            ])->columns(2),
        ]);
    }
}
