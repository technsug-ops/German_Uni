<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Post;
use App\Services\Enrichment\CityEnrichmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tests\TestCase;

/**
 * SEO Growth Batch 4 (2026-10-11): Berlin, München, Aachen, Hamburg şehir sayfaları + yaşam maliyeti rehberi (TR yeniden
 * yazım, EN/DE kardeşler) + iki destekleyici rehberde bağlantı satırı. Migration güvenliği (eksik hedef, kısmi kilit,
 * beklenmeyen blok yapısı, rollback, no-op), editoryal kilit koruması, sayfa çıktıları ve üç dilde sayı tutarlılığı.
 */
class SeoGrowthBatch4Test extends TestCase
{
    use RefreshDatabase;

    private const MIGRATION = 'database/migrations/2026_10_11_000400_apply_batch4_student_cities_and_living_cost_guide.php';

    /** Ortama özgü (prod'da farklı) çeviri grubunu temsil eder. */
    private const FIXTURE_GROUP = '0b4b4b4b-0000-4000-8000-000000000004';

    private const CITIES = ['berlin-q64' => 'Berlin', 'munchen-q1726' => 'München', 'aachen-q1017' => 'Aachen', 'hamburg-q1055' => 'Hamburg'];

    private function data(): array
    {
        return json_decode(file_get_contents(base_path('database/data/city-content/batch4-student-cities-2026-10-11.json')), true);
    }

    private function run400(): void
    {
        (require base_path(self::MIGRATION))->up();
    }

    /** Canlıdaki eski durum: dört şehir (AI blokları, kaldırılacak başlıklarla), sağlayıcılar, yurtlar, rehberler. */
    private function seedBatch(array $skipCities = [], bool $guides = true): void
    {
        $data = $this->data();
        $stateId = DB::table('states')->insertGetId(['name_tr' => 'Berlin', 'name_de' => 'Berlin', 'name_en' => 'Berlin', 'slug' => 'berlin', 'created_at' => now(), 'updated_at' => now()]);
        foreach (self::CITIES as $slug => $name) {
            if (in_array($slug, $skipCities, true)) {
                continue;
            }
            $spec = $data['cities'][$slug];
            $cols = [];
            foreach (['tr' => 'content_blocks', 'en' => 'content_blocks_en', 'de' => 'content_blocks_de'] as $loc => $col) {
                $cols[$col] = json_encode($this->oldBlocks($name, $spec['drop_headings'][$loc]), JSON_UNESCAPED_UNICODE);
            }
            DB::table('cities')->insert(array_merge([
                'name_tr' => $name === 'München' ? 'Münih' : $name, 'name_de' => $name, 'name_en' => $name === 'München' ? 'Munich' : $name,
                'slug' => $slug, 'state_id' => $stateId, 'is_active' => 1, 'population' => 500000, 'latitude' => 50.0, 'longitude' => 10.0,
                'avg_rent_min' => 250, 'avg_rent_max' => 350, 'stw_name' => 'Studierendenwerk ' . $name, 'stw_capacity' => 1000, 'stw_waiting' => '2-3 sem',
                'created_at' => now()->subDay(), 'updated_at' => now()->subDay(),
            ], $cols));
            DB::table('universities')->insert(['name_tr' => 'Uni ' . $name, 'name_de' => 'Universität ' . $name, 'slug' => 'uni-' . $slug, 'type' => 'public', 'is_active' => 1,
                'city_id' => DB::table('cities')->where('slug', $slug)->value('id'), 'created_at' => now(), 'updated_at' => now()]);
            DB::table('housing_providers')->insert(['slug' => $spec['provider']['slug'], 'name' => 'Studierendenwerk ' . $name, 'type' => 'studierendenwerk',
                'price_min' => 200, 'price_max' => 300, 'total_capacity' => 1000, 'waiting_period' => '2 sem', 'is_active' => 1, 'created_at' => now()->subDay(), 'updated_at' => now()->subDay()]);
            if ($spec['dorm']) {
                DB::table('student_dorms')->insert(['city_id' => DB::table('cities')->where('slug', $slug)->value('id'), 'city_name' => $name, 'organization' => 'STW ' . $name,
                    'website_url' => 'https://example.invalid', 'rent_min_eur' => 200, 'rent_max_eur' => 300, 'waitlist_avg' => '6 ay', 'notes_tr' => 'eski', 'created_at' => now()->subDay(), 'updated_at' => now()->subDay()]);
            }
        }
        if ($guides) {
            $g = $data['guide'];
            $this->insertPost('tr', $g['posts']['tr']['slug'], $g['posts']['tr']['old_titles'][0], "## Eski bölüm\n\nEski metin.", self::FIXTURE_GROUP);
            foreach ($data['supporting'] as $s) {
                $this->insertPost($s['locale'], $s['slugs'][0], 'Destek ' . $s['locale'], "Giriş.\n\n" . $s['before_heading'] . "\n\nMetin.\n\n## Başka\n\nMetin.", null);
            }
        }
    }

