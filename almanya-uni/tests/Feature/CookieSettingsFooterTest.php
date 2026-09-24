<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Footer'daki kalıcı "Çerez Ayarları" erişim noktası (GDPR 7(3): rızayı geri
 * çekmek vermek kadar kolay olmalı).
 *
 * Tıklamanın paneli gerçekten açtığı PHPUnit'te sınanamaz (JS); burada sözleşme
 * sabitleniyor: her dilde doğru etiket, sayfa açmayan bir <button>, handler'ın
 * dinlediği [data-cookie-settings] işareti ve açılacak panelin sayfada olması.
 */
class CookieSettingsFooterTest extends TestCase
{
    use RefreshDatabase;

    private const LABELS = ['tr' => 'Çerez Ayarları', 'en' => 'Cookie Settings', 'de' => 'Cookie-Einstellungen'];

    protected function setUp(): void
    {
        parent::setUp();

        $id = DB::table('legal_pages')->insertGetId([
            'key' => 'cookies', 'titles' => '{}', 'descriptions' => '{}', 'bodies' => '{}',
            'is_published' => true, 'sort_order' => 0, 'created_at' => now(), 'updated_at' => now(),
        ]);

        foreach (array_keys(self::LABELS) as $locale) {
            DB::table('legal_page_translations')->insert([
                'legal_page_id' => $id, 'locale' => $locale, 'title' => "Cookies {$locale}",
                'body' => "## {$locale}", 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    public function test_footer_shows_a_localised_cookie_settings_button_in_every_locale(): void
    {
        foreach (self::LABELS as $locale => $label) {
            $html = $this->get("/{$locale}/cookie-policy")->assertOk()->getContent();

            $footer = substr($html, strpos($html, '<footer'), strpos($html, '</footer>') - strpos($html, '<footer'));

            $this->assertMatchesRegularExpression(
                '~<button type="button" data-cookie-settings[^>]*>\s*' . preg_quote($label, '~') . '\s*</button>~u',
                $footer,
                "/{$locale}: footer'da '{$label}' butonu yok"
            );

            // Başka dilin etiketi sızmamalı.
            foreach (self::LABELS as $other => $otherLabel) {
                if ($other !== $locale && $otherLabel !== $label) {
                    $this->assertStringNotContainsString(">{$otherLabel}<", $footer, "/{$locale}: {$other} etiketi görünüyor");
                }
            }
        }
    }

    public function test_the_button_opens_the_existing_panel_instead_of_navigating(): void
    {
        $html = $this->get('/en/cookie-policy')->assertOk()->getContent();

        // Link değil: href yok, yeni sayfa açılmaz.
        $this->assertDoesNotMatchRegularExpression('~<a[^>]*data-cookie-settings~', substr($html, strpos($html, '<footer')));

        // Butonun bağlandığı handler ve açacağı panel sayfada.
        $this->assertStringContainsString("e.target.closest('[data-cookie-settings]')", $html);
        $this->assertStringContainsString('window.openCookieSettings', $html);
        $this->assertStringContainsString('id="cookieConsent"', $html);
        $this->assertStringContainsString('id="cookiePrefs"', $html);
        $this->assertStringContainsString('id="prefAnalytics"', $html);
    }
}
