<?php

namespace Tests\Feature;

use App\Models\LegalPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Prod deploy provası.
 *
 * tests/Fixtures/legal/prod-snapshot.json, canlı siteden (applytogerman.com,
 * 2026-09-24) indirilen 5 hukuki sayfanın 3 dildeki GERÇEK başlık/açıklama/gövde
 * içeriğidir. Burada o anlık görüntü DB'ye yüklenip migration zinciri prod'daki
 * sırayla koşturulur ve sonuç `legal:audit` + `legal:parity` ile denetlenir.
 *
 * Amaç: "lokalde geçti" ile "canlıda geçer" arasındaki farkı kapatmak. Önceki
 * iki başarısız migration tam bu boşlukta kayboldu.
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
    ];

    /** @return array<string, array{titles: array, descriptions: array, bodies: array}> */
    private function snapshot(): array
    {
        return json_decode(file_get_contents(base_path('tests/Fixtures/legal/prod-snapshot.json')), true);
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

    public function test_after_the_chain_the_only_remaining_findings_are_the_pending_liability_citations(): void
    {
        $this->loadProductionSnapshot();
        $this->runChain();

        // Sorumluluk (Haftung) metinlerinin akıbeti ayrı bir karara bağlı:
        // eski §§ 8-10 TMG rejimi aynı numaralı DDG maddelerine denk DEĞİL
        // (DDG § 9/§ 10 farklı konu), aracı sorumluluğu bugün esas olarak DSA
        // m. 4-6 altında. Numara eşleştirmesi tahminle yapılamayacağı için o
        // cümlelere dokunulmadı. Bu test, GERİ KALAN her şeyin temiz olduğunu
        // ve bekleyen borcun tam olarak bu 9 atıf olduğunu sabitler.
        $findings = $this->auditFindings();

        $this->assertSame(
            array_fill(0, count($findings), 'eski kanun atfı'),
            array_column($findings, 'issue'),
            'sorumluluk atıfları dışında bulgu var — dil sızıntısı/yanlış beyan/eksik beyan sıfır olmalı'
        );

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

        // Parity etkilenmez: taşıma bütünlüğü ayrı bir konu.
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
            $this->assertSame('eski kanun atfı', $f['issue'], "beklenmeyen bulgu: {$f['key']}/{$f['locale']} — {$f['detail']}");
        }
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

    public function test_only_the_verified_paragraph_5_citation_is_converted(): void
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

        // DOKUNULMADI: sorumluluk maddeleri aynı numaralı DDG maddelerine denk
        // değil (DDG § 9/§ 10 farklı konu; aracı sorumluluğu bugün DSA m. 4-6).
        // Karar verilene kadar metin olduğu gibi kalmalı — uydurma eşleştirme yok.
        $this->assertStringContainsString('§ 7 Abs. 1 TMG', $impressum->getBody('tr'));
        $this->assertStringContainsString('§§ 8-10 TMG', $impressum->getBody('tr'));
        $this->assertStringContainsString('§ 7 (1) TMG', $impressum->getBody('en'));
        $this->assertStringContainsString('§ 7 Abs. 1 TMG', $impressum->getBody('de'));

        foreach (['tr', 'en', 'de'] as $locale) {
            $this->assertStringContainsString('§§ 7-10 TMG', $disclaimer->getBody($locale));
            // Hiçbir yerde uydurma DDG eşleştirmesi oluşmamış olmalı.
            $this->assertStringNotContainsString('DDG', $disclaimer->getBody($locale));
            $this->assertStringNotContainsString('§ 7 Abs. 1 DDG', $impressum->getBody($locale));
            $this->assertStringNotContainsString('§§ 8-10 DDG', $impressum->getBody($locale));
            $this->assertStringNotContainsString('§§ 7-10 DDG', $disclaimer->getBody($locale));
        }

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

        // Bulgu sayısı da sabit kalmalı: tekrar koşmak ne yeni sorun yaratmalı
        // ne de bekleyen sorumluluk atıflarını sessizce yutmalı.
        $this->assertCount(9, $this->auditFindings());
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
