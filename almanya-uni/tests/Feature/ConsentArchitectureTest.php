<?php

namespace Tests\Feature;

use App\Support\Consent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Rıza mimarisi — davranışsal güvence.
 *
 * NEDEN BU TESTLER VAR: 2026-09-23 denetiminde iki gerçek uyumsuzluk çıktı.
 * (1) Karar vermemiş ziyaretçi için analitik çerez yazılıp sayfa görüntülemesi
 * kaydediliyordu; kod yalnızca "rejected" değerini engelliyordu. (2) gtag.js
 * rıza olmadan yükleniyordu (Advanced Consent Mode), yani reddeden ziyaretçinin
 * verisi de Google'a ulaşıyordu. İkisi de gizlilik metniyle çelişiyordu.
 *
 * Bu testler "varsayılan reddir" kuralını kilitler: rıza yoksa ne çerez yazılır,
 * ne kayıt tutulur, ne de üçüncü taraf script'i sayfaya girer.
 */
class ConsentArchitectureTest extends TestCase
{
    use RefreshDatabase;

    /** Setting::get() cache'i rememberForever kullanıyor; yazdıktan sonra temizlenmeli. */
    private function setTracker(string $key, ?string $value): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => $key],
            ['value' => $value, 'group' => 'integrations', 'updated_at' => now(), 'created_at' => now()]
        );
        cache()->flush();
    }

    /**
     * Gozlem noktasi: almanyauni_uid cerezi.
     *
     * NEDEN DB DEGIL: sayfa goruntulemesi INSERT'u terminate()'te yapiliyor ve
     * feature testlerinde terminate() calismiyor. Cerez ise handle() icinde
     * yaziliyor, yani ayni karardan turuyor ve gizlilik acisindan belirleyici
     * olan da tam olarak bu: riza yokken 1 yillik takip cerezi yazilmamali.
     */
    private const UID = 'almanyauni_uid';

    // ── A. Yeni ziyaretçi: karar yok → izleme yok ──────────────────────
    public function test_new_visitor_without_consent_is_not_tracked(): void
    {
        $this->get('/tr')->assertOk()->assertCookieMissing(self::UID);
    }

    // ── B. Reddetti → izleme yok ───────────────────────────────────────
    public function test_rejected_visitor_is_not_tracked(): void
    {
        $this->withUnencryptedCookie(Consent::COOKIE, Consent::DENIED)
            ->get('/tr')->assertOk()->assertCookieMissing(self::UID);
    }

    // ── C. Analitik rızası verdi → izleme çalışır ──────────────────────
    public function test_granted_visitor_is_tracked(): void
    {
        $this->withUnencryptedCookie(Consent::COOKIE, Consent::GRANTED)
            ->get('/tr')->assertOk()->assertCookie(self::UID);
    }

    // ── C2. Eski biçim 'accepted' da rıza sayılır (geriye uyumluluk) ───
    public function test_legacy_accepted_cookie_still_counts_as_consent(): void
    {
        $this->withUnencryptedCookie(Consent::COOKIE, 'accepted')
            ->get('/tr')->assertOk()->assertCookie(self::UID);
    }

    // ── D. Rıza geri çekildi → yeni izleme yok ─────────────────────────
    public function test_withdrawn_consent_stops_tracking(): void
    {
        $this->withUnencryptedCookie(Consent::COOKIE, Consent::GRANTED)->get('/tr')->assertOk()->assertCookie(self::UID);
        $this->withUnencryptedCookie(Consent::COOKIE, Consent::DENIED)->get('/tr')->assertOk()->assertCookieMissing(self::UID);
    }

    // ── E. GA kimliği var ama rıza yok → GA bootstrap YOK ──────────────
    public function test_ga_is_not_bootstrapped_without_consent(): void
    {
        $this->setTracker('google_analytics_id', 'G-TESTID123');

        $html = $this->get('/tr')->assertOk()->getContent();

        // Ölçüt: fonksiyonun ÇAĞRILMASI. Adresin bootstrap fonksiyonu içinde metin
        // olarak geçmesi kaçınılmaz; belirleyici olan çalıştırılıp çalıştırılmaması.
        $this->assertStringNotContainsString('consent-autostart:analytics', $html, 'GA rıza olmadan başlatılmamalı (Basic Consent Mode).');
        $this->assertStringNotContainsString('<script async src="https://www.googletagmanager.com', $html, 'Statik gtag.js etiketi hiç basılmamalı.');
        $this->assertStringContainsString('__startAnalytics', $html, 'Bootstrap fonksiyonu tanımlı olmalı (rızayla çağrılacak).');
    }

    public function test_ga_is_bootstrapped_with_consent(): void
    {
        $this->setTracker('google_analytics_id', 'G-TESTID123');

        $html = $this->withUnencryptedCookie(Consent::COOKIE, Consent::GRANTED)->get('/tr')->assertOk()->getContent();

        $this->assertStringContainsString('consent-autostart:analytics', $html, 'Rıza varsa analitik başlatılmalı.');
    }

    // ── F. Clarity kimliği var ama rıza yok → Clarity bootstrap YOK ────
    public function test_clarity_is_not_bootstrapped_without_consent(): void
    {
        $this->setTracker('microsoft_clarity_id', 'clarity123');

        $html = $this->get('/tr')->assertOk()->getContent();

        $this->assertStringNotContainsString('consent-autostart:analytics', $html, 'Clarity rıza olmadan başlatılmamalı.');
    }

    // ── G. GA boş + Clarity dolu → harici izleyici VAR sayılmalı ───────
    public function test_clarity_alone_counts_as_external_tracker(): void
    {
        $this->setTracker('google_analytics_id', null);
        $this->setTracker('microsoft_clarity_id', 'clarity123');

        $html = $this->get('/tr')->assertOk()->getContent();

        $this->assertStringNotContainsString(
            'no Google Analytics, hosted on our own server',
            $html,
            'Clarity açıkken "harici izleyici yok" metni gösterilmemeli.'
        );
    }

    // ── H & I. Pazarlama kimliği var ama pazarlama rızası yok → piksel YOK
    public function test_meta_pixel_requires_marketing_consent(): void
    {
        $this->setTracker('meta_pixel_id', '123456789');

        $html = $this->withUnencryptedCookie(Consent::COOKIE, Consent::GRANTED)->get('/tr')->assertOk()->getContent();

        $this->assertStringNotContainsString('consent-autostart:marketing', $html, 'Analitik rızası pazarlama pikselini açmamalı.');
    }

    public function test_tiktok_pixel_requires_marketing_consent(): void
    {
        $this->setTracker('tiktok_pixel_id', 'TT123');

        $html = $this->get('/tr')->assertOk()->getContent();

        $this->assertStringNotContainsString('consent-autostart:marketing', $html);
    }

    public function test_marketing_runs_only_with_marketing_consent(): void
    {
        $this->setTracker('meta_pixel_id', '123456789');

        $html = $this->withUnencryptedCookie(Consent::COOKIE, Consent::GRANTED)
            ->withUnencryptedCookie(Consent::COOKIE_MARKETING, Consent::GRANTED)
            ->get('/tr')->assertOk()->getContent();

        $this->assertStringContainsString('consent-autostart:marketing', $html);
    }

    // ── Consent yardımcı sınıfı ────────────────────────────────────────
    public function test_consent_defaults_to_denied(): void
    {
        $this->assertFalse(Consent::analytics(request()));
        $this->assertFalse(Consent::marketing(request()));
        $this->assertFalse(Consent::decided(request()));
    }
}
