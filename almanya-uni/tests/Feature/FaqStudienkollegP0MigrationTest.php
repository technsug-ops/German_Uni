<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\FaqQualityAtlas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\TestCase;

/**
 * Acil P0 temizliği (Batch C'den öne çekildi): studienkolleg-icin-vize-basvurusu-ozel-mi TR/EN/DE.
 * Chatbot bu SSS'den "20 saat/hafta" (oturum tavanı gibi) ve dil kursu için "çalışma hakkı genelde yok" çekiyordu.
 * Motor Batch A/B ile aynı; burada gerçek spec'in içerik kuralları + temel akış sabitlenir.
 */
class FaqStudienkollegP0MigrationTest extends TestCase
{
    use RefreshDatabase;

    private const MIGRATION = 'database/migrations/2026_09_30_000400_faq_studienkolleg_p0_pullforward.php';

    private const SLUG = 'studienkolleg-icin-vize-basvurusu-ozel-mi';

    private function migration(): object
    {
        return require base_path(self::MIGRATION);
    }

    private function spec(): array
    {
        $m = $this->migration();

        return json_decode(str_replace("\r\n", "\n", file_get_contents(base_path($m::SPEC))), true);
    }

    /** Üretimdeki eski durumu kurar (soru + görünen gövde başı; EN/DE görünmez gizli gövde). */
    private function fixture(): void
    {
        $topic = DB::table('faq_topics')->insertGetId(['name' => 'Studienkolleg', 'slug' => 'studienkolleg', 'created_at' => now(), 'updated_at' => now()]);
        $g = (string) Str::uuid();
        foreach ($this->spec()['clusters'][0]['records'] as $r) {
            $old = $r['old'];
            $html = $old['visible'] === 'empty' ? null
                : '<p>'.e($old['head']).' Çalışma hakkı: 20 saat/hafta çalışabilirsin. Dil kursu: Genelde yok.</p>';
            DB::table('faqs')->insert([
                'locale' => $r['locale'], 'translation_group_id' => $g, 'faq_topic_id' => $topic, 'question' => $old['question'],
                'slug' => $r['slug'], 'answer_md' => 'Eski gizli gövde: 20 saat/hafta çalışabilirsin; dil kursu çalışma hakkı genelde yok.',
                'answer_html' => $html, 'has_answer' => (bool) $html, 'is_published' => true, 'intent' => 'bilgi', 'sort_order' => 1,
                'created_at' => '2026-09-01 10:00:00', 'updated_at' => '2026-09-01 10:00:00',
            ]);
        }
        FaqQualityAtlas::create([
            'audit_label' => '2026-09-28', 'translation_group_id' => $g, 'tr_slug' => self::SLUG, 'tr_question_at_audit' => 'Soru',
            'topic' => 'Studienkolleg', 'risk_level' => 'P0', 'tr_quality' => 'D', 'tr_status' => 'COMPLETE', 'en_status' => 'EMPTY',
            'de_status' => 'EMPTY', 'tr_issues' => [], 'external_source_needed' => true, 'translation_strategy' => 'RESEARCH_FIRST',
            'parity_score' => 1, 'quality_ready_locales' => [], 'recommended_action' => 'Fix.', 'proposed_batch' => 'C',
            'priority_score' => 300, 'duplicate_of' => [], 'junk' => false, 'chatbot_risk' => true,
            'audited_at' => '2026-09-28 20:00:00', 'resolved' => true,
        ]);
    }

    private function snapshot(): array
    {
        return DB::table('faqs')->orderBy('id')->get()->map(fn ($f) => (array) $f)->all();
    }

