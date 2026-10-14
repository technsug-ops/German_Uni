<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Support\OfficialInfoRepair;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * OSV-3 Paket 1 (2026_10_14_000100): remonstrasyon, APS, Türkiye'den öğrenim vizesi kanalı ve iDATA bedelleri.
 * Gerçek paket JSON'u kullanılır; fixture kayıtları paketteki beklenen metinleri içerir.
 *  - Yanlış güncel tavsiye kalkar, doğru tarihsel anlatım (ör. "Remonstration 01.07.2025'te kaldırıldı") korunur.
 *  - id, slug, çeviri grubu, canonical ve hreflang değişmez; yönlendirme oluşmaz.
 *  - Beklenmeyen metin ezilmez (mismatch → atlanır); ikinci çalıştırma no-op.
 *  - Aktif kaynaklar (sözlük, brief tohumu, 13 Ekim paketleri, araç sayfası) bilinen yanlış ifadeleri yeniden üretmez.
 */
class OsvPackage1RepairTest extends TestCase
{
    use RefreshDatabase;

    private const PKG = 'database/data/osv/2026-10-14-osv3-package1.json';
    private const MIGRATION = 'database/migrations/2026_10_14_000100_osv3_package1_remonstration_aps_visa_channel.php';

    /** Denetimde bulunan, güncel tavsiye olarak yanlış ifadeler (kelime filtresi değil, birebir eski cümleler). */
    private const KNOWN_WRONG = [
        'usually offers a remonstration',
        'bietet die Auslandsvertretung in der Regel eine Remonstration',
        '30 gün içinde **itiraz**',
        '30 gün içinde itiraz (Remonstration)',
        'within 30 days**',
        'Remonstration 1 ay zorunlu',
        '1 ay içinde Remonstration (itiraz) hakkı var',
        "Türkiye\\'den başvuranların akademik belgelerinin denkliğini",
        "Türkiye'den başvuranların akademik belgelerinin denkliğini",
        "Türkiye'den başvuran öğrenciler için APS belgesi zorunludur",
        'iData is the only center authorized',
        'tek yetkili kanal iData',
        '€32.81',
        '32,81 €',
        'work exclusively with the external service provider **iDATA**',
        'Use your **30-day right to appeal**',
        '30 gün **itiraz hakkı** kullan',
    ];

    private const HISTORICAL_TR = "Remonstration (itiraz) usulü 1 Temmuz 2025'te kaldırıldı; reddedilirsen yeni başvuru veya Berlin İdare Mahkemesi'nde dava yolu kaldı.";

