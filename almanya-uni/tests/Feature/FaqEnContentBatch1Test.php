<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Support\FaqEnglishContentBatch;
use App\Support\SlugRedirects;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

/**
 * EN FAQ içerik paketi (2026_10_13_000700): Türkçe kalmış EN kayıtların onarımı, TR-only gruplara EN kardeş,
 * etkilenen grupların slug'ları İngilizce kurala göre; eski URL tek 301 (doğru dilde), zincir yok, ele geçirme yok,
 * ikinci çalıştırma no-op; beklenmeyen eski metin her şeyi durdurur.
 */
class FaqEnContentBatch1Test extends TestCase
{
    use RefreshDatabase;

    private const MIGRATION = 'database/migrations/2026_10_13_000700_faq_en_content_batch1_repairs_creations_and_slugs.php';
    private const G = ['a' => '0b4b4b4b-0000-4000-8000-00000000000a', 'b' => '0b4b4b4b-0000-4000-8000-00000000000b', 'c' => '0b4b4b4b-0000-4000-8000-00000000000c'];

    private array $topics = [];

    private function faqRow(string $locale, string $slug, string $question, ?string $group, string $topic, string $answer = 'Cevap.'): int
    {
        return DB::table('faqs')->insertGetId(['locale' => $locale, 'slug' => $slug, 'question' => $question, 'answer_md' => $answer, 'answer_html' => "<p>$answer</p>",
            'has_answer' => 1, 'is_published' => 1, 'intent' => 'community', 'translation_group_id' => $group, 'faq_topic_id' => $this->topics[$topic], 'created_at' => now(), 'updated_at' => now()->subDay()]);
    }

