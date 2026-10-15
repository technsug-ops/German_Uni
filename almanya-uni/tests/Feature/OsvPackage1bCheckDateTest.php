<?php

namespace Tests\Feature;

use App\Support\OfficialInfoRepair;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * OSV-3 Paket 1b (2026_10_14_000500): öğrenim vizesi notunda kontrol tarihi ≠ yürürlük tarihi.
 *  - "Güncelleme (14.10.2026) … artık yalnızca" → "Güncel durum (kontrol tarihi: 14.10.2026)" + başlangıç tarihi yok notu.
 *  - Uydurma yürürlük tarihi yok; ikinci çalıştırma no-op; beklenmeyen metin ezilmez.
 *  - Paket 1'in not marker'ları yeni metinde de geçer → paket 1 tekrar çalışırsa notu ikinci kez eklemez.
 */
class OsvPackage1bCheckDateTest extends TestCase
{
    use RefreshDatabase;

    private const PKG = 'database/data/osv/2026-10-14-osv3-package1b-check-date.json';

    private const PKG1 = 'database/data/osv/2026-10-14-osv3-package1.json';

    private function seedFaq(string $locale, string $find, string $slug = 'how-do-you-apply-for-a-visa-through-idata'): int
    {
        $topic = DB::table('faq_topics')->insertGetId(['name' => 'Randevu', 'slug' => 'appointments-' . $locale, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()]);

        return DB::table('faqs')->insertGetId(['locale' => $locale, 'slug' => $slug, 'question' => 'Soru?',
            'answer_md' => "> {$find} Bu iki vize türünde aşağıdaki adımlar geçerli değildir.\n\nCevap gövdesi.", 'answer_html' => '<p>x</p>', 'has_answer' => 1,
            'is_published' => 1, 'intent' => 'community', 'translation_group_id' => '0e5c0000-0000-4000-8000-00000000b1b1', 'faq_topic_id' => $topic,
            'created_at' => now(), 'updated_at' => now()]);
    }

    public function test_package_separates_check_date_from_effective_date_without_inventing_one(): void
    {
        $pkg = OfficialInfoRepair::load(base_path(self::PKG));
        $this->assertCount(30, $pkg['edits']);
        foreach ($pkg['edits'] as $e) {
            $this->assertSame('replace', $e['op']);
            $this->assertMatchesRegularExpression('/(kontrol tarihi: 14\.10\.2026|checked 14\.10\.2026|geprüft am 14\.10\.2026)/', $e['replace']);
            $this->assertDoesNotMatchRegularExpression('/\b(artık|now handled|inzwischen)\b/u', $e['replace']);
            // Başlangıç tarihi uydurulmaz: metinde 14.10.2026 dışında tarih yok.
            preg_match_all('/\b\d{1,2}\.\d{1,2}\.\d{4}\b/', $e['replace'], $m);
            $this->assertSame(['14.10.2026'], array_values(array_unique($m[0])), $e['key']);
        }
    }

    public function test_applies_once_and_skips_unexpected_text(): void
    {
        $pkg = OfficialInfoRepair::load(base_path(self::PKG));
        $byLoc = [];
        foreach ($pkg['edits'] as $e) {
            $byLoc[$e['locale']] ??= $e;
        }
        $id = $this->seedFaq('tr', $byLoc['tr']['find'], $byLoc['tr']['slug']);
        $other = $this->seedFaq('en', 'Some unexpected wording about the portal.', $byLoc['en']['slug']);

        $plan = OfficialInfoRepair::apply($pkg);
        $md = DB::table('faqs')->where('id', $id)->value('answer_md');
        $this->assertStringContainsString('**Güncel durum (kontrol tarihi: 14.10.2026):**', $md);
        $this->assertStringContainsString('resmî sayfada belirtilmiyor', $md);
        $this->assertStringNotContainsString('Güncelleme (14.10.2026)', $md);
        $this->assertStringContainsString('Cevap gövdesi.', $md);
        $this->assertStringStartsWith('> Some unexpected wording about the portal.', DB::table('faqs')->where('id', $other)->value('answer_md'));
        $this->assertGreaterThanOrEqual(1, $plan['counts']['apply'] ?? 0);

        $again = OfficialInfoRepair::plan($pkg);
        $this->assertSame(0, $again['counts']['apply'] ?? 0);
        $this->assertSame($md, DB::table('faqs')->where('id', $id)->value('answer_md'));
    }

    public function test_package_one_markers_survive_the_rewording(): void
    {
        $p1 = OfficialInfoRepair::load(base_path(self::PKG1));
        $p1b = OfficialInfoRepair::load(base_path(self::PKG));
        $new = [];
        foreach ($p1b['edits'] as $e) {
            $new[$e['locale']] = $e['replace'];
        }
        $checked = 0;
        foreach ($p1['edits'] as $e) {
            if (in_array($e['op'], ['prepend', 'insert_after'], true) && str_contains($e['text'], 'digital.diplo.de/studium')) {
                $this->assertStringContainsString($e['marker'], $new[$e['locale']], $e['key']);
                $checked++;
            }
        }
        $this->assertSame(30, $checked);
    }
}
