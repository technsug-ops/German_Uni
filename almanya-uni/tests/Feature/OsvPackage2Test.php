<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Support\OfficialInfoRepair;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * OSV-4 Paket 2 (2026_10_15_000100): Blue Card 2026, Chancenkarte / § 20, öğrenci çalışması (140 iş günü),
 * geçim kanıtı (992 € öğrenim / 1.091 € dil kursu, Sperrkonto VEYA Verpflichtungserklärung), iDATA ↔ portal.
 * Gerçek paket JSON'u kullanılır; fixture kayıtları paketteki beklenen metinleri içerir.
 *  - Yanlış güncel ifade kalkar, doğru tarihsel anlatım korunur; id/slug/çeviri grubu değişmez, yönlendirme oluşmaz.
 *  - Beklenmeyen metin ezilmez (mismatch); ikinci çalıştırma no-op; kontrol tarihi yürürlük tarihi gibi yazılmaz.
 *  - Aktif kaynaklar (sözlük, araç denetleyicisi, PageFaq, BlogAutoLinker, lang, OSV/FAQ paketleri, brief tohumları)
 *    paket 2'nin bilinen yanlış ifadelerini yeniden üretmez.
 */
class OsvPackage2Test extends TestCase
{
    use RefreshDatabase;

    private const PKG = 'database/data/osv/2026-10-15-osv4-package2.json';

    private const MIGRATION = 'database/migrations/2026_10_15_000100_osv4_package2_bluecard_chancenkarte_work_funds.php';

    /** Paket 2'nin düzelttiği birebir eski ifadeler (kelime filtresi değil). */
    private const KNOWN_WRONG = [
        // Blue Card
        '45.300 € maaş = Blue Card otomatik', '45,300 € = automatic Blue Card', '45.300 € = Blue Card automatisch',
        '= automatic Blue Card', '= Blue Card otomatik', '= Blue Card automatisch',
        '56,400 €/yıl', '56,400 €/year', '56.400 €/Jahr', '56,400 € (2026)', '56.400 € (2026)',
        '44,000 €/yıl', '44,000 €/year', '44.000 €/Jahr', '**44,000 €+**', '**44.000 €+**',
        'Yıllık brüt **€41.041**', 'Annual gross **€41,041**', 'Jährlich brutto **€41.041**',
        '~43.760€ 2025', '~€43,760 in 2025', '~48.3k€ (2025)', '~48,3k€ (2025)', '~€48.3k (2025)',
        '(~€43.759,80/yıl, 2025', '(~43.759,80 €/Jahr, 2025', '(~€43,759.80/yr, 2025',
        '~21-33 ay', '~21-33 Monate', '~21-33 months', '33 ayda B1 olmadan', '33 months without', 'oturma süresi (33 ay)',
        '€45.300 yıllık maaş şart', '2026 normal eşik: 48.300', 'Mindestgehalt 2026: 48.300',
        // APS
        'OPSIYONEL ama bazı üniler ister', 'Çin, Hindistan, Pakistan, Vietnam ve Moğolistan', 'China, India, Pakistan, Vietnam, and Mongolia',
        'China, Indien, Pakistan, Vietnam und der Mongolei',
        // Chancenkarte / iş arama
        'This visa does **NOT** allow you to work', 'lets you stay in Germany for **up to 6 months**', 'with the 6-month Jobseeker Visa',
        '6 aylık Jobseeker Vizesi', '6-monatigen Jobsuchvisum', '6 aylık iş arama vizesi (Job-seeker visa)', '6-month job-seeker visa:**',
        '6-monatiges Job-Seeker-Visum', 'A Sperrkonto is also required for highly qualified professionals',
        'yüksek vasıflı profesyoneller için de Sperrkonto gereklidir', 'Job-Seeker Visa allows 6 months job search',
        'Job-Seeker Visa) ile 6 ay arayabilirsin',
        // Öğrenci çalışması / § 20
        '18 aylık iş arama vizesi (§ 20 Abs. 3)', '18-month job-seeker visa (§ 20 Abs. 3)', 'Visum zur Arbeitsplatzsuche (§ 20 Abs. 3)',
        'Automatic right to an **18-month job-seeker visa**', 'Automatischer Übergang zu einem **18-monatigen Visum',
        '18 aylık iş arama vizesi** otomatik geçiş hakkı', 'Yıllık en fazla **20 saat/hafta**', 'maximum of **20 hours per week** per year',
        'maximal **20 Stunden/Woche** pro Jahr', '120 volle oder 240 halbe Tage', '120-day limit', '120-Tage-Grenze',
        'yılda **120 tam gün', '**120 full days/year**',
        // Geçim kanıtı / dil kursu
        'dil kursuna katılmak isteyen öğrenciler için de Sperrkonto zorunludur', 'mandatory for students who wish to attend an intensive language course',
        'ein Sperrkonto obligatorisch', 'kurs süresi × 992', 'course duration × 992', 'Kursdauer × 992', 'Kursmonate × 992',
        '= 5.952 Euro', '= 5,952 Euro', '= 5,952 €', '= 5.952 €', '6 ay = 5,952', '6 Monate = 5.952', '992 Euro x 6',
        'expected to remain valid for 2025', 'expected to be valid for 2025', 'geçerli olması beklenen', 'voraussichtlich auch für 2025',
        'DAAD estimates around €992/month', 'the German embassy requires the standard DAAD rate', 'Up-to-date 2025 figures',
        'Numbers are average values for 2025', 'required for visa as of 2025', '~11.904 €/yıl (2025)', 'Sperrkonto min: €992/month (2025)',
        // iDATA / portal
        'Tüm vize başvuruları IDATA üzerinden yapılır', 'Alle Visumanträge werden über IDATA gestellt', 'Only iDATA and the missions issue appointments',
        "yalnızca iDATA'nın resmi web sitesini kullanmanız", "you only use iDATA's official website", 'ausschließlich die offizielle Website von iDATA zu nutzen',
        'Termine für deutsche Visa werden ausschließlich über die offizielle Website von iDATA',
    ];

