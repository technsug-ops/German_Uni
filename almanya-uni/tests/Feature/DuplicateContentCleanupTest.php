<?php

namespace Tests\Feature;

use App\Models\MenuPage;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * Mükerrer içerik temizliği (2026-09-25): haber ↔ blog, uni-assist, Studienkolleg ve
 * dile duyarlı blog yönlendirmesi.
 *
 * Canlı teşhis: 14 haber × 3 dil hem /news/ hem /blog/ altında 200/self-canonical; blog
 * sürümü sitemap'te ama hreflang'i yalnız kendisi. uni-assist ve Studienkolleg'in eski slug'lı
 * kopyaları yayında. blog_redirects dil filtresiz aranıyordu → /en/blog/<tr-slug> TR'ye gidiyordu.
 */
class DuplicateContentCleanupTest extends TestCase
{
    use RefreshDatabase;

    private const L = ['tr', 'en', 'de'];

    /* ---------------------------------------------------------------- yardımcı */

    /** @param array<string, string> $slugs locale => slug */
    private function posts(array $slugs, string $type = 'blog', array $extra = []): string
    {
        $group = (string) Str::uuid();
        foreach ($slugs as $locale => $slug) {
            DB::table('posts')->insert(array_merge([
                'title' => "{$slug} başlık", 'slug' => $slug, 'content_md' => "## {$slug}\n\nMetin.",
                'locale' => $locale, 'type' => $type, 'is_published' => true, 'published_at' => now()->subDays(2),
                'translation_group_id' => $group, 'created_at' => now()->subDays(3), 'updated_at' => now()->subDays(3),
            ], $extra));
        }

        return $group;
    }

    private function redirect(string $from, string $to, string $locale): void
    {
        DB::table('blog_redirects')->insert(['from_slug' => $from, 'to_slug' => $to, 'locale' => $locale, 'created_at' => now(), 'updated_at' => now()]);
    }

    private function migrate(string $file): string
    {
        ob_start();
        try {
            (require database_path("migrations/{$file}"))->up();
        } finally {
            $out = (string) ob_get_clean();
        }

        return $out;
    }

    private function path(?string $url): string
    {
        return parse_url((string) $url, PHP_URL_PATH) ?: '/';
    }

    /** Tek adım: 301 ve hedefi doğrudan 200. */
    private function assertSingleHop(string $from, string $to): void
    {
        view()->share('localeUrls', null);
        $r = $this->get($from);
        $this->assertSame(301, $r->getStatusCode(), "{$from} 301 değil");
        $this->assertSame($to, $this->path($r->headers->get('Location')), "{$from} yanlış hedef");

        view()->share('localeUrls', null);
        $this->assertSame(200, $this->get($to)->getStatusCode(), "{$to} 200 değil (zincir/404)");
    }

    /** @return array<string, array<string, string>> */
    private function sitemap(string $lang): array
    {
        $xml = $this->get("/sitemap-{$lang}.xml")->assertOk()->getContent();
        preg_match_all('~<url>(.*?)</url>~s', $xml, $m);
        $out = [];
        foreach ($m[1] as $b) {
            preg_match('~<loc>([^<]+)</loc>~', $b, $l);
            preg_match_all('~hreflang="([^"]+)" href="([^"]+)"~', $b, $a, PREG_SET_ORDER);
            $loc = $this->path(html_entity_decode($l[1]));
            $this->assertArrayNotHasKey($loc, $out, "tekrarlanan <loc> {$loc}");
            $out[$loc] = collect($a)->mapWithKeys(fn ($x) => [$x[1] => $this->path(html_entity_decode($x[2]))])->all();
        }

        return $out;
    }

    /** @return array{alts: array<string, string>, canonical: array<int, string>} */
    private function pageHead(string $url): array
    {
        view()->share('localeUrls', null);
        $h = explode('</head>', $this->get($url)->assertOk()->getContent(), 2)[0];
        preg_match_all('~<link rel="alternate" hreflang="([^"]+)" href="([^"]+)"~', $h, $a, PREG_SET_ORDER);
        preg_match_all('~<link rel="canonical" href="([^"]+)"~', $h, $c);

        return ['alts' => collect($a)->mapWithKeys(fn ($x) => [$x[1] => $this->path(html_entity_decode($x[2]))])->all(),
            'canonical' => array_map(fn ($u) => $this->path($u), $c[1])];
    }