    private function oldBlocks(string $name, array $dropHeadings): array
    {
        $blocks = [
            ['type' => 'hero', 'image_url' => 'https://example.invalid/x.png'],
            ['type' => 'intro', 'body_md' => "Merhaba $name! Eski giriş."],
            ['type' => 'quick_facts', 'h' => 'Hızlı Bilgiler', 'items' => [['label' => 'Nüfus', 'value' => '1'], ['label' => 'Toplam Program', 'value' => '99']]],
        ];
        foreach ($dropHeadings as $h) {
            $blocks[] = ['type' => 'section', 'h' => $h, 'body_md' => 'Kira 300-480 € eski tahmin.'];
        }
        $blocks[] = ['type' => 'section', 'h' => 'Şehre Genel Bakış ve Tarih', 'body_md' => 'Tarih metni KORUNUR.'];
        $blocks[] = ['type' => 'cost_of_living', 'h' => 'Aylık Yaşam Maliyeti (Tahmini)', 'items' => [['label' => 'Kira', 'amount' => '300-480']], 'total' => '740-1210'];
        $blocks[] = ['type' => 'places', 'h' => 'Gezilecek Yerler', 'items' => [['name' => 'Dom', 'type' => 'landmark']]];
        $blocks[] = ['type' => 'faq', 'h' => 'SSS', 'items' => [['q' => 'Eski soru?', 'a' => 'Eski cevap.']]];
        $blocks[] = ['type' => 'cta', 'h' => null, 'body_md' => 'AlmanyaUni forumlarına katıl.'];
        $blocks[] = ['type' => 'universities_in_city', 'total' => 2, 'public' => 2, 'private' => 0, 'top_unis' => ['Institut X']];
        $blocks[] = ['type' => 'external_links', 'h' => 'Bağlantılar', 'items' => [['label' => 'Wikipedia', 'url' => 'https://de.wikipedia.org/wiki/' . $name]]];
        $blocks[] = ['type' => 'schema_jsonld', 'data' => ['@type' => 'City', 'name' => $name, 'description' => 'eski']];

        return $blocks;
    }

    private function insertPost(string $locale, string $slug, string $title, string $md, ?string $group): void
    {
        // Eski blog migration'ları (ör. 2026_06_17_256400) test DB'sinde aynı slug'ı farklı çeviri grubuyla oluşturmuş olabilir → güncelle.
        DB::table('posts')->updateOrInsert(['locale' => $locale, 'slug' => $slug], ['type' => 'blog', 'title' => $title, 'content_md' => $md, 'content_html' => '<p>eski</p>',
            'excerpt' => 'Eski özet.', 'meta_description' => 'Eski açıklama.', 'translation_group_id' => $group, 'user_id' => null, 'category_id' => null,
            'is_published' => 1, 'published_at' => now()->subMonths(4), 'created_at' => now()->subMonths(4), 'updated_at' => now()->subMonths(4)]);
    }

    private function blocks(string $slug, string $loc): array
    {
        $col = ['tr' => 'content_blocks', 'en' => 'content_blocks_en', 'de' => 'content_blocks_de'][$loc];

        return json_decode(DB::table('cities')->where('slug', $slug)->value($col), true);
    }

