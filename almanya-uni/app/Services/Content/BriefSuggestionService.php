<?php

namespace App\Services\Content;

use App\Models\City;
use App\Models\ContentBrief;
use App\Models\Faq;
use App\Models\FaqTopic;
use App\Models\Post;
use App\Models\Program;
use App\Models\University;
use Illuminate\Support\Facades\Http;

/**
 * Topic + audience ver → Telegram + Forum + DB context + opsiyonel Google Search → brief önerileri.
 * Açık devre: ek kullanıcı talimatı, live search grounding, format çeşitliliği.
 */
class BriefSuggestionService
{
    private const API_BASE = 'https://generativelanguage.googleapis.com/v1beta/models/';
    private const MODELS = [
        'gemini-2.5-flash',
        'gemini-2.5-flash-lite',
        'gemini-2.0-flash',
    ];

    private const CACHE_PATH = 'community/telegram_by_topic.json';
    private const FORUM_CACHE = 'community/forum_insights.json';
    private const TG_REPORT_CACHE = 'community/telegram_report.json';

    public function isConfigured(): bool
    {
        return !empty(config('services.gemini.key'));
    }

    /**
     * @param  string|null  $extraInstructions  kullanıcının ek talimatı
     * @param  bool   $liveSearch  Gemini Google Search grounding
     */
    public function suggest(
        string $topic,
        string $audience = 'aday_ogrenci',
        int $count = 10,
        ?string $extraInstructions = null,
        bool $liveSearch = false,
    ): array {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'Gemini API key yok'];
        }

        $cache = $this->loadCache();
        $questions = $cache['topics'][$topic] ?? [];
        if (empty($questions)) {
            return ['success' => false, 'error' => "Topic '$topic' için cache'de soru yok. Önce 'community:prepare-telegram' çalıştır."];
        }

        $sample = array_slice($questions, 0, 40);
        $forum = $this->loadForumInsights();
        $forumContext = $this->extractForumContext($forum, $topic);
        $dbContext = $this->loadDbContext($topic);

        $prompt = $this->buildPrompt($topic, $audience, $count, $sample, $forumContext, $dbContext, $extraInstructions);

        try {
            $response = $this->callGemini($prompt, $liveSearch);
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => substr($e->getMessage(), 0, 500)];
        }

        if (empty($response['text'])) {
            return ['success' => false, 'error' => 'Gemini boş cevap'];
        }

        $suggestions = $this->parseSuggestions($response['text']);
        if (empty($suggestions)) {
            return ['success' => false, 'error' => 'JSON parse edilemedi. Raw: ' . substr($response['text'], 0, 300)];
        }

        return [
            'success' => true,
            'suggestions' => $suggestions,
            'tokens' => [
                'input' => $response['input_tokens'],
                'output' => $response['output_tokens'],
            ],
            'grounding' => $response['grounding'] ?? null,
            'model_used' => $response['model_used'] ?? null,
        ];
    }

    /**
     * Kullanıcının yazdığı BAŞLIK'tan tek bir tam-dolu brief üret.
     *
     * AKILLI KAYNAK TARAMA:
     *   1. Title'dan anlamlı kelimeler çıkar (stop word'leri at)
     *   2. Tüm telegram havuzunu (tüm topic'ler) RELEVANCE'e göre sırala — en alakalı 35 soru
     *   3. Forum top topics + trending keywords'i title keyword'leriyle eşleştir
     *   4. AlmanyaUni DB context (mevcut FAQ/Post — duplicate önleme)
     *   5. Tüm kaynaklarla Gemini'ye prompt
     *
     * Audit trail: kaç kayıt tarandığı, kaç tanesi prompt'a girdi.
     */
    public function suggestFromTitle(string $title, ?string $topic = null, string $audience = 'aday_ogrenci'): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'Gemini API key yok'];
        }

        // 1. Title'dan anlamlı kelimeler
        $titleKeywords = $this->extractKeywords($title);

        // 2. Telegram havuzu — eski cache (1075 sample) + yeni rapor (Top500 yüksek-kalite)
        $cache = $this->loadCache();
        $tgReport = $this->loadTelegramReport();

        $allTelegramQuestions = collect($cache['topics'] ?? [])
            ->flatMap(fn ($qs, $t) => collect($qs)->map(fn ($q) => ['q' => $q, 'topic' => $t]))
            ->all();

        // Top500 raporundan da ekle (Soru + Konular alanı)
        $top500 = collect($tgReport['top500_soru'] ?? [])
            ->map(fn ($r) => ['q' => $r['Soru'] ?? '', 'topic' => $r['Konular'] ?? '', 'source' => 'top500'])
            ->filter(fn ($x) => !empty($x['q']))
            ->all();

        $combinedPool = array_merge($allTelegramQuestions, $top500);
        $rankedTg = $this->rankByRelevance($combinedPool, $titleKeywords, fn ($x) => $x['q']);
        $topTgQuestions = array_slice($rankedTg, 0, 35);

        // Eğer topic verildiyse onun da TÜM sorularını öncelikle ekle
        if ($topic && isset($cache['topics'][$topic])) {
            $topicQs = collect($cache['topics'][$topic])->take(15)->map(fn ($q) => ['score' => 999, 'q' => $q, 'topic' => $topic])->all();
            $topTgQuestions = collect($topicQs)->merge($topTgQuestions)->unique(fn ($x) => $x['q'])->take(40)->all();
        }

        // 3. Forum data — TÜM 13 katman title-relevance ile match
        $forum = $this->loadForumInsights();
        $forumTopTopics = array_slice($this->rankByRelevance($forum['top_topics'] ?? [], $titleKeywords, fn ($x) => $x['title'] ?? ''), 0, 8);
        $forumTrendingBigrams = array_slice($this->rankByRelevance($forum['trending_bigrams'] ?? [], $titleKeywords, fn ($x) => $x['term'] ?? ''), 0, 8);
        $forumTrendingUnigrams = array_slice($this->rankByRelevance($forum['trending_unigrams'] ?? [], $titleKeywords, fn ($x) => $x['term'] ?? ''), 0, 6);
        $forumTitleTrigrams = array_slice($this->rankByRelevance($forum['title_trigrams'] ?? [], $titleKeywords, fn ($x) => $x['term'] ?? ''), 0, 8);
        $forumTitleBigrams = array_slice($this->rankByRelevance($forum['title_bigrams'] ?? [], $titleKeywords, fn ($x) => $x['term'] ?? ''), 0, 8);
        $forumAllTrigrams = array_slice($this->rankByRelevance($forum['all_trigrams'] ?? [], $titleKeywords, fn ($x) => $x['term'] ?? ''), 0, 8);
        $anchorMsgCount = array_slice($this->rankByRelevance($forum['anchor_message_count'] ?? [], $titleKeywords, fn ($x) => $x['anchor'] ?? ''), 0, 8);

        // 3b. ISI HARİTASI — başlığa relevant terimleri içeren co-occurrence çiftleri
        $coOcc = $this->extractRelevantCoOccurrence($forum['anchor_co_occurrence'] ?? [], $titleKeywords, 12);

        // 4. AlmanyaUni DB context (duplicate önleme)
        $dbContext = $this->loadDbContext($topic ?: 'general');

        // 5. Prompt
        $audienceLabel = ContentBrief::AUDIENCES[$audience] ?? $audience;
        $tgBlock = "Topluluk'tan başlığa EN ALAKALI gerçek sorular (relevance-ranked):\n- " .
            implode("\n- ", array_map(fn ($x) => $x['q'] . ' [tg:' . ($x['topic'] ?? '?') . ']', $topTgQuestions));

        $forumBlock = '';
        if (!empty($forumTopTopics)) {
            $forumBlock .= "\n\n[FORUM] Başlığa en alakalı yüksek-view konular:\n";
            foreach ($forumTopTopics as $t) {
                $forumBlock .= "- \"{$t['title']}\" ({$t['views']} view)\n";
            }
        }
        if (!empty($anchorMsgCount)) {
            $forumBlock .= "\n[FORUM] Topic merkez kelimeler (mesaj sayısı):\n";
            foreach ($anchorMsgCount as $a) {
                $forumBlock .= "- \"{$a['anchor']}\" ({$a['messages']} mesaj)\n";
            }
        }
        if (!empty($coOcc)) {
            $forumBlock .= "\n🔥 KONU ISI HARİTASI — başlığa relevant terimlerin birlikte konuşulduğu konular:\n";
            foreach ($coOcc as $c) {
                $forumBlock .= "- \"{$c['a']}\" + \"{$c['b']}\" → {$c['count']} mesajda birlikte\n";
            }
        }
        if (!empty($forumTitleTrigrams)) {
            $forumBlock .= "\n[FORUM] Forum başlıklarında geçen 3-grams (başlık intent):\n";
            foreach ($forumTitleTrigrams as $t) {
                $forumBlock .= "- \"{$t['term']}\" ({$t['count']} başlık)\n";
            }
        }
        if (!empty($forumTitleBigrams)) {
            $forumBlock .= "\n[FORUM] Başlık 2-grams:\n";
            foreach ($forumTitleBigrams as $t) {
                $forumBlock .= "- \"{$t['term']}\" ({$t['count']})\n";
            }
        }
        if (!empty($forumAllTrigrams)) {
            $forumBlock .= "\n[FORUM] Gövde 3-grams (phrase intent):\n";
            foreach ($forumAllTrigrams as $t) {
                $forumBlock .= "- \"{$t['term']}\" ({$t['count']})\n";
            }
        }
        if (!empty($forumTrendingBigrams)) {
            $forumBlock .= "\n[FORUM] Son 12 ay yükselen 2-grams (lift):\n";
            foreach ($forumTrendingBigrams as $kw) {
                $forumBlock .= "- \"{$kw['term']}\" ({$kw['lift']}× lift)\n";
            }
        }
        if (!empty($forumTrendingUnigrams)) {
            $forumBlock .= "\n[FORUM] Son 12 ay yükselen tek kelimeler:\n";
            foreach ($forumTrendingUnigrams as $kw) {
                $forumBlock .= "- \"{$kw['term']}\" ({$kw['lift']}× lift)\n";
            }
        }

        $dbBlock = '';
        if (!empty($dbContext['existing_faqs'])) {
            $dbBlock .= "\n\n📚 ALMANYAUNI'DE ZATEN VAR (duplicate olma, farklı açı bul):\nMevcut FAQ:\n- " .
                implode("\n- ", array_slice($dbContext['existing_faqs'], 0, 15));
        }
        if (!empty($dbContext['existing_posts'])) {
            $dbBlock .= "\nMevcut blog:\n- " . implode("\n- ", $dbContext['existing_posts']);
        }

        $topicValue = $topic ?: 'belirsiz (sen tahmin et — telegram source\'larındaki topic etiketlerine bak)';

        $prompt = <<<TXT
