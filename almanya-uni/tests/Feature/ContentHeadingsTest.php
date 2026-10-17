<?php

namespace Tests\Feature;

use App\Support\ContentHeadings;
use App\Support\MarkdownRenderer;
use App\Support\TocBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * SEO Recovery P1-1 / Paket B — blog/haber sayfasında tek H1 (şablondaki başlık).
 * Gövdedeki H1 yalnız render'da kaldırılır/H2'ye çevrilir; kayıtlı içerik ve mevcut çapalar değişmez.
 */
class ContentHeadingsTest extends TestCase
{
    use RefreshDatabase;

    /* ------------------------------------------------------------ birim */

    public function test_leading_h1_same_as_title_is_removed(): void
    {
        $html = "<h1>Almanya'da Yüksek Lisans 2026: A'dan Z'ye Rehber</h1>\n<p>Giriş.</p>\n<h2>Şartlar</h2>";
        $out = ContentHeadings::normalizeH1($html, "Almanya'da Yüksek Lisans: A'dan Z'ye Rehber");

        $this->assertStringNotContainsString('<h1', $out);
        $this->assertStringNotContainsString('Yüksek Lisans 2026', $out);
        $this->assertStringStartsWith('<p>Giriş.</p>', $out);
        $this->assertStringContainsString('<h2>Şartlar</h2>', $out);
    }

    public function test_body_starting_with_h2_is_untouched(): void
    {
        $html = "<h2>Bölüm</h2>\n<p>Metin.</p>\n<h3>Alt</h3>";
        $this->assertSame($html, ContentHeadings::normalizeH1($html, 'Başlık'));
    }

    public function test_leading_h1_with_different_meaning_becomes_h2(): void
    {
        $out = ContentHeadings::normalizeH1("<h1>Wohnungssuche in München</h1>\n<p>x</p>", 'Studiengebühren in Baden-Württemberg');

        $this->assertStringNotContainsString('<h1', $out);
        $this->assertStringContainsString('<h2 id="post-heading-1">Wohnungssuche in München</h2>', $out);
    }

    public function test_later_h1_becomes_h2_even_if_it_repeats_the_title(): void
    {
        $html = "<h1>Studienkolleg Guide 2026</h1>\n<p>Intro.</p>\n<h1>Studienkolleg Guide — Which course?</h1>\n<h2>Who needs it?</h2>";
        $out = ContentHeadings::normalizeH1($html, 'Studienkolleg Guide');

        $this->assertSame(0, preg_match_all('/<h1\b/', $out));
        $this->assertStringContainsString('<h2 id="post-heading-2">Studienkolleg Guide — Which course?</h2>', $out);
        $this->assertStringStartsWith('<p>Intro.</p>', $out);
    }

    public function test_h1_inside_code_block_is_not_a_heading(): void
    {
        $html = "<h2>Örnek</h2>\n<pre><code class=\"language-md\"># Bu bir başlık değil\n<h1>ham etiket</h1></code></pre>";
        $this->assertSame($html, ContentHeadings::normalizeH1($html, 'Örnek'));
    }

    public function test_markdown_pipeline_with_hash_in_code_fence(): void
    {
        $md = "# Rehber Başlığı\n\nGiriş.\n\n## Birinci bölüm\n\n```bash\n# yorum satırı\necho 1\n```\n\n### Alt bölüm\n";
        $html = app(MarkdownRenderer::class)->render($md);
        $out = ContentHeadings::normalizeH1($html, 'Rehber Başlığı');

        $this->assertSame(0, preg_match_all('/<h1\b/', $out));
        $this->assertStringContainsString('# yorum satırı', $out);
        $this->assertSame(1, preg_match_all('/<h2\b/', $out));
        $this->assertSame(1, preg_match_all('/<h3\b/', $out));
    }