    private function snapshot(): array
    {
        return [
            DB::table('cities')->orderBy('id')->get()->toArray(),
            DB::table('posts')->orderBy('id')->get(['id', 'title', 'content_md', 'published_at', 'updated_at'])->toArray(),
            DB::table('housing_providers')->orderBy('id')->get()->toArray(),
            DB::table('student_dorms')->orderBy('id')->get()->toArray(),
        ];
    }

    public static function locales(): array
    {
        return ['tr' => ['tr'], 'en' => ['en'], 'de' => ['de']];
    }

    /* ------------------------------------------------------------ migration */

    public function test_applies_cities_guide_and_links_then_second_run_is_a_noop(): void
    {
        $this->seedBatch();
        $this->run400();
        $data = $this->data();

        foreach (self::CITIES as $slug => $name) {
            $spec = $data['cities'][$slug];
            foreach (['tr', 'en', 'de'] as $loc) {
                $blocks = $this->blocks($slug, $loc);
                $types = array_column($blocks, 'type');
                $headings = array_values(array_filter(array_map(fn ($b) => $b['h'] ?? null, $blocks)));
                $intro = collect($blocks)->firstWhere('type', 'intro');
                $this->assertTrue($intro['editorial_lock'], "$slug $loc kilit");
                $this->assertSame($spec['locales'][$loc]['seo_title'], $intro['seo_title']);
                $this->assertSame($spec['locales'][$loc]['intro'], $intro['body_md']);
                foreach ($spec['drop_headings'][$loc] as $h) {
                    $this->assertNotContains($h, $headings, "$slug $loc: eski bölüm kaldırıldı");
                }
                $this->assertContains('Şehre Genel Bakış ve Tarih', $headings, "$slug $loc: doğru mevcut içerik korunur");
                foreach ($spec['locales'][$loc]['sections'] as [$h]) {
                    $this->assertContains($h, $headings, "$slug $loc: $h");
                }
                $this->assertSame(2, count(array_keys($types, 'table')), "$slug $loc: konaklama + Semesterbeitrag tabloları");
                $this->assertSame(1, count(array_keys($types, 'cost_of_living')));
                $cost = collect($blocks)->firstWhere('type', 'cost_of_living');
                $this->assertSame($spec['locales'][$loc]['cost']['total'], $cost['total']);
                $this->assertStringContainsString('MLP', $cost['note']);
                $this->assertSame('hero', $types[0]);
                $this->assertSame('intro', $types[1]);
                $this->assertSame('quick_facts', $types[2]);
                $this->assertSame('section', $types[3], 'kaynaklı bölümler quick_facts\'ten hemen sonra');
                $faq = collect($blocks)->firstWhere('type', 'faq');
                $this->assertCount(5, $faq['items']);
                $cta = collect($blocks)->firstWhere('type', 'cta');
                $this->assertStringNotContainsString('AlmanyaUni', $cta['body_md']);
                $this->assertStringContainsString('/' . $loc . '/english-taught', $cta['body_md']);
                $this->assertStringNotContainsString('tools/cost-of-living', $cta['body_md'], 'paket CTA\'sı aracı değil kaynaklı bütçe tablosunu ve rehberi gösterir');
                $this->assertStringContainsString('](#', $cta['body_md'], 'kaynaklı bütçe bölümüne çapa');
                $this->assertStringContainsString('/blog/', $cta['body_md'], 'rehber bağlantısı');
                $links = collect($blocks)->firstWhere('type', 'external_links');
                $this->assertContains($spec['sources'][0], array_column($links['items'], 'url'));
                $this->assertContains('https://de.wikipedia.org/wiki/' . $name, array_column($links['items'], 'url'), 'mevcut dış bağlantı korunur');
                $this->assertSame($spec['locales'][$loc]['top_unis'], collect($blocks)->firstWhere('type', 'universities_in_city')['top_unis']);
                $this->assertStringNotContainsString('eski', collect($blocks)->firstWhere('type', 'schema_jsonld')['data']['description']);
                $this->assertSame(1, count(array_keys($types, 'places')), 'gezilecek yerler korunur');
            }
            $row = DB::table('cities')->where('slug', $slug)->first();
            $int = static fn ($v) => $v === null ? null : (int) $v;
            $this->assertSame($spec['fields']['avg_rent_min'], $int($row->avg_rent_min), "$slug avg_rent_min");
            $this->assertSame($spec['fields']['avg_rent_max'], $int($row->avg_rent_max), "$slug avg_rent_max");
            $this->assertSame($spec['fields']['stw_capacity'], (int) $row->stw_capacity);
            $prov = DB::table('housing_providers')->where('slug', $spec['provider']['slug'])->first();
            $this->assertSame($spec['provider']['price_min'], $int($prov->price_min), "$slug provider price_min");
            $this->assertSame($spec['provider']['price_max'], $int($prov->price_max), "$slug provider price_max");
            if ($spec['dorm']) {
                $dorm = DB::table('student_dorms')->where('city_id', $row->id)->first();
                $this->assertSame($spec['dorm']['rent_min_eur'], $int($dorm->rent_min_eur), "$slug dorm rent_min");
                $this->assertSame($spec['dorm']['rent_max_eur'], $int($dorm->rent_max_eur), "$slug dorm rent_max");
                $this->assertStringNotContainsString('eski', (string) $dorm->notes_tr);
            }
        }
        // şehir sayısı değişmedi (isim varyasyonu için yeni kayıt yok)
        $this->assertSame(4, DB::table('cities')->whereIn('slug', array_keys(self::CITIES))->count());

        // ana rehber
        $g = $data['guide'];
        $tr = Post::where('slug', $g['posts']['tr']['slug'])->firstOrFail();
        $this->assertSame($g['posts']['tr']['title'], $tr->title);
        $this->assertSame(self::FIXTURE_GROUP, $tr->translation_group_id, 'ortamın mevcut çeviri grubu korunur');
        $this->assertTrue($tr->published_at->lt(now()->subMonths(3)), 'TR ilk yayın tarihi korunur');
        $this->assertStringContainsString('<table', $tr->content_html);
        foreach (['en', 'de'] as $loc) {
            $s = Post::where('locale', $loc)->where('slug', $g['posts'][$loc]['slug'])->firstOrFail();
            $this->assertSame(self::FIXTURE_GROUP, $s->translation_group_id, 'EN/DE kardeşler TR rehberin grubuna bağlanır');
            $this->assertTrue((bool) $s->is_published);
            $this->assertNotNull($s->published_at);
            $this->assertGreaterThan(now()->subMinute(), $s->published_at, 'EN/DE ilk gerçek yayın anı');
            $this->assertSame((int) $tr->user_id, (int) $s->user_id);
            $this->assertSame($g['posts'][$loc]['title'], $s->title);
        }
        $this->assertSame(3, Post::where('translation_group_id', self::FIXTURE_GROUP)->count());

        // destekleyici rehberler: satır tam bir kez, başlık korunur
        foreach ($data['supporting'] as $s) {
            $md = (string) Post::where('locale', $s['locale'])->where('slug', $s['slugs'][0])->value('content_md');
            $this->assertSame(1, substr_count($md, $s['line']), $s['slugs'][0]);
            $this->assertSame(1, substr_count($md, $s['before_heading']));
            $this->assertStringContainsString($s['line'] . "\n\n" . $s['before_heading'], $md);
        }

        $before = $this->snapshot();
        $this->run400();
        $this->assertEquals($before, $this->snapshot(), 'ikinci çalıştırma hiçbir şey değiştirmez (updated_at dahil)');
    }

