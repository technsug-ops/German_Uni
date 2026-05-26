<?php

namespace App\Services\Content;

/**
 * Forum (DeutschStudent, 120K mesaj) + Telegram (142K + 716K mesaj rapor) cache'lerini
 * okur ve verilen şehir/üni/topic adına göre EN ALAKALI içeriği döner.
 *
 * Enrichment ve Brief Suggestion servislerinin ortak veri kaynağı.
 */
class CommunityInsightsService
{
    private const TG_TOPIC_CACHE = 'community/telegram_by_topic.json';
    private const FORUM_CACHE = 'community/forum_insights.json';

    /**
     * Bir entity (şehir adı, üni adı, konu) için ilgili topluluk içgörülerini topla.
     *
     * @return array{
     *     telegram_questions: array<int, array{q:string, topic:string}>,
     *     forum_titles: array<int, array{title:string, views:int, replies:int}>,
     *     anchor_msg_count: array<int, array{anchor:string, messages:int}>,
     *     co_occurrence: array<int, array{a:string, b:string, count:int}>,
     *     trending_bigrams: array<int, array{term:string, lift:float}>,
     *     trending_unigrams: array<int, array{term:string, lift:float}>,
     *     stats: array{tg_pool:int, forum_layers:int}
     * }
     */
    public function getInsightsFor(string $entityName, int $tgLimit = 15, int $forumLimit = 8): array
    {
        $keywords = $this->extractKeywords($entityName);

        // Telegram pool: topic_cache + tüm telegram_report'lar
        $tgCache = $this->loadJson(self::TG_TOPIC_CACHE) ?? ['topics' => []];
        $tgReports = $this->loadTelegramReports();

        $allTgQuestions = collect($tgCache['topics'] ?? [])
            ->flatMap(fn ($qs, $topic) => collect($qs)->map(fn ($q) => ['q' => $q, 'topic' => $topic]))
            ->all();

        $top500 = collect($tgReports['top500_soru'] ?? [])
            ->map(fn ($r) => ['q' => $r['Soru'] ?? '', 'topic' => $r['Konular'] ?? ''])
            ->filter(fn ($x) => !empty($x['q']))
            ->all();

        $tgPool = array_merge($allTgQuestions, $top500);

        $rankedTg = $this->rankByRelevance($tgPool, $keywords, fn ($x) => $x['q']);
        $topTgQuestions = array_slice($rankedTg, 0, $tgLimit);

        // Forum: tüm katmanlar
        $forum = $this->loadJson(self::FORUM_CACHE) ?? [];

        $forumTopTitles = array_slice(
            $this->rankByRelevance($forum['top_topics'] ?? [], $keywords, fn ($x) => $x['title'] ?? ''),
            0, $forumLimit
        );

        // URL'i olan forum konularını filtrele (ayrı blok için)
        $forumTopicsWithUrl = array_values(array_filter(
            $forumTopTitles,
            fn ($t) => !empty($t['url'] ?? '')
        ));

        $anchorMsgCount = array_slice(
            $this->rankByRelevance($forum['anchor_message_count'] ?? [], $keywords, fn ($x) => $x['anchor'] ?? ''),
            0, 6
        );

        $coOcc = $this->extractRelevantCoOccurrence($forum['anchor_co_occurrence'] ?? [], $keywords, 8);

        $trendingBigrams = array_slice(
            $this->rankByRelevance($forum['trending_bigrams'] ?? [], $keywords, fn ($x) => $x['term'] ?? ''),
            0, 6
        );
        $trendingUnigrams = array_slice(
            $this->rankByRelevance($forum['trending_unigrams'] ?? [], $keywords, fn ($x) => $x['term'] ?? ''),
            0, 5
        );

        return [
            'telegram_questions' => $topTgQuestions,
            'forum_titles' => $forumTopTitles,
            'forum_topics_with_url' => $forumTopicsWithUrl,
            'anchor_msg_count' => $anchorMsgCount,
            'co_occurrence' => $coOcc,
            'trending_bigrams' => $trendingBigrams,
            'trending_unigrams' => $trendingUnigrams,
            'stats' => [
                'tg_pool' => count($tgPool),
                'forum_layers' => count(array_filter($forum)),
            ],
            'keywords_used' => $keywords,
        ];
    }

