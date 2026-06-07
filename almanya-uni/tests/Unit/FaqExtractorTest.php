<?php

namespace Tests\Unit;

use App\Support\FaqExtractor;
use PHPUnit\Framework\TestCase;

/**
 * FaqExtractor birim testleri — blog gövdesindeki soru-başlıklarından (sonu "?")
 * Q&A çiftleri çıkaran saf-mantık. DB gerektirmez (PHPUnit\TestCase).
 *
 * Pozitif · Negatif · Uç durumlar · Güvenlik (HTML/entity sızıntısı).
 */
class FaqExtractorTest extends TestCase
{
    // ─────────────── POZİTİF ───────────────

    public function test_iki_soru_basligindan_iki_faq_cikarir(): void
    {
        $html = '<h2>Sperrkonto nedir?</h2><p>Bloke hesap, Almanya vize başvurusu için gereken ve belirli bir tutarın yatırıldığı hesaptır.</p>'
              . '<h2>Nasıl açılır?</h2><p>Online sağlayıcılar üzerinden pasaport ve video kimlik doğrulamayla birkaç günde açılabilir.</p>';

        $faqs = FaqExtractor::fromHtml($html);

        $this->assertCount(2, $faqs);
        $this->assertSame('Sperrkonto nedir?', $faqs[0]['q']);
        $this->assertStringContainsString('Bloke hesap', $faqs[0]['a']);
        $this->assertSame('Nasıl açılır?', $faqs[1]['q']);
    }

    public function test_h3_basliklari_da_calisir(): void
    {
        $html = '<h3>APS nedir ve neden gerekir?</h3><p>APS, Türkiye\'deki diplomaların Almanya için ön incelemesini yapan akademik denklik birimidir.</p>';

        $faqs = FaqExtractor::fromHtml($html);

        $this->assertCount(1, $faqs);
        $this->assertSame('APS nedir ve neden gerekir?', $faqs[0]['q']);
    }

    public function test_tam_genislik_soru_isareti_kabul_edilir(): void
    {
        // Full-width '？' (CJK) — bazı çevirilerde geçebilir.
        $html = '<h2>Vize randevusu nasıl alınır？</h2><p>iDATA üzerinden uygun şehir seçilerek randevu talebi oluşturulur ve onay beklenir.</p>';

        $faqs = FaqExtractor::fromHtml($html);

        $this->assertCount(1, $faqs);
    }

    // ─────────────── NEGATİF ───────────────

    public function test_soru_isareti_olmayan_baslik_atlanir(): void
    {
        $html = '<h2>Giriş</h2><p>Bu bir giriş paragrafıdır ve soru başlığı değildir, dolayısıyla FAQ üretmemelidir kesinlikle.</p>'
              . '<h2>Sonuç olarak ne yapmalı?</h2><p>Belgeleri erkenden hazırlayıp randevu takvimini takip etmek en doğrusudur.</p>';

        $faqs = FaqExtractor::fromHtml($html);

        $this->assertCount(1, $faqs);
        $this->assertSame('Sonuç olarak ne yapmalı?', $faqs[0]['q']);
    }

    public function test_cok_kisa_cevap_atlanir(): void
    {
        // Cevap < 20 karakter → atlanmalı (içi boş soru başlığı).
        $html = '<h2>Kısa mı?</h2><p>Evet.</p>';

        $this->assertSame([], FaqExtractor::fromHtml($html));
    }

    // ─────────────── UÇ DURUMLAR ───────────────

    public function test_null_girdi_bos_dizi_doner(): void
    {
        $this->assertSame([], FaqExtractor::fromHtml(null));
    }

    public function test_bos_string_bos_dizi_doner(): void
    {
        $this->assertSame([], FaqExtractor::fromHtml(''));
    }

    public function test_baslik_olmayan_html_bos_doner(): void
    {
        $this->assertSame([], FaqExtractor::fromHtml('<p>Sadece paragraf, başlık yok.</p>'));
    }

    public function test_cok_uzun_cevap_700_karaktere_kirpilir(): void
    {
        $long = str_repeat('a', 1500);
        $html = "<h2>Uzun cevap kırpılır mı?</h2><p>{$long}</p>";

        $faqs = FaqExtractor::fromHtml($html);

        $this->assertCount(1, $faqs);
        // Str::limit(700) → 700 + '...' = en fazla 703 karakter.
        $this->assertLessThanOrEqual(703, mb_strlen($faqs[0]['a']));
        $this->assertStringEndsWith('...', $faqs[0]['a']);
    }

    public function test_son_baslik_eof_a_kadar_okunur(): void
    {
        // Son soru başlığının cevabı dosya sonuna kadar uzanır (sonraki başlık yok).
        $html = '<h2>Son soru burada mı biter?</h2><p>Evet, son başlığın cevabı belge sonuna kadar doğru biçimde toplanmalıdır.</p>';

        $faqs = FaqExtractor::fromHtml($html);

        $this->assertCount(1, $faqs);
        $this->assertStringContainsString('belge sonuna kadar', $faqs[0]['a']);
    }

    // ─────────────── GÜVENLİK / TEMİZLİK ───────────────

    public function test_cevaptaki_ic_etiketler_metne_indirgenir(): void
    {
        $html = '<h2>İç etiketler temizlenir mi?</h2><p>Bu cevapta <strong>kalın</strong> ve <a href="#">link</a> bulunur; düz metne dönmelidir.</p>';

        $faqs = FaqExtractor::fromHtml($html);

        $this->assertStringNotContainsString('<strong>', $faqs[0]['a']);
        $this->assertStringNotContainsString('<a ', $faqs[0]['a']);
        $this->assertStringContainsString('kalın', $faqs[0]['a']);
    }

    public function test_html_entityleri_cozulur(): void
    {
        $html = '<h2>Vize &amp; sigorta birlikte mi?</h2><p>Vize başvurusunda sağlık sigortası ve bloke hesap genellikle birlikte istenir.</p>';

        $faqs = FaqExtractor::fromHtml($html);

        $this->assertSame('Vize & sigorta birlikte mi?', $faqs[0]['q']);
    }

    public function test_script_etiketi_cevaba_sizmaz(): void
    {
        // strip_tags <script> içeriğini metin olarak bırakır ama etiketi kaldırır;
        // JSON-LD'ye çıplak <script> sızmadığını garanti et.
        $html = '<h2>XSS denemesi geçer mi?</h2><p>Normal cevap metni burada yeterince uzun olmalı.<script>alert(1)</script></p>';

        $faqs = FaqExtractor::fromHtml($html);

        $this->assertStringNotContainsString('<script', $faqs[0]['a']);
    }
}
