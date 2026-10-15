<?php

namespace Tests\Feature;

use App\Support\PhraseGuard;
use Tests\TestCase;

/**
 * OSV Faz 0 — bilinen yanlış resmî ifadeler aktif kaynaklarda geri gelmesin (veri: database/data/osv/known_wrong.json).
 *
 *  - Kural kelime filtresi değil, birebir yanlış ifade (deny-list). Tarihsel anlatım `allow_context` ile serbest
 *    (ör. "eskiden 120 gün", "früher 120 Tage", "2025: 48.300 €").
 *  - Taranan: config/*.php, ToolsController, PageFaq, BlogAutoLinker, public Blade görünümleri (filament hariç),
 *    lang/*.json, OSV paketlerinin YENİ metinleri (replace/text/value — find değil), faq-en paketleri, brief tohum komutları.
 *  - PHP/Blade yorumları taranmaz: "eski sürümdeki X iddiası kaldırıldı" gibi düzeltme notları yanlış iddia değildir.
 *
 * Paket 2 kapsamındaki dosyalar (sözlük, araç denetleyicisi, PageFaq, BlogAutoLinker, lang) ayrı testte: bu dosyalardaki
 * Blue Card / APS / Sperrkonto / 120 gün düzeltmesi paket 2'de; o yayına kadar bu test KIRMIZI beklenir.
 */
class OfficialKnownWrongRegressionTest extends TestCase
{
    private const DATA = 'database/data/osv/known_wrong.json';

    /** OSV paket 2'nin düzelttiği dosyalar (başka mühendis). */
    private const PACKAGE2_SOURCES = [
        'config/glossary.php',
        'app/Http/Controllers/Web/ToolsController.php',
        'app/Support/PageFaq.php',
        'app/Services/Content/BlogAutoLinker.php',
        'lang/tr.json',
        'lang/en.json',
        'lang/de.json',
    ];

    private function data(): array
    {
        return json_decode((string) file_get_contents(base_path(self::DATA)), true, 512, JSON_THROW_ON_ERROR);
    }

    /** @return array<string,string> göreli yol → taranacak metin */
    private function activeSources(): array
    {
        $base = base_path() . DIRECTORY_SEPARATOR;
        $rel = fn (string $p) => str_replace('\\', '/', substr($p, strlen($base)));
        $out = [];

        $php = array_merge(
            glob(base_path('config/*.php')) ?: [],
            [base_path('app/Http/Controllers/Web/ToolsController.php'), base_path('app/Support/PageFaq.php'), base_path('app/Services/Content/BlogAutoLinker.php')],
            glob(base_path('app/Console/Commands/Seed*Briefs.php')) ?: [],
            [base_path('app/Console/Commands/BlogGenerateStarter.php')],
        );
        foreach ($php as $f) {
            $out[$rel($f)] = self::stripPhpComments((string) file_get_contents($f));
        }

        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(base_path('resources/views'), \FilesystemIterator::SKIP_DOTS));
        foreach ($it as $file) {
            $path = $rel($file->getPathname());
            if (! str_ends_with($path, '.blade.php') || str_starts_with($path, 'resources/views/filament/')) {
                continue;
            }
            $src = (string) file_get_contents($file->getPathname());
            $out[$path] = preg_replace('/\{\{--.*?--\}\}/s', '', $src) ?? $src;
        }

        foreach (glob(base_path('lang/*.json')) ?: [] as $f) {
            $out[$rel($f)] = (string) file_get_contents($f);
        }
        foreach (glob(base_path('database/data/faq-en/*.json')) ?: [] as $f) {
            $out[$rel($f)] = (string) file_get_contents($f);
        }
        foreach (glob(base_path('database/data/osv/*.json')) ?: [] as $f) {
            $pkg = json_decode((string) file_get_contents($f), true);
            $texts = [];
            foreach ($pkg['edits'] ?? [] as $e) {
                foreach (['replace', 'text', 'value'] as $k) {
                    if (isset($e[$k]) && is_string($e[$k])) {
                        $texts[] = $e[$k];
                    }
                }
            }
            foreach ($pkg['html'] ?? [] as $h) {
                if (isset($h['replace']) && is_string($h['replace'])) {
                    $texts[] = $h['replace'];
                }
            }
            $out[$rel($f) . ' (yeni metinler)'] = implode("\n\n", $texts);
        }

