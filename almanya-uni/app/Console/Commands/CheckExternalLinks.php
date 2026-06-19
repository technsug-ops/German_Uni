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
    protected $signature = 'links:check-external {--timeout=12} {--only=} {--max=800}';

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
        $timeout = (int) $this->option('timeout');
        $max     = (int) $this->option('max');

        // 1) URL'leri topla (dedupe by URL → hangi kayıtlarda kullanıldığını sakla)
        $items = []; // url => [ref, ...]
        foreach ($this->sources as $s) {
            if ($only && $only !== $s['table']) {
                continue;
            }
            if (! Schema::hasTable($s['table']) || ! Schema::hasColumn($s['table'], $s['url'])) {
                continue;
            }
            foreach (DB::table($s['table'])->whereNotNull($s['url'])->where($s['url'], '!=', '')->get([$s['key'], $s['url'], $s['label']]) as $row) {
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

        $bar = $this->output->createProgressBar(count($urls));
        foreach ($urls as $url) {
            try {
                $code = Http::timeout($timeout)->connectTimeout($timeout)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; ApplyToGermanLinkBot/1.0)'])
                    ->get($url)->status();

                if ($code >= 200 && $code < 400) {
                    $ok++;
                } elseif (in_array($code, [401, 403, 405, 429], true)) {
                    $blocked[] = [$code, $url, $items[$url]];
                } elseif (in_array($code, [404, 410], true)) {
                    $dead[] = [$code, $url, $items[$url]];
                } else {
                    $error[] = [$code, $url, $items[$url]];
                }
            } catch (\Throwable $e) {
                $unreach[] = ['ERR', $url, $items[$url], $e->getMessage()];
            }
            $bar->advance();
        }
        $bar->finish();
        $this->newLine(2);

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
