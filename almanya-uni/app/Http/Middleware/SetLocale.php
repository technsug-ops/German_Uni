<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * URL-otoriter çok-dilli locale yönetimi (SEO-temiz).
     *
     * - Locale, URL prefix'inden belirlenir. Prefix yoksa = default dil (config('locale.default')).
     * - route() çağrıları otomatik locale-aware olsun diye URL::defaults(['locale' => ...]) set edilir
     *   (default dil için prefix yok → null).
     * - Henüz hazır olmayan (coming_soon) veya kapalı (active=false) dile gelen istek,
     *   ilk AKTİF dile (ör. /tr) 302 ile yönlendirilir — içerik o dilde yok.
     *
     * Accept-Language header kasıtlı devre dışı (Almanya'daki TR öğrenci tarayıcısı `de` gönderir).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $default = $this->resolveDefaultFromDomain($request, config('locale.default', 'en'));
        $locale  = $this->resolveLocale($request, $default);
        $cfg     = config("locale.locales.$locale", []);

        // Hazır olmayan dil → ilk aktif dile yönlendir (URL'i değiştirir, içerik tutarlı kalır)
        if (empty($cfg['active']) || ! empty($cfg['coming_soon'])) {
            $active = $this->firstActiveLocale();
            if ($active && $active !== $locale) {
                return redirect($this->swapLocalePath($request, $active, $default), 302);
            }
        }

        App::setLocale($locale);

        // KRİTİK: {locale} route param'ını düşür ki controller metoduna POZİSYONEL geçmesin.
        // (Laravel class-olmayan method param'larını route param sırasına göre doldurur;
        //  düşürmezsek show($slugOrId) metodu slug yerine 'tr' alır → 404.)
        $request->route()?->forgetParameter('locale');

        // route() üretimini locale-aware yap: route prefix '{locale}' zorunlu olduğu için
        // her zaman locale değerini geç (null bırakamayız — required param exception verir).
        URL::defaults(['locale' => $locale]);

        view()->share('currentLocale', $locale);
        view()->share('localeConfig', $cfg);

        return $next($request);
    }

    /**
     * Locale = URL prefix (route param) — SEO için URL otoriter.
     * URL prefix YOKSA: kullanıcının dil-switcher ile seçtiği dili (cookie/session)
     * onurlandır; yoksa domain default. Bot'lar cookie göndermediği için domain
     * default'a düşer → canonical/hreflang stabil kalır (SEO güvenli).
     *
     * Bu, kayıt sonrası /dashboard ve prefix'siz akışların kullanıcının seçtiği
     * dilde kalmasını sağlar (önceden applytogerman.com'da TR seçmiş kullanıcı bile
     * her geçişte EN'e düşüyordu).
     */
    private function resolveLocale(Request $request, string $default): string
    {
        $supported = array_keys(config('locale.locales', []));

        $routeLocale = $request->route('locale');
        if ($routeLocale && in_array($routeLocale, $supported, true)) {
            return $routeLocale; // URL otoriter
        }

        // URL prefix yok → kullanıcının açıkça seçtiği dil (sadece switcher set eder)
        $chosen = $request->cookie('locale');
        if (! $chosen && $request->hasSession()) {
            $chosen = $request->session()->get('locale');
        }
        if ($chosen && in_array($chosen, $supported, true)) {
            $cfg = config("locale.locales.$chosen", []);
            if (! empty($cfg['active']) && empty($cfg['coming_soon'])) {
                return $chosen;
            }
        }

        return $default;
    }

    /**
     * Domain-aware default locale.
     * almanyauni.com    → 'tr' (AlmanyaUni brand'in default_locale'ı)
     * applytogerman.com → 'en' (ApplyToGerman brand'in default_locale'ı)
     */
    private function resolveDefaultFromDomain(Request $request, string $configDefault): string
    {
        $host = strtolower(preg_replace('/^www\./', '', $request->getHost()));
        $domains = config('brand.domains', []);
        $brandKey = $domains[$host] ?? null;
        if ($brandKey) {
            $brands = config('brand.brands', []);
            $brand = $brands[$brandKey] ?? [];
            if (! empty($brand['default_locale'])) {
                return $brand['default_locale'];
            }
        }
        return $configDefault;
    }

    private function firstActiveLocale(): ?string
    {
        return collect(config('locale.locales', []))
            ->filter(fn ($c) => ! empty($c['active']) && empty($c['coming_soon']))
            ->keys()
            ->first();
    }

    /**
     * Mevcut path'in locale prefix'ini hedef locale ile değiştirir (default ise prefix'siz).
     */
    private function swapLocalePath(Request $request, string $target, string $default): string
    {
        $segments = array_values(array_filter(explode('/', $request->path())));
        $allLocales = array_keys(config('locale.locales', []));

        // Baştaki locale segmentini at
        if (! empty($segments[0]) && in_array($segments[0], $allLocales, true)) {
            array_shift($segments);
        }

        $prefix = $target === $default ? '' : "/$target";
        $rest   = $segments ? '/' . implode('/', $segments) : '';
        $path   = ($prefix . $rest) ?: '/';

        $qs = $request->getQueryString();
        return $path . ($qs ? '?' . $qs : '');
    }
}
