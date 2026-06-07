<?php
/**
 * Post-Deploy: Bundle Extract + Cache Rebuilder
 * ──────────────────────────────────────────────
 * GitHub Actions deploy akışı:
 *   1. Workflow source kodu tek bir ZIP yapar (deploy-bundle.zip).
 *   2. ZIP + .deploy-pulse dosyalarını storage/app/ altına FTPS ile gönderir.
 *   3. KAS Cronjob bu scripti her dakika çağırır.
 *   4. Bu script pulse görünce: ZIP'i app root'a extract eder, cache rebuild yapar.
 *
 * KAS Cronjob (1 dk):
 *   php /www/htdocs/w02196cc/almanya-uni/deploy/post-deploy.php
 *
 * Web-exposed DEĞİL — sadece CLI'dan çağrılabilir (SAPI guard).
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI only');
}

$appRoot = realpath(__DIR__ . '/..');
$storageApp = $appRoot . '/storage/app';
$pulseFile = $storageApp . '/.deploy-pulse';
$bundleFile = $storageApp . '/deploy-bundle.zip';
$logFile = $appRoot . '/storage/logs/deploy-pulse.log';

// Pulse yoksa hiçbir şey yapma (her dakika çağrılsa bile maliyet sıfıra yakın)
if (!file_exists($pulseFile)) {
    exit(0);
}

// Eş zamanlı çalıştırmayı engelle (cronjob 1 dk, ama önceki devam ediyor olabilir)
$lockFile = $storageApp . '/.deploy-lock';
$lock = @fopen($lockFile, 'w');
if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) {
    exit(0); // başka bir instance çalışıyor
}

// Log helper
$log = function (string $msg) use ($logFile) {
    @file_put_contents(
        $logFile,
        '[' . date('c') . '] ' . $msg . PHP_EOL,
        FILE_APPEND
    );
};

$log('🔄 Deploy pulse detected, starting...');

$allOk = true;

// ───────────────────────────────────────────────
// STEP 1: Bundle ZIP varsa extract et
// ───────────────────────────────────────────────
if (file_exists($bundleFile)) {
    $log('📦 Bundle ZIP found: ' . filesize($bundleFile) . ' bytes');

    if (!class_exists('ZipArchive')) {
        $log('❌ ZipArchive PHP extension YOK — zip extract edilemiyor');
        $allOk = false;
    } else {
        $zip = new ZipArchive();
        $openResult = $zip->open($bundleFile);

        if ($openResult !== true) {
            $log("❌ ZIP open FAIL (code $openResult)");
            $allOk = false;
        } else {
            $fileCount = $zip->numFiles;
            $log("📂 Extracting $fileCount entries to $appRoot ...");

            if ($zip->extractTo($appRoot)) {
                $log("✅ Extract successful");
                $zip->close();
                // Zip'i sil — bir sonraki deploy'a kadar duracak yer yok
                if (@unlink($bundleFile)) {
                    $log('🧹 Bundle ZIP removed');
                } else {
                    $log('⚠️  Bundle ZIP could not be removed');
                }
            } else {
                $log("❌ Extract FAIL");
                $zip->close();
                $allOk = false;
            }
        }
    }
} else {
    $log('ℹ️  No bundle ZIP — pulse-only run (manual trigger)');
}

// ───────────────────────────────────────────────
// STEP 2: Cache rebuild — extract başarılıysa veya pulse-only
// ───────────────────────────────────────────────
if ($allOk) {
    chdir($appRoot);

    // Artisan'ı Laravel kernel üzerinden çağır — exec('php artisan ...') KAS cron
    // ortamında `php` PATH'te olmadığı / exec kısıtlı olduğu için sessizce
    // başarısız olabiliyordu (migration'lar hiç uygulanmıyordu). Kernel::call
    // PHP binary'sine ve exec'e bağımlı değil.
    $kernel = null;
    try {
        require $appRoot . '/vendor/autoload.php';
        $laravel = require $appRoot . '/bootstrap/app.php';
        $kernel = $laravel->make(Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();
    } catch (\Throwable $e) {
        $log('❌ Laravel bootstrap FAIL — ' . $e->getMessage());
        $kernel = null;
    }

    $artisan = function (string $command, array $params = []) use (&$kernel, $log): bool {
        if (! $kernel) return false;
        try {
            $code = $kernel->call($command, $params);
            $out = trim((string) $kernel->output());
            if ($code === 0) {
                $log("✅ artisan $command");
                return true;
            }
            $log("⚠️  artisan $command exit $code — " . mb_substr($out, 0, 300));
        } catch (\Throwable $e) {
            $log("⚠️  artisan $command exception — " . mb_substr($e->getMessage(), 0, 300));
        }
        return false;
    };

    // ── DB migrations (non-fatal) — başarısız olsa bile cache rebuild + site ayakta ──
    $artisan('migrate', ['--force' => true, '--no-interaction' => true]);

    // ── İçerik self-heal: content_html boş kalan yazıları content_md'den render et ──
    // Legacy/mutator-bypass importlarda html üretilmemiş olabilir → blog boş görünür.
    // Idempotent: dolu olanları atlar, ikinci deploy'da no-op. Non-fatal.
    $artisan('blog:render-html', ['--apply' => true]);

    // Cache: SIRA ÖNEMLİ — clear önce, cache sonra
    foreach (['view:clear', 'config:clear', 'route:clear', 'cache:clear'] as $cmd) {
        $artisan($cmd);
    }
    foreach (['config:cache', 'route:cache', 'view:cache'] as $cmd) {
        if (! $artisan($cmd)) $allOk = false;
    }
}

// ───────────────────────────────────────────────
// STEP 3: Pulse sil (bir sonraki deploy'a kadar tetiklenmesin)
// ───────────────────────────────────────────────
if (@unlink($pulseFile)) {
    $log('🧹 Pulse file removed');
} else {
    $log('⚠️  Pulse file could not be removed');
}

$log($allOk ? '🎉 Deploy completed successfully' : '⚠️  Deploy completed with errors');

// ───────────────────────────────────────────────
// Log housekeeping: 100KB'ı geçtiyse son 5KB'ı tut
// ───────────────────────────────────────────────
clearstatcache();
if (file_exists($logFile) && filesize($logFile) > 100 * 1024) {
    $content = file_get_contents($logFile);
    file_put_contents($logFile, substr($content, -5 * 1024));
}

// Lock release
flock($lock, LOCK_UN);
fclose($lock);
@unlink($lockFile);

exit($allOk ? 0 : 1);
