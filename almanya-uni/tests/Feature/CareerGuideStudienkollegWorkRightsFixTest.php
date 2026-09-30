<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

/**
 * Kariyer rehberi — Studienkolleg / dil kursu çalışma bölümü (TR/EN/DE, 3 blok). Chatbot bu bölümden "çalışma kısıtlı,
 * çoğu durumda yalnız tatilde, bazen hiç izin yok" ve "Studienkolleg öğrencisi Werkstudent olamaz" çekiyordu.
 * Motor Batch 2C / A1 ile aynı (canlı blok metni → md satırı; diğer satırlara dokunulmaz).
 */
class CareerGuideStudienkollegWorkRightsFixTest extends TestCase
{
    use RefreshDatabase;

    private const MIGRATION = 'database/migrations/2026_09_30_000500_career_guide_studienkolleg_work_rights_fix.php';

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

    /** Üretimdeki bölümü (eski 3 blok) + ilgisiz komşu satırlar. */
    private function fixture(): void
    {
        foreach ($this->spec() as $r) {
            $old = array_column($r['md']['content_md'], 'line');
            $md = implode("\n\n", ['## Çalışma izni', '- Unrelated neighbour line (salary / tax).', '**'.$old[0].'**', $old[1], $old[2], 'Unrelated closing paragraph.']);
            DB::table('posts')->insert([
                'locale' => $r['locale'], 'translation_group_id' => '11111111-2222-4333-8444-555555555555', 'title' => "Guide {$r['locale']}",
                'slug' => $r['slug'], 'content_md' => $md, 'content_html' => '<p>old</p>', 'is_published' => true,
                'published_at' => '2026-03-01 10:00:00', 'created_at' => '2026-03-01 10:00:00', 'updated_at' => '2026-09-01 10:00:00',
            ]);
        }
    }

    private function snapshot(): array
    {
        return DB::table('posts')->orderBy('id')->get(['id', 'slug', 'title', 'content_md', 'content_html', 'updated_at', 'published_at'])->map(fn ($p) => (array) $p)->all();
    }

    public function test_spec_content_rules_in_all_three_locales(): void
    {
        $must = [
            'tr' => ['ilk yıl için genel bir çalışma yasağı yoktur', '140 iş günü', 'yarım gün', '2,5 iş günü', 'lehine olan hesap', 'genel bir oturum tavanı değildir', '§ 16f', 'haftada 20 saate kadar', 'sosyal sigorta'],
            'en' => ['no general work ban in the first year', '140 working days', 'half a day', '2.5 working days', 'more favourable', 'not a general residence-law ceiling', '§ 16f', 'up to 20 hours per week', 'social-insurance'],
            'de' => ['kein allgemeines Arbeitsverbot im ersten Jahr', '140 Arbeitstage', 'halber Tag', '2,5 Arbeitstage', 'günstigere Berechnung', 'keine allgemeine aufenthaltsrechtliche Obergrenze', '§ 16f', 'bis zu 20 Stunden pro Woche', 'Sozialversicherung'],
        ];
        $specs = $this->spec();
        $this->assertSame(['tr', 'en', 'de'], array_column($specs, 'locale'));
        foreach ($specs as $r) {
            $this->assertCount(3, $r['md']['content_md']);
            $new = implode("\n", array_column($r['md']['content_md'], 'replace'));
            $old = implode("\n", array_column($r['md']['content_md'], 'line'));
            foreach ($must[$r['locale']] as $s) {
                $this->assertStringContainsString($s, $new, "{$r['locale']}: missing «{$s}»");
            }
            // eski bölümde gerçekten olan, yeni bölümde olmaması gereken iddialar
            $this->assertMatchesRegularExpression('/Werkstudent/u', $old);
            $this->assertDoesNotMatchRegularExpression('/genellikle Werkstudent|generally can.?t work as a Werkstudent|in der Regel nicht als Werkstudent|sadece (sömestr )?tatil|only (work )?during (semester breaks|holiday)|nur in den (Semesterferien|Ferien)|hiç çalışma izni|not be allowed to work at all|überhaupt keine Arbeitserlaubnis|çalışma hakkı kısıtlı|restricted work rights|eingeschränkt|120\s*\/\s*240|iptal|cancell?ation|Aufhebung|ceza|penalt|Strafe/iu', $new);
            preg_match_all('/\]\((\/[^)\s]+)\)/', $new, $l);
            foreach ($l[1] as $u) {
                $this->assertStringStartsWith("/{$r['locale']}/blog/internship-in-germany-with-b1-b2-german", $u);
            }
        }
    }

    public function test_only_the_three_blocks_change_then_second_run_is_noop(): void
    {
        $this->fixture();
        $before = DB::table('posts')->pluck('content_md', 'slug')->all();
        $this->migrate();

        foreach ($this->spec() as $r) {
            $p = Post::where('slug', $r['slug'])->first();
            $old = explode("\n", $before[$r['slug']]);
            $now = explode("\n", $p->content_md);
            $this->assertCount(count($old), $now);
            $this->assertCount(3, array_diff_assoc($old, $now), "{$r['locale']}: exactly 3 lines should change");
            $this->assertContains('Unrelated closing paragraph.', $now);
            $this->assertContains('- Unrelated neighbour line (salary / tax).', $now);
            $this->assertSame("Guide {$r['locale']}", $p->title);
            $this->assertStringContainsString('§ 16f', $p->content_html);
        }

        $snap = $this->snapshot();
        $this->travel(1)->days();
        $this->migrate();
        $this->assertSame($snap, $this->snapshot());
    }

    public function test_mismatch_writes_nothing_and_write_error_rolls_back(): void
    {
        $this->fixture();
        DB::table('posts')->where('locale', 'en')->update(['content_md' => DB::raw("REPLACE(content_md, 'Studienkolleg', 'Prep college')")]);
        $snap = $this->snapshot();
        $this->migrate();                                   // ön kontrol eşleşmez → test ortamında sessiz çıkış, yazım yok
        $this->assertSame($snap, $this->snapshot());

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
