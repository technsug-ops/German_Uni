<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Forum (DeutschStudent) analiz CSV'lerini cache'e ayrıştırır — TÜM 13 dosya.
 *
 * KULLANILAN ANALİZLER:
 *   - seo_topics_top.csv         — yüksek-view konular
 *   - trending_bigrams.csv       — 12 ay yükselen 2-grams
 *   - trending_unigrams.csv      — 12 ay yükselen 1-grams
 *   - title_bigrams.csv          — başlık 2-grams
 *   - title_trigrams.csv         — başlık 3-grams (en güçlü blog başlık sinyali)
 *   - title_unigrams.csv         — başlık tek kelimeler
 *   - all_bigrams.csv            — gövde 2-grams
 *   - all_trigrams.csv           — gövde 3-grams (phrase intent)
 *   - all_unigrams.csv           — gövde tek kelimeler
 *   - anchor_message_count.csv   — topic merkez kelime frekans
 *   - anchor_co_occurrence.csv   — "ISI HARİTASI" — hangi konular birlikte
 *   - forum_summary.csv          — forum × kategori dağılımı
 *   - yearly_volume.csv          — yıllık trend
 */
class CommunityPrepareForum extends Command
{
    protected $signature = 'community:prepare-forum
        {--base= : Forum CSV base path}';

    protected $description = 'DeutschStudent forum 13 CSV → tek cache JSON.';

    private const STOP = [
        'icin', 'daha', 'quote', 'tarafindan', 'metin', 'yazilan', 'ederim',
        'olarak', 'ile', 'bir', 'olur', 'oldugu', 'oluyor', 'gibi', 'sonra',
        'kadar', 'çok', 'hala', 'falan', 'tesekkur', 'cok', 'ben', 'bu',
        'physiker', 'welly275',
    ];

