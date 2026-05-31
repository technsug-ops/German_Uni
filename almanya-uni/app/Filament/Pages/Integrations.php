<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * Pazarlama & analitik entegrasyonları — tek formdan yönetim.
 *
 * Buraya girilen ID'ler `settings` tablosuna yazılır ve frontend layout'unda
 * (partials/_tracking_*.blade.php) Google Consent Mode v2 + KVKK çerez onayına
 * bağlı olarak render edilir. ID boşsa o entegrasyon hiç basılmaz.
 *
 * Hassas anahtarlar olduğu için SADECE tam admin erişebilir (isFullAdmin).
 */
class Integrations extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    protected static ?string $navigationLabel = 'Entegrasyonlar';

    protected static ?string $title = 'Pazarlama & Analitik Entegrasyonları';

    protected static ?int $navigationSort = 90;

    protected static string|\UnitEnum|null $navigationGroup = 'Ayarlar';

    protected string $view = 'filament.pages.integrations';

    /** Formun bağlandığı state. */
    public ?array $data = [];

    /**
     * Bu sayfada yönetilen tüm ayar anahtarları.
     * Form alan adları = settings tablosundaki key'ler (birebir).
     */
    public const KEYS = [
        // Google
        'google_analytics_id',
        'google_ads_id',
        'google_ads_conversion_label',
        'google_tag_manager_id',
        'google_site_verification',
        'bing_site_verification',
        'yandex_site_verification',
        // Meta
        'meta_pixel_id',
        // TikTok
        'tiktok_pixel_id',
        // Davranış
        'tracking_require_consent',
    ];

    public static function canAccess(): bool
    {
        return auth()->user()?->isFullAdmin() === true;
    }

    public function mount(): void
    {
        $state = [];
        foreach (self::KEYS as $key) {
            $state[$key] = Setting::get($key);
        }
        // Onay zorunluluğu varsayılan: açık (KVKK/GDPR güvenli taraf)
        $state['tracking_require_consent'] = (bool) Setting::get('tracking_require_consent', '1');

        $this->form->fill($state);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Google')
                    ->description('Google Analytics 4, Google Ads, Tag Manager ve Search Console. Tek bir ölçüm aracı istiyorsanız önerimiz GTM — diğerlerini GTM içinden de yönetebilirsiniz.')
                    ->icon(Heroicon::OutlinedGlobeAlt)
                    ->columns(2)
                    ->components([
                        TextInput::make('google_analytics_id')
                            ->label('Google Analytics 4 — Ölçüm Kimliği')
                            ->placeholder('G-XXXXXXXXXX')
                            ->helperText('GA4 → Yönetici → Veri akışları → Ölçüm kimliği.')
                            ->rule('regex:/^G-[A-Z0-9]+$/i')
                            ->validationMessages(['regex' => 'Format G-XXXXXXXXXX olmalı.']),
                        TextInput::make('google_tag_manager_id')
                            ->label('Google Tag Manager — Kapsayıcı Kimliği')
                            ->placeholder('GTM-XXXXXXX')
                            ->helperText('GTM çalışma alanı → sağ üstteki GTM-... kodu.')
                            ->rule('regex:/^GTM-[A-Z0-9]+$/i')
                            ->validationMessages(['regex' => 'Format GTM-XXXXXXX olmalı.']),
                        TextInput::make('google_ads_id')
                            ->label('Google Ads — Dönüşüm Kimliği')
                            ->placeholder('AW-XXXXXXXXXX')
                            ->helperText('Google Ads → Araçlar → Dönüşümler. GTM kullanıyorsanız boş bırakabilirsiniz.')
                            ->rule('regex:/^AW-[A-Z0-9]+$/i')
                            ->validationMessages(['regex' => 'Format AW-XXXXXXXXXX olmalı.']),
                        TextInput::make('google_ads_conversion_label')
                            ->label('Google Ads — Dönüşüm Etiketi (opsiyonel)')
                            ->placeholder('abcDEF12gh')
                            ->helperText('Belirli bir dönüşüm eylemi için. Genel kurulum için gerekmez.'),
                        TextInput::make('google_site_verification')
                            ->label('Google Search Console — Doğrulama')
                            ->placeholder('content="..." içindeki değer')
                            ->helperText('Search Console → Mülk ekle → HTML etiketi → content="..." kısmı. (Google Merchant Center doğrulaması da aynı etiketi kullanır.)')
                            ->columnSpanFull(),
                    ]),

                Section::make('Diğer Arama Motorları')
                    ->description('Site sahipliği doğrulama etiketleri.')
                    ->icon(Heroicon::OutlinedMagnifyingGlass)
                    ->collapsed()
                    ->collapsible()
                    ->columns(2)
                    ->components([
                        TextInput::make('bing_site_verification')
                            ->label('Bing Webmaster — Doğrulama')
                            ->placeholder('msvalidate.01 değeri'),
                        TextInput::make('yandex_site_verification')
                            ->label('Yandex Webmaster — Doğrulama')
                            ->placeholder('yandex-verification değeri'),
                    ]),

                Section::make('Meta (Facebook / Instagram)')
                    ->description('Meta Pixel — reklam dönüşümü ve yeniden pazarlama. Çerez onayına bağlı yüklenir.')
                    ->icon(Heroicon::OutlinedRectangleGroup)
                    ->components([
                        TextInput::make('meta_pixel_id')
                            ->label('Meta Pixel Kimliği')
                            ->placeholder('15-16 haneli sayı')
                            ->helperText('Meta Events Manager → Veri kaynakları → Pixel kimliği.')
                            ->rule('regex:/^[0-9]+$/')
                            ->validationMessages(['regex' => 'Yalnızca rakam.']),
                    ]),

                Section::make('TikTok Ads')
                    ->description('TikTok Pixel — reklam dönüşümü. Çerez onayına bağlı yüklenir.')
                    ->icon(Heroicon::OutlinedMusicalNote)
                    ->components([
                        TextInput::make('tiktok_pixel_id')
                            ->label('TikTok Pixel Kimliği')
                            ->placeholder('Örn: CABCD1234EFGH5678')
                            ->helperText('TikTok Ads Manager → Varlıklar → Etkinlikler → Web etkinlikleri → Pixel kimliği.'),
                    ]),

                Section::make('Gizlilik & Onay')
                    ->description('KVKK/GDPR uyumu için pazarlama/analitik izleyiciler kullanıcı çerez onayına bağlanır.')
                    ->icon(Heroicon::OutlinedShieldCheck)
                    ->components([
                        Toggle::make('tracking_require_consent')
                            ->label('Çerez onayı zorunlu (önerilir)')
                            ->helperText('Açık: GA/Ads/Meta/TikTok yalnızca ziyaretçi çerezleri kabul ettikten sonra çalışır (Google Consent Mode v2 ile varsayılan reddedilir). Kapalı: izleyiciler herkese hemen yüklenir — yalnızca yasal yükümlülüğünüz yoksa.')
                            ->default(true),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Kaydet')
                ->icon(Heroicon::OutlinedCheck)
                ->color('success')
                ->action('save'),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();

        try {
            foreach (self::KEYS as $key) {
                $value = $state[$key] ?? null;

                // Toggle → '1'/'0' string olarak sakla (boş = null mantığını bozmasın)
                if ($key === 'tracking_require_consent') {
                    $value = ! empty($value) ? '1' : '0';
                } elseif (is_string($value)) {
                    $value = trim($value) ?: null;
                }

                Setting::set($key, $value, 'integrations');
            }
        } catch (\Throwable $e) {
            // En olası sebep: settings tablosu henüz migrate edilmemiş (canlı).
            // Opak 500 yerine net yönlendirme göster.
            report($e);
            Notification::make()
                ->title('❌ Kaydedilemedi — settings tablosu yok gibi görünüyor')
                ->body('Sunucuda `php artisan migrate --force` çalıştırılmalı (settings tablosunu oluşturur). Detay: ' . $e->getMessage())
                ->danger()
                ->persistent()
                ->send();

            return;
        }

        Notification::make()
            ->title('✅ Entegrasyonlar kaydedildi')
            ->body('Değişiklikler sitede anında yayında. (Yalnızca dolu kimlikler basılır.)')
            ->success()
            ->send();
    }
}
