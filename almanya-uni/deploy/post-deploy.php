<?php
/**
 * Post-Deploy Cache Rebuilder
 * ───────────────────────────
 * GitHub Actions deploy sonunda `storage/app/.deploy-pulse` dosyası upload edilir.
 * Bu script KAS Cronjob'tan periyodik çağrılır; pulse dosyası varsa cache rebuild
 * yapıp dosyayı siler. Pulse yoksa hızlıca çıkar (idempotent, ucuz çağrı).
 *
 * KAS Cronjob (1-2 dk aralıkla):
 *   Befehl: php /www/htdocs/w02196cc/almanya-uni/deploy/post-deploy.php
 *   Tarih: dakika başı, periyodik
 *
 * Web-exposed DEĞİL (public/ dışında, sadece CLI'dan çağrılabilir).
 * SAPI guard: HTTP isteğine yanıt vermez (güvenlik).
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI only');
}

$appRoot = realpath(__DIR__ . '/..');
$pulseFile = $appRoot . '/storage/app/.deploy-pulse';
$logFile = $appRoot . '/storage/logs/deploy-pulse.log';

// Pulse yoksa hiç bir şey yapma (her dakika çağrılsa bile maliyet sıfıra yakın)
if (!file_exists($pulseFile)) {
    exit(0);
}

// Log helper
$log = function (string $msg) use ($logFile) {
    @file_put_contents(
        $logFile,
        '[' . date('c') . '] ' . $msg . PHP_EOL,
        FILE_APPEND
    );
};

$log('🔄 Deploy pulse detected, rebuilding cache...');

chdir($appRoot);

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

$allOk = true;
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

// Pulse dosyasını sil (bir sonraki deploy'a kadar tetiklenmesin)
if (@unlink($pulseFile)) {
    $log('🧹 Pulse file removed');
} else {
    $log('⚠️  Pulse file could not be removed (permission?)');
}

$log($allOk ? '🎉 Cache rebuild successful' : '⚠️  Cache rebuild completed with errors');

// Log dosyası 100KB'ı geçtiyse trim et (son 5KB'ı tut)
clearstatcache();
if (file_exists($logFile) && filesize($logFile) > 100 * 1024) {
    $content = file_get_contents($logFile);
    file_put_contents($logFile, substr($content, -5 * 1024));
}

exit($allOk ? 0 : 1);
