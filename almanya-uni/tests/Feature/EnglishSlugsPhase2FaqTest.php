<?php

namespace Tests\Feature;

use App\Support\SlugRedirects;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * İngilizce URL kuralı — Faz 2: FAQ slug'ları İngilizce sorudan üretilir; TR taban, EN -en, DE -de; eski URL'ler 301.
 */
class EnglishSlugsPhase2FaqTest extends TestCase
{
    use RefreshDatabase;

    private const MIGRATION = 'database/migrations/2026_10_13_000300_english_slugs_for_faqs.php';
    private const G = ['a' => '0b4b4b4b-0000-4000-8000-00000000000a', 'b' => '0b4b4b4b-0000-4000-8000-00000000000b', 'c' => '0b4b4b4b-0000-4000-8000-00000000000c', 'd' => '0b4b4b4b-0000-4000-8000-00000000000d', 'e' => '0b4b4b4b-0000-4000-8000-00000000000e'];

    private int $topicId;

    private function run300(): void
    {
        (require base_path(self::MIGRATION))->up();
    }

    private function faq(string $locale, string $slug, string $question, ?string $group, ?int $topicId = null): int
    {
        return DB::table('faqs')->insertGetId(['locale' => $locale, 'slug' => $slug, 'question' => $question, 'answer_md' => 'Cevap ' . $locale, 'answer_html' => '<p>Cevap ' . $locale . '</p>',
            'has_answer' => 1, 'is_published' => 1, 'translation_group_id' => $group, 'faq_topic_id' => $topicId ?? $this->topicId, 'created_at' => now(), 'updated_at' => now()->subDay()]);
    }

