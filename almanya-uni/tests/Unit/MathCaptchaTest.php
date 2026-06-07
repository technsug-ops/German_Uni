<?php

namespace Tests\Unit;

use App\Support\MathCaptcha;
use Tests\TestCase;

/**
 * Stateless HMAC captcha'nın güvenlik çekirdeği.
 * Login / register / şifre-sıfırlama formlarını bot/brute-force'a karşı korur.
 * Burada imza sahteciliği, süre dolması ve bozuk token reddi doğrulanır.
 */
class MathCaptchaTest extends TestCase
{
    /** Gerçek app.key ile imzalı, istenen cevap/son-kullanma'lı token üretir (sahte token testleri için). */
    private function mintToken(string $answer, int $expires): string
    {
        $payload = $answer . '|' . $expires;
        $sig = hash_hmac('sha256', $payload, (string) config('app.key'));

        return base64_encode($payload . '|' . $sig);
    }

    public function test_generate_dogru_cevapla_dogrulanir(): void
    {
        $c = MathCaptcha::generate();

        $this->assertTrue(MathCaptcha::validate($c['key'], $c['a'] + $c['b']));
    }

    public function test_yanlis_cevap_reddedilir(): void
    {
        $c = MathCaptcha::generate();

        $this->assertFalse(MathCaptcha::validate($c['key'], $c['a'] + $c['b'] + 1));
    }

    public function test_null_ve_bos_anahtar_reddedilir(): void
    {
        $this->assertFalse(MathCaptcha::validate(null, 5));
        $this->assertFalse(MathCaptcha::validate('', 5));
    }

    public function test_base64_olmayan_cop_reddedilir(): void
    {
        // base64 alfabesi dışı karakter → strict decode false.
        $this->assertFalse(MathCaptcha::validate('!!!not-base64!!!', 5));
    }

    public function test_eksik_parcali_payload_reddedilir(): void
    {
        // 3 parça beklenir (answer|expires|sig); 2 parça → reddet.
        $this->assertFalse(MathCaptcha::validate(base64_encode('5|123'), 5));
    }

    public function test_rakam_olmayan_cevap_reddedilir(): void
    {
        $token = $this->mintToken('abc', time() + 3600);

        $this->assertFalse(MathCaptcha::validate($token, 'abc'));
    }

    public function test_kurcalanmis_imza_reddedilir(): void
    {
        // Doğru cevap + geçerli süre AMA yanlış imza → constant-time hash_equals reddeder.
        $forged = base64_encode('5|' . (time() + 3600) . '|deadbeefdeadbeef');

        $this->assertFalse(MathCaptcha::validate($forged, 5));
    }

    public function test_suresi_gecmis_token_dogru_cevapla_bile_reddedilir(): void
    {
        // Geçerli imza, doğru cevap — ama TTL geçmiş → reddet (replay penceresi kapanır).
        $expired = $this->mintToken('7', time() - 1);

        $this->assertFalse(MathCaptcha::validate($expired, 7));
    }

    public function test_baska_token_uzerinden_cevap_aktarimi_olmaz(): void
    {
        // Bir token'ın imzası, farklı cevaplı başka token'a taşınamaz (payload imzaya dahil).
        $a = MathCaptcha::generate();
        $decoded = explode('|', base64_decode($a['key']));
        // Cevabı değiştir, eski imzayı koru → reddedilmeli.
        $tampered = base64_encode(((int) $decoded[0] + 1) . '|' . $decoded[1] . '|' . $decoded[2]);

        $this->assertFalse(MathCaptcha::validate($tampered, (int) $decoded[0] + 1));
    }
}
