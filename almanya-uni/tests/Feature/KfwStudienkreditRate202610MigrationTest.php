<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

/**
 * KfW-Studienkredit + Bildungskredit faiz güncellemesi (01.10.2026) migration'ı: gerçek rehber gövdeleri (2026_09_27_000100 kaynağından)
 * üzerinde uygulama, dil paritesi, değişken-faiz ifadesi, eski oranın yalnız "önceki dönem" olarak kalması, ilgisiz
 * blokların korunması, render + canonical/hreflang/JSON-LD, no-op, kısmi durum ve rollback sabitlenir.
 */
class KfwStudienkreditRate202610MigrationTest extends TestCase
{
    use RefreshDatabase;

    private const MIGRATION = 'database/migrations/2026_10_02_000100_kfw_studienkredit_rate_2026_10.php';

    private const SOURCE = 'database/migrations/2026_09_27_000100_blog_student_loans_and_study_financing_in_germany.php';

    private const GROUP = '054874dc-fc54-481b-ba30-d060d837e5a1';

    private function migration(): object
    {
        return require base_path(self::MIGRATION);
    }

    /** 000100 kaynağındaki $trBody/$enBody/$deBody nowdoc gövdeleri (canlı content_md ile aynı metin). */
    private function bodies(): array
    {
        $src = str_replace("\r\n", "\n", file_get_contents(base_path(self::SOURCE)));
        $out = [];
        foreach (['tr', 'en', 'de'] as $loc) {
            $this->assertSame(1, preg_match('/\$'.$loc."Body = <<<'MD'\n(.*?)\nMD;/s", $src, $m), "{$loc} gövdesi bulunamadı");
            $out[$loc] = $m[1];
        }

        return $out;
    }

    /** Üç dil kaydını gerçek gövdeyle kurar (000100 test DB'sinde de çalışmış olabilir → önce temizlenir). */
    private function seedGuides(): array
    {
        $m = $this->migration();
        DB::table('posts')->whereIn('slug', array_values($m::SLUGS))->delete();
        $ids = [];
        foreach ($this->bodies() as $loc => $md) {
            $ids[$loc] = DB::table('posts')->insertGetId([
                'locale' => $loc, 'type' => 'blog', 'translation_group_id' => self::GROUP, 'slug' => $m::SLUGS[$loc],
                'title' => "Studienkredit {$loc}", 'excerpt' => "Excerpt {$loc}", 'meta_title' => "Meta {$loc}",
                'meta_description' => "Meta description {$loc}", 'content_md' => $md, 'content_html' => '<p>old</p>',
                'is_published' => true, 'published_at' => '2026-09-27 12:00:00',
                'created_at' => '2026-09-27 12:00:00', 'updated_at' => '2026-09-27 12:00:00',
            ]);
        }

        return $ids;
    }

    private function snapshot(): array
    {
        return DB::table('posts')->orderBy('id')->get(['id', 'slug', 'title', 'excerpt', 'meta_title', 'meta_description', 'content_md', 'content_html', 'updated_at'])
            ->map(fn ($r) => (array) $r)->all();
    }

