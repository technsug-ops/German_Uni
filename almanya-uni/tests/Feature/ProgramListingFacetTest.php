<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * SEO Recovery Faz 2A: /programs filtre (faceted) URL'leri.
 * - Parametre normalizasyonu: boş/varsayılan değerler (sort=relevance, page=1, false boolean) ve karışık sıra →
 *   tek 301 ile kanonik sıra. Bilinmeyen parametreler (utm_*) korunur. XHR (partial=1) yönlendirilmez.
 * - Geçersiz değer (enum dışı, bilinmeyen field/state slug'ı, dizi, sayısal olmayan sayfa, son sayfa ötesi) → 404.
 * - Hızlı filtre çipleri sayfa numarasını taşımaz ve kanonik sırada üretilir; sayfalama bağlantıları kanonik.
 * - Canonical ve robots politikası değişmez: tüm liste URL'leri canonical → /{locale}/programs, index.
 */
class ProgramListingFacetTest extends TestCase
{
    use RefreshDatabase;

    private function seedFixture(int $n = 45): void
    {
        $uni = DB::table('universities')->insertGetId(['name_de' => 'Test Uni', 'name_tr' => 'Test Uni', 'slug' => 'test-uni', 'is_active' => 1, 'is_uni_assist_member' => 1, 'created_at' => now(), 'updated_at' => now()]);
        $field = DB::table('fields_of_study')->insertGetId(['slug' => 'computer-science', 'name_tr' => 'Bilgisayar', 'name_en' => 'Computer Science', 'name_de' => 'Informatik', 'is_active' => 1, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()]);
        for ($i = 1; $i <= $n; $i++) {
            DB::table('programs')->insert([
                'university_id' => $uni, 'field_of_study_id' => $field, 'name_de' => sprintf('Programm %02d', $i), 'slug' => sprintf('programm-%02d', $i),
                'degree' => $i % 2 ? 'bachelor' : 'master', 'language' => $i % 3 ? 'de' : 'en', 'is_active' => 1,
                'description_en' => 'A programme with seminars, projects and an internship semester in industry.', 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    protected function tearDown(): void
    {
        // Testte bilerek değiştirilen statik varsayılan görünümü Laravel varsayılanına geri al.
        \Illuminate\Pagination\Paginator::defaultView('pagination::tailwind');
        \Illuminate\Pagination\Paginator::defaultSimpleView('pagination::simple-tailwind');
        parent::tearDown();
    }

    private function canonical(string $html): ?string
    {
        return preg_match('#<link rel="canonical" href="([^"]+)"#', $html, $m) ? $m[1] : null;
    }

    public function test_clean_listing_and_valid_filters_render_with_unchanged_canonical(): void
    {
        $this->seedFixture();
        foreach (['/tr/programs', '/en/programs?degree=master', '/de/programs?degree=master&language=en', '/tr/programs?field=computer-science&uni_assist=1'] as $u) {
            $html = $this->get($u)->assertOk()->getContent();
            $loc = substr($u, 1, 2);
            $this->assertSame(url("/$loc/programs"), $this->canonical($html), $u);
            $this->assertStringContainsString('<meta name="robots" content="index', $html);
        }
    }

    public function test_parameter_normalization_redirects_once_to_canonical_order(): void
    {
        $this->seedFixture();
        $cases = [
            '/tr/programs?language=en&degree=master' => '/tr/programs?degree=master&language=en',
            '/tr/programs?degree=master&page=1' => '/tr/programs?degree=master',
            '/tr/programs?sort=relevance' => '/tr/programs',
            '/tr/programs?degree=&language=&field=' => '/tr/programs',
            '/tr/programs?has_tr=0&uni_assist=on' => '/tr/programs?uni_assist=1',
            '/en/programs?page=2&degree=bachelor' => '/en/programs?degree=bachelor&page=2',
            '/de/programs?q=&degree=master&utm_source=news' => '/de/programs?degree=master&utm_source=news',
        ];
        foreach ($cases as $from => $to) {
            $res = $this->get($from);
            $res->assertStatus(301);
            $this->assertSame(url($to), $res->headers->get('Location'), $from);
            $this->get($to)->assertOk();
        }
        // Zaten kanonik → yönlendirme yok
        $this->get('/tr/programs?degree=master&page=2')->assertOk();
        $this->get('/tr/programs?degree=master&language=en&page=2')->assertNotFound(); // 7 program = 1 sayfa
    }

    public function test_old_field_slug_and_parameter_order_are_fixed_in_a_single_redirect(): void
    {
        $this->seedFixture();
        DB::table('slug_redirects')->insert(['entity' => 'field', 'from_slug' => 'bilisim', 'to_slug' => 'computer-science', 'created_at' => now(), 'updated_at' => now()]);
        $res = $this->get('/tr/programs?language=en&field=bilisim&page=1');
        $res->assertStatus(301);
        $this->assertSame(url('/tr/programs?field=computer-science&language=en'), $res->headers->get('Location'));
        $this->get('/tr/programs?field=computer-science&language=en')->assertOk(); // zincir yok: hedef doğrudan 200
        $this->get('/tr/programs?field=bilisim')->assertStatus(301)->assertRedirect(url('/tr/programs?field=computer-science'));
    }

    public function test_xhr_partial_requests_are_not_redirected(): void
    {
        $this->seedFixture();
        $this->get('/tr/programs?language=en&degree=master&partial=1')->assertOk();
        $this->get('/tr/programs?language=en&degree=master', ['X-Requested-With' => 'XMLHttpRequest'])->assertOk();
    }

    public function test_invalid_values_and_pages_return_404(): void
    {
        $this->seedFixture();
        foreach ([
            '/tr/programs?degree=xyz', '/tr/programs?language=fr', '/tr/programs?sort=random', '/tr/programs?admission=foo',
            '/tr/programs?semester=autumn', '/tr/programs?duration_range=huge', '/tr/programs?field=no-such-field', '/tr/programs?state=no-such-state',
            '/tr/programs?degree[]=master', '/tr/programs?page=0', '/tr/programs?page=abc', '/tr/programs?page=4', '/tr/programs?degree=master&page=3',
        ] as $u) {
            $this->get($u)->assertNotFound();
        }
        // 45 program / 20 = 3 sayfa; master 22 → 2 sayfa
        $this->get('/tr/programs?page=3')->assertOk();
        $this->get('/tr/programs?degree=master&page=2')->assertOk();
    }

    public function test_quick_filter_chips_drop_the_page_and_use_canonical_order(): void
    {
        $this->seedFixture();
        $html = html_entity_decode($this->get('/tr/programs?degree=master&page=2')->assertOk()->getContent());
        // Çip: master'ı kaldır (toggle) → sayfa sıfırlanır
        $this->assertStringContainsString('href="' . url('/tr/programs') . '"', $html);
        // Çip: İngilizce ekle → kanonik sıra, sayfa yok
        $this->assertStringContainsString('href="' . url('/tr/programs?degree=master&language=en') . '"', $html);
        $this->assertStringNotContainsString('language=en&page=', $html);
        $this->assertStringNotContainsString('page=2&language', $html);
        $this->assertStringNotContainsString('uni_assist=1&page=', $html);
    }

    public function test_pagination_links_are_canonical_and_reach_every_program(): void
    {
        $this->seedFixture();
        // Livewire'ın statik varsayılan görünüm sızıntısını taklit et (Filament tablo testlerinden sonra olan buydu):
        // liste yine de <a href> bağlantıları üretmeli.
        \Illuminate\Pagination\Paginator::defaultView('livewire::tailwind');
        $paginator = $this->get('/tr/programs?degree=bachelor')->assertOk()->viewData('programs');
        $this->assertSame(23, $paginator->total(), 'DB bachelor: ' . DB::table('programs')->where('is_active', 1)->where('degree', 'bachelor')->count());
        $this->assertTrue($paginator->hasPages());
        $this->assertStringEndsWith('/tr/programs?degree=bachelor&page=2', $paginator->url(2));
        // Gerçek HTML: filtreli sayfa 1'de sayfa 2'ye taranabilir <a href> bağlantısı var.
        $html = html_entity_decode($this->get('/tr/programs?degree=bachelor')->getContent());
        $this->assertStringContainsString('href="' . url('/tr/programs?degree=bachelor&page=2') . '"', $html);
        $this->assertStringNotContainsString('wire:click', $html);
        // Sayfa 2: "önceki" / "1" bağlantısı ?page=1 değil, doğrudan kanonik 1. sayfa (fazladan 301 yok).
        $html2 = html_entity_decode($this->get('/tr/programs?degree=bachelor&page=2')->assertOk()->getContent());
        $this->assertStringContainsString('href="' . url('/tr/programs?degree=bachelor') . '"', $html2);
        $this->assertStringNotContainsString('page=1"', $html2);

        // Gerçek HTML'deki sayfalama bağlantılarını izleyerek (tahmin edilmiş URL değil) tüm programlara ulaşılır.
        $seen = [];
        $queue = ['/tr/programs'];
        $visited = [];
        while ($queue) {
            $u = array_shift($queue);
            if (isset($visited[$u])) {
                continue;
            }
            $visited[$u] = true;
            $page = html_entity_decode($this->get($u)->assertOk()->getContent());
            preg_match_all('#/tr/programs/(programm-\d+)"#', $page, $m);
            $seen = array_merge($seen, $m[1]);
            preg_match_all('#href="' . preg_quote(url('/tr/programs'), '#') . '(\?page=\d+)?"#', $page, $links);
            foreach ($links[1] as $q) {
                $queue[] = '/tr/programs' . $q;
            }
        }
        $this->assertCount(45, array_unique($seen), 'tüm programlara filtresiz sayfalamanın HTML bağlantılarıyla erişilir');
        $this->assertArrayHasKey('/tr/programs?page=3', $visited);
    }

    public function test_program_detail_and_seo_landing_pages_are_unaffected(): void
    {
        $this->seedFixture();
        $this->get('/tr/programs/programm-01')->assertOk();
        $this->get('/en/programs/field/computer-science/degree/master')->assertOk();
        $this->get('/de/programs/field/computer-science/language/en')->assertOk();
    }
}
