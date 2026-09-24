<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * İzleme beyanı sözleşmesi — prod'daki migration zincirinin SONUCU.
 *
 * Gövdeler tests/Fixtures/legal/prod-raw-snapshot.json'dan: prod DB'deki HAM
 * Markdown. Beyanı fiilen düzelten 000400'dür; 000100/000150 HTML kalıpları
 * aradığı için ham gövdede hiçbir şey yapmaz (bkz. LegalRawMarkdownFixTest).
 * Zincir yine de prod'daki sırasıyla koşulur ki aralarındaki etkileşim de
 * sınansın.
 *
 * Bu dosya önceden canlı sayfadan kazınmış HTML ile besleniyordu ve 000100'ü
 * o HTML'e karşı test ediyordu — prod'da hiçbir şey değişmezken yeşil geçti.
 */
class LegalTrackingDisclosureTest extends TestCase
{
    use RefreshDatabase;

    /** Prod sırası. */
    private const CHAIN = [
        '2026_09_24_000100_fix_legal_tracking_disclosure_v2.php',
        '2026_09_24_000150_fix_legal_cookie_locale_leaks.php',
        '2026_09_24_000160_replace_tmg_with_ddg_in_legal_pages.php',
        '2026_09_24_000300_backfill_legal_page_translations.php',
        '2026_09_24_000400_fix_legal_disclosure_raw_markdown.php',
    ];

    /** Prod metnindeki, fiilî davranışla çelişen ifadeler (ham Markdown biçimiyle). */
    private const FALSE_CLAIMS = [
        'cookies' => [
            'tr' => ['Google Analytics, Facebook Pixel veya benzer kullanmıyoruz', 'Yok. Üçüncü taraf takip kullanmıyoruz'],
            'en' => ['We do not use Google Analytics, Facebook Pixel, or similar', 'Yok. Üçüncü taraf takip kullanmıyoruz'],
            'de' => ['Wir nutzen kein Google Analytics, Facebook Pixel', 'Yok. Üçüncü taraf takip kullanmıyoruz'],
        ],
        'privacy' => [
            'tr' => ['Self-hosted (Google Analytics yok)', "ABD'ye veri aktarımı **yapılmaz**", 'GDPR 6(1)(f) meşru menfaat (self-hosted'],
            'en' => ['Self-hosted (no Google Analytics)', 'No transfers to the USA.', 'Art. 6(1)(f) GDPR (self-hosted'],
            'de' => ['Self-hosted (kein Google Analytics)', 'Keine Übermittlung in die USA.', 'Art. 6(1)(f) DSGVO (self-hosted'],
        ],
    ];

    /** Düzeltmeden sonra bulunması gereken beyanlar. */
    private const REQUIRED = [
        'cookies' => [
            'tr' => ['Google Analytics 4', 'Microsoft Clarity', 'oturum kayıtları', 'G-D0VB1M1RKF', '`_clck`', 'GDPR 6(1)(a) açık rıza', 'EU-U.S. Data Privacy Framework', 'AB Standart Sözleşme Maddeleri'],
            'en' => ['Google Analytics 4', 'Microsoft Clarity', 'session recordings', 'G-D0VB1M1RKF', '`_clck`', 'Art. 6(1)(a) GDPR', 'EU-U.S. Data Privacy Framework', 'EU Standard Contractual Clauses'],
            'de' => ['Google Analytics 4', 'Microsoft Clarity', 'Sitzungsaufzeichnungen', 'G-D0VB1M1RKF', '`_clck`', 'Art. 6(1)(a) DSGVO', 'EU-U.S. Data Privacy Framework', 'EU-Standardvertragsklauseln'],
        ],
        'privacy' => [
            'tr' => ['Google Analytics 4', 'Microsoft Clarity', 'GDPR 6(1)(a) açık rıza', 'EU-U.S. Data Privacy Framework', 'Analitik verileri', 'oturum kayıtları'],
            'en' => ['Google Analytics 4', 'Microsoft Clarity', 'Art. 6(1)(a) GDPR', 'EU-U.S. Data Privacy Framework', 'Analytics data', 'session recordings'],
            'de' => ['Google Analytics 4', 'Microsoft Clarity', 'Art. 6(1)(a) DSGVO', 'EU-U.S. Data Privacy Framework', 'Analysedaten', 'Sitzungsaufzeichnungen'],
        ],
    ];