    private function newsCluster(string $base = 'sm-news'): array
    {
        $slugs = ['tr' => $base, 'en' => "{$base}-en", 'de' => "{$base}-de"];
        $this->posts($slugs, 'news');

        return $slugs;
    }

    private function disableNews(): void
    {
        DB::table('menu_pages')->updateOrInsert(['key' => 'news.index'], ['label' => 'News', 'is_enabled' => false, 'created_at' => now(), 'updated_at' => now()]);
        MenuPage::flushCache();
    }

    /* ======================================================== NEWS ↔ BLOG */

    public function test_blog_url_of_a_news_item_301s_once_to_the_same_locale_news_url(): void   // A, B, C, U
    {
        $slugs = $this->newsCluster();
        foreach ($slugs as $l => $s) {
            $this->assertSingleHop("/{$l}/blog/{$s}", "/{$l}/news/{$s}");
        }
    }

    public function test_news_page_is_self_canonical_and_matches_the_sitemap_cluster(): void   // D, E, F
    {
        $slugs = $this->newsCluster();
        $cluster = ['tr' => '/tr/news/sm-news', 'en' => '/en/news/sm-news-en', 'de' => '/de/news/sm-news-de', 'x-default' => '/en/news/sm-news-en'];

        foreach ($slugs as $l => $s) {
            $sm = $this->sitemap($l);
            $this->assertArrayHasKey("/{$l}/news/{$s}", $sm);
            $this->assertArrayNotHasKey("/{$l}/blog/{$s}", $sm, 'haber /blog/ yoluyla sitemap\'te');
            $this->assertSame($cluster, $sm["/{$l}/news/{$s}"]);

            $page = $this->pageHead("/{$l}/news/{$s}");
            $this->assertSame(["/{$l}/news/{$s}"], $page['canonical']);
            $this->assertSame($sm["/{$l}/news/{$s}"], $page['alts'], 'sitemap ile sayfa hreflang farklı');
        }
    }

    public function test_public_url_helper_routes_by_type(): void   // G, H
    {
        $this->newsCluster();
        $this->posts(['tr' => 'sm-blog-post'], 'blog');

        $news = Post::where('slug', 'sm-news-en')->first();
        $this->assertSame('/en/news/sm-news-en', $this->path($news->publicUrl()));
        $this->assertSame('/en/blog/sm-blog-post', $this->path(Post::where('slug', 'sm-blog-post')->first()->publicUrl('en')));
        $this->assertSame('/tr/blog/sm-blog-post', $this->path(Post::where('slug', 'sm-blog-post')->first()->publicUrl()));
        $this->assertSame('/de/news/sm-news-de', $this->path(Post::urlFor('news', 'sm-news-de', 'de')));

        // type seçilmeden çağrılırsa test ortamında hata (sessizce /blog/ üretmesin).
        $this->expectException(\LogicException::class);
        Post::select(['slug', 'locale'])->where('slug', 'sm-news')->first()->publicUrl();
    }

    public function test_with_the_news_module_off_there_is_no_blind_redirect_to_a_404(): void   // I
    {
        $slugs = $this->newsCluster();
        $this->disableNews();

        view()->share('localeUrls', null);
        $this->get("/tr/blog/{$slugs['tr']}")->assertOk();
        $this->assertSame('/tr/blog/sm-news', $this->path(Post::where('slug', 'sm-news')->first()->publicUrl()));
        $this->assertArrayNotHasKey('/tr/news/sm-news', $this->sitemap('tr'));
    }

    public function test_link_producers_point_news_to_news(): void
    {
        $this->newsCluster('sm-latest');

        view()->share('localeUrls', null);
        $home = $this->get('/tr')->assertOk()->getContent();
        $this->assertStringContainsString('/tr/news/sm-latest"', $home, 'ana sayfa haberi /news/ ile linklemeli');
        $this->assertStringNotContainsString('/tr/blog/sm-latest"', $home);

        cache()->flush();
        $rss = $this->get('/rss.xml')->assertOk()->getContent();
        $this->assertStringNotContainsString('/blog/sm-latest', $rss);

        $llms = $this->get('/llms-full.txt')->assertOk()->getContent();
        $this->assertStringContainsString('/tr/news/sm-latest)', $llms);
        $this->assertStringNotContainsString('/tr/blog/sm-latest)', $llms);
    }

