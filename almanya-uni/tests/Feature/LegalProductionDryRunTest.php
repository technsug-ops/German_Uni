<?php

namespace Tests\Feature;

use App\Models\LegalPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Prod deploy provası — HAM MARKDOWN üzerinde.
 *
 * tests/Fixtures/legal/prod-raw-snapshot.json, prod DB'deki 5 hukuki sayfanın
 * 3 dildeki GERÇEK başlık/açıklama/ham gövdesidir (seeder Markdown'u + prod'daki
 * eski TR slug'lı iç linkler + zincir öncesi § 5 TMG; render edildiğinde canlı
 * HTML ile birebir aynı olduğu 2026-09-24'te doğrulandı). Burada o görüntü DB'ye
 * yüklenip migration zinciri prod'daki sırayla koşturulur ve sonuç `legal:audit`
 * + `legal:parity` ile denetlenir.
 *
 * NEDEN HAM: Bu test önceden canlı sayfadan kazınmış HTML ile besleniyordu.
 * DB'deki gövde ise Markdown; HTML kalıbı arayan 000100/000150 testte yeşil
 * geçip prod'da hiçbir şey değiştirmedi. Migration'ın eşleştirdiği katman DB
 * olduğu için fixture da DB'deki hâl olmak zorunda.
 */
class LegalProductionDryRunTest extends TestCase
{
    use RefreshDatabase;

    /** Prod'daki migration sırası. */
    private const CHAIN = [
        '2026_09_24_000100_fix_legal_tracking_disclosure_v2.php',
        '2026_09_24_000150_fix_legal_cookie_locale_leaks.php',
        '2026_09_24_000160_replace_tmg_with_ddg_in_legal_pages.php',
        '2026_09_24_000300_backfill_legal_page_translations.php',
        '2026_09_24_000400_fix_legal_disclosure_raw_markdown.php',
        '2026_09_24_000500_modernize_legacy_liability_disclosures.php',
    ];

    /** @return array<string, array{titles: array, descriptions: array, bodies: array}> */
    private function snapshot(): array
    {
        return json_decode(file_get_contents(base_path('tests/Fixtures/legal/prod-raw-snapshot.json')), true);
    }

    private function loadProductionSnapshot(): void
    {
        DB::table('legal_page_translations')->delete();
        DB::table('legal_pages')->delete();

        $order = 0;

        foreach ($this->snapshot() as $key => $data) {
            DB::table('legal_pages')->insert([
                'key'          => $key,
                'titles'       => json_encode($data['titles'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'descriptions' => json_encode($data['descriptions'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'bodies'       => json_encode($data['bodies'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'effective_date' => '2026-05-27',
                'is_published' => true,
                'sort_order'   => $order++,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }

    private function runChain(): void
    {
        ob_start();
        foreach (self::CHAIN as $file) {
            (require database_path("migrations/{$file}"))->up();
        }
        ob_end_clean();
    }

    public function test_production_snapshot_starts_out_failing_the_audit(): void
    {
        // Koruma testi: anlık görüntü canlının HATALI hâlini tutmalı, yoksa
        // aşağıdaki PASS'lar hiçbir şey kanıtlamaz.
        $this->loadProductionSnapshot();
        $this->artisan('legal:audit')->assertExitCode(1);
    }

    public function test_after_the_full_chain_the_audit_is_clean(): void
    {
        $this->loadProductionSnapshot();
        $this->runChain();

        // 000400 izleme beyanını, 000500 eski TMG sorumluluk kalıbını düzeltti:
        // prod'un ham içeriği zincirden geçince hiçbir bulgu kalmamalı.
        $this->assertSame([], $this->auditFindings());
        $this->artisan('legal:audit')->assertExitCode(0);
        $this->artisan('legal:parity')->assertExitCode(0);
    }

    /** @return array<int, array{key: string, locale: string, issue: string, detail: string}> */
    private function auditFindings(): array
    {
        \Illuminate\Support\Facades\Artisan::call('legal:audit', ['--json' => true]);

        return json_decode(\Illuminate\Support\Facades\Artisan::output(), true)['findings'];
    }

    public function test_tracking_and_language_checks_pass_on_real_production_content(): void
    {
        $this->loadProductionSnapshot();
        $this->runChain();

        foreach ($this->auditFindings() as $f) {
            $this->fail("beklenmeyen bulgu: {$f['key']}/{$f['locale']} — {$f['issue']}: {$f['detail']}");
        }

        $this->addToAssertionCount(1);
    }

    public function test_audit_does_not_flag_tmg_when_it_is_cited_historically(): void
    {
        // Yürürlükten kalkmış bir kanuna ATIF hata değil; onu yürürlükteki hukuk
        // gibi SUNMAK hata. Tarihsel açıklamalar FAIL üretmemeli.
        $this->loadProductionSnapshot();
        $this->runChain();

        $before = count($this->auditFindings());

        $page = \App\Models\LegalPage::findByKey('disclaimer');
        $bodies = $page->bodies;
        $bodies['de'] .= '<p>Bis zum 13.05.2024 galt insoweit § 8 TMG a. F.</p>';
        $page->update(['titles' => $page->titles, 'descriptions' => $page->descriptions, 'bodies' => $bodies]);

        $this->assertCount($before, $this->auditFindings(), 'tarihsel TMG atfı yanlışlıkla FAIL sayıldı');
    }

    public function test_all_five_pages_are_migrated_in_all_three_locales(): void
    {
        $this->loadProductionSnapshot();
        $this->runChain();

        $this->assertSame(15, DB::table('legal_page_translations')->count());

        foreach (['tr', 'en', 'de'] as $locale) {
            $this->assertSame(5, DB::table('legal_page_translations')->where('locale', $locale)->count());
        }
    }

    public function test_paragraph_5_is_converted_and_liability_text_is_rewritten_not_renumbered(): void
    {
        $this->loadProductionSnapshot();
        $this->runChain();

        $impressum = LegalPage::findByKey('impressum');
        $disclaimer = LegalPage::findByKey('disclaimer');

        // DOĞRULANMIŞ: künye yükümlülüğü DDG § 5'e taşındı, konu ve numara örtüşüyor.
        foreach (['tr', 'en', 'de'] as $locale) {
            $this->assertStringContainsString('§ 5 DDG', $impressum->getBody($locale));
            $this->assertStringContainsString('§ 5 DDG', (string) $impressum->getDescription($locale));
            $this->assertStringNotContainsString('§ 5 TMG', $impressum->getBody($locale));
            $this->assertStringNotContainsString('§ 5 TMG', (string) $impressum->getDescription($locale));
        }

        // Sorumluluk atıfları DDG'ye MEKANİK çevrilmedi: 000500 metni DSA m. 6/8'e
        // (kullanıcı içeriği) göre yeniden yazdı, harici linklerden atfı kaldırdı.
        foreach (['tr', 'en', 'de'] as $locale) {
            $this->assertDoesNotMatchRegularExpression('~\bTMG\b~u', $impressum->getBody($locale));
            $this->assertDoesNotMatchRegularExpression('~\bTMG\b~u', $disclaimer->getBody($locale));
            $this->assertStringNotContainsString('DDG', $disclaimer->getBody($locale));
            $this->assertStringNotContainsString('§ 7 Abs. 1 DDG', $impressum->getBody($locale));
            $this->assertStringNotContainsString('§§ 8-10 DDG', $impressum->getBody($locale));
            $this->assertStringNotContainsString('§§ 7-10 DDG', $disclaimer->getBody($locale));
        }
        $this->assertStringContainsString('Art. 6 DSA', $impressum->getBody('de'));
        $this->assertStringContainsString('Art. 8 DSA', $impressum->getBody('de'));

        // MStV atfı geçerliliğini koruyor, dokunulmamalı.
        $this->assertStringContainsString('§ 18 MStV', (string) $impressum->getDescription('de'));
    }

    public function test_every_locale_url_still_renders_after_the_chain(): void
    {
        $this->loadProductionSnapshot();
        $this->runChain();

        $urls = ['privacy', 'terms', 'cookie-policy', 'impressum', 'disclaimer'];

        foreach (['tr', 'en', 'de'] as $locale) {
            foreach ($urls as $url) {
                $this->get("/{$locale}/{$url}")->assertOk();
            }
        }
    }

    public function test_chain_is_idempotent_on_production_content(): void
    {
        $this->loadProductionSnapshot();
        $this->runChain();

        $bodies = DB::table('legal_page_translations')->orderBy('id')->pluck('body', 'id')->all();

        $this->runChain();
        $this->runChain();

        $this->assertSame(15, DB::table('legal_page_translations')->count(), 'zincir kayıt çoğalttı');
        $this->assertSame($bodies, DB::table('legal_page_translations')->orderBy('id')->pluck('body', 'id')->all());

        // Tekrar koşmak yeni bulgu da yaratmamalı.
        $this->assertSame([], $this->auditFindings());
    }

    /* ------------------------------------------------ rollout guard (tablo yok) */

    public function test_legal_pages_do_not_500_when_the_translations_table_is_missing(): void
    {
        // Deploy sırası "önce kod, sonra migration" olduğu için bu pencere gerçek:
        // yeni model canlıda, tablo henüz yok. Guard olmasa QueryException → 500.
        $this->loadProductionSnapshot();

        Schema::dropIfExists('legal_page_translations');
        LegalPage::flushTranslationsTableCheck();

        $this->assertFalse(LegalPage::translationsTableAvailable());

        foreach (['tr', 'en', 'de'] as $locale) {
            foreach (['privacy', 'terms', 'cookie-policy', 'impressum', 'disclaimer'] as $url) {
                $this->get("/{$locale}/{$url}")->assertOk();
            }
        }
    }

    public function test_missing_table_bridge_never_serves_another_locale(): void
    {
        $this->loadProductionSnapshot();

        // DE gövdesini boşalt: guard devredeyken bile TR/EN metnine DÜŞMEMELİ.
        $bodies = $this->snapshot()['privacy']['bodies'];
        unset($bodies['de']);
        DB::table('legal_pages')->where('key', 'privacy')->update([
            'bodies' => json_encode($bodies, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);

        Schema::dropIfExists('legal_page_translations');
        LegalPage::flushTranslationsTableCheck();

        $this->get('/de/privacy')->assertNotFound();
        $this->get('/tr/privacy')->assertOk()->assertSee('Veri Sorumlusu', false);
        $this->get('/en/privacy')->assertOk()->assertSee('Data Controller', false);
    }

    public function test_panel_saves_are_inert_while_the_table_is_missing(): void
    {
        // Guard yazma yolunda da devrede olmalı; yoksa panelden kayıt 500 verir.
        $this->loadProductionSnapshot();

        Schema::dropIfExists('legal_page_translations');
        LegalPage::flushTranslationsTableCheck();

        $page = LegalPage::findByKey('cookies');
        $bodies = $page->bodies;
        $bodies['en'] .= '<!-- panel -->';

        $page->update(['titles' => $page->titles, 'descriptions' => $page->descriptions, 'bodies' => $bodies]);

        $this->assertStringContainsString('<!-- panel -->', LegalPage::findByKey('cookies')->getBody('en'));
        $this->assertStringNotContainsString('<!-- panel -->', LegalPage::findByKey('cookies')->getBody('tr'));
    }
}
