<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

/**
 * Wave 2 ek P0 blog kaynağı — studienkolleg-your-bridge-to-university-in-germany-who-needs-it-how (TR/EN/DE).
 * Yalnız F1/F4/F5 ile çelişen bölümler: "kimler Studienkolleg'e gitmeli" listesi, "çoğu Türk lise mezunu" özeti /
 * giriş / sonuç, YKS maddesi, FSP → "tüm üniversiteler", T-Kurs'a biyoloji, uni-assist posta cevabı; excerpt (+ EN/DE meta).
 * Motor 2026_09_30_000600 ile aynı (orada ayrıntılı test edildi); burada içerik kuralları + temel akış sabitlenir.
 */
class FaqWave2BlogStudienkollegBridgeFixTest extends TestCase
{
    use RefreshDatabase;

    private const MIGRATION = 'database/migrations/2026_09_30_000810_faq_wave2_blog_studienkolleg_bridge_fix.php';

    private const STALE = '/çoğu öğrencinin yolu|most high school graduates from Turkey|Für die meisten Abiturienten|Hiç Yerleşememiş|not placed at all|gar keinen Platz|yerleşmiş olmak yeterli|is considered sufficient|als ausreichend|tüm üniversitelere|at any university|an allen Universitäten|posta yoluyla gönderilmesini|via postal mail|per Post anfordern|kaçınılmaz|unavoidable|unvermeidlich|\(fizik, kimya, biyoloji\)|\(physics, chemistry, biology\)|\(Physik, Chemie, Biologie\)|For many Turkish high school graduates|Für viele türkische Abiturienten|Türk lise mezunları için bu hayalin/u';

    private function spec(): array
    {
        $src = file_get_contents(base_path(self::MIGRATION));
        preg_match("/<<<'JSON'\n(.*?)\nJSON/s", $src, $m);

        return json_decode($m[1], true)['records'];
    }

    private function migrate(): void
    {
        (require base_path(self::MIGRATION))->up();
    }

    /** Üretimdeki eski bloklar (spec 'line') + ilgisiz komşular; excerpt / meta_description = üretimdeki eski değerler. */
    private function fixture(array $override = []): void
    {
        foreach ($this->spec() as $r) {
            $md = ['## Unrelated heading', 'Unrelated opening paragraph.'];
            foreach ($r['md']['content_md'] as $e) {
                $md[] = $e['line'];
            }
            $md[] = '*   **M-Kurs (Medizinischer Kurs):** unrelated list item.';
            $old = $r['fieldsub']['excerpt'][0]['old'];
            DB::table('posts')->insert(array_merge([
                'locale' => $r['locale'], 'translation_group_id' => '11111111-2222-4333-8444-0000000000b5', 'title' => "Bridge {$r['locale']}",
                'slug' => $r['slug'], 'excerpt' => "Intro. {$old} Rest.", 'meta_description' => $r['locale'] === 'tr' ? 'TR meta unchanged.' : "Intro. {$old} Rest.",
                'content_md' => implode("\n\n", $md), 'content_html' => '<p>old</p>', 'is_published' => true,
                'published_at' => '2026-01-10 10:00:00', 'created_at' => '2026-01-10 10:00:00', 'updated_at' => '2026-09-26 11:24:00',
            ], $override[$r['locale']] ?? []));
        }
    }

    private function snapshot(): array
    {
        return DB::table('posts')->orderBy('id')->get(['id', 'slug', 'locale', 'translation_group_id', 'title', 'excerpt', 'meta_description', 'content_md', 'content_html', 'updated_at', 'published_at'])->map(fn ($p) => (array) $p)->all();
    }

    public function test_spec_content_rules_in_three_locales(): void
    {
        $must = [
            'tr' => ['giriş hakkı yoluna', '180 puanın üzerinde', 'genel bir yol tanımlamıyor', 'yalnızca Fachhochschule', 'tamamen dijitaldir', 'biyoloji hariç', 'herkes için zorunlu değildir'],
            'en' => ['access route', 'more than 180 points', 'define no general route', 'Fachhochschulen only', 'fully digital', 'not biology', 'not mandatory for everyone'],
            'de' => ['Zugangsweg', 'mehr als 180 Punkte', 'keinen allgemeinen Weg', 'nur zu Fachhochschulen', 'vollständig digital', 'ohne Biologie', 'nicht für alle Pflicht'],
        ];
        $specs = $this->spec();
        $this->assertSame(['tr', 'en', 'de'], array_column($specs, 'locale'));
        $deletes = [];
        foreach ($specs as $r) {
            $l = $r['locale'];
            $new = implode("\n", array_filter(array_column($r['md']['content_md'], 'replace')))
                .' '.implode(' ', array_column(array_merge(...array_column(array_filter($r['md']['content_md'], fn ($e) => isset($e['subs'])), 'subs')), 'new'))
                .' '.$r['fieldsub']['excerpt'][0]['new'];
            foreach ($must[$l] as $s) {
                $this->assertStringContainsString($s, $new, "{$l}: «{$s}» eksik");
            }
            $this->assertDoesNotMatchRegularExpression(self::STALE, $new);
            $this->assertMatchesRegularExpression(self::STALE, implode("\n", array_column($r['md']['content_md'], 'line')), "{$l}: fixture eski iddiayı taşımalı");
            preg_match_all('/\]\((\/[^)\s]+)\)/', $new, $links);
            foreach ($links[1] as $u) {
                $this->assertStringStartsWith("/{$l}/blog/what-is-anabin-h-h-h-how-is-a-turkish-diploma", $u);
            }
            preg_match_all('/(?<![\d.,])(\d{3})(?!\d)/', $new, $n);
            $nums[$l] = array_values(array_unique($n[1]));
            sort($nums[$l]);
            $deletes[$l] = count(array_filter($r['md']['content_md'], fn ($e) => ! empty($e['delete'])));
            if ($l !== 'tr') {
                $this->assertDoesNotMatchRegularExpression('/\b(için|değil|yerleşme|olarak|ancak)\b/u', $new);
                $this->assertTrue($r['fieldsub']['meta_description'][0]['null_ok']);
            }
        }
        $this->assertSame($nums['tr'], $nums['en']);
        $this->assertSame($nums['tr'], $nums['de']);
        $this->assertSame(['tr' => 5, 'en' => 5, 'de' => 5], $deletes);
    }

