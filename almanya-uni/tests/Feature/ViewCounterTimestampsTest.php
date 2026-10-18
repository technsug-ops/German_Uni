<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * SB-1 — ziyaretçi görüntülemesi ve "faydalı mı" oyu içerik güncelleme tarihini (updated_at) değiştirmemeli.
 * updated_at; sitemap lastmod, JSON-LD dateModified, article:modified_time ve "Güncellendi" etiketini besliyor.
 * Gerçek içerik düzenlemesinde updated_at ilerlemeye devam etmeli.
 */
class ViewCounterTimestampsTest extends TestCase
{
    use RefreshDatabase;

    private const PUBLISHED = '2026-01-10 09:00:00';
    private const EDITED = '2026-02-20 15:30:00';

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-10-18 12:00:00'); // "bugün" — eski davranışta updated_at buna atlardı
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function makePost(string $locale, string $type, string $slug): int
    {
        $p = new Post();
        $p->forceFill([
            'title' => "SB1 {$slug}", 'slug' => $slug, 'locale' => $locale, 'type' => $type,
            'content_md' => "## Section\n\nText.", 'is_published' => true, 'published_at' => self::PUBLISHED,
            'translation_group_id' => (string) Str::uuid(),
        ])->save();
        DB::table('posts')->where('id', $p->id)->update(['updated_at' => self::EDITED, 'view_count' => 5]);

        return $p->id;
    }