Sen bir SEO ve içerik stratejisi uzmanısın. AlmanyaUni projesi (Türk öğrencilerin Almanya rehberi) için brief üretiyorsun.

BAŞLIK: "$title"
HEDEF KİTLE: $audienceLabel
TOPIC: $topicValue
TARİH: {$this->today()}
Title'dan çıkarılan kelimeler: {$this->kwList($titleKeywords)}

KAYNAK 1 — TELEGRAM (142K mesajdan title-relevance ile süzülmüş):

$tgBlock$forumBlock$dbBlock

GÖREV: Bu başlığa uygun, EKSİKSİZ bir brief üret.

ÇIKTI (SADECE JSON):

{
  "primary_keyword": "Türkçe long-tail (3-6 kelime, başlıkla uyumlu)",
  "secondary_keywords": ["kw1", "kw2", "kw3", "kw4", "kw5"],
  "pain_point": "Topluluk'taki konfüzyonu özetleyen 1-2 cümle. Yukarıdaki telegram/forum verilerinden damıt.",
  "source_questions": ["Yukarıdaki telegram sorularından SEÇTİĞİN 3-5 tanesi (tam metni). Uydurma!"],
  "target_word_count": 1500,
  "brand_tone": "TEK string (pipe yok). Seç: instructive | casual | formal | inspirational",
  "content_format": "TEK string. Seç: how_to | listicle | comparison | case_study | deep_dive | myth_busting | checklist | interview | news_analysis | calculator_tool | data_driven",
  "topic_suggestion": "TEK string. Seç: vize | dil | para | randevu | uni_assist | yurt | sehir | master | sigorta | studienkolleg | denklik | is | anmeldung | burs",
  "audience_suggestion": "TEK string. Seç: aday_ogrenci | veli | mevcut_ogrenci | phd_adayi | genel",
  "notes": "Stratejik not (1-2 cümle — neden bu format/açı, AlmanyaUni'de farkı ne)"
}

