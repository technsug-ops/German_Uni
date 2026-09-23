<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Çerez rızasının TEK kaynağı. Sunucu (TrackPageView), görünüm (izleyici
 * partial'ı) ve banner aynı kararı vermek zorunda; karar üç yerde ayrı ayrı
 * yorumlanırsa "politika ne diyor" ile "kod ne yapıyor" kaçınılmaz olarak ayrışır.
 *
 * TASARIM: varsayılan REDDİ temsil eder. Karar verilmemiş ziyaretçi, reddetmiş
 * ziyaretçiyle aynı muameleyi görür. Eski kod yalnızca "rejected" değerini
 * engelliyordu; karar verilmemiş durumda analitik çerez yazıp sayfa görüntülemesi
 * kaydediyordu — GDPR/ePrivacy açısından hatalıydı.
 *
 * GERİYE UYUMLULUK: eski banner 'accepted' / 'rejected' yazıyordu.
 *   'accepted' → analitik RIZA VAR sayılır (kullanıcı bilinçli olarak kabul etti)
 *   'accepted' → pazarlama RIZA YOK sayılır. Eski banner pazarlamadan söz etse de
 *                Meta/TikTok kimlikleri hiç tanımlı olmadığı için o araçlar zaten
 *                hiç çalışmadı; dar yorum gizlilik lehinedir.
 *   diğer her değer ve yokluk → reddedilmiş sayılır.
 */
final class Consent
{
    /** Analitik kategorisi (GA4, Microsoft Clarity, site içi sayaç). */
    public const COOKIE = 'almanyauni_consent';

    /** Pazarlama kategorisi (Meta, TikTok, Google Ads, GTM). */
    public const COOKIE_MARKETING = 'almanyauni_consent_mkt';

    public const GRANTED = 'granted';

    public const DENIED = 'denied';

    /** 1 yıl (gün). */
    public const DAYS = 365;

    public static function analytics(?Request $request = null): bool
    {
        $v = self::raw($request, self::COOKIE);

        // 'accepted' eski biçimdir; yeni biçim 'granted'.
        return $v === self::GRANTED || $v === 'accepted';
    }

    public static function marketing(?Request $request = null): bool
    {
        return self::raw($request, self::COOKIE_MARKETING) === self::GRANTED;
    }

    /** Ziyaretçi bir seçim yaptı mı? (banner'ı göstermek için) */
    public static function decided(?Request $request = null): bool
    {
        $v = self::raw($request, self::COOKIE);

        return in_array($v, [self::GRANTED, self::DENIED, 'accepted', 'rejected'], true);
    }

    private static function raw(?Request $request, string $name): ?string
    {
        $request ??= request();

        if (! $request) {
            return null;
        }

        $v = $request->cookie($name);

        return is_string($v) ? $v : null;
    }
}
