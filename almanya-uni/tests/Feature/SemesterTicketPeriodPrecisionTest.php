<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

/**
 * 2026_10_13_000600: "2026: 37,80 €" genellemesi dönem bazına çekilir (KVB: SoSe 2026 34,80 / WiSe 2026/27 37,80);
 * ücret rehberi Senaryo A'da 2 × dönem ücreti varsayımı açıkça belirtilir. Ön doğrulama, rerun no-op.
 */
class SemesterTicketPeriodPrecisionTest extends TestCase
{
    use RefreshDatabase;

    private const MIGRATION = 'database/migrations/2026_10_13_000600_semester_ticket_period_precision_in_two_guides.php';

    private function run600(): void
    {
        (require base_path(self::MIGRATION))->up();
    }

    private function insertGuide(string $locale, string $slug, string $md, string $meta): int
    {
        return DB::table('posts')->insertGetId(['locale' => $locale, 'slug' => $slug, 'type' => 'blog', 'title' => 'T', 'content_md' => $md, 'content_html' => '<p>eski</p>', 'meta_description' => $meta, 'excerpt' => $meta,
            'translation_group_id' => '0b4b4b4b-0000-4000-8000-' . substr(md5($slug), 0, 12), 'is_published' => 1, 'published_at' => '2026-01-28 18:03:00', 'created_at' => '2026-01-28 18:03:00', 'updated_at' => now()->subDay()]);
    }

    private function seedAfter500(): array
    {
        $ids = [];
        $ids['t_tr'] = $this->insertGuide('tr', 'deutschlandticket-2026-price-coverage-and-the-student-semester-ticket',
            "Öğrenciler birçok üniversitede **Deutschlandsemesterticket**'i (2026: aylık €37.80, standart fiyatın %60'ı) Semesterbeitrag ile alır.\n\n- **Standart:** Ocak 2026'dan itibaren **aylık €63** (2025'te €58 idi).\n- **Öğrenci (Deutschlandsemesterticket):** Standart fiyatın %60'ı: 2026'da **aylık €37.80** (≈ dönemlik €226.80; 2025'te €34.80/ay idi). Genelde **Semesterbeitrag** içinde ödenir.\n",
            "Öğrenciler Deutschlandsemesterticket'i (€37.80/ay, 2026) Semesterbeitrag ile alır.");
        $ids['t_en'] = $this->insertGuide('en', 'deutschlandticket-2026-price-coverage-and-the-student-semester-ticket-en',
            "students get the **Deutschlandsemesterticket** (€37.80/month in 2026, 60% of the standard price) via the Semesterbeitrag.\n\n- **Student (Deutschlandsemesterticket):** 60% of the standard price: **€37.80/month** in 2026 (≈ €226.80 per semester; it was €34.80/month in 2025).\n",
            'Students get the Deutschlandsemesterticket (€37.80/month in 2026) via the Semesterbeitrag.');
        $ids['t_de'] = $this->insertGuide('de', 'deutschlandticket-2026-price-coverage-and-the-student-semester-ticket-de',
            "das **Deutschlandsemesterticket** (2026: 37,80 €/Monat, 60 % des Standardpreises) über den Semesterbeitrag.\n\n- **Studierende (Deutschlandsemesterticket):** 60 % des Standardpreises: 2026 **37,80 €/Monat** (≈ 226,80 € pro Semester; 2025 waren es 34,80 €/Monat).\n",
            'Studierende bekommen das Deutschlandsemesterticket (37,80 €/Monat, 2026) über den Semesterbeitrag.');
        $ids['f_tr'] = $this->insertGuide('tr', 'is-university-free-in-germany-2026-real-costs', "| Öğrenim ücreti | 0 € |\n| Semesterbeitrag (2 sömestre, TU Berlin 2026/27: 2 × 379,06 €; Deutschlandsemesterticket dahil) | 758,12 € |\n| **TOPLAM** | **~12.758 €** |\n", 'm');
        $ids['f_de'] = $this->insertGuide('de', 'is-university-free-in-germany-2026-real-costs-de', "| Studiengebühren | 0 € |\r\n| Semesterbeitrag (2 Semester, TU Berlin WS 2026/27: 2 × 379,06 €; Deutschlandsemesterticket inklusive) | 758,12 € |\r\n| **GESAMT** | **~12.758 €** |\r\n", 'm');

        return $ids;
    }

    public function test_period_split_is_applied_and_second_run_is_a_noop(): void
    {
        $ids = $this->seedAfter500();
        $this->run600();

        $tr = Post::findOrFail($ids['t_tr']);
        $this->assertStringNotContainsString("(2026: aylık €37.80, standart fiyatın %60'ı)", $tr->content_md);
        $this->assertStringContainsString('2026 yaz dönemi aylık €34.80, 2026/27 kış dönemi aylık €37.80', $tr->content_md);
        $this->assertStringContainsString('KVB (Köln) örneği: 2026 yaz dönemi **aylık €34.80** (≈ dönemlik €208.80), 2026/27 kış dönemi **aylık €37.80** (≈ dönemlik €226.80)', $tr->content_md);
        $this->assertStringContainsString("(2025'te €58 idi)", $tr->content_md, 'ilgisiz satır dokunulmadı');
        $this->assertStringContainsString('(2026/27 kış: €37.80/ay; 2026 yaz: €34.80/ay, KVB örneği)', $tr->meta_description);
        $this->assertStringContainsString('winter semester 2026/27 **€37.80/month** (≈ €226.80 per semester)', Post::findOrFail($ids['t_en'])->content_md);
        $this->assertStringContainsString('Sommersemester 2026 **34,80 €/Monat** (≈ 208,80 € pro Semester)', Post::findOrFail($ids['t_de'])->content_md);
        $f = Post::findOrFail($ids['f_tr']);
        $this->assertStringContainsString('aynı ücretin 2027 yaz döneminde de geçerli olduğu varsayıldı', $f->content_md);
        $this->assertStringContainsString('| ~758 € |', $f->content_md);
        $this->assertStringContainsString('**~12.758 €**', $f->content_md);
        $de = Post::findOrFail($ids['f_de']);
        $this->assertStringContainsString("\r\n", $de->content_md);
        $this->assertStringContainsString('unter der Annahme, dass er auch im Sommersemester 2027 gilt', $de->content_md);

        $snap = DB::table('posts')->orderBy('id')->get()->toArray();
        $this->run600();
        $this->assertEquals($snap, DB::table('posts')->orderBy('id')->get()->toArray(), 'rerun no-op');
    }

    public function test_unexpected_text_blocks_everything(): void
    {
        $ids = $this->seedAfter500();
        DB::table('posts')->where('id', $ids['t_de'])->update(['content_md' => 'Handgeschrieben.']);
        $before = DB::table('posts')->orderBy('id')->get()->toArray();
        try {
            $this->run600();
            $this->fail('RuntimeException bekleniyordu');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('ticket de', $e->getMessage());
        }
        $this->assertEquals($before, DB::table('posts')->orderBy('id')->get()->toArray());
    }
}
