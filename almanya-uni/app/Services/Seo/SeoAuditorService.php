<?php

namespace App\Services\Seo;

use App\Models\SeoAudit;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Tek bir page template'i için SEO audit:
 *   1. Sample URL'i HTTP GET ile fetch
 *   2. HTML'den text çıkar (strip + clean)
 *   3. Forum trending + telegram top keyword'lerle karşılaştır
 *   4. Eksik keyword'leri tespit et + opportunity score
 *   5. AI ile section önerisi üret (opsiyonel)
 */
class SeoAuditorService
{
    private const KEYWORD_SOURCES = [
        // Forum kaynak yapısı: ['file_key', 'kw_field', 'count_field', 'weight']
        ['forum', 'title_trigrams', 'term', 'count', 5],
        ['forum', 'title_bigrams', 'term', 'count', 4],
        ['forum', 'all_trigrams', 'term', 'count', 3],
        ['forum', 'all_bigrams', 'term', 'count', 2],
        ['forum', 'trending_bigrams', 'term', 'recent_count', 6],
        ['forum', 'trending_unigrams', 'term', 'recent_count', 4],
        ['forum', 'anchor_message_count', 'anchor', 'messages', 5],
    ];

    public function audit(string $template, string $sampleUrl, bool $aiSuggestions = false): SeoAudit
    {
        $analysis = $this->analyzePage($sampleUrl);
        $allKeywords = $this->loadKeywordPool();

        $text = mb_strtolower($analysis['text']);
        $found = [];
        $missing = [];

        foreach ($allKeywords as $kw => $weight) {
            if (mb_strpos($text, mb_strtolower($kw)) !== false) {
                $found[$kw] = $weight;
            } else {
                $missing[$kw] = $weight;
            }
        }

        // Opportunity = eksik kw'lerin toplam ağırlığı / mümkün maksimum
        $totalWeight = array_sum($allKeywords);
        $missingWeight = array_sum($missing);
        $opportunityScore = $totalWeight > 0
            ? (int) round(($missingWeight / $totalWeight) * 100)
            : 0;

        // High-value gaps = en yüksek skorlu eksik kw'ler (top 30)
        arsort($missing);
        $highValueGaps = array_slice($missing, 0, 30, true);

        $aiSugg = null;
        $aiMeta = null;
        if ($aiSuggestions) {
            $result = $this->generateAiSuggestions($template, $sampleUrl, $analysis, $highValueGaps);
            $aiSugg = $result['text'] ?? null;
            $aiMeta = $result['meta'] ?? null;
        }

        return SeoAudit::updateOrCreate(
            ['template' => $template, 'sample_url' => $sampleUrl],
            [
                'page_title' => $analysis['title'],
                'content_length' => mb_strlen($analysis['text']),
                'h1_count' => $analysis['h1_count'],
                'h2_count' => $analysis['h2_count'],
                'image_count' => $analysis['image_count'],
                'internal_link_count' => $analysis['internal_link_count'],
                'keywords_found' => array_keys($found),
                'keywords_missing' => array_keys($missing),
                'high_value_gaps' => $highValueGaps,
                'opportunity_score' => $opportunityScore,
                'ai_suggestions' => $aiSugg,
                'ai_meta' => $aiMeta,
                'last_audited_at' => now(),
            ]
        );
    }

    /**
     * Sayfayı fetch et + DOM analiz.
     */
    private function analyzePage(string $url): array
    {
        try {
            $html = $this->fetchHtml($url);
            if ($html === null || trim($html) === '') {
                throw new \RuntimeException("Boş içerik: $url");
            }

            $crawler = new Crawler($html);

            $title = '';
            try {
                $title = trim($crawler->filter('title')->first()->text());
            } catch (\Throwable $e) {}

            // İçeriği topla (sadece main / article / body içinden)
            $contentText = '';
            foreach (['main', 'article', '.content', 'body'] as $sel) {
                try {
                    $node = $crawler->filter($sel)->first();
                    if ($node->count() > 0) {
                        $contentText = $node->text();
                        break;
                    }
                } catch (\Throwable $e) {}
            }
            if (!$contentText) {
                $contentText = strip_tags($html);
            }
            $contentText = trim(preg_replace('/\s+/u', ' ', $contentText));

            return [
                'title' => $title,
                'text' => $contentText,
                'h1_count' => $crawler->filter('h1')->count(),
                'h2_count' => $crawler->filter('h2')->count(),
                'image_count' => $crawler->filter('img')->count(),
                'internal_link_count' => $crawler->filter('a[href^="/"]')->count(),
            ];
        } catch (\Throwable $e) {
            Log::warning("SEO audit fetch fail: $url — " . $e->getMessage());
            return [
                'title' => '',
                'text' => '',
                'h1_count' => 0, 'h2_count' => 0,
                'image_count' => 0, 'internal_link_count' => 0,
            ];
        }
    }

