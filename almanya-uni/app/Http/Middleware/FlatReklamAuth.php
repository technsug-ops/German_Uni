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
        $provided = (string) ($request->bearerToken() ?: $request->getPassword() ?: '');
        if ($provided === '') {
            return response()->json(['error' => 'Token eksik', 'code' => 'UNAUTHORIZED'], 401);
        }

        // 1) Entegrasyonlar → FlatReklam ayar token'ı (basit yol)
        $settingToken = (string) setting('flatreklam_api_token', '');
        if ($settingToken !== '' && hash_equals($settingToken, $provided)) {
            return $next($request);
        }

        // 2) API İstemcileri (ApiClient) Sanctum token'ı — aktif client'a aitse kabul
        $pat = \Laravel\Sanctum\PersonalAccessToken::findToken($provided);
        if ($pat
            && $pat->tokenable instanceof \App\Models\ApiClient
            && $pat->tokenable->is_active) {
            $pat->forceFill(['last_used_at' => now()])->save();
            $request->setUserResolver(fn () => $pat->tokenable);
            return $next($request);
        }

        return response()->json(['error' => 'Geçersiz token', 'code' => 'UNAUTHORIZED'], 401);
    }
}
