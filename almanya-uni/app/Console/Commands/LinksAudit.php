<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\Program;
use App\Models\University;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * Kırık dış link denetimi — kullanıcıya görünen dış URL'leri HTTP ile kontrol eder.
 * Read-only. URL'leri kaynağıyla toplar, tekilleştirir, eşzamanlı kontrol eder ve
 * kırık olanları (4xx/5xx/timeout/DNS) kaynak kaydıyla raporlar.
 *
 * Kategoriler (--only ile sınırlanabilir):
 *   websites   — üni website_url (373)        [varsayılan]
 *   content    — üni content_blocks external_links
 *   blog       — yayınlanmış blog/news markdown dış linkleri
 *   wikipedia  — üni wikipedia_url_tr/en/de
 *   sources    — program source_url (2491, yavaş — op-in)
 *
 *   php artisan links:audit                       → websites+content+blog
 *   php artisan links:audit --only=websites
 *   php artisan links:audit --all                 → tüm kategoriler (yavaş)
 */
class LinksAudit extends Command
{
    protected $signature = 'links:audit
        {--only= : websites|content|blog|wikipedia|sources (virgülle)}
        {--all : Tüm kategoriler (program source_url dahil — yavaş)}
        {--limit=0 : Kategori başına URL sınırı (0=sınırsız, test için)}';

    protected $description = 'Kırık dış linkleri HTTP ile tespit eder (read-only).';

    // Gerçek tarayıcı UA — bazı siteler (hochschulkompass vb.) bot UA'sını 400/403 ile engeller.
    private const UA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36';
    private const TIMEOUT = 10;
    private const BATCH = 25;

    public function handle(): int
    {
        $cats = $this->option('all')
            ? ['websites', 'content', 'blog', 'wikipedia', 'sources']
            : ($this->option('only') ? array_map('trim', explode(',', $this->option('only'))) : ['websites', 'content', 'blog']);

        $limit = (int) $this->option('limit');

        // url => [ ['label'=>..], .. ]  (aynı URL birden çok kayıtta olabilir)
        $map = [];
        foreach ($cats as $cat) {
            foreach ($this->collect($cat) as [$url, $label]) {
                $url = trim($url);
                if (! preg_match('#^https?://#i', $url)) continue;
                $map[$url][] = $label;
            }
        }
        if ($limit > 0) $map = array_slice($map, 0, $limit, true);

        $urls = array_keys($map);
        $this->info('Kontrol edilecek tekil URL: ' . count($urls) . '  (kategori: ' . implode(',', $cats) . ')');

        // 1. GEÇİŞ — eşzamanlı hızlı tarama: aday (≥400 veya bağlantı hatası) topla.
        $candidates = [];
        $chunks = array_chunk($urls, self::BATCH);
        $bar = $this->output->createProgressBar(count($chunks));
        foreach ($chunks as $chunk) {
            $results = Http::pool(fn ($pool) => array_map(
                fn ($u) => $pool->as($u)->withHeaders(['User-Agent' => self::UA])
                    ->timeout(self::TIMEOUT)->connectTimeout(self::TIMEOUT)
                    ->withOptions(['allow_redirects' => true])->get($u),
                $chunk
            ));
            foreach ($chunk as $u) {
                $res = $results[$u] ?? null;
                if ($res instanceof \Illuminate\Http\Client\Response && $res->status() < 400) continue;
                $candidates[] = $u;
            }
            $bar->advance();
        }
        $bar->finish();
        $this->newLine(2);
        $this->line('1. geçiş adayı: ' . count($candidates) . ' — sıralı yeniden doğrulanıyor (rate-limit/geçici elenir)...');

        // 2. GEÇİŞ — her adayı TEK TEK, beklemeyle yeniden dene (eşzamanlılık FP'lerini ele).
        // KESİN kırık = kalıcı 404/410/451/526. ŞÜPHELİ = 403/429/5xx/ERR (bot-savunması/geçici olabilir).
        $confident = []; $suspicious = [];
        foreach ($candidates as $u) {
            $status = $this->recheck($u);
            if ($status !== null && $status < 400) continue; // toparladı → sağlam
            $entry = ['status' => $status ?? 'ERR', 'refs' => $map[$u]];
            if (is_int($status) && in_array($status, [404, 410, 451, 526], true)) $confident[$u] = $entry;
            else $suspicious[$u] = $entry; // 403/429/5xx/ERR — geçici/bot olabilir
        }

        $this->newLine();
        $this->error('✖ KESİN KIRIK (404/410/526): ' . count($confident));
        foreach ($confident as $u => $info) {
            $this->line(sprintf('  [%s] %s', $info['status'], $u));
            foreach (array_slice(array_unique($info['refs']), 0, 4) as $r) $this->line('        ← ' . $r);
        }
        $this->newLine();
        $this->warn('▲ ŞÜPHELİ (403/429/5xx/timeout — bot-savunması/geçici olabilir, elle bak): ' . count($suspicious));
        foreach ($suspicious as $u => $info) {
            $this->line(sprintf('  [%s] %s  ← %s', $info['status'], $u, array_unique($info['refs'])[0] ?? ''));
        }

        return empty($confident) ? self::SUCCESS : self::FAILURE;
    }

