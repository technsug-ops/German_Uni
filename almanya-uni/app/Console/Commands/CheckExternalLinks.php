<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

/**
 * Sağlayıcı/partner dış linklerini ("siteye git" hedefleri) tarar; ölü/hatalı
 * olanları raporlar. Prod'da çalıştırılmalı (sandbox dışarı çıkamaz).
 * 403/401 = bot-bloku (canlı sayılır); 404/410/5xx/timeout = sorun.
 */
class CheckExternalLinks extends Command
{
    protected $signature = 'links:check-external {--timeout=8} {--only=} {--max=800}';

    protected $description = 'Sağlayıcı/partner dış linklerini tarar, ölü/hatalı olanları raporlar.';

    private array $sources = [
        ['table' => 'housing_providers',           'url' => 'website',     'key' => 'slug', 'label' => 'name'],
        ['table' => 'language_courses',            'url' => 'website',     'key' => 'id',   'label' => 'name'],
        ['table' => 'translation_offices',         'url' => 'website',     'key' => 'id',   'label' => 'name'],
        ['table' => 'health_insurance_providers',  'url' => 'website_url', 'key' => 'slug', 'label' => 'name'],
        ['table' => 'blocked_account_providers',   'url' => 'website_url', 'key' => 'slug', 'label' => 'name'],
        ['table' => 'scholarships',                'url' => 'detail_url',  'key' => 'slug', 'label' => 'name_en'],
        ['table' => 'advisors',                    'url' => 'profile_url', 'key' => 'id',   'label' => 'name'],
    ];

    public function handle(): int
    {
        $only    = $this->option('only');
        $timeout = max(3, (int) $this->option('timeout'));
        $max     = (int) $this->option('max');

        // 1) URL'leri topla (dedupe by URL → hangi kayıtlarda kullanıldığını sakla).
        // scholarships HARİÇ (166 DAAD linki, resmî; web isteğini şişirmesin) — sadece ?only=scholarships ile.
        $items = []; // url => [ref, ...]
        foreach ($this->sources as $s) {
            if ($only && $only !== $s['table']) {
                continue;
            }
            if (! $only && $s['table'] === 'scholarships') {
                continue;
            }
            if (! Schema::hasTable($s['table']) || ! Schema::hasColumn($s['table'], $s['url'])) {
                continue;
            }
            $q = DB::table($s['table'])->whereNotNull($s['url'])->where($s['url'], '!=', '');
            // Gizli/yayınlanmamış kayıtları atla — kullanıcıya görünmeyen linkler gürültü yapmasın.
            if (Schema::hasColumn($s['table'], 'is_active')) {
                $q->where('is_active', 1);
            }
            if (Schema::hasColumn($s['table'], 'is_published')) {
                $q->where('is_published', 1);
            }
            foreach ($q->get([$s['key'], $s['url'], $s['label']]) as $row) {
                $url = trim((string) $row->{$s['url']});
                if (! preg_match('#^https?://#i', $url)) {
                    continue;
                }
                $items[$url][] = "{$s['table']}#{$row->{$s['key']}} ({$row->{$s['label']}})";
            }
        }

        $urls = array_slice(array_keys($items), 0, $max);
        $this->info('Kontrol edilecek tekil URL: ' . count($urls));

        $dead = [];     // 404/410
        $error = [];    // 5xx
        $unreach = [];  // dns/conn/timeout
        $blocked = [];  // 401/403/405/429 (muhtemelen canlı)
        $ok = 0;

        // Eşzamanlı (concurrent) kontrol — 20'lik gruplar; web isteği timeout'a girmesin.
        foreach (array_chunk($urls, 20) as $chunk) {
            $responses = Http::pool(fn ($pool) => array_map(
                fn ($u) => $pool->connectTimeout(min(5, $timeout))->timeout($timeout)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; ApplyToGermanLinkBot/1.0)'])
                    ->get($u),
                $chunk
            ));

            foreach ($chunk as $i => $url) {
                $r = $responses[$i] ?? null;
                if ($r instanceof \Illuminate\Http\Client\Response) {
                    $code = $r->status();
                    if ($code >= 200 && $code < 400) {
                        $ok++;
                    } elseif (in_array($code, [401, 403, 405, 429], true)) {
                        $blocked[] = [$code, $url, $items[$url]];
                    } elseif (in_array($code, [404, 410], true)) {
                        $dead[] = [$code, $url, $items[$url]];
                    } else {
                        $error[] = [$code, $url, $items[$url]];
                    }
                } else {
                    $unreach[] = ['ERR', $url, $items[$url]];
                }
            }
        }
        $this->newLine();

        $this->info("✅ Sağlam: {$ok}");
        $this->printGroup('🔴 ÖLÜ (404/410)', $dead);
        $this->printGroup('🟠 SUNUCU HATASI (5xx)', $error);
        $this->printGroup('⛔ ERİŞİLEMEDİ (dns/timeout)', $unreach);
        $this->printGroup('🔒 BLOKLU (403/401 — muhtemelen canlı, elle bak)', $blocked);

        $this->newLine();
        $this->info('Düzeltilmesi gerekenler: ' . (count($dead) + count($error) + count($unreach)));

        return self::SUCCESS;
    }

    private function printGroup(string $title, array $rows): void
    {
        if (empty($rows)) {
            return;
        }
        $this->newLine();
        $this->line("=== {$title} — " . count($rows) . ' ===');
        foreach ($rows as $r) {
            [$code, $url] = $r;
            $this->line("  [{$code}] {$url}");
            $this->line('        ↳ ' . implode(', ', $r[2]));
        }
    }
}