    private function seedFaqs(): array
    {
        DB::table('slug_redirects')->delete();
        $this->topicId = DB::table('faq_topics')->insertGetId(['name' => 'Housing', 'slug' => 'housing', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('slug_redirects')->insert(['entity' => 'faq_topic', 'from_slug' => 'yurt', 'to_slug' => 'housing', 'created_at' => now(), 'updated_at' => now()]);
        $ids = [];
        // A: tam grup
        $ids['a_tr'] = $this->faq('tr', 'berlin-munih-hamburg-ortalama-kira-ne-kadar', 'Berlin, Münih, Hamburg ortalama kira ne kadar?', self::G['a']);
        $ids['a_en'] = $this->faq('en', 'berlin-munih-hamburg-ortalama-kira-ne-kadar-en', 'What is the average rent in Berlin, München, Hamburg?', self::G['a']);
        $ids['a_de'] = $this->faq('de', 'berlin-munih-hamburg-ortalama-kira-ne-kadar-de', 'Wie hoch ist die Miete?', self::G['a']);
        // B: yalnız TR → değişmez
        $ids['b_tr'] = $this->faq('tr', 'sadece-turkce-soru', 'Sadece Türkçe soru?', self::G['b']);
        // C ve D: aynı İngilizce soru → ikinci -2 alır
        $ids['c_tr'] = $this->faq('tr', 'ayni-soru-bir', 'Aynı soru bir?', self::G['c']);
        $ids['c_en'] = $this->faq('en', 'ayni-soru-bir-en', 'Can I work while studying?', self::G['c']);
        $ids['d_tr'] = $this->faq('tr', 'ayni-soru-iki', 'Aynı soru iki?', self::G['d']);
        $ids['d_en'] = $this->faq('en', 'ayni-soru-iki-en', 'Can I work while studying?', self::G['d']);
        // E: uzun soru → 80 karakterde kelime sınırı
        $ids['e_tr'] = $this->faq('tr', 'uzun-soru', 'Uzun soru?', self::G['e']);
        $ids['e_en'] = $this->faq('en', 'uzun-soru-en', 'After your visa application, how long does it take on average to receive a positive or negative decision from the embassy?', self::G['e']);
        // Gruba bağlı olmayan kayıt → değişmez
        $ids['x_tr'] = $this->faq('tr', 'grupsuz-kayit', 'Grupsuz?', null);
        DB::table('kb_chunks')->insert(['source_type' => 'faq', 'source_id' => $ids['a_en'], 'locale' => 'en', 'chunk_index' => 0, 'title' => 'rent', 'url' => '/en/faq/housing/berlin-munih-hamburg-ortalama-kira-ne-kadar-en', 'content' => 'x', 'token_estimate' => 1, 'content_hash' => str_repeat('b', 64), 'created_at' => now(), 'updated_at' => now()]);

        return $ids;
    }

    public function test_slug_helper_is_ascii_bounded_and_word_safe(): void
    {
        $this->assertSame('what-is-the-average-rent-in-berlin-munchen-hamburg', SlugRedirects::fromText('What is the average rent in Berlin, München, Hamburg?'));
        $long = SlugRedirects::fromText(str_repeat('word ', 30));
        $this->assertLessThanOrEqual(80, strlen($long));
        $this->assertStringEndsNotWith('-', $long);
        $this->assertSame('', SlugRedirects::fromText('???'));
    }

    public function test_renames_groups_with_english_questions_and_second_run_is_a_noop(): void
    {
        $ids = $this->seedFaqs();
        $this->run300();

        $slug = fn (int $id) => DB::table('faqs')->where('id', $id)->value('slug');
        $this->assertSame('what-is-the-average-rent-in-berlin-munchen-hamburg', $slug($ids['a_tr']));
        $this->assertSame('what-is-the-average-rent-in-berlin-munchen-hamburg-en', $slug($ids['a_en']));
        $this->assertSame('what-is-the-average-rent-in-berlin-munchen-hamburg-de', $slug($ids['a_de']));
        $this->assertSame('sadece-turkce-soru', $slug($ids['b_tr']), 'EN kardeşi olmayan grup değişmez');
        $this->assertSame('grupsuz-kayit', $slug($ids['x_tr']), 'grupsuz kayıt değişmez');
        $this->assertSame('can-i-work-while-studying', $slug($ids['c_tr']));
        $this->assertSame('can-i-work-while-studying-en', $slug($ids['c_en']));
        $this->assertSame('can-i-work-while-studying-2', $slug($ids['d_tr']), 'çakışmada sayı eki');
        $this->assertSame('can-i-work-while-studying-2-en', $slug($ids['d_en']));
        $this->assertLessThanOrEqual(80, strlen($slug($ids['e_tr'])));
        $this->assertStringStartsWith('after-your-visa-application-how-long', $slug($ids['e_tr']));
        $this->assertSame('what-is-the-average-rent-in-berlin-munchen-hamburg-en', DB::table('slug_redirects')->where('entity', 'faq')->where('from_slug', 'berlin-munih-hamburg-ortalama-kira-ne-kadar-en')->value('to_slug'));
        $this->assertSame('/en/faq/housing/what-is-the-average-rent-in-berlin-munchen-hamburg-en', DB::table('kb_chunks')->where('source_id', $ids['a_en'])->value('url'), 'KB URL yeniden yazıldı');
        $this->assertSame(9, DB::table('slug_redirects')->where('entity', 'faq')->count(), 'yalnız yeniden adlandırılan 9 kayıt (A3 + C2 + D2 + E2) için yönlendirme');
        $this->assertSame(0, DB::table('faqs')->where('slug', 'like', '__slugmove%')->count());

        $snap = [DB::table('faqs')->orderBy('id')->get()->toArray(), DB::table('slug_redirects')->orderBy('id')->get()->toArray()];
        $this->run300();
        $this->assertEquals($snap, [DB::table('faqs')->orderBy('id')->get()->toArray(), DB::table('slug_redirects')->orderBy('id')->get()->toArray()], 'rerun no-op');
    }

    public function test_old_urls_redirect_in_one_hop_and_new_pages_render_with_hreflang(): void
    {
        $this->seedFaqs();
        $this->run300();

        $new = url('/en/faq/housing/what-is-the-average-rent-in-berlin-munchen-hamburg-en');
        // eski konu + eski slug → tek 301
        $this->get('/en/faq/yurt/berlin-munih-hamburg-ortalama-kira-ne-kadar-en')->assertStatus(301)->assertRedirect($new);
        // yeni konu + eski slug → 301
        $this->get('/en/faq/housing/berlin-munih-hamburg-ortalama-kira-ne-kadar-en')->assertStatus(301)->assertRedirect($new);
        $this->get('/tr/faq/housing/berlin-munih-hamburg-ortalama-kira-ne-kadar')->assertStatus(301)->assertRedirect(url('/tr/faq/housing/what-is-the-average-rent-in-berlin-munchen-hamburg'));

        $html = $this->get('/en/faq/housing/what-is-the-average-rent-in-berlin-munchen-hamburg-en')->assertOk()->assertSee('Cevap en')->getContent();
        $this->assertStringContainsString('hreflang="tr" href="' . url('/tr/faq/housing/what-is-the-average-rent-in-berlin-munchen-hamburg') . '"', $html);
        $this->assertStringContainsString('hreflang="de" href="' . url('/de/faq/housing/what-is-the-average-rent-in-berlin-munchen-hamburg-de') . '"', $html);
        $this->get('/tr/faq/housing/sadece-turkce-soru')->assertOk();
        $this->get('/tr/faq/housing/olmayan-slug')->assertNotFound();
    }
}
