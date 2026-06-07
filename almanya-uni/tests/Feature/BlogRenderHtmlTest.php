<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * blog:render-html komutu — content_html boş kalan yazıları content_md'den
 * backfill eder (mutator-bypass legacy importlar için self-heal).
 *
 * DB::table ile insert: Post saving() mutator'ını atlayıp content_html'i
 * gerçekten boş bırakabilmek için (Eloquent create() mutator'ı tetiklerdi).
 */
class BlogRenderHtmlTest extends TestCase
{
    use RefreshDatabase;

    private function insertPost(array $attrs): int
    {
        return DB::table('posts')->insertGetId(array_merge([
            'locale' => 'tr',
            'type' => 'blog',
            'title' => 'Test',
            'slug' => 'test-' . uniqid(),
            'content_md' => "# Başlık\n\nBu paragraf, render testi için yeterince uzun bir gövde metni içerir.",
            'content_html' => '',
            'is_published' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ], $attrs));
    }

    public function test_bos_content_html_backfill_edilir(): void
    {
        $id = $this->insertPost(['content_html' => '']);

        $this->artisan('blog:render-html', ['--apply' => true])->assertExitCode(0);

        $html = DB::table('posts')->where('id', $id)->value('content_html');
        $this->assertNotEmpty($html);
        $this->assertStringContainsString('render testi için', strip_tags($html));
    }

    public function test_dolu_content_html_korunur(): void
    {
        $id = $this->insertPost(['content_html' => '<p>DOKUNMA-MEVCUT</p>']);

        $this->artisan('blog:render-html', ['--apply' => true])->assertExitCode(0);

        $html = DB::table('posts')->where('id', $id)->value('content_html');
        $this->assertSame('<p>DOKUNMA-MEVCUT</p>', $html);
    }

    public function test_dry_run_degisiklik_yapmaz(): void
    {
        $id = $this->insertPost(['content_html' => '']);

        // --apply yok → sadece raporlamalı, yazmamalı.
        $this->artisan('blog:render-html')->assertExitCode(0);

        $html = DB::table('posts')->where('id', $id)->value('content_html');
        $this->assertSame('', $html);
    }

    public function test_id_filtresi_sadece_hedef_postu_isler(): void
    {
        $hedef = $this->insertPost(['content_html' => '']);
        $diger = $this->insertPost(['content_html' => '']);

        $this->artisan('blog:render-html', ['--apply' => true, '--id' => $hedef])->assertExitCode(0);

        $this->assertNotEmpty(DB::table('posts')->where('id', $hedef)->value('content_html'));
        // Diğer post --id filtresi dışında → dokunulmamalı.
        $this->assertSame('', DB::table('posts')->where('id', $diger)->value('content_html'));
    }
}