    public function test_existing_heading_anchors_and_toc_ids_are_preserved(): void
    {
        // Uzun başlıklar TocBuilder'da sıra numaralı kimlik (toc-hN) alır; çevrilen H1 bu numaraları kaydırmamalı.
        $long = str_repeat('Çok uzun bir bölüm başlığı ', 5);
        $html = "<h1>Farklı bir tanıtım bölümü başlığı burada</h1>\n<p>x</p>\n<h2>{$long}</h2>\n<h2>Short</h2>\n<h2>Short</h2>\n<h3>Detay</h3>";
        $before = TocBuilder::process($html);
        $after = TocBuilder::process(ContentHeadings::normalizeH1($html, 'Sayfanın asıl başlığı Almanya'));

        $ids = fn ($h) => (preg_match_all('/\bid="([^"]+)"/', $h, $m) ? $m[1] : []);
        $this->assertSame([], array_values(array_diff($ids($before['html']), $ids($after['html']))));
        $this->assertSame(['toc-h1', 'short', 'short-2'], array_column($before['toc'], 'id'));
        $this->assertSame(['post-heading-1', 'toc-h1', 'short', 'short-2'], array_column($after['toc'], 'id'));
    }

    #[DataProvider('titlePairs')]
    public function test_same_title_judgement(string $heading, string $title, bool $same): void
    {
        $this->assertSame($same, ContentHeadings::sameAsTitle($heading, $title));
    }

    public static function titlePairs(): array
    {
        return [
            ['Rundfunkbeitrag (GEZ) Student Guide 2026: Is there an Exemption, How to Apply?', 'Rundfunkbeitrag (GEZ) Student Guide: Exemption and Application', true],
            ['Ist das Studium in Deutschland kostenlos? Die echten Kosten 2026', 'Ist das Studium in Deutschland kostenlos? Die echten Kosten', true],
            ['İNGİLİZCE MASTER', 'İngilizce Master', true],
            ['Wohnungssuche in München', 'Studiengebühren in Baden-Württemberg', false],
        ];
    }

    /* ------------------------------------------------------------ sayfa render */

    private function makePost(string $locale, string $type, string $slug, string $title, string $md): void
    {
        $post = new \App\Models\Post();
        $post->forceFill([
            'title' => $title, 'slug' => $slug, 'locale' => $locale, 'type' => $type, 'content_md' => $md,
            'is_published' => true, 'published_at' => now()->subDay(), 'translation_group_id' => (string) Str::uuid(),
        ])->save(); // saving kancası content_html'i Markdown'dan üretir (gerçek akış)
    }

    public static function locales(): array
    {
        return [['tr'], ['en'], ['de']];
    }

    #[DataProvider('locales')]
    public function test_blog_and_news_pages_have_single_h1_and_working_toc(string $locale): void
    {
        $md = "# Title {$locale} 2026\n\nIntro.\n\n## First section\n\nText.\n\n## Second section\n\n### Detail\n\n```\n# not a heading\n```\n";
        $this->makePost($locale, 'blog', "p-b-blog-{$locale}", "Title {$locale}", $md);
        $this->makePost($locale, 'news', "p-b-news-{$locale}", "Title {$locale}", $md);
        $storedBefore = DB::table('posts')->whereIn('slug', ["p-b-blog-{$locale}", "p-b-news-{$locale}"])->orderBy('slug')->pluck('content_html')->all();

        foreach (["/{$locale}/blog/p-b-blog-{$locale}", "/{$locale}/news/p-b-news-{$locale}"] as $url) {
            $html = $this->get($url)->assertOk()->getContent();
            $this->assertSame(1, preg_match_all('/<h1\b/i', $html), "{$url}: tek H1 değil");
            $this->assertStringContainsString('# not a heading', $html);
            $this->assertStringContainsString('First section', $html);
        }

        // İçindekiler (blog): her bağlantının sayfada hedefi var.
        $blog = $this->get("/{$locale}/blog/p-b-blog-{$locale}")->getContent();
        $this->assertSame(1, preg_match('/<nav id="post-toc-ssr".*?<\/nav>/s', $blog, $nav));
        preg_match_all('/href="#([^"]+)"/', $nav[0], $targets);
        $this->assertCount(2, $targets[1]);
        foreach ($targets[1] as $id) {
            $this->assertStringContainsString('id="' . $id . '"', $blog, "TOC hedefi yok: #{$id}");
        }

        // Kayıtlı içerik değişmedi (gövdedeki H1 DB'de duruyor; yalnız render'da ele alınıyor).
        $storedAfter = DB::table('posts')->whereIn('slug', ["p-b-blog-{$locale}", "p-b-news-{$locale}"])->orderBy('slug')->pluck('content_html')->all();
        $this->assertSame($storedBefore, $storedAfter);
        $this->assertStringContainsString('<h1', $storedAfter[0]);
    }
}
