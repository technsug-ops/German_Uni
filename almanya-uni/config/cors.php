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

$origins = array_values(array_filter(array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS', '*')))));
$patterns = array_values(array_filter(array_map('trim', explode(',', env('CORS_ALLOWED_ORIGIN_PATTERNS', '')))));

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
