<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

/**
 * 2026_10_12_000200: kira FAQ'si (TR/EN/DE) ve ücret rehberinin (TR/DE) ulaşım blokları kaynaklı metinle değiştirilir;
 * ön doğrulama, rerun no-op, elle değiştirilmiş içerik ezilmez, eksik kardeş oluşturulmaz.
 */
class RentFaqAndFeeGuideTransportAlignmentTest extends TestCase
{
    use RefreshDatabase;

    private const MIGRATION = 'database/migrations/2026_10_12_000200_align_rent_faq_and_fee_guide_transport_with_sources.php';
    private const FAQ_GROUP = '58a03f5c-0000-4000-8000-000000000001';
    private const GUIDE_GROUP = '234c7f38-0000-4000-8000-000000000002';

    private const OLD_FAQ = "2026 başı itibarıyla ortalama aylık kira:\n\n## Berlin\n\n| Tür | Aylık (warm) |\n| --- | --- |\n| Studierendenwerk yurdu | 240-340 € |\n| WG odası | 480-650 € |\n\n## Münih\n\n| Studierendenwerk yurdu | 290-380 € |\n| WG odası | 650-900 € |\n\n## Hamburg\n\n| Studierendenwerk yurdu | 260-360 € |\n| WG odası | 520-720 € |\n";

    private const OLD_GUIDE_TR = "# Başlık\n\n| Universität Bonn | ~330 € |\n\n**Pratik bilgi:** 330 € öderken **150 € Semesterticket'a gidiyor** — yani Berlin BVG aboneliği 6 ay boyunca dahil. Tek başına aylık 60 € olan toplu taşıma aboneliği, sömestre içinde \"ücretsiz\" geliyor.\n\n---\n\n| Gider kalemi | Berlin / Doğu Almanya | Münih / Frankfurt | Küçük şehir (Tübingen, Konstanz) |\n|---|---|---|---|\n| Telefon + internet | 30–50 € | 30–50 € | 30–50 € |\n| Toplu taşıma (sömestreticket içinde) | 0 € (dahil) | 0 € (dahil) | 0 € (dahil) |\n| **Toplam aylık** | **830–1.225 €** | **1.130–1.615 €** | **780–1.095 €** |\n\nTUM ve Baden-Württemberg istisnası korunur.\n";

    private const OLD_GUIDE_DE = "# Titel\r\n\r\n**Praktischer Tipp:** Wenn du 330 € zahlst, gehen davon **150 € an das Semesterticket** – das heißt, ein 6-monatiges BVG-Abonnement in Berlin ist inklusive. Ein ÖPNV-Abonnement, das allein monatlich 60 € kostet, ist im Semesterbeitrag quasi „kostenlos“ enthalten.\n\n| Öffentliche Verkehrsmittel (im Semesterticket enthalten) | 0 € (inklusive) | 0 € (inklusive) | 0 € (inklusive) |\n| **Gesamt monatlich** | **830–1.225 €** | **1.130–1.615 €** | **780–1.095 €** |\n";

    private function run200(): void
    {
        (require base_path(self::MIGRATION))->up();
    }

    private function seedFixture(bool $withEnDeFaq = true): void
    {
        $topic = DB::table('faq_topics')->insertGetId(['slug' => 'yurt-test', 'name' => 'Yurt', 'created_at' => now(), 'updated_at' => now()]);
        foreach (['tr' => 'berlin-munih-hamburg-ortalama-kira-ne-kadar', 'en' => 'berlin-munih-hamburg-ortalama-kira-ne-kadar-en', 'de' => 'berlin-munih-hamburg-ortalama-kira-ne-kadar-de'] as $loc => $slug) {
            if ($loc !== 'tr' && ! $withEnDeFaq) continue;
            DB::table('faqs')->updateOrInsert(['locale' => $loc, 'slug' => $slug], ['translation_group_id' => self::FAQ_GROUP, 'faq_topic_id' => $topic,
                'question' => 'Ortalama kira? ' . $loc, 'answer_md' => self::OLD_FAQ, 'answer_html' => $loc === 'tr' ? '<p>eski</p>' : null, 'has_answer' => 1, 'is_published' => 1,
                'created_at' => now()->subMonths(5), 'updated_at' => now()->subMonths(5)]);
        }
        foreach (['tr' => ['is-university-free-in-germany-2026-real-costs', self::OLD_GUIDE_TR], 'de' => ['is-university-free-in-germany-2026-real-costs-de', self::OLD_GUIDE_DE],
            'en' => ['is-university-free-in-germany-2026-real-costs-en', "Short answer: mostly free. Semesterbeitrag of 100–400 € is paid every semester."]] as $loc => [$slug, $md]) {
            DB::table('posts')->updateOrInsert(['locale' => $loc, 'slug' => $slug], ['type' => 'blog', 'title' => 'Ücret rehberi ' . $loc, 'translation_group_id' => self::GUIDE_GROUP,
                'content_md' => $md, 'content_html' => '<p>eski</p>', 'user_id' => null, 'is_published' => 1, 'published_at' => '2026-04-30 13:03:00', 'created_at' => '2026-04-30 13:03:00', 'updated_at' => now()->subDay()]);
        }
    }

    private function snapshot(): array
    {
        return [DB::table('faqs')->orderBy('id')->get()->toArray(), DB::table('posts')->orderBy('id')->get()->toArray()];
    }

