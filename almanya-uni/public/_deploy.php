<?php
/**
 * Deploy Auto-Trigger Webhook
 * ───────────────────────────
 * GitHub Actions workflow deploy-bundle.zip'i upload ettikten sonra bu endpoint'i
 * HTTPS GET ile çağırır. Endpoint:
 *   1. Pulse dosyası VAR mı kontrol eder (workflow zaten upload etti)
 *   2. Yoksa instant 204 No Content (spam saldırı no-op)
 *   3. Varsa: rate limit check (60 sn) → extract ZIP + cache rebuild
 *
 * Güvenlik:
 *   - Sadece pulse dosyası varsa çalışır (saldırgan pulse oluşturamaz — FTP/SSH gerek)
 *   - 60 saniye rate limit (DDoS koruması)
 *   - Output minimal (saldırgan bilgi alamaz)
 *   - public/ altında ama anlamlı saldırı yüzeyi YOK (yetkilendirme pulse-gated)
 *
 * URL: https://applytogerman.com/_deploy.php (veya almanyauni.com/_deploy.php)
 */

// Sadece GET/POST kabul et
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if (!in_array($method, ['GET', 'POST'], true)) {
    http_response_code(405);
    exit('Method not allowed');
}

// Proje kökü (public/'ten bir yukarı)
$appRoot = dirname(__DIR__);
$storageApp = $appRoot . '/storage/app';
$pulseFile = $storageApp . '/.deploy-pulse';
$bundleFile = $storageApp . '/deploy-bundle.zip';
$lockFile = $storageApp . '/.deploy-lock';
$lastRunFile = $storageApp . '/.deploy-last-run';
$logFile = $appRoot . '/storage/logs/deploy-pulse.log';

// Pulse yoksa: spam saldırı no-op → 204 (success but no content)
if (!file_exists($pulseFile)) {
    http_response_code(204);
    exit;
}

// Rate limit: son 60 sn'de çalıştıysa skip
if (file_exists($lastRunFile)) {
    $lastRun = (int) @file_get_contents($lastRunFile);
    if (time() - $lastRun < 60) {
        http_response_code(429);
        header('Retry-After: 60');
        exit('Rate limited');
    }
}
@file_put_contents($lastRunFile, time());

// Eş zamanlı çalıştırmayı engelle
$lock = @fopen($lockFile, 'w');
if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) {
    http_response_code(409);
    exit('Already running');
}

// Buffered output → response sonunda tek seferde yaz
ob_start();
$log = function (string $msg) use ($logFile) {
    @file_put_contents(
        $logFile,
        '[' . date('c') . '] [webhook] ' . $msg . PHP_EOL,
        FILE_APPEND
    );
};

$log('🔄 Deploy webhook triggered');

$allOk = true;

// ─── 1. Bundle extract ───
if (file_exists($bundleFile)) {
    $log('📦 Bundle found: ' . filesize($bundleFile) . ' bytes');

    if (!class_exists('ZipArchive')) {
        $log('❌ ZipArchive PHP extension YOK');
        $allOk = false;
    } else {
        $zip = new ZipArchive();
        $result = $zip->open($bundleFile);
        if ($result !== true) {
            $log("❌ ZIP open FAIL (code $result)");
            $allOk = false;
        } else {
            $fileCount = $zip->numFiles;
            $log("📂 Extracting $fileCount entries...");
            if ($zip->extractTo($appRoot)) {
                $log("✅ Extract OK");
                $zip->close();
                if (@unlink($bundleFile)) {
                    $log('🧹 Bundle removed');
                }
            } else {
                $log("❌ Extract FAIL");
                $zip->close();
                $allOk = false;
            }
        }
    }
} else {
    $log('ℹ️  No bundle (pulse-only trigger)');
}

// (Migrate adımı RESPONSE'tan SONRA çalışır — aşağıya taşındı. Böylece migrate
//  crash etse bile extract + cache-clear tamamlanır, deploy yeşil kalır.)

