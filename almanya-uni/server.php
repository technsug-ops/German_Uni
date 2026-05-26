<?php

/**
 * Laravel + phpBB ortak development router.
 *
 * artisan serve PHP built-in server'ı bu dosyayı router olarak kullanır.
 * Standart Laravel server.php davranışına ek olarak phpBB'nin
 * `app.php/help/faq` gibi PATH_INFO URL'lerini doğru şekilde dispatch eder.
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$publicPath = __DIR__ . DIRECTORY_SEPARATOR . 'public';

// 1) Doğrudan var olan dosya (asset veya .php) — built-in server kendi serve etsin.
if ($uri !== '/' && file_exists($publicPath . $uri)) {
    return false;
}

// 2) PHP dosyası + PATH_INFO (phpBB: /forum/app.php/help/faq, vb.)
if (preg_match('#^(/.+\.php)(/.*)$#', $uri, $m)) {
    $scriptPath = $publicPath . str_replace('/', DIRECTORY_SEPARATOR, $m[1]);
    if (file_exists($scriptPath)) {
        $_SERVER['SCRIPT_NAME']     = $m[1];
        $_SERVER['SCRIPT_FILENAME'] = $scriptPath;
        $_SERVER['PHP_SELF']        = $m[1] . $m[2];
        $_SERVER['PATH_INFO']       = $m[2];
        $_SERVER['ORIG_PATH_INFO']  = $m[2];
        chdir(dirname($scriptPath));
        require $scriptPath;
        return true;
    }
}

// 3) Fallback: Laravel
require_once $publicPath . DIRECTORY_SEPARATOR . 'index.php';
