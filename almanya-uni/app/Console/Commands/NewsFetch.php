<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\NewsCandidate;
use App\Models\NewsSource;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

/**
 * Otomatik haber çekimi (Mod 1). Kaynaklar DB'den (news_sources / admin "Haber
 * Kaynakları" paneli) okunur; tablo yoksa/boşsa config/news_sources.php'ye düşer.
 * Başlık+özet+link çeker → NewsCandidate (origin=auto, pending). Dedupe: url_hash.
 * AI taslak ÜRETMEZ (token tasarrufu) — admin panelde "AI Taslak Üret" ile üretir.
 */
class NewsFetch extends Command
{
    protected $signature = 'news:fetch {--source= : Sadece bu kaynak adı} {--dry-run} {--max-seconds=0 : Zaman bütçesi (sn) — KAS gateway timeout öncesi temiz çık}';
    protected $description = 'RSS/Atom kaynaklardan haber adayı çeker (onay öncesi gelen kutusu)';

    public function handle(): int
    {
        $cfg = config('news_sources');
        $maxPerGlobal = (int) ($cfg['max_per_source'] ?? 6);
        $maxAge = (int) ($cfg['max_age_days'] ?? 45);

        $only = $this->option('source');
        $dry  = (bool) $this->option('dry-run');
        $maxSeconds = (int) $this->option('max-seconds');
        $started = microtime(true);
        $feeds = $this->loadFeeds($only);
        if ($feeds->isEmpty()) {
            $this->warn('Aktif kaynak yok.');
            return self::SUCCESS;
        }

        $catMap = Category::where('kind', 'news')->pluck('id', 'slug');

        $created = 0; $skipped = 0; $processed = 0;
        $total = $feeds->count();

        foreach ($feeds as $feed) {
            // Zaman bütçesi: gateway öldürmeden önce temiz çık (kısmi ilerleme kayıtlı).
            if ($maxSeconds > 0 && (microtime(true) - $started) >= $maxSeconds) {
                $kalan = $total - $processed;
                $this->warn("⏳ Süre doldu — {$kalan} kaynak kaldı (tekrar çalıştır).");
                break;
            }
            $processed++;
            $this->line("📡 {$feed['name']} — {$feed['url']}");
            $maxPer = $feed['max_per_source'] ?: $maxPerGlobal;
            $items = $this->parseFeed($feed['url']);
            if ($items === null) {
                $this->warn("   ⚠️ feed okunamadı");
                $this->markFetched($feed, 'okunamadı', $dry);
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

                try {
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
                } catch (\Throwable $e) {
                    // Tek bozuk satır tüm çekimi öldürmesin — atla, devam et.
                    $take--;
                    $this->warn('   ⚠️ atlandı: ' . mb_substr($e->getMessage(), 0, 90));
                    continue;
                }
                $created++;
                $this->info('   ➕ ' . mb_substr($it['title'], 0, 70));
            }

            $this->markFetched($feed, "{$take} yeni", $dry);
        }

        $this->newLine();
        $this->info("✅ Yeni aday: {$created}, atlanan (mevcut): {$skipped} ({$processed}/{$total} kaynak)");
        return self::SUCCESS;
    }

    /**
     * Aktif kaynakları DB'den (news_sources) yükler; tablo yoksa/boşsa config'e düşer.
     * Normalleştirilmiş şekil: name, url, keywords[], default_category, max_per_source, model.
     *
     * @return Collection<int,array{name:string,url:string,keywords:array,default_category:?string,max_per_source:?int,model:?NewsSource}>
     */
    private function loadFeeds(?string $only): Collection
    {
        if (Schema::hasTable('news_sources')) {
            $q = NewsSource::where('enabled', true);
            if ($only) {
                $q->where('name', $only);
            }
            $rows = $q->orderBy('sort_order')->orderBy('id')->get();
            if ($rows->isNotEmpty()) {
                return $rows->map(fn (NewsSource $s) => [
                    'name'             => $s->name,
                    'url'              => $s->url,
                    'keywords'         => $s->keywords ?? [],
                    'default_category' => $s->default_category,
                    'max_per_source'   => $s->max_per_source,
                    'model'            => $s,
                ]);
            }
        }

        // Fallback: config (tablo henüz migrate edilmediyse / boşsa)
        return collect(config('news_sources.feeds', []))
            ->filter(fn ($f) => $f['enabled'] ?? false)
            ->when($only, fn ($c) => $c->filter(fn ($f) => $f['name'] === $only))
            ->map(fn ($f) => [
                'name'             => $f['name'],
                'url'              => $f['url'],
                'keywords'         => $f['keywords'] ?? [],
                'default_category' => $f['default_category'] ?? null,
                'max_per_source'   => null,
                'model'            => null,
            ])->values();
    }

    /** Kaynak modeline son çekim teşhisini yazar (DB kaynağı + dry-run değilse). */
    private function markFetched(array $feed, string $result, bool $dry): void
    {
        if ($dry || empty($feed['model'])) {
            return;
        }
        $feed['model']->forceFill([
            'last_fetched_at' => now(),
            'last_result'     => $result,
        ])->save();
    }

    /**
     * RSS veya Atom feed'i parse eder.
     * @return array<int,array{title:?string,link:?string,summary:?string,date:?Carbon}>|null
     */
    private function parseFeed(string $url): ?array
    {
        try {
            $resp = Http::connectTimeout(6)->timeout(10)
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
