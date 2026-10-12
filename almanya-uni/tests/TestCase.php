<?php

namespace Tests;

use App\Support\MathCaptcha;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Filament tablo/sayfa sınıfları istek içinde @set_time_limit(180) çağırır. PHPUnit tüm testleri TEK süreçte
        // koştuğundan bu sınır sonraki testlere de uygulanır; Windows'ta PHP duvar saatini saydığı için paket ~180 sn
        // sonra rastgele bir testte "Maximum execution time of 180 seconds exceeded" ile ölüyordu (Linux CI'da CPU
        // süresi sayıldığından görünmez). Her test başında sınır kaldırılır.
        set_time_limit(0);
        // CI'da Vite assets build edilmez → @vite manifest hatası. Tüm
        // render testlerinde Vite'ı stub'la (built-asset bağımlılığı yok).
        $this->withoutVite();
    }

    /**
     * Math-captcha'lı formlar (login / register / forgot-password / bülten) için geçerli captcha alanları.
     * MathCaptcha oturuma bağlıdır: soru test oturumuna yazılır, istemci alanları referans + doğru cevaptır.
     *
     *   $this->post('/login', ['email' => ..., 'password' => ..., ...$this->captcha()]);
     */
    protected function captcha(): array
    {
        $c = MathCaptcha::generate();
        $this->withSession([MathCaptcha::SESSION_KEY => session()->get(MathCaptcha::SESSION_KEY)]);

        return ['captcha_key' => $c['key'], 'captcha_answer' => $c['a'] + $c['b']];
    }
}