    public function test_real_spec_integrity_and_content_rules(): void
    {
        $m = $this->migration();
        $raw = str_replace("\r\n", "\n", file_get_contents(base_path($m::SPEC)));
        $this->assertSame($m::SPEC_SHA256, hash('sha256', $raw));
        $spec = json_decode($raw, true);
        $this->assertSame('C', $spec['proposed_batch']);
        $this->assertCount(1, $spec['clusters']);
        $this->assertSame(self::SLUG, $spec['clusters'][0]['tr_slug']);
        $this->assertSame(['tr', 'en', 'de'], array_column($spec['clusters'][0]['records'], 'locale'));

        $must = [
            'tr' => ['140 iş günü', '§ 16f', 'haftada 20 saate kadar', 'sosyal sigorta', 'Ausländerbehörde'],
            'en' => ['140 working days', '§ 16f', 'up to 20 hours per week', 'social-insurance', 'Ausländerbehörde'],
            'de' => ['140 Arbeitstage', '§ 16f', 'bis zu 20 Stunden pro Woche', 'Sozialversicherung', 'Ausländerbehörde'],
        ];
        foreach ($spec['clusters'][0]['records'] as $r) {
            $md = $r['new']['answer_md'];
            foreach ($must[$r['locale']] as $s) {
                $this->assertStringContainsString($s, $md, "{$r['locale']}: missing «{$s}»");
            }
            // 20 saat yalnız (a) 2,5 gün sayımı, (b) § 16f, (c) Werkstudent sosyal sigorta ve (d) "genel tavan değildir" bağlamında
            $this->assertDoesNotMatchRegularExpression('/20\s?saat\/hafta çalışabilirsin|20 hours per week as a Studienkolleg|max(imal|imum)? 20 (Stunden|hours)/iu', $md);
            $this->assertMatchesRegularExpression('/(genel bir oturum tavanı değildir|not a general residence-law ceiling|keine allgemeine aufenthaltsrechtliche Obergrenze)/u', $md);
            // eski/desteksiz iddialar
            $this->assertDoesNotMatchRegularExpression('/Genelde yok|generally (no|not)|B2 sertifika|30K|otomatik uzatma|Maximum 2|1\+1|17,856|23,808|6-12 hafta|120\s*\/\s*240|iptal|cancell?ation|Aufhebung|ceza|penalt|Strafe|Werkstudent olarak (genellikle )?çalışamaz/u', $md);
            if ($r['locale'] !== 'tr') {
                $this->assertFalse(Faq::looksTurkish($r['new']['question'].' '.$md), "{$r['locale']}: Türkçe sızıntı");
            }
            preg_match_all('/\]\((\/[^)\s]+)\)/', $md, $l);
            $this->assertSame(["/{$r['locale']}/blog/internship-in-germany-with-b1-b2-german".($r['locale'] === 'tr' ? '' : "-{$r['locale']}")], $l[1]);
        }
    }

    public function test_apply_replaces_all_three_bodies_then_second_run_is_noop(): void
    {
        $this->fixture();
        $count = Faq::count();
        $this->assertStringStartsWith('applied: TR 1, EN/DE güncellenen 2, EN/DE oluşturulan 0', $this->migration()->run($this->spec()));
        $this->assertSame($count, Faq::count());
        foreach (Faq::where('slug', 'like', self::SLUG.'%')->get() as $f) {
            $this->assertStringNotContainsString('20 saat/hafta çalışabilirsin', $f->answer_md);
            $this->assertStringNotContainsString('genelde yok', $f->answer_md);
            $this->assertTrue($f->has_answer);          // EN/DE gövdesi artık görünür (render edildi)
            $this->assertStringContainsString('§ 16f', (string) $f->answer_html);
        }
        $before = $this->snapshot();
        $this->travel(1)->days();
        $this->assertSame('noop', $this->migration()->run($this->spec()));
        $this->assertSame($before, $this->snapshot());
    }

    public function test_unexpected_old_state_and_rollback_leave_everything_untouched(): void
    {
        $this->fixture();
        DB::table('faqs')->where('slug', self::SLUG.'-de')->update(['question' => 'Anders?']);
        $before = $this->snapshot();
        try {
            $this->migration()->run($this->spec());
            $this->fail('RuntimeException bekleniyordu');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('beklenmeyen eski durum', $e->getMessage());
        }
        $this->assertSame($before, $this->snapshot());

        DB::table('faqs')->where('slug', self::SLUG.'-de')->update(['question' => $this->spec()['clusters'][0]['records'][2]['old']['question']]);
        $before = $this->snapshot();
        $n = 0;
        Faq::saving(function () use (&$n) {
            if (++$n === 3) {
                throw new RuntimeException('simulated write error');
            }
        });
        try {
            $this->migration()->run($this->spec());
            $this->fail('RuntimeException bekleniyordu');
        } catch (RuntimeException $e) {
            $this->assertSame('simulated write error', $e->getMessage());
        }
        $this->assertSame($before, $this->snapshot());
    }
}