    public function test_only_targets_change_then_second_run_is_noop(): void
    {
        $this->fixture();
        $before = DB::table('posts')->get()->keyBy('slug');
        $this->migrate();

        foreach ($this->spec() as $r) {
            $p = Post::where('slug', $r['slug'])->first();
            $b = $before[$r['slug']];
            foreach (['title', 'translation_group_id', 'locale'] as $k) {
                $this->assertSame($b->{$k}, $p->{$k});
            }
            $this->assertSame('2026-01-10 10:00:00', (string) $p->published_at);
            $lines = explode("\n", $p->content_md);
            foreach (['## Unrelated heading', 'Unrelated opening paragraph.', '*   **M-Kurs (Medizinischer Kurs):** unrelated list item.'] as $keep) {
                $this->assertContains($keep, $lines);
            }
            foreach ($r['md']['content_md'] as $e) {
                if (! empty($e['delete'])) {
                    $this->assertNotContains($e['line'], $lines);
                }
            }
            $this->assertDoesNotMatchRegularExpression(self::STALE, $p->content_md.' '.html_entity_decode(strip_tags($p->content_html)).' '.$p->excerpt.' '.$p->meta_description);
            $this->assertStringContainsString($r['fieldsub']['excerpt'][0]['new'], $p->excerpt);
            $r['locale'] === 'tr' ? $this->assertSame('TR meta unchanged.', $p->meta_description) : $this->assertSame($p->excerpt, $p->meta_description);
        }

        $snap = $this->snapshot();
        $this->travel(1)->days();
        $this->migrate();
        $this->assertSame($snap, $this->snapshot());
    }

    public function test_empty_meta_description_is_left_empty(): void
    {
        $this->fixture(['en' => ['meta_description' => null]]);
        $this->migrate();
        $p = Post::where('slug', 'studienkolleg-your-bridge-to-university-in-germany-who-needs-it-how-en')->first();
        $this->assertNull($p->meta_description);
        $this->assertStringContainsString('access route', $p->metaDescriptionResolved());
    }

    public function test_mismatch_and_partial_state_write_nothing_and_write_error_rolls_back(): void
    {
        // üretimde hedef blok değişmiş → ön kontrol eşleşmez; test ortamında sessiz çıkış, yazım yok
        $this->fixture();
        DB::table('posts')->where('slug', 'like', 'studienkolleg-your-bridge%')->where('locale', 'en')->update(['content_md' => DB::raw("REPLACE(content_md, 'at any university in Germany', 'at a university in Germany')")]);
        $snap = $this->snapshot();
        $this->migrate();
        $this->assertSame($snap, $this->snapshot());

        // kısmi durum: bir yazı eski halinde → hiçbirine yazılmaz
        DB::table('posts')->delete();
        $this->fixture();
        $oldDe = (array) DB::table('posts')->where('slug', 'like', 'studienkolleg-your-bridge%')->where('locale', 'de')->first(['content_md', 'excerpt', 'meta_description']);
        $this->migrate();
        DB::table('posts')->where('slug', 'like', 'studienkolleg-your-bridge%')->where('locale', 'de')->update($oldDe);
        $snap = $this->snapshot();
        $this->migrate();
        $this->assertSame($snap, $this->snapshot());

        // yazım hatası → tek transaction geri alınır
        DB::table('posts')->delete();
        $this->fixture();
        $snap = $this->snapshot();
        $n = 0;
        Post::saving(function () use (&$n) {
            if (++$n === 2) {
                throw new RuntimeException('simulated write error');
            }
        });
        try {
            $this->migrate();
            $this->fail('RuntimeException bekleniyordu');
        } catch (RuntimeException $e) {
            $this->assertSame('simulated write error', $e->getMessage());
        }
        $this->assertSame($snap, $this->snapshot());
    }
}
