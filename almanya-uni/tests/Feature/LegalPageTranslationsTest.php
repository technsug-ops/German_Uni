<?php

namespace Tests\Feature;

use App\Models\LegalPage;
use App\Models\LegalPageTranslation;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Hukuki içeriğin locale-bazlı veri modeli.
 *
 * Buradaki testlerin asıl derdi tek bir cümle: bir dilin metni başka bir dilin
 * sayfasında ASLA görünmemeli — ne fallback yoluyla, ne de JSON'daki komşu
 * key'den sızarak.
 */
class LegalPageTranslationsTest extends TestCase
{
    use RefreshDatabase;

    /** Legacy JSON bundle'ından bir sayfa kurar (backfill'in gördüğü hâl). */
    private function seedLegacyPage(string $key = 'cookies'): LegalPage
    {
        $page = new LegalPage();
        $page->forceFill([
            'key'          => $key,
            'titles'       => ['tr' => 'Çerez', 'en' => 'Cookies', 'de' => 'Cookies DE'],
            'descriptions' => ['tr' => 'tr desc', 'en' => 'en desc', 'de' => 'de desc'],
            'bodies'       => ['tr' => '<p>TR gövde</p>', 'en' => '<p>EN body</p>', 'de' => '<p>DE Inhalt</p>'],
            'is_published' => true,
            'sort_order'   => 0,
        ])->save();

        return $page->fresh('translations');
    }

    private function runBackfill(): void
    {
        $m = require database_path('migrations/2026_09_24_000300_backfill_legal_page_translations.php');

        ob_start();
        $m->up();
        ob_end_clean();
    }

    private function body(string $key, string $locale): ?string
    {
        return DB::table('legal_page_translations')
            ->join('legal_pages', 'legal_pages.id', '=', 'legal_page_translations.legal_page_id')
            ->where('legal_pages.key', $key)
            ->where('legal_page_translations.locale', $locale)
            ->value('legal_page_translations.body');
    }

    /* ---------------------------------------------------------- A) sızıntı */

