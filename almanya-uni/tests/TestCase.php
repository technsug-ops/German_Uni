<?php

namespace Tests;

use App\Support\MathCaptcha;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // CI'da Vite assets build edilmez → @vite manifest hatası. Tüm
        // render testlerinde Vite'ı stub'la (built-asset bağımlılığı yok).
        $this->withoutVite();
    }

    /**
     * Math-captcha'lı formlar (login / register / forgot-password) için
     * geçerli captcha alanları. MathCaptcha stateless (HMAC token) olduğundan
     * gerçek token + doğru cevabı döner (session seed'e gerek yok).
     *
     *   $this->post('/login', ['email' => ..., 'password' => ..., ...$this->captcha()]);
     */
    protected function captcha(): array
    {
        $c = MathCaptcha::generate();

        return ['captcha_key' => $c['key'], 'captcha_answer' => $c['a'] + $c['b']];
    }
}