    public function test_no_known_producer_builds_news_urls_with_blog_show(): void
    {
        // Yalnız blog türünü gösteren iki yer istisna: blog listesi (blogType) ve blog yazısı
        // breadcrumb'ı (haber buraya gelmeden 301 alır). Sitemap blog.show'u yalnız blog için kullanır.
        $allowed = ['resources/views/blog/index.blade.php', 'resources/views/blog/show.blade.php', 'app/Http/Controllers/Web/SitemapController.php'];
        $hits = [];
        foreach (['app', 'resources/views', 'routes'] as $dir) {
            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(base_path($dir)));
            foreach ($it as $f) {
                if (! $f->isFile() || ! str_ends_with($f->getFilename(), '.php')) {
                    continue;
                }
                $rel = str_replace('\\', '/', substr($f->getPathname(), strlen(base_path()) + 1));
                if (in_array($rel, $allowed, true)) {
                    continue;
                }
                if (preg_match("~route\(\s*'blog\.show'~", file_get_contents($f->getPathname()))) {
                    $hits[] = $rel;
                }
            }
        }
        $this->assertSame([], $hits, 'route(\'blog.show\') doğrudan kullanılıyor — Post::publicUrl() kullanılmalı');
    }

    /* ========================================================= UNIASSIST */

    private function uniassistFixture(): array
    {
        $this->posts(['tr' => 'uni-assist-application-vpd-common-rejection-reasons-solutions',
            'en' => 'uni-assist-application-vpd-common-rejection-reasons-solutions-en',
            'de' => 'uni-assist-application-vpd-common-rejection-reasons-solutions-de']);
        $this->posts(['tr' => 'uniassist-vpd-reddedilme-cozumler'], 'blog', ['translation_group_id' => null]);
        $this->redirect('uniassist-vpd-reddedilme-cozumler', 'uni-assist-application-vpd-common-rejection-reasons-solutions', 'tr');

        return (array) DB::table('posts')->where('slug', 'uniassist-vpd-reddedilme-cozumler')->first();
    }

    public function test_uniassist_loser_is_retired_and_301s_once_to_the_winner(): void   // J, K, L, M
    {
        $before = $this->uniassistFixture();
        $this->migrate('2026_09_25_000200_retire_duplicate_uniassist_post.php');

        $after = DB::table('posts')->where('id', $before['id'])->first();
        $this->assertFalse((bool) $after->is_published);
        $this->assertSame((string) $before['updated_at'], (string) $after->updated_at, 'updated_at değişmemeli');

        $this->assertSingleHop('/tr/blog/uniassist-vpd-reddedilme-cozumler', '/tr/blog/uni-assist-application-vpd-common-rejection-reasons-solutions');
        $this->assertArrayNotHasKey('/tr/blog/uniassist-vpd-reddedilme-cozumler', $this->sitemap('tr'));
        $this->assertArrayHasKey('/tr/blog/uni-assist-application-vpd-common-rejection-reasons-solutions', $this->sitemap('tr'));

        // İdempotent
        $this->migrate('2026_09_25_000200_retire_duplicate_uniassist_post.php');
        $this->assertSame(1, DB::table('blog_redirects')->where('from_slug', 'uniassist-vpd-reddedilme-cozumler')->count());
    }

    public function test_uniassist_migration_refuses_when_the_redirect_points_elsewhere(): void
    {
        $this->uniassistFixture();
        DB::table('blog_redirects')->where('from_slug', 'uniassist-vpd-reddedilme-cozumler')->update(['to_slug' => 'baska-bir-yazi']);

        $this->expectException(\RuntimeException::class);
        $this->migrate('2026_09_25_000200_retire_duplicate_uniassist_post.php');
    }

    /* ====================================================== STUDIENKOLLEG */

    private const SK_NEW = 'studienkolleg-guide-2026-who-needs-it-which-course-which-school';

    private const SK_OLD = ['tr' => 'studienkolleg-rehberi-t-kurs-m-kurs-2026', 'en' => 'studienkolleg-guide-who-needs-it-which-course-school', 'de' => 'studienkolleg-guide-pflicht-kurs-schule'];

    private function skWinners(): array
    {
        return ['tr' => self::SK_NEW, 'en' => self::SK_NEW . '-en', 'de' => self::SK_NEW . '-de'];
    }

    private function studienkollegFixture(): void
    {
        $this->posts($this->skWinners());
        $this->posts(self::SK_OLD);
        $this->redirect(self::SK_OLD['tr'], self::SK_NEW, 'tr');
        $this->redirect(self::SK_OLD['en'], self::SK_NEW . '-en', 'en');   // de satırı yok (canlıdaki gibi)
    }

    public function test_studienkolleg_old_cluster_is_retired_with_same_locale_single_hops(): void   // N, O, P, Q, R
    {
        $this->studienkollegFixture();
        $this->migrate('2026_09_25_000300_retire_duplicate_studienkolleg_cluster.php');

        foreach (self::SK_OLD as $l => $old) {
            $this->assertFalse((bool) DB::table('posts')->where('slug', $old)->value('is_published'), "{$l} yayında kaldı");
            $this->assertSingleHop("/{$l}/blog/{$old}", "/{$l}/blog/" . $this->skWinners()[$l]);
        }
        $this->assertSame(self::SK_NEW . '-de', DB::table('blog_redirects')->where('from_slug', self::SK_OLD['de'])->where('locale', 'de')->value('to_slug'));

        $cluster = ['tr' => '/tr/blog/' . self::SK_NEW, 'en' => '/en/blog/' . self::SK_NEW . '-en', 'de' => '/de/blog/' . self::SK_NEW . '-de', 'x-default' => '/en/blog/' . self::SK_NEW . '-en'];
        foreach ($this->skWinners() as $l => $w) {
            $sm = $this->sitemap($l);
            $this->assertSame($cluster, $sm["/{$l}/blog/{$w}"]);
            $this->assertSame($cluster, $this->pageHead("/{$l}/blog/{$w}")['alts']);
            $this->assertArrayNotHasKey("/{$l}/blog/" . self::SK_OLD[$l], $sm);
        }
    }

    public function test_studienkolleg_migration_fails_when_the_pairing_is_ambiguous(): void
    {
        $this->posts($this->skWinners());
        $group = DB::table('posts')->where('slug', self::SK_NEW)->value('translation_group_id');
        $this->posts(self::SK_OLD, 'blog', ['translation_group_id' => $group]);   // loser kazananın grubunda

        $this->expectException(\RuntimeException::class);
        $this->migrate('2026_09_25_000300_retire_duplicate_studienkolleg_cluster.php');
    }

    /* ============================================== DİLE DUYARLI YÖNLENDİRME */

    public function test_a_request_never_uses_another_locales_redirect_row(): void   // S, T
    {
        $this->posts(['tr' => 'sm-target', 'en' => 'sm-target-en']);          // DE kardeşi yok
        $this->redirect('sm-old-slug', 'sm-target', 'tr');

        // Tamamlama öncesi: yalnız TR satırı var → EN/DE istekleri TR'ye GİTMEZ.
        $this->assertSingleHop('/tr/blog/sm-old-slug', '/tr/blog/sm-target');
        view()->share('localeUrls', null);
        $this->get('/en/blog/sm-old-slug')->assertNotFound();

        $this->migrate('2026_09_25_000400_make_blog_redirects_locale_complete.php');

        // EN gerçek kardeşe; DE'de kardeş yok → 404 (başka dile gönderilmez).
        $this->assertSingleHop('/en/blog/sm-old-slug', '/en/blog/sm-target-en');
        view()->share('localeUrls', null);
        $this->get('/de/blog/sm-old-slug')->assertNotFound();
        $this->assertSame(0, DB::table('blog_redirects')->where('from_slug', 'sm-old-slug')->where('locale', 'de')->count());
    }

    public function test_redirect_chains_are_collapsed_to_one_hop(): void   // U
    {
        $this->posts(['tr' => 'sm-final']);
        $this->redirect('sm-first', 'sm-middle', 'tr');
        $this->redirect('sm-middle', 'sm-final', 'tr');

        $this->migrate('2026_09_25_000400_make_blog_redirects_locale_complete.php');

        $this->assertSingleHop('/tr/blog/sm-first', '/tr/blog/sm-final');
        $this->assertSingleHop('/tr/blog/sm-middle', '/tr/blog/sm-final');
    }

    public function test_locale_backfill_is_idempotent_and_allows_one_row_per_locale(): void
    {
        $this->posts(['tr' => 'sm-t2', 'en' => 'sm-t2-en', 'de' => 'sm-t2-de']);
        $this->redirect('sm-o2', 'sm-t2', 'tr');

        $this->migrate('2026_09_25_000400_make_blog_redirects_locale_complete.php');
        $rows = DB::table('blog_redirects')->where('from_slug', 'sm-o2')->orderBy('locale')->pluck('to_slug', 'locale')->all();
        $this->assertSame(['de' => 'sm-t2-de', 'en' => 'sm-t2-en', 'tr' => 'sm-t2'], $rows);

        $this->migrate('2026_09_25_000400_make_blog_redirects_locale_complete.php');
        $this->assertSame(3, DB::table('blog_redirects')->where('from_slug', 'sm-o2')->count());
    }

    /* ================================================ KURTARMA YEDEĞİ + RAPOR */

    public function test_backup_keeps_the_pre_change_schema_and_is_never_overwritten(): void
    {
        $backup = 'blog_redirects_backup_20260925';
        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasTable($backup));

        // Yedek, değişiklik ÖNCESİ şemayı taşır: yalnız from_slug UNIQUE.
        $unique = collect(DB::select("SHOW INDEX FROM `{$backup}`"))->where('Non_unique', 0)->groupBy('Key_name')
            ->map(fn ($c) => $c->pluck('Column_name')->all())->all();
        $this->assertSame(['from_slug'], $unique['blog_redirects_from_slug_unique'] ?? null);

        // Canlı tablo artık (from_slug, locale).
        $live = collect(DB::select('SHOW INDEX FROM `blog_redirects`'))->where('Non_unique', 0)->groupBy('Key_name')
            ->map(fn ($c) => $c->sortBy('Seq_in_index')->pluck('Column_name')->all())->all();
        $this->assertSame(['from_slug', 'locale'], $live['blog_redirects_from_slug_locale_unique'] ?? null);
        $this->assertArrayNotHasKey('blog_redirects_from_slug_unique', $live);

        $count = DB::table($backup)->count();
        $this->posts(['tr' => 'sm-bk', 'en' => 'sm-bk-en']);
        $this->redirect('sm-bk-old', 'sm-bk', 'tr');
        $this->migrate('2026_09_25_000400_make_blog_redirects_locale_complete.php');
        $this->assertSame($count, DB::table($backup)->count(), 'yeniden koşu yedeğin üzerine yazdı');
    }

    public function test_redirect_check_report_is_admin_only_and_complete(): void
    {
        $this->posts(['tr' => 'sm-rc', 'en' => 'sm-rc-en']);
        $this->redirect('sm-rc-old', 'sm-rc', 'tr');
        $this->migrate('2026_09_25_000400_make_blog_redirects_locale_complete.php');

        $this->get('/admin/ops/redirect-check')->assertRedirect();
        $this->actingAs(\App\Models\User::factory()->create(['is_admin' => false]))->get('/admin/ops/redirect-check')->assertForbidden();

        $body = $this->actingAs(\App\Models\User::factory()->create(['is_admin' => true]))->get('/admin/ops/redirect-check')->assertOk()->getContent();
        foreach (['BLOG_REDIRECTS BEFORE', 'BLOG_REDIRECTS AFTER', 'UNIQUE blog_redirects_from_slug_locale_unique(from_slug, locale)',
            'duplicate (from_slug, locale) pairs: 0', 'NO_SAME_LOCALE_TARGET', '/de/blog/sm-rc-old', 'DUPLICATE CLEANUP POSTS'] as $needle) {
            $this->assertStringContainsString($needle, $body);
        }
    }

    /* ============================================================ SITEMAP */

    public function test_sitemap_has_news_only_under_news_and_no_orphan_alternates(): void   // V, W, X, Y
    {
        $this->newsCluster('sm-n1');
        $this->newsCluster('sm-n2');
        $this->studienkollegFixture();
        $this->uniassistFixture();
        $this->migrate('2026_09_25_000200_retire_duplicate_uniassist_post.php');
        $this->migrate('2026_09_25_000300_retire_duplicate_studienkolleg_cluster.php');

        $flat = [];
        foreach (self::L as $l) {
            $sm = $this->sitemap($l);
            foreach (['sm-n1', 'sm-n2'] as $b) {
                $s = $l === 'tr' ? $b : "{$b}-{$l}";
                $this->assertArrayHasKey("/{$l}/news/{$s}", $sm);
                $this->assertArrayNotHasKey("/{$l}/blog/{$s}", $sm);
            }
            $flat += $sm;
        }
        foreach ($flat as $loc => $alts) {
            foreach ($alts as $hl => $u) {
                if ($hl !== 'x-default' && $u !== $loc) {
                    $this->assertArrayHasKey($u, $flat, "{$loc} → {$u}: alternatifin kendi <loc>'u yok");
                }
            }
        }
    }
}
