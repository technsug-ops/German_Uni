<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Render smoke testi — kritik sayfalar + API uçları FATAL (5xx) vermeden
 * açılıyor mu? `php -l` (lint) sınıf-yokluğu/render hatalarını yakalamaz;
 * bu test yakalar. Bu oturumda 2 kez 500 yedik (Filament FormAction +
 * ContactController) — bu test deploy öncesi koşsaydı ikisini de durdururdu.
 *
 * Boş DB (RefreshDatabase) yeterli: fatal render hataları veriden bağımsızdır.
 * Veri gerektiren detay sayfaları 404 döner (5xx değil) → sorun değil.
 */
class SmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // CI'da Vite assets build edilmez → @vite manifest hatası verir.
        // Render testlerinde asset'leri stub'la (built-asset bağımlılığı yok).
        $this->withoutVite();
    }

    /** 5xx = fatal. 2xx/3xx/4xx kabul (boş DB'de 404/302 normal). */
    private function assertNotServerError(string $url, ?int $status = null): void
    {
        $response = $this->get($url);
        $code = $response->getStatusCode();
        $this->assertLessThan(500, $code, "FATAL: {$url} → HTTP {$code}");
        if ($status !== null) {
            $this->assertSame($status, $code, "{$url} beklenen {$status}, gelen {$code}");
        }
    }

    public function test_public_pages_render(): void
    {
        foreach ([
            '/',
            route('about'),
            route('faqs.index'),
            route('blog.index'),
            route('news.index'),
            route('scholarships.index'),
            route('universities.index'),
            route('cities.index'),
            route('events.index'),
            route('mentors.index'),
            route('contact'),
            '/tr/search?q=informatik', // FULLTEXT arama yolu (whereFullText + fallback)
            '/tr/search?q=be',          // kısa sorgu → LIKE fallback
            '/tr/search/suggest?q=informatik', // autocomplete FULLTEXT (her tuş)
            '/tr/search/suggest?q=in',         // autocomplete kısa → LIKE fallback
            '/api/docs',
            '/login',
            '/register',
        ] as $url) {
            $this->assertNotServerError($url);
        }
    }

    /** Veri API'si artık İZNE TABİ — token'sız istek 401 olmalı (açık erişim yok). */
    public function test_data_api_requires_authentication(): void
    {
        foreach ([
            '/api/v1/universities',
            '/api/v1/programs',
            '/api/v1/cities',
            '/api/v1/scholarships',
            '/api/v1/blog',
        ] as $url) {
            $this->getJson($url)->assertStatus(401);
        }
    }

    /** Geçerli token ile (kullanıcı '*' ability) uçlar fatal vermeden açılır. */
    public function test_data_api_responds_with_valid_token(): void
    {
        \Laravel\Sanctum\Sanctum::actingAs(User::factory()->create(), ['*']);

        foreach ([
            '/api/v1/universities',
            '/api/v1/scholarships',
            '/api/v1/housing-providers',
            '/api/v1/blocked-accounts',
            '/api/v1/blog',
            '/api/v1/faqs',
            '/api/v1/cities',
            '/api/v1/states',
            '/api/v1/programs',
            '/api/v1/professions',
            '/api/v1/fields-of-study',
        ] as $url) {
            $this->assertNotServerError($url, 200);
        }
    }

    public function test_concept_synonym_search_finds_tool(): void
    {
        // "blocked account" (EN) → Sperrkonto aracını bulmalı (sinonim katmanı).
        // Config-driven, veriden bağımsız → boş DB'de de çalışır.
        $this->get('/en/search?q=blocked account')
            ->assertStatus(200)
            ->assertSee('Sperrkonto', false);

        $this->get('/tr/search?q=bloke hesap')
            ->assertStatus(200)
            ->assertSee('Sperrkonto', false);
    }

    public function test_flatreklam_requires_token(): void
    {
        // Token yapılandırılmamış → 401 (NOT_CONFIGURED yoksa UNAUTHORIZED) — 5xx olmamalı
        $this->getJson('/api/flatreklam/v1/ping')->assertStatus(401);
    }

    public function test_news_module_can_be_toggled(): void
    {
        // Varsayılan (kayıt yok) → modül açık → 200
        $this->get(route('news.index'))->assertStatus(200);

        // Admin "Menü Sayfa Yönetimi"nden kapatınca → menüden gizli + URL 404
        \App\Models\MenuPage::create([
            'key' => 'news.index', 'label' => 'Haberler', 'group' => 'icerik',
            'link_type' => 'route', 'is_enabled' => false,
        ]);
        \App\Models\MenuPage::flushCache();

        $this->get(route('news.index'))->assertStatus(404);
    }

    public function test_admin_pages_render_for_admin(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        // /admin/integrations bu oturumda FormAction yüzünden 500 vermişti.
        $this->actingAs($admin)->get('/admin/integrations')->assertStatus(200);

        // Haber Akışı resource (3-mod panel) — Filament render hatasını yakala.
        $this->actingAs($admin)->get('/admin/news-candidates')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/news-candidates/create')->assertStatus(200);

        // Profil dashboard — journey-progress-card render (Application Tracker promosu).
        $this->actingAs($admin)->get('/profile')->assertStatus(200);
    }
}