    /** Tek URL'yi sıralı, 1.2s beklemeyle yeniden dener. Dönüş: status int veya null (bağlantı hatası). */
    private function recheck(string $u): int|null
    {
        usleep(1_200_000);
        try {
            $res = Http::withHeaders(['User-Agent' => self::UA])
                ->timeout(self::TIMEOUT)->connectTimeout(self::TIMEOUT)
                ->withOptions(['allow_redirects' => true])->get($u);
            return $res->status();
        } catch (\Throwable $e) {
            return null; // DNS/timeout — bağlantı kurulamadı (şüpheli say)
        }
    }

    /** @return iterable<array{0:string,1:string}> [url, label] */
    private function collect(string $cat): iterable
    {
        switch ($cat) {
            case 'websites':
                foreach (University::where('is_active', 1)->whereNotNull('website_url')->where('website_url', '!=', '')
                    ->get(['name_de', 'slug', 'website_url']) as $u) {
                    yield [$u->website_url, "uni website: {$u->name_de}"];
                }
                break;

            case 'wikipedia':
                foreach (University::where('is_active', 1)->get(['name_de', 'wikipedia_url_tr', 'wikipedia_url_en', 'wikipedia_url_de']) as $u) {
                    foreach (['wikipedia_url_tr', 'wikipedia_url_en', 'wikipedia_url_de'] as $c) {
                        if ($u->$c) yield [$u->$c, "uni wiki: {$u->name_de}"];
                    }
                }
                break;

            case 'sources':
                foreach (Program::where('is_active', 1)->whereNotNull('source_url')->where('source_url', '!=', '')
                    ->select('name_de', 'slug', 'source_url')->cursor() as $p) {
                    yield [$p->source_url, "program source: {$p->name_de}"];
                }
                break;

            case 'content':
                foreach (University::where('is_active', 1)->whereNotNull('content_blocks')->get(['name_de', 'content_blocks']) as $u) {
                    foreach ((array) $u->content_blocks as $b) {
                        if (($b['type'] ?? '') !== 'external_links') continue;
                        foreach (($b['links'] ?? $b['items'] ?? []) as $link) {
                            $url = is_array($link) ? ($link['url'] ?? null) : $link;
                            if ($url) yield [$url, "uni içerik linki: {$u->name_de}"];
                        }
                    }
                }
                break;

            case 'blog':
                foreach (Post::where('is_published', 1)->get(['title', 'slug', 'content_md']) as $p) {
                    if (preg_match_all('/\]\((https?:\/\/[^)\s]+)\)/', (string) $p->content_md, $m)) {
                        foreach ($m[1] as $url) yield [$url, "blog: {$p->title}"];
                    }
                }
                break;
        }
    }
}
