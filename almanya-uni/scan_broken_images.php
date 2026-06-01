<?php

/**
 * One-shot scan: HEAD every university.image_url, list IDs whose
 * image is broken (non-2xx, timeout, or 0-byte). Output goes to
 * storage/app/broken_uni_images.json so we can roll a seed migration
 * that NULLs exactly those rows.
 *
 * Run from project root:  php scan_broken_images.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$rows = Illuminate\Support\Facades\DB::table('universities')
    ->whereNotNull('image_url')
    ->where('image_url', '<>', '')
    ->get(['id', 'name_de', 'image_url']);

echo "Scanning ".$rows->count()." URLs...\n";

$mh = curl_multi_init();
$handles = [];
$results = [];

foreach ($rows as $r) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $r->image_url,
        CURLOPT_NOBODY         => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 12,
        CURLOPT_CONNECTTIMEOUT => 6,
        // Commons throttles generic/bot UAs with 429. A real browser UA is
        // what site visitors send, so it reflects what they actually get.
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_RETURNTRANSFER => true,
    ]);
    $handles[$r->id] = ['ch' => $ch, 'row' => $r];
    curl_multi_add_handle($mh, $ch);
}

// Run all in parallel (curl_multi handles parallelism automatically)
$active = null;
do {
    $status = curl_multi_exec($mh, $active);
    if ($active) curl_multi_select($mh, 0.5);
} while ($active && $status == CURLM_OK);

// First (parallel) pass only flags CANDIDATES. The parallel burst trips
// Wikimedia/CDN rate limits (HTTP 429) and occasional timeouts, so a non-2xx
// here does NOT mean the image is dead — it gets re-checked one-by-one below.
$candidates = [];
$ok         = 0;
foreach ($handles as $id => $h) {
    $code = (int) curl_getinfo($h['ch'], CURLINFO_HTTP_CODE);
    $err  = curl_error($h['ch']);
    if ($code >= 200 && $code < 400) {
        $ok++;
    } else {
        $candidates[] = [
            'id'   => $id,
            'name' => $h['row']->name_de,
            'url'  => $h['row']->image_url,
            'code' => $code,
            'err'  => $err ?: null,
        ];
    }
    curl_multi_remove_handle($mh, $h['ch']);
    curl_close($h['ch']);
}
curl_multi_close($mh);

// Single-request HEAD check (own connection, slow, retries on 429/timeout).
$headCheck = function (string $url): array {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_NOBODY         => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_RETURNTRANSFER => true,
    ]);
    curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);
    return ['code' => $code, 'err' => $err ?: null];
};

// Re-verify every candidate sequentially with backoff so transient 429s clear.
$broken = [];
if (count($candidates)) {
    echo "Re-checking ".count($candidates)." candidate(s) one-by-one...\n";
    foreach ($candidates as $c) {
        $res = ['code' => 0, 'err' => null];
        for ($attempt = 1; $attempt <= 3; $attempt++) {
            $res = $headCheck($c['url']);
            if ($res['code'] >= 200 && $res['code'] < 400) {
                break; // recovered — not actually broken
            }
            if ($res['code'] === 429) {
                usleep(2_000_000 * $attempt); // 2s, 4s backoff
                continue;
            }
            usleep(800_000); // small pause for other transient errors, then retry
        }
        if ($res['code'] >= 200 && $res['code'] < 400) {
            $ok++;
            echo "  recovered [{$c['id']}] {$c['name']} (was {$c['code']})\n";
        } else {
            $broken[] = [
                'id'   => $c['id'],
                'name' => $c['name'],
                'url'  => $c['url'],
                'code' => $res['code'],
                'err'  => $res['err'],
            ];
        }
        usleep(1_200_000); // be polite to the CDN between candidates
    }
}

echo "OK: $ok\nBroken: ".count($broken)."\n\n";

if (count($broken)) {
    echo "First 10 broken:\n";
    foreach (array_slice($broken, 0, 10) as $b) {
        echo sprintf("  [%d] %s — code=%s err=%s\n  → %s\n", $b['id'], $b['name'], $b['code'], $b['err'], $b['url']);
    }
}

$outPath = __DIR__ . '/storage/app/broken_uni_images.json';
file_put_contents($outPath, json_encode($broken, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "\nSaved: $outPath\n";
