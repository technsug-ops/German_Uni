<?php

namespace Tests\Feature;

use App\Models\University;
use App\Services\Enrichment\UniversityEnrichmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Şartlı kabul paketi (2026-10-10): koleksiyon resmî kaynaklarla çelişen "Confirmed / Limited" rozetleri yerine
 * kapsamı belli başvuru yollarını gösterir; 9 profil doğrulanmış bloklara çevrilir ve editoryal kilitle korunur;
 * iki rehber koleksiyona bağlanır. Olgusal doğruluk resmî sayfalarla elle kontrol edildi — bu testler yapıyı,
 * güvenlik kurallarını (guard, idempotency, kilit) ve görünür/şema tutarlılığını sabitler.
 */
class ConditionalAdmissionBatchTest extends TestCase
{
    use RefreshDatabase;

    private const PROFILES = 'database/migrations/2026_10_10_000100_apply_conditional_admission_profiles.php';

    private const GUIDES = 'database/migrations/2026_10_10_000200_link_conditional_admission_guides_to_collection.php';

    private const COLLECTION = 'conditional-admission-universities';

    private function data(): array
    {
        return json_decode(file_get_contents(base_path('database/data/university-content/conditional-admission-profiles-2026-10-10.json')), true);
    }

    /** Beklenen blok tipi dizisine uyan, eski (yanlış) iddiaları taşıyan sahte bloklar. */
    private function legacyBlocks(array $types): array
    {
        return array_map(fn ($type) => match ($type) {
            'intro' => ['type' => 'intro', 'body_md' => 'Eski giriş: şartlı kabul neredeyse herkese verilir.'],
            'quick_facts' => ['type' => 'quick_facts', 'h' => 'Hızlı Bakış', 'items' => [
                ['label' => 'Şehir', 'value' => 'Bremen'], ['label' => 'Program Sayısı', 'value' => '256'], ['label' => 'Uni-Assist Üyesi', 'value' => 'Hayır'],
            ]],
            'section' => ['type' => 'section', 'h' => 'Öğrenci yaşamı', 'body_md' => "Yurtlar erken dolar. Özellikle şartlı kabul alan öğrenciler de yurda başvurabilir. Erken başvur.\n\nİkinci paragraf."],
            'faq' => ['type' => 'faq', 'h' => 'SSS', 'items' => [['q' => 'Eski soru?', 'a' => 'Eski cevap.']]],
            'cta' => ['type' => 'cta', 'body_md' => 'AlmanyaUni ekibine yaz!'],
            'external_links' => ['type' => 'external_links', 'items' => [['url' => 'https://example.org', 'label' => 'Resmi']]],
            'schema_jsonld' => ['type' => 'schema_jsonld', 'data' => ['@type' => 'CollegeOrUniversity', 'description' => 'eski']],
            'hero' => ['type' => 'hero', 'image_url' => 'https://example.org/hero.jpg', 'alt' => 'Kampüs'],
            default => ['type' => $type],
        }, $types);
    }

