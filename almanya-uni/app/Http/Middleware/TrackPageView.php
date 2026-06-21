<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Self-hosted page view tracker — Google Analytics yerine kullanılabilir.
 * Anonim ziyaretçiler için "almanyauni_uid" cookie kullanır (1 yıl).
 * Bot trafiğini user_agent ile filtreler, admin/api/static dosyaları atlar.
 */
class TrackPageView
{
    private const COOKIE_NAME = 'almanyauni_uid';
    private const COOKIE_TTL = 60 * 24 * 365; // 1 yıl

    /**
     * terminate()'te yazılacak satır — handle()'da hazırlanır (TTFB'ye girmez).
     * DİKKAT: Laravel terminate()'i container'dan TAZE bir middleware instance'ı ile
     * çağırır (bu middleware singleton değil), yani instance property'leri taşınmaz.
     * Bu yüzden satırı Request nesnesine (handle↔terminate arasında AYNI instance) yazıyoruz.
     */
    private const REQ_ATTR = '_pageview_pending';

    private const EXCLUDED_PATHS = [
        'admin', 'api', 'livewire', 'sanctum',
        '_debugbar', 'telescope', 'horizon',
        'sitemap.xml', 'robots.txt', 'favicon.ico',
        'site.webmanifest', 'manifest.json', 'sw.js',
        'forum', // phpBB ayrı tracking yapar
    ];

    // Asset/feed uzantıları — bunlar SAYFA değil; sayılmamalı (webmanifest, ics takvim,
    // json/xml feed, statik dosyalar). /site.webmanifest'in "en çok ziyaret edilen sayfa"
    // çıkmasının sebebi buydu.
    private const ASSET_EXT = '/\.(webmanifest|ics|json|xml|txt|rss|atom|csv|css|js|map|png|jpe?g|gif|svg|webp|avif|ico|woff2?|ttf|eot|pdf|zip|gz|mp3|mp4|webm)$/i';

    private const BOT_PATTERNS = [
        'bot', 'crawler', 'spider', 'slurp', 'mediapartners',
        'facebookexternalhit', 'whatsapp', 'telegrambot',
        'lighthouse', 'pagespeed', 'pingdom', 'headlesschrome', 'phantomjs',
        'python-requests', 'python-httpx', 'curl/', 'wget', 'go-http', 'okhttp',
        'axios', 'node-fetch', 'scrapy', 'libwww', 'java/', 'apache-httpclient',
        'http_request', 'dataforseo', 'semrush', 'ahrefs', 'mj12', 'dotbot',
        'petalbot', 'bytespider', 'censys', 'zgrab', 'expanse', 'gptbot',
        'preview', 'monitoring', 'uptimerobot', 'statuscake', 'site24x7',
        'powershell', 'seoaudit', 'almanyauni-', // PowerShell scriptleri + sitenin kendi araçları
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $started = microtime(true);
        $response = $next($request);

        try {
            // Sadece satırı HAZIRLA + (gerekiyorsa) uid cookie'sini response'a yaz.
            // Asıl DB INSERT terminate()'te — yani response gönderildikten SONRA.
            $this->record($request, $response, microtime(true) - $started);
        } catch (\Throwable $e) {
            \Log::warning('TrackPageView error: ' . $e->getMessage());
        }

        return $response;
    }

    /**
     * Response istemciye gönderildikten sonra çalışır (PHP-FPM fastcgi_finish_request).
     * Ağır/yavaş DB yazımı TTFB'ye eklenmez.
     */
    public function terminate(Request $request, Response $response): void
    {
        $pending = $request->attributes->get(self::REQ_ATTR);
        if (! is_array($pending)) {
            return;
        }

        try {
            DB::table('page_views')->insert($pending);
        } catch (\Throwable $e) {
            \Log::warning('TrackPageView insert error: ' . $e->getMessage());
        }
    }

    private function record(Request $request, Response $response, float $duration): void
    {
        // Sadece GET + 2xx
        if ($request->method() !== 'GET') return;
        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) return;

        // SADECE HTML sayfa görüntülemeleri sayılır — webmanifest/ics/json/xml/feed,
        // resim, css/js gibi asset'ler "sayfa" DEĞİL. Content-Type ile en sağlam filtre.
        $contentType = strtolower((string) $response->headers->get('Content-Type'));
        if ($contentType !== '' && ! str_contains($contentType, 'text/html')) return;

        $path = '/' . ltrim($request->path(), '/');

        // Asset uzantısı → atla (Content-Type boş kalsa bile yakalar)
        if (preg_match(self::ASSET_EXT, $path)) return;

        // Dışlama
        foreach (self::EXCLUDED_PATHS as $excl) {
            if ($path === '/' . $excl || str_starts_with($path, '/' . $excl . '/')) return;
        }

        // KVKK — kullanıcı cookie consent'i reddettiyse hiç kayıt yapma
        // (almanyauni_uid yoksa ve daha önce reddedilmişse banner LocalStorage'da takip ediyor;
        // burada server-side koruma yok ama uid set edilmemiş olur — yine de tutup is_bot=1 ile filtreleriz)
        if ($request->cookie('almanyauni_consent') === 'rejected') return;

        $ua = (string) $request->userAgent();
        $isBot = $this->isBot($ua);

        // Bot trafiği opsiyonel — config'e bağlı (default kayıt edip is_bot=1 ile işaretle, sonra filtrele)
        $sid = $this->getOrCreateSessionId($request, $response);

        $referrer = $request->headers->get('referer');
        $refHost = $referrer ? parse_url($referrer, PHP_URL_HOST) : null;
        $myHost = $request->getHost();
        // Kendi sitemizden gelen referer'ı temizle
        if ($refHost === $myHost) {
            $referrer = null;
            $refHost = null;
        }

        // INSERT'i terminate()'e bırak — response gönderildikten sonra yazılır.
        // Satırı Request'e yaz: terminate() taze bir instance'ta çalışsa da Request aynıdır.
        $request->attributes->set(self::REQ_ATTR, [
            'session_id' => $sid,
            'user_id' => auth()->id(),
            'path' => mb_substr($path, 0, 500),
            'referrer' => $referrer ? mb_substr($referrer, 0, 500) : null,
            'referrer_host' => $refHost ? mb_substr($refHost, 0, 120) : null,
            'user_agent' => mb_substr($ua, 0, 255),
            'is_bot' => $isBot ? 1 : 0,
            'ip_hash' => md5(($request->ip() ?? '') . config('app.key')),
            'response_ms' => (int) ($duration * 1000),
            'created_at' => now(),
        ]);
    }

    private function isBot(string $ua): bool
    {
        // UA yok / şüpheli kısa → neredeyse her zaman bot/script
        if (trim($ua) === '' || mb_strlen($ua) < 15) return true;

        $lower = mb_strtolower($ua);
        foreach (self::BOT_PATTERNS as $p) {
            if (str_contains($lower, $p)) return true;
        }
        return false;
    }

    private function getOrCreateSessionId(Request $request, Response $response): string
    {
        $sid = $request->cookie(self::COOKIE_NAME);
        if ($sid && preg_match('/^[a-f0-9]{32}$/', $sid)) {
            return $sid;
        }

        $sid = bin2hex(random_bytes(16));
        $response->headers->setCookie(cookie(
            self::COOKIE_NAME,
            $sid,
            self::COOKIE_TTL,
            null, null, false, true, false, 'lax'
        ));
        return $sid;
    }
}
