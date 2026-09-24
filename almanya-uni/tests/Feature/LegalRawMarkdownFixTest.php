<?php

namespace Tests\Feature;

use App\Models\LegalPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * 2026_09_24_000400 — izleme beyanını HAM MARKDOWN üzerinde düzelten migration.
 *
 * Fixture: tests/Fixtures/legal/prod-raw-snapshot.json. Prod DB'deki gövdeler
 * seeder Markdown'udur; bu dosya o Markdown'un prod'daki hâlidir (eski TR slug'lı
 * iç linkler + zincir öncesi § 5 TMG dahil). Render edildiğinde canlıdaki 15
 * sayfa/dil HTML'iyle birebir aynı çıktığı 2026-09-24'te doğrulandı.
 *
 * Bilinçli olarak HTML fixture KULLANILMAZ: önceki üç migration HTML'e karşı
 * test edildiği için yeşil geçip prod'da hiçbir şey yapmadı.
 */
class LegalRawMarkdownFixTest extends TestCase
{
    use RefreshDatabase;

    /** Prod'daki sıra: önceki zincir, ardından yeni düzeltme. */
    private const CHAIN = [
        '2026_09_24_000100_fix_legal_tracking_disclosure_v2.php',
        '2026_09_24_000150_fix_legal_cookie_locale_leaks.php',
        '2026_09_24_000160_replace_tmg_with_ddg_in_legal_pages.php',
        '2026_09_24_000300_backfill_legal_page_translations.php',
    ];

    private const FIX = '2026_09_24_000400_fix_legal_disclosure_raw_markdown.php';

    private const LOCALES = ['tr', 'en', 'de'];

    protected function setUp(): void
    {
        parent::setUp();
        LegalPage::flushTranslationsTableCheck();
    }

    /* ---------------------------------------------------------------- yardımcı */

    private function snapshot(): array
    {
        return json_decode(file_get_contents(base_path('tests/Fixtures/legal/prod-raw-snapshot.json')), true);
    }