    private function faq(string $locale, string $slug): int
    {
        $topic = DB::table('faq_topics')->where('slug', 'master')->value('id')
            ?? DB::table('faq_topics')->insertGetId(['name' => 'Master', 'slug' => 'master', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);

        return DB::table('faqs')->insertGetId([
            'faq_topic_id' => $topic, 'locale' => $locale, 'question' => "SB1 question {$slug}?", 'slug' => $slug,
            'answer_md' => 'Answer.', 'answer_html' => '<p>Answer.</p>', 'has_answer' => true, 'is_published' => true,
            'view_count' => 5, 'created_at' => self::PUBLISHED, 'updated_at' => self::EDITED,
        ]);
    }

    private function row(string $table, int $id): object
    {
        return DB::table($table)->where('id', $id)->first(['view_count', 'updated_at']);
    }

    private function headSignals(string $html): array
    {
        preg_match('/<link rel="canonical" href="([^"]*)"/', $html, $c);
        preg_match_all('/<link rel="alternate" hreflang="([^"]+)" href="([^"]+)"/', $html, $h, PREG_SET_ORDER);
        preg_match('/"dateModified":"([^"]*)"/', $html, $d);
        preg_match('/<meta property="article:modified_time" content="([^"]*)"/', $html, $m);

        return ['canonical' => $c[1] ?? null, 'hreflang' => array_map(fn ($x) => $x[1] . '=' . $x[2], $h), 'dateModified' => $d[1] ?? null, 'modified_time' => $m[1] ?? null];
    }

    public static function locales(): array
    {
        return [['tr'], ['en'], ['de']];
    }

    #[DataProvider('locales')]
    public function test_blog_view_counts_but_keeps_updated_at_and_seo_dates(string $locale): void
    {
        $id = $this->makePost($locale, 'blog', "sb1-blog-{$locale}");
        $url = "/{$locale}/blog/sb1-blog-{$locale}";

        $first = $this->get($url)->assertOk()->getContent();
        $second = $this->get($url)->assertOk()->getContent();

        $r = $this->row('posts', $id);
        $this->assertSame(7, (int) $r->view_count, 'analitik: her GET +1');
        $this->assertSame(self::EDITED, (string) $r->updated_at);

        $s1 = $this->headSignals($first);
        $s2 = $this->headSignals($second);
        $this->assertSame($s1, $s2, 'canonical/hreflang/dateModified iki ziyaret arasında aynı');
        $this->assertStringStartsWith('2026-02-20T15:30:00', (string) $s2['dateModified']);
        $this->assertStringContainsString($url, (string) $s2['canonical']);
        $this->assertStringNotContainsString('2026-10-18', $second, '"bugün" tarihi sayfada görünmemeli');
    }

    #[DataProvider('locales')]
    public function test_news_view_counts_but_keeps_updated_at_and_modified_time(string $locale): void
    {
        $id = $this->makePost($locale, 'news', "sb1-news-{$locale}");
        $url = "/{$locale}/news/sb1-news-{$locale}";

        $html = $this->get($url)->assertOk()->getContent();
        $this->get($url)->assertOk();

        $r = $this->row('posts', $id);
        $this->assertSame(7, (int) $r->view_count);
        $this->assertSame(self::EDITED, (string) $r->updated_at);
        $this->assertStringStartsWith('2026-02-20T15:30:00', (string) $this->headSignals($html)['modified_time']);
    }

    #[DataProvider('locales')]
    public function test_faq_view_counts_but_keeps_updated_at(string $locale): void
    {
        $id = $this->faq($locale, "sb1-faq-{$locale}");
        $this->get("/{$locale}/faq/master/sb1-faq-{$locale}")->assertOk();

        $r = $this->row('faqs', $id);
        $this->assertSame(6, (int) $r->view_count);
        $this->assertSame(self::EDITED, (string) $r->updated_at);
    }

    public function test_helpful_votes_keep_updated_at(): void
    {
        $postId = $this->makePost('tr', 'blog', 'sb1-vote');
        $faqId = $this->faq('tr', 'sb1-vote-faq');

        $this->postJson('/api/blog-feedback', ['post_id' => $postId, 'vote' => 'up'])->assertOk();
        $this->postJson('/api/faq-feedback', ['faq_id' => $faqId, 'vote' => 'down'])->assertOk();

        $this->assertSame(1, (int) DB::table('posts')->where('id', $postId)->value('helpful_count'));
        $this->assertSame(1, (int) DB::table('faqs')->where('id', $faqId)->value('unhelpful_count'));
        $this->assertSame(self::EDITED, (string) DB::table('posts')->where('id', $postId)->value('updated_at'));
        $this->assertSame(self::EDITED, (string) DB::table('faqs')->where('id', $faqId)->value('updated_at'));
    }

    public function test_job_view_keeps_updated_at(): void
    {
        $id = DB::table('job_postings')->insertGetId([
            'slug' => 'sb1-job', 'title' => 'SB1 Job', 'position_type' => 'phd', 'is_active' => true, 'view_count' => 5,
            'posted_at' => self::PUBLISHED, 'created_at' => self::PUBLISHED, 'updated_at' => self::EDITED,
        ]);
        $this->get('/tr/jobs/sb1-job')->assertOk();

        $r = $this->row('job_postings', $id);
        $this->assertSame(6, (int) $r->view_count);
        $this->assertSame(self::EDITED, (string) $r->updated_at);
    }

    public function test_sitemap_lastmod_is_not_moved_by_views(): void
    {
        $this->makePost('tr', 'blog', 'sb1-sitemap');
        $lastmod = fn () => preg_match('#sb1-sitemap</loc>\s*<lastmod>([^<]+)</lastmod>#', $this->get('/sitemap-tr.xml')->getContent(), $m) ? $m[1] : null;

        $before = $lastmod();
        $this->assertNotNull($before, 'yazı sitemap\'te olmalı');
        $this->get('/tr/blog/sb1-sitemap')->assertOk();
        $this->assertSame($before, $lastmod());
        $this->assertStringStartsWith('2026-02-20', $before);
    }

    public function test_real_content_edit_still_moves_updated_at(): void
    {
        $id = $this->makePost('en', 'blog', 'sb1-edit');
        $post = Post::findOrFail($id);
        $post->content_md = "## Section\n\nEdited text.";
        $post->save();

        $this->assertSame('2026-10-18 12:00:00', (string) DB::table('posts')->where('id', $id)->value('updated_at'));
    }
}
