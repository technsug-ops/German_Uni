<?php

namespace Tests\Feature;

use App\Models\SearchQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * SearchController'ın "smoke (200 döndü)" ötesindeki davranışı:
 *  - 100 karakter DoS kırpması gerçekten uygulanıyor mu (loglanan değer üzerinden)
 *  - Arama logu koşulları: bot hariç, < 2 karakter hariç, normalde yazılır.
 */
class SearchLoggingTest extends TestCase
{
    use RefreshDatabase;

    public function test_uzun_sorgu_logda_100_karaktere_kirpilir(): void
    {
        $this->get('/tr/search?q=' . str_repeat('a', 150))->assertStatus(200);

        $logged = SearchQuery::first();
        $this->assertNotNull($logged, 'Normal sorgu loglanmalıydı.');
        // Kontrolör mb_substr(..., 0, 100) uyguluyor → log da kırpık.
        $this->assertSame(100, mb_strlen($logged->query));
    }

    public function test_bot_user_agent_loglanmaz(): void
    {
        $this->withHeaders(['User-Agent' => 'Googlebot/2.1 (+http://www.google.com/bot.html)'])
            ->get('/tr/search?q=informatik')
            ->assertStatus(200);

        $this->assertSame(0, SearchQuery::count(), 'Bot trafiği loglanmamalı.');
    }

    public function test_cok_kisa_sorgu_loglanmaz(): void
    {
        // Tek karakterlik sorgu (>= 2 şartını geçmez) → log yok.
        $this->get('/tr/search?q=a')->assertStatus(200);

        $this->assertSame(0, SearchQuery::count());
    }

    public function test_normal_sorgu_breakdown_ile_loglanir(): void
    {
        // Eşleşmeyen sorgu kullan: migration'lar içerik (post/şehir/eyalet) seed eder,
        // gerçek bir kelime (ör. "hamburg") fulltext'te eşleşip results_count'u bozar.
        $this->get('/tr/search?q=zqxwvnomatch')->assertStatus(200);

        $logged = SearchQuery::first();
        $this->assertNotNull($logged);
        $this->assertSame('zqxwvnomatch', $logged->query);        // mb_strtolower
        $this->assertSame(0, (int) $logged->results_count);       // eşleşmeyen sorgu → 0 sonuç
        $this->assertIsArray($logged->breakdown);                 // tür kırılımı JSON
        $this->assertArrayHasKey('universities', $logged->breakdown);
    }
}
