<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * FlatReklam SEO API auth. Token /admin → Ayarlar → Entegrasyonlar'dan gelir
 * (setting('flatreklam_api_token')). Bearer veya HTTP Basic (parola = token).
 */
class FlatReklamAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) setting('flatreklam_api_token', '');
        if ($expected === '') {
            return response()->json(['error' => 'FlatReklam entegrasyonu yapılandırılmamış', 'code' => 'NOT_CONFIGURED'], 503);
        }

        $provided = $request->bearerToken() ?: $request->getPassword();

        if (! $provided || ! hash_equals($expected, (string) $provided)) {
            return response()->json(['error' => 'Geçersiz veya eksik token', 'code' => 'UNAUTHORIZED'], 401);
        }

        return $next($request);
    }
}