    public function test_partially_locked_city_blocks_everything(): void
    {
        $this->seedBatch();
        $blocks = $this->blocks('aachen-q1017', 'en');
        $blocks[1]['editorial_lock'] = true;
        DB::table('cities')->where('slug', 'aachen-q1017')->update(['content_blocks_en' => json_encode($blocks)]);
        $before = $this->snapshot();

        try {
            $this->run400();
            $this->fail('RuntimeException bekleniyordu');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('aachen-q1017: kısmen uygulanmış', $e->getMessage());
        }
        $this->assertEquals($before, $this->snapshot(), 'hiçbir şehir, sağlayıcı veya yazı yazılmadı');
    }

    public function test_unexpected_block_structure_or_missing_heading_blocks_everything(): void
    {
        $this->seedBatch();
        $blocks = array_values(array_filter($this->blocks('hamburg-q1055', 'de'), fn ($b) => ($b['h'] ?? null) !== 'Studieren in Hamburg: Chancen und Möglichkeiten'));
        DB::table('cities')->where('slug', 'hamburg-q1055')->update(['content_blocks_de' => json_encode($blocks)]);
        $before = $this->snapshot();

        try {
            $this->run400();
            $this->fail('RuntimeException bekleniyordu');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString("kaldırılacak 'Studieren in Hamburg: Chancen und Möglichkeiten' bölümü bulunamadı", $e->getMessage());
        }
        $this->assertEquals($before, $this->snapshot());
        $this->assertSame(1, Post::where('translation_group_id', self::FIXTURE_GROUP)->count(), 'EN/DE rehber de oluşturulmadı');
    }

