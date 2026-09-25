<?php

namespace Tests\Feature;

use App\Http\Middleware\TrackPageView;
use App\Support\Consent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Tests\TestCase;

/**
 * almanyauni_uid yaşam döngüsü — rıza verilir, geri çekilir, yeniden verilir.
 *
 * Canlı denetimde (2026-09-25) bulunan açık: çerez HttpOnly olduğu için banner'ın JS
 * temizliği onu silemiyordu; geri çekmeden sonra 1 yıl kalıyor, yeniden rıza verilince
 * AYNI kimlik kullanılıyordu. Artık rıza yokken sunucu eski kimliği response'ta,
 * oluşturulduğu kapsamla birebir aynı kapsamla siler.
 *
 * DB tarafı page_views üzerinden doğrulanır: kayıt terminate()'te yazılır ve Laravel'in
 * test istemcisi Kernel::terminate'i çağırır.
 */
class ConsentUidLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private const UID = 'almanyauni_uid';

    private const UA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0 Safari/537.36';

    /**
     * Tek sayfa ziyareti: çerezler şifresiz gönderilir (uid ve rıza çerezleri şifreleme
     * istisnasında), gerçek tarayıcı UA'sı kullanılır.
     *
     * @param  array<string, string>  $cookies
     */
    private function visit(array $cookies, string $url = '/tr'): TestResponse
    {
        // Her ziyaret ayrı bir tarayıcı durumu: test istemcisi önceki withUnencryptedCookie
        // değerlerini taşır; gerçek tarayıcı ise expire edilen çerezi bir daha göndermez.
        $this->unencryptedCookies = [];
        $this->defaultCookies = [];

        $req = $this->withHeader('User-Agent', self::UA);

        foreach ($cookies as $name => $value) {
            $req = $req->withUnencryptedCookie($name, $value);
        }

        // Test istemcisi Kernel::terminate'i de çağırır → page_views INSERT'i burada gerçekleşir.
        return $req->get($url);
    }

    private function uidCookie(TestResponse $response): ?Cookie
    {
        foreach ($response->headers->getCookies() as $cookie) {
            if ($cookie->getName() === self::UID) {
                return $cookie;
            }
        }

        return null;
    }

    private function pageViews(): int
    {
        return DB::table('page_views')->count();
    }

    /* ------------------------------------------------------ A. karar yok */

    public function test_fresh_visitor_gets_no_uid_and_is_not_tracked(): void
    {
        $response = $this->visit([])->assertOk();

        $this->assertNull($this->uidCookie($response));
        $this->assertSame(0, $this->pageViews());
    }

    public function test_undecided_visitor_with_a_stale_uid_has_it_expired(): void
    {
        $response = $this->visit([self::UID => str_repeat('a', 32)])->assertOk();

        $response->assertCookieExpired(self::UID);
        $this->assertSame(0, $this->pageViews());
    }

    /* --------------------------------------------------------- B. red */

    public function test_rejected_visitor_gets_no_uid_and_is_not_tracked(): void
    {
        $response = $this->visit([Consent::COOKIE => Consent::DENIED])->assertOk();

        $this->assertNull($this->uidCookie($response));
        $this->assertSame(0, $this->pageViews());
    }

    /* ------------------------------------------------------- C. rıza */

    public function test_granted_visitor_gets_a_uid_and_is_tracked(): void
    {
        $response = $this->visit([Consent::COOKIE => Consent::GRANTED])->assertOk();

        $uid = $this->uidCookie($response);
        $this->assertNotNull($uid);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{32}$/', $uid->getValue());
        $this->assertSame(1, $this->pageViews());
        $this->assertSame($uid->getValue(), DB::table('page_views')->value('session_id'));
    }

    /* ------------------------------------------- D. geri çekme → silme */

    public function test_withdrawal_expires_the_existing_uid_and_stops_tracking(): void
    {
        $uidA = $this->uidCookie($this->visit([Consent::COOKIE => Consent::GRANTED]))->getValue();
        $this->assertSame(1, $this->pageViews());

        $response = $this->visit([Consent::COOKIE => Consent::DENIED, self::UID => $uidA])->assertOk();

        $response->assertCookieExpired(self::UID);
        $this->assertSame('', (string) $this->uidCookie($response)->getValue());
        $this->assertSame(1, $this->pageViews(), 'geri çekmeden sonra yeni page view yazıldı');
    }

    public function test_marketing_consent_does_not_keep_the_analytics_uid_alive(): void
    {
        $response = $this->visit([
            Consent::COOKIE           => Consent::DENIED,
            Consent::COOKIE_MARKETING => Consent::GRANTED,
            self::UID                 => str_repeat('b', 32),
        ])->assertOk();

        $response->assertCookieExpired(self::UID);
        $this->assertSame(0, $this->pageViews());
    }

    public function test_deletion_uses_exactly_the_scope_the_cookie_was_created_with(): void
    {
        $created = $this->uidCookie($this->visit([Consent::COOKIE => Consent::GRANTED]));
        $deleted = $this->uidCookie($this->visit([Consent::COOKIE => Consent::DENIED, self::UID => $created->getValue()]));

        // Tarayıcı çerezi ad + domain + path ile eşler; biri farklıysa silme boşa gider.
        $this->assertSame($created->getPath(), $deleted->getPath());
        $this->assertSame($created->getDomain(), $deleted->getDomain());
        $this->assertSame('/', $deleted->getPath());
        $this->assertNull($deleted->getDomain(), 'host-only olmalı (SESSION_DOMAIN yok)');
        $this->assertSame($created->isSecure(), $deleted->isSecure());
        $this->assertSame($created->isHttpOnly(), $deleted->isHttpOnly());
        $this->assertSame($created->getSameSite(), $deleted->getSameSite());
        $this->assertTrue($deleted->isCleared());
    }

    /* ------------------------------------- E. yeniden rıza → yeni kimlik */

    public function test_regranting_after_withdrawal_creates_a_new_identity(): void
    {
        $uidA = $this->uidCookie($this->visit([Consent::COOKIE => Consent::GRANTED]))->getValue();

        // Geri çekme: tarayıcı expire'ı uygular, uid artık gönderilmez.
        $this->visit([Consent::COOKIE => Consent::DENIED, self::UID => $uidA])->assertCookieExpired(self::UID);

        $uidB = $this->uidCookie($this->visit([Consent::COOKIE => Consent::GRANTED]))->getValue();

        $this->assertNotSame($uidA, $uidB);
        // İki dönemin kayıtları iki ayrı takma adlı ziyaretçiye ait.
        $this->assertSame([$uidA, $uidB], DB::table('page_views')->orderBy('id')->pluck('session_id')->all());
    }

    /* --------------------------------------------- F / G. çerez nitelikleri */

    public function test_production_cookie_is_secure_and_http_only(): void
    {
        $this->app['env'] = 'production';

        $uid = $this->uidCookie($this->visit([Consent::COOKIE => Consent::GRANTED]));
        $this->assertTrue($uid->isSecure());
        $this->assertTrue($uid->isHttpOnly());
        $this->assertSame('lax', strtolower((string) $uid->getSameSite()));

        $deleted = $this->uidCookie($this->visit([Consent::COOKIE => Consent::DENIED, self::UID => $uid->getValue()]));
        $this->assertTrue($deleted->isSecure());
        $this->assertTrue($deleted->isHttpOnly());
    }

    public function test_local_http_keeps_working_without_secure(): void
    {
        $uid = $this->uidCookie($this->visit([Consent::COOKIE => Consent::GRANTED], 'http://localhost/tr'));

        $this->assertFalse($uid->isSecure(), 'HTTP yerel ortamda Secure çerez tarayıcıya hiç yazılmaz');
        $this->assertTrue($uid->isHttpOnly());
    }

    public function test_https_request_outside_production_is_secure(): void
    {
        $uid = $this->uidCookie($this->visit([Consent::COOKIE => Consent::GRANTED], 'https://localhost/tr'));

        $this->assertTrue($uid->isSecure());
    }

    /* ------------------------------------------------------- H. banner */

    public function test_banner_describes_real_behaviour_in_every_locale(): void
    {
        $expected = [
            'tr' => 'Zorunlu çerezleri sitenin temel işlevleri için kullanıyoruz. Onayınızla analitik araçları ve takma adlı ziyaretçi istatistiklerini de kullanıyoruz.',
            'en' => 'We use necessary cookies for essential site functions. With your consent, we also use analytics tools and pseudonymous visitor statistics.',
            'de' => 'Wir verwenden notwendige Cookies für grundlegende Funktionen der Website. Mit Ihrer Einwilligung verwenden wir außerdem Analysetools und pseudonymisierte Besucherstatistiken.',
        ];

        // Canlıdaki gibi harici izleyici yapılandırılmış.
        DB::table('settings')->updateOrInsert(['key' => 'google_analytics_id'],
            ['value' => 'G-TEST', 'group' => 'integrations', 'created_at' => now(), 'updated_at' => now()]);
        cache()->flush();

        foreach ($expected as $locale => $text) {
            $html = $this->get("/{$locale}")->assertOk()->getContent();
            $banner = substr($html, strpos($html, 'id="cookieConsent"'), 4000);

            $this->assertStringContainsString($text, html_entity_decode($banner), "/{$locale}: banner metni");

            foreach (['anonymous visitor statistics', 'anonim ziyaretçi', 'anonyme Besucher', 'no personal info is stored'] as $old) {
                $this->assertStringNotContainsString($old, $banner, "/{$locale}: eski ifade '{$old}'");
            }
        }
    }

    public function test_banner_without_external_trackers_is_also_truthful(): void
    {
        DB::table('settings')->whereIn('key', [
            'google_analytics_id', 'microsoft_clarity_id', 'google_ads_id',
            'google_tag_manager_id', 'meta_pixel_id', 'tiktok_pixel_id',
        ])->update(['value' => null]);
        cache()->flush();

        $html = $this->get('/en')->assertOk()->getContent();
        $banner = substr($html, strpos($html, 'id="cookieConsent"'), 4000);

        $this->assertStringContainsString('With your consent, we also use pseudonymous visitor statistics.', $banner);
        $this->assertStringNotContainsString('anonymous visitor statistics', $banner);
    }
}
