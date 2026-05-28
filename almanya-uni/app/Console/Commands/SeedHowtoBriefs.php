<?php

namespace App\Console\Commands;

use App\Models\ContentAsset;
use App\Models\ContentBrief;
use App\Services\Content\ContentGenerationService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * 4 yeni "nasıl yapılır" briefini seed eder, Gemini ile blog asset üretir, status=ready yapar.
 *
 * Sonraki adım: php artisan content:publish-blog-assets → Post'a sync.
 *
 * Source questions: storage/app/community/telegram_by_topic.json (gerçek topluluk soruları).
 */
class SeedHowtoBriefs extends Command
{
    /**
     * 4 yeni brief tanımı. Her biri Türk öğrencinin pain point'lerine odaklı.
     */
    private const BRIEFS = [
        [
            'title' => 'APS Sertifikası Türk Öğrenci Rehberi 2026 — Akademische Prüfstelle Adım Adım',
            'slug' => 'aps-sertifikasi-turk-ogrenci-rehberi-2026',
            'audience' => 'aday_ogrenci',
            'topic' => 'denklik',
            'primary_keyword' => 'aps sertifikası türkiye',
            'secondary_keywords' => ['akademische prüfstelle', 'aps türkiye 2026', 'aps başvuru', 'transkript çevirisi aps', 'aps mülakat'],
            'pain_point' => 'Türk öğrenciler APS\'i hangi durumda almak zorunda, transkript + mülakat akışı, üniversite başvurusu öncesi ne kadar sürede halletmeli? Hangi konsolosluk yapar, ücret 2026 itibariyle ne kadar, online vs yüz yüze mülakat farkı?',
            'topic_filter' => 'denklik',
            'notes' => 'APS Türkiye için yalnızca Çin/Hindistan/Vietnam zorunlu — Türk öğrenci için OPSIYONEL ama bazı üniler ister. Hangi durumda gerekli, hangi durumda gereksiz net açıkla. Pekin/İstanbul APS yapma — Türk konsolosluğu yok.',
        ],
        [
            'title' => 'Anmeldung Rehberi 2026 — Almanya\'da İlk Hafta Şehir Kayıt Adımları',
            'slug' => 'anmeldung-ilk-hafta-rehberi-2026',
            'audience' => 'mevcut_ogrenci',
            'topic' => 'anmeldung',
            'primary_keyword' => 'anmeldung nasıl yapılır',
            'secondary_keywords' => ['bürgeramt randevu', 'wohnungsgeberbestätigung', 'meldebescheinigung', 'ilk hafta almanya', '14 gün anmeldung'],
            'pain_point' => 'Almanya\'ya yeni gelen öğrenci 14 gün içinde Anmeldung yapmak zorunda ama Bürgeramt randevuları çoğu şehirde 4-6 hafta ileriye. Wohnungsgeberbestätigung\'u kiracıdan kim alır, walk-in randevu mümkün mü, geç kayıt cezası nedir?',
            'topic_filter' => 'anmeldung',
            'notes' => 'Berlin/Münih/Frankfurt randevu beklemeleri farklı. Walk-in saatleri şehir bazlı. Geç kayıt için resmi bir ceza yok ama vize uzatma için kanıt gerek. Yurt müdürü Wohnungsgeberbestätigung verir.',
        ],
        [
            'title' => 'Rundfunkbeitrag (GEZ) Öğrenci Rehberi 2026 — Muafiyet Var Mı, Nasıl Başvurulur?',
            'slug' => 'rundfunkbeitrag-gez-ogrenci-muafiyet-2026',
            'audience' => 'mevcut_ogrenci',
            'topic' => 'anmeldung',
            'primary_keyword' => 'rundfunkbeitrag öğrenci',
            'secondary_keywords' => ['gez muafiyet', '18.36 euro ayda', 'rundfunkbeitrag bafög', 'wg ortak ödeme', 'gez iptali'],
            'pain_point' => 'Anmeldung\'dan 2-4 hafta sonra otomatik gelen €18.36/ay GEZ faturası öğrencileri şaşırtıyor. BAföG alanlar muaf — ama Türk öğrenci BAföG\'a hak kazanmıyor. WG\'de ortak ödenebilir mi, geri ödememe ne olur, "Befreiung" başvurusu mümkün mü?',
            'topic_filter' => 'anmeldung',
            'notes' => '€18.36/ay = €220/yıl. BAföG sahipleri muaf. Konut WG ise tek bir kişi öder + bölüştürülür. Ödememe → mahkeme + iceberg ücret. Resmi başvuru formu rundfunkbeitrag.de\'de.',
        ],
        [
            'title' => 'TestAS Rehberi 2026 — Türk Lise Mezunu İçin Gerekli Mi, Nasıl Hazırlanılır?',
            'slug' => 'testas-turk-ogrenci-rehberi-2026',
            'audience' => 'aday_ogrenci',
            'topic' => 'denklik',
            'primary_keyword' => 'testas türk öğrenci',
            'secondary_keywords' => ['testas online', 'testas core test', 'testas matematik fen', 'testas studienkolleg alternatif', 'testas puanı yeterli'],
            'pain_point' => 'TestAS Türk lise mezunu için ZORUNLU değil ama bazı üniler istiyor ve yüksek puan = bonus puan. Hangi modül seçmeli (Mühendislik/Tıp/İktisat/Sosyal), Türkçe seçeneği var mı, online mı yüz yüze, ne kadara mal olur, kaç puan iyi?',
            'topic_filter' => 'denklik',
            'notes' => 'TestAS online 2025\'ten beri var. Türkçe dil seçeneği YAR. Core Test + 1 modül. Free Türk öğrenci için. 100/110/120 ortalama. 130+ üst düzey üni için bonus.',
        ],
        [
            'title' => 'Bürgeramt Randevusu 2026 — Berlin/Münih/Frankfurt Hızlı Yöntemler',
            'slug' => 'burgeramt-randevu-hizli-yontemler-2026',
            'audience' => 'mevcut_ogrenci',
            'topic' => 'anmeldung',
            'primary_keyword' => 'bürgeramt randevu nasıl alınır',
            'secondary_keywords' => ['bürgeramt termin trick', 'berlin meldebescheinigung', 'walk-in anmeldung', 'termin-bot ahlaki mi', 'spontantermin'],
            'pain_point' => 'Berlin\'de Bürgeramt randevuları 6-12 hafta ileride. Erken yakalamak için saat 06:00 portal kontrolü, walk-in saatleri (bazı ofisler 07:30 sırada), Termin-Bot etiği (yasak değil ama tartışmalı), küçük şehirlere gitme stratejisi. Anmeldung\'u 14 gün içinde nasıl tamamlarsın?',
            'topic_filter' => 'anmeldung',
            'notes' => 'Berlin 12 Bezirk her birinin ayrı portal — Lichtenberg, Mitte, Neukölln en zor; Spandau, Reinickendorf görece kolay. Walk-in: Pankow, Tempelhof. Termin-Bot servisleri €30-60 — yasal ama portala yük binmesi sorun.',
        ],
        [
            'title' => 'Schufa Rehberi 2026 — Türk Öğrenci İçin Kredi Notu Neden Önemli?',
            'slug' => 'schufa-turk-ogrenci-rehberi-2026',
            'audience' => 'mevcut_ogrenci',
            'topic' => 'para',
            'primary_keyword' => 'schufa nedir öğrenci',
            'secondary_keywords' => ['schufa auskunft ücretsiz', 'kein schufa wohnung', 'schufa bonitätscheck', 'yabancı schufa kayıt', 'schufa türk öğrenci'],
            'pain_point' => 'Almanya\'da yeni gelen öğrenci Schufa\'sız → ev kiralama zor, telefon kontratı reddediliyor. "Daha 1 ay oldu, Schufa nereden olacak?" Ev sahipleri "Schufa-Auskunft" ister, yoksa yüksek depozit veya kefil. İlk Schufa nasıl başlatılır, ücretsiz versiyon nedir, yabancılar için "kein Schufa" alternatifleri?',
            'topic_filter' => 'para',
            'notes' => 'Schufa BasisScore otomatik oluşur Anmeldung + banka açılışı sonrası. Ücretsiz "Datenkopie nach §15 DSGVO" yılda 1 kez. Yabancılar için ev: Wunderflats (Schufa-free), 3-6 ay depozit, garantörlü konutlar.',
        ],
        [
            'title' => 'Vize Reddi Sonrası Remonstration 2026 — Konsolosluk İtiraz Mektubu Rehberi',
            'slug' => 'vize-reddi-remonstration-itiraz-2026',
            'audience' => 'aday_ogrenci',
            'topic' => 'vize',
            'primary_keyword' => 'vize reddi sonrası ne yapmalı',
            'secondary_keywords' => ['remonstration mektubu örnek', 'vize itiraz süresi 1 ay', '36F vize red sebepleri', 'vize tekrar başvuru', 'idata vize reddi'],
            'pain_point' => 'Vize reddi mektubu geldi — "yetersiz finans", "şüpheli niyet", "evrak eksikliği" gibi belirsiz gerekçeler. 1 ay içinde Remonstration (itiraz) hakkı var ama nasıl yazılır? Yeniden başvurmak mı, itiraz etmek mi? Türk konsolosluklarındaki red oranı + itiraz başarı şansı.',
            'topic_filter' => 'vize',
            'notes' => 'Remonstration 1 ay zorunlu, ücretsiz, posta ile. 60% itiraz başarısı (red sebebine göre). Yeniden başvuru daha hızlı ama §75 ücreti yeniden. Avukat €200-500 — sadece kompleks vakalar.',
        ],
        [
            'title' => 'ZAB Diploma Denkliği 2026 — Master/PhD İçin Resmi Tanıma Süreci',
            'slug' => 'zab-diploma-denklik-master-phd-2026',
            'audience' => 'aday_ogrenci',
            'topic' => 'denklik',
            'primary_keyword' => 'zab diploma denklik',
            'secondary_keywords' => ['zentralstelle für ausländisches bildungswesen', 'statement of comparability', 'zab gutachten', 'türk lisans denklik almanya', 'phd başvuru denklik'],
            'pain_point' => 'Türk lisans/master mezunu Almanya\'da master/PhD\'ye başvurmak için diploma denkliği gerek. Bazı üniler Anabin yetiyor diyor, bazıları ZAB Zeugnisbewertung istiyor. ZAB başvurusu €200, 2-3 ay sürer. Hangi durumda zorunlu, hangi durumda opsiyonel?',
            'topic_filter' => 'denklik',
            'notes' => 'ZAB resmi Bund kuruluşu, Anabin\'den ayrı ama bağlantılı. Zeugnisbewertung 200€. Çok unili başvurularda 1 belge yetiyor. Lisans → master için genelde Anabin yeter; master → PhD için ZAB daha güvenli.',
        ],
        [
            'title' => 'Blue Card 2026 — Almanya Mezunları İçin AB Mavi Kart Rehberi',
            'slug' => 'blue-card-almanya-mezun-2026',
            'audience' => 'mevcut_ogrenci',
            'topic' => 'is',
            'primary_keyword' => 'blue card almanya başvuru',
            'secondary_keywords' => ['eu blue card mezun', 'mavi kart maaş limiti 2026', 'fachkräfteeinwanderungsgesetz', 'oturma izni mavi kart', 'aile birleşimi blue card'],
            'pain_point' => 'Almanya\'da mezun olan Türk öğrenci için iş arama 18 ay var, ama daimi kalmak için Blue Card en güçlü yol. 2026 maaş eşiği €45.300 (mark mesleklerde €41.041). Lisans yetiyor mu, yoksa master mi? Aile birleşimi avantajları, oturma süresi (33 ay), tatil bürokrasisi.',
            'topic_filter' => 'is',
            'notes' => '2024 Fachkräfteeinwanderungsgesetz reform: Blue Card 33 ay daimi oturma (Almanca B1) veya 21 ay (B1+pozisyon). Lisans veya tanınan dengi. STEM/IT/sağlık "Mangelberufe" daha düşük maaş eşiği.',
        ],
        [
            'title' => 'BAföG Alternatifleri 2026 — Türk Öğrenciye Burs ve Finansman Seçenekleri',
            'slug' => 'bafog-alternatifleri-turk-ogrenci-2026',
            'audience' => 'aday_ogrenci',
            'topic' => 'burs',
            'primary_keyword' => 'bafög alternatifleri yabancı öğrenci',
            'secondary_keywords' => ['daad burs türk öğrenci', 'deutschlandstipendium', 'erasmus+ master', 'parteistiftung burs', 'türk öğrenci finansman'],
            'pain_point' => 'BAföG Türk öğrenciye uygun değil (sadece daimi oturma + 5 yıl şartı). Peki diğer yollar? DAAD master/PhD bursları, Deutschlandstipendium €300/ay (uniden bağımsız), Erasmus+ exchange, Konrad-Adenauer/Heinrich-Böll/Friedrich-Ebert parti vakıfları. Hangisine başvurmalı, ne zaman, ne kadar şans?',
            'topic_filter' => 'burs',
            'notes' => 'DAAD master/PhD: Türk için en güçlü, €850-1.300/ay. Deutschlandstipendium: %50 not + sosyal angajman, üni başvuru. Parti vakıfları: sosyal/akademik aktif olmak şart, 2 yıllık süreç. Erasmus+: hâlâ Türk üniyle başlamış olmak gerek.',
        ],
    ];

