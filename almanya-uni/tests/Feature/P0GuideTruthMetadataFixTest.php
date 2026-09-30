<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

/**
 * P0 rehber düzeltmesi — SEO metadata (000700). 000600 gövdeyi düzeltti; eski framing title/meta_title/excerpt/
 * meta_description alanlarında (meta, og, twitter, JSON-LD description) kaldı. Eski değerler canlı sayfalardan birebir.
 */
class P0GuideTruthMetadataFixTest extends TestCase
{
    use RefreshDatabase;

    private const MIGRATION = 'database/migrations/2026_09_30_000700_p0_guide_truth_metadata_fix.php';

    private const STALE = '/Türk lise mezunlarının çoğu|most Turkish high school graduates|meisten türkischen Abiturienten|Sınıflandırılır|Classified|klassifiziert|yeterli olmadığını|isn\'t directly suffici|dass Ihr Abitur nicht|H\+[^.]{0,30}(direkt başvuru|direct admission|direkte[nr]? Zugang)|H\+ ?= ?HZB/iu';

    private const KEYWORDS = [
        'anabin' => [
            'tr' => ['Anabin', 'H+', 'H+/-', 'H-', 'Türk diploma', 'HZB', 'üniversite başvurusu', 'yükseköğretim kurumlarının statüsünü'],
            'en' => ['anabin', 'H+', 'H+/-', 'H-', 'Turkish diploma', 'HZB', 'university admission', 'higher-education institutions'],
            'de' => ['anabin', 'H+', 'H+/-', 'H-', 'türkischer Abschluss', 'Hochschulzugangsberechtigung', 'Hochschulzulassung', 'Status von Hochschulen'],
        ],
        'center' => [
            'tr' => ['Studienkolleg', 'devlet ve özel', 'giriş hakkı yolu', 'lise türüne değil'],
            'en' => ['Studienkolleg', 'Public and private', 'access route', 'not on your type of school'],
            'de' => ['Studienkolleg', 'Staatliche und private', 'Zugangsweg', 'nicht von der Schulart'],
        ],
        'guide' => [
            'tr' => ['Studienkolleg', 'giriş hakkı yoluna bağlı'],
            'en' => ['Studienkolleg', 'depends on your access route'],
            'de' => ['Studienkolleg', 'hängt von deinem Zugangsweg ab'],
        ],
    ];

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

    private function guide(string $slug): string
    {
        return match (true) {
            str_starts_with($slug, 'what-is-anabin') => 'anabin',
            str_starts_with($slug, 'studienkolleg-center') => 'center',
            default => 'guide',
        };
    }

    /** Üretimdeki metadata: eski title/meta_title/excerpt/meta_description; guide excerpt'i 000600 ile zaten yeni. */
    private function fixture(array $override = []): void
    {
        $groups = ['anabin' => 'aaaa0000-0000-4000-8000-00000000b001', 'center' => 'aaaa0000-0000-4000-8000-00000000b002', 'guide' => 'aaaa0000-0000-4000-8000-00000000b003'];
        foreach ($this->spec() as $r) {
            $g = $this->guide($r['slug']);
            $f = $r['fields'];
            $row = [
                'locale' => $r['locale'], 'translation_group_id' => $groups[$g], 'slug' => $r['slug'],
                'title' => $f['title']['old'] ?? "Unchanged title {$r['slug']}",
                'meta_title' => $f['meta_title']['old'] ?? "Unchanged title {$r['slug']}",
                'excerpt' => $f['excerpt']['old'] ?? "Already corrected excerpt {$r['locale']}.",
                'meta_description' => $f['meta_description']['old'],
                'content_md' => "# Body H1 {$r['locale']}\n\nBody paragraph that must not change.", 'content_html' => '<p>body html</p>',
                'is_published' => true, 'published_at' => '2026-01-23 10:00:00', 'created_at' => '2026-01-23 10:00:00', 'updated_at' => '2026-09-30 05:00:00',
            ];
            if ($g === 'guide') {
                $row['excerpt'] = $f['meta_description']['new'];   // 000600 excerpt'i zaten düzeltti
            }
            DB::table('posts')->insert(array_merge($row, $override[$r['slug']] ?? []));
        }
    }

    private function snapshot(): array
    {
        return DB::table('posts')->orderBy('id')->get()->map(fn ($p) => (array) $p)->all();
    }

    public function test_spec_targets_nine_posts_with_safe_seo_text(): void
    {
        $specs = $this->spec();
        $this->assertCount(9, $specs);
        $fields = [];
        foreach ($specs as $r) {
            $g = $this->guide($r['slug']);
            $fields[$g][$r['locale']] = array_keys($r['fields']);
            $new = implode("\n", array_column($r['fields'], 'new'));
            $old = implode("\n", array_column($r['fields'], 'old'));
            $this->assertMatchesRegularExpression(self::STALE, $old, "{$r['slug']}: fixture must carry the old framing");
            $this->assertDoesNotMatchRegularExpression(self::STALE, $new, "{$r['slug']}: stale framing in new metadata");
            $this->assertDoesNotMatchRegularExpression('/(lise|school|Schul)[^.]{0,30}\bH[+-]|\bH[+-][^.]{0,15}(lise|school certificate|Schulabschluss) (sınıf|classif|eingestuft)/iu', $new);
            foreach (self::KEYWORDS[$g][$r['locale']] as $k) {
                $this->assertStringContainsString($k, $new, "{$r['slug']}: missing «{$k}»");
            }
            if (isset($r['fields']['title'])) {
                $this->assertSame($r['fields']['title']['new'], $r['fields']['meta_title']['new']);
                $this->assertLessThanOrEqual(70, mb_strlen($r['fields']['title']['new']));
            }
            if (isset($r['fields']['excerpt'])) {
                $this->assertSame($r['fields']['excerpt']['new'], $r['fields']['meta_description']['new']);
            }
            foreach ($r['fields'] as $f) {
                $this->assertLessThanOrEqual(260, mb_strlen($f['new']));
            }
            if ($r['locale'] !== 'tr') {
                $this->assertDoesNotMatchRegularExpression('/\b(için|değil|nasıl|başvuru|yerleşme|olarak)\b/u', $new);
            }
        }
        $this->assertSame(['title', 'meta_title', 'excerpt', 'meta_description'], $fields['anabin']['de']);
        $this->assertSame(['excerpt', 'meta_description'], $fields['center']['en']);
        $this->assertSame(['meta_description'], $fields['guide']['tr']);
        foreach ($fields as $g => $byLocale) {
            $this->assertSame($byLocale['tr'], $byLocale['en'], "{$g}: TR/EN field parity");
            $this->assertSame($byLocale['tr'], $byLocale['de'], "{$g}: TR/DE field parity");
        }
    }