    public function handle(): int
    {
        $base = $this->option('base')
            ?: 'C:\\Users\\Yapra\\OneDrive\\Masaüstü\\Data\\uni_Finder_ MyGermany\\analysis_v2\\output\\data';

        if (!is_dir($base)) {
            $this->error("Klasör bulunamadı: $base");
            return self::FAILURE;
        }

        $insights = [
            'source' => 'DeutschStudent community',
            'generated_at' => now()->toIso8601String(),
            'top_topics' => $this->readTopTopics("$base/seo_topics_top.csv", 80),
            'trending_bigrams' => $this->readTrendingPhrase("$base/trending_bigrams.csv", 50, 20),
            'trending_unigrams' => $this->readTrendingPhrase("$base/trending_unigrams.csv", 50, 10),
            'title_bigrams' => $this->readNgram("$base/title_bigrams.csv", 80, 30),
            'title_trigrams' => $this->readNgram("$base/title_trigrams.csv", 80, 10),
            'title_unigrams' => $this->readNgramFiltered("$base/title_unigrams.csv", 80, 50),
            'all_bigrams' => $this->readNgram("$base/all_bigrams.csv", 100, 200),
            'all_trigrams' => $this->readNgram("$base/all_trigrams.csv", 80, 100),
            'all_unigrams' => $this->readNgramFiltered("$base/all_unigrams.csv", 120, 200),
            'anchor_message_count' => $this->readAnchorCount("$base/anchor_message_count.csv"),
            'anchor_co_occurrence' => $this->readCoOccurrence("$base/anchor_co_occurrence.csv", 60),
            'category_distribution' => $this->readForumSummary("$base/forum_summary.csv"),
            'volume_yearly' => $this->readVolume("$base/yearly_volume.csv"),
        ];

        $cachePath = storage_path('app/community/forum_insights.json');
        if (!is_dir(dirname($cachePath))) {
            mkdir(dirname($cachePath), 0755, true);
        }
        file_put_contents($cachePath, json_encode($insights, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $this->info('✅ ' . $cachePath);
        $this->line('Boyut: ' . round(filesize($cachePath) / 1024, 1) . ' KB');
        $this->newLine();
        foreach ($insights as $k => $v) {
            if (in_array($k, ['source', 'generated_at'])) continue;
            $this->line(sprintf('  %-26s %d kayıt', $k . ':', count($v)));
        }
        return self::SUCCESS;
    }

    private function readTopTopics(string $path, int $limit): array
    {
        if (!is_file($path)) return [];
        return collect($this->readCsv($path))
            ->filter(fn ($r) => (int) ($r['views'] ?? 0) >= 5000
                && (int) ($r['replies'] ?? 0) >= 5
                && !preg_match('/Sosyal Medya|reklam|sponsor|@/i', $r['title'] ?? ''))
            ->take($limit)
            ->map(fn ($r) => [
                'forum' => $r['forum'] ?? '', 'category' => $r['category'] ?? '',
                'title' => $r['title'] ?? '', 'views' => (int) ($r['views'] ?? 0),
                'replies' => (int) ($r['replies'] ?? 0),
                'has_question' => (bool) ($r['has_question_in_title'] ?? 0),
                'url' => $r['url'] ?? null,
            ])
            ->values()->all();
    }

    private function readTrendingPhrase(string $path, int $limit, float $minLift): array
    {
        if (!is_file($path)) return [];
        return collect($this->readCsv($path))
            ->filter(fn ($r) => (float) ($r['lift'] ?? 0) >= $minLift
                && (int) ($r['recent'] ?? 0) >= 5
                && !$this->isStop($r['term'] ?? ''))
            ->take($limit)
            ->map(fn ($r) => [
                'term' => $r['term'] ?? '',
                'lift' => round((float) ($r['lift'] ?? 0), 2),
                'recent_count' => (int) ($r['recent'] ?? 0),
                'older_count' => (int) ($r['older'] ?? 0),
            ])
            ->values()->all();
    }

    private function readNgram(string $path, int $limit, int $minCount): array
    {
        if (!is_file($path)) return [];
        return collect($this->readCsv($path))
            ->filter(fn ($r) => (int) ($r['count'] ?? 0) >= $minCount
                && !$this->isStop($r['term'] ?? ''))
            ->take($limit)
            ->map(fn ($r) => ['term' => $r['term'] ?? '', 'count' => (int) ($r['count'] ?? 0)])
            ->values()->all();
    }

    private function readNgramFiltered(string $path, int $limit, int $minCount): array
    {
        if (!is_file($path)) return [];
        return collect($this->readCsv($path))
            ->filter(fn ($r) => (int) ($r['count'] ?? 0) >= $minCount
                && mb_strlen($r['term'] ?? '') >= 4
                && !$this->isStop($r['term'] ?? ''))
            ->take($limit)
            ->map(fn ($r) => ['term' => $r['term'] ?? '', 'count' => (int) ($r['count'] ?? 0)])
            ->values()->all();
    }

    private function readAnchorCount(string $path): array
    {
        if (!is_file($path)) return [];
        return collect($this->readCsv($path))
            ->map(fn ($r) => ['anchor' => $r['anchor'] ?? '', 'messages' => (int) ($r['messages'] ?? 0)])
            ->sortByDesc('messages')
            ->values()->all();
    }

    private function readCoOccurrence(string $path, int $limit): array
    {
        if (!is_file($path)) return [];
        return collect($this->readCsv($path))
            ->filter(fn ($r) => (int) ($r['co_occurring_messages'] ?? 0) >= 50)
            ->take($limit)
            ->map(fn ($r) => [
                'a' => $r['term_a'] ?? '', 'b' => $r['term_b'] ?? '',
                'count' => (int) ($r['co_occurring_messages'] ?? 0),
            ])
            ->values()->all();
    }

    private function readForumSummary(string $path): array
    {
        if (!is_file($path)) return [];
        return collect($this->readCsv($path))
            ->map(fn ($r) => [
                'forum' => $r['forum'] ?? '', 'category' => $r['category'] ?? '',
                'topics' => (int) ($r['topics'] ?? 0), 'messages' => (int) ($r['messages'] ?? 0),
                'total_views' => (int) ($r['total_views'] ?? 0),
            ])
            ->sortByDesc('messages')
            ->values()->all();
    }

    private function readVolume(string $path): array
    {
        if (!is_file($path)) return [];
        return collect($this->readCsv($path))->map(fn ($r) => array_map('strval', $r))->values()->all();
    }

    private function isStop(string $term): bool
    {
        $t = mb_strtolower($term);
        foreach (self::STOP as $s) {
            if (mb_strpos($t, $s) !== false) return true;
        }
        return false;
    }

    private function readCsv(string $path): array
    {
        $rows = [];
        if (!($handle = fopen($path, 'r'))) return [];
        $header = fgetcsv($handle);
        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) === count($header)) {
                $rows[] = array_combine($header, $data);
            }
        }
        fclose($handle);
        return $rows;
    }
}
