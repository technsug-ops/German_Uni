<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

/**
 * 2026_10_13_000500: Deutschlandticket rehberi (34,80 → 37,80 €/ay, 2026) ve ücret rehberi (TU Berlin ~330 → 379,06 €,
 * tablo başlığı, Senaryo A) hedefli düzeltmesi; ön doğrulama, rerun no-op, elle değiştirilmiş içerik ezilmez.
 */
class SemesterTicketAndBerlinFeeFixTest extends TestCase
{
    use RefreshDatabase;

    private const MIGRATION = 'database/migrations/2026_10_13_000500_fix_semester_ticket_and_berlin_fee_figures_in_two_guides.php';

    private function run500(): void
    {
        (require base_path(self::MIGRATION))->up();
    }

    private function insertGuide(string $locale, string $slug, string $md, string $meta, string $group): int
    {
        return DB::table('posts')->insertGetId(['locale' => $locale, 'slug' => $slug, 'type' => 'blog', 'title' => 'T ' . $locale, 'content_md' => $md, 'content_html' => '<p>eski</p>',
            'meta_description' => $meta, 'excerpt' => $meta, 'translation_group_id' => $group, 'is_published' => 1, 'published_at' => '2026-01-28 18:03:00', 'created_at' => '2026-01-28 18:03:00', 'updated_at' => now()->subDay()]);
    }

    private function seedGuides(): array
    {
        $ids = [];
        $ids['t_tr'] = $this->insertGuide('tr', 'deutschlandticket-2026-price-coverage-and-the-student-semester-ticket',
            "**30 saniye özet:** Deutschlandticket Ocak 2026'dan itibaren **aylık €63**. Öğrenciler birçok üniversitede **Deutschlandsemesterticket**'i (~aylık €34.80) Semesterbeitrag ile alır.\n\n- **Standart:** Ocak 2026'dan itibaren **aylık €63** (2025'te €58 idi).\n- **Öğrenci (Deutschlandsemesterticket):** Standart fiyatın %60'ı, yaklaşık **aylık €34.80** (≈ dönemlik €208.80). Genelde **Semesterbeitrag** içinde ödenir.\n",
            "Deutschlandticket Ocak 2026'dan itibaren aylık €63. Öğrenciler Deutschlandsemesterticket'i (~€34.80/ay) Semesterbeitrag ile alır.", 'g-ticket');
        $ids['t_en'] = $this->insertGuide('en', 'deutschlandticket-2026-price-coverage-and-the-student-semester-ticket-en',
            "**30-second summary:** students get the **Deutschlandsemesterticket** (~€34.80/month) via the Semesterbeitrag.\n\n- **Student (Deutschlandsemesterticket):** 60% of the standard price, about **€34.80/month** (≈ €208.80 per semester). Usually paid within the **Semesterbeitrag**.\n",
            'Students get the Deutschlandsemesterticket (~€34.80/month) via the Semesterbeitrag.', 'g-ticket');
        $ids['t_de'] = $this->insertGuide('de', 'deutschlandticket-2026-price-coverage-and-the-student-semester-ticket-de',
            "**30-Sekunden-Zusammenfassung:** das **Deutschlandsemesterticket** (~34,80 €/Monat) über den Semesterbeitrag.\n\n- **Studierende (Deutschlandsemesterticket):** 60 % des Standardpreises, etwa **34,80 €/Monat** (≈ 208,80 € pro Semester). Wird meist über den **Semesterbeitrag** gezahlt.\n",
            'Studierende bekommen das Deutschlandsemesterticket (~34,80 €/Monat) über den Semesterbeitrag.', 'g-ticket');
        $feesTr = "# Başlık\n\n### Eyalet ve üni bazında ortalama Semesterbeitrag (2026)\n\n| Üniversite | Semesterbeitrag/sömestre |\n|---|---|\n| TU Berlin | ~330 € |\n| LMU München | ~138 € |\n| Universität Heidelberg | ~170 € (+ 1.500 € öğrenim) |\n| Universität Hamburg | ~340 € |\n| RWTH Aachen | ~310 € |\n| Universität Bonn | ~330 € |\n\n### Senaryo A — Devlet üni, Berlin\n\n| Kalem | Yıllık |\n|---|---|\n| Öğrenim ücreti | 0 € |\n| Semesterbeitrag (2 sömestre) | 660 € |\n| Yaşam (aylık 1.000 € × 12) | 12.000 € |\n| **TOPLAM** | **~12.660 €** |\n\n### Senaryo B — Heidelberg\n\n| Semesterbeitrag | 340 € |\n";
        $ids['f_tr'] = $this->insertGuide('tr', 'is-university-free-in-germany-2026-real-costs', $feesTr, 'meta', 'g-fees');
        $feesDe = str_replace(["### Eyalet ve üni bazında ortalama Semesterbeitrag (2026)", "| Semesterbeitrag (2 sömestre) | 660 € |", "| **TOPLAM** | **~12.660 €** |"],
            ["### Durchschnittlicher Semesterbeitrag nach Bundesland und Uni (2026)", "| Semesterbeitrag (2 Semester) | 660 € |", "| **GESAMT** | **~12.660 €** |"], $feesTr);
        $ids['f_de'] = $this->insertGuide('de', 'is-university-free-in-germany-2026-real-costs-de', str_replace("\n", "\r\n", $feesDe), 'meta', 'g-fees');
        $ids['f_en'] = $this->insertGuide('en', 'is-university-free-in-germany-2026-real-costs-en', "Short answer: mostly free. Semesterbeitrag of 100–400 €.", 'meta', 'g-fees');

        return $ids;
    }