    public function test_guide_group_is_read_from_the_environment(): void
    {
        // Prod'daki grup UUID'si local'dekinden farklı (ilk migration Str::uuid() üretmişti) → sabit UUID beklenmez.
        $this->seedBatch();
        $g = $this->data()['guide'];
        DB::table('posts')->where('slug', $g['posts']['tr']['slug'])->update(['translation_group_id' => null]);

        $this->run400();

        $tr = Post::where('locale', 'tr')->where('slug', $g['posts']['tr']['slug'])->firstOrFail();
        $this->assertNotEmpty($tr->translation_group_id, 'grubu olmayan TR rehbere grup atanır');
        foreach (['en', 'de'] as $loc) {
            $this->assertSame($tr->translation_group_id, Post::where('locale', $loc)->where('slug', $g['posts'][$loc]['slug'])->value('translation_group_id'));
        }
        $this->assertSame(3, Post::where('translation_group_id', $tr->translation_group_id)->count());
    }

    public function test_existing_sibling_slug_in_another_group_blocks_everything(): void
    {
        $this->seedBatch();
        $g = $this->data()['guide'];
        $this->insertPost('en', $g['posts']['en']['slug'], 'Başka grup', 'Metin.', '11111111-2222-4333-8444-555555555555');
        $before = $this->snapshot();

        try {
            $this->run400();
            $this->fail('RuntimeException bekleniyordu');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('başka bir çeviri grubuna ait', $e->getMessage());
        }
        $this->assertEquals($before, $this->snapshot());
    }

    public function test_guide_with_unexpected_title_blocks_everything(): void
    {
        $this->seedBatch();
        $g = $this->data()['guide'];
        DB::table('posts')->where('slug', $g['posts']['tr']['slug'])->update(['title' => 'Elle değiştirilmiş başlık']);
        $before = $this->snapshot();

        try {
            $this->run400();
            $this->fail('RuntimeException bekleniyordu');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('beklenmeyen başlık', $e->getMessage());
        }
        $this->assertEquals($before, $this->snapshot());
    }

    public function test_supporting_heading_missing_blocks_everything(): void
    {
        $this->seedBatch();
        $s = $this->data()['supporting'][0];
        DB::table('posts')->where('slug', $s['slugs'][0])->update(['content_md' => "Giriş.\n\n## Başka başlık\n\nMetin."]);
        $before = $this->snapshot();

        try {
            $this->run400();
            $this->fail('RuntimeException bekleniyordu');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString("'{$s['before_heading']}' başlığı tam bir kez bulunamadı", $e->getMessage());
        }
        $this->assertEquals($before, $this->snapshot());
    }

