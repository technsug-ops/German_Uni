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
            route('scholarships.index'),
            route('universities.index'),
            route('cities.index'),
            route('events.index'),
            route('mentors.index'),
            route('contact'),
            '/tr/search?q=informatik', // FULLTEXT arama yolu (whereFullText + fallback)
            '/tr/search?q=be',          // kısa sorgu → LIKE fallback
            '/api/docs',
            '/login',
            '/register',
        ] as $url) {
            $this->assertNotServerError($url);
        }
    }

    public function test_public_api_endpoints_respond(): void
    {
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

    public function test_flatreklam_requires_token(): void
    {
        // Token yapılandırılmamış → 401 (NOT_CONFIGURED yoksa UNAUTHORIZED) — 5xx olmamalı
        $this->getJson('/api/flatreklam/v1/ping')->assertStatus(401);
    }

    public function test_admin_pages_render_for_admin(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        // /admin/integrations bu oturumda FormAction yüzünden 500 vermişti.
        $this->actingAs($admin)->get('/admin/integrations')->assertStatus(200);
    }
}