    /**
     * HTML'i getir. Kendi sitemizin URL'i ise INTERNAL kernel request kullan
     * (php artisan serve tek-thread olduğu için self-HTTP-request deadlock'unu önler).
     * Dış URL ise normal HTTP GET.
     */
    private function fetchHtml(string $url): ?string
    {
        $parsed = parse_url($url);
        $host = $parsed['host'] ?? '';
        $appHost = parse_url(config('app.url'))['host'] ?? '';

        $isInternal = $host === '' // path-only
            || $host === $appHost
            || in_array($host, ['localhost', '127.0.0.1', '::1'], true);

        if ($isInternal) {
            // HTTP katmanı olmadan route'u doğrudan çalıştır — deadlock yok, hızlı.
            $path = $parsed['path'] ?? '/';
            if (! empty($parsed['query'])) {
                $path .= '?' . $parsed['query'];
            }
            $request = \Illuminate\Http\Request::create($path, 'GET');
            $request->headers->set('User-Agent', 'AlmanyaUni-SeoAudit/1.0');

            $kernel = app(\Illuminate\Contracts\Http\Kernel::class);
            $response = $kernel->handle($request);

            if ($response->getStatusCode() >= 400) {
                throw new \RuntimeException("HTTP {$response->getStatusCode()} (internal) on $path");
            }
            return $response->getContent();
        }

        // Dış URL — gerçek HTTP
        $resp = Http::timeout(30)
            ->withHeaders(['User-Agent' => 'AlmanyaUni-SeoAudit/1.0'])
            ->get($url);

        if (! $resp->ok()) {
            throw new \RuntimeException("HTTP {$resp->status()} on $url");
        }
        return $resp->body();
    }

    /**
     * Forum + Telegram'dan birleşik weighted keyword havuzu.
     * @return array<string, int> kw => weight
     */
    private function loadKeywordPool(): array
    {
        $pool = [];
        $forumPath = storage_path('app/community/forum_insights.json');
        if (is_file($forumPath)) {
            $forum = json_decode(file_get_contents($forumPath), true) ?? [];

            foreach (self::KEYWORD_SOURCES as [$src, $key, $termField, $countField, $weight]) {
                if ($src !== 'forum') continue;
                foreach ($forum[$key] ?? [] as $row) {
                    $term = mb_strtolower(trim($row[$termField] ?? ''));
                    if (mb_strlen($term) < 4) continue;
                    $count = (int) ($row[$countField] ?? 1);
                    $score = $count * $weight;
                    $pool[$term] = max($pool[$term] ?? 0, $score);
                }
            }
        }

        // Telegram rapor konularını da ekle (Konular sütunu)
        foreach (glob(storage_path('app/community/telegram_report_*.json')) ?: [] as $file) {
            $r = json_decode(file_get_contents($file), true) ?? [];
            foreach ($r['konular'] ?? [] as $row) {
                $v = array_values($row);
                $term = mb_strtolower(trim($v[0] ?? ''));
                if ($term && !str_starts_with($term, '_')) {
                    $pool[$term] = max($pool[$term] ?? 0, 100); // topic etiketi yüksek değer
                }
            }
        }

        // Çok kısa veya stop word'leri at
        $stop = ['icin', 'daha', 'olur', 'oldu', 'gibi', 'sonra', 'kadar'];
        foreach ($stop as $s) unset($pool[$s]);

        arsort($pool);
        return array_slice($pool, 0, 200, true); // top 200
    }

    /**
     * AI ile sayfaya eklenecek bölüm önerisi.
     */
    private function generateAiSuggestions(string $template, string $url, array $analysis, array $gaps): array
    {
        $key = config('services.gemini.key');
        if (!$key || empty($gaps)) return ['text' => null, 'meta' => null];

        $gapList = "- " . implode("\n- ", array_keys($gaps));
        $templateLabel = \App\Models\SeoAudit::TEMPLATES[$template] ?? $template;
        $contentLen = mb_strlen($analysis['text'] ?? '');
        $h1 = $analysis['h1_count'] ?? 0;
        $h2 = $analysis['h2_count'] ?? 0;
        $imgs = $analysis['image_count'] ?? 0;
        $title = $analysis['title'] ?? '';

        $prompt = <<<TXT
AlmanyaUni (Türk öğrencilere Almanya rehberi) sitesinin "$templateLabel" sayfası için SEO audit yapıyorsun.

URL: $url
Mevcut başlık: $title
Mevcut içerik uzunluğu: ~$contentLen char
H1: $h1 · H2: $h2 · Görsel: $imgs

🚨 Bu sayfada EKSIK olan, ama topluluk verisinde sıkça konuşulan keyword/konular (skor sıralı):
$gapList

GÖREV: Sayfa şablonuna eklenmesi önerilen 5-8 yeni section/bölüm öner. Her öneri için:
- Bölüm başlığı (H2)
- 1-2 cümle içerik özeti
- Hedef keyword(ler)

ÖNEMLİ:
- Sayfa şablonu olduğunu unutma — her ünivers/şehre uygun, generic olmalı.
- Tahmin/halüsinasyon yok. Bilmiyorsan "..şartlarını üni resmi sayfasından doğrula" de.

ÇIKTI: Markdown listesi (## kullanma, ### kullan).
TXT;

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';
        try {
            $resp = Http::asJson()
                ->timeout(120)
                ->withHeaders(['x-goog-api-key' => $key])
                ->retry(2, 2000)
                ->post($url, [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['temperature' => 0.5, 'maxOutputTokens' => 4096],
                ]);

            if (!$resp->ok()) {
                throw new \RuntimeException('HTTP ' . $resp->status());
            }
            $data = $resp->json();
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            $usage = $data['usageMetadata'] ?? [];

            return [
                'text' => $text ? trim($text) : null,
                'meta' => [
                    'model' => 'gemini-2.5-flash',
                    'input_tokens' => $usage['promptTokenCount'] ?? 0,
                    'output_tokens' => $usage['candidatesTokenCount'] ?? 0,
                ],
            ];
        } catch (\Throwable $e) {
            return ['text' => null, 'meta' => ['error' => $e->getMessage()]];
        }
    }
}
