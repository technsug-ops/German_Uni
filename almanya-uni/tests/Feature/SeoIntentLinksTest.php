<?php

namespace Tests\Feature;

use App\Support\SeoIntents;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * SEO Recovery P1-1 / Paket A — beş ana niyetin iç bağlantı mimarisi (config/seo_intents.php).
 * Ana sayfa → ana URL tek tık; alt/rakip sayfa → "Ana rehber" kutusu; FAQ → deterministik eşleştirme.
 */
class SeoIntentLinksTest extends TestCase
{
    use RefreshDatabase;

    private const LOCALES = ['tr', 'en', 'de'];

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        SeoIntents::flush();
    }

    /* ---------------------------------------------------------------- yardımcılar */

    /** config'teki yazı slug'larını gerçek translation_group kümeleri olarak oluşturur (dil = slug'ın tanımlı dili). */
    private function seedConfigPosts(array $skip = []): void
    {
        $groups = [];
        foreach (SeoIntents::intents() as $intent => $cfg) {
            foreach ($cfg['primary'] as $loc => $ref) {
                if (isset($ref['post'])) {
                    $groups["{$intent}:primary"][$loc] = [$ref['post'], 'blog'];
                }
            }
        }
        $members = [
            'news-master' => ['your-guide-to-masters-degrees-in-germany-top-universities-and-how', 'news'],
            'news-fees'   => ['is-tuition-free-university-in-germany-a-reality-your-application-guide', 'news'],
            'bachelor'    => ['germany-bachelor-application-2026-complete-flow-guide-for-turkish-high-school', 'blog'],
            'cond-visa'   => ['conditional-admission-visa-germany-which-merkblatt-and-language-level', 'blog'],
        ];
        foreach ($members as $k => [$base, $type]) {
            foreach (self::LOCALES as $loc) {
                $groups[$k][$loc] = [$loc === 'tr' ? $base : "{$base}-{$loc}", $type];
            }
        }

        foreach ($groups as $set) {
            $group = (string) Str::uuid();
            foreach ($set as $loc => [$slug, $type]) {
                if (in_array($slug, $skip, true)) {
                    DB::table('posts')->where('slug', $slug)->delete();

                    continue;
                }
                // Bazı yazılar veri migration'larıyla test DB'sinde zaten var → slug + dil üzerinden güncelle.
                DB::table('posts')->updateOrInsert(['slug' => $slug, 'locale' => $loc], [
                    'title' => "Title {$slug}", 'type' => $type,
                    'content_md' => "## Section\n\nText {$slug}.", 'content_html' => "<h2>Section</h2><p>Text {$slug}.</p>",
                    'is_published' => true, 'published_at' => now()->subDay(), 'translation_group_id' => $group,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }
    }

    private function topic(string $slug): int
    {
        return DB::table('faq_topics')->where('slug', $slug)->value('id')
            ?? DB::table('faq_topics')->insertGetId(['name' => ucfirst($slug), 'slug' => $slug, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
    }

    private function faq(string $topic, string $locale, string $question, ?string $category = null): string
    {
        $slug = Str::slug(Str::limit($question, 60, '')) . '-' . $locale;
        DB::table('faqs')->insert([
            'faq_topic_id' => $this->topic($topic), 'locale' => $locale, 'question' => $question, 'slug' => $slug,
            'answer_md' => 'Answer.', 'answer_html' => '<p>Answer.</p>', 'has_answer' => true, 'is_published' => true,
            'category' => $category, 'intent' => 'bilgi', 'created_at' => now(), 'updated_at' => now(),
        ]);

        return "/{$locale}/faq/{$topic}/{$slug}";
    }

    /** Ana URL'nin yolu (/{locale}/…). */
    private function primaryPath(string $intent, string $locale): ?string
    {
        app()->setLocale($locale);
        \Illuminate\Support\Facades\URL::defaults(['locale' => $locale]);
        $url = SeoIntents::primaryUrl($intent, $locale);

        return $url ? parse_url($url, PHP_URL_PATH) : null;
    }

    /* ---------------------------------------------------------------- config */

    public function test_config_defines_five_intents_with_three_languages(): void
    {
        $this->assertSame(
            ['university_application', 'masters_requirements', 'university_costs', 'conditional_admission', 'application_deadlines'],
            array_keys(SeoIntents::intents())
        );
        foreach (SeoIntents::intents() as $key => $cfg) {
            foreach (self::LOCALES as $loc) {
                $this->assertArrayHasKey($loc, $cfg['primary'], "{$key} {$loc} ana URL tanımı yok");
            }
            // Ana URL üye listesinde olamaz (kendine bağlanan kutu olmaz).
            foreach ($cfg['primary'] as $ref) {
                if (isset($ref['post'])) {
                    $this->assertNotContains($ref['post'], $cfg['members']['posts'] ?? [], "{$key}: ana yazı üye listesinde");
                }
            }
        }
    }

    public function test_unpublished_or_missing_translation_gets_no_url(): void
    {
        $this->seedConfigPosts(skip: ['doing-a-masters-in-germany-2026-a-z-guide-de']);
        DB::table('posts')->where('slug', 'is-university-free-in-germany-2026-real-costs-en')->update(['is_published' => false]);

        $this->assertNull($this->primaryPath('masters_requirements', 'de'));
        $this->assertNull($this->primaryPath('university_costs', 'en'));
        $this->assertSame('/tr/blog/doing-a-masters-in-germany-2026-a-z-guide', $this->primaryPath('masters_requirements', 'tr'));

        Cache::flush();
        SeoIntents::flush();
        $html = $this->get('/de')->assertOk()->getContent();
        $this->assertStringNotContainsString('data-seo-intent="masters_requirements"', $html);
        $this->assertStringContainsString('data-seo-intent="university_costs"', $html);
    }

    /* ---------------------------------------------------------------- ana sayfa */

    #[DataProvider('locales')]
    public function test_home_links_all_five_primary_urls_and_each_is_200_self_canonical(string $locale): void
    {
        $this->seedConfigPosts();
        $html = $this->get("/{$locale}")->assertOk()->getContent();

        foreach (array_keys(SeoIntents::intents()) as $intent) {
            $path = $this->primaryPath($intent, $locale);
            $this->assertNotNull($path, "{$intent} {$locale}");
            $this->assertStringStartsWith("/{$locale}/", $path);
            $this->assertMatchesRegularExpression(
                '#<a href="[^"]*' . preg_quote($path, '#') . '"\s+data-seo-intent="' . $intent . '"#',
                $html,
                "Ana sayfa {$locale}: {$intent} bağlantısı yok"
            );

            $page = $this->get($path);
            $page->assertOk();
            $this->assertMatchesRegularExpression('#<link rel="canonical" href="[^"]*' . preg_quote($path, '#') . '"#', $page->getContent(), "{$path} self-canonical değil");
            // Ana rehberin kendisinde "Ana rehber" kutusu yok.
            $this->assertStringNotContainsString('aria-label="' . e(__('Main guide', [], $locale)) . '"', $page->getContent(), "{$path} kendine bağlanıyor");
        }
    }

    public static function locales(): array
    {
        return [['tr'], ['en'], ['de']];
    }

    /* ---------------------------------------------------------------- üye sayfalar */

    #[DataProvider('locales')]
    public function test_member_pages_link_to_same_language_primary(string $locale): void
    {
        $this->seedConfigPosts();
        $sfx = $locale === 'tr' ? '' : "-{$locale}";
        $cases = [
            "/{$locale}/news/your-guide-to-masters-degrees-in-germany-top-universities-and-how{$sfx}" => 'masters_requirements',
            "/{$locale}/news/is-tuition-free-university-in-germany-a-reality-your-application-guide{$sfx}" => 'university_costs',
            "/{$locale}/blog/germany-bachelor-application-2026-complete-flow-guide-for-turkish-high-school{$sfx}" => 'university_application',
            "/{$locale}/blog/conditional-admission-visa-germany-which-merkblatt-and-language-level{$sfx}" => 'conditional_admission',
            "/{$locale}/universities/collections/conditional-admission-universities" => 'conditional_admission',
            "/{$locale}/tuition-free" => 'university_costs',
        ];
        foreach ($cases as $url => $intent) {
            $html = $this->get($url)->assertOk()->getContent();
            $target = $this->primaryPath($intent, $locale);
            $this->assertSame(1, substr_count($html, 'data-seo-intent="'), "{$url}: tam bir kutu bekleniyor");
            $this->assertMatchesRegularExpression(
                '#data-seo-intent="' . $intent . '"[\s\S]*?<a href="[^"]*' . preg_quote($target, '#') . '"#',
                $html,
                "{$url} → {$target} bağlantısı yok"
            );
        }
    }

    public function test_unrelated_post_has_no_guide_box(): void
    {
        $this->seedConfigPosts();
        DB::table('posts')->insert([
            'title' => 'Anmeldung', 'slug' => 'anmeldung-guide', 'locale' => 'tr', 'type' => 'blog',
            'content_md' => 'x', 'content_html' => '<p>x</p>', 'is_published' => true, 'published_at' => now()->subDay(),
            'translation_group_id' => (string) Str::uuid(), 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->assertStringNotContainsString('data-seo-intent="', $this->get('/tr/blog/anmeldung-guide')->assertOk()->getContent());
    }

    /* ---------------------------------------------------------------- FAQ */

    public static function faqCases(): array
    {
        return [
            // pozitif
            ['master', 'Almanya\'daki yüksek lisans programları için başvuru şartları (GPA, ECTS) programdan programa değişir mi?', null, 'masters_requirements'],
            ['master', 'Can you pursue a master\'s degree in Germany without a related bachelor\'s degree?', null, 'masters_requirements'],
            ['uni-assist', 'Uni-Assist ile üniversite paralel başvurusu yapılabilir mi?', null, 'university_application'],
            ['uni-assist', 'Almanya\'da üniversiteye kabul şartları nelerdir?', null, 'university_application'],
            ['city-life', 'Which German states charge tuition fees for non-EU students?', null, 'university_costs'],
            ['master', 'Ist ein kostenloses Masterstudium in Deutschland möglich?', null, 'university_costs'],
            ['language', 'Şartlı kabul ile C1 dil seviyesine ulaşmadan üniversiteye kayıt yaptırılabilir mi?', null, 'conditional_admission'],
            ['uni-assist', 'Wann beginnen die Bewerbungsfristen für Universitäten in Deutschland?', null, 'application_deadlines'],
            ['uni-assist', 'Almanya\'da lisans eğitimi için kış dönemi başvuruları ne zaman yapılır?', null, 'application_deadlines'],
            ['master', 'Is it possible to do a master\'s degree for free in Germany?', null, 'university_costs'],
            ['money', 'Wie hoch ist der Semesterbeitrag an deutschen Hochschulen?', null, 'university_costs'],
            ['uni-assist', 'How much is the uni-assist fee for a VPD and for additional applications?', null, 'university_application'],
            // negatif: yalnız yaşam gideri / kira / market / ulaşım → ücret rehberi değil
            ['money', 'Almanya öğrenci yıllık yaşam maliyeti ortalama kaç Euro?', null, null],
            ['money', 'What is the monthly cost of living for a student in Germany?', null, null],
            ['money', 'Wie hoch sind die monatlichen Lebenshaltungskosten für Studierende in Deutschland?', null, null],
            ['city-life', 'Öğrenciler için toplu taşıma ücreti ne kadar?', null, null],
            ['city-life', 'Wie viel kosten Lebensmittel im Supermarkt für Studierende?', null, null],
            ['money', 'Öğrenci yurdu kira ücreti ne kadar?', null, null],
            // negatif: belirsizse bağlantı yok
            ['money', 'Fintiba\'ya para transferinde SWIFT şube bilgisi sorun yaratır mı?', null, null],
            ['money', 'Almanya\'daki üniversite ücretleri Türkiye\'den nasıl ödenir ve komisyon kesintileri nelerdir?', null, null],
            ['appointments', 'Ankara konsolosluk öğrenci vizesi başvurusu randevu süreci?', null, null],
            ['visa', 'Üniversite kabul belgesi vize başvurusu için yeterli mi?', 'visa', null],
            ['master', 'Master kabul belgesi vize başvurusu için yeterli mi?', null, null],
            ['language', 'Goethe vs Telc sertifikası — uni başvurusu için fark var mı?', null, null],
            ['language', 'B1 ile dil kursu vizesi başvurusu mümkün mü?', null, null],
            ['uni-assist', 'VPD belgesiyle başvuru sonuçları ne zaman açıklanır?', null, 'university_application'],
            ['studienkolleg', 'Studienkolleg yurdu var mı, başvuru süreci nasıl?', null, null],
            ['scholarships', 'DAAD burs başvuru tarihleri ne zaman?', null, null],
            ['housing', 'Yurt başvurusu için deadline ne zaman?', null, null],
            ['master', 'Master sonrası iş arama vizesi (Jobsuche) kaç ay geçerli?', null, null],
            ['master', 'Online master diploması Almanya\'da geçerli mi?', null, null],
        ];
    }

    #[DataProvider('faqCases')]
    public function test_faq_matching_is_deterministic(string $topic, string $question, ?string $category, ?string $expected): void
    {
        $this->assertSame($expected, SeoIntents::matchFaq($question, $topic, $category, null), $question);
    }

    public function test_faq_page_shows_matching_guide_only_when_relevant(): void
    {
        $this->seedConfigPosts();
        $master = $this->faq('master', 'en', 'Do application requirements (GPA, ECTS) for master\'s programs in Germany vary by program?');
        $swift = $this->faq('money', 'en', 'Does the SWIFT branch code matter for a transfer to Fintiba?');

        $html = $this->get($master)->assertOk()->getContent();
        $this->assertMatchesRegularExpression('#data-seo-intent="masters_requirements"[\s\S]*?<a href="[^"]*/en/blog/doing-a-masters-in-germany-2026-a-z-guide-en"#', $html);
        $this->assertStringNotContainsString('data-seo-intent="', $this->get($swift)->assertOk()->getContent());
    }
}
