<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * İngilizce URL kuralı — Faz 1: alan, FAQ konusu ve blog kategorisi slug'ları İngilizce olur; eski URL'ler 301.
 */
class EnglishSlugsPhase1Test extends TestCase
{
    use RefreshDatabase;

    private const MIGRATION = 'database/migrations/2026_10_13_000200_english_slugs_for_fields_faq_topics_and_blog_categories.php';

    private function run200(): void
    {
        (require base_path(self::MIGRATION))->up();
    }

    private function seedOld(): void
    {
        DB::table('slug_redirects')->delete();
        DB::table('fields_of_study')->updateOrInsert(['slug' => 'bilisim'], ['name_tr' => 'Bilişim / Bilgisayar', 'name_en' => 'Computer Science', 'name_de' => 'Informatik', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()->subDay()]);
        DB::table('fields_of_study')->where('slug', 'computer-science')->delete();
        DB::table('faq_topics')->updateOrInsert(['slug' => 'yurt'], ['name' => 'Yurt', 'name_tr' => 'Yurt & Konaklama', 'name_en' => 'Housing & Accommodation', 'name_de' => 'Wohnen', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()->subDay()]);
        DB::table('faq_topics')->where('slug', 'housing')->delete();
        DB::table('categories')->updateOrInsert(['slug' => 'basvuru'], ['name' => 'Başvuru', 'name_tr' => 'Başvuru', 'name_en' => 'Application', 'name_de' => 'Bewerbung', 'kind' => 'blog', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()->subDay()]);
        DB::table('categories')->where('slug', 'application')->delete();
        $topicId = DB::table('faq_topics')->where('slug', 'yurt')->value('id');
        DB::table('faqs')->updateOrInsert(['locale' => 'tr', 'slug' => 'berlin-kira-test'], ['faq_topic_id' => $topicId, 'question' => 'Berlin kira?', 'answer_md' => 'Cevap.', 'answer_html' => '<p>Cevap.</p>', 'has_answer' => 1, 'is_published' => 1, 'translation_group_id' => '0b4b4b4b-0000-4000-8000-000000000099', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('kb_chunks')->insert(['source_type' => 'faq', 'source_id' => 1, 'locale' => 'tr', 'chunk_index' => 0, 'title' => 'Berlin kira?', 'url' => '/tr/faq/yurt/berlin-kira-test', 'content' => 'Cevap.', 'token_estimate' => 1, 'content_hash' => str_repeat('a', 64), 'created_at' => now(), 'updated_at' => now()]);
    }

    public function test_renames_slugs_writes_redirects_and_second_run_is_a_noop(): void
    {
        $this->seedOld();
        $this->run200();

        $this->assertSame('computer-science', DB::table('fields_of_study')->where('name_en', 'Computer Science')->value('slug'));
        $this->assertSame('housing', DB::table('faq_topics')->where('name_en', 'Housing & Accommodation')->value('slug'));
        $this->assertSame('application', DB::table('categories')->where('name_en', 'Application')->value('slug'));
        $this->assertNull(DB::table('fields_of_study')->where('slug', 'bilisim')->first(), 'eski slug kalmadı');
        $this->assertSame('computer-science', DB::table('slug_redirects')->where('entity', 'field')->where('from_slug', 'bilisim')->value('to_slug'));
        $this->assertSame('housing', DB::table('slug_redirects')->where('entity', 'faq_topic')->where('from_slug', 'yurt')->value('to_slug'));
        $this->assertSame('application', DB::table('slug_redirects')->where('entity', 'category')->where('from_slug', 'basvuru')->value('to_slug'));
        $this->assertSame('/tr/faq/housing/berlin-kira-test', DB::table('kb_chunks')->where('source_type', 'faq')->where('source_id', 1)->value('url'), 'KB URL güncellendi');
        $this->assertNull(DB::table('slug_redirects')->where('entity', 'field')->where('from_slug', 'muhendislik')->first(), 'test DB\'de olmayan satır için yönlendirme yazılmaz');

        $snap = [DB::table('fields_of_study')->orderBy('id')->get()->toArray(), DB::table('faq_topics')->orderBy('id')->get()->toArray(), DB::table('categories')->orderBy('id')->get()->toArray(), DB::table('slug_redirects')->orderBy('id')->get()->toArray()];
        $this->run200();
        $this->assertEquals($snap, [DB::table('fields_of_study')->orderBy('id')->get()->toArray(), DB::table('faq_topics')->orderBy('id')->get()->toArray(), DB::table('categories')->orderBy('id')->get()->toArray(), DB::table('slug_redirects')->orderBy('id')->get()->toArray()], 'rerun no-op');
    }

    public function test_old_urls_redirect_permanently_and_new_urls_render(): void
    {
        $this->seedOld();
        $this->run200();

        $this->get('/en/fields/bilisim')->assertStatus(301)->assertRedirect(url('/en/fields/computer-science'));
        $this->get('/en/subjects/bilisim/nc-free')->assertStatus(301)->assertRedirect(url('/en/subjects/computer-science/nc-free'));
        $this->get('/tr/programs/field/bilisim/degree/master')->assertStatus(301)->assertRedirect(url('/tr/programs/field/computer-science/degree/master'));
        $this->get('/de/faq/yurt')->assertStatus(301)->assertRedirect(url('/de/faq/housing'));
        $this->get('/tr/faq/yurt/berlin-kira-test')->assertStatus(301)->assertRedirect(url('/tr/faq/housing/berlin-kira-test'));
        $this->get('/tr/blog/category/basvuru')->assertStatus(301)->assertRedirect(url('/tr/blog/category/application'));
        $this->get('/en/programs?field=bilisim&degree=master')->assertStatus(301)->assertRedirect(url('/en/programs?field=computer-science&degree=master'));

        $this->get('/en/fields/computer-science')->assertOk();
        $this->get('/en/subjects/computer-science/nc-free')->assertOk();
        $this->get('/tr/faq/housing')->assertOk();
        $this->get('/tr/faq/housing/berlin-kira-test')->assertOk()->assertSee('Berlin kira?');
        $this->get('/tr/blog/category/application')->assertOk();
        $this->get('/en/fields/does-not-exist')->assertNotFound();
        $this->get('/en/faq/no-such-topic')->assertNotFound();
    }

    public function test_conflicting_target_slug_blocks_everything(): void
    {
        $this->seedOld();
        DB::table('faq_topics')->insert(['name' => 'Housing2', 'slug' => 'housing', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()]);
        $before = DB::table('faq_topics')->orderBy('id')->get()->toArray();
        try {
            $this->run200();
            $this->fail('RuntimeException bekleniyordu');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString("hem 'yurt' hem 'housing'", $e->getMessage());
        }
        $this->assertEquals($before, DB::table('faq_topics')->orderBy('id')->get()->toArray());
        $this->assertSame('bilisim', DB::table('fields_of_study')->where('name_en', 'Computer Science')->value('slug'), 'alan da yazılmadı');
    }
}