    public function test_real_guides_apply_with_parity_and_safe_wording(): void
    {
        $ids = $this->seedGuides();
        $before = collect($ids)->map(fn ($id) => (array) DB::table('posts')->find($id));
        $m = $this->migration();

        $this->assertSame('applied: tr 9, en 9, de 9', $m->run());

        $nums = [];
        foreach ($ids as $loc => $id) {
            $row = Post::findOrFail($id);
            $md = (string) $row->content_md;
            $html = strip_tags((string) $row->content_html);

            // Yeni oran + geçerlilik tarihi + değişken ifadesi (md ve render edilmiş HTML)
            [$nom, $eff] = $loc === 'en' ? ['6.20%', '6.38%'] : ($loc === 'tr' ? ['%6,20', '%6,38'] : ['6,20 %', '6,38 %']);
            foreach ([$nom, $eff, '01.10.2026'] as $needle) {
                $this->assertStringContainsString($needle, $md, "{$loc}: {$needle}");
                $this->assertStringContainsString($needle, $html, "{$loc} html: {$needle}");
            }
            $this->assertMatchesRegularExpression(['tr' => '/değişkendir/u', 'en' => '/is variable/', 'de' => '/ist variabel/'][$loc], $md);
            $this->assertMatchesRegularExpression(['tr' => '/1 Nisan ve 1 Ekim/u', 'en' => '/1 April and 1 October/', 'de' => '/1\. April und 1\. Oktober/'][$loc], $md);

            // 01.10 belirsizlik cümleleri kalktı
            $this->assertDoesNotMatchRegularExpression('/henüz doğrulanamadı|had not been verified|noch nicht bestätigt|Stand 26\.09\.2026 beträgt|As of 26\.09\.2026, the rate|26\.09\.2026 itibarıyla, 01\.04|KfW Studienkredit interest rate: Stand 26|Zinssatz KfW-Studienkredit – Stand: 26|KfW Studienkredit faizi — Stand: 26/u', $md);

            // Eski oran (6,34 / 6,53) yalnız "önceki dönem / 1 Nisan 2026" bağlamında geçebilir
            foreach (preg_split('/(?<=\D[.;!?])\s+|\n/u', $md) as $sentence) {
                if (preg_match('/(?<![\d.,])6[.,](34|53)(?!\d)/', $sentence)) {
                    $this->assertMatchesRegularExpression('/önceki|previous|vorherige|1 Nisan 2026|1 April 2026|1\. April 2026/u', $sentence, "{$loc}: eski oran güncel gibi: {$sentence}");
                }
            }

            // Arama niyeti
            foreach (['tr' => ['KfW Studienkredit', 'faiz', 'öğrenci kredisi'], 'en' => ['KfW Studienkredit', 'interest rate', 'student loan'],
                'de' => ['KfW-Studienkredit', 'Zinssatz', 'Studienkredit']][$loc] as $kw) {
                $this->assertStringContainsStringIgnoringCase($kw, $md, "{$loc}: anahtar kelime {$kw}");
            }

            // İlgisiz bloklar aynı: yeni metinleri eskisine geri çevirince orijinal gövde birebir çıkar
            $reverted = $md;
            foreach ($m::EDITS[$loc] as [$old, $new]) {
                $reverted = str_replace($new, $old, $reverted);
            }
            $this->assertSame($before[$loc]['content_md'], $reverted, "{$loc}: düzenleme dışı içerik değişti");

            // Bildungskredit (173): yeni oran kutu + tabloda, eski 3,57/3,53 hiç kalmadı
            [$bNom, $bEff] = $loc === 'en' ? ['4.14%', '4.09%'] : ($loc === 'tr' ? ['%4,14', '%4,09'] : ['4,14 %', '4,09 %']);
            $this->assertSame(2, substr_count($md, $bNom), "{$loc}: Bildungskredit nominal");
            $this->assertSame(2, substr_count($md, $bEff), "{$loc}: Bildungskredit efektif");
            $this->assertStringContainsString($bEff, $html);
            $this->assertDoesNotMatchRegularExpression('/(?<![\d.,])3[.,](57|53)(?!\d)/', $md, "{$loc}: eski Bildungskredit oranı kaldı");
            $this->assertDoesNotMatchRegularExpression('/Bildungskredit faizi — Stand: 26|Bildungskredit interest rate: Stand 26|Zinssatz Bildungskredit – Stand: 26/u', $md);

            // Diğer doğrulanmış olgular dokunulmadan duruyor
            $this->assertStringContainsString('16b', $md);
            $this->assertStringContainsString('§ 8 BAföG', $md);

            // Slug/başlık/meta değişmedi
            foreach (['slug', 'title', 'excerpt', 'meta_title', 'meta_description', 'translation_group_id'] as $f) {
                $this->assertSame($before[$loc][$f], $row->getRawOriginal($f), "{$loc}: {$f}");
            }

            preg_match_all('/(?<![\d.,])([346][.,]\d{2})(?=\s?%)|%([346],\d{2})(?!\d)/', $md, $mm);
            $nums[$loc] = collect(array_merge($mm[1], $mm[2]))->filter()->map(fn ($n) => str_replace(',', '.', $n))->unique()->sort()->values()->all();
        }
        $this->assertSame($nums['tr'], $nums['en'], 'TR/EN oran sayıları farklı');
        $this->assertSame($nums['tr'], $nums['de'], 'TR/DE oran sayıları farklı');
        $this->assertSame(['3.95', '4.09', '4.14', '6.04', '6.20', '6.34', '6.38', '6.53'], $nums['tr']);
    }