    public function test_en_and_de_cookie_policies_contain_no_turkish_fragments(): void
    {
        // Fixture'lar canlıdan indirilen gerçek gövdeler; 000150 migration'ı bu
        // sızıntıları temizliyor.
        $bodies = [];
        foreach (['tr', 'en', 'de'] as $locale) {
            $bodies[$locale] = file_get_contents(base_path("tests/Fixtures/legal/cookies.{$locale}.html"));
        }

        DB::table('legal_pages')->updateOrInsert(['key' => 'cookies'], [
            'titles'       => json_encode(['tr' => 'Ç', 'en' => 'C', 'de' => 'C']),
            'descriptions' => json_encode([]),
            'bodies'       => json_encode($bodies, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'is_published' => true,
            'sort_order'   => 0,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        $leakFix = require database_path('migrations/2026_09_24_000150_fix_legal_cookie_locale_leaks.php');
        $trackingFix = require database_path('migrations/2026_09_24_000100_fix_legal_tracking_disclosure_v2.php');

        ob_start();
        $trackingFix->up();
        $leakFix->up();
        ob_end_clean();

        $fixed = json_decode(DB::table('legal_pages')->where('key', 'cookies')->value('bodies'), true);

        $turkishFragments = [
            'dil tercihi',
            'Session yönetimi',
            'Onay gerektirmez',
            'Anonim ziyaretçi sayımı',
            'onayınızla',
            'Üçüncü taraf takip kullanmıyoruz',
            'kullanmıyoruz',
        ];

        foreach (['en', 'de'] as $locale) {
            foreach ($turkishFragments as $fragment) {
                $this->assertStringNotContainsString(
                    $fragment,
                    $fixed[$locale],
                    "cookies/{$locale} hâlâ Türkçe parça içeriyor: {$fragment}"
                );
            }
        }

        // Doğal çeviriler yerine geçmiş olmalı.
        $this->assertStringContainsString('language preference', $fixed['en']);
        $this->assertStringContainsString('Session management', $fixed['en']);
        $this->assertStringContainsString('Spracheinstellung', $fixed['de']);
        $this->assertStringContainsString('Sitzungsverwaltung', $fixed['de']);

        // TR gövdesine dokunulmamış olmalı.
        $this->assertStringContainsString('dil tercihi', $fixed['tr']);
    }

    /* ------------------------------------------------- B) translation isolation */

    public function test_updating_one_locale_does_not_touch_the_others(): void
    {
        $page = $this->seedLegacyPage('privacy');
        $this->runBackfill();

        $trBefore = $this->body('privacy', 'tr');
        $deBefore = $this->body('privacy', 'de');

        $page->translations()->where('locale', 'en')->update(['body' => '<p>EN rewritten</p>']);

        $this->assertSame('<p>EN rewritten</p>', $this->body('privacy', 'en'));
        $this->assertSame($trBefore, $this->body('privacy', 'tr'), 'EN düzenlemesi TR gövdesini değiştirdi');
        $this->assertSame($deBefore, $this->body('privacy', 'de'), 'EN düzenlemesi DE gövdesini değiştirdi');
    }

    public function test_panel_style_save_of_one_locale_leaves_other_locales_intact(): void
    {
        // Panel/seeder hâlâ bodies[tr|en|de] dizisiyle kaydediyor; model bunu
        // locale satırlarına bölmeli ve yalnız gelen dile dokunmalı.
        $page = $this->seedLegacyPage('cookies');
        $this->runBackfill();

        $trBefore = $this->body('cookies', 'tr');
        $enBefore = $this->body('cookies', 'en');

        $page->syncTranslations(
            ['de' => 'Cookies DE v2'],
            ['de' => 'de desc v2'],
            ['de' => '<p>DE Inhalt v2</p>'],
        );

        $this->assertSame('<p>DE Inhalt v2</p>', $this->body('cookies', 'de'));
        $this->assertSame($trBefore, $this->body('cookies', 'tr'));
        $this->assertSame($enBefore, $this->body('cookies', 'en'));
    }

    /* ----------------------------------------------------- C) unique constraint */

    public function test_a_page_cannot_have_two_rows_for_the_same_locale(): void
    {
        $page = $this->seedLegacyPage('terms');
        $this->runBackfill();

        $this->expectException(QueryException::class);

        LegalPageTranslation::create([
            'legal_page_id' => $page->id,
            'locale'        => 'en',
            'title'         => 'duplicate',
            'body'          => '<p>duplicate</p>',
        ]);
    }

    /* ------------------------------------------------------------ D) no fallback */

    public function test_missing_translation_returns_404_instead_of_another_language(): void
    {
        $this->seedLegacyPage('privacy');
        $this->runBackfill();

        // DE çevirisini kaldır — /de/privacy artık TR/EN metnine DÜŞMEMELİ.
        DB::table('legal_page_translations')
            ->join('legal_pages', 'legal_pages.id', '=', 'legal_page_translations.legal_page_id')
            ->where('legal_pages.key', 'privacy')
            ->where('legal_page_translations.locale', 'de')
            ->delete();

        $this->get('/de/privacy')->assertNotFound();

        // Diğer diller etkilenmemeli.
        $this->get('/tr/privacy')->assertOk()->assertSee('TR gövde', false);
        $this->get('/en/privacy')->assertOk()->assertSee('EN body', false);
    }

    public function test_model_never_returns_another_locales_body(): void
    {
        $page = $this->seedLegacyPage('disclaimer');
        $this->runBackfill();
        $page = LegalPage::findByKey('disclaimer');

        $page->translations()->where('locale', 'en')->delete();
        $page->unsetRelation('translations');
        $page->load('translations');

        $this->assertSame('', $page->getBody('en'), 'EN gövdesi başka dile düştü');
        $this->assertFalse($page->hasContentFor('en'));
        $this->assertSame('<p>TR gövde</p>', $page->getBody('tr'));
        $this->assertSame('<p>DE Inhalt</p>', $page->getBody('de'));
    }

    public function test_legacy_bridge_serves_the_same_locale_only_when_no_translations_exist(): void
    {
        // Backfill koşmamış senaryo (rollout penceresi): sayfa yaşamaya devam
        // etmeli, ama yalnız KENDİ dilinin legacy değerinden.
        $this->seedLegacyPage('impressum');
        DB::table('legal_page_translations')->delete();

        $this->get('/tr/impressum')->assertOk()->assertSee('TR gövde', false)->assertDontSee('EN body', false);
        $this->get('/en/impressum')->assertOk()->assertSee('EN body', false)->assertDontSee('TR gövde', false);

        // Legacy JSON'da o dil yoksa köprü de uydurmaz.
        DB::table('legal_pages')->where('key', 'impressum')->update([
            'bodies' => json_encode(['tr' => '<p>TR gövde</p>'], JSON_UNESCAPED_UNICODE),
        ]);

        $this->get('/de/impressum')->assertNotFound();
    }

    public function test_legacy_bridge_is_off_once_the_page_has_translations(): void
    {
        // Çevirileri olan bir sayfada eksik dil GERÇEKTEN eksiktir; bayat JSON'a
        // dönmek, panelden silinmiş bir metni geri getirmek olurdu.
        $page = $this->seedLegacyPage('privacy');
        $this->runBackfill();
        $page->translations()->where('locale', 'de')->delete();

        $this->get('/de/privacy')->assertNotFound();
        $this->get('/tr/privacy')->assertOk();
    }

    public function test_each_locale_page_renders_only_its_own_body(): void
    {
        $this->seedLegacyPage('terms');
        $this->runBackfill();

        $this->get('/tr/terms')->assertOk()->assertSee('TR gövde', false)->assertDontSee('EN body', false)->assertDontSee('DE Inhalt', false);
        $this->get('/en/terms')->assertOk()->assertSee('EN body', false)->assertDontSee('TR gövde', false)->assertDontSee('DE Inhalt', false);
        $this->get('/de/terms')->assertOk()->assertSee('DE Inhalt', false)->assertDontSee('TR gövde', false)->assertDontSee('EN body', false);
    }

    /* ------------------------------------------------------ E) migration integrity */

    public function test_backfill_moves_every_locale_byte_for_byte(): void
    {
        $page = $this->seedLegacyPage('impressum');

        // Modelin saved() hook'u zaten senkronladı; sıfırdan taşımayı görmek için
        // çeviri satırlarını silip backfill'i tek başına koşturuyoruz.
        DB::table('legal_page_translations')->delete();
        $this->runBackfill();

        $legacy = json_decode(DB::table('legal_pages')->where('key', 'impressum')->value('bodies'), true);

        foreach (['tr', 'en', 'de'] as $locale) {
            $this->assertSame(
                $legacy[$locale],
                $this->body('impressum', $locale),
                "impressum/{$locale} taşınırken içerik değişti"
            );
        }

        $this->assertSame(3, $page->translations()->count());
    }

    public function test_backfill_does_not_invent_translations_for_missing_locales(): void
    {
        $page = new LegalPage();
        $page->forceFill([
            'key'          => 'disclaimer',
            'titles'       => ['tr' => 'Uyarı'],
            'descriptions' => [],
            'bodies'       => ['tr' => '<p>Sadece TR</p>'],
            'is_published' => true,
            'sort_order'   => 0,
        ])->save();

        DB::table('legal_page_translations')->delete();
        $this->runBackfill();

        $this->assertSame(['tr'], $page->translations()->pluck('locale')->all());
        $this->assertNull($this->body('disclaimer', 'en'), 'EN için sahte çeviri üretildi');
        $this->assertNull($this->body('disclaimer', 'de'), 'DE için sahte çeviri üretildi');
    }

    /* ------------------------------------------------------------ F) idempotency */

    public function test_backfill_is_idempotent_and_preserves_manual_edits(): void
    {
        $page = $this->seedLegacyPage('cookies');
        $this->runBackfill();

        // Panelden yapılmış bir düzenlemeyi taklit et.
        $page->translations()->where('locale', 'en')->update(['body' => '<p>panelden düzenlendi</p>']);

        $this->runBackfill();
        $this->runBackfill();

        $this->assertSame(3, $page->translations()->count(), 'ikinci koşu kayıt çoğalttı');
        $this->assertSame('<p>panelden düzenlendi</p>', $this->body('cookies', 'en'), 'backfill elle düzenlemeyi ezdi');
    }

    /* ---------------------------------------------------------------- parity */

    public function test_parity_command_reports_full_match_after_backfill(): void
    {
        $this->seedLegacyPage('privacy');
        $this->seedLegacyPage('terms');
        $this->runBackfill();

        $this->artisan('legal:parity')->assertExitCode(0);
    }

    public function test_parity_command_fails_when_a_translation_drifts(): void
    {
        $page = $this->seedLegacyPage('privacy');
        $this->runBackfill();

        $page->translations()->where('locale', 'en')->update(['body' => '<p>drift</p>']);

        $this->artisan('legal:parity')->assertExitCode(1);
    }
}
