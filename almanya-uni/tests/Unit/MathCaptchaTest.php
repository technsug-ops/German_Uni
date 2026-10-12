<?php

namespace Tests\Unit;

use App\Support\MathCaptcha;
use Tests\TestCase;

/**
 * Oturuma bağlı math captcha'nın güvenlik çekirdeği.
 * Login / register / şifre-sıfırlama / bülten formlarını bot ve brute-force'a karşı korur.
 * Cevap istemciye gitmez; referans anlamsız, süreli, tek kullanımlık ve oturuma bağlıdır.
 * (Eski HMAC token tasarımı cevabı base64 içinde taşıyordu; o format artık kabul edilmez.)
 */
class MathCaptchaTest extends TestCase
{
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

    public function test_cop_ve_asiri_uzun_anahtar_reddedilir(): void
    {
        $this->assertFalse(MathCaptcha::validate('!!!not-a-reference!!!', 5));
        $this->assertFalse(MathCaptcha::validate(str_repeat('a', 200), 5));
    }

    public function test_eski_imzali_token_formati_artik_gecmez(): void
    {
        // Eski tasarım: base64("cevap|süre|hmac") — cevap istemcideydi. Artık yalnız oturumdaki referans geçerli.
        $payload = '5|' . (time() + 3600);
        $legacy = base64_encode($payload . '|' . hash_hmac('sha256', $payload, (string) config('app.key')));

        $this->assertFalse(MathCaptcha::validate($legacy, 5));
    }

    public function test_rakam_olmayan_cevap_reddedilir(): void
    {
        $c = MathCaptcha::generate();

        $this->assertFalse(MathCaptcha::validate($c['key'], 'abc'));
        $this->assertTrue(MathCaptcha::validate($c['key'], (string) ($c['a'] + $c['b'])), 'sayısal dize kabul edilir');
    }

    public function test_kurcalanmis_referans_reddedilir(): void
    {
        $c = MathCaptcha::generate();
        $tampered = substr($c['key'], 0, -1) . ($c['key'][-1] === 'x' ? 'y' : 'x');

        $this->assertFalse(MathCaptcha::validate($tampered, $c['a'] + $c['b']));
    }

    public function test_suresi_gecmis_referans_dogru_cevapla_bile_reddedilir(): void
    {
        $c = MathCaptcha::generate();
        $pending = session()->get(MathCaptcha::SESSION_KEY);
        $pending[$c['key']]['expires'] = time() - 1;
        session()->put(MathCaptcha::SESSION_KEY, $pending);

        $this->assertFalse(MathCaptcha::validate($c['key'], $c['a'] + $c['b']));
    }

    public function test_referans_cevabi_tasimaz_ve_tek_kullanimliktir(): void
    {
        $c = MathCaptcha::generate();
        $answer = (string) ($c['a'] + $c['b']);

        // Rastgele alfasayısal referans tesadüfen cevap rakamını içerebilir; sızıntı testi yapıyı ve oturumu kontrol eder.
        $this->assertSame(0, preg_match('/[^A-Za-z0-9]/', $c['key']), 'referans yalnız alfasayısal, yapı taşımaz');
        $this->assertSame((int) $answer, session()->get(MathCaptcha::SESSION_KEY)[$c['key']]['answer'], 'cevap yalnız oturumda');
        $this->assertTrue(MathCaptcha::validate($c['key'], $answer));
        $this->assertFalse(MathCaptcha::validate($c['key'], $answer), 'ikinci kullanım (replay) reddedilir');
    }

    public function test_baska_oturumun_referansi_reddedilir(): void
    {
        $c = MathCaptcha::generate();
        session()->flush();

        $this->assertFalse(MathCaptcha::validate($c['key'], $c['a'] + $c['b']));
    }
}
