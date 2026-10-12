<?php

namespace App\Support;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Eski slug → yeni slug (slug_redirects tablosu). URL kuralı: bütün slug'lar İngilizce; eski Türkçe slug'lar
 * kalıcı 301 ile yeni adrese taşınır. Zincir (a→b→c) en fazla 5 adım izlenir.
 */
final class SlugRedirects
{
    public static function to(string $entity, string $slug): ?string
    {
        static $hasTable = null;
        $hasTable ??= Schema::hasTable('slug_redirects');
        if (! $hasTable) {
            return null;
        }
        $current = $slug;
        for ($i = 0; $i < 5; $i++) {
            $next = DB::table('slug_redirects')->where('entity', $entity)->where('from_slug', $current)->value('to_slug');
            if ($next === null || $next === $current) {
                return $i === 0 ? null : $current;
            }
            $current = $next;
        }

        return $current;
    }

    /**
     * Geçerli rota için verilen parametreyi yeni slug'la değiştirerek 301 üretir; sorgu dizesi korunur.
     * Yönlendirme yoksa null (çağıran 404'e düşer).
     */
    public static function response(string $entity, string $param, string $slug): ?RedirectResponse
    {
        $to = self::to($entity, $slug);
        $route = request()->route();
        if ($to === null || ! $route || ! $route->getName()) {
            return null;
        }
        $params = array_merge($route->parameters(), [$param => $to]);
        $query = request()->query();

        return redirect()->route($route->getName(), $params + $query, 301);
    }

    /** /programs?field=eski gibi sorgu parametreleri için: değişen bir parametre varsa 301, yoksa null. */
    public static function queryResponse(array $map): ?RedirectResponse
    {
        $query = request()->query();
        $changed = false;
        foreach ($map as $param => $entity) {
            $value = $query[$param] ?? null;
            if (is_string($value) && $value !== '' && ($to = self::to($entity, $value)) !== null) {
                $query[$param] = $to;
                $changed = true;
            }
        }
        if (! $changed) {
            return null;
        }
        $route = request()->route();

        return redirect()->route($route->getName(), $route->parameters() + $query, 301);
    }
}
