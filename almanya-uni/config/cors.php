<?php

/*
|--------------------------------------------------------------------------
| CORS yapılandırması
|--------------------------------------------------------------------------
|
| Public API ve sanctum çerez akışı için origin politikası.
|
| Üretimde `CORS_ALLOWED_ORIGINS` env değişkenini explicit domain listesine
| ayarla. `*` sadece geliştirmede kabul edilebilir. `*.almanyauni.de` gibi
| wildcard pattern'ler için `CORS_ALLOWED_ORIGIN_PATTERNS` kullan.
|
*/

// Güvenli varsayılan: env hiç set edilmemişse PROD'da `*`'a düşme — bilinen
// brand domain'lerine kilitlen (scraper'ın tarayıcıdan API kazımasını engeller).
// Sadece local/dev'de `*` serbest (geliştirme kolaylığı).
// Not: config dosyaları çok erken yüklenir → app()->environment() yerine env()
// kullan (o aşamada 'env' container binding'i henüz hazır değil).
$isDev = in_array(env('APP_ENV', 'production'), ['local', 'testing'], true);
$defaultOrigins = $isDev
    ? '*'
    : 'https://almanyauni.com,https://www.almanyauni.com,https://applytogerman.com,https://www.applytogerman.com';
$defaultPatterns = $isDev
    ? ''
    : 'https://*.almanyauni.com,https://*.applytogerman.com';

$origins = array_values(array_filter(array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS', $defaultOrigins)))));
$patterns = array_values(array_filter(array_map('trim', explode(',', env('CORS_ALLOWED_ORIGIN_PATTERNS', $defaultPatterns)))));

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => $origins,

    'allowed_origins_patterns' => $patterns,

    'allowed_headers' => ['*'],

    'exposed_headers' => ['X-RateLimit-Limit', 'X-RateLimit-Remaining', 'X-RateLimit-Reset'],

    'max_age' => 86400,

    'supports_credentials' => false,

];