    private function loadRawSnapshot(?callable $mutate = null): void
    {
        DB::table('legal_page_translations')->delete();
        DB::table('legal_pages')->delete();

        $order = 0;

        foreach ($this->snapshot() as $key => $data) {
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

    /** @return array<int, array{key: string, locale: string, issue: string, detail: string}> */
    private function auditFindings(): array
    {
        Artisan::call('legal:audit', ['--json' => true]);

        return json_decode(Artisan::output(), true)['findings'];
    }

    /** @return array<string, string> "key/locale" => body */
    private function allTranslationBodies(): array
    {
        return DB::table('legal_page_translations')
            ->join('legal_pages', 'legal_pages.id', '=', 'legal_page_translations.legal_page_id')
            ->get(['legal_pages.key', 'legal_page_translations.locale', 'legal_page_translations.body'])
            ->mapWithKeys(fn ($r) => ["{$r->key}/{$r->locale}" => $r->body])
            ->all();
    }

    /* -------------------------------------------------------------- kök neden */

    public function test_the_old_html_chain_changes_nothing_on_raw_markdown(): void
    {
        // Prod'da yaşanan: zincir koştu, "tamamlandı" dedi, metin aynı kaldı.
        $this->loadRawSnapshot();
        $this->runChain();

        $this->assertStringContainsString('Self-hosted (no Google Analytics)', $this->body('privacy', 'en'));
        $this->assertStringContainsString('dil tercihi', $this->body('cookies', 'en'));
        $this->assertStringNotContainsString('Microsoft Clarity', $this->body('cookies', 'de'));
        // Düz metin değiştiren 000160 ise çalışmıştı — canlıyla aynı.
        $this->assertStringContainsString('§ 5 DDG', $this->body('impressum', 'tr'));
    }

    /* ---------------------------------------------------------------- privacy */

    public function test_privacy_discloses_ga4_and_clarity_in_every_locale(): void
    {
        $this->loadRawSnapshot();
        $this->runAll();

        $expect = [
            'tr' => ['GDPR 6(1)(a) açık rıza', 'ısı haritaları', 'tıklama ve kaydırma analizi', 'oturum kayıtları'],
            'en' => ['Art. 6(1)(a) GDPR', 'heatmaps', 'click and scroll analytics', 'session recordings'],
            'de' => ['Art. 6(1)(a) DSGVO', 'Heatmaps', 'Klick- und Scroll-Analyse', 'Sitzungsaufzeichnungen'],
        ];

        foreach (self::LOCALES as $locale) {
            $body = $this->body('privacy', $locale);

            foreach (array_merge(['Google Analytics 4', 'G-D0VB1M1RKF', 'Microsoft Clarity', 'EU-U.S. Data Privacy Framework', 'data-cookie-settings'], $expect[$locale]) as $needle) {
                $this->assertStringContainsString($needle, $body, "privacy/{$locale}: '{$needle}' yok");
            }

            foreach (['Google Analytics yok', 'no Google Analytics', 'kein Google Analytics', "ABD'ye veri aktarımı **yapılmaz**", 'No transfers to the USA', 'Keine Übermittlung in die USA', '6(1)(f) meşru menfaat (self-hosted', 'GDPR (self-hosted', 'DSGVO (self-hosted'] as $claim) {
                $this->assertStringNotContainsString($claim, $body, "privacy/{$locale}: yanlış beyan kaldı: {$claim}");
            }
        }
    }

    public function test_privacy_keeps_untouched_sections_verbatim(): void
    {
        $this->loadRawSnapshot();
        $this->runChain();
        $before = $this->body('privacy', 'en');
        $this->migrateFile(self::FIX);
        $after = $this->body('privacy', 'en');

        // Yalnızca hedef satırlar değişir; hak listesi, saklama süreleri vb. aynen kalır.
        foreach (['## 4. Retention Periods', '- **Access logs** — Anonymized after 90 days', '## 7. Your Rights (GDPR Art. 15-22 / KVKK Art. 11)', '[Cookie Policy](/en/cerez-politikasi)'] as $kept) {
            $this->assertStringContainsString($kept, $before);
            $this->assertStringContainsString($kept, $after);
        }
    }

    /* ---------------------------------------------------------------- cookies */

    public function test_cookie_policy_lists_the_real_cookies_in_every_locale(): void
    {
        $this->loadRawSnapshot();
        $this->runAll();

        foreach (self::LOCALES as $locale) {
            $body = $this->body('cookies', $locale);

            foreach (['`_ga`', '`_ga_*`', '`_clck`', '`_clsk`', '`almanyauni_consent_mkt`', '`almanyauni_uid`', 'G-D0VB1M1RKF', 'Microsoft Clarity', 'data-cookie-settings'] as $needle) {
                $this->assertStringContainsString($needle, $body, "cookies/{$locale}: '{$needle}' yok");
            }

            // Tablo satırı başına bir kez — çoğalma yok.
            foreach (['| `_ga` |', '| `_ga_*` |', '| `_clck` |', '| `_clsk` |', '| `almanyauni_consent_mkt` |'] as $row) {
                $this->assertSame(1, substr_count($body, $row), "cookies/{$locale}: {$row} satırı tekil değil");
            }

            foreach (['We do not use Google Analytics', 'Wir nutzen kein Google Analytics', 'Self-hosted analitik tercih', 'self-hosted analytics', 'self-hosted Analytik'] as $claim) {
                $this->assertStringNotContainsString($claim, $body);
            }
        }

        // Meta açıklamadaki "üçüncü taraf yok" da gitti.
        foreach (['tr' => 'üçüncü taraf yok', 'en' => 'no third parties', 'de' => 'keine Drittanbieter'] as $locale => $old) {
            $description = (string) LegalPage::findByKey('cookies')->getDescription($locale);
            $this->assertStringNotContainsString($old, $description);
            $this->assertStringContainsString('Microsoft Clarity', $description);
        }
    }

    public function test_english_and_german_have_zero_turkish_leaks(): void
    {
        $this->loadRawSnapshot();
        $this->runAll();

        $leaks = array_filter($this->auditFindings(), fn ($f) => $f['issue'] === 'dil sızıntısı');
        $this->assertSame([], array_values($leaks));

        // Audit listesinden bağımsız ikinci kontrol: Türkçe'ye özgü harf kalmamalı.
        foreach (['privacy', 'cookies'] as $key) {
            foreach (['en', 'de'] as $locale) {
                $page = LegalPage::findByKey($key);
                $text = $this->body($key, $locale) . ' ' . $page->getDescription($locale) . ' ' . $page->getTitle($locale);
                $this->assertSame(0, preg_match_all('/[ışğİŞĞ]/u', $text), "{$key}/{$locale}: Türkçe karakter kaldı");
            }
        }
    }

    /* --------------------------------------------------- audit + parity + TMG */

    public function test_only_remaining_audit_findings_are_the_liability_citations_under_legal_review(): void
    {
        $this->loadRawSnapshot();
        $this->runAll();

        $findings = $this->auditFindings();

        // LEGAL_REFERENCE_REVIEW_REQUIRED: § 7 / §§ 8-10 / §§ 7-10 TMG bilerek bırakıldı.
        $this->assertSame([
            ['impressum', 'tr', '§ 7 Abs. 1 TMG'],
            ['impressum', 'tr', '§§ 8-10 TMG'],
            ['impressum', 'en', '§ 7 (1) TMG'],
            ['impressum', 'en', '§§ 8-10 TMG'],
            ['impressum', 'de', '§ 7 Abs. 1 TMG'],
            ['impressum', 'de', '§§ 8-10 TMG'],
            ['disclaimer', 'tr', '§§ 7-10 TMG'],
            ['disclaimer', 'en', '§§ 7-10 TMG'],
            ['disclaimer', 'de', '§§ 7-10 TMG'],
        ], array_map(fn ($f) => [$f['key'], $f['locale'], $f['detail']], $findings));

        $this->assertSame(['eski kanun atfı'], array_values(array_unique(array_column($findings, 'issue'))));
    }

    public function test_audit_separates_critical_failures_from_legal_review_items(): void
    {
        $this->loadRawSnapshot();
        $this->runAll();

        Artisan::call('legal:audit', ['--json' => true]);
        $report = json_decode(Artisan::output(), true);

        $this->assertSame([
            'Tracking disclosure'         => 'PASS',
            'Locale isolation'            => 'PASS',
            '§5 DDG'                      => 'PASS',
            'Data parity'                 => 'PASS',
            'Legacy liability references' => 'REVIEW REQUIRED (9)',
        ], $report['summary']);

        $this->assertSame(['REVIEW REQUIRED'], array_values(array_unique(array_column($report['findings'], 'severity'))));

        // Çıkış kodu değişmedi: inceleme bekleyen atıf varken 1.
        $this->assertSame(1, Artisan::call('legal:audit'));
        $text = Artisan::output();
        $this->assertStringContainsString('REVIEW REQUIRED — LEGAL_REFERENCE_REVIEW_REQUIRED (9)', $text);
        $this->assertStringNotContainsString('CRITICAL FAIL', $text);
    }

    public function test_audit_flags_real_problems_as_critical(): void
    {
        // Zincir öncesi prod: § 5 TMG, yanlış beyanlar, sızıntılar, çeviri satırı yok.
        $this->loadRawSnapshot();

        Artisan::call('legal:audit', ['--json' => true]);
        $report = json_decode(Artisan::output(), true);

        $this->assertStringStartsWith('FAIL', $report['summary']['Tracking disclosure']);
        $this->assertStringStartsWith('FAIL', $report['summary']['Locale isolation']);
        $this->assertStringStartsWith('FAIL', $report['summary']['§5 DDG']);
        $this->assertStringStartsWith('FAIL', $report['summary']['Data parity']);

        $ddg = array_filter($report['findings'], fn ($f) => $f['group'] === '§5 DDG');
        $this->assertContains('§ 5 TMG', array_column($ddg, 'detail'));
        $this->assertSame(['CRITICAL'], array_values(array_unique(array_column($ddg, 'severity'))));
    }

    public function test_translation_rows_and_legacy_json_stay_in_parity(): void
    {
        $this->loadRawSnapshot();
        $this->runAll();

        $this->assertSame(0, Artisan::call('legal:parity'));
        $this->assertSame(15, DB::table('legal_page_translations')->count());

        foreach (['privacy', 'cookies'] as $key) {
            $page = DB::table('legal_pages')->where('key', $key)->first();
            $bodies = json_decode($page->bodies, true);
            $descriptions = json_decode($page->descriptions, true);

            foreach (self::LOCALES as $locale) {
                $row = DB::table('legal_page_translations')->where('legal_page_id', $page->id)->where('locale', $locale)->first();
                $this->assertSame($row->body, $bodies[$locale], "{$key}/{$locale} gövde parity");
                $this->assertSame($row->description, $descriptions[$locale], "{$key}/{$locale} açıklama parity");
            }
        }
    }

    public function test_other_legal_pages_are_not_touched(): void
    {
        $this->loadRawSnapshot();
        $this->runChain();

        $before = DB::table('legal_pages')->whereIn('key', ['terms', 'impressum', 'disclaimer'])->orderBy('id')->get(['key', 'titles', 'descriptions', 'bodies', 'effective_date', 'updated_at']);
        $rowsBefore = array_filter($this->allTranslationBodies(), fn ($k) => ! str_starts_with($k, 'privacy/') && ! str_starts_with($k, 'cookies/'), ARRAY_FILTER_USE_KEY);

        $this->travel(1)->days();
        $this->migrateFile(self::FIX);

        $after = DB::table('legal_pages')->whereIn('key', ['terms', 'impressum', 'disclaimer'])->orderBy('id')->get(['key', 'titles', 'descriptions', 'bodies', 'effective_date', 'updated_at']);
        $rowsAfter = array_filter($this->allTranslationBodies(), fn ($k) => ! str_starts_with($k, 'privacy/') && ! str_starts_with($k, 'cookies/'), ARRAY_FILTER_USE_KEY);

        $this->assertEquals($before, $after);
        $this->assertSame($rowsBefore, $rowsAfter);
        $this->assertCount(9, $rowsAfter);
    }

    public function test_last_updated_date_moves_only_for_changed_pages(): void
    {
        $this->loadRawSnapshot();
        $this->runAll();

        $dates = DB::table('legal_pages')->pluck('effective_date', 'key')->map(fn ($d) => substr((string) $d, 0, 10))->all();

        $this->assertSame(now()->toDateString(), $dates['privacy']);
        $this->assertSame(now()->toDateString(), $dates['cookies']);
        $this->assertSame('2026-06-17', $dates['terms']);
        $this->assertSame('2026-06-17', $dates['impressum']);
        $this->assertSame('2026-06-17', $dates['disclaimer']);
    }

    /* ------------------------------------------------------------ idempotency */

    public function test_running_the_fix_again_changes_nothing(): void
    {
        $this->loadRawSnapshot();
        $this->runAll();

        $bodies = $this->allTranslationBodies();
        $legacy = DB::table('legal_pages')->orderBy('id')->pluck('bodies', 'key')->all();
        $dates = DB::table('legal_pages')->orderBy('id')->pluck('effective_date', 'key')->all();

        $this->travel(3)->days();
        $this->migrateFile(self::FIX);
        $this->runAll();

        $this->assertSame($bodies, $this->allTranslationBodies());
        $this->assertSame($legacy, DB::table('legal_pages')->orderBy('id')->pluck('bodies', 'key')->all());
        $this->assertEquals($dates, DB::table('legal_pages')->orderBy('id')->pluck('effective_date', 'key')->all(), 'değişiklik yokken tarih kaydı');
        $this->assertSame(15, DB::table('legal_page_translations')->count());
        $this->assertCount(9, $this->auditFindings());
    }

    /* ------------------------------------------------------- sağlamlık/güvenlik */

    public function test_tolerates_crlf_line_endings_and_star_bullets(): void
    {
        $this->loadRawSnapshot(function (string $key, array $data) {
            if ($key === 'privacy') {
                $en = preg_replace('/^- /m', '* ', $data['bodies']['en']);
                $data['bodies']['en'] = str_replace("\n", "\r\n", $en);
            }

            return $data;
        });
        $this->runAll();

        $body = $this->body('privacy', 'en');
        $this->assertStringContainsString('* **Analytics:** Google Analytics 4 (Google Ireland Ltd., measurement ID `G-D0VB1M1RKF`)', $body);
        $this->assertStringContainsString("\r\n| **Analytics data** |", $body);
        // Karışık satır sonu üretilmedi.
        $this->assertSame(0, preg_match("/(?<!\r)\n/", $body));
    }

    public function test_missing_target_block_fails_loudly_and_writes_nothing(): void
    {
        $this->loadRawSnapshot(function (string $key, array $data) {
            if ($key === 'cookies') {
                // Panelden elle değiştirilmiş bir üçüncü taraf paragrafı: ne eski ne yeni metin.
                $data['bodies']['de'] = preg_replace('/^\*\*Keine\.\*\*.*$/m', 'Individuell bearbeiteter Absatz.', $data['bodies']['de']);
            }

            return $data;
        });
        $this->runChain();
        $before = $this->allTranslationBodies();

        try {
            $this->migrateFile(self::FIX);
            $this->fail('eşleşmeyen blokta migration sessizce geçti');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('cookies/de', $e->getMessage());
            $this->assertStringContainsString('üçüncü taraf bölümü', $e->getMessage());
        }

        // Başka sayfa/dil de dahil HİÇBİR ŞEY yazılmadı.
        $this->assertSame($before, $this->allTranslationBodies());
        $this->assertStringContainsString('Self-hosted (no Google Analytics)', $this->body('privacy', 'en'));
    }

    public function test_never_writes_across_locales(): void
    {
        $this->loadRawSnapshot(function (string $key, array $data) {
            if ($key === 'privacy') {
                unset($data['bodies']['de'], $data['titles']['de'], $data['descriptions']['de']);
            }

            return $data;
        });
        $this->runAll();

        $this->assertNull(DB::table('legal_page_translations')
            ->join('legal_pages', 'legal_pages.id', '=', 'legal_page_translations.legal_page_id')
            ->where('legal_pages.key', 'privacy')->where('locale', 'de')->first());
        $this->assertArrayNotHasKey('de', json_decode(DB::table('legal_pages')->where('key', 'privacy')->value('bodies'), true));

        $this->get('/de/privacy')->assertNotFound();
        $this->get('/en/privacy')->assertOk()->assertSee('Microsoft Clarity');
        // Her dil kendi metnini aldı.
        $this->assertStringContainsString('ısı haritaları', $this->body('privacy', 'tr'));
        $this->assertStringNotContainsString('ısı haritaları', $this->body('privacy', 'en'));
    }

    public function test_is_a_noop_on_an_empty_install(): void
    {
        DB::table('legal_pages')->delete();

        $this->migrateFile(self::FIX);

        $this->assertSame(0, DB::table('legal_pages')->count());
    }

    /* ---------------------------------------------------------------- 15 URL */

    public function test_all_fifteen_urls_render_the_right_language(): void
    {
        $this->loadRawSnapshot();
        $this->runAll();

        $headings = [
            'privacy'       => ['tr' => '1. Veri Sorumlusu', 'en' => '1. Data Controller', 'de' => '1. Verantwortliche Stelle'],
            'cookie-policy' => ['tr' => 'Çerez Nedir?', 'en' => 'What is a cookie?', 'de' => 'Was sind Cookies?'],
            'terms'         => ['tr' => 'Kullanım Koşulları', 'en' => 'Terms of Use', 'de' => 'Nutzungsbedingungen'],
            'impressum'     => ['tr' => 'Operatör (§ 5 DDG)', 'en' => 'Operator (§ 5 DDG)', 'de' => 'Betreiber (§ 5 DDG)'],
            'disclaimer'    => ['tr' => 'Yasal Uyarı', 'en' => 'Disclaimer', 'de' => 'Haftungsausschluss'],
        ];

        foreach ($headings as $url => $byLocale) {
            foreach ($byLocale as $locale => $heading) {
                $res = $this->get("/{$locale}/{$url}")->assertOk()->assertSee($heading, false);
                $html = $res->getContent();

                $this->assertStringNotContainsString('§ 5 TMG', $html, "/{$locale}/{$url}");

                if (in_array($url, ['privacy', 'cookie-policy'], true)) {
                    $this->assertStringContainsString('Google Analytics 4', $html, "/{$locale}/{$url}");
                    $this->assertStringContainsString('Microsoft Clarity', $html, "/{$locale}/{$url}");
                    // Geri çekme bağlantısı render'da korunuyor (html_input=allow).
                    $this->assertStringContainsString('data-cookie-settings>', $html, "/{$locale}/{$url}");

                    if ($locale !== 'tr') {
                        foreach (['dil tercihi', 'Onay gerektirmez', 'Session yönetimi', 'kullanmıyoruz', 'onayınızla', 'Anonim ziyaretçi'] as $leak) {
                            $this->assertStringNotContainsString($leak, $html, "/{$locale}/{$url}: {$leak}");
                        }
                    }
                }
            }
        }
    }
}
