<?php

namespace Tests\Feature;

use App\Console\Commands\ChatEval;
use App\Services\Rag\ChatService;
use App\Support\OfficialInfoRepair;
use App\Support\PhraseGuard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * OSV-4 Paket 2b (2026_10_15_000300): tarih / ücret tutarlılığı.
 *  - "01.09.2024'ten beri 992 €" doğrulanamadı → "2026 için 992 €" (BMI ilanı); "Eylül 2026 itibarıyla" → kontrol tarihi + kaynak.
 *  - iDATA SSS: 75 € ulusal vize harcı ≠ iDATA bedeli (girişte, chatbot'un gördüğü ilk 1.400 karakter içinde).
 *  - 140 iş günü 01.03.2024 (BGBl. 2023 I Nr. 217) kaynaklı cümlede; yarım gün kavramı 2024'e atfedilmez.
 *  - Dil kursu +%10 → yasa (AufenthG § 2 Abs. 3 S. 6) + kaynak.
 * Gerçek paket JSON'u kullanılır; fixture kayıtları paketteki beklenen metinleri içerir.
 */
class OsvPackage2bTest extends TestCase
{
    use RefreshDatabase;

    private const PKG = 'database/data/osv/2026-10-15-osv4-package2b.json';

    private const MIGRATION = 'database/migrations/2026_10_15_000300_osv4_package2b_dates_fees_consistency.php';

    /** Paket 2b'nin kaldırdığı doğrulanmamış ifadeler (birebir). */
    private const REMOVED = [
        "01.09.2024'ten beri", "1 Eylül 2024'ten beri", 'since 01.09.2024', 'since 1 September 2024', 'seit dem 01.09.2024',
        'seit 01.09.2024', 'seit dem 1. September 2024', "01.09.2024'te 11.208", 'Zum 01.09.2024 wurde',
        '(Eylül 2026 itibarıyla)', 'currently (September 2026)', '(Stand September 2026)',
        '(01.03.2024\'ten beri)', '(since 1 March 2024)', '(seit 01.03.2024)',
        'the mission calculates the required funds', 'berechnet die Vertretung den Lebensunterhalt', 'temsilcilik, geçim güvencesini',
    ];

    private array $pkg;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pkg = OfficialInfoRepair::load(base_path(self::PKG));
    }

    private function newText(array $e): string
    {
        return (string) ($e['replace'] ?? $e['text'] ?? $e['value'] ?? '');
    }

    /** Paketteki tüm kayıtları, beklenen (find) metinleri içerecek şekilde kurar. */
    private function seedPackageRecords(): array
    {
        $topic = DB::table('faq_topics')->insertGetId(['name' => 'Vize', 'slug' => 'visa-osv2b', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()]);
        $records = [];
        foreach ($this->pkg['edits'] as $e) {
            $base = preg_replace('/-(en|de)$/', '', $e['slug']);
            $rk = "{$e['table']}|{$e['slug']}|{$e['locale']}";
            $records[$rk] ??= ['table' => $e['table'], 'slug' => $e['slug'], 'locale' => $e['locale'], 'group' => '0e5c2b00-0000-4000-8000-' . substr(md5($base), 0, 12), 'fields' => []];
            $records[$rk]['fields'][$e['field']][] = $e['find'];
        }
        $ids = [];
        foreach ($records as $rk => $r) {
            $f = $r['fields'];
            $join = fn (string $field, string $fallback) => isset($f[$field]) ? implode("\n\nAra paragraf.\n\n", $f[$field]) : $fallback;
            if ($r['table'] === 'faqs') {
                $ids[$rk] = $this->upsert('faqs', $r, ['question' => 'Soru ' . $r['slug'] . '?',
                    'answer_md' => "Giriş paragrafı.\n\n" . $join('answer_md', 'Cevap.') . "\n\nSon paragraf. Eski 120/240 günlük kural 29.02.2024'te sona erdi.",
                    'answer_html' => '<p>x</p>', 'has_answer' => 1, 'is_published' => 1, 'intent' => 'community', 'translation_group_id' => $r['group'],
                    'faq_topic_id' => $topic, 'created_at' => now(), 'updated_at' => now()->subDay()]);
            } else {
                $ids[$rk] = $this->upsert('posts', $r, ['title' => 'Başlık ' . $r['slug'], 'meta_title' => '',
                    'meta_description' => $join('meta_description', 'Açıklama.'), 'excerpt' => $join('excerpt', 'Özet.'),
                    'content_md' => "Giriş paragrafı.\n\n" . $join('content_md', 'Gövde.') . "\n\nSon paragraf.", 'content_html' => '<p>x</p>',
                    'is_published' => 1, 'published_at' => now()->subMonth(), 'translation_group_id' => $r['group'], 'created_at' => now(), 'updated_at' => now()->subDay()]);
            }
        }

        return $ids;
    }

    private function upsert(string $table, array $r, array $values): int
    {
        $existing = DB::table($table)->where('slug', $r['slug'])->where('locale', $r['locale'])->value('id');
        if ($existing) {
            DB::table($table)->where('id', $existing)->update($values);

            return (int) $existing;
        }

        return DB::table($table)->insertGetId(['locale' => $r['locale'], 'slug' => $r['slug']] + $values);
    }

    public function test_package_is_valid_sourced_and_free_of_known_wrong_statements(): void
    {
        $this->assertSame('2026-10-15', $this->pkg['checked_at']);
        foreach ($this->pkg['sources'] as $k => $s) {
            $this->assertStringStartsWith('https://', $s['url'], $k);
            $this->assertNotEmpty($s['owner'], $k);
            $this->assertNotEmpty($s['scope'], $k);
            $this->assertNotEmpty($s['quote'] ?? null, "$k: birebir alıntı");
            $this->assertSame('2026-10-15', $s['checked'] ?? null, "$k: okuma tarihi");
        }
        $kw = json_decode((string) file_get_contents(base_path('database/data/osv/known_wrong.json')), true);
        $topics = [];
        $keys = [];
        foreach ($this->pkg['edits'] as $e) {
            $this->assertContains($e['op'], ['replace', 'insert_after'], "{$e['key']}: set kullanılmaz");
            $this->assertNotEmpty($e['sources'], $e['key']);
            foreach ($e['sources'] as $s) {
                $this->assertArrayHasKey($s, $this->pkg['sources'], $e['key']);
            }
            $topics[$e['topic']] = true;
            $keys["{$e['table']}|{$e['slug']}|{$e['locale']}|{$e['field']}|{$e['find']}"] = true;
            $t = $this->newText($e);
            $this->assertStringNotContainsString("\r", $t, $e['key']);
            if ($e['op'] === 'replace') {
                $this->assertStringNotContainsString($e['find'], $t, "{$e['key']}: replace find'ı içeremez (idempotent değil)");
            }
            foreach (self::REMOVED as $bad) {
                $this->assertStringNotContainsString($bad, $t, "{$e['key']}: $bad");
            }
            foreach ($kw['entries'] as $k) {
                $this->assertSame([], PhraseGuard::hits($t, $k['phrase'], $k['allow_context'] ?? [], (int) $kw['window']), "{$e['key']}: known_wrong {$k['id']}");
            }
        }
        $this->assertCount(count($this->pkg['edits']), $keys, 'yinelenen düzenleme yok');
        $this->assertEqualsCanonicalizing(['funds-date', 'check-date', 'visa-fee', 'student-work', 'language-course'], array_keys($topics));
        $this->assertArrayNotHasKey('html', $this->pkg);
    }

    public function test_no_invented_dates_and_no_hedging_of_official_values(): void
    {
        foreach ($this->pkg['edits'] as $e) {
            $t = $this->newText($e);
            // kontrol tarihi yürürlük tarihi gibi yazılmaz
            $this->assertDoesNotMatchRegularExpression('/(since|seit|ab dem|from)\s+(the\s+)?15\.10\.2026|15\.10\.2026[\'’]?(dan|den|tan|ten)?\s+itibaren/u', $t, $e['key']);
            $this->assertDoesNotMatchRegularExpression('/\b(artık|inzwischen|now handled|currently)\b/u', $t, $e['key']);
            // doğrulanmamış tarihler geri gelmez
            $this->assertDoesNotMatchRegularExpression('/01\.09\.2024|1 September 2024|September 1, 2024|1\. September 2024|1 Eylül 2024|September 2026|Eylül 2026/u', $t, $e['key']);
            // resmî yıllık değer "yaklaşık" değildir
            $this->assertDoesNotMatchRegularExpression('/\b(approximately|approx\.|ungefähr|yaklaşık|ca\.)/iu', $t, $e['key']);
            // sayısal tarihler: yalnız kontrol tarihi ya da ilan künyesi
            preg_match_all('/(.{0,12})\b(\d{1,2}\.\d{1,2}\.\d{4})\b/u', $t, $m, PREG_SET_ORDER);
            foreach ($m as [$all, $before, $date]) {
                if ($date === '15.10.2026') {
                    continue;
                }
                $this->assertContains($date, ['29.08.2025', '30.08.2023'], "{$e['key']}: beklenmeyen tarih $date");
                $this->assertMatchesRegularExpression('/(BAnz AT |%20AT%20)$/', $before, "{$e['key']}: $date yalnız ilan künyesi olabilir");
            }
            if (str_contains($t, '15.10.2026')) {
                $this->assertMatchesRegularExpression('/(kontrol tarihi: 15\.10\.2026|checked 15\.10\.2026|geprüft am 15\.10\.2026|15\.10\.2026 yalnızca bizim kontrol tarihimiz|15\.10\.2026 is only the date we checked|der 15\.10\.2026 ist nur unser Prüfdatum)/u', $t, $e['key']);
            }
            // yazıyla tarih (1 Mart / March / März 2024) yalnız öğrenci çalışmasında, BGBl kaynağıyla ve yarım günün eskiden de var olduğu notuyla
            if (preg_match('/1 Mart 2024|1 March 2024|1\. März 2024/u', $t)) {
                $this->assertSame('student-work', $e['topic'], $e['key']);
                $this->assertStringContainsString('recht.bund.de/bgbl/1/2023/217', $t, $e['key']);
                $this->assertMatchesRegularExpression('/240 (yarım gün|half days|halbe Tage)/u', $t, "{$e['key']}: yarım gün kavramı 2024'e atfedilmez");
                $this->assertStringContainsString('2006500.pdf', $t, "{$e['key']}: BT-Drs. 20/6500 kaynağı");
            }
        }
    }

    public function test_preview_writes_nothing_then_apply_and_rerun_is_noop(): void
    {
        $ids = $this->seedPackageRecords();
        $snap = fn () => [DB::table('faqs')->orderBy('id')->get()->toArray(), DB::table('posts')->orderBy('id')->get()->toArray()];
        $before = $snap();

        $plan = OfficialInfoRepair::plan($this->pkg);
        $this->assertEquals($before, $snap(), 'önizleme yazmaz');
        $this->assertSame(count($this->pkg['edits']), $plan['counts']['apply'] ?? 0, json_encode(array_values(array_filter($plan['items'], fn ($i) => $i['status'] !== 'apply'))));

        (require base_path(self::MIGRATION))->up();
        $afterFirst = $snap();
        $this->assertNotEquals($before, $afterFirst);

        $second = OfficialInfoRepair::plan($this->pkg);
        $this->assertSame(['already'], array_values(array_unique(array_column($second['items'], 'status'))), 'ikinci plan: hepsi already');
        (require base_path(self::MIGRATION))->up();
        $this->assertEquals($afterFirst, $snap(), 'ikinci çalıştırma no-op');
        $this->assertGreaterThanOrEqual(40, count($ids));
    }

    public function test_corrected_content_identity_preserved_and_unverified_dates_gone(): void
    {
        $ids = $this->seedPackageRecords();
        $identity = fn () => [DB::table('faqs')->orderBy('id')->get(['id', 'slug', 'locale', 'translation_group_id'])->toArray(), DB::table('posts')->orderBy('id')->get(['id', 'slug', 'locale', 'translation_group_id'])->toArray()];
        $before = $identity();
        OfficialInfoRepair::apply($this->pkg);
        $this->assertEquals($before, $identity(), 'id/slug/dil/grup değişmez');
        $this->assertSame(0, DB::table('slug_redirects')->count(), 'yönlendirme oluşmaz');

        // yalnız paketin kayıtları (test DB'deki diğer içerik migration kayıtları kapsam dışı)
        $pick = fn (string $t) => array_values(array_map(fn ($k) => $ids[$k], array_filter(array_keys($ids), fn ($k) => str_starts_with($k, "$t|"))));
        $all = DB::table('posts')->whereIn('id', $pick('posts'))->get()->map(fn ($p) => implode("\n", [$p->meta_description, $p->excerpt, $p->content_md]))
            ->merge(DB::table('faqs')->whereIn('id', $pick('faqs'))->pluck('answer_md'))->implode("\n\n");
        foreach (self::REMOVED as $bad) {
            $this->assertStringNotContainsString($bad, $all, $bad);
        }
        $this->assertStringContainsString("Eski 120/240 günlük kural 29.02.2024'te sona erdi.", $all, 'doğrulanmış tarihsel cümle korunur');

        $faq = fn (string $slug) => (string) DB::table('faqs')->where('slug', $slug)->value('answer_md');
        // A — öğrenim vizesi SSS
        $en = $faq('how-do-i-get-a-german-student-visa-en');
        $this->assertStringContainsString('For 2026 the reference amount is **€992 per month, €11,904 per year**', $en);
        $this->assertStringContainsString('BAnz%20AT%2029.08.2025%20B1.pdf', $en);
        $this->assertStringContainsString('The official page does not say since when this rule applies; 15.10.2026 is only the date we checked it.', $en);
        $this->assertStringContainsString('Seit wann diese Regel gilt, nennt die offizielle Seite nicht', $faq('how-do-i-get-a-german-student-visa-de'));
        $this->assertStringContainsString('2026 için esas alınan tutar', $faq('how-do-i-get-a-german-student-visa'));
        $this->assertStringContainsString('2024 için aylık 934 € idi', $faq('how-much-money-do-i-need-for-a-blocked-account-sperrkonto'));
        $post = DB::table('posts')->where('slug', '2026-germany-blocked-account-sperrkonto-update-how-much-money-do-you-en')->first();
        $this->assertStringContainsString('(€992 a month) for 2026 (BMI notice).', $post->meta_description);
        $this->assertStringContainsString('(€992 a month) for 2026 (BMI notice).', $post->excerpt);
        $this->assertLessThanOrEqual(280, mb_strlen($post->excerpt));

        // B — iDATA: 75 € harç ayrı ve chatbot'un okuduğu ilk 1.400 karakterde
        foreach (['how-do-you-apply-for-a-visa-through-idata' => '75 €', 'how-do-you-apply-for-a-visa-through-idata-en' => '€75', 'how-do-you-apply-for-a-visa-through-idata-de' => '75 €'] as $slug => $fee) {
            $md = $faq($slug);
            $pos = mb_strpos($md, $fee);
            $this->assertNotFalse($pos, $slug);
            $this->assertLessThan(1400, $pos, "$slug: 75 € chatbot bağlamında (ilk 1.400 karakter)");
            $this->assertMatchesRegularExpression('/39[.,]92/', $md, $slug);
            $this->assertStringContainsString('aufenthv/__46.html', $md, $slug);
            $this->assertStringContainsString('aufenthv/__52.html', $md, $slug);
        }

        // C — 140 gün: tarih kaynaklı cümlede, yarım gün eskiden de vardı
        $w = $faq('how-many-hours-a-week-can-i-work-as-a-student-in-germany-en');
        $this->assertStringContainsString("### How the 140 days are counted\nThe 140-day account has applied since 1 March 2024", $w);
        $this->assertStringContainsString('Half days already existed under the previous rule (120 full or 240 half days)', $w);
        $this->assertStringContainsString('Halbe Tage gab es schon in der alten Regel', $faq('can-i-work-part-time-during-my-masters-in-germany-de'));

        // D — dil kursu: +%10 yasaya dayanır, tutar ve kaynak
        $lc = $faq('how-much-money-must-be-in-the-blocked-account-for-a-language-course-visa-en');
        $this->assertStringContainsString('the Residence Act requires the student amount **plus a 10% surcharge**', $lc);
        $this->assertStringContainsString('aufenthg_2004/__2.html', $lc);
        $this->assertStringContainsString('at least €1,091 per month', $lc);
    }

    public function test_unexpected_text_is_not_overwritten(): void
    {
        $this->seedPackageRecords();
        $slug = 'how-do-i-get-a-german-student-visa-en';
        $edited = 'Editor rewrote this answer. Since 01.09.2024 the reference amount is 992 € (no bold).';
        DB::table('faqs')->where('slug', $slug)->update(['answer_md' => $edited]);

        $plan = OfficialInfoRepair::apply($this->pkg);
        $mine = array_values(array_filter($plan['items'], fn ($i) => $i['slug'] === $slug));
        $this->assertNotEmpty($mine);
        $this->assertSame(['mismatch'], array_values(array_unique(array_column($mine, 'status'))));
        $this->assertSame($edited, DB::table('faqs')->where('slug', $slug)->value('answer_md'), 'beklenmeyen metin ezilmez');
        // diğer kayıtlar yine uygulanır
        $this->assertStringContainsString('Für 2026 gilt als Richtwert', (string) DB::table('faqs')->where('slug', 'how-do-i-get-a-german-student-visa-de')->value('answer_md'));
    }

    public function test_chat_prompt_keeps_official_values_exact_and_strips_rule_leak(): void
    {
        // ChatService kurucusu GeminiEmbedder'ı başlatır; CI'da GEMINI_API_KEY yok → sahte anahtar (API çağrısı yapılmaz).
        config(['services.gemini.key' => 'test-key']);
        $chat = app(ChatService::class);
        foreach (['tr', 'en', 'de'] as $loc) {
            $system = $chat->buildPayload('Blue Card 2026?', $loc, [['title' => 'T', 'url' => '/x', 'content' => '2026: 50.700 €']], [])['systemInstruction']['parts'][0]['text'];
            $this->assertStringContainsString('kaynaktaki tam rakamla, kaynaktaki yıl ve kapsamla ver', $system, $loc);
            $this->assertStringContainsString('"approximately", "ungefähr" veya "ca."', $system, $loc);
            $this->assertStringContainsString('rakam veya yıl uydurma', $system, $loc);
            $this->assertStringContainsString('kuralın başladığı tarih değildir', $system, "$loc: check-date kuralı korunur");
        }
        $leak = "2026 için eşik 50.700 € [1].\n- Resmî ilan veya yasayla belirli bir yıl ya da dönem için belirlenmiş tutar ve eşiği kaynaktaki tam rakamla, kaynaktaki yıl ve kapsamla ver.";
        $this->assertSame('2026 için eşik 50.700 € [1].', $chat->stripInstructionLeak($leak));
        $natural = 'Für 2026 beträgt das Mindestgehalt 50.700 € [1].';
        $this->assertSame($natural, $chat->stripInstructionLeak($natural), 'doğal cümleye dokunulmaz');
    }

    public function test_eval_set_has_package2b_cases_and_live_scenarios(): void
    {
        $items = json_decode((string) file_get_contents(resource_path('data/chat_eval.json')), true)['items'];
        $osv = array_values(array_filter($items, fn ($i) => in_array('osv', (array) ($i['tag'] ?? []), true)));
        $claims = array_count_values(array_column($osv, 'claim'));
        foreach (['funds.date' => 3, 'work.student.counting' => 3, 'langcourse.funds' => 3, 'visa.fee' => 5, 'bluecard.2026' => 5, 'check-date' => 4] as $c => $min) {
            $this->assertGreaterThanOrEqual($min, $claims[$c] ?? 0, $c);
        }
        foreach ($osv as $i) {
            $this->assertNotEmpty($i['must_not_contain'] ?? $i['must_contain'] ?? null, $i['q']);
        }
        $live = array_filter($items, fn ($i) => in_array('osv-live-2026-10-15', (array) ($i['tag'] ?? []), true));
        $this->assertCount(44, $live, '15.10.2026 canlı kapanış testinin 44 sorusu');
        $this->assertCount(44, array_unique(array_map(fn ($i) => $i['lang'] . '|' . $i['q'], $live)));

        // Deterministik kontrol: canlıda görülen kısmen doğru cevaplar artık yakalanır
        $byQ = array_column($osv, null, 'q');
        $bc = $byQ['What is the minimum annual gross salary for the EU Blue Card in Germany in 2026?'];
        $this->assertContains('approximately', ChatEval::stringChecks($bc, 'Approximately €50,700; reduced €45,934.20 [1].')['forbidden']);
        $this->assertSame(['forbidden' => [], 'missing' => []], ChatEval::stringChecks($bc, 'For 2026: €50,700; reduced threshold €45,934.20 [1].'));
        $de = $byQ['Wie viel muss für ein Studienvisum monatlich auf dem Sperrkonto sein, und seit wann gilt der Betrag?'];
        $this->assertContains('seit dem 01.09.2024', ChatEval::stringChecks($de, 'Seit dem 01.09.2024 gilt ein Richtwert von 992 € [1].')['forbidden']);
        $fee = $byQ['How much is the German national visa fee, and does iDATA charge a separate fee?'];
        $this->assertSame(['€75 | 75 € | 75 euro | EUR 75'], ChatEval::stringChecks($fee, 'iDATA charges €39.92. The sources do not specify the exact amount for the German national visa fee itself.')['missing']);
    }
}
