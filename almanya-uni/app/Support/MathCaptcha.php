<?php

namespace App\Support;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

/**
 * Lightweight math captcha — adds a simple addition question to forms
 * to deter brute-force/spam bots without third-party services or images.
 *
 * Each form load generates a unique session key holding the answer.
 * Submission must include both the key and the answered number.
 */
class MathCaptcha
{
    private const SESSION_PREFIX = 'captcha_';
    private const TTL_MINUTES = 30;

    /**
     * Generate a new captcha question + store its answer in session.
     *
     * @return array{key:string, question:string, a:int, b:int}
     */
    public static function generate(): array
    {
        $a = random_int(1, 9);
        $b = random_int(1, 9);
        $answer = $a + $b;
        $key = Str::random(16);

        Session::put(self::SESSION_PREFIX . $key, [
            'answer' => $answer,
            'expires_at' => now()->addMinutes(self::TTL_MINUTES)->timestamp,
        ]);

        return [
            'key' => $key,
            'question' => "{$a} + {$b}",
            'a' => $a,
            'b' => $b,
        ];
    }

    /**
     * Validate a submitted answer. On success, consume the entry (single-use).
     * On wrong answer: allow up to 3 retries (prevents bot brute-force,
     * preserves UX for legit typos).
     */
    public static function validate(?string $key, $submittedAnswer): bool
    {
        if (! $key || ! is_string($key)) {
            return false;
        }

        $sessionKey = self::SESSION_PREFIX . $key;
        $entry = Session::get($sessionKey);

        if (! is_array($entry) || ! isset($entry['answer'], $entry['expires_at'])) {
            return false;
        }

        if (now()->timestamp > $entry['expires_at']) {
            Session::forget($sessionKey);
            return false;
        }

        if ((int) $submittedAnswer === (int) $entry['answer']) {
            Session::forget($sessionKey); // single-use on success
            return true;
        }

        // Wrong answer: increment fail counter; expire after 3 wrong tries
        $attempts = (int) ($entry['attempts'] ?? 0) + 1;
        if ($attempts >= 3) {
            Session::forget($sessionKey);
            return false;
        }
        $entry['attempts'] = $attempts;
        Session::put($sessionKey, $entry);
        return false;
    }
}
