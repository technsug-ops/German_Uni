<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

/**
 * 2026_10_12_000100: Batch 4 rehberlerindeki sağlık sigortası bağlantısı prod slug'ına çevrilir;
 * her rehberde tam bir kez, rerun no-op, beklenmeyen içerikte hiçbir şey yazılmaz.
 */
class Batch4HealthInsuranceLinkFixTest extends TestCase
{
    use RefreshDatabase;

    private const MIGRATION = 'database/migrations/2026_10_12_000100_fix_health_insurance_link_in_living_cost_guides.php';

    private const GUIDES = [
        'tr' => ['real-cost-of-being-a-student-in-germany-budget-truth', '/tr/blog/health-insurance-comparison-2026-which-krankenkasse-is-best-for-students-in', '/tr/blog/krankenkasse-comparison-2026-which-is-the-best-option-for-students'],
        'en' => ['real-cost-student-germany-budget-reality-check', '/en/blog/health-insurance-comparison-2026-which-krankenkasse-is-best-for-students-in-en', '/en/blog/krankenkasse-comparison-2026-which-is-the-best-option-for-students-en'],
        'de' => ['wahre-kosten-studium-deutschland-budget-check', '/de/blog/health-insurance-comparison-2026-which-krankenkasse-is-best-for-students-in-de', '/de/blog/krankenkasse-comparison-2026-which-is-the-best-option-for-students-de'],
    ];

    private function run100(): void
    {
        (require base_path(self::MIGRATION))->up();
    }

    private function seedGuides(): void
    {
        foreach (self::GUIDES as $loc => [$slug, $old]) {
            DB::table('posts')->updateOrInsert(['locale' => $loc, 'slug' => $slug], [
                'type' => 'blog', 'title' => 'Rehber ' . $loc, 'translation_group_id' => '0b4b4b4b-0000-4000-8000-000000000004',
                'content_md' => "Giriş.\n\n- [sigorta]($old) ve [bloke hesap](/$loc/blog/sperrkonto-x)\n\nSon.",
                'content_html' => '<p>eski</p>', 'is_published' => 1, 'published_at' => now()->subMonths(4), 'created_at' => now()->subMonths(4), 'updated_at' => now()->subDay(),
            ]);
        }
    }

    public function test_replaces_link_once_per_guide_and_second_run_is_a_noop(): void
    {
        $this->seedGuides();
        $this->run100();

        foreach (self::GUIDES as $loc => [$slug, $old, $new]) {
            $post = Post::where('locale', $loc)->where('slug', $slug)->firstOrFail();
            $this->assertSame(1, substr_count($post->content_md, $new . ')'), "$loc yeni bağlantı tam bir kez");
            $this->assertStringNotContainsString($old . ')', $post->content_md, "$loc eski bağlantı kalmadı");
            $this->assertStringContainsString($new, (string) $post->content_html, "$loc content_html yeniden üretildi");
            $this->assertStringContainsString('/blog/sperrkonto-x', $post->content_md, 'diğer bağlantılar dokunulmadı');
        }
        $snapshot = DB::table('posts')->orderBy('id')->get()->toArray();
        $this->run100();
        $this->assertEquals($snapshot, DB::table('posts')->orderBy('id')->get()->toArray(), 'rerun no-op');
    }

    public function test_unexpected_content_blocks_everything(): void
    {
        $this->seedGuides();
        [$slug, $old] = self::GUIDES['de'];
        DB::table('posts')->where('locale', 'de')->where('slug', $slug)->update(['content_md' => "Elle değiştirilmiş; bağlantı yok."]);
        $before = DB::table('posts')->orderBy('id')->get()->toArray();

        try {
            $this->run100();
            $this->fail('RuntimeException bekleniyordu');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('de/' . $slug, $e->getMessage());
        }
        $this->assertEquals($before, DB::table('posts')->orderBy('id')->get()->toArray(), 'TR/EN de yazılmadı');
    }

    public function test_missing_guides_are_a_noop_in_testing(): void
    {
        DB::table('posts')->whereIn('slug', array_column(self::GUIDES, 0))->delete();
        $before = DB::table('posts')->orderBy('id')->get()->toArray();
        $this->run100();
        $this->assertEquals($before, DB::table('posts')->orderBy('id')->get()->toArray());
    }
}