    /** Doğru tarihsel anlatım (değişmemeli). */
    private const HISTORICAL_WORK_TR = "Yıllık çalışma limitin 120 tam günden 140 tam güne (veya 240 yarım günden 280 yarım güne) yükseltildi.";

    private array $pkg;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pkg = OfficialInfoRepair::load(base_path(self::PKG));
    }

    private function newTexts(array $e): string
    {
        return (string) ($e['replace'] ?? $e['text'] ?? $e['value'] ?? '');
    }

    /** Seçilen slug tabanlarına ait kayıtları, paketteki beklenen metinleri içerecek şekilde kurar (set işlemleri hariç). */
    private function seedFrom(array $slugBases): array
    {
        $topic = DB::table('faq_topics')->insertGetId(['name' => 'Vize', 'slug' => 'visa-osv2', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()]);
        $records = [];
        foreach ($this->pkg['edits'] as $e) {
            $base = preg_replace('/-(en|de)$/', '', $e['slug']);
            if (! in_array($base, $slugBases, true)) {
                continue;
            }
            $rk = "{$e['table']}|{$e['slug']}|{$e['locale']}";
            $records[$rk] ??= ['table' => $e['table'], 'slug' => $e['slug'], 'locale' => $e['locale'], 'group' => '0e5c0000-0000-4000-8000-' . substr(md5($base), 0, 12), 'fields' => []];
            if (in_array($e['op'], ['replace', 'insert_after'], true)) {
                $records[$rk]['fields'][$e['field']][] = $e['find'];
            }
        }
        $ids = [];
        foreach ($records as $rk => $r) {
            $f = $r['fields'];
            $join = fn (string $field, string $fallback) => isset($f[$field]) ? implode("\n\nAra paragraf.\n\n", $f[$field]) : $fallback;
            if ($r['table'] === 'faqs') {
                $ids[$rk] = $this->upsert('faqs', $r, ['question' => 'Soru ' . $r['slug'] . '?',
                    'answer_md' => "Giriş paragrafı.\n\n" . $join('answer_md', 'Cevap.') . "\n\nSon paragraf.", 'answer_html' => '<p>x</p>', 'has_answer' => 1,
                    'is_published' => 1, 'intent' => 'community', 'translation_group_id' => $r['group'], 'faq_topic_id' => $topic, 'created_at' => now(), 'updated_at' => now()->subDay()]);
            } else {
                $md = "Giriş paragrafı.\n\n" . $join('content_md', 'Gövde.') . "\n\nSon paragraf.";
                if ($r['locale'] === 'tr') {
                    $md .= "\n\n" . self::HISTORICAL_WORK_TR;
                }
                $ids[$rk] = $this->upsert('posts', $r, ['title' => $join('title', 'Başlık ' . $r['slug']),
                    'meta_title' => $join('meta_title', ''), 'meta_description' => $join('meta_description', 'Açıklama.'), 'excerpt' => $join('excerpt', 'Özet.'),
                    'content_md' => $md, 'content_html' => '<p>x</p>', 'is_published' => 1, 'published_at' => now()->subMonth(),
                    'translation_group_id' => $r['group'], 'created_at' => now(), 'updated_at' => now()->subDay()]);
            }
        }

        return $ids;
    }

    /** Test DB'de içerik migration'larının oluşturduğu aynı slug+locale kaydı varsa üzerine yazar (id korunur). */
    private function upsert(string $table, array $r, array $values): int
    {
        $existing = DB::table($table)->where('slug', $r['slug'])->where('locale', $r['locale'])->value('id');
        if ($existing) {
            DB::table($table)->where('id', $existing)->update($values);

            return (int) $existing;
        }

        return DB::table($table)->insertGetId(['locale' => $r['locale'], 'slug' => $r['slug']] + $values);
    }

    private const GROUPS = [
        'doing-a-masters-in-germany-2026-a-z-guide',
        'working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary',
        'how-long-is-a-german-student-visa-valid',
        'how-to-apply-for-a-language-course-visa',
        'sperrkonto-usage-different-visa-and-scholarship-situations-extension-2026',
        'whats-the-difference-between-idata-and-the-consulate-for-german-visa',
        'how-do-you-apply-for-a-visa-through-idata',
        'is-there-an-obligation-to-return-to-turkey-after-the-scholarship',
    ];

    public function test_package_is_valid_sourced_and_free_of_known_wrong_statements(): void
    {
        $this->assertSame('2026-10-15', $this->pkg['checked_at']);
        foreach ($this->pkg['sources'] as $k => $s) {
            $this->assertStringStartsWith('https://', $s['url'], $k);
            $this->assertNotEmpty($s['owner'], $k);
            $this->assertNotEmpty($s['scope'], $k);
            $this->assertSame('2026-10-15', $s['checked'] ?? null, "$k: okuma tarihi");
        }
        $topics = [];
        foreach ($this->pkg['edits'] as $e) {
            $this->assertNotEmpty($e['sources'], $e['key']);
            foreach ($e['sources'] as $s) {
                $this->assertArrayHasKey($s, $this->pkg['sources'], $e['key']);
            }
            $topics[$e['topic']] = true;
            foreach (self::KNOWN_WRONG as $bad) {
                $this->assertStringNotContainsString($bad, $this->newTexts($e), "{$e['key']} yeni metni bilinen yanlış ifadeyi içeriyor: $bad");
            }
            if ($e['op'] === 'set') {
                $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $e['expect_sha256'], $e['key']);
            }
            $this->assertStringNotContainsString("\r", $this->newTexts($e), "{$e['key']}: satır sonu LF olmalı (chunk/markdown tutarlılığı)");
            if ($e['op'] === 'replace') {
                $this->assertStringNotContainsString($e['find'], $e['replace'], "{$e['key']}: replace find'ı içeriyorsa tekrar çalıştırma no-op olmaz");
            }
        }
        $this->assertEqualsCanonicalizing(['bluecard', 'chancenkarte', 'student-work', 'financial-proof', 'visa-channel'], array_keys($topics));
        $this->assertCount(1, $this->pkg['html']);
    }

    public function test_check_date_is_never_written_as_effective_date(): void
    {
        foreach ($this->pkg['edits'] as $e) {
            $t = $this->newTexts($e);
            $this->assertDoesNotMatchRegularExpression('/(since|seit|ab dem|from)\s+(the\s+)?15\.10\.2026|15\.10\.2026[\'’]?(dan|den|tan|ten)?\s+itibaren/u', $t, $e['key']);
            $this->assertDoesNotMatchRegularExpression('/\b(artık|inzwischen|now handled)\b/u', $t, $e['key']);
            // Tarih uydurulmaz: 15.10.2026 (kontrol) dışındaki tarihler yalnız Bundesanzeiger ilan künyesinde geçer.
            preg_match_all('/(.{0,12})\b(\d{1,2}\.\d{1,2}\.\d{4})\b/u', $t, $m, PREG_SET_ORDER);
            foreach ($m as [$all, $before, $date]) {
                if ($date === '15.10.2026') {
                    continue;
                }
                $this->assertContains($date, ['18.12.2025', '29.08.2025'], "{$e['key']}: beklenmeyen tarih $date");
                $this->assertMatchesRegularExpression('/(BAnz AT |%20AT%20)$/', $before, "{$e['key']}: $date yalnız ilan künyesi olabilir");
            }
            if (preg_match('/15\.10\.2026/', $t)) {
                $this->assertMatchesRegularExpression('/(kontrol tarihi: 15\.10\.2026|checked 15\.10\.2026|geprüft am 15\.10\.2026|kontrol: 15\.10\.2026)/u', $t, $e['key']);
            }
        }
    }

    public function test_preview_writes_nothing_then_apply_and_rerun_is_noop(): void
    {
        $ids = $this->seedFrom(self::GROUPS);
        $snap = fn () => [DB::table('faqs')->orderBy('id')->get()->toArray(), DB::table('posts')->orderBy('id')->get()->toArray()];
        $before = $snap();

        $plan = OfficialInfoRepair::plan($this->pkg);
        $this->assertEquals($before, $snap(), 'önizleme yazmaz');
        $mine = array_filter($plan['items'], fn ($i) => isset($ids["{$i['table']}|{$i['slug']}|{$i['locale']}"]) && $i['op'] !== 'set');
        $this->assertGreaterThan(40, count($mine));
        $this->assertSame(['apply'], array_values(array_unique(array_column($mine, 'status'))), json_encode(array_values(array_filter($mine, fn ($i) => $i['status'] !== 'apply'))));

        (require base_path(self::MIGRATION))->up();
        $afterFirst = $snap();
        $this->assertNotEquals($before, $afterFirst);

        $second = OfficialInfoRepair::plan($this->pkg);
        $this->assertNotContains('apply', array_column($second['items'], 'status'), 'ikinci plan hiçbir şey uygulamaz');
        $this->assertSame(['already'], array_values(array_unique(array_column(array_filter($second['items'], fn ($i) => isset($ids["{$i['table']}|{$i['slug']}|{$i['locale']}"]) && $i['op'] !== 'set'), 'status'))));
        (require base_path(self::MIGRATION))->up();
        $this->assertEquals($afterFirst, $snap(), 'ikinci çalıştırma no-op');
    }

    public function test_wrong_claims_replaced_history_and_identity_preserved(): void
    {
        $this->seedFrom(self::GROUPS);
        $identity = fn () => [DB::table('faqs')->orderBy('id')->get(['id', 'slug', 'locale', 'translation_group_id'])->toArray(), DB::table('posts')->orderBy('id')->get(['id', 'slug', 'locale', 'translation_group_id'])->toArray()];
        $before = $identity();
        OfficialInfoRepair::apply($this->pkg);
        $this->assertEquals($before, $identity(), 'id/slug/dil/grup değişmez');
        $this->assertSame(0, DB::table('slug_redirects')->count(), 'yönlendirme oluşmaz');

        $all = DB::table('posts')->get()->map(fn ($p) => implode("\n", [$p->title, $p->meta_description, $p->excerpt, $p->content_md]))
            ->merge(DB::table('faqs')->pluck('answer_md'))->implode("\n\n");
        foreach (self::KNOWN_WRONG as $bad) {
            $this->assertStringNotContainsString($bad, $all, $bad);
        }

        // A — Blue Card 2026
        foreach (['doing-a-masters-in-germany-2026-a-z-guide' => '50.700 €', 'doing-a-masters-in-germany-2026-a-z-guide-en' => '€50,700', 'doing-a-masters-in-germany-2026-a-z-guide-de' => '50.700 €'] as $slug => $amount) {
            $md = DB::table('posts')->where('slug', $slug)->value('content_md');
            $this->assertStringContainsString($amount, $md, $slug);
            $this->assertMatchesRegularExpression('/45[.,]934[.,]20/', $md, $slug);
            $this->assertStringContainsString('BAnz%20AT%2018.12.2025%20B3.pdf', $md, "$slug: BMI ilanı kaynağı");
            $this->assertStringContainsString('Giriş paragrafı.', $md, 'yalnız ilgili satır değişir');
            $this->assertStringContainsString('Son paragraf.', $md);
        }
        $it = DB::table('posts')->where('slug', 'working-in-it-tech-in-germany-as-a-foreigner-blue-card-salary')->first();
        $this->assertStringContainsString("2024'te bu rakamlar €45.300 / €41.041,80 idi", $it->content_md, 'doğru tarihsel karşılaştırma korunur');
        $this->assertStringContainsString('~€45.934,20', $it->content_md);
        $this->assertStringContainsString('| Kategori (2026) |', $it->content_md);
        $this->assertStringContainsString('EU Blue Card 2026 eşikleri', $it->meta_description);
        $this->assertStringContainsString("Chancenkarte'dir (§ 20a AufenthG)", $it->content_md);
        $this->assertStringContainsString(self::HISTORICAL_WORK_TR, $it->content_md, 'doğru tarihsel çalışma limiti cümlesi korunur');

        // D — öğrenci çalışması + § 20
        $g = 'how-long-is-a-german-student-visa-valid';
        foreach (['tr' => $g, 'en' => "$g-en", 'de' => "$g-de"] as $loc => $slug) {
            $md = DB::table('faqs')->where('slug', $slug)->value('answer_md');
            $this->assertStringContainsString('140', $md, $loc);
            $this->assertStringContainsString('__16b.html', $md, "$loc: § 16b kaynağı");
            $this->assertStringContainsString('__20.html', $md, "$loc: § 20 kaynağı");
            $this->assertStringNotContainsString('Abs. 3)', str_replace('§ 16b Abs. 3', '', $md), "$loc: § 20 Abs. 3 kalmaz");
            $this->assertMatchesRegularExpression('/18 (ay|months|Monate)/u', $md, $loc);
        }

        // E — dil kursu geçim kanıtı
        foreach (['how-to-apply-for-a-language-course-visa', 'how-to-apply-for-a-language-course-visa-en', 'how-to-apply-for-a-language-course-visa-de'] as $slug) {
            $md = DB::table('faqs')->where('slug', $slug)->value('answer_md');
            $this->assertMatchesRegularExpression('/1[.,]091/', $md, $slug);
            $this->assertStringContainsString('Verpflichtungserklärung', $md, "$slug: eşit seçenek korunur");
            $this->assertStringContainsString('2725588-2725588', $md, "$slug: Merkblatt 44F");
        }
        $usage = DB::table('posts')->where('slug', 'sperrkonto-usage-different-visa-and-scholarship-situations-extension-2026')->value('content_md');
        $this->assertStringContainsString('ayda en az 1.091 €', $usage);
        $this->assertStringContainsString('BAnz%20AT%2029.08.2025%20B1.pdf', $usage);
        $this->assertStringContainsString('### 3. İş Arama: Chancenkarte', $usage);

        // iDATA ↔ portal
        $diff = DB::table('faqs')->where('slug', 'whats-the-difference-between-idata-and-the-consulate-for-german-visa-en')->value('answer_md');
        $this->assertStringContainsString('digital.diplo.de/studium', $diff);
        $this->assertStringContainsString('iDATA is solely an application center.', $diff, 'mevcut cümle korunur, not eklenir');
        $this->assertStringContainsString('IOM-FAP', DB::table('faqs')->where('slug', 'how-do-you-apply-for-a-visa-through-idata-de')->value('answer_md'));
    }

    public function test_unexpected_text_is_skipped_not_overwritten(): void
    {
        $this->seedFrom(['how-long-is-a-german-student-visa-valid']);
        $slug = 'how-long-is-a-german-student-visa-valid-de';
        DB::table('faqs')->where('slug', $slug)->update(['answer_md' => 'Redaktionell überarbeiteter Text ohne die erwarteten Sätze.']);
        $plan = OfficialInfoRepair::apply($this->pkg);
        $de = array_filter($plan['items'], fn ($i) => $i['slug'] === $slug);
        $this->assertSame(['mismatch'], array_values(array_unique(array_column($de, 'status'))));
        $this->assertSame('Redaktionell überarbeiteter Text ohne die erwarteten Sätze.', DB::table('faqs')->where('slug', $slug)->value('answer_md'));
        $this->assertStringContainsString('__20.html', DB::table('faqs')->where('slug', 'how-long-is-a-german-student-visa-valid')->value('answer_md'), 'kardeş yine düzeltilir');
    }

    public function test_job_search_rewrite_requires_expected_hash_and_states_current_law(): void
    {
        $sets = array_values(array_filter($this->pkg['edits'], fn ($e) => $e['op'] === 'set'));
        $this->assertCount(15, $sets, '3 dil × 5 alan');
        foreach ($sets as $e) {
            $this->assertStringStartsWith('germany-job-seeker-visa-2026-complete-guide-for-graduates', $e['slug'], 'slug korunur');
            $this->assertSame('mismatch', OfficialInfoRepair::evaluate($e, 'editörün değiştirdiği metin')[0], $e['key']);
            $this->assertSame('already', OfficialInfoRepair::evaluate($e, $e['value'])[0], $e['key']);
            if ($e['field'] === 'content_md') {
                $v = $e['value'];
                $this->assertStringContainsString('__20a.html', $v);
                $this->assertStringContainsString('__20.html', $v);
                $this->assertMatchesRegularExpression('/1 (yıla kadar|year|Jahr)/u', $v);
                $this->assertMatchesRegularExpression('/20 (saate|Stunden|hours)/u', $v);
                $this->assertMatchesRegularExpression('/2 (hafta|weeks|Wochen)/u', $v);
                $this->assertMatchesRegularExpression('/18 (ay|months|Monate)/u', $v);
                $this->assertMatchesRegularExpression('/(doğrulayamadık|could not verify|nicht verifizieren)/u', $v, 'Chancenkarte geçim tutarı uydurulmaz');
                $this->assertDoesNotMatchRegularExpression('/(1[.,]091|5[.,]952)/u', $v, 'doğrulanmamış Chancenkarte tutarı yok');
            } else {
                $this->assertDoesNotMatchRegularExpression('/\b6[- ]?(ay|month|Monat)/u', $e['value'], "{$e['key']}: meta/başlıkta 6 ay iddiası yok");
                $this->assertStringContainsString('Chancenkarte', $e['value'], $e['key']);
            }
        }
        // Beklenmeyen gövde ezilmez
        $id = DB::table('posts')->insertGetId(['locale' => 'en', 'slug' => 'germany-job-seeker-visa-2026-complete-guide-for-graduates-en', 'title' => 'Edited', 'content_md' => 'Editor rewrote this already.',
            'content_html' => '<p>x</p>', 'is_published' => 1, 'created_at' => now(), 'updated_at' => now()]);
        OfficialInfoRepair::apply($this->pkg);
        $this->assertSame('Editor rewrote this already.', DB::table('posts')->where('id', $id)->value('content_md'));
        $this->assertSame('Edited', DB::table('posts')->where('id', $id)->value('title'));
    }

    public function test_sperrkonto_tooltip_fixed_in_stored_html_and_for_new_renders(): void
    {
        $old = 'Bloke hesap — Almanya öğrenci vizesi için ~11.904 €/yıl (2025) bloke edilen banka hesabı. Aylık ~992 € çekilebilir.';
        $id = DB::table('posts')->insertGetId(['locale' => 'tr', 'slug' => 'tooltip-yazisi', 'title' => 'T', 'content_md' => 'Sperrkonto (yıllık 11.904 €).',
            'content_html' => '<p><span class="glossary-term" data-tip="' . $old . '">Sperrkonto</span> (yıllık 11.904 €).</p>', 'is_published' => 1, 'created_at' => now(), 'updated_at' => now()]);
        $plan = OfficialInfoRepair::apply($this->pkg);
        $this->assertSame([$id], $plan['html'][0]['ids']);
        $html = DB::table('posts')->where('id', $id)->value('content_html');
        $this->assertStringNotContainsString('(2025)', $html);
        $this->assertStringContainsString('Öğrenim için 2026 tutarı ayda 992 €', $html);
        $this->assertStringContainsString('(yıllık 11.904 €).', $html, 'yalnız tooltip değişir');
        $this->assertSame('already', OfficialInfoRepair::plan($this->pkg)['html'][0]['status']);

        Cache::flush();
        $p = Post::create(['locale' => 'tr', 'slug' => 'yeni-sperrkonto-yazisi', 'title' => 'Yeni', 'content_md' => 'Vize için Sperrkonto gerekir mi?']);
        $this->assertStringNotContainsString('(2025)', (string) $p->content_html);
        $this->assertStringContainsString('Verpflichtungserklärung', (string) $p->content_html);
    }

    public function test_glossary_entries_match_official_sources(): void
    {
        $g = config('glossary');
        foreach (['tr', 'en', 'de'] as $loc) {
            $bc = $g['blue-card']['body'][$loc] . ' ' . $g['blue-card']['faq'][0]['a'][$loc];
            $this->assertMatchesRegularExpression('/50[.,]700/', $bc, "blue-card $loc");
            $this->assertMatchesRegularExpression('/45[.,]934[.,]20/', $bc, "blue-card $loc");
            $this->assertStringContainsString('18.12.2025 B3', $bc, "blue-card $loc: BMI ilanı");
            $this->assertDoesNotMatchRegularExpression('/48[.,]300|43[.,]759/', $bc);
            $this->assertMatchesRegularExpression('/27/', $g['blue-card']['faq'][2]['a'][$loc], "blue-card $loc: 27 ay");

            $aps = $g['aps']['short'][$loc] . ' ' . $g['aps']['body'][$loc] . ' ' . $g['aps']['faq'][0]['a'][$loc];
            $this->assertStringNotContainsString('Pakistan', $aps, "aps $loc");
            $this->assertDoesNotMatchRegularExpression('/Mongol|Moğol/u', $aps, "aps $loc");
            $this->assertMatchesRegularExpression('/(Çin|China)/u', $aps);
            $this->assertMatchesRegularExpression('/(Hindistan|India|Indien)/u', $aps);
            $this->assertMatchesRegularExpression('/(uyruk|nationality|Staatsangehörigkeit)/u', $aps, "aps $loc: belge ülkesi ≠ uyruk");

            $sk = $g['sperrkonto']['body'][$loc] . ' ' . $g['sperrkonto']['faq'][0]['a'][$loc];
            $this->assertStringContainsString('Verpflichtungserklärung', $sk, "sperrkonto $loc");
            $this->assertMatchesRegularExpression('/1[.,]091/', $sk, "sperrkonto $loc: dil kursu");
        }
        // BlogAutoLinker tooltip'i sözlükle aynı ülke listesini kullanır
        $src = file_get_contents(base_path('app/Services/Content/BlogAutoLinker.php'));
        $this->assertStringContainsString("Çin, Vietnam ve Hindistan\\'da verilmiş", $src);
        $this->assertStringContainsString('blog_autolinker_terms_v5_', $src, 'tooltip değişti → önbellek anahtarı yükseldi');
    }

    public function test_glossary_and_tool_pages_render_corrected_figures(): void
    {
        $h = $this->get('/tr/sozluk/blue-card')->assertOk()->getContent();
        $this->assertStringContainsString('50.700', $h);
        $this->assertStringContainsString('45.934,20', $h);
        $this->assertStringContainsString('href="https://www.bundesanzeiger.de/pub/publication/REViP4bN6jVdpGxPaiQ', $h, 'kaynak linki çalışır');
        $this->assertStringNotContainsString('48.300', $h);

        $v = $this->get('/tr/tools/visa-cost')->assertOk()->getContent();
        $this->assertStringContainsString('15.10.2026', $v);
        $this->assertStringNotContainsString('2025 güncel ortalama', $v);
        $this->assertStringContainsString('§ 46 AufenthV', $v);
    }

    public function test_active_sources_do_not_regenerate_known_wrong_statements(): void
    {
        $files = ['config/glossary.php', 'app/Http/Controllers/Web/ToolsController.php', 'app/Support/PageFaq.php', 'app/Services/Content/BlogAutoLinker.php',
            'app/Console/Commands/SeedHowtoBriefs.php', 'app/Console/Commands/BlogGenerateStarter.php', 'lang/tr.json', 'lang/en.json', 'lang/de.json',
            'resources/views/tools/visa-cost.blade.php', 'resources/views/tools/cost-of-living.blade.php', 'resources/views/tools/recommendation.blade.php',
            'resources/views/glossary/show.blade.php'];
        foreach (glob(base_path('database/data/faq-en/*.json')) as $f) {
            $files[] = 'database/data/faq-en/' . basename($f);
        }
        foreach ($files as $f) {
            $src = file_get_contents(base_path($f));
            foreach (self::KNOWN_WRONG as $bad) {
                $this->assertStringNotContainsString($bad, $src, "$f: $bad");
            }
        }
        // OSV paketlerinin YENİ metinleri (find eski metni taşıyabilir)
        foreach (glob(base_path('database/data/osv/*.json')) as $f) {
            $pkg = json_decode(file_get_contents($f), true);
            $new = implode("\n", array_map(fn ($e) => $this->newTexts($e), $pkg['edits'] ?? []));
            foreach ($pkg['html'] ?? [] as $h) {
                $new .= "\n" . $h['replace'];
            }
            foreach (self::KNOWN_WRONG as $bad) {
                $this->assertStringNotContainsString($bad, $new, basename($f) . ": $bad");
            }
        }
        // Doğru tarihsel anlatım yasaklanmadı (deny-list birebir ifade, kelime filtresi değil)
        $this->assertStringContainsString("2024'te bu rakamlar €45.300", json_encode(json_decode(file_get_contents(base_path(self::PKG)), true), JSON_UNESCAPED_UNICODE));
    }
}
