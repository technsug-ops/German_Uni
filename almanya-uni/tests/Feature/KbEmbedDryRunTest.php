<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * kb:embed --dry-run, gerçek çalıştırmanın yapacağı işi göstermeli: değişmemiş chunk'lar "atlandı",
 * yalnız içeriği değişenler "embed edilecek" sayılır. Dry-run hiçbir şey silmez, yazmaz, API çağırmaz.
 * (Önceden dry-run hash karşılaştırmasını atlıyor ve her şeyi "embed edilecek" gösteriyordu.)
 */
class KbEmbedDryRunTest extends TestCase
{
    use RefreshDatabase;

    private function faq(string $slug, string $q, string $a): int
    {
        $topic = DB::table('faq_topics')->where('slug', 'genel-test')->value('id')
            ?? DB::table('faq_topics')->insertGetId(['name' => 'Genel', 'slug' => 'genel-test', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()]);

        return DB::table('faqs')->insertGetId(['locale' => 'en', 'slug' => $slug, 'question' => $q, 'answer_md' => $a, 'answer_html' => "<p>$a</p>",
            'has_answer' => 1, 'is_published' => 1, 'faq_topic_id' => $topic, 'created_at' => now(), 'updated_at' => now()]);
    }

    private function chunk(int $id, string $content): void
    {
        DB::table('kb_chunks')->insert(['source_type' => 'faq', 'source_id' => $id, 'locale' => 'en', 'chunk_index' => 0, 'title' => 't', 'url' => '/en/faq/x/y',
            'content' => $content, 'token_estimate' => 1, 'content_hash' => hash('sha256', $content), 'created_at' => now(), 'updated_at' => now()]);
    }

    public function test_dry_run_compares_hashes_and_writes_nothing(): void
    {
        DB::table('kb_chunks')->delete();
        $same = $this->faq('same-faq', 'Is it the same?', 'Yes it is the same.');
        $this->chunk($same, "Is it the same?\n\nYes it is the same.");
        $changed = $this->faq('changed-faq', 'Was it changed?', 'The new answer text.');
        $this->chunk($changed, "Was it changed?\n\nThe old answer text.");
        $before = DB::table('kb_chunks')->orderBy('id')->get()->toArray();

        $this->artisan('kb:embed', ['--source' => 'faq', '--dry-run' => true])
            ->expectsOutputToContain('embed edilen chunk: 1 · değişmeyen (atlandı): 1')
            ->assertSuccessful();

        $this->assertEquals($before, DB::table('kb_chunks')->orderBy('id')->get()->toArray(), 'dry-run silmez/yazmaz');
    }
}
