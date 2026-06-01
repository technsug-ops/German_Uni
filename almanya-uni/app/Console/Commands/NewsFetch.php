<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\NewsCandidate;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

/**
 * Otomatik haber çekimi (Mod 1). config/news_sources.php'deki RSS/Atom
 * kaynaklardan başlık+özet+link çeker → NewsCandidate (origin=auto, pending).
 * Dedupe: url_hash. AI taslak ÜRETMEZ (token tasarrufu) — admin panelde "AI
 * Taslak Üret" ile üretir. KAS Cronjob veya elle çalıştırılır.
 */
class NewsFetch extends Command
{
    protected $signature = 'news:fetch {--source= : Sadece bu kaynak adı} {--dry-run}';
    protected $description = 'RSS/Atom kaynaklardan haber adayı çeker (onay öncesi gelen kutusu)';

    public function handle(): int
    {
        $cfg = config('news_sources');
        $feeds = collect($cfg['feeds'] ?? [])->filter(fn ($f) => $f['enabled'] ?? false);
        if ($only = $this->option('source')) {
            $feeds = $feeds->filter(fn ($f) => $f['name'] === $only);
        }
        if ($feeds->isEmpty()) {
            $this->warn('Aktif kaynak yok.');
            return self::SUCCESS;
        }

        $maxPer = (int) ($cfg['max_per_source'] ?? 6);
        $maxAge = (int) ($cfg['max_age_days'] ?? 45);
        $catMap = Category::where('kind', 'news')->pluck('id', 'slug');

        $created = 0; $skipped = 0;

        foreach ($feeds as $feed) {
            $this->line("📡 {$feed['name']} — {$feed['url']}");
            $items = $this->parseFeed($feed['url']);
            if ($items === null) {
                $this->warn("   ⚠️ feed okunamadı");
                continue;
            }

            $catId = $catMap[$feed['default_category'] ?? ''] ?? null;
            $keywords = array_map('mb_strtolower', $feed['keywords'] ?? []);
            $take = 0;

            foreach ($items as $it) {
                if ($take >= $maxPer) break;
                if (! $it['link'] || ! $it['title']) continue;

                // Anahtar-kelime filtresi (geniş kaynaklarda sadece alakalı haber)
                if ($keywords) {
                    $hay = mb_strtolower(($it['title'] ?? '') . ' ' . ($it['summary'] ?? ''));
                    $hit = false;
                    foreach ($keywords as $kw) {
                        if ($kw !== '' && mb_strpos($hay, $kw) !== false) { $hit = true; break; }
                    }
                    if (! $hit) continue;
                }

                if ($it['date'] && $maxAge > 0 && $it['date']->lt(now()->subDays($maxAge))) {
                    continue; // bayat
                }

                $hash = NewsCandidate::hashUrl($it['link']);
                if (NewsCandidate::where('url_hash', $hash)->exists()) {
                    $skipped++;
                    continue;
                }

                $take++;
                if ($this->option('dry-run')) {
                    $this->info('   🔍 ' . mb_substr($it['title'], 0, 70));
                    continue;
                }

                NewsCandidate::create([
                    'origin'                => NewsCandidate::ORIGIN_AUTO,
                    'status'                => NewsCandidate::STATUS_PENDING,
                    'source_name'           => $feed['name'],
                    'source_url'            => $it['link'],
                    'url_hash'              => $hash,
                    'orig_title'            => mb_substr($it['title'], 0, 300),
                    'raw_excerpt'           => $it['summary'] ? mb_substr($it['summary'], 0, 1000) : null,
                    'event_date'            => $it['date']?->toDateString(),
                    'suggested_category_id' => $catId,
                    'primary_locale'        => 'tr',
                ]);
                $created++;
                $this->info('   ➕ ' . mb_substr($it['title'], 0, 70));
            }
        }

        $this->newLine();
        $this->info("✅ Yeni aday: {$created}, atlanan (mevcut): {$skipped}");
        return self::SUCCESS;
    }

    /**
     * RSS veya Atom feed'i parse eder.
     * @return array<int,array{title:?string,link:?string,summary:?string,date:?Carbon}>|null
     */
    private function parseFeed(string $url): ?array
    {
        try {
            $resp = Http::timeout(25)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; AlmanyaUniBot/1.0)'])
                ->get($url);
            if (! $resp->ok()) return null;

            $xml = @simplexml_load_string($resp->body(), 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOERROR | LIBXML_NOWARNING);
            if ($xml === false) return null;

            $items = [];

            // RSS 2.0
            if (isset($xml->channel->item)) {
                foreach ($xml->channel->item as $item) {
                    $items[] = [
                        'title'   => trim((string) $item->title) ?: null,
                        'link'    => trim((string) $item->link) ?: null,
                        'summary' => trim(strip_tags((string) $item->description)) ?: null,
                        'date'    => $this->parseDate((string) $item->pubDate),
                    ];
                }
                return $items;
            }

            // Atom
            if (isset($xml->entry)) {
                foreach ($xml->entry as $entry) {
                    $link = '';
                    foreach ($entry->link as $l) {
                        $href = (string) $l['href'];
                        if (((string) $l['rel'] === 'alternate' || (string) $l['rel'] === '') && $href) {
                            $link = $href; break;
                        }
                    }
                    $items[] = [
                        'title'   => trim((string) $entry->title) ?: null,
                        'link'    => $link ?: null,
                        'summary' => trim(strip_tags((string) ($entry->summary ?: $entry->content))) ?: null,
                        'date'    => $this->parseDate((string) ($entry->updated ?: $entry->published)),
                    ];
                }
                return $items;
            }

            return [];
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function parseDate(string $raw): ?Carbon
    {
        $raw = trim($raw);
        if ($raw === '') return null;
        try {
            return Carbon::parse($raw);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