KURALLAR:
- TR doğal dili (Sperrkonto, idata, VPD, anabin)
- source_questions YUKARIDAKİ ham telegram sorularından SEÇ (uydurma!)
- topic_suggestion'ı telegram source'undaki [tg:topic] etiketlerinden ipucu al
- AlmanyaUni FAQ'larına duplicate olma
- Halüsinasyon YOK — kesin bilgi
TXT;

        try {
            $resp = $this->callGemini($prompt, false);
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => substr($e->getMessage(), 0, 500)];
        }

        $brief = json_decode($resp['text'] ?? '', true);
        if (!is_array($brief)) {
            $clean = preg_replace('/^```(?:json)?\s*|\s*```$/u', '', trim($resp['text'] ?? ''));
            $brief = json_decode($clean, true);
        }

        if (!is_array($brief) || empty($brief['primary_keyword'])) {
            return ['success' => false, 'error' => 'JSON parse hatası. Raw: ' . substr($resp['text'] ?? '', 0, 300)];
        }

        return [
            'success' => true,
            'brief' => $brief,
            'tokens' => [
                'input' => $resp['input_tokens'] ?? 0,
                'output' => $resp['output_tokens'] ?? 0,
            ],
            'sources_used' => [
                'title_keywords' => $titleKeywords,
                'telegram_pool_size' => count($combinedPool),
                'telegram_old_cache' => count($allTelegramQuestions),
                'telegram_top500_report' => count($top500),
                'telegram_in_prompt' => count($topTgQuestions),
                'forum_top_topics' => count($forumTopTopics),
                'forum_anchor_msg_count' => count($anchorMsgCount),
                'forum_heatmap_pairs' => count($coOcc),
                'forum_title_trigrams' => count($forumTitleTrigrams),
                'forum_title_bigrams' => count($forumTitleBigrams),
                'forum_all_trigrams' => count($forumAllTrigrams),
                'forum_trending_bigrams' => count($forumTrendingBigrams),
                'forum_trending_unigrams' => count($forumTrendingUnigrams),
                'almanyauni_faqs_context' => count($dbContext['existing_faqs'] ?? []),
                'almanyauni_posts_context' => count($dbContext['existing_posts'] ?? []),
            ],
        ];
    }

    /**
     * Konu ısı haritası — başlık keyword'lerinde geçen anchor'ları içeren co-occurrence çiftleri.
     */
    private function extractRelevantCoOccurrence(array $cooc, array $keywords, int $limit): array
    {
        if (empty($keywords)) return [];

        $matchAny = function (string $term, array $keywords): bool {
            $t = mb_strtolower($term);
            foreach ($keywords as $kw) {
                if (mb_strpos($t, $kw) !== false) return true;
            }
            return false;
        };

        $relevant = [];
        foreach ($cooc as $pair) {
            $hitA = $matchAny($pair['a'] ?? '', $keywords);
            $hitB = $matchAny($pair['b'] ?? '', $keywords);
            if ($hitA || $hitB) {
                $relevant[] = $pair;
            }
        }
        return array_slice($relevant, 0, $limit);
    }

    /**
     * Title'dan anlamlı kelimeler çıkar (stop word'leri at).
     */
    private function extractKeywords(string $title): array
    {
        $stop = ['ne', 'nasıl', 'nedir', 'için', 'ile', 'mı', 'mi', 'mu', 'mü',
            've', 'veya', 'ama', 'fakat', 'bu', 'şu', 'o', 'bir', 'çok',
            'da', 'de', 'den', 'dan', 'a', 'e', 'i', 'ı', 'kadar', 'beri',
            'sonra', 'önce', 'olarak', 'gibi', 'mal', 'olur', 'edilir'];

        $words = preg_split('/[\s,\.\-\?\!\(\)\/\:\;\"\'üöäçşğıİÖÜÄÇŞĞ]+/u', mb_strtolower($title));
        $words = array_filter($words, fn ($w) => mb_strlen($w) >= 3 && !in_array($w, $stop, true));
        return array_values(array_unique($words));
    }

    private function kwList(array $kws): string
    {
        return $kws ? implode(', ', $kws) : '(yok)';
    }

    /**
     * Bir listede her item için keyword match sayısına göre relevance skoru ver, sırala.
     *
     * @param  array  $items  herhangi liste
     * @param  array  $keywords  arama kelimeleri (lowercase)
     * @param  callable  $textExtractor  $item → string (üzerinde match yapılacak text)
     */
    private function rankByRelevance(array $items, array $keywords, callable $textExtractor): array
    {
        if (empty($keywords) || empty($items)) {
            return [];
        }
        $scored = [];
        foreach ($items as $item) {
            $text = mb_strtolower($textExtractor($item));
            $score = 0;
            foreach ($keywords as $kw) {
                if (mb_strpos($text, $kw) !== false) {
                    $score++;
                }
            }
            if ($score > 0) {
                $scored[] = ['score' => $score] + (is_array($item) ? $item : ['_value' => $item]);
            }
        }
        usort($scored, fn ($a, $b) => $b['score'] - $a['score']);
        return $scored;
    }

    /**
     * AlmanyaUni'nin kendi DB'sinden ilgili topic için bağlam yükle.
     * Duplicate önleme + sayısal context.
     */
    private function loadDbContext(string $topic): array
    {
        // Topic → FaqTopic slug mapping
        $faqTopicSlug = match ($topic) {
            'master' => 'master',
            'uni_assist' => 'uni-assist',
            default => $topic,
        };

        try {
            $existingFaqs = Faq::query()
                ->where('is_published', true)
                ->where('has_answer', true)
                ->whereHas('topic', fn ($q) => $q->where('slug', $faqTopicSlug))
                ->orderByDesc('view_count')
                ->limit(20)
                ->pluck('question')
                ->all();

            $existingPosts = Post::query()
                ->published()
                ->orderByDesc('published_at')
                ->limit(15)
                ->pluck('title')
                ->all();

            $topCities = City::has('universities')
                ->withCount('universities')
                ->orderByDesc('universities_count')
                ->take(5)
                ->pluck('name_de')
                ->implode(', ');

            $siteStats = [
                'unis' => University::where('is_official', 1)->count(),
                'programs' => Program::where('is_active', 1)->count(),
                'cities' => City::count(),
                'top_cities' => $topCities,
            ];
        } catch (\Throwable $e) {
            return ['existing_faqs' => [], 'existing_posts' => [], 'site_stats' => []];
        }

        return [
            'existing_faqs' => $existingFaqs,
            'existing_posts' => $existingPosts,
            'site_stats' => $siteStats,
        ];
    }

    private function buildPrompt(
        string $topic,
        string $audience,
        int $count,
        array $questions,
        array $forumContext = [],
        array $dbContext = [],
        ?string $extraInstructions = null,
    ): string {
        $audienceLabel = ContentBrief::AUDIENCES[$audience] ?? $audience;
        $questionsBlock = "- " . implode("\n- ", $questions);

        $forumBlock = '';
        if (!empty($forumContext['top_titles'])) {
            $forumBlock .= "\n\nKAYNAK 2 — FORUM (DeutschStudent, 120K mesaj, 5 yıllık): Bu topic'le ilgili EN ÇOK GÖRÜNTÜLENEN gerçek forum konuları:\n";
            foreach ($forumContext['top_titles'] as $t) {
                $forumBlock .= "- \"{$t['title']}\" ({$t['views']} view, {$t['replies']} cevap)\n";
            }
        }
        if (!empty($forumContext['trending'])) {
            $forumBlock .= "\nForum'da SON 12 AY YÜKSELEN keyword'ler (lift = artış katı):\n";
            foreach ($forumContext['trending'] as $kw) {
                $forumBlock .= "- \"{$kw['term']}\" ({$kw['lift']}× lift, recent={$kw['recent_count']})\n";
            }
        }
        if (!empty($forumContext['title_keywords'])) {
            $forumBlock .= "\nForum başlıklarında sık geçen kelime kalıpları:\n";
            foreach ($forumContext['title_keywords'] as $tk) {
                $forumBlock .= "- \"{$tk['term']}\" ({$tk['count']} başlık)\n";
            }
        }

        $dbBlock = '';
        if (!empty($dbContext['existing_faqs'])) {
            $dbBlock .= "\n\n📚 ALMANYAUNI'DE ZATEN VAR (bu konuları TEKRARLAMA, farklı açı bul):\n";
            $dbBlock .= "Mevcut FAQ'lar:\n- " . implode("\n- ", $dbContext['existing_faqs']);
        }
        if (!empty($dbContext['existing_posts'])) {
            $dbBlock .= "\nMevcut blog yazıları:\n- " . implode("\n- ", $dbContext['existing_posts']);
        }
        if (!empty($dbContext['site_stats'])) {
            $s = $dbContext['site_stats'];
            $dbBlock .= "\n\n🏛 ALMANYAUNI VERİTABANI (referans/sayı vermek için):\n";
            $dbBlock .= "- {$s['unis']} resmi Hochschule, {$s['programs']} program, {$s['cities']} şehir\n";
            $dbBlock .= "- En çok üni olan şehirler: {$s['top_cities']}";
        }

        $extraBlock = '';
        if ($extraInstructions) {
            $extraBlock = "\n\n⚙️ KULLANICI EK TALİMATI (en yüksek öncelikli, mutlaka uy):\n" . trim($extraInstructions);
        }

        return <<<TXT
Sen bir SEO ve içerik stratejisi uzmanısın. AlmanyaUni projesi (Türk öğrencilerin Almanya rehberi) için brief önerileri üreteceksin.

KONU: $topic
HEDEF KİTLE: $audienceLabel
TARİH: {$this->today()}

KAYNAK 1 — TELEGRAM (142K mesaj, 2021-2026): Bu topic'te kullanıcıların DOĞAL OLARAK sorduğu gerçek sorular:

$questionsBlock$forumBlock$dbBlock$extraBlock

GÖREV:
Telegram + Forum + AlmanyaUni DB context'i çapraz analiz et. AlmanyaUni'de zaten var olan FAQ/Blog'lara DUPLICATE OLMA. $count adet farklı, birbirinden bağımsız içerik konusu öner.

ZORUNLU FORMAT ÇEŞİTLİLİĞİ (aynı format'tan en fazla 2 öneri, kalanlar farklı):
- "how_to" — Adım adım rehber
- "listicle" — "X şey", "5 hata" gibi numaralı liste
- "comparison" — A vs B vs C karşılaştırma
- "case_study" — Gerçek deneyim/öyküleme (1 öğrencinin başına gelenler)
- "deep_dive" — Konuya derinlemesine teknik açıklama
- "myth_busting" — Yanlış bilinenler / efsane çürütme
- "checklist" — Kontrol listesi (do/don't, gerekli evraklar)
- "interview" — Soru-cevap formatı (uzman/öğrenci röportajı)
- "news_analysis" — Güncel değişiklik (yeni vize kuralı, kota değişikliği)
- "calculator_tool" — Hesaplama + araç sayfası önerisi
- "data_driven" — Sayı/istatistik odaklı

KURALLAR:
- Her öneri yüksek arama hacmi/intent'e sahip olmalı.
- Niş alt konular (örn. vize'de değil → "Sperrkonto okul ücreti dahil mi", "idata randevu yoğunluk", "EU/EEA vize farkı").
- Forum'daki yüksek-view + telegram sıkça-sorulan **kesişimi** kanıtlanmış demand.
- Türk öğrencinin gerçek kelimelerini kullan (Sperrkonto, idata, VPD, anabin, Coracle).
- pain_point: Topluluk konfüzyonunu özetle.
- source_questions: HAM telegram sorusundan 2-4 örnek (tam metin).
- primary_keyword: Türkçe long-tail (3-6 kelime).
- secondary_keywords: 3-5 ek kelime.
- unique_angle: AlmanyaUni'de zaten olanlardan farkı (1 cümle).

ÇIKTI FORMATI (saf JSON array):

[
  {
    "title": "Başlık (60-80 char)",
    "slug": "kebab-case-slug",
    "content_format": "how_to|listicle|comparison|case_study|deep_dive|myth_busting|checklist|interview|news_analysis|calculator_tool|data_driven",
    "primary_keyword": "Türkçe long-tail",
    "secondary_keywords": ["kw1", "kw2", "kw3"],
    "pain_point": "1-2 cümle, topluluk'tan damıtılmış",
    "source_questions": ["ham soru 1", "ham soru 2"],
    "search_intent": "informational|navigational|transactional",
    "target_word_count": 1200,
    "unique_angle": "Bu önerinin farkı (1 cümle)"
  }
]

SADECE JSON array. Markdown/açıklama YOK.
TXT;
    }

    private function today(): string
    {
        return now()->format('Y-m-d');
    }

    private function callGemini(string $prompt, bool $liveSearch = false): array
    {
        $lastError = null;

        foreach (self::MODELS as $model) {
            $url = self::API_BASE . $model . ':generateContent';

            for ($attempt = 0; $attempt < 3; $attempt++) {
                try {
                    $payload = [
                        'contents' => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
                        'generationConfig' => [
                            'temperature' => 0.7,
                            'maxOutputTokens' => 16384,
                            'topP' => 0.95,
                        ],
                    ];

                    // Gemini 2.5 thinking model'leri thinking-token'larıyla maxOutputTokens
                    // bütçesini yiyip JSON çıktısını TRUNCATE ediyordu (→ "JSON parse edilemedi").
                    // Structured öneri için derin düşünme gereksiz → thinking'i kapat, tüm bütçe
                    // çıktıya gitsin. (2.0 modelleri bu alanı desteklemez → sadece 2.5'e uygula.)
                    if (str_starts_with($model, 'gemini-2.5')) {
                        $payload['generationConfig']['thinkingConfig'] = ['thinkingBudget' => 0];
                    }

                    // liveSearch: Google Search grounding (tools ile JSON mode birlikte çalışmaz, manuel parse)
                    if ($liveSearch) {
                        $payload['tools'] = [['google_search' => new \stdClass()]];
                    } else {
                        $payload['generationConfig']['responseMimeType'] = 'application/json';
                    }

                    $resp = Http::asJson()
                        ->timeout(180)
                        ->withHeaders(['x-goog-api-key' => config('services.gemini.key')])
                        ->post($url, $payload);

                    if ($resp->ok()) {
                        $data = $resp->json();
                        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                        $usage = $data['usageMetadata'] ?? [];
                        $grounding = $data['candidates'][0]['groundingMetadata'] ?? null;
                        return [
                            'text' => $text ? trim($text) : null,
                            'input_tokens' => (int) ($usage['promptTokenCount'] ?? 0),
                            'output_tokens' => (int) ($usage['candidatesTokenCount'] ?? 0),
                            'model_used' => $model,
                            'grounding' => $grounding,
                        ];
                    }

                    $status = $resp->status();
                    $body = substr($resp->body(), 0, 200);
                    $lastError = "Model=$model attempt=" . ($attempt + 1) . " HTTP $status: $body";

                    if ($status === 503 || $status === 429 || $status >= 500) {
                        if ($attempt < 2) {
                            sleep(2 << $attempt);
                            continue;
                        }
                        break;
                    }
                    throw new \RuntimeException("Model=$model HTTP $status: $body");
                } catch (\Throwable $e) {
                    $lastError = "Model=$model attempt=" . ($attempt + 1) . " exception: " . $e->getMessage();
                    if ($attempt < 2) {
                        sleep(2 << $attempt);
                        continue;
                    }
                }
            }
        }

        throw new \RuntimeException("Tüm modeller başarısız. Son hata: $lastError");
    }

    private function parseSuggestions(string $jsonText): array
    {
        $arr = json_decode($jsonText, true);
        if (is_array($arr) && !empty($arr)) {
            return array_values(array_filter($arr, fn ($x) => is_array($x) && !empty($x['title'])));
        }

        // Code block içindeyse temizle
        $clean = preg_replace('/^```(?:json)?\s*|\s*```$/u', '', trim($jsonText));
        $arr = json_decode($clean, true);
        if (is_array($arr) && !empty($arr)) {
            return array_values(array_filter($arr, fn ($x) => is_array($x) && !empty($x['title'])));
        }

        // Free-form text içinden JSON array çıkar (live search grounding'de markdown gelebilir)
        if (preg_match('/\[\s*\{.*?\}\s*\]/us', $jsonText, $m)) {
            $arr = json_decode($m[0], true);
            if (is_array($arr) && !empty($arr)) {
                return array_values(array_filter($arr, fn ($x) => is_array($x) && !empty($x['title'])));
            }
        }

        return [];
    }

    private function loadCache(): array
    {
        try {
            $path = storage_path('app/' . self::CACHE_PATH);
            if (!is_file($path) || !is_readable($path)) return ['topics' => []];
            $decoded = json_decode((string) @file_get_contents($path), true);
            return is_array($decoded) ? $decoded + ['topics' => []] : ['topics' => []];
        } catch (\Throwable $e) {
            return ['topics' => []];
        }
    }

    private function loadForumInsights(): array
    {
        try {
            $path = storage_path('app/' . self::FORUM_CACHE);
            if (!is_file($path) || !is_readable($path)) return [];
            $decoded = json_decode((string) @file_get_contents($path), true);
            return is_array($decoded) ? $decoded : [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Tüm telegram_report_*.json dosyalarını oku ve birleştir.
     * Eski tek-cache (TG_REPORT_CACHE) fallback olarak destekli.
     */
    private function loadTelegramReport(): array
    {
        $dir = storage_path('app/community');
        $files = glob("$dir/telegram_report_*.json") ?: [];

        // Eski tek-cache fallback
        if (empty($files) && is_file(storage_path('app/' . self::TG_REPORT_CACHE))) {
            $files = [storage_path('app/' . self::TG_REPORT_CACHE)];
        }

        $combined = [
            'top500_soru' => [],
            'konular' => [],
            'gruplar' => [],
            'reports' => [],
        ];

        foreach ($files as $file) {
            $r = json_decode(file_get_contents($file), true) ?? [];
            $name = $r['name'] ?? basename($file, '.json');
            $combined['reports'][$name] = [
                'top500_count' => count($r['top500_soru'] ?? []),
                'gruplar_count' => count($r['gruplar'] ?? []),
                'aylik_count' => count($r['aylik'] ?? []),
            ];

            foreach ($r['top500_soru'] ?? [] as $row) {
                if (empty($row['Soru'])) continue;
                $combined['top500_soru'][] = [
                    'Soru' => $row['Soru'],
                    'Konular' => $row['Konular'] ?? '',
                    'Tarih' => $row['Tarih'] ?? '',
                    'Grup' => $row['Grup'] ?? '',
                    '_report' => $name,
                ];
            }
            foreach ($r['konular'] ?? [] as $row) {
                $combined['konular'][] = $row + ['_report' => $name];
            }
            foreach ($r['gruplar'] ?? [] as $row) {
                $combined['gruplar'][] = $row + ['_report' => $name];
            }
        }

        return $combined;
    }

    private function extractForumContext(array $forum, string $topic): array
    {
        if (empty($forum)) return [];

        $topicKeywords = [
            'vize' => ['vize', 'visa', 'idata', 'konsoloslu', 'randevu', 'bloke', 'sperrkonto'],
            'dil' => ['dil kursu', 'almanca', 'testdaf', 'dsh', 'goethe', 'b2', 'c1', 'telc', 'ielts'],
            'para' => ['bloke', 'sperrkonto', 'finans', 'para', 'masraf', 'maliyet', 'expatrio', 'coracle'],
            'randevu' => ['randevu', 'idata', 'konsoloslu'],
            'uni_assist' => ['uni assist', 'uni-assist', 'vpd', 'başvuru'],
            'yurt' => ['yurt', 'studentenwohnheim', 'wg', 'ev', 'kalacak', 'kira'],
            'sehir' => ['berlin', 'münih', 'münchen', 'hamburg', 'köln', 'frankfurt', 'eyalet'],
            'master' => ['master', 'yüksek lisans', 'msc', 'lisansüstü'],
            'sigorta' => ['sigorta', 'sağlık', 'krankenversicherung', 'tk', 'aok'],
            'studienkolleg' => ['studienkolleg', 'şartlı kabul', 'feststellung'],
            'denklik' => ['denklik', 'anabin', 'aps', 'diploma'],
            'is' => ['iş', 'çalışma', 'werkstudent', 'part time', 'minijob'],
            'anmeldung' => ['anmeldung', 'kayıt', 'oturum', 'aufenthalt'],
            'burs' => ['burs', 'daad', 'stipendium', 'scholarship'],
        ];
        $needles = $topicKeywords[$topic] ?? [$topic];

        $matchAny = function (string $text, array $needles): bool {
            $lower = mb_strtolower($text);
            foreach ($needles as $n) {
                if (mb_strpos($lower, mb_strtolower($n)) !== false) return true;
            }
            return false;
        };

        return [
            'top_titles' => collect($forum['top_topics'] ?? [])->filter(fn ($t) => $matchAny($t['title'] ?? '', $needles))->take(8)->values()->all(),
            'trending' => collect($forum['trending_keywords'] ?? [])->filter(fn ($k) => $matchAny($k['term'] ?? '', $needles))->take(10)->values()->all(),
            'title_keywords' => collect($forum['title_keywords'] ?? [])->filter(fn ($k) => $matchAny($k['term'] ?? '', $needles))->take(8)->values()->all(),
        ];
    }

    public function availableTopics(): array
    {
        $cache = $this->loadCache();
        return array_keys($cache['topics'] ?? []);
    }

    /**
     * Kaynak veri özeti — ASLA exception fırlatmaz. Dosya yoksa/bozuksa veya DB
     * erişilemezse her alan 0/güvenli fallback döner (sayfa render'ı patlamaz).
     */
    public function stats(): array
    {
        $tg = $this->loadCache();
        $fm = $this->loadForumInsights();

        $topics = (array) ($tg['topics'] ?? []);

        $dbCount = function (callable $query): int {
            try {
                return (int) $query();
            } catch (\Throwable $e) {
                return 0;
            }
        };

        return [
            'telegram_topics' => count($topics),
            'telegram_total_questions' => collect($topics)->sum(fn ($v) => is_countable($v) ? count($v) : 0),
            'forum_top_topics' => count((array) ($fm['top_topics'] ?? [])),
            'forum_trending_keywords' => count((array) ($fm['trending_keywords'] ?? [])),
            'forum_categories' => count((array) ($fm['category_distribution'] ?? [])),
            'almanyauni_faqs' => $dbCount(fn () => Faq::published()->answered()->count()),
            'almanyauni_posts' => $dbCount(fn () => Post::published()->count()),
            'almanyauni_unis' => $dbCount(fn () => University::where('is_official', 1)->count()),
        ];
    }
}
