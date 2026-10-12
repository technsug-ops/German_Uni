<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Basit toplama captcha'sı — üçüncü taraf servis ve görsel olmadan form spam'ini azaltır.
 *
 * SUNUCU TARAFLI, OTURUMA BAĞLI: cevap istemciye HİÇ gitmez. İstemci yalnız rastgele, anlamsız bir
 * referans (captcha_key) taşır; cevap, süre ve kullanım durumu oturumda tutulur.
 *  - Süreli: her soru TTL içinde geçerlidir.
 *  - Tek kullanımlık: doğru cevapla doğrulanan referans hemen silinir (replay yok).
 *  - Oturuma bağlı: başka oturumdan gelen referans tanınmaz.
 *  - Aynı sayfada birden çok captcha (bülten inline + sticky) için oturumda birden çok soru saklanır.
 * Eski tasarım (HMAC imzalı token) cevabı base64 içinde istemciye taşıyordu; bu bir sızıntıydı.
 */
class MathCaptcha
{
    public const SESSION_KEY = 'math_captcha';
    public const TTL_SECONDS = 3600;   // 1 saat (oturum ömrü 2 saat)
    public const MAX_PENDING = 20;     // oturumda bekleyen soru sayısı (eskiler atılır)

    /**
     * Yeni soru üret; cevabı oturuma yaz, istemciye yalnız referansı ver.
     *
     * @return array{key:string, question:string, a:int, b:int}
     */
    public static function generate(): array
    {
        $a = random_int(1, 9);
        $b = random_int(1, 9);
        $key = Str::random(40);

        $pending = self::pending();
        $pending[$key] = ['answer' => $a + $b, 'expires' => time() + self::TTL_SECONDS];
        if (count($pending) > self::MAX_PENDING) {
            uasort($pending, fn ($x, $y) => $x['expires'] <=> $y['expires']);
            $pending = array_slice($pending, -self::MAX_PENDING, null, true);
        }
        session()->put(self::SESSION_KEY, $pending);

        return ['key' => $key, 'question' => "{$a} + {$b}", 'a' => $a, 'b' => $b];
    }

    /**
     * Gönderilen cevabı oturumdaki soruyla doğrula. Doğruysa soru silinir (tek kullanım);
     * süresi dolmuş, bilinmeyen, değiştirilmiş veya başka oturuma ait referans reddedilir.
     */
    public static function validate(?string $key, $submittedAnswer): bool
    {
        if (! is_string($key) || $key === '' || strlen($key) > 64) {
            return false;
        }
        $pending = self::pending();
        $entry = $pending[$key] ?? null;
        if (! is_array($entry)) {
            return false;
        }
        if (time() > (int) ($entry['expires'] ?? 0)) {
            unset($pending[$key]);
            session()->put(self::SESSION_KEY, $pending);

            return false;
        }
        if (! is_numeric($submittedAnswer) || (int) $submittedAnswer !== (int) $entry['answer']) {
            return false; // yanlış cevap: soru kalır, kullanıcı aynı soruya yeniden cevap verebilir (TTL içinde)
        }
        unset($pending[$key]);
        session()->put(self::SESSION_KEY, $pending);

        return true;
    }

    /** @return array<string, array{answer:int, expires:int}> */
    private static function pending(): array
    {
        $pending = session()->get(self::SESSION_KEY, []);
        if (! is_array($pending)) {
            return [];
        }
        $now = time();

        return array_filter($pending, fn ($e) => is_array($e) && (int) ($e['expires'] ?? 0) >= $now);
    }
}
