<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

/**
 * P0 rehber düzeltmesi — anabin H+/H+-/H- rehberi, Studienkolleg merkez listesi, Studienkolleg rehberi (TR/EN/DE, 9 yazı).
 * Eski içerik H kodlarını lise diploması sınıfı sanıyordu (H+ = doğrudan giriş, H- = Studienkolleg zorunlu) ve
 * "çoğu Türk lise mezunu Studienkolleg'e gitmeli" diyordu. Doğrusu: H kodları kurum statüsüdür; okul diploması
 * anabin'in Türkiye okul kurallarıyla (ÖSYM sonucu + yerleşme) değerlendirilir. Motor Batch 2C / A1 ile aynı.
 */
class P0GuideTruthFixTest extends TestCase
{
    use RefreshDatabase;

    private const MIGRATION = 'database/migrations/2026_09_30_000600_p0_guide_truth_fix_anabin_studienkolleg.php';

    private const STALE = '/H\+[^.\n]{0,40}(direkt başvuru hakkı|Direct Admission Right|Direkte Hochschulzugangsberechtigung)|H-[^.\n]{0,40}(Studienkolleg Zorunlulu|Studienkolleg Requirement|Studienkolleg-Pflicht)|Çoğu Türk (lise mezunu|öğrencisi)|Türk lise mezunlarının çoğu|most Turkish (high school graduates|students)|meisten türkischen Abiturienten|Lise \(genel\/Anadolu\/fen\)|YKS 450|200-250|H\+ olarak (sınıflandır|listelen)|listed as H\+|als H\+ aufgeführt/iu';

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

    private function group(string $slug): string
    {
        return match (true) {
            str_starts_with($slug, 'what-is-anabin') => '11111111-2222-4333-8444-00000000a001',
            str_starts_with($slug, 'studienkolleg-center') => '11111111-2222-4333-8444-00000000a002',
            default => '11111111-2222-4333-8444-00000000a003',
        };
    }

    /** Üretimdeki eski bloklar (spec 'line') + ilgisiz komşu satırlar; excerpt = üretimdeki eski excerpt. */
    private function fixture(): void
    {
        foreach ($this->spec() as $r) {
            $md = ['## Unrelated heading', 'Unrelated opening paragraph.'];
            foreach ($r['md']['content_md'] as $e) {
                $md[] = $e['line'];
            }
            $md[] = '- Unrelated closing list item.';
            DB::table('posts')->insert([
                'locale' => $r['locale'], 'translation_group_id' => $this->group($r['slug']), 'title' => "Guide {$r['slug']}",
                'slug' => $r['slug'], 'excerpt' => $r['fieldsub']['excerpt'][0]['old'] ?? 'Unrelated excerpt.',
                'content_md' => implode("\n\n", $md), 'content_html' => '<p>old</p>', 'is_published' => true,
                'published_at' => '2026-03-01 10:00:00', 'created_at' => '2026-03-01 10:00:00', 'updated_at' => '2026-09-01 10:00:00',
            ]);
        }
    }

    private function snapshot(): array
    {
        return DB::table('posts')->orderBy('id')->get(['id', 'slug', 'locale', 'translation_group_id', 'title', 'excerpt', 'content_md', 'content_html', 'updated_at', 'published_at'])->map(fn ($p) => (array) $p)->all();
    }

    private function newText(array $r): string
    {
        return implode("\n", array_filter(array_column($r['md']['content_md'], 'replace')))
            ."\n".implode("\n", array_column($r['fieldsub']['excerpt'] ?? [], 'new'));
    }

    public function test_spec_covers_nine_posts_with_locale_parity(): void
    {
        $specs = $this->spec();
        $this->assertCount(9, $specs);
        $groups = [];
        foreach ($specs as $r) {
            $base = preg_replace('/-(en|de)$/', '', $r['slug']);
            $groups[$base][$r['locale']] = $r;
            $this->assertSame($r['locale'] === 'tr' ? $base : "{$base}-{$r['locale']}", $r['slug']);
        }
        $this->assertSame(['what-is-anabin-h-h-h-how-is-a-turkish-diploma', 'studienkolleg-center-list-2026-public-private-institutions', 'studienkolleg-guide-2026-who-needs-it-which-course-which-school'], array_keys($groups));
        foreach ($groups as $base => $g) {
            $this->assertSame(['tr', 'en', 'de'], array_keys($g));
            // aynı rakamlar üç dilde (180 / 170 / 150 eşikleri, tarih)
            $nums = array_map(fn ($r) => array_values(array_unique(preg_match_all('/\b(180|170|150|2020)\b/', $this->newText($r), $m) ? $m[1] : [])), $g);
            foreach ($nums as &$n) {
                sort($n);
            }
            $this->assertSame($nums['tr'], $nums['en'], "{$base}: TR/EN numbers differ");
            $this->assertSame($nums['tr'], $nums['de'], "{$base}: TR/DE numbers differ");
            // aynı sayıda silme her dilde
            $del = array_map(fn ($r) => count(array_filter($r['md']['content_md'], fn ($e) => ! empty($e['delete']))), $g);
            $this->assertSame($del['tr'], $del['en'], "{$base}: delete count TR/EN");
            $this->assertSame($del['tr'], $del['de'], "{$base}: delete count TR/DE");
        }
    }