    private function seedUni(string $slug, array $blocks): void
    {
        $json = json_encode($blocks, JSON_UNESCAPED_UNICODE);
        DB::table('universities')->insert([
            'name_de' => $slug, 'name_tr' => $slug, 'slug' => $slug, 'is_active' => 1, 'type' => 'public',
            'content_blocks' => $json, 'content_blocks_en' => $json, 'content_blocks_de' => $json,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function runProfiles(): void
    {
        (require base_path(self::PROFILES))->up();
    }

    public function test_profile_migration_rewrites_blocks_and_is_idempotent(): void
    {
        $slug = 'universitat-bremen-q500692';
        $spec = $this->data()['universities'][$slug];
        $this->seedUni($slug, $this->legacyBlocks($spec['expected_types']));

        $this->runProfiles();
        $u = University::where('slug', $slug)->first();

        $this->assertTrue($u->hasEditorialLock());
        foreach (['content_blocks' => 'tr', 'content_blocks_en' => 'en', 'content_blocks_de' => 'de'] as $col => $loc) {
            $blocks = collect($u->{$col});
            $intro = $blocks->firstWhere('type', 'intro');
            $this->assertSame($spec['locales'][$loc]['seo_title'], $intro['seo_title']);
            $this->assertLessThanOrEqual(160, mb_strlen($intro['seo_description']));
            $this->assertFalse($blocks->contains(fn ($b) => $b['type'] === 'programs_summary'), 'katalog sayı bloğu kaldırılmalı');

            $labels = collect($blocks->firstWhere('type', 'quick_facts')['items'])->pluck('label')->implode('|');
            $this->assertDoesNotMatchRegularExpression('/Program Sayısı|Uni-Assist/u', $labels);

            $this->assertCount(4, $blocks->firstWhere('type', 'faq')['items']);
            $this->assertStringNotContainsString('AlmanyaUni', $blocks->firstWhere('type', 'cta')['body_md']);
            $this->assertSame(collect($spec['locales'][$loc]['sections'])->pluck(0)->all(),
                $blocks->where('type', 'section')->whereNotNull('verified_at')->pluck('h')->values()->all());
        }

        // Kalan eski bölümden yalnız şartlı kabul varsayan cümle silinir, paragraf yapısı korunur.
        $legacy = collect($u->content_blocks)->firstWhere('h', 'Öğrenci yaşamı');
        $this->assertSame("Yurtlar erken dolar. Erken başvur.\n\nİkinci paragraf.", $legacy['body_md']);

        $before = DB::table('universities')->where('slug', $slug)->value('content_blocks');
        $this->runProfiles();
        $this->assertSame($before, DB::table('universities')->where('slug', $slug)->value('content_blocks'), 'ikinci çalıştırma no-op olmalı');
    }

    public function test_profile_migration_skips_unexpected_structure(): void
    {
        $slug = 'universitat-hamburg-q156725';
        $blocks = [['type' => 'intro', 'body_md' => 'Farklı yapı.'], ['type' => 'faq', 'items' => []]];
        $this->seedUni($slug, $blocks);

        $this->runProfiles();

        $this->assertSame($blocks, University::where('slug', $slug)->first()->content_blocks);
    }

    public function test_locked_profile_is_not_overwritten_by_enrichment(): void
    {
        $slug = 'rwth-aachen-university-partner-019de9ee';
        $this->seedUni($slug, $this->legacyBlocks($this->data()['universities'][$slug]['expected_types']));
        $this->runProfiles();

        $result = app(UniversityEnrichmentService::class)->enrich(University::where('slug', $slug)->first(), force: true);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Editoryal', $result['error']);
    }

    public static function locales(): array
    {
        return [['tr'], ['en'], ['de']];
    }

    #[DataProvider('locales')]
    public function test_locked_profile_page_uses_verified_title_and_hides_generic_apply_box(string $locale): void
    {
        $slug = 'technische-universitat-dortmund-q685557';
        $spec = $this->data()['universities'][$slug];
        $this->seedUni($slug, $this->legacyBlocks($spec['expected_types']));
        $this->runProfiles();

        $html = $this->get("/{$locale}/universities/{$slug}")->assertOk()->getContent();

        $this->assertStringContainsString('<title>' . e($spec['locales'][$locale]['seo_title']) . ' — ', $html);
        $this->assertStringContainsString('content="' . e($spec['locales'][$locale]['seo_description']) . '"', $html);
        // Genel kutu "uni-assist + 15 Temmuz/15 Ocak" varsayımı taşır; kilitli profilde gösterilmez.
        $this->assertStringNotContainsString(__('Submit by 15 July (winter) / 15 January (summer) deadlines.'), $html);
    }

    #[DataProvider('locales')]
    public function test_collection_shows_verified_routes_and_lists_only_real_routes_in_schema(string $locale): void
    {
        foreach ($this->data()['universities'] as $slug => $_) {
            DB::table('universities')->insert(['name_de' => $slug, 'name_tr' => $slug, 'slug' => $slug, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()]);
        }

        $html = $this->get("/{$locale}/universities/collections/" . self::COLLECTION)->assertOk()->getContent();

        $this->assertStringNotContainsString('Definitely offers', $html);
        $this->assertStringNotContainsString('almost every qualified applicant', $html);

        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m);
        $itemList = collect($m[1])->map(fn ($j) => json_decode($j, true))->first(fn ($d) => ($d['@type'] ?? null) === 'ItemList');
        $listed = collect($itemList['itemListElement'])->pluck('name')->sort()->values()->all();
        $this->assertSame([
            'philipps-universitat-marburg-q155354', 'technische-universitat-clausthal-q447354',
            'technische-universitat-dortmund-q685557', 'universitat-duisburg-essen-q696757',
        ], $listed, 'ItemList yalnız başlığın vaat ettiği yolu sunan kurumları içermeli');

        // Karıştırılan yollar görünür kalır (sessizce silinmez), her kartta resmî kaynak bağlantısı var.
        $this->assertStringContainsString('universitat-hamburg-q156725', $html);
        $this->assertStringContainsString('https://www.uni-hamburg.de/campuscenter/bewerbung/international/studium-mit-abschluss/sprachkenntnisse/deutschkenntnisse.html', $html);
    }

    public function test_collection_is_in_every_language_sitemap(): void
    {
        foreach (['tr', 'en', 'de'] as $l) {
            $this->get("/sitemap-{$l}.xml")->assertOk()->assertSee("/{$l}/universities/collections/" . self::COLLECTION, false);
        }
    }

    public function test_guide_link_is_inserted_once_before_bachelor_heading(): void
    {
        $md = "Giriş.\n\n## Nedir?\n\nMetin.\n\n## Kimler alır?\n\nMetin.\n\n## Bachelor için şartlı kabul\n\nMetin.";
        $html = "<p>Giriş.</p>\n<h2>Nedir?</h2><p>Metin.</p>\n<h2>Kimler alır?</h2><p>Metin.</p>\n<h2>Bachelor için şartlı kabul</h2><p>Metin.</p>";
        // Migration'larla oluşan gerçek yazı varsa içeriğini bilinen bir fixture ile değiştir.
        DB::table('posts')->updateOrInsert(['locale' => 'tr', 'slug' => 'germany-conditional-admission-bedingte-zulassung-guide'],
            ['title' => 'Rehber', 'content_md' => $md, 'content_html' => $html, 'is_published' => 1, 'published_at' => now()->subDay(), 'created_at' => now(), 'updated_at' => now()]);

        (require base_path(self::GUIDES))->up();
        (require base_path(self::GUIDES))->up();

        $post = DB::table('posts')->where('slug', 'germany-conditional-admission-bedingte-zulassung-guide')->first();
        $href = '/tr/universities/collections/' . self::COLLECTION;
        $this->assertSame(1, substr_count($post->content_md, $href));
        $this->assertSame(1, substr_count($post->content_html, $href));
        $this->assertLessThan(strpos($post->content_html, '<h2>Bachelor'), strpos($post->content_html, $href));
        $this->assertGreaterThan(strpos($post->content_html, '<h2>Kimler'), strpos($post->content_html, $href));
    }

    public function test_guide_without_anchor_is_left_untouched(): void
    {
        DB::table('posts')->updateOrInsert(['locale' => 'en', 'slug' => 'conditional-admission-germany-bachelor-master-2026-guide'],
            ['title' => 'Guide', 'content_md' => 'Intro only.', 'content_html' => '<p>Intro only.</p>', 'is_published' => 1, 'created_at' => now(), 'updated_at' => now()]);

        (require base_path(self::GUIDES))->up();

        $this->assertSame('Intro only.', DB::table('posts')->where('slug', 'conditional-admission-germany-bachelor-master-2026-guide')->value('content_md'));
    }
}