    public function test_no_targets_is_a_noop_only_in_testing(): void
    {
        $this->assertSame(0, DB::table('cities')->whereIn('slug', array_keys(self::CITIES))->count());
        $before = $this->snapshot();
        $this->run400();
        $this->assertEquals($before, $this->snapshot(), 'hedef şehir/rehber grubu yokken (CI) hiçbir şey yazılmaz');
    }

    /* ------------------------------------------------------------ editoryal kilit */

    public function test_locked_city_is_protected_from_enrichment(): void
    {
        $this->seedBatch();
        $this->run400();
        $city = City::where('slug', 'berlin-q64')->firstOrFail();
        $this->assertTrue($city->hasEditorialLock());

        $before = DB::table('cities')->where('slug', 'berlin-q64')->first();
        $result = app(CityEnrichmentService::class)->enrich($city, true);
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('kilitli', $result['error']);
        $this->assertEquals($before, DB::table('cities')->where('slug', 'berlin-q64')->first(), 'bloklar değişmedi');
    }

    /* ------------------------------------------------------------ sayfalar */

    #[DataProvider('locales')]
    public function test_city_pages_render_with_sourced_sections_metadata_and_links(string $locale): void
    {
        $this->seedBatch();
        $this->run400();
        $data = $this->data();

        foreach (self::CITIES as $slug => $name) {
            $t = $data['cities'][$slug]['locales'][$locale];
            $html = $this->get("/{$locale}/cities/{$slug}")->assertOk()->getContent();
            $this->assertStringContainsString('<title>' . e($t['seo_title']) . ' — ' . brand('name') . '</title>', $html, "$slug $locale title");
            $this->assertStringContainsString('<meta name="description" content="' . e($t['seo_description']) . '"', $html);
            $this->assertSame(1, substr_count($html, '<h1'));
            foreach ($t['sections'] as [$h]) {
                $this->assertStringContainsString(e($h) . '</h2>', $html, "$slug $locale: $h");
            }
            $this->assertStringContainsString(e($t['housing_table']['h']) . '</h2>', $html);
            $this->assertStringContainsString(e($t['fees_table']['h']) . '</h2>', $html);
            $this->assertStringContainsString('class="overflow-x-auto rounded-lg ring-1 ring-gray-200"', $html, 'tablolar mobilde kaydırılabilir');
            $this->assertStringContainsString('id="universities"', $html);
            $this->assertStringContainsString("/{$locale}/english-taught", $html);
            $this->assertStringContainsString('/blog/real-cost-of-being-a-student-in-germany-budget-truth', $html, 'yaşam maliyeti rehberine bağlantı');
            preg_match_all('/\]\(#([a-z0-9-]+)\)/u', $t['cta'], $m);
            $this->assertNotEmpty($m[1], 'CTA sayfa içi çapa içerir');
            foreach ($m[1] as $anchor) {
                $this->assertStringContainsString('id="' . $anchor . '"', $html, "$slug $locale: CTA çapası #$anchor sayfada var");
            }
            $this->assertStringNotContainsString('365–370', $html, 'hesaplanmış RWTH toplamı yok');
            $this->assertStringNotContainsString('601', $html, 'depozito kademesinden türetilen üst sınır yok');
            $this->assertStringNotContainsString(__('Average dorm price:'), $html, 'aralık "ortalama" etiketiyle gösterilmez');
            if ($slug === 'berlin-q64') {
                $this->assertMatchesRegularExpression('/(Kasım|November) 2022/u', $html, 'Berlin yurt bandı tarihli');
                $this->assertStringNotContainsString(__('Dorm rent range:'), $html, 'Berlin: tarihli istatistik güncel aralık olarak panelde yok');
                $this->assertStringContainsString('9.200', $html, 'Berlin STW kapasitesi panelde kalır');
            }
            if ($slug === 'hamburg-q1055') {
                $this->assertStringNotContainsString(__('Dorm rent range:'), $html, 'Hamburg: doğrulanmamış üst sınır yok');
                $this->assertStringContainsString('€310+', $html, 'Hamburg STW kartı yalnız yayımlanmış başlangıç fiyatı');
            }
            if ($slug === 'aachen-q1017') {
                $this->assertMatchesRegularExpression('/354[,.]80/u', $html, 'RWTH yalnız doğrulanan bileşenler');
            }
            $this->assertStringContainsString('/universities/', $html, 'üniversite profiline bağlantı');
            $this->assertStringNotContainsString('300-480', $html, 'eski kaynaksız kira aralığı kalmadı');
            $this->assertStringNotContainsString('AlmanyaUni', $html);
            $this->assertStringContainsString('MLP', $html, 'kaynak görünür');
            if ($t['stats']) {
                $this->assertStringContainsString('/blog/germany-student-statistics-2026-international-students-and-turkiye', $html, 'istatistik raporu bağlantısı');
            }
        }
    }

