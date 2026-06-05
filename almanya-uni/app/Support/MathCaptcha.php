<?php

namespace App\Support;

/**
 * Lightweight math captcha — adds a simple addition question to forms
 * to deter brute-force/spam bots without third-party services or images.
 *
 * STATELESS (HMAC-imzalı token): cevap session yerine, app key ile imzalı
 * kendinden-doğrulanır bir token'da taşınır. Bu sayede:
 *  - Doğru cevap session kaybı / TTL bitişi yüzünden reddedilmez (eski bug).
 *  - Aynı sayfada birden çok captcha (newsletter inline + sticky) çakışmaz.
 *  - Sayfa cache'lense bile token geçerlilik penceresi boyunca çalışır.
 * Tekrar-kullanım (replay) penceresi kısa (2 saat) + honeypot + rate-limit ile sınırlı.
 */
class MathCaptcha
{
    private const TTL_SECONDS = 7200; // 2 saat

    /**
     * Generate a new captcha question + a signed token carrying the answer.
     *
     * @return array{key:string, question:string, a:int, b:int}
     */
    public static function generate(): array
    {
        $a = random_int(1, 9);
        $b = random_int(1, 9);
        $answer = $a + $b;
        $expires = time() + self::TTL_SECONDS;

        return [
            'key' => self::makeToken($answer, $expires),
            'question' => "{$a} + {$b}",
            'a' => $a,
            'b' => $b,
        ];
    }

    /**
     * Validate a submitted answer against the signed token (no session).
     */
    public static function validate(?string $key, $submittedAnswer): bool
    {
        if (! $key || ! is_string($key)) {
            return false;
        }

        $decoded = base64_decode($key, true);
        if ($decoded === false) {
            return false;
        }

        $parts = explode('|', $decoded);
        if (count($parts) !== 3) {
            return false;
        }

        [$answer, $expires, $sig] = $parts;
        if (! ctype_digit($answer) || ! ctype_digit($expires)) {
            return false;
        }

        // Süresi geçmiş token
        if (time() > (int) $expires) {
            return false;
        }

        // İmza doğrulama (constant-time)
        $expected = self::sign($answer . '|' . $expires);
        if (! hash_equals($expected, $sig)) {
            return false;
        }

        return (int) $submittedAnswer === (int) $answer;
    }

    private static function makeToken(int $answer, int $expires): string
    {
        $payload = $answer . '|' . $expires;

        return base64_encode($payload . '|' . self::sign($payload));
    }

    private static function sign(string $payload): string
    {
        return hash_hmac('sha256', $payload, (string) config('app.key'));
    }
}
