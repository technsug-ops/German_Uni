<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

/**
 * Admin uyarısı: Gemini API erişimi reddedildi (403/429/401) — genelde prepaid kredi/bütçe bitti; chatbot o sırada
 * her soruya genel hata döner. ChatService::alertAccessDenied gönderir (6 saatte en fazla bir kez).
 */
class GeminiAccessAlert extends Mailable
{
    public function __construct(public string $detail) {}

    public function build(): static
    {
        return $this->subject('[' . brand('name') . '] Gemini erişimi kesildi — chatbot çalışmıyor')
            ->html(
                '<p>Gemini API isteği reddedildi; chatbot şu anda her soruya hata mesajı veriyor.</p>'
                . '<p>Olası neden: Gemini ön ödemeli kredisi (Guthaben) veya bütçe bitti — Google Cloud → '
                . 'Abrechnung → "My Billing Account" (012EA5-…).</p>'
                . '<p>Hata: <code>' . e($this->detail) . '</code></p>'
                . '<p>Düzelttikten sonra kontrol: sitede chatbot\'a bir soru sor. Bu uyarı 6 saatte en fazla bir kez gelir.</p>'
            );
    }
}