    protected $signature = 'content:seed-howto-briefs
        {--generate-asset : Brief\'leri oluşturduktan sonra her biri için Gemini\'den blog asset üret}
        {--sleep=2 : Gemini API\'leri arası bekleme}
        {--skip-existing : Mevcut brief\'i (slug) atla}';

    protected $description = '4 nasıl-yapılır blog briefini seed eder + opsiyonel asset üretir';

    public function handle(ContentGenerationService $svc): int
    {
        $tg = json_decode(@file_get_contents(storage_path('app/community/telegram_by_topic.json')), true);
        $msgs = $tg['topics'] ?? [];

        $created = 0; $skipped = 0; $assetSuccess = 0; $assetFail = 0;

        foreach (self::BRIEFS as $def) {
            $existing = ContentBrief::where('slug', $def['slug'])->first();

            if ($existing && $this->option('skip-existing')) {
                $this->line('⏭️ ' . $def['title'] . ' — zaten var');
                $skipped++;
                continue;
            }

            // Topic'e göre telegram cache'ten 6-8 soru al
            $sourceQs = [];
            $topicMsgs = $msgs[$def['topic_filter']] ?? [];
            foreach (array_slice($topicMsgs, 0, 30) as $m) {
                $text = is_array($m) ? ($m['text'] ?? '') : (string) $m;
                $text = trim(html_entity_decode($text));
                if (str_contains(mb_strtolower($text), '?') && mb_strlen($text) > 20 && mb_strlen($text) < 250) {
                    $sourceQs[] = $text;
                }
                if (count($sourceQs) >= 7) break;
            }

            $payload = [
                'title' => $def['title'],
                'slug' => $def['slug'],
                'audience' => $def['audience'],
                'topic' => $def['topic'],
                'primary_keyword' => $def['primary_keyword'],
                'secondary_keywords' => $def['secondary_keywords'],
                'pain_point' => $def['pain_point'],
                'source_questions' => $sourceQs,
                'target_word_count' => 1500,
                'brand_tone' => 'instructive',
                'status' => 'ready',
                'notes' => $def['notes'],
            ];

            $brief = ContentBrief::updateOrCreate(['slug' => $def['slug']], $payload);
            $verb = $brief->wasRecentlyCreated ? '✅ Created' : '🔄 Updated';
            $this->info($verb . ' #' . $brief->id . ' ' . mb_substr($brief->title, 0, 55) . ' · ' . count($sourceQs) . ' Q\'s');
            $created++;

            if ($this->option('generate-asset')) {
                $this->line('   🤖 Generating blog asset...');
                $result = $svc->generateAsset($brief, 'blog');
                if ($result['success']) {
                    $result['asset']->update(['status' => 'ready']);
                    $this->info('   ✅ Asset #' . $result['asset']->id . ' (' . mb_strlen($result['asset']->body_md) . ' chars, ' . ($result['tokens']['output'] ?? '?') . ' tokens)');
                    $assetSuccess++;
                } else {
                    $this->error('   ❌ ' . ($result['error'] ?? 'unknown'));
                    $assetFail++;
                }
                sleep((int) $this->option('sleep'));
            }
        }

        $this->newLine();
        $this->info("━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("Briefs: {$created} processed, {$skipped} skipped");
        if ($this->option('generate-asset')) {
            $this->info("Assets: {$assetSuccess} success, {$assetFail} fail");
        }

        return self::SUCCESS;
    }
}
