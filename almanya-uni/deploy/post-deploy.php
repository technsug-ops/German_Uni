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

    // ── DB migrations (non-fatal) ──
    // Yeni migration'ları uygula. Başarısız olsa bile deploy'u BLOKLAMAZ
    // (allOk'a dokunmaz) — cache rebuild yine de çalışır, site ayakta kalır.
    // Migration sorunu logda görünür, manuel müdahale edilir.
    {
        $output = [];
        $exitCode = 0;
        exec('php artisan migrate --force --no-interaction 2>&1', $output, $exitCode);
        if ($exitCode === 0) {
            $log('✅ php artisan migrate --force');
        } else {
            $log('⚠️  migrate --force başarısız (deploy devam ediyor) — ' . implode(' | ', array_slice($output, 0, 3)));
        }
    }

    // Bu komutlar SIRA ÖNEMLİ — clear önce, cache sonra
    $commands = [
        'php artisan view:clear',
        'php artisan config:clear',
        'php artisan route:clear',
        'php artisan cache:clear',
        'php artisan config:cache',
        'php artisan route:cache',
        'php artisan view:cache',
    ];

    foreach ($commands as $cmd) {
        $output = [];
        $exitCode = 0;
        exec($cmd . ' 2>&1', $output, $exitCode);

        if ($exitCode !== 0) {
            $allOk = false;
            $log("❌ FAIL: $cmd (exit $exitCode) — " . implode(' | ', array_slice($output, 0, 3)));
        } else {
            $log("✅ $cmd");
        }
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