    /**
     * Bu içgörüleri AI prompt'una eklenebilir formatlanmış string'e dönüştür.
     */
    public function formatForPrompt(array $insights): string
    {
        if (empty($insights['telegram_questions']) && empty($insights['forum_titles'])) {
            return '';
        }

        $out = "\n\n━━━ TOPLULUK İÇGÖRÜLERİ (Forum 120K + Telegram 142K + visa/denklik 716K mesaj) ━━━\n";
        $out .= "Bunlar gerçek Türk öğrencilerin sorduğu/tartıştığı şeyler — sayfa içeriğine MUTLAKA yansıt:\n";

        if (!empty($insights['telegram_questions'])) {
            $out .= "\n🗣️ Telegram'da bu konuda sorulan gerçek sorular (FAQ bölümünde mutlaka cevapla):\n";
            foreach ($insights['telegram_questions'] as $q) {
                $topic = !empty($q['topic']) ? " [tg:{$q['topic']}]" : '';
                $out .= "- {$q['q']}{$topic}\n";
            }
        }

        if (!empty($insights['forum_titles'])) {
            $out .= "\n📋 Forum'da yüksek-görüntülenen konular (intro/section'lara yansıt):\n";
            foreach ($insights['forum_titles'] as $t) {
                $out .= "- \"{$t['title']}\" ({$t['views']} view, {$t['replies']} cevap)\n";
            }
        }

        if (!empty($insights['anchor_msg_count'])) {
            $out .= "\n🎯 Forum'da topluluk merkez kelimeler:\n";
            foreach ($insights['anchor_msg_count'] as $a) {
                $out .= "- \"{$a['anchor']}\" ({$a['messages']} mesaj)\n";
            }
        }

        if (!empty($insights['co_occurrence'])) {
            $out .= "\n🔥 Konu ısı haritası — bu entity ile birlikte konuşulan başlıklar:\n";
            foreach ($insights['co_occurrence'] as $c) {
                $out .= "- \"{$c['a']}\" + \"{$c['b']}\" → {$c['count']} mesaj\n";
            }
        }

        if (!empty($insights['trending_bigrams'])) {
            $out .= "\n📈 Son 12 ay yükselen kalıplar:\n";
            foreach ($insights['trending_bigrams'] as $kw) {
                $out .= "- \"{$kw['term']}\" ({$kw['lift']}× lift)\n";
            }
        }

        $out .= "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        return $out;
    }

    // ─────────────────────────────────────────────────────────────────────

    private function extractKeywords(string $text): array
    {
        $stop = ['ne', 'nasıl', 'nedir', 'için', 'ile', 'mı', 'mi', 'mu', 'mü',
            've', 'veya', 'ama', 'fakat', 'bu', 'şu', 'o', 'bir', 'çok',
            'da', 'de', 'den', 'dan', 'a', 'e', 'i', 'ı', 'kadar',
            'sonra', 'önce', 'olarak', 'gibi',
            'hochschule', 'universität', 'university', 'üniversite', 'üniversitesi',
            'der', 'die', 'das', 'in', 'am', 'an',
            'technische', 'angewandte', 'wissenschaften',
            // şehir entity'lerindeki bazı genel suffixler
            'an', 'der', 'main', 'rhein',
        ];

        $words = preg_split('/[\s,\.\-\?\!\(\)\/\:\;\"\']+/u', mb_strtolower($text));
        $words = array_filter($words, fn ($w) => mb_strlen($w) >= 3 && !in_array($w, $stop, true));

        // Ana entity adı (en uzun parça) mutlaka kalır — generic stop'a yakalanabilen kısa adlar için (örn. "Ulm")
        $main = preg_split('/[\s,\.\-]+/u', mb_strtolower($text));
        $main = array_filter($main, fn ($w) => mb_strlen($w) >= 2 && !in_array($w, ['der', 'die', 'das', 'am', 'an', 'in', 'auf'], true));

        return array_values(array_unique(array_merge($words, $main)));
    }

    private function rankByRelevance(array $items, array $keywords, callable $textExtractor): array
    {
        if (empty($keywords) || empty($items)) return [];

        $scored = [];
        foreach ($items as $item) {
            $text = mb_strtolower((string) $textExtractor($item));
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

    private function extractRelevantCoOccurrence(array $cooc, array $keywords, int $limit): array
    {
        if (empty($keywords)) return [];

        $matchAny = function (string $term) use ($keywords): bool {
            $t = mb_strtolower($term);
            foreach ($keywords as $kw) {
                if (mb_strpos($t, $kw) !== false) return true;
            }
            return false;
        };

        $relevant = [];
        foreach ($cooc as $pair) {
            if ($matchAny($pair['a'] ?? '') || $matchAny($pair['b'] ?? '')) {
                $relevant[] = $pair;
            }
        }
        return array_slice($relevant, 0, $limit);
    }

    private function loadJson(string $relative): ?array
    {
        $path = storage_path('app/' . $relative);
        if (!is_file($path)) return null;
        $data = json_decode(file_get_contents($path), true);
        return is_array($data) ? $data : null;
    }

    private function loadTelegramReports(): array
    {
        $dir = storage_path('app/community');
        $files = glob("$dir/telegram_report_*.json") ?: [];

        $combined = ['top500_soru' => []];
        foreach ($files as $file) {
            $r = json_decode(file_get_contents($file), true) ?? [];
            foreach ($r['top500_soru'] ?? [] as $row) {
                if (!empty($row['Soru'])) {
                    $combined['top500_soru'][] = $row;
                }
            }
        }
        return $combined;
    }
}