    private function seedFixture(): array
    {
        foreach (['work', 'money', 'visa'] as $t) {
            $this->topics[$t] = DB::table('faq_topics')->insertGetId(['name' => ucfirst($t), 'slug' => $t, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()]);
        }
        $ids = [];
        // A: EN kaydı Türkçe kalmış (Faz 2 sonrası slug'lar Türkçe tabanlı) + eski Faz-2 yönlendirmesi (zincir adayı)
        $ids['a_tr'] = $this->faqRow('tr', 'almanyada-zorunlu-olmayan-stajlarda-maas-odenir-mi', 'Almanya\'da zorunlu olmayan stajlarda maaş ödenir mi?', self::G['a'], 'work');
        $ids['a_en'] = $this->faqRow('en', 'almanyada-zorunlu-olmayan-stajlarda-maas-odenir-mi-en', 'Almanya\'da zorunlu olmayan stajlarda maaş ödenir mi?', self::G['a'], 'work', 'Türkçe cevap için.');
        $ids['a_de'] = $this->faqRow('de', 'almanyada-zorunlu-olmayan-stajlarda-maas-odenir-mi-de', 'Almanya\'da zorunlu olmayan stajlarda maaş ödenir mi?', self::G['a'], 'work');
        DB::table('slug_redirects')->insert(['entity' => 'faq', 'from_slug' => 'cok-eski-staj-slug', 'to_slug' => 'almanyada-zorunlu-olmayan-stajlarda-maas-odenir-mi', 'locale' => null, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('kb_chunks')->insert(['source_type' => 'faq', 'source_id' => $ids['a_en'], 'locale' => 'en', 'chunk_index' => 0, 'title' => 't', 'url' => '/en/faq/work/almanyada-zorunlu-olmayan-stajlarda-maas-odenir-mi-en', 'content' => 'x', 'token_estimate' => 1, 'content_hash' => str_repeat('a', 64), 'created_at' => now(), 'updated_at' => now()]);
        // B: yalnız TR → EN kardeş oluşturulacak
        $ids['b_tr'] = $this->faqRow('tr', 'steuer-id-nedir-ve-nasil-alinir', 'Steuer-ID nedir ve nasıl alınır?', self::G['b'], 'money');
        // C: çakışma — başka grubun geçerli slug'ı hedef tabana eşit → -2 eki; C'nin slug'ına dokunulmaz
        $ids['c_tr'] = $this->faqRow('tr', 'are-voluntary-internships-paid-in-germany', 'Başka grubun sorusu?', self::G['c'], 'work');
        $ids['c_en'] = $this->faqRow('en', 'are-voluntary-internships-paid-in-germany-en', 'Are voluntary internships paid in Germany?', self::G['c'], 'work', 'Other group answer.');

        return $ids;
    }

    private function data(array $ids): array
    {
        return [
            'repairs' => [[
                'en_slug' => 'almanyada-zorunlu-olmayan-stajlarda-maas-odenir-mi-en', 'expect_question' => 'Almanya\'da zorunlu olmayan stajlarda maaş ödenir mi?',
                'question' => 'Are voluntary internships paid in Germany?', 'answer_md' => "Usually yes. Under § 22 MiLoG interns are entitled to the minimum wage (€13.90/h in 2026) unless the internship is mandatory or a voluntary one of up to three months.",
            ]],
            'creations' => [[
                'tr_id' => $ids['b_tr'], 'expect_question' => 'Steuer-ID nedir ve nasıl alınır?', 'topic' => 'money',
                'question' => 'What is the Steuer-ID and how do I get it?', 'answer_md' => 'The Steuer-ID is an 11-digit personal tax number sent by post after your first Anmeldung.',
            ]],
        ];
    }

    public function test_plan_writes_nothing_and_apply_then_rerun_is_noop(): void
    {
        $ids = $this->seedFixture();
        $before = DB::table('faqs')->orderBy('id')->get()->toArray();
        $plan = FaqEnglishContentBatch::plan($this->data($ids));
        $this->assertSame([], $plan['problems']);
        $this->assertCount(1, $plan['repairs']);
        $this->assertCount(1, $plan['creations']);
        $this->assertEquals($before, DB::table('faqs')->orderBy('id')->get()->toArray(), 'önizleme yazmaz');
        $this->assertSame(0, DB::table('faqs')->where('locale', 'en')->where('translation_group_id', self::G['b'])->count());

        $counts = FaqEnglishContentBatch::apply($plan);
        $this->assertSame(1, $counts['repairs']);
        $this->assertSame(1, $counts['creations']);

        // Onarım: id + grup korunur, İngilizce içerik, HTML yeniden üretildi
        $en = Faq::findOrFail($ids['a_en']);
        $this->assertSame('Are voluntary internships paid in Germany?', $en->question);
        $this->assertSame(self::G['a'], $en->translation_group_id);
        $this->assertStringContainsString('<p>Usually yes.', $en->answer_html);
        $this->assertFalse($en->contentIsBroken());
        // Çakışma: 'are-voluntary-internships-paid-in-germany' C grubuna ait → A grubu -2 alır, C dokunulmaz
        $this->assertSame('are-voluntary-internships-paid-in-germany-2-en', $en->slug);
        $this->assertSame('are-voluntary-internships-paid-in-germany-2', Faq::findOrFail($ids['a_tr'])->slug);
        $this->assertSame('are-voluntary-internships-paid-in-germany-2-de', Faq::findOrFail($ids['a_de'])->slug);
        $this->assertSame('are-voluntary-internships-paid-in-germany-en', Faq::findOrFail($ids['c_en'])->slug);
        $this->assertSame('are-voluntary-internships-paid-in-germany', Faq::findOrFail($ids['c_tr'])->slug);

        // Oluşturma: TR grubunda, aynı konuda, yayında, İngilizce slug
        $newEn = Faq::where('locale', 'en')->where('translation_group_id', self::G['b'])->firstOrFail();
        $this->assertTrue($newEn->is_published);
        $this->assertTrue($newEn->has_answer);
        $this->assertSame($this->topics['money'], $newEn->faq_topic_id);
        $this->assertSame('what-is-the-steuer-id-and-how-do-i-get-it-en', $newEn->slug);
        $this->assertSame('what-is-the-steuer-id-and-how-do-i-get-it', Faq::findOrFail($ids['b_tr'])->slug);

        // Yönlendirmeler dile özgü, eski Faz-2 satırı son hedefe güncellendi (zincir yok)
        $this->assertSame('are-voluntary-internships-paid-in-germany-2-en', SlugRedirects::to('faq', 'almanyada-zorunlu-olmayan-stajlarda-maas-odenir-mi-en', 'en'));
        $this->assertSame('are-voluntary-internships-paid-in-germany-2', SlugRedirects::to('faq', 'almanyada-zorunlu-olmayan-stajlarda-maas-odenir-mi', 'tr'));
        $this->assertSame('are-voluntary-internships-paid-in-germany-2-de', SlugRedirects::to('faq', 'almanyada-zorunlu-olmayan-stajlarda-maas-odenir-mi-de', 'de'));
        $this->assertSame('are-voluntary-internships-paid-in-germany-2', DB::table('slug_redirects')->where('from_slug', 'cok-eski-staj-slug')->value('to_slug'));
        $this->assertSame('what-is-the-steuer-id-and-how-do-i-get-it', SlugRedirects::to('faq', 'steuer-id-nedir-ve-nasil-alinir', 'tr'));
        $this->assertSame('/en/faq/work/are-voluntary-internships-paid-in-germany-2-en', DB::table('kb_chunks')->where('source_id', $ids['a_en'])->value('url'));

        // İkinci çalıştırma: plan boş, hiçbir şey değişmez
        $snapF = DB::table('faqs')->orderBy('id')->get()->toArray();
        $snapR = DB::table('slug_redirects')->orderBy('id')->get()->toArray();
        $plan2 = FaqEnglishContentBatch::plan($this->data($ids));
        $this->assertSame([], $plan2['problems']);
        $this->assertSame([[], [], [], [], []], [$plan2['repairs'], $plan2['creations'], $plan2['slugs'], $plan2['redirects'], $plan2['rewrites']]);
        $this->assertCount(2, $plan2['skipped']);
        FaqEnglishContentBatch::apply($plan2);
        $this->assertEquals($snapF, DB::table('faqs')->orderBy('id')->get()->toArray());
        $this->assertEquals($snapR, DB::table('slug_redirects')->orderBy('id')->get()->toArray());
    }

    public function test_old_urls_redirect_once_in_the_right_locale_and_new_pages_serve_english(): void
    {
        $ids = $this->seedFixture();
        FaqEnglishContentBatch::apply(FaqEnglishContentBatch::plan($this->data($ids)));

        $this->get('/en/faq/work/almanyada-zorunlu-olmayan-stajlarda-maas-odenir-mi-en')
            ->assertStatus(301)->assertRedirect('/en/faq/work/are-voluntary-internships-paid-in-germany-2-en');
        $this->get('/tr/faq/work/almanyada-zorunlu-olmayan-stajlarda-maas-odenir-mi')
            ->assertStatus(301)->assertRedirect('/tr/faq/work/are-voluntary-internships-paid-in-germany-2');
        // Çapraz dil: eski TR slug /en altında → EN kardeşe tek adımda
        $this->get('/en/faq/work/almanyada-zorunlu-olmayan-stajlarda-maas-odenir-mi')
            ->assertStatus(301)->assertRedirect('/en/faq/work/are-voluntary-internships-paid-in-germany-2-en');
        $this->get('/tr/faq/money/steuer-id-nedir-ve-nasil-alinir')
            ->assertStatus(301)->assertRedirect('/tr/faq/money/what-is-the-steuer-id-and-how-do-i-get-it');
        // Çakışan grubun (C) adresi ele geçirilmedi
        $this->get('/en/faq/work/are-voluntary-internships-paid-in-germany-en')->assertOk()->assertSee('Other group answer.');

        $html = $this->get('/en/faq/work/are-voluntary-internships-paid-in-germany-2-en')->assertOk()->getContent();
        $this->assertStringContainsString('Are voluntary internships paid in Germany?', $html);
        $this->assertStringContainsString('Usually yes.', $html);
        $this->assertStringNotContainsString('Türkçe cevap', $html);
        $this->assertStringContainsString('hreflang="tr"', $html);
        $this->assertStringContainsString('/tr/faq/work/are-voluntary-internships-paid-in-germany-2"', $html);
        $this->assertStringContainsString('/de/faq/work/are-voluntary-internships-paid-in-germany-2-de"', $html);
        $this->assertStringContainsString('rel="canonical" href="' . url('/en/faq/work/are-voluntary-internships-paid-in-germany-2-en') . '"', $html);
        $this->assertStringContainsString('hreflang="x-default" href="' . url('/en/faq/work/are-voluntary-internships-paid-in-germany-2-en') . '"', $html);

        $new = $this->get('/en/faq/money/what-is-the-steuer-id-and-how-do-i-get-it-en')->assertOk()->getContent();
        $this->assertStringContainsString('11-digit personal tax number', $new);
        $this->assertStringContainsString('/tr/faq/money/what-is-the-steuer-id-and-how-do-i-get-it"', $new, 'TR kardeşe hreflang');
        $this->assertStringNotContainsString('<link rel="alternate" hreflang="de"', $new, 'olmayan DE kardeş için hreflang üretilmez');
        $this->assertStringContainsString('<link rel="alternate" hreflang="tr" href="' . url('/tr/faq/money/what-is-the-steuer-id-and-how-do-i-get-it') . '"', $new);
        $this->get('/en/faq/money')->assertOk()->assertSee('What is the Steuer-ID and how do I get it?');
    }

    public function test_unexpected_old_text_is_never_overwritten_and_existing_en_sibling_prevents_a_duplicate(): void
    {
        $ids = $this->seedFixture();
        DB::table('faqs')->where('id', $ids['a_en'])->update(['question' => 'Elle düzenlenmiş soru?']);
        $beforeA = DB::table('faqs')->whereIn('id', [$ids['a_tr'], $ids['a_en'], $ids['a_de']])->orderBy('id')->get()->toArray();
        $plan = FaqEnglishContentBatch::plan($this->data($ids));
        $this->assertSame([], $plan['problems']);
        $this->assertSame([], $plan['repairs'], 'uyuşmayan eski metin üzerine yazılmaz');
        $this->assertStringContainsString('eşleşmiyor', implode(' ', $plan['skipped']));
        $this->assertCount(1, $plan['creations'], 'diğer maddeler devam eder');
        FaqEnglishContentBatch::apply($plan);
        $this->assertEquals($beforeA, DB::table('faqs')->whereIn('id', [$ids['a_tr'], $ids['a_en'], $ids['a_de']])->orderBy('id')->get()->toArray(), 'A grubu (slug dahil) dokunulmadı');
        $this->assertSame(1, DB::table('faqs')->where('translation_group_id', self::G['b'])->where('locale', 'en')->count());

        // Grupta taslak EN varsa yeni kayıt açılmaz (ikinci grup örneği)
        $gC = '0b4b4b4b-0000-4000-8000-00000000000f';
        $cTr = $this->faqRow('tr', 'bloke-hesap-adresi', 'Bloke hesap açarken hangi adresi belirtmeliyim?', $gC, 'money');
        $this->faqRow('en', 'blocked-draft-en', 'Draft about the blocked account address?', $gC, 'money');
        DB::table('faqs')->where('slug', 'blocked-draft-en')->update(['is_published' => 0]);
        $data = ['repairs' => [], 'creations' => [['tr_id' => $cTr, 'expect_question' => 'Bloke hesap açarken hangi adresi belirtmeliyim?', 'topic' => 'money', 'question' => 'Which address should I give when opening a blocked account?', 'answer_md' => 'Use the address the provider asks for; most accept your home address abroad.']]];
        $plan = FaqEnglishContentBatch::plan($data);
        $this->assertSame([], $plan['problems']);
        $this->assertSame([], $plan['creations']);
        $this->assertStringContainsString('zaten var', implode(' ', $plan['skipped']));
        $this->assertSame(1, DB::table('faqs')->where('translation_group_id', $gC)->where('locale', 'en')->count());
        $this->assertSame('bloke-hesap-adresi', DB::table('faqs')->where('id', $cTr)->value('slug'), 'atlanan grubun slug\'ına dokunulmaz');

        // Tipografik kesme işareti / boşluk farkı içerik farkı sayılmaz
        $this->assertTrue(FaqEnglishContentBatch::same("Almanya’da  staj? ", "Almanya'da staj?"));
        $this->assertFalse(FaqEnglishContentBatch::same('Almanya\'da staj?', 'Almanya\'da stajlar?'));
    }

    public function test_migration_is_silent_on_a_database_without_the_batch_records(): void
    {
        $before = DB::table('faqs')->count();
        (require base_path(self::MIGRATION))->up();
        $this->assertSame($before, DB::table('faqs')->count());
    }

    public function test_real_batch_file_is_well_formed_english_with_unique_short_slugs(): void
    {
        $data = FaqEnglishContentBatch::load(database_path('data/faq-en/2026-10-13-batch1.json'));
        $this->assertCount(12, $data['repairs']);
        $this->assertCount(10, $data['creations']);
        $bases = [];
        foreach (array_merge($data['repairs'], $data['creations']) as $item) {
            $this->assertFalse(Faq::looksTurkish($item['question']), $item['question']);
            $this->assertFalse(Faq::looksTurkish($item['answer_md']), 'Türkçe cevap: ' . $item['question']);
            $this->assertGreaterThan(400, mb_strlen($item['answer_md']), 'çok kısa cevap: ' . $item['question']);
            $base = SlugRedirects::fromText($item['question']);
            $this->assertLessThanOrEqual(80, strlen($base));
            $this->assertNotContains($base, $bases, "çift slug tabanı: $base");
            $bases[] = $base;
        }
        $ids = array_merge(array_column($data['creations'], 'tr_id'), array_column($data['pending'] ?? [], 'tr_id'), array_column($data['dropped_creations'] ?? [], 'tr_id'));
        $this->assertSame(count($ids), count(array_unique($ids)), 'aynı TR kayıt birden fazla listede');
        $slugs = array_merge(array_column($data['repairs'], 'en_slug'), array_column($data['already_english_on_prod'] ?? [], 'en_slug'));
        $this->assertSame(count($slugs), count(array_unique($slugs)), 'aynı EN slug birden fazla listede');
    }
}