// ─── 2. Cache reset (manuel file deletion — KAS CLI PHP 7.4 olduğu için artisan kullanamıyoruz) ───
if ($allOk) {
    // Laravel her cache dosyasını runtime'da otomatik yeniden compile eder.
    // Sadece eski derlenmiş dosyaları silmek YETER — artisan komutuna gerek yok.
    //
    // KAS gerçeği: web tarafı PHP 8.3 (Laravel için yeterli) ama CLI default PHP 7.4
    // → exec("php artisan ...") composer platform check fail → artisan'ı kullanma
    $cleaned = [
        'views' => 0,
        'bootstrap' => 0,
        'routes' => 0,
    ];
    foreach (glob($appRoot . '/storage/framework/views/*.php') as $f) {
        if (@unlink($f)) $cleaned['views']++;
    }
    foreach (glob($appRoot . '/bootstrap/cache/*.php') as $f) {
        // services.json hariç tut — Laravel onsuz boot edemez
        if (basename($f) === 'services.json') continue;
        if (@unlink($f)) $cleaned['bootstrap']++;
    }
    // routes-*.php Laravel 9+ için
    foreach (glob($appRoot . '/bootstrap/cache/routes-*.php') as $f) {
        if (@unlink($f)) $cleaned['routes']++;
    }

    // UYGULAMA CACHE'İ (cache()->remember) — file driver: storage/framework/cache/data.
    // KRİTİK: bunu temizlemezsek cache()'lenmiş Eloquent collection'lar (örn. ana sayfa
    // home.featured_*) deploy'lar arası HAYATTA KALIR; şema/kod değişince bozulup
    // deterministik 500 verir. Recursive sil (nested hash dizinleri).
    $cleaned['appcache'] = 0;
    $dataDir = $appRoot . '/storage/framework/cache/data';
    if (is_dir($dataDir)) {
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dataDir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($it as $f) {
            if ($f->isFile()) {
                if (@unlink($f->getPathname())) $cleaned['appcache']++;
            } elseif ($f->isDir()) {
                @rmdir($f->getPathname());
            }
        }
    }

    $log("🧹 Cache cleared — views: {$cleaned['views']}, bootstrap: {$cleaned['bootstrap']}, routes: {$cleaned['routes']}, appcache: {$cleaned['appcache']}");

    // OPcache reset — düzenlenen Model/Controller dosyaları PHP-FPM worker'larında
    // eski bytecode'la çalışmaya devam edebilir (KAS'ta 5+ dk). Reset → yeni kod hemen.
    if (function_exists('opcache_reset')) {
        @opcache_reset();
        $log('🧹 OPcache reset');
    }
    $log('ℹ️  Laravel will auto-rebuild caches on next request (no artisan needed)');
}

// ─── 3. Pulse temizle + state cleanup ───
@unlink($pulseFile);
@unlink($storageApp . '/.ftp-deploy-bundle-state.json');
$log('🧹 Pulse + state cleared');

$log($allOk ? '🎉 Deploy completed successfully' : '⚠️  Deploy completed with errors');

// Log housekeeping: 100KB'ı geçtiyse son 5KB'ı tut
clearstatcache();
if (file_exists($logFile) && filesize($logFile) > 100 * 1024) {
    $content = file_get_contents($logFile);
    file_put_contents($logFile, substr($content, -5 * 1024));
}

ob_end_clean();

// Response'u workflow'a ÖNCE gönder (migrate'ten önce). Böylece migrate crash
// etse bile workflow 200 alır → deploy yeşil + extract/cache zaten tamamlandı.
http_response_code($allOk ? 200 : 500);
header('Content-Type: text/plain');
echo $allOk ? "OK\n" : "ERR\n";
if (function_exists('fastcgi_finish_request')) {
    @fastcgi_finish_request();
} else {
    @ob_flush();
    @flush();
}

// ─── 4. DB migrations + içerik render (RESPONSE SONRASI, best-effort) ───
// Sunucuda cron YOK → migration'lar SADECE burada uygulanır. Response gönderildiği
// için migrate burada crash/timeout olsa bile deploy yeşil ve kod/route güncel.
// in-process Kernel::call (exec/CLI yok → KAS PHP 7.4 CLI sorunu yaşanmaz).
if ($allOk && is_file($appRoot . '/vendor/autoload.php')) {
    @ini_set('memory_limit', '512M');
    @set_time_limit(600);
    try {
        require $appRoot . '/vendor/autoload.php';
        $laravel = require $appRoot . '/bootstrap/app.php';
        $kernel = $laravel->make(Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();
        $code = $kernel->call('migrate', ['--force' => true, '--no-interaction' => true]);
        $log('🛠️  migrate exit ' . $code . ' — ' . mb_substr(trim((string) $kernel->output()), 0, 1500));
        try {
            $kernel->call('blog:render-html', ['--apply' => true]);
            $log('🖋️  blog:render-html OK');
        } catch (\Throwable $e) {
            $log('⚠️  blog:render-html FAIL — ' . mb_substr($e->getMessage(), 0, 300));
        }
        // #12 storytelling: blog brief'leri için infografik üret (idempotent, Gemini)
        try {
            $kernel->call('storytelling:infographics');
            $log('📊 storytelling:infographics OK');
        } catch (\Throwable $e) {
            $log('⚠️  storytelling:infographics FAIL — ' . mb_substr($e->getMessage(), 0, 300));
        }
    } catch (\Throwable $e) {
        $log('⚠️  migrate FAIL — ' . mb_substr($e->getMessage(), 0, 1500));
    }
}

// Lock release (migrate sonrası)
flock($lock, LOCK_UN);
fclose($lock);
@unlink($lockFile);
