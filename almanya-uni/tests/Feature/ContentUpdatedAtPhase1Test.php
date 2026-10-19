<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * content_updated_at Faz 1 — yalnız gerçek editoryal içerik değişikliği tarihi ilerletir
 * (docs/SEO_CONTENT_UPDATED_AT_PHASE0_RESULT.md §3.1–3.2). Okuma noktaları Faz 2'de; bu test onlara dokunmaz.
 */
class ContentUpdatedAtPhase1Test extends TestCase
{
    use RefreshDatabase;

    private const T0 = '2026-10-18 09:00:00';
    private const T1 = '2026-10-19 10:00:00';

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(self::T0);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function newPost(array $extra = []): Post
    {
        $p = new Post();
        $p->forceFill(array_merge([
            'title' => 'Başlık', 'slug' => 'cu-' . Str::random(6), 'locale' => 'tr', 'type' => 'blog',
            'content_md' => "## Bölüm\n\nMetin.", 'excerpt' => 'Özet', 'is_published' => true,
            'published_at' => '2026-10-01 08:00:00', 'translation_group_id' => (string) Str::uuid(),
        ], $extra))->save();

        return $p->fresh();
    }

    private function newFaq(): Faq
    {
        $topic = DB::table('faq_topics')->insertGetId(['name' => 'Master', 'slug' => 'master-' . Str::random(4), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
        $f = new Faq();
        $f->forceFill(['faq_topic_id' => $topic, 'locale' => 'tr', 'question' => 'Soru?', 'slug' => 'cu-faq-' . Str::random(6), 'answer_md' => 'Cevap.', 'is_published' => true])->save();

        return $f->fresh();
    }

    private function at(string $time): void
    {
        Carbon::setTestNow($time);
    }

    public function test_migration_adds_nullable_columns_to_posts_and_faqs(): void
    {
        foreach (['posts', 'faqs'] as $t) {
            foreach (['content_updated_at', 'content_updated_floor', 'content_date_source', 'content_hash'] as $c) {
                $this->assertTrue(Schema::hasColumn($t, $c), "{$t}.{$c}");
            }
        }
        // Model olayı olmadan eklenen eski kayıtlar boş kalır (veri dokunulmaz).
        $id = DB::table('posts')->insertGetId(['title' => 'x', 'slug' => 'raw-x', 'locale' => 'tr', 'content_md' => 'x', 'is_published' => true, 'created_at' => now(), 'updated_at' => now()]);
        $this->assertNull(DB::table('posts')->where('id', $id)->value('content_updated_at'));
    }

    public function test_creating_content_records_the_date(): void
    {
        $p = $this->newPost();
        $this->assertSame(self::T0, (string) $p->content_updated_at);
        $this->assertSame('recorded', $p->content_date_source);
        $this->assertSame(Post::contentHashOf($p->getAttributes()), $p->content_hash);

        $f = $this->newFaq();
        $this->assertSame(self::T0, (string) $f->content_updated_at);
    }

    public function test_editing_each_content_field_advances_the_date(): void
    {
        foreach (['title' => 'Yeni başlık', 'excerpt' => 'Yeni özet', 'content_md' => "## Bölüm\n\nYeni metin.", 'meta_title' => 'Meta', 'meta_description' => 'Açıklama'] as $field => $value) {
            $this->at(self::T0);
            $p = $this->newPost();
            $this->at(self::T1);
            $p->{$field} = $value;
            $p->save();
            $this->assertSame(self::T1, (string) $p->fresh()->content_updated_at, $field);
        }

        $this->at(self::T0);
        $f = $this->newFaq();
        $this->at(self::T1);
        $f->answer_md = 'Güncel cevap.';
        $f->save();
        $this->assertSame(self::T1, (string) $f->fresh()->content_updated_at);
    }

    public function test_non_content_changes_do_not_advance_the_date_but_updated_at_still_moves(): void
    {
        $p = $this->newPost(['featured_image' => 'blog/x.jpg', 'featured_image_caption' => 'Alt yazı']);
        $this->at(self::T1);
        $p->forceFill(['featured_image' => null, 'featured_image_caption' => null, 'slug' => 'yeni-slug-' . Str::random(4), 'is_published' => false, 'view_count' => 99])->save();

        $fresh = $p->fresh();
        $this->assertSame(self::T0, (string) $fresh->content_updated_at);
        $this->assertSame(self::T1, (string) $fresh->updated_at, 'updated_at kayıt değişikliğini izlemeye devam eder');

        $f = $this->newFaq();
        $this->at('2026-10-20 10:00:00');
        $f->forceFill(['sort_order' => 5, 'is_featured' => true])->save();
        $this->assertSame(self::T1, (string) $f->fresh()->content_updated_at);
    }

    public function test_resaving_identical_text_or_line_ending_change_does_not_advance(): void
    {
        $p = $this->newPost();
        $this->at(self::T1);
        $p->content_md = str_replace("\n", "\r\n", $p->content_md) . "\n";
        $p->save();
        $this->assertSame(self::T0, (string) $p->fresh()->content_updated_at);
    }

    public function test_maintenance_saves_do_not_advance(): void
    {
        $p = $this->newPost();
        $this->at(self::T1);
        Post::maintenance(function () use ($p) {
            $p->content_md = "## Bölüm\n\nMetin ve [bağlantı](/tr/blog/hedef).";
            $p->save();
        });
        $this->assertSame(self::T0, (string) $p->fresh()->content_updated_at);
        $this->assertSame(Post::contentHashOf($p->fresh()->getAttributes()), $p->fresh()->content_hash, 'özet yine güncellenir');

        // Bakım bloğundan sonra normal davranış geri gelir.
        $p->title = 'Editoryal değişiklik';
        $p->save();
        $this->assertSame(self::T1, (string) $p->fresh()->content_updated_at);
    }

    public function test_direct_queries_and_counters_do_not_touch_the_date(): void
    {
        $p = $this->newPost();
        $this->at(self::T1);
        Post::where('id', $p->id)->toBase()->increment('view_count');
        DB::table('posts')->where('id', $p->id)->update(['content_html' => '<p>yeniden üretilmiş</p>']);
        $this->get('/tr/blog/' . $p->slug)->assertOk();

        $this->assertSame(self::T0, (string) $p->fresh()->content_updated_at);
    }

    public function test_render_html_command_does_not_advance_updated_at_or_content_date(): void
    {
        $p = $this->newPost();
        DB::table('posts')->where('id', $p->id)->update(['content_html' => null]);
        $before = DB::table('posts')->where('id', $p->id)->first(['updated_at', 'content_updated_at']);

        $this->at(self::T1);
        $this->artisan('blog:render-html', ['--apply' => true, '--id' => $p->id])->assertSuccessful();

        $after = DB::table('posts')->where('id', $p->id)->first(['updated_at', 'content_updated_at', 'content_html']);
        $this->assertNotEmpty($after->content_html, 'HTML üretildi');
        $this->assertSame($before->updated_at, $after->updated_at);
        $this->assertSame($before->content_updated_at, $after->content_updated_at);
    }
}
