<?php

namespace App\Support;

/**
 * hreflang kararlarının TEK kaynağı — sayfa <head>'i ve sitemap aynı sonucu vermek zorunda.
 *
 * Canlı denetimde (2026-09-25) ikisi ayrışmıştı: sitemap x-default'u activeLocales[0]
 * (tr) seçerken sayfa config('locale.default') (en) seçiyordu; Google aynı kümeye iki
 * farklı x-default görüyordu. Karar artık yalnızca burada verilir.
 */
final class Hreflang
{
    /**
     * hreflang üretilen diller, config sırasıyla (aktif ve "yakında" olmayanlar).
     *
     * @return array<int, string>
     */
    public static function activeLocales(): array
    {
        return collect(config('locale.locales', []))
            ->filter(fn ($c) => ! empty($c['active']) && empty($c['coming_soon']))
            ->keys()
            ->all();
    }

    /**
     * x-default için tercih edilen dil: uygulamanın varsayılan dili (şu an en) hreflang
     * üretiliyorsa o, değilse ilk aktif dil.
     */
    public static function preferredDefault(): string
    {
        $active = self::activeLocales();
        $default = (string) config('locale.default');

        return in_array($default, $active, true) ? $default : ($active[0] ?? $default);
    }

    /**
     * Kümenin GERÇEK alternatiflerinden x-default: tercih edilen dil kümedeyse o, yoksa
     * activeLocales sırasıyla kümede bulunan ilk dil. Kümede olmayan bir dile asla
     * işaret edilmez (olmayan URL / 404 üretilmez).
     *
     * @param  array<string, string>  $alternates  locale => URL
     */
    public static function xDefault(array $alternates): ?string
    {
        $alternates = array_filter($alternates, fn ($url) => is_string($url) && $url !== '');

        $preferred = self::preferredDefault();
        if (isset($alternates[$preferred])) {
            return $alternates[$preferred];
        }

        foreach (self::activeLocales() as $locale) {
            if (isset($alternates[$locale])) {
                return $alternates[$locale];
            }
        }

        return null;
    }
}