    public function test_applies_targeted_fixes_and_second_run_is_a_noop(): void
    {
        $ids = $this->seedGuides();
        $this->run500();

        $tr = Post::findOrFail($ids['t_tr']);
        $this->assertStringNotContainsString('(~aylık €34.80)', $tr->content_md);
        $this->assertStringContainsString("(2026: aylık €37.80, standart fiyatın %60'ı)", $tr->content_md);
        $this->assertStringContainsString('≈ dönemlik €226.80; 2025\'te €34.80/ay idi', $tr->content_md, 'geçmiş dönem bilgisi korunur');
        $this->assertStringContainsString("(2025'te €58 idi)", $tr->content_md, 'ilgisiz satır dokunulmadı');
        $this->assertSame("Deutschlandticket Ocak 2026'dan itibaren aylık €63. Öğrenciler Deutschlandsemesterticket'i (€37.80/ay, 2026) Semesterbeitrag ile alır.", $tr->meta_description);
        $this->assertStringContainsString('(€37.80/ay, 2026)', (string) $tr->excerpt);
        $this->assertStringContainsString('37.80', (string) $tr->content_html);
        $this->assertSame('2026-01-28 18:03:00', $tr->published_at->format('Y-m-d H:i:s'));
        $this->assertStringContainsString('€37.80/month in 2026', Post::findOrFail($ids['t_en'])->content_md);
        $this->assertStringContainsString('2026 **37,80 €/Monat** (≈ 226,80 € pro Semester; 2025 waren es 34,80 €/Monat)', Post::findOrFail($ids['t_de'])->content_md);

        $f = Post::findOrFail($ids['f_tr']);
        $this->assertStringContainsString('| TU Berlin | 379,06 € (2026/27 kış, resmî; 226,80 € Deutschlandsemesterticket dahil) |', $f->content_md);
        $this->assertStringContainsString('| Universität Heidelberg | ~170 € (+ 1.500 € öğrenim) |', $f->content_md, 'doğrulanmamış satır olduğu gibi (~ işaretli)');
        $this->assertStringContainsString('| Universität Bonn | ~330 € |', $f->content_md);
        $this->assertStringContainsString('~ işaretli satırlar önceki dönemin yaklaşık değerleri', $f->content_md);
        $this->assertStringContainsString('| 758,12 € |', $f->content_md);
        $this->assertStringContainsString('**~12.758 €**', $f->content_md);
        $this->assertStringContainsString('| Semesterbeitrag | 340 € |', $f->content_md, 'Senaryo B dokunulmadı');
        $this->assertStringNotContainsString('~330 € |' . "\n" . '| LMU', $f->content_md);
        $de = Post::findOrFail($ids['f_de']);
        $this->assertStringContainsString("\r\n", $de->content_md, 'CRLF korunur');
        $this->assertStringContainsString('| TU Berlin | 379,06 € (WS 2026/27, amtlich; inkl. 226,80 € Deutschlandsemesterticket) |', $de->content_md);
        $this->assertSame('<p>eski</p>', Post::findOrFail($ids['f_en'])->content_html, 'EN kısa sürüm dokunulmadı');

        $snap = DB::table('posts')->orderBy('id')->get()->toArray();
        $this->run500();
        $this->assertEquals($snap, DB::table('posts')->orderBy('id')->get()->toArray(), 'rerun no-op');
    }

    public function test_unexpected_text_blocks_everything(): void
    {
        $ids = $this->seedGuides();
        DB::table('posts')->where('id', $ids['f_de'])->update(['content_md' => "# Titel\r\n\r\n| TU Berlin | 350 € (elle) |\r\n"]);
        $before = DB::table('posts')->orderBy('id')->get()->toArray();
        try {
            $this->run500();
            $this->fail('RuntimeException bekleniyordu');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('fees de/', $e->getMessage());
        }
        $this->assertEquals($before, DB::table('posts')->orderBy('id')->get()->toArray(), 'TR ve bilet rehberleri de yazılmadı');
    }

    public function test_missing_records_are_skipped_without_writing(): void
    {
        $before = DB::table('posts')->orderBy('id')->get()->toArray();
        $this->run500();
        $this->assertEquals($before, DB::table('posts')->orderBy('id')->get()->toArray());
    }
}