    public function test_pages_render_with_canonical_hreflang_and_valid_schema(): void
    {
        $this->seedGuides();
        $m = $this->migration();
        $m->run();

        foreach ($m::SLUGS as $loc => $slug) {
            $html = $this->get("/{$loc}/blog/{$slug}")->assertOk()->getContent();
            $this->assertStringContainsString(['tr' => '%6,38', 'en' => '6.38%', 'de' => '6,38 %'][$loc], $html);
            $this->assertMatchesRegularExpression('#<link rel="canonical" href="[^"]*/'.$loc.'/blog/'.preg_quote($slug, '#').'"#', $html);
            foreach ($m::SLUGS as $l2 => $s2) {
                $this->assertMatchesRegularExpression('#hreflang="'.$l2.'" href="[^"]*/'.$l2.'/blog/'.preg_quote($s2, '#').'"#', $html, "{$loc}: hreflang {$l2}");
            }
            preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $blocks);
            $this->assertNotEmpty($blocks[1]);
            foreach ($blocks[1] as $b) {
                json_decode($b, true, 512, JSON_THROW_ON_ERROR);   // geçerli JSON-LD
            }
        }
    }

    public function test_second_run_is_a_noop(): void
    {
        $this->seedGuides();
        $m = $this->migration();
        $m->run();
        $after = $this->snapshot();
        $this->assertSame('noop', $m->run());
        $this->assertSame($after, $this->snapshot());
    }

    public function test_partial_or_unexpected_state_fails_without_writes(): void
    {
        $ids = $this->seedGuides();
        $m = $this->migration();

        // EN'de tek bir düzenleme önceden uygulanmış → kısmi durum
        [$old, $new] = $m::EDITS['en'][3];
        DB::table('posts')->where('id', $ids['en'])->update(['content_md' => str_replace($old, $new, DB::table('posts')->where('id', $ids['en'])->value('content_md'))]);
        $before = $this->snapshot();
        try {
            $m->run();
            $this->fail('RuntimeException bekleniyordu');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('kısmen uygulanmış', $e->getMessage());
        }
        $this->assertSame($before, $this->snapshot());

        // Beklenmeyen içerik (DE eski blok iki kez) → hata, yazım yok
        $ids = $this->seedGuides();
        [$old] = $m::EDITS['de'][5];
        DB::table('posts')->where('id', $ids['de'])->update(['content_md' => DB::table('posts')->where('id', $ids['de'])->value('content_md')."\n\n".$old]);
        $before = $this->snapshot();
        try {
            $m->run();
            $this->fail('RuntimeException bekleniyordu');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('de #5', $e->getMessage());
        }
        $this->assertSame($before, $this->snapshot());

        // Kayıt eksik → hata
        DB::table('posts')->where('id', $ids['tr'])->delete();
        $this->expectException(RuntimeException::class);
        $m->run();
    }

    public function test_write_error_rolls_back(): void
    {
        $this->seedGuides();
        $before = $this->snapshot();
        Post::saving(function (Post $p) {
            if ($p->locale === 'de') {
                throw new RuntimeException('simulated write error');
            }
        });
        try {
            $this->migration()->run();
            $this->fail('RuntimeException bekleniyordu');
        } catch (RuntimeException $e) {
            $this->assertSame('simulated write error', $e->getMessage());
        }
        $this->assertSame($before, $this->snapshot());
    }
}
