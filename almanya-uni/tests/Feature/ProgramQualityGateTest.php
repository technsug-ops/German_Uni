<?php

namespace Tests\Feature;

use App\Models\Program;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Program Data Quality Gate (Phase 2): dil/şablon render kuralları, ince içerik eşiği (noindex + sitemap dışı)
 * ve son başvuru tarihi tazeliği ("son bilinen"). Başlık/H1/slug/canonical/hreflang değişmez.
 */
class ProgramQualityGateTest extends TestCase
{
    use RefreshDatabase;

    private const DESC_EN = 'The programme trains engineers in wind energy, photovoltaics, grid integration and storage systems through lab projects and an industry semester.';

    private const DESC_TR = 'Program, rüzgâr enerjisi, fotovoltaik, şebeke entegrasyonu ve depolama sistemlerini laboratuvar projeleri ve sanayi dönemiyle öğretir.';

    private const REQ_TR = 'Başvuru sahipleri mühendislik alanında lisans derecesi ve en az B2 seviyesinde İngilizce belgesi sunmalıdır, motivasyon mektubu gereklidir.';

    private const REQ_EN = 'Applicants need a bachelor degree in engineering, English at level B2 or higher and a motivation letter describing research interests.';

    private int $uni;

    protected function setUp(): void
    {
        parent::setUp();
        $city = DB::table('cities')->insertGetId(['name_de' => 'Qualitätsstadt', 'name_tr' => 'Kalite şehri', 'slug' => 'qg-stadt', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
        $this->uni = DB::table('universities')->insertGetId(['name_de' => 'Qualitäts Universität', 'name_tr' => 'Kalite Üni', 'slug' => 'qg-uni', 'city_id' => $city, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
    }

    private function program(string $slug, array $attrs = []): Program
    {
        DB::table('programs')->insert(array_merge([
            'name_de' => 'Erneuerbare Energien', 'slug' => $slug, 'degree' => 'master', 'language' => 'en', 'university_id' => $this->uni,
            'is_active' => true, 'source' => 'partner', 'created_at' => now(), 'updated_at' => now(),
        ], $attrs));

        return Program::where('slug', $slug)->firstOrFail();
    }

    private function page(string $loc, string $slug): string
    {
        return $this->get("/{$loc}/programs/{$slug}")->assertOk()->getContent();
    }

    private function main(string $html): string
    {
        preg_match('#<main.*?</main>#s', $html, $m);

        return html_entity_decode(strip_tags(preg_replace('#<(script|style)\b.*?</\1>#s', '', $m[0] ?? $html)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /* ------------------------------------------------------------ 1. locale / template */

    public function test_en_and_de_requirements_never_fall_back_to_turkish(): void
    {
        $this->program('qg-tr-req', ['description_en' => self::DESC_EN, 'description_tr' => self::DESC_TR,
            'qualification_requirements_tr' => self::REQ_TR, 'language_requirements_tr' => self::REQ_TR]);

        foreach (['en', 'de'] as $loc) {
            $main = $this->main($this->page($loc, 'qg-tr-req'));
            $this->assertStringNotContainsString('Başvuru sahipleri', $main, "{$loc}: Türkçe şart metni sızdı");
            $this->assertDoesNotMatchRegularExpression('/[ğışİŞĞ]/u', $main, "{$loc}: Türkçe karakter");
        }
        $this->assertStringContainsString('Başvuru sahipleri', $this->main($this->page('tr', 'qg-tr-req')));
    }

    public function test_de_shows_english_requirement_labelled_and_tr_falls_back_to_english(): void
    {
        $this->program('qg-en-req', ['description_en' => self::DESC_EN, 'qualification_requirements_en' => self::REQ_EN]);

        $de = $this->page('de', 'qg-en-req');
        $this->assertStringContainsString('Applicants need a bachelor degree', $de);
        $this->assertMatchesRegularExpression('/Bewerbungsvoraussetzungen.{0,400}\(Englisch\)|\(Englisch\)/su', $de);
        $this->assertStringContainsString('Applicants need a bachelor degree', $this->page('en', 'qg-en-req'));
        $this->assertStringContainsString('Applicants need a bachelor degree', $this->page('tr', 'qg-en-req'));
    }

    public function test_english_description_renders_once_on_en_and_de(): void
    {
        $this->program('qg-once', ['description_en' => self::DESC_EN, 'description_tr' => self::DESC_TR]);
        $needle = 'trains engineers in wind energy';

        $this->assertSame(1, substr_count($this->main($this->page('en', 'qg-once')), $needle), 'EN: açıklama iki kez');
        $de = $this->main($this->page('de', 'qg-once'));
        $this->assertSame(1, substr_count($de, $needle), 'DE: açıklama iki kez');
        $this->assertStringContainsString('noch nicht', $de, 'DE: İngilizce fallback etiketli değil');
        $this->assertStringNotContainsString('rüzgâr', $de);

        // TR: kendi açıklaması + gerçekten farklı orijinal İngilizce metin (katlanır) — ikisi de bir kez
        $tr = $this->main($this->page('tr', 'qg-once'));
        $this->assertSame(1, substr_count($tr, 'rüzgâr enerjisi'));
        $this->assertSame(1, substr_count($tr, $needle));
    }

    public function test_tr_page_with_only_english_description_renders_it_once_with_warning(): void
    {
        $this->program('qg-tr-fallback', ['description_en' => self::DESC_EN]);
        $tr = $this->main($this->page('tr', 'qg-tr-fallback'));
        $this->assertSame(1, substr_count($tr, 'trains engineers in wind energy'));
        $this->assertStringContainsString('hazır değil', $tr);
    }

    public function test_structured_data_has_no_turkish_on_en_de(): void
    {
        $this->program('qg-schema', ['description_en' => self::DESC_EN, 'description_tr' => self::DESC_TR]);
        foreach (['en' => 'trains engineers', 'de' => 'trains engineers', 'tr' => 'rüzgâr'] as $loc => $expect) {
            $html = $this->page($loc, 'qg-schema');
            preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $b);
            $course = collect($b[1])->map(fn ($j) => json_decode($j, true, 512, JSON_THROW_ON_ERROR))->firstWhere('@type', 'Course');
            $this->assertNotNull($course);
            $this->assertStringContainsString($expect, $course['description']);
            $this->assertArrayNotHasKey('applicationDeadline', $course);
        }
    }

    /* ------------------------------------------------------------ 2/3. thin gate + index/sitemap */

    public function test_title_only_daad_description_is_thin(): void
    {
        $p = $this->program('physics-msc-qg', ['name_de' => 'Physics (MSc)', 'source' => 'daad', 'description_en' => 'Physics (MSc)',
            'description_tr' => 'Fizik (MSc)', 'duration_semesters' => 4, 'application_deadline_winter' => '2027-05-31']);
        $this->assertTrue($p->isThin());
        $this->assertSame(0, $p->meaningfulWordCount('<p>Physics (MSc)</p>'));
    }

    public function test_meaningful_short_description_is_not_thin(): void
    {
        $p = $this->program('qg-short', ['description_en' => 'Focus on wind turbines, solar cells, smart grids and battery storage.']);
        $this->assertFalse($p->isThin());
    }

    public function test_requirements_alone_keep_page_indexable_but_bare_facts_do_not(): void
    {
        $this->assertFalse($this->program('qg-req-only', ['description_en' => 'Erneuerbare Energien', 'qualification_requirements_en' => self::REQ_EN])->isThin());
        $this->assertTrue($this->program('qg-facts-only', ['duration_semesters' => 4, 'tuition_fee_eur' => 0, 'application_deadline_winter' => '2027-05-31'])->isThin());
    }

    public function test_thin_page_is_noindex_follow_200_self_canonical_and_absent_from_sitemap(): void
    {
        $this->program('qg-thin', ['description_en' => 'Erneuerbare Energien (M.Sc.)', 'source' => 'daad']);
        $this->program('qg-good', ['description_en' => self::DESC_EN, 'description_tr' => self::DESC_TR]);

        foreach (['tr', 'en', 'de'] as $loc) {
            $thin = $this->page($loc, 'qg-thin');
            $this->assertStringContainsString('<meta name="robots" content="noindex, follow">', $thin);
            $this->assertMatchesRegularExpression('#<link rel="canonical" href="[^"]*/'.$loc.'/programs/qg-thin"#', $thin);
            foreach (['tr', 'en', 'de'] as $l2) {
                $this->assertMatchesRegularExpression('#hreflang="'.$l2.'" href="[^"]*/'.$l2.'/programs/qg-thin"#', $thin);
            }

            $good = $this->page($loc, 'qg-good');
            $this->assertStringNotContainsString('noindex', $good);
            $this->assertMatchesRegularExpression('#<link rel="canonical" href="[^"]*/'.$loc.'/programs/qg-good"#', $good);

            $xml = $this->get("/sitemap-{$loc}.xml")->assertOk()->getContent();
            $this->assertStringContainsString("/{$loc}/programs/qg-good<", $xml);
            $this->assertStringNotContainsString("/{$loc}/programs/qg-thin<", $xml);
        }
    }

    public function test_title_and_h1_unchanged(): void
    {
        $this->program('qg-title', ['description_en' => self::DESC_EN]);
        $html = $this->page('en', 'qg-title');
        $this->assertMatchesRegularExpression('#<title>\s*Erneuerbare Energien — Master @ #u', $html);
        preg_match('#<h1[^>]*>(.*?)</h1>#s', $html, $h1);
        $this->assertStringStartsWith('Erneuerbare Energien', trim(strip_tags($h1[1] ?? '')));
    }

    /* ------------------------------------------------------------ 4. deadlines */

    public function test_past_deadline_is_labelled_last_known_and_future_is_normal(): void
    {
        $past = now()->subDays(30)->format('Y-m-d');
        $future = now()->addDays(60)->format('Y-m-d');
        $this->program('qg-deadlines', ['description_en' => self::DESC_EN, 'application_deadline_winter' => $past, 'application_deadline_summer' => $future]);

        $pastDmy = \Illuminate\Support\Carbon::parse($past)->format('d.m.Y');
        $futureDmy = \Illuminate\Support\Carbon::parse($future)->format('d.m.Y');
        foreach (['tr' => 'Son bilinen başvuru tarihi: ', 'en' => 'Last known application deadline: ', 'de' => 'Zuletzt bekannte Bewerbungsfrist: '] as $loc => $label) {
            $html = $this->page($loc, 'qg-deadlines');
            $this->assertStringContainsString($label.$pastDmy, $html, "{$loc}: geçmiş tarih etiketsiz");
            $this->assertMatchesRegularExpression('#data-deadline-state="current">.*?'.preg_quote($futureDmy, '#').'#s', $html);
            $this->assertDoesNotMatchRegularExpression('#font-semibold text-gray-900">'.preg_quote($pastDmy, '#').'#', $html, "{$loc}: geçmiş tarih güncel gibi");
        }
        $this->assertSame('past', Program::deadlineState($past));
        $this->assertSame('current', Program::deadlineState(now()->format('Y-m-d')));
        $this->assertNull(Program::deadlineState(null));
    }

    public function test_no_synthetic_deadline_is_created(): void
    {
        $past = now()->subYear()->format('Y-m-d');
        $this->program('qg-no-synth', ['description_en' => self::DESC_EN, 'application_deadline_winter' => $past]);
        $html = $this->page('en', 'qg-no-synth');
        $next = \Illuminate\Support\Carbon::parse($past)->addYear()->format('d.m.Y');
        $this->assertStringNotContainsString($next, $html, 'tahmini bir sonraki tarih üretildi');
        $this->assertStringNotContainsString('Summer Semester Deadline', $html, 'olmayan yaz tarihi üretildi');
        $this->assertSame($past, DB::table('programs')->where('slug', 'qg-no-synth')->value('application_deadline_winter'));
    }
}
