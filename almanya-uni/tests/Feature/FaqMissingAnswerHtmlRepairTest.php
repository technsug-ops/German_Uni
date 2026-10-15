<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * 2026_10_15_000200: answer_md dolu ama answer_html boş SSS'lerin HTML'i yeniden üretilir.
 * Metin değişmez, dolu HTML'e dokunulmaz, tekrar çalıştırma no-op.
 */
class FaqMissingAnswerHtmlRepairTest extends TestCase
{
    use RefreshDatabase;

    private function faq(string $slug, string $md, ?string $html): int
    {
        $topic = DB::table('faq_topics')->insertGetId(['name' => 'Vize', 'slug' => 'visa-' . $slug, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()]);

        return DB::table('faqs')->insertGetId(['locale' => 'de', 'slug' => $slug, 'question' => 'Frage?', 'answer_md' => $md, 'answer_html' => $html,
            'has_answer' => 1, 'is_published' => 1, 'intent' => 'community', 'faq_topic_id' => $topic, 'created_at' => now(), 'updated_at' => now()]);
    }

    public function test_regenerates_only_missing_html_and_is_idempotent(): void
    {
        $empty = $this->faq('leer-de', "## Dauer\n\n- **140 volle Arbeitstage** pro Jahr", null);
        $blank = $this->faq('blank-de', 'Kurzer Text.', '');
        $ok = $this->faq('ok-de', 'Anderer Text.', '<p>Bestehendes HTML</p>');

        $migration = require database_path('migrations/2026_10_15_000200_regenerate_missing_faq_answer_html.php');
        $migration->up();

        $html = DB::table('faqs')->where('id', $empty)->value('answer_html');
        $this->assertStringContainsString('<strong>140 volle Arbeitstage</strong>', $html);
        $this->assertSame("## Dauer\n\n- **140 volle Arbeitstage** pro Jahr", DB::table('faqs')->where('id', $empty)->value('answer_md'));
        $this->assertStringContainsString('Kurzer Text.', DB::table('faqs')->where('id', $blank)->value('answer_html'));
        $this->assertSame('<p>Bestehendes HTML</p>', DB::table('faqs')->where('id', $ok)->value('answer_html'));

        $before = DB::table('faqs')->orderBy('id')->pluck('answer_html', 'id')->all();
        $migration->up();
        $this->assertSame($before, DB::table('faqs')->orderBy('id')->pluck('answer_html', 'id')->all());
    }
}
