<?php

namespace Tests\Feature;

use App\Models\LegalPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * 2026_09_24_000500 — Künye + Yasal Uyarı'daki eski TMG sorumluluk kalıbı.
 *
 * Fixture prod DB'nin ham Markdown'u (prod-raw-snapshot.json); zincir prod'daki
 * sırayla koşulur. Hedef: 9 TMG sorumluluk atfının hiçbiri kalmaz; kullanıcı
 * içeriği için DSA m. 6 + m. 8 anılır; harici link bölümünde hiçbir kanun atfı yok.
 */
class LegalLiabilityModernizationTest extends TestCase
{
    use RefreshDatabase;

    private const CHAIN = [
        '2026_09_24_000100_fix_legal_tracking_disclosure_v2.php',
        '2026_09_24_000150_fix_legal_cookie_locale_leaks.php',
        '2026_09_24_000160_replace_tmg_with_ddg_in_legal_pages.php',
        '2026_09_24_000300_backfill_legal_page_translations.php',
        '2026_09_24_000400_fix_legal_disclosure_raw_markdown.php',
    ];

    private const FIX = '2026_09_24_000500_modernize_legacy_liability_disclosures.php';

    private const LOCALES = ['tr', 'en', 'de'];

    /** Kaldırılması gereken 9 atıf — prod legal:audit'in REVIEW REQUIRED listesi. */
    private const LEGACY = [
        ['impressum', 'tr', '§ 7 Abs. 1 TMG'], ['impressum', 'tr', '§§ 8-10 TMG'],
        ['impressum', 'en', '§ 7 (1) TMG'], ['impressum', 'en', '§§ 8-10 TMG'],
        ['impressum', 'de', '§ 7 Abs. 1 TMG'], ['impressum', 'de', '§§ 8-10 TMG'],
        ['disclaimer', 'tr', '§§ 7-10 TMG'], ['disclaimer', 'en', '§§ 7-10 TMG'], ['disclaimer', 'de', '§§ 7-10 TMG'],
    ];

    private const ART6 = ['tr' => 'DSA m. 6', 'en' => 'Article 6 DSA', 'de' => 'Art. 6 DSA'];

    private const ART8 = ['tr' => 'DSA m. 8', 'en' => 'Article 8 DSA', 'de' => 'Art. 8 DSA'];

    private const LINKS = ['tr' => 'Harici Linkler', 'en' => 'External Links', 'de' => 'Externe Links'];

    protected function setUp(): void
    {
        parent::setUp();
        LegalPage::flushTranslationsTableCheck();
    }

    /* ---------------------------------------------------------------- yardımcı */