    /** Prod'un ham gövdelerini legacy JSON'a yazar; çeviri satırlarını zincir doldurur. */
    private function seedProductionBodies(): void
    {
        DB::table('legal_page_translations')->delete();

        $snapshot = json_decode(file_get_contents(base_path('tests/Fixtures/legal/prod-raw-snapshot.json')), true);

        foreach (['cookies', 'privacy'] as $key) {
            DB::table('legal_pages')->updateOrInsert(
                ['key' => $key],
                [
                    'titles'       => json_encode($snapshot[$key]['titles'], JSON_UNESCAPED_UNICODE),
                    'descriptions' => json_encode($snapshot[$key]['descriptions'], JSON_UNESCAPED_UNICODE),
                    'bodies'       => json_encode($snapshot[$key]['bodies'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'is_published' => true,
                    'sort_order'   => 0,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]
            );
        }
    }

    private function runFix(): void
    {
        ob_start();
        try {
            foreach (self::CHAIN as $file) {
                (require database_path("migrations/{$file}"))->up();
            }
        } finally {
            ob_end_clean();
        }
    }

    /** @return array<string, string> */
    private function bodies(string $key): array
    {
        return json_decode(DB::table('legal_pages')->where('key', $key)->value('bodies'), true);
    }

    public function test_fixtures_still_contain_the_false_claims_we_are_fixing(): void
    {
        // Koruma testi: fixture prod'un HATALI hâlini tutmalı. Biri gelip
        // fixture'ı düzeltirse asıl testler boşuna yeşile döner.
        $this->seedProductionBodies();

        foreach (self::FALSE_CLAIMS as $key => $perLocale) {
            foreach ($perLocale as $locale => $claims) {
                foreach ($claims as $claim) {
                    $this->assertStringContainsString(
                        $claim,
                        $this->bodies($key)[$locale],
                        "fixture {$key}/{$locale} artık '{$claim}' içermiyor — test anlamsızlaştı"
                    );
                }
            }
        }
    }

    public function test_false_tracking_claims_are_removed_in_every_locale(): void
    {
        $this->seedProductionBodies();
        $this->runFix();

        foreach (self::FALSE_CLAIMS as $key => $perLocale) {
            foreach ($perLocale as $locale => $claims) {
                foreach ($claims as $claim) {
                    $this->assertStringNotContainsString(
                        $claim,
                        $this->bodies($key)[$locale],
                        "{$key}/{$locale} hâlâ yanlış beyan içeriyor: {$claim}"
                    );
                }
            }
        }
    }

    public function test_real_disclosures_are_present_in_every_locale(): void
    {
        $this->seedProductionBodies();
        $this->runFix();

        foreach (self::REQUIRED as $key => $perLocale) {
            foreach ($perLocale as $locale => $needles) {
                foreach ($needles as $needle) {
                    $this->assertStringContainsString(
                        $needle,
                        $this->bodies($key)[$locale],
                        "{$key}/{$locale} beklenen beyanı içermiyor: {$needle}"
                    );
                }
            }
        }
    }

    public function test_analytics_legal_basis_moves_from_legitimate_interest_to_consent(): void
    {
        $this->seedProductionBodies();
        $this->runFix();

        // Analitik satırı 6(1)(a) + § 25 TDDDG'ye geçmeli...
        $this->assertStringContainsString('GDPR 6(1)(a) açık rıza ve § 25 (1) TDDDG (Google Analytics 4', $this->bodies('privacy')['tr']);
        $this->assertStringContainsString('Art. 6(1)(a) GDPR, explicit consent, and § 25(1) TDDDG (Google Analytics 4', $this->bodies('privacy')['en']);
        $this->assertStringContainsString('Art. 6(1)(a) DSGVO, ausdrückliche Einwilligung, und § 25 Abs. 1 TDDDG (Google Analytics 4', $this->bodies('privacy')['de']);

        // ...ama güvenlik/log için 6(1)(f) meşru menfaat DOĞRU dayanak, kalmalı.
        $this->assertStringContainsString('GDPR 6(1)(f) meşru menfaat (botların engellenmesi', $this->bodies('privacy')['tr']);
        $this->assertStringContainsString('Art. 6(1)(f) GDPR, legitimate interest', $this->bodies('privacy')['en']);
        $this->assertStringContainsString('Art. 6(1)(f) DSGVO berechtigtes Interesse', $this->bodies('privacy')['de']);
    }

    public function test_cookie_table_lists_third_party_cookies(): void
    {
        $this->seedProductionBodies();
        $this->runFix();

        foreach (['tr', 'en', 'de'] as $locale) {
            $body = $this->bodies('cookies')[$locale];

            foreach (['_ga', '_ga_*', '_clck', '_clsk', 'almanyauni_consent_mkt'] as $cookie) {
                $this->assertStringContainsString("| `{$cookie}` |", $body, "cookies/{$locale}: {$cookie} tabloda yok");
            }

            // Eski satırlar yerinde kalmalı — tablo değiştirilmedi, eklendi.
            $this->assertStringContainsString('| `XSRF-TOKEN` |', $body);
            $this->assertStringContainsString('| `almanyauni_consent` |', $body);
        }
    }

    public function test_is_idempotent(): void
    {
        $this->seedProductionBodies();
        $this->runFix();
        $after = $this->bodies('cookies');
        $afterPrivacy = $this->bodies('privacy');

        $this->runFix();

        $this->assertSame($after, $this->bodies('cookies'), 'ikinci koşu çerez gövdesini değiştirdi');
        $this->assertSame($afterPrivacy, $this->bodies('privacy'), 'ikinci koşu gizlilik gövdesini değiştirdi');

        // Çift ekleme olmamalı. (_clck tabloda ve üçüncü taraf listesinde geçer —
        // o mükerrerlik değil, o yüzden tablo satırını sayıyoruz.)
        $analyticsRow = ['tr' => 'Analitik verileri', 'en' => 'Analytics data', 'de' => 'Analysedaten'];

        foreach (['tr', 'en', 'de'] as $locale) {
            $this->assertSame(1, substr_count($this->bodies('cookies')[$locale], '| `_clck` |'), "cookies/{$locale}: tablo satırı mükerrer");
            $this->assertSame(1, substr_count($this->bodies('privacy')[$locale], $analyticsRow[$locale]), "privacy/{$locale}: veri satırı mükerrer");
        }
    }

    public function test_unrelated_sections_and_other_pages_are_untouched(): void
    {
        $this->seedProductionBodies();

        // Panelden elle düzenlenmiş başka bir hukuki sayfa (dokunulmamalı).
        DB::table('legal_pages')->updateOrInsert(
            ['key' => 'impressum'],
            [
                'titles'       => json_encode(['tr' => 'Impressum']),
                'descriptions' => json_encode(['tr' => '']),
                'bodies'       => json_encode(['tr' => 'ELLE DÜZENLENDİ']),
                'is_published' => true,
                'sort_order'   => 0,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]
        );

        $this->runFix();

        $this->assertSame(
            'ELLE DÜZENLENDİ',
            json_decode(DB::table('legal_pages')->where('key', 'impressum')->value('bodies'), true)['tr']
        );

        // Hedeflenmeyen bölümler yerinde kalmalı.
        $tr = $this->bodies('privacy')['tr'];
        $this->assertStringContainsString('**TechNS UG (haftungsbeschränkt)**', $tr);
        $this->assertStringContainsString('10 yıl (HGB § 257, AO § 147)', $tr);
        $this->assertStringContainsString('GDPR Madde 22 anlamında otomatik karar verme veya profilleme yapılmaz.', $tr);
        $this->assertStringContainsString('All-Inkl (KASSERVER.COM)', $tr);

        $cookiesTr = $this->bodies('cookies')['tr'];
        $this->assertStringContainsString('Çerezler tarayıcınızda saklanan küçük metin dosyalarıdır', $cookiesTr);
        $this->assertStringContainsString('admin@applytogerman.com', $cookiesTr);
    }

    public function test_unrecognised_body_fails_loudly_and_is_not_overwritten(): void
    {
        // Gövde tanınmıyorsa yanlış yere içerik basılmaz — ama 2026_09_23_170000
        // gibi SESSİZCE de geçilmez: 000400 exception atar, hiçbir şey yazmaz.
        DB::table('legal_pages')->updateOrInsert(
            ['key' => 'cookies'],
            [
                'titles'       => json_encode(['tr' => 'c']),
                'descriptions' => json_encode(['tr' => '']),
                'bodies'       => json_encode(['tr' => 'Tamamen farklı, elle yazılmış bir çerez metni.']),
                'is_published' => true,
                'sort_order'   => 0,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]
        );

        try {
            $this->runFix();
            $this->fail('tanınmayan gövdede migration sessizce geçti');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('cookies/tr', $e->getMessage());
        }

        $this->assertSame('Tamamen farklı, elle yazılmış bir çerez metni.', $this->bodies('cookies')['tr']);
    }

    public function test_down_does_not_restore_the_false_legal_text(): void
    {
        $this->seedProductionBodies();
        $this->runFix();

        (require database_path('migrations/2026_09_24_000400_fix_legal_disclosure_raw_markdown.php'))->down();
        (require database_path('migrations/2026_09_24_000100_fix_legal_tracking_disclosure_v2.php'))->down();

        foreach (['tr', 'en', 'de'] as $locale) {
            $this->assertStringContainsString('Microsoft Clarity', $this->bodies('cookies')[$locale]);
            $this->assertStringNotContainsString('kullanmıyoruz', $this->bodies('cookies')[$locale]);
        }
    }

    public function test_rendered_pages_show_the_corrected_disclosure(): void
    {
        $this->seedProductionBodies();
        $this->runFix();

        foreach (['tr', 'en', 'de'] as $locale) {
            $this->get("/{$locale}/cookie-policy")
                ->assertOk()
                ->assertSee('Microsoft Clarity', false)
                ->assertSee('G-D0VB1M1RKF', false)
                ->assertSee('<code>_clck</code>', false)
                ->assertDontSee('kullanmıyoruz', false);

            $this->get("/{$locale}/privacy")
                ->assertOk()
                ->assertSee('Microsoft Clarity', false)
                ->assertDontSee('Self-hosted (Google Analytics', false);
        }
    }
}