    private array $pkg;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pkg = OfficialInfoRepair::load(base_path(self::PKG));
    }

    /** Seçilen slug tabanlarına ait kayıtları, paketteki beklenen metinleri içerecek şekilde kurar. */
    private function seedFrom(array $slugBases): array
    {
        $topic = DB::table('faq_topics')->insertGetId(['name' => 'Randevu', 'slug' => 'appointments', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()]);
        $records = [];
        foreach ($this->pkg['edits'] as $e) {
            $base = preg_replace('/-(en|de)$/', '', $e['slug']);
            if (! in_array($base, $slugBases, true)) {
                continue;
            }
            $rk = "{$e['table']}|{$e['slug']}|{$e['locale']}";
            $records[$rk] ??= ['table' => $e['table'], 'slug' => $e['slug'], 'locale' => $e['locale'], 'group' => '0e5c0000-0000-4000-8000-' . substr(md5($base), 0, 12), 'fields' => []];
            $piece = match ($e['op']) {
                'replace' => $e['find'],
                'insert_after' => $e['find'],
                default => null,
            };
            if ($piece !== null) {
                $records[$rk]['fields'][$e['field']][] = $piece;
            }
        }
        $ids = [];
        foreach ($records as $rk => $r) {
            $f = $r['fields'];
            // insert_after çapası (H1) metnin başında olmalı
            $join = fn (string $field, string $fallback) => isset($f[$field]) ? implode("\n\nAra paragraf.\n\n", $f[$field]) : $fallback;
            if ($r['table'] === 'faqs') {
                $ids[$rk] = DB::table('faqs')->insertGetId(['locale' => $r['locale'], 'slug' => $r['slug'], 'question' => 'Soru ' . $r['slug'] . '?',
                    'answer_md' => "Giriş paragrafı.\n\n" . $join('answer_md', 'Cevap.') . "\n\nSon paragraf.", 'answer_html' => '<p>x</p>', 'has_answer' => 1,
                    'is_published' => 1, 'intent' => 'community', 'translation_group_id' => $r['group'], 'faq_topic_id' => $topic, 'created_at' => now(), 'updated_at' => now()->subDay()]);
            } else {
                $md = $join('content_md', '# Başlık');
                if ($r['locale'] === 'tr') {
                    $md .= "\n\n" . self::HISTORICAL_TR;
                }
                $ids[$rk] = DB::table('posts')->insertGetId(['locale' => $r['locale'], 'slug' => $r['slug'], 'title' => $join('title', 'Başlık ' . $r['slug']),
                    'meta_title' => $join('meta_title', ''), 'meta_description' => $join('meta_description', 'Açıklama.'), 'excerpt' => $join('excerpt', 'Özet.'),
                    'content_md' => $md, 'content_html' => '<p>x</p>', 'is_published' => 1, 'published_at' => now()->subMonth(),
                    'translation_group_id' => $r['group'], 'created_at' => now(), 'updated_at' => now()->subDay()]);
            }
        }

        return $ids;
    }

    public function test_package_is_valid_sourced_and_free_of_known_wrong_statements(): void
    {
        $this->assertSame('2026-10-14', $this->pkg['checked_at']);
        $topics = [];
        foreach ($this->pkg['edits'] as $e) {
            $this->assertNotEmpty($e['sources'], $e['key']);
            foreach ($e['sources'] as $s) {
                $this->assertArrayHasKey($s, $this->pkg['sources'], $e['key']);
                $this->assertStringStartsWith('https://', $this->pkg['sources'][$s]['url']);
            }
            $topics[$e['topic']] = true;
            $new = $e['replace'] ?? $e['text'] ?? $e['value'];
            foreach (self::KNOWN_WRONG as $bad) {
                $this->assertStringNotContainsString($bad, $new, "{$e['key']} yeni metni bilinen yanlış ifadeyi içeriyor");
            }
            if ($e['op'] === 'set') {
                $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $e['expect_sha256'], $e['key']);
            }
        }
        $this->assertEqualsCanonicalizing(['remonstration', 'aps', 'visa', 'visa-fee'], array_keys($topics));
        // Paket 2 konuları (Chancenkarte, çalışma limitleri) bu pakette yok
        $this->assertStringNotContainsStringIgnoringCase('chancenkarte', json_encode($this->pkg['edits'], JSON_UNESCAPED_UNICODE));
    }

    public function test_preview_writes_nothing_and_apply_then_rerun_is_noop(): void
    {
        $ids = $this->seedFrom(['if-the-appointment-is-cancelled-do-you-have-to-re-book-it', 'how-many-weeks-does-it-take-to-get-a-visa-appointment-at-the-german-consulate',
            'germany-student-visa-2026-application-steps-documents-rejection', 'what-can-i-do-about-communication-problems-with-officials-at-visa-interviews']);
        $before = [DB::table('faqs')->orderBy('id')->get()->toArray(), DB::table('posts')->orderBy('id')->get()->toArray()];

        $plan = OfficialInfoRepair::plan($this->pkg);
        $this->assertEquals($before, [DB::table('faqs')->orderBy('id')->get()->toArray(), DB::table('posts')->orderBy('id')->get()->toArray()], 'önizleme yazmaz');
        // Yalnız bu testin kurduğu kayıtlar (test DB'de içerik migration'larının oluşturduğu başka kayıtlar zaten düzeltilmiş olabilir)
        $seeded = array_keys($ids);
        $mine = array_filter($plan['items'], fn ($i) => in_array("{$i['table']}|{$i['slug']}|{$i['locale']}", $seeded, true));
        $this->assertNotEmpty($mine);
        $this->assertSame(['apply'], array_values(array_unique(array_column($mine, 'status'))), json_encode(array_values(array_filter($mine, fn ($i) => $i['status'] !== 'apply'))));

        (require base_path(self::MIGRATION))->up();
        $afterFirst = [DB::table('faqs')->orderBy('id')->get()->toArray(), DB::table('posts')->orderBy('id')->get()->toArray()];

        $second = OfficialInfoRepair::plan($this->pkg);
        $this->assertSame(['already'], array_values(array_unique(array_column(array_filter($second['items'], fn ($i) => in_array("{$i['table']}|{$i['slug']}|{$i['locale']}", $seeded, true)), 'status'))));
        $this->assertNotContains('apply', array_column($second['items'], 'status'), 'ikinci plan hiçbir şey uygulamaz');
        (require base_path(self::MIGRATION))->up();
        $this->assertEquals($afterFirst, [DB::table('faqs')->orderBy('id')->get()->toArray(), DB::table('posts')->orderBy('id')->get()->toArray()], 'ikinci çalıştırma no-op');
    }

    public function test_wrong_advice_is_replaced_history_and_identity_preserved_in_all_languages(): void
    {
        $ids = $this->seedFrom(['if-the-appointment-is-cancelled-do-you-have-to-re-book-it', 'germany-student-visa-2026-application-steps-documents-rejection',
            'what-can-i-do-about-communication-problems-with-officials-at-visa-interviews']);
        $identity = fn () => [DB::table('faqs')->orderBy('id')->get(['id', 'slug', 'locale', 'translation_group_id'])->toArray(), DB::table('posts')->orderBy('id')->get(['id', 'slug', 'locale', 'translation_group_id'])->toArray()];
        $before = $identity();
        OfficialInfoRepair::apply($this->pkg);
        $this->assertEquals($before, $identity(), 'id/slug/dil/grup değişmez');
        $this->assertSame(0, DB::table('slug_redirects')->count(), 'yönlendirme oluşmaz');

        $g = 'if-the-appointment-is-cancelled-do-you-have-to-re-book-it';
        foreach (['tr' => $g, 'en' => "$g-en", 'de' => "$g-de"] as $loc => $slug) {
            $f = DB::table('faqs')->where('slug', $slug)->first();
            foreach (self::KNOWN_WRONG as $bad) {
                $this->assertStringNotContainsString($bad, $f->answer_md, "$loc: $bad");
            }
            $this->assertStringNotContainsString('30 €', $f->answer_md, "$loc: eski iDATA tutarı kalktı");
            $this->assertStringContainsString('2715934-2715934', $f->answer_md, "$loc: Auswärtiges Amt kaynağı");
            $this->assertStringContainsString('Rechtsbehelfsbelehrung', $f->answer_md, "$loc: yargı yolu karara bağlanır");
            $this->assertMatchesRegularExpression('/39,92 €|€39\.92/', $f->answer_md, "$loc: resmî iDATA bedeli");
            $this->assertStringContainsString('href="https://www.auswaertiges-amt.de/de/2715934-2715934"', $f->answer_html, "$loc: HTML yeniden üretildi");
            $this->assertStringContainsString('Giriş paragrafı.', $f->answer_md, 'yalnız ilgili blok değişir');
            $this->assertStringContainsString('Son paragraf.', $f->answer_md);
        }
        // Somut dava süresi uydurulmaz
        foreach (array_filter($this->pkg['edits'], fn ($e) => $e['topic'] === 'remonstration') as $e) {
            $this->assertDoesNotMatchRegularExpression('/\b(\d+)\s*(gün|days|Tage|hafta|weeks|Wochen|ay|month|Monat)/u', $e['replace'], $e['key']);
        }

        $post = DB::table('posts')->where('slug', 'germany-student-visa-2026-application-steps-documents-rejection')->first();
        $this->assertStringContainsString(self::HISTORICAL_TR, $post->content_md, 'doğru tarihsel bilgi korunur');
        $this->assertStringContainsString('digital.diplo.de/studium', $post->content_md);
        $this->assertStringNotContainsString('vfsglobal.com', $post->content_md);
        $this->assertStringNotContainsString('dilekçesi rehberi', $post->excerpt);

        $en = DB::table('faqs')->where('slug', 'what-can-i-do-about-communication-problems-with-officials-at-visa-interviews-en')->first();
        $this->assertStringContainsString('abolished on 1 July 2025', $en->answer_md);
        $this->assertStringContainsString('Giriş paragrafı.', $en->answer_md, 'yalnız remonstrasyon cümlesi değişir, çevre metin korunur');
        $this->assertStringContainsString('A complaint concerns how you were treated', $en->answer_md, 'şikâyet yolu (meşru) ile vize kararı ayrıştırılır');
    }

    public function test_unexpected_text_is_skipped_not_overwritten(): void
    {
        $this->seedFrom(['if-the-appointment-is-cancelled-do-you-have-to-re-book-it']);
        $slug = 'if-the-appointment-is-cancelled-do-you-have-to-re-book-it-de';
        DB::table('faqs')->where('slug', $slug)->update(['answer_md' => 'Redaktionell überarbeiteter Text ohne die erwarteten Sätze.']);
        $plan = OfficialInfoRepair::apply($this->pkg);
        $de = array_filter($plan['items'], fn ($i) => $i['slug'] === $slug);
        $this->assertSame(['mismatch'], array_values(array_unique(array_column($de, 'status'))));
        $this->assertSame('Redaktionell überarbeiteter Text ohne die erwarteten Sätze.', DB::table('faqs')->where('slug', $slug)->value('answer_md'));
        // Kardeşler yine düzeltilir (kayıt bazında atlama)
        $this->assertStringContainsString('2715934-2715934', DB::table('faqs')->where('slug', 'if-the-appointment-is-cancelled-do-you-have-to-re-book-it')->value('answer_md'));
    }

    public function test_whole_article_set_requires_expected_hash(): void
    {
        $e = ['op' => 'set', 'expect_sha256' => hash('sha256', 'eski metin'), 'value' => 'yeni metin'];
        $this->assertSame(['apply', 'yeni metin'], OfficialInfoRepair::evaluate($e, 'eski metin'));
        $this->assertSame('mismatch', OfficialInfoRepair::evaluate($e, 'editörün değiştirdiği metin')[0]);
        $this->assertSame('already', OfficialInfoRepair::evaluate($e, 'yeni metin')[0]);

        $aps = array_filter($this->pkg['edits'], fn ($x) => str_starts_with($x['key'], 'A-aps-guide-') && $x['field'] === 'content_md');
        $this->assertCount(3, $aps, 'TR/EN/DE');
        foreach ($aps as $x) {
            $this->assertStringContainsString('aps-india.de/faqs', $x['value']);
            $this->assertMatchesRegularExpression('/China|Çin/u', $x['value']);
            $this->assertMatchesRegularExpression('/(uyruk|nationality|Staatsangehörigkeit)/u', $x['value'], 'belge ülkesi ≠ uyruk');
            $this->assertMatchesRegularExpression('/(anabin)/u', $x['value']);
            $this->assertMatchesRegularExpression('/(International Baccalaureate|IB )/u', $x['value'], 'istisna korunur');
            $this->assertStringNotContainsString('aps.org.tr', $x['value']);
        }
    }

    public function test_glossary_tooltip_is_fixed_in_stored_html_and_for_new_renders(): void
    {
        $old = "Akademische Prüfstelle — Türkiye'den başvuranların akademik belgelerinin denkliğini kontrol eden kurum. Çoğu başvuru için zorunlu.";
        $id = DB::table('posts')->insertGetId(['locale' => 'tr', 'slug' => 'logistik-yazisi', 'title' => 'Lojistik', 'content_md' => 'APS (Türkiye için genelde gerekmez).',
            'content_html' => '<p><span class="glossary-term" data-tip="' . $old . '">APS</span> (Türkiye için genelde gerekmez).</p>', 'is_published' => 1, 'created_at' => now(), 'updated_at' => now()]);
        $plan = OfficialInfoRepair::apply($this->pkg);
        $this->assertSame([$id], $plan['html'][0]['ids']);
        $html = DB::table('posts')->where('id', $id)->value('content_html');
        $this->assertStringNotContainsString('Çoğu başvuru için zorunlu', $html);
        $this->assertStringContainsString("Türkiye'de verilmiş belgeler için APS yok", $html);
        $this->assertStringContainsString('(Türkiye için genelde gerekmez).', $html, 'yalnız tooltip değişir');
        $this->assertSame('already', OfficialInfoRepair::plan($this->pkg)['html'][0]['status']);

        // Yeni kayıt/düzenleme: sözlük artık doğru tanımı üretir
        Cache::flush();
        $p = Post::create(['locale' => 'tr', 'slug' => 'yeni-aps-yazisi', 'title' => 'Yeni', 'content_md' => 'Belgeler için APS gerekir mi?']);
        $this->assertStringNotContainsString('Çoğu başvuru için zorunlu', (string) $p->content_html);
    }

    public function test_active_sources_do_not_regenerate_known_wrong_statements(): void
    {
        $files = ['app/Services/Content/BlogAutoLinker.php', 'app/Console/Commands/SeedHowtoBriefs.php', 'app/Console/Commands/BlogGenerateStarter.php',
            'resources/views/tools/visa-appointment.blade.php', 'database/data/faq-en/2026-10-13-batch1.json', 'database/data/faq-en/2026-10-13-batch2.json',
            'lang/tr.json', 'lang/en.json', 'lang/de.json'];
        foreach ($files as $f) {
            $src = file_get_contents(base_path($f));
            foreach (self::KNOWN_WRONG as $bad) {
                $this->assertStringNotContainsString($bad, $src, "$f: $bad");
            }
        }
        // 13 Ekim paketleri, OSV düzeltmesinin metniyle birebir aynı (yeniden uygulanırsa hata geri gelmez)
        $byKey = array_column($this->pkg['edits'], null, 'key');
        $this->assertStringContainsString(substr(json_encode($byKey['R-1159-en']['replace'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 1, -1),
            json_encode(json_decode(file_get_contents(base_path('database/data/faq-en/2026-10-13-batch1.json')), true), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $this->assertStringContainsString(substr(json_encode($byKey['R-1160-de']['replace'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 1, -1),
            json_encode(json_decode(file_get_contents(base_path('database/data/faq-en/2026-10-13-batch2.json')), true), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        foreach (['V-2000-intro', 'V-1999-wait', 'V-1999-todo', 'V-1999-fee'] as $k) {
            $this->assertStringContainsString(substr(json_encode($byKey[$k]['replace'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 1, -1),
                json_encode(json_decode(file_get_contents(base_path('database/data/faq-en/2026-10-13-batch1.json')), true), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $k);
        }
        // Doğru tarihsel anlatımlar yasaklanmadı: kaynaklarda "kaldırıldı" ifadesi serbest
        $this->assertStringContainsString("Remonstration 01.07.2025 itibarıyla kaldırıldı", file_get_contents(base_path('app/Console/Commands/SeedHowtoBriefs.php')));
    }

    public function test_visa_appointment_tool_states_portal_channel_and_separate_fees(): void
    {
        $html = $this->get('/tr/tools/visa-appointment')->assertOk()->getContent();
        $this->assertStringContainsString('digital.diplo.de/studium', $html);
        $this->assertStringContainsString('2728622-2728622', $html);
        $this->assertStringContainsString('39,92 €', $html);
        $this->assertStringContainsString('12 €', $html);
        $this->assertStringContainsString('75 €', $html);
        $this->assertStringNotContainsString('32.81', $html);
        $this->assertStringNotContainsString('37.50', $html);
        $this->assertStringContainsString('<link rel="canonical" href="' . url('/tr/tools/visa-appointment') . '"', $html);
        $this->get('/en/tools/visa-appointment')->assertRedirect();
    }

    public function test_blog_page_canonical_and_hreflang_do_not_change(): void
    {
        $this->seedFrom(['germany-student-visa-2026-application-steps-documents-rejection']);
        $grab = function () {
            $h = $this->get('/tr/blog/germany-student-visa-2026-application-steps-documents-rejection')->assertOk()->getContent();
            preg_match('#<link rel="canonical" href="([^"]+)"#', $h, $c);
            preg_match_all('#<link rel="alternate" hreflang="([^"]+)" href="([^"]+)"#', $h, $alt, PREG_SET_ORDER);

            return [$c[1] ?? null, array_map(fn ($a) => $a[1] . '=' . $a[2], $alt), $h];
        };
        [$c1, $alt1] = $grab();
        OfficialInfoRepair::apply($this->pkg);
        [$c2, $alt2, $html] = $grab();
        $this->assertSame($c1, $c2);
        $this->assertSame($alt1, $alt2);
        $this->assertNotEmpty($alt2);
        $this->assertStringContainsString('digital.diplo.de/studium', $html);
    }
}