        return $out;
    }

    private static function stripPhpComments(string $src): string
    {
        $code = '';
        foreach (token_get_all($src) as $t) {
            if (is_array($t)) {
                if ($t[0] === T_COMMENT || $t[0] === T_DOC_COMMENT) {
                    $code .= ' ';
                    continue;
                }
                $code .= $t[1];
            } else {
                $code .= $t;
            }
        }

        return $code;
    }

    /** @return array<int,string> "dosya :: id «phrase» → bağlam" */
    private function scan(array $sources): array
    {
        $d = $this->data();
        $window = (int) ($d['window'] ?? 40);
        $failures = [];
        foreach ($sources as $file => $text) {
            foreach ($d['entries'] as $e) {
                foreach (PhraseGuard::hits($text, $e['phrase'], $e['allow_context'] ?? [], $window) as $ctx) {
                    $failures[] = "{$file} :: {$e['id']} «{$e['phrase']}» → " . str_replace("\n", ' ', mb_substr($ctx, 0, 140));
                }
            }
        }

        return $failures;
    }

    public function test_known_wrong_list_is_valid_and_covers_package1_known_wrong(): void
    {
        $d = $this->data();
        $this->assertSame([], $d['edits'], 'onarım paketi değil; düzenleme içermez');
        $ids = array_column($d['entries'], 'id');
        $this->assertSame(count($ids), count(array_unique($ids)), 'id benzersiz');
        foreach ($d['entries'] as $e) {
            foreach (['id', 'phrase', 'locale', 'topic', 'context'] as $k) {
                $this->assertNotEmpty($e[$k] ?? null, ($e['id'] ?? '?') . " alanı eksik: $k");
            }
        }
        // OsvPackage1RepairTest::KNOWN_WRONG'un tamamı bu listede (tek kaynak buraya taşınabilir)
        $known = (new \ReflectionClass(OsvPackage1RepairTest::class))->getConstant('KNOWN_WRONG');
        $phrases = array_column($d['entries'], 'phrase');
        foreach ($known as $k) {
            $this->assertContains($k, $phrases, "KNOWN_WRONG eksik: $k");
        }
        // Görevde istenen başlıklar temsil ediliyor
        foreach (['blue-card', 'aps', 'student-work', 'sperrkonto', 'visa-channel', 'visa-fee', 'remonstration', 'check-date'] as $topic) {
            $this->assertContains($topic, array_column($d['entries'], 'topic'), $topic);
        }
    }

    public function test_historical_context_is_allowed_but_current_claim_is_not(): void
    {
        $d = $this->data();
        $byId = array_column($d['entries'], null, 'id');
        $w = (int) $d['window'];
        $hit = fn (string $id, string $text) => PhraseGuard::hits($text, $byId[$id]['phrase'], $byId[$id]['allow_context'] ?? [], $w) !== [];

        $this->assertTrue($hit('work-120-2', 'Öğrenciler yılda 120 gün çalışabilir.'));
        $this->assertFalse($hit('work-120-2', 'Sınır eskiden 120 gündü; şimdi 140 tam gün.'));
        $this->assertTrue($hit('work-120-3', 'Studierende dürfen 120 Tage im Jahr arbeiten.'));
        $this->assertFalse($hit('work-120-3', 'Früher waren es 120 Tage, seit 2024 sind es 140.'));
        $this->assertTrue($hit('work-120-5', 'Students can work 120 days a year.'));
        $this->assertFalse($hit('work-120-5', 'The limit was previously 120 days; it is now 140.'));
        $this->assertTrue($hit('bluecard-48300-de', 'Mindestgehalt 2026: 48.300 EUR'));
        $this->assertFalse($hit('bluecard-48300-de', 'Eşik 2025 yılında 48.300 € idi; 2026: 50.700 €.'));
        $this->assertTrue($hit('checkdate-1', 'Başvurular 14.10.2026 tarihinden itibaren portaldan.'));
        $this->assertFalse($hit('checkdate-1', 'Güncel durum (kontrol tarihi: 14.10.2026): başvurular portaldan.'));
        $this->assertTrue($hit('idata-only-en', 'iData is the only authorized center'), 'büyük/küçük harf duyarsız');
    }

    /** Paket 2 dışındaki aktif kaynaklar: şimdi yeşil olmalı. */
    public function test_active_sources_outside_package2_are_free_of_known_wrong_phrases(): void
    {
        $sources = array_filter($this->activeSources(), fn ($f) => ! in_array($f, self::PACKAGE2_SOURCES, true), ARRAY_FILTER_USE_KEY);
        $this->assertGreaterThan(100, count($sources), 'tarama kapsamı beklenenden dar');
        $failures = $this->scan($sources);
        $this->assertSame([], $failures, "Bilinen yanlış ifade aktif kaynakta:\n" . implode("\n", $failures));
    }

    /**
     * Paket 2 kaynakları (sözlük, ToolsController, PageFaq, BlogAutoLinker, lang). OSV paket 2 yayına girene kadar
     * KIRMIZI beklenir; çıktı, hangi ifadenin hangi dosyada kaldığını listeler.
     */
    public function test_package2_sources_are_free_of_known_wrong_phrases(): void
    {
        $sources = array_intersect_key($this->activeSources(), array_flip(self::PACKAGE2_SOURCES));
        $this->assertCount(count(self::PACKAGE2_SOURCES), $sources);
        $failures = $this->scan($sources);
        $this->assertSame([], $failures, "Bilinen yanlış ifade (OSV paket 2 kapsamı):\n" . implode("\n", $failures));
    }
}
