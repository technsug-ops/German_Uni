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
