<?php

namespace App\Filament\Resources\BlockedAccountProviders\Schemas;

use App\Models\BlockedAccountProvider;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BlockedAccountProviderForm
{
    public static function configure(Schema $schema): Schema
    {
        $typeOptions = [];
        foreach (BlockedAccountProvider::TYPES as $key => $meta) {
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
                TextInput::make('backend_bank')->label('Arka uç banka')
                    ->placeholder('Örn: Sutor Bank, UniCredit')
                    ->helperText('FinTech ise hangi bankayı kullanıyor'),
                TextInput::make('logo_url')->label('Logo URL')->url()->columnSpanFull(),
            ])->columns(2),

            Section::make('Bağlantılar')->schema([
                TextInput::make('website_url')->label('Resmi Website')->url()->required(),
                TextInput::make('affiliate_url')->label('Affiliate / Referans URL')->url()
                    ->helperText('Boşsa CTA buton website_url\'e gider'),
            ])->columns(2),

            Section::make('Fiyatlandırma (EUR)')->schema([
                TextInput::make('setup_fee_eur')->label('Açılış ücreti')->numeric()->step(0.01)->prefix('€'),
                TextInput::make('monthly_fee_eur')->label('Aylık ücret')->numeric()->step(0.01)->prefix('€'),
                TextInput::make('yearly_fee_eur')->label('Yıllık tek seferde')->numeric()->step(0.01)->prefix('€')
                    ->helperText('Tek seferde yıllık ödeme yapıyorsa'),
                TextInput::make('required_yearly_deposit_eur')->label('Gerekli yıllık deposit')->numeric()->prefix('€')
                    ->placeholder('11904'),
                TextInput::make('monthly_withdrawal_limit_eur')->label('Aylık çekme limiti')->numeric()->prefix('€')
                    ->placeholder('992'),
            ])->columns(3),

            Section::make('Aktivasyon & Süre')->schema([
                TextInput::make('activation_days_min')->label('Aktivasyon min (gün)')->numeric(),
                TextInput::make('activation_days_max')->label('Aktivasyon max (gün)')->numeric(),
            ])->columns(2),

            Section::make('Sağlık Sigortası Combo')->schema([
                Toggle::make('combo_insurance')->label('Sigorta combo paketi var'),
                TextInput::make('insurance_provider_name')->label('Sigorta sağlayıcı(lar)')
                    ->placeholder('Örn: TK, ottonova, DR-WALTER'),
                TextInput::make('insurance_monthly_eur')->label('Sigorta aylık (€)')->numeric()->step(0.01)->prefix('€'),
            ])->columns(3),

            Section::make('Özellikler')->schema([
                Toggle::make('has_mobile_app')->label('Mobil uygulama'),
                Toggle::make('bafin_licensed')->label('BaFin lisanslı'),
                TagsInput::make('supported_languages')->label('Desteklenen diller')
                    ->placeholder('tr, en, de, ar, ru…')
                    ->helperText('ISO kodları: tr, en, de, fr, es, ar, ru, zh, fa'),
            ])->columns(2),

            Section::make('Açıklama')->schema([
                Textarea::make('description')->label('Kısa açıklama (1-2 cümle)')->rows(2)->columnSpanFull(),
                Textarea::make('description_long')->label('Uzun açıklama (Markdown)')->rows(8)->columnSpanFull(),
                TagsInput::make('pros')->label('Avantajlar')
                    ->placeholder('Tek tek artılar')
                    ->columnSpanFull(),
                TagsInput::make('cons')->label('Dezavantajlar')
                    ->placeholder('Tek tek eksiler')
                    ->columnSpanFull(),
                TagsInput::make('features')->label('Ek özellikler')
                    ->placeholder('Apple Pay, IBAN, 24/7 destek…')
                    ->columnSpanFull(),
                Textarea::make('visa_recognition_note')->label('Vize tanınma notu')->rows(2)->columnSpanFull(),
                Textarea::make('turkish_students_note')->label('Türk öğrenciler için özel not')->rows(3)->columnSpanFull(),
            ]),

            Section::make('Yönetim')->schema([
                Toggle::make('is_published')->label('Yayında')
                    ->helperText('Sadece yayındaki sağlayıcılar frontend\'de görünür'),
                Toggle::make('is_featured')->label('Öne Çıkan'),
                TextInput::make('sort_order')->numeric()->default(0),
                DateTimePicker::make('last_verified_at')->label('Son doğrulama')
                    ->helperText('Bu sağlayıcının verileri ne zaman elle kontrol edildi'),
            ])->columns(2)->collapsed(),
        ]);
    }
}