    public function test_new_texts_state_h_codes_are_institution_statuses_and_no_blanket_studienkolleg_rule(): void
    {
        $inst = ['tr' => 'yükseköğretim kurumlarını', 'en' => 'higher-education institutions', 'de' => 'Hochschulen'];
        $notHzb = [
            'tr' => ['tek başına bir **üniversiteye giriş hakkı (HZB)** değildir', '**otomatik olarak tanıtmaz**', '**otomatik kabul**'],
            'en' => ['**university entrance qualification (HZB)**', '**not automatically recognise**', '**automatic admission**'],
            'de' => ['**keine** Hochschulzugangsberechtigung (HZB)', '**keine automatische Anerkennung**', '**keine automatische Zulassung**'],
        ];
        $route = ['tr' => 'giriş hakkı yolu', 'en' => 'access route', 'de' => 'Zugangsweg'];
        foreach ($this->spec() as $r) {
            $l = $r['locale'];
            $new = $this->newText($r);
            $old = implode("\n", array_column($r['md']['content_md'], 'line'));
            $this->assertDoesNotMatchRegularExpression(self::STALE, $new, "{$r['slug']}: stale P0 claim in new text");
            $this->assertDoesNotMatchRegularExpression('/H\+[^.\n]{0,30}(school|okul|Schul)[^.\n]{0,20}(class|sınıf|eingestuft)|H-\s*(=|:)?\s*(Studienkolleg|SK)\b/iu', $new);
            if (str_starts_with($r['slug'], 'what-is-anabin')) {
                $this->assertMatchesRegularExpression('/H\+ Nedir: Direkt|H\+.{0,20}Direct Admission|H\+.{0,40}Hochschulzugangsberechtigung/iu', $old, 'fixture must carry the old claim');
                $this->assertStringContainsString($inst[$l], $new);
                foreach ($notHzb[$l] as $s) {
                    $this->assertStringContainsString($s, $new, "{$r['slug']}: missing «{$s}»");
                }
                $this->assertStringContainsString('Schulabschlüsse mit Hochschulzugang', $new);
            } else {
                $this->assertStringContainsString($route[$l], $new, "{$r['slug']}: access-route framing missing");
                $this->assertMatchesRegularExpression('/\b180\b/', $new);
            }
            // iç linkler aynı dilde kalır
            preg_match_all('/\]\((\/[^)\s]+)\)/', $new, $links);
            foreach ($links[1] as $u) {
                $this->assertStringStartsWith("/{$l}/blog/", $u);
            }
            // EN/DE metinlerine Türkçe cümle sızmaz (Türkçe özel adlar hariç)
            if ($l !== 'tr') {
                $this->assertDoesNotMatchRegularExpression('/\b(için|değil|nasıl|başvuru|yerleşme|olarak|ayrıca)\b/u', preg_replace('/\]\([^)]*\)/', '', $new));
            }
        }
    }

    public function test_only_targets_change_then_second_run_is_noop(): void
    {
        $this->fixture();
        $before = DB::table('posts')->get()->keyBy('slug');
        $this->migrate();

        foreach ($this->spec() as $r) {
            $p = Post::where('slug', $r['slug'])->first();
            $b = $before[$r['slug']];
            $this->assertSame($b->title, $p->title);
            $this->assertSame($b->translation_group_id, $p->translation_group_id);
            $this->assertSame($b->locale, $p->locale);
            $this->assertSame('2026-03-01 10:00:00', (string) $p->published_at);
            foreach (['## Unrelated heading', 'Unrelated opening paragraph.', '- Unrelated closing list item.'] as $keep) {
                $this->assertContains($keep, explode("\n", $p->content_md), "{$r['slug']}: neighbour line lost");
            }
            foreach ($r['md']['content_md'] as $e) {
                if (! empty($e['delete'])) {
                    $this->assertNotContains($e['line'], explode("\n", $p->content_md), "{$r['slug']}: deleted line still present");
                } elseif ($e['replace'] !== $e['line']) {
                    $this->assertStringContainsString(strtok($e['replace'], "\n"), $p->content_md);
                }
            }
            if (isset($r['fieldsub'])) {
                $this->assertSame($r['fieldsub']['excerpt'][0]['new'], $p->excerpt);
            } else {
                $this->assertSame('Unrelated excerpt.', $p->excerpt);
            }
            $this->assertDoesNotMatchRegularExpression(self::STALE, html_entity_decode(strip_tags($p->content_html)));
            $this->assertDoesNotMatchRegularExpression(self::STALE, $p->excerpt);
        }

        $snap = $this->snapshot();
        $this->travel(1)->days();
        $this->migrate();
        $this->assertSame($snap, $this->snapshot());
    }

    public function test_mismatch_partial_state_and_write_error_write_nothing(): void
    {
        // üretimde hedef blok değişmiş → ön kontrol eşleşmez; test ortamında sessiz çıkış, yazım yok
        $this->fixture();
        $en = $this->spec()[1];
        $target = collect($en['md']['content_md'])->first(fn ($e) => isset($e['replace']))['line'];
        DB::table('posts')->where('slug', $en['slug'])->update(['content_md' => str_replace($target, 'Changed on production.', DB::table('posts')->where('slug', $en['slug'])->value('content_md'))]);
        $snap = $this->snapshot();
        $this->migrate();
        $this->assertSame($snap, $this->snapshot());

        // kısmi durum: 9 yazıdan biri eski halinde → hiçbirine yazılmaz
        DB::table('posts')->delete();
        $this->fixture();
        $oldDe = DB::table('posts')->where('slug', 'studienkolleg-center-list-2026-public-private-institutions-de')->value('content_md');
        $this->migrate();
        DB::table('posts')->where('slug', 'studienkolleg-center-list-2026-public-private-institutions-de')->update(['content_md' => $oldDe]);
        $snap = $this->snapshot();
        $this->migrate();
        $this->assertSame($snap, $this->snapshot());

        // yazım hatası → tek transaction geri alınır
        DB::table('posts')->delete();
        $this->fixture();
        $snap = $this->snapshot();
        $n = 0;
        Post::saving(function () use (&$n) {
            if (++$n === 5) {
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