    public function test_applies_sourced_texts_and_second_run_is_a_noop(): void
    {
        $this->seedFixture();
        $this->run200();

        foreach (['tr', 'en', 'de'] as $loc) {
            $faq = Faq::where('locale', $loc)->where('translation_group_id', self::FAQ_GROUP)->firstOrFail();
            $this->assertStringNotContainsString('240-340', $faq->answer_md, "$loc eski bant kalmadı");
            $this->assertStringContainsString('265–680', $faq->answer_md, "$loc Berlin yurt bandı tarihli");
            $this->assertMatchesRegularExpression('/(Kasım 2022|November 2022)/u', $faq->answer_md);
            $this->assertStringContainsString('MLP', $faq->answer_md);
            $this->assertStringContainsString('624', $faq->answer_md);
            $this->assertStringContainsString("/$loc/student-rent-map", $faq->answer_md, 'aynı dilde kira haritası bağlantısı');
            $this->assertStringContainsString("/$loc/cities/berlin-q64", $faq->answer_md);
            $this->assertStringContainsString('<table', (string) $faq->answer_html, "$loc answer_html yeniden üretildi");
            $this->assertTrue((bool) $faq->has_answer);
            $this->assertStringNotContainsString('ortalama aylık kira (warm', (string) $faq->answer_html);
        }

        $tr = Post::where('locale', 'tr')->where('slug', 'is-university-free-in-germany-2026-real-costs')->firstOrFail();
        $this->assertStringNotContainsString('150 € Semesterticket', $tr->content_md);
        $this->assertStringNotContainsString('aylık 60 €', $tr->content_md);
        $this->assertStringContainsString('226,80 €', $tr->content_md);
        $this->assertStringContainsString('43 €', $tr->content_md);
        $this->assertStringContainsString('| Universität Bonn | ~330 € |', $tr->content_md, 'kapsam dışı tablo satırı dokunulmadı');
        $this->assertStringContainsString('TUM ve Baden-Württemberg istisnası korunur.', $tr->content_md);
        $this->assertStringContainsString('| Telefon + internet | 30–50 € | 30–50 € | 30–50 € |', $tr->content_md);
        $this->assertStringContainsString('(Münih: +43 € bilet)', $tr->content_md, 'ulaşım ayrı bilet gerektiğinde koşuluyla eklenir');
        $this->assertSame(1, substr_count($tr->content_md, '226,80 €'));
        $this->assertStringContainsString('226,80', (string) $tr->content_html);
        $this->assertSame('2026-04-30 13:03:00', $tr->published_at->format('Y-m-d H:i:s'), 'ilk yayın tarihi korunur');

        $de = Post::where('locale', 'de')->where('slug', 'is-university-free-in-germany-2026-real-costs-de')->firstOrFail();
        $this->assertStringNotContainsString('150 € an das Semesterticket', $de->content_md);
        $this->assertStringContainsString('226,80 €', $de->content_md);
        $this->assertStringContainsString("\r\n", $de->content_md, 'CRLF korunur');
        $this->assertStringNotContainsString('Praktischer Tipp', $de->content_md);

        $en = Post::where('locale', 'en')->where('slug', 'is-university-free-in-germany-2026-real-costs-en')->firstOrFail();
        $this->assertSame('<p>eski</p>', $en->content_html, 'EN kısa sürüm dokunulmadı');

        $snap = $this->snapshot();
        $this->run200();
        $this->assertEquals($snap, $this->snapshot(), 'rerun no-op');
    }

    public function test_hand_edited_faq_blocks_everything(): void
    {
        $this->seedFixture();
        DB::table('faqs')->where('locale', 'de')->where('translation_group_id', self::FAQ_GROUP)->update(['answer_md' => 'Handgeschriebene Antwort ohne alte Zahlen.']);
        $before = $this->snapshot();
        try {
            $this->run200();
            $this->fail('RuntimeException bekleniyordu');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('faq de/', $e->getMessage());
        }
        $this->assertEquals($before, $this->snapshot(), 'FAQ ve rehber hiçbiri yazılmadı');
    }

    public function test_modified_guide_paragraph_blocks_everything(): void
    {
        $this->seedFixture();
        DB::table('posts')->where('slug', 'is-university-free-in-germany-2026-real-costs-de')->update(['content_md' => "# Titel\n\nElle yazılmış ulaşım paragrafı.\n"]);
        $before = $this->snapshot();
        try {
            $this->run200();
            $this->fail('RuntimeException bekleniyordu');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('rehber de/', $e->getMessage());
        }
        $this->assertEquals($before, $this->snapshot());
    }

    public function test_missing_siblings_are_not_created(): void
    {
        $this->seedFixture(withEnDeFaq: false);
        $this->run200();
        $this->assertSame(1, Faq::where('translation_group_id', self::FAQ_GROUP)->count(), 'EN/DE FAQ kardeşi oluşturulmadı');
        $this->assertStringContainsString('265–680', Faq::where('locale', 'tr')->where('translation_group_id', self::FAQ_GROUP)->value('answer_md'));
    }

    public function test_no_targets_is_a_noop_in_testing(): void
    {
        $before = $this->snapshot();
        $this->run200();
        $this->assertEquals($before, $this->snapshot());
    }
}