    public function test_only_metadata_changes_then_second_run_is_noop(): void
    {
        $this->fixture();
        $before = DB::table('posts')->get()->keyBy('slug');
        $this->migrate();

        foreach ($this->spec() as $r) {
            $p = Post::where('slug', $r['slug'])->first();
            $b = $before[$r['slug']];
            foreach (['slug', 'locale', 'translation_group_id', 'content_md', 'content_html', 'is_published'] as $keep) {
                $this->assertSame($b->{$keep}, DB::table('posts')->where('id', $p->id)->value($keep), "{$r['slug']}: {$keep} changed");
            }
            $this->assertSame('2026-01-23 10:00:00', (string) $p->published_at);
            foreach (['title', 'meta_title', 'excerpt', 'meta_description'] as $col) {
                $expected = $r['fields'][$col]['new'] ?? $b->{$col};
                $this->assertSame($expected, $p->{$col}, "{$r['slug']}: {$col}");
            }
            $this->assertDoesNotMatchRegularExpression(self::STALE, $p->metaTitleResolved().' '.$p->metaDescriptionResolved().' '.$p->title.' '.$p->excerpt);
            if ($this->guide($r['slug']) === 'guide') {
                $this->assertSame($p->excerpt, $p->meta_description);
                $this->assertSame($b->title, $p->title);
            }
        }

        $snap = $this->snapshot();
        $this->travel(1)->days();
        $this->migrate();
        $this->assertSame($snap, $this->snapshot());
    }

    public function test_empty_optional_fields_are_left_empty(): void
    {
        $slug = 'studienkolleg-center-list-2026-public-private-institutions-de';
        $anabin = 'what-is-anabin-h-h-h-how-is-a-turkish-diploma';
        $this->fixture([$slug => ['meta_description' => null], $anabin => ['meta_title' => null]]);
        $this->migrate();

        $center = Post::where('slug', $slug)->first();
        $this->assertNull($center->meta_description);
        $this->assertStringContainsString('Zugangsweg', $center->metaDescriptionResolved());
        $a = Post::where('slug', $anabin)->first();
        $this->assertNull($a->meta_title);
        $this->assertStringStartsWith("Anabin H+, H+/- ve H- Nedir?", $a->metaTitleResolved());
    }

    public function test_mismatch_and_partial_state_fail_without_writes_and_write_error_rolls_back(): void
    {
        // üretimde alan farklıysa → FAIL, yazım yok
        $this->fixture(['studienkolleg-guide-2026-who-needs-it-which-course-which-school-en' => ['meta_description' => 'Edited in the admin panel.']]);
        $snap = $this->snapshot();
        try {
            $this->migrate();
            $this->fail('RuntimeException bekleniyordu');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('meta_description eşleşmedi', $e->getMessage());
        }
        $this->assertSame($snap, $this->snapshot());

        // kısmi durum: 9 yazıdan biri eski halinde → FAIL, yazım yok
        DB::table('posts')->delete();
        $this->fixture();
        $old = (array) DB::table('posts')->where('slug', 'what-is-anabin-h-h-h-how-is-a-turkish-diploma-en')->first(['title', 'meta_title', 'excerpt', 'meta_description']);
        $this->migrate();
        DB::table('posts')->where('slug', 'what-is-anabin-h-h-h-how-is-a-turkish-diploma-en')->update($old);
        $snap = $this->snapshot();
        try {
            $this->migrate();
            $this->fail('RuntimeException bekleniyordu');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('kısmi durum', $e->getMessage());
        }
        $this->assertSame($snap, $this->snapshot());

        // kayıt içi kısmi durum (title yeni, excerpt eski) → FAIL
        DB::table('posts')->delete();
        $this->fixture(['what-is-anabin-h-h-h-how-is-a-turkish-diploma-de' => ['title' => $this->spec()[2]['fields']['title']['new']]]);
        $snap = $this->snapshot();
        try {
            $this->migrate();
            $this->fail('RuntimeException bekleniyordu');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('kısmen uygulanmış', $e->getMessage());
        }
        $this->assertSame($snap, $this->snapshot());

        // eksik yazı (9'dan 1'i yok) → FAIL
        DB::table('posts')->delete();
        $this->fixture();
        DB::table('posts')->where('slug', 'studienkolleg-center-list-2026-public-private-institutions-en')->delete();
        $snap = $this->snapshot();
        try {
            $this->migrate();
            $this->fail('RuntimeException bekleniyordu');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('kayıt sayısı 0', $e->getMessage());
        }
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