    #[DataProvider('locales')]
    public function test_guide_pages_render_and_link_four_cities_and_tools(string $locale): void
    {
        $this->seedBatch();
        $this->run400();
        $g = $this->data()['guide'];
        $html = $this->get("/{$locale}/blog/" . $g['posts'][$locale]['slug'])->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, '<h1'));
        foreach (array_keys(self::CITIES) as $slug) {
            $this->assertStringContainsString("/{$locale}/cities/{$slug}", $html, $slug);
        }
        $this->assertStringContainsString("/{$locale}/tools/cost-of-living", $html);
        $this->assertStringContainsString('<table', $html);
        foreach (['tr', 'en', 'de'] as $alt) {
            $this->assertStringContainsString('hreflang="' . $alt . '" href="' . url("/{$alt}/blog/" . $g['posts'][$alt]['slug']) . '"', $html, "hreflang $alt");
        }
        $this->assertStringContainsString('992', $html, 'bloke hesap eşiği');
        $this->assertStringContainsString('226,80', str_replace('226.80', '226,80', $html), 'Deutschlandsemesterticket');
    }

    public function test_numbers_match_across_locales_in_city_tables_and_cost_cards(): void
    {
        $data = $this->data();
        $norm = fn (string $s) => preg_replace('/[.,]/', '', $s);
        foreach ($data['cities'] as $slug => $spec) {
            $sets = [];
            foreach (['tr', 'en', 'de'] as $loc) {
                $t = $spec['locales'][$loc];
                $nums = [];
                foreach (array_merge($t['housing_table']['rows'], $t['fees_table']['rows']) as $row) {
                    foreach ($row as $cell) {
                        preg_match_all('/\d[\d.,]*/', $cell, $m);
                        $nums = array_merge($nums, array_map($norm, array_map(fn ($x) => trim($x, '.,'), $m[0])));
                    }
                }
                foreach ($t['cost']['items'] as $item) {
                    preg_match_all('/\d[\d.,]*/', $item['amount'], $m);
                    $nums = array_merge($nums, array_map($norm, $m[0]));
                }
                sort($nums);
                $sets[$loc] = $nums;
                $this->assertCount(5, $t['faq'], "$slug $loc");
                $this->assertCount(8, $t['facts'], "$slug $loc");
            }
            $this->assertSame($sets['tr'], $sets['en'], "$slug: EN sayıları TR ile aynı");
            $this->assertSame($sets['tr'], $sets['de'], "$slug: DE sayıları TR ile aynı");
        }
    }

    public function test_only_the_four_cities_are_touched(): void
    {
        $this->seedBatch();
        $stateId = DB::table('states')->value('id');
        DB::table('cities')->insert(['name_tr' => 'Köln', 'name_de' => 'Köln', 'name_en' => 'Cologne', 'slug' => 'koln-q365', 'state_id' => $stateId, 'is_active' => 1,
            'avg_rent_min' => 300, 'avg_rent_max' => 400, 'content_blocks' => json_encode([['type' => 'intro', 'body_md' => 'Köln.']]), 'created_at' => now()->subDay(), 'updated_at' => now()->subDay()]);
        $before = DB::table('cities')->where('slug', 'koln-q365')->first();

        $this->run400();

        $this->assertEquals($before, DB::table('cities')->where('slug', 'koln-q365')->first());
    }
}
