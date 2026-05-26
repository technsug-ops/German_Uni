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

// ─── 2. Cache rebuild ───
if ($allOk) {
    chdir($appRoot);

    // Manuel cache temizliği (kompilasyon sırasında yol farklı olabilir)
    foreach (glob($appRoot . '/storage/framework/views/*.php') as $f) @unlink($f);
    foreach (glob($appRoot . '/bootstrap/cache/*.php') as $f) @unlink($f);
    $log('🧹 Old caches cleared (views + bootstrap)');

    // Artisan komutları: clear → cache (sıra önemli)
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
            $log("❌ $cmd (exit $exitCode) — " . implode(' | ', array_slice($output, 0, 2)));
        } else {
            $log("✅ $cmd");
        }
    }
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

// Lock release
flock($lock, LOCK_UN);
fclose($lock);
@unlink($lockFile);

ob_end_clean();

// Minimal output (info leak'i önle)
http_response_code($allOk ? 200 : 500);
header('Content-Type: text/plain');
echo $allOk ? "OK\n" : "ERR\n";