    private function loadRawSnapshot(?callable $mutate = null): void
    {
        DB::table('legal_page_translations')->delete();
        DB::table('legal_pages')->delete();

        $order = 0;

        foreach (json_decode(file_get_contents(base_path('tests/Fixtures/legal/prod-raw-snapshot.json')), true) as $key => $data) {
            if ($mutate) {
                $data = $mutate($key, $data);
            }

            DB::table('legal_pages')->insert([
                'key'            => $key,
                'titles'         => json_encode($data['titles'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'descriptions'   => json_encode($data['descriptions'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'bodies'         => json_encode($data['bodies'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'effective_date' => '2026-06-17',
                'is_published'   => true,
                'sort_order'     => $order++,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }
    }

    private function migrateFile(string $file): void
    {
        ob_start();
        try {
            (require database_path("migrations/{$file}"))->up();
        } finally {
            ob_end_clean();
        }
    }

    private function runChain(): void
    {
        foreach (self::CHAIN as $file) {
            $this->migrateFile($file);
        }
    }

    private function runAll(): void
    {
        $this->runChain();
        $this->migrateFile(self::FIX);
    }

    private function body(string $key, string $locale): string
    {
        return (string) LegalPage::findByKey($key)->getBody($locale);
    }

    /** "## Başlık" ile bir sonraki "## " arasındaki bölüm. */
    private function section(string $body, string $heading): ?string
    {
        return preg_match('~^##\h+' . preg_quote($heading, '~') . '\h*$(.*?)(?=^##\h|\z)~msu', $body, $m) ? $m[0] : null;
    }

    /** @return array{summary: array<string, string>, findings: array<int, array<string, string>>} */
    private function audit(): array
    {
        Artisan::call('legal:audit', ['--json' => true]);

        return json_decode(Artisan::output(), true);
    }

    /** @return array<string, string> */
    private function allTranslationBodies(): array
    {
        return DB::table('legal_page_translations')
            ->join('legal_pages', 'legal_pages.id', '=', 'legal_page_translations.legal_page_id')
            ->get(['legal_pages.key', 'legal_page_translations.locale', 'legal_page_translations.body'])
            ->mapWithKeys(fn ($r) => ["{$r->key}/{$r->locale}" => $r->body])
            ->all();
    }

    /* ------------------------------------------------------------ ön koşul */

    public function test_fixture_contains_the_nine_legacy_references_before_the_fix(): void
    {
        $this->loadRawSnapshot();
        $this->runChain();

        foreach (self::LEGACY as [$key, $locale, $citation]) {
            $this->assertStringContainsString($citation, $this->body($key, $locale), "{$key}/{$locale}: fixture artık '{$citation}' içermiyor");
        }

        $report = $this->audit();
        $this->assertSame('REVIEW REQUIRED (9)', $report['summary']['Legacy liability references']);
        $this->assertSame(self::LEGACY, array_map(fn ($f) => [$f['key'], $f['locale'], $f['detail']], $report['findings']));
    }

    /* ------------------------------------------------------------ sonuç */

    public function test_all_nine_legacy_references_are_gone_and_tmg_appears_nowhere(): void
    {
        $this->loadRawSnapshot();
        $this->runAll();

        foreach (LegalPage::all() as $page) {
            foreach (self::LOCALES as $locale) {
                $text = $page->getBody($locale) . ' ' . $page->getDescription($locale) . ' ' . $page->getTitle($locale);
                $this->assertDoesNotMatchRegularExpression('~\bTMG\b~u', $text, "{$page->key}/{$locale}: TMG kaldı");
            }
        }

        // DDG'ye mekanik dönüşüm YOK.
        foreach (self::LOCALES as $locale) {
            foreach (['impressum', 'disclaimer'] as $key) {
                $this->assertDoesNotMatchRegularExpression('~§§?\s*(7|8|9|10)\b[^§]{0,12}DDG~u', $this->body($key, $locale));
            }
        }
    }

    public function test_impressum_uses_dsa_articles_6_and_8_and_keeps_paragraph_5_ddg(): void
    {
        $this->loadRawSnapshot();
        $this->runAll();

        $own = [
            'tr' => 'Bu sayfalardaki kendi içeriğimizden genel hükümler uyarınca sorumluyuz.',
            'en' => 'We are responsible for our own content on these pages under the general laws.',
            'de' => 'Für eigene Inhalte auf diesen Seiten sind wir nach den allgemeinen Gesetzen verantwortlich.',
        ];

        foreach (self::LOCALES as $locale) {
            $body = $this->body('impressum', $locale);

            $this->assertStringContainsString($own[$locale], $body);
            $this->assertStringContainsString(self::ART6[$locale], $body);
            $this->assertStringContainsString(self::ART8[$locale], $body);
            $this->assertStringContainsString('§ 5 DDG', $body);
            $this->assertStringContainsString('§ 18', $body . LegalPage::findByKey('impressum')->getDescription($locale));
        }
    }

    public function test_external_links_section_has_no_statutory_reference_and_no_unverified_claim(): void
    {
        $this->loadRawSnapshot();
        $this->runAll();

        $claim = ['tr' => 'kontrol etmiş', 'en' => 'We check content at the time of linking', 'de' => 'Zum Zeitpunkt der Verlinkung'];

        foreach (self::LOCALES as $locale) {
            $section = $this->section($this->body('disclaimer', $locale), self::LINKS[$locale]);

            $this->assertNotNull($section, "disclaimer/{$locale}: '" . self::LINKS[$locale] . "' bölümü yok");
            $this->assertDoesNotMatchRegularExpression('~\b(TMG|DDG|DSA)\b~u', $section);
            $this->assertStringNotContainsString($claim[$locale], $section);
        }
    }

    public function test_locales_stay_isolated(): void
    {
        $this->loadRawSnapshot();
        $this->runAll();

        foreach (['impressum', 'disclaimer'] as $key) {
            foreach (self::LOCALES as $locale) {
                $body = $this->body($key, $locale);

                foreach (self::LOCALES as $other) {
                    if ($other === $locale) {
                        continue;
                    }
                    $this->assertStringNotContainsString(self::ART6[$other], $key === 'impressum' ? $body : '');
                    $this->assertStringNotContainsString('## ' . self::LINKS[$other] . "\n", $key === 'disclaimer' ? $body : '');
                }

                if ($locale !== 'tr') {
                    $this->assertSame(0, preg_match_all('/[ışğİŞĞ]/u', $body), "{$key}/{$locale}: Türkçe karakter");
                }
            }
        }
    }

    public function test_audit_is_fully_clean_and_parity_holds(): void
    {
        $this->loadRawSnapshot();
        $this->runAll();

        $report = $this->audit();

        $this->assertSame([
            'Tracking disclosure'         => 'PASS',
            'Locale isolation'            => 'PASS',
            '§5 DDG'                      => 'PASS',
            'Data parity'                 => 'PASS',
            'Legacy liability references' => 'PASS',
        ], $report['summary']);
        $this->assertSame([], $report['findings']);

        // Bulgu kalmayınca çıkış kodu mevcut semantikle 0.
        $this->assertSame(0, Artisan::call('legal:audit'));
        $this->assertSame(0, Artisan::call('legal:parity'));
        $this->assertSame(15, DB::table('legal_page_translations')->count());
    }

    public function test_only_impressum_and_disclaimer_change(): void
    {
        $this->loadRawSnapshot();
        $this->runChain();

        $before = $this->allTranslationBodies();
        $legacyBefore = DB::table('legal_pages')->pluck('bodies', 'key')->all();

        $this->travel(1)->days();
        $this->migrateFile(self::FIX);

        $after = $this->allTranslationBodies();

        foreach (['privacy', 'cookies', 'terms'] as $key) {
            foreach (self::LOCALES as $locale) {
                $this->assertSame($before["{$key}/{$locale}"], $after["{$key}/{$locale}"], "{$key}/{$locale} değişti");
            }
            $this->assertSame($legacyBefore[$key], DB::table('legal_pages')->where('key', $key)->value('bodies'));
        }

        // Künye'de diğer bölümler yerinde (operatör, sicil, § 18 MStV sorumlusu, telif).
        foreach (['## Operatör (§ 5 DDG)', 'Ticaret Sicili', '## Telif Hakkı'] as $kept) {
            $this->assertStringContainsString($kept, $after['impressum/tr']);
        }

        $dates = DB::table('legal_pages')->pluck('effective_date', 'key')->map(fn ($d) => substr((string) $d, 0, 10))->all();
        $this->assertSame(now()->toDateString(), $dates['impressum']);
        $this->assertSame(now()->toDateString(), $dates['disclaimer']);
    }

    public function test_is_idempotent(): void
    {
        $this->loadRawSnapshot();
        $this->runAll();

        $bodies = $this->allTranslationBodies();
        $legacy = DB::table('legal_pages')->orderBy('id')->pluck('bodies', 'key')->all();
        $dates = DB::table('legal_pages')->orderBy('id')->pluck('effective_date', 'key')->all();

        $this->travel(2)->days();
        $this->migrateFile(self::FIX);
        $this->runAll();

        $this->assertSame($bodies, $this->allTranslationBodies());
        $this->assertSame($legacy, DB::table('legal_pages')->orderBy('id')->pluck('bodies', 'key')->all());
        $this->assertEquals($dates, DB::table('legal_pages')->orderBy('id')->pluck('effective_date', 'key')->all());

        // Yeni başlık eski başlığın ön eki — bölüm çoğalmamalı.
        foreach (self::LOCALES as $locale) {
            $this->assertSame(1, substr_count($this->body('disclaimer', $locale), '## ' . self::LINKS[$locale]));
            $this->assertSame(1, substr_count($this->body('impressum', $locale), self::ART6[$locale]));
        }
    }

    public function test_tolerates_crlf_line_endings(): void
    {
        $this->loadRawSnapshot(function (string $key, array $data) {
            if (in_array($key, ['impressum', 'disclaimer'], true)) {
                $data['bodies']['de'] = str_replace("\n", "\r\n", $data['bodies']['de']);
            }

            return $data;
        });
        $this->runAll();

        foreach (['impressum', 'disclaimer'] as $key) {
            $body = $this->body($key, 'de');
            $this->assertDoesNotMatchRegularExpression('~\bTMG\b~u', $body);
            $this->assertSame(0, preg_match("/(?<!\r)\n/", $body), "{$key}/de: karışık satır sonu");
        }
    }

    public function test_changed_paragraph_fails_loudly_and_writes_nothing(): void
    {
        $this->loadRawSnapshot(function (string $key, array $data) {
            if ($key === 'impressum') {
                // Panelden elle değiştirilmiş: ne eski ne yeni metin.
                $data['bodies']['en'] = str_replace('we are not obliged to monitor', 'we do not have to monitor', $data['bodies']['en']);
            }

            return $data;
        });
        $this->runChain();
        $before = $this->allTranslationBodies();

        try {
            $this->migrateFile(self::FIX);
            $this->fail('eşleşmeyen metinde migration sessizce geçti');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('impressum/en', $e->getMessage());
        }

        $this->assertSame($before, $this->allTranslationBodies());
        $this->assertStringContainsString('§§ 7-10 TMG', $this->body('disclaimer', 'de'));
    }

    public function test_never_writes_across_locales(): void
    {
        $this->loadRawSnapshot(function (string $key, array $data) {
            if ($key === 'disclaimer') {
                unset($data['bodies']['de'], $data['titles']['de'], $data['descriptions']['de']);
            }

            return $data;
        });
        $this->runAll();

        $this->assertArrayNotHasKey('de', json_decode(DB::table('legal_pages')->where('key', 'disclaimer')->value('bodies'), true));
        $this->get('/de/disclaimer')->assertNotFound();
        $this->assertNotNull($this->section($this->body('disclaimer', 'en'), 'External Links'));
    }

    public function test_is_a_noop_on_an_empty_install(): void
    {
        DB::table('legal_pages')->delete();
        $this->migrateFile(self::FIX);
        $this->assertSame(0, DB::table('legal_pages')->count());
    }

    public function test_all_fifteen_urls_render(): void
    {
        $this->loadRawSnapshot();
        $this->runAll();

        foreach (['privacy', 'cookie-policy', 'terms', 'impressum', 'disclaimer'] as $url) {
            foreach (self::LOCALES as $locale) {
                $html = $this->get("/{$locale}/{$url}")->assertOk()->getContent();
                $this->assertDoesNotMatchRegularExpression('~§§?\s*[\d\-–]+(\s*(Abs\.\s*\d+|\(\d+\)))?\s*TMG~u', $html, "/{$locale}/{$url}");
            }
        }

        $this->get('/de/impressum')->assertSee('Art. 6 DSA', false)->assertSee('Art. 8 DSA', false);
        $this->get('/en/disclaimer')->assertSee('External Links', false)->assertDontSee('§§ 7-10', false);
    }
}
