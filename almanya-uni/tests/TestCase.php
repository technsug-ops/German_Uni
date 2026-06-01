<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Str;

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
     * geçerli captcha alanları. Session'a doğru cevabı seed'ler + key+answer döner.
     *
     *   $this->post('/login', ['email' => ..., 'password' => ..., ...$this->captcha()]);
     */
    protected function captcha(int $answer = 7): array
    {
        $key = 'test-' . Str::random(10);
        $this->withSession(['captcha_' . $key => [
            'answer' => $answer,
            'expires_at' => now()->addMinutes(30)->timestamp,
        ]]);

        return ['captcha_key' => $key, 'captcha_answer' => $answer];
    }
}
