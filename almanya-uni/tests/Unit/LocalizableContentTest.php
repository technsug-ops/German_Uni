<?php

namespace Tests\Unit;

use App\Models\Concerns\LocalizableContent;
use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;

/**
 * i18n çekirdek kuralı: aktif dil TR değilse, serbest-metin (strict) alanlarda
 * Türkçe içerik fallback ile /en /de sayfasına SIZMAMALI.
 * Bu kural memory'de tekrar tekrar geçiyor; burada koruma altına alınır.
 */
class LocalizableContentTest extends TestCase
{
    private function stub(array $attributes): Model
    {
        return new class($attributes) extends Model
        {
            use LocalizableContent;

            protected $guarded = [];
        };
    }

    public function test_aktif_dil_kolonunu_doner(): void
    {
        app()->setLocale('de');
        $m = $this->stub(['name_tr' => 'Türkçe Ad', 'name_de' => 'Deutscher Name']);

        // de zinciri: de → en → tr; de dolu → Almanca döner.
        $this->assertSame('Deutscher Name', $m->localized('name'));
    }

    public function test_strict_modunda_tr_yabanci_dile_sizmaz(): void
    {
        app()->setLocale('en');
        // Sadece TR açıklama var; EN/DE yok.
        $m = $this->stub(['description_tr' => 'Türkçe açıklama']);

        // strict: en zincirinden tr çıkarılır → null (blade gizler).
        $this->assertNull($m->localized('description', strict: true));
        // Accessor da strict kullanır → TR sızmaz.
        $this->assertNull($m->description);
    }

    public function test_de_strict_tr_yerine_en_e_duser(): void
    {
        app()->setLocale('de');
        // DE yok; EN ve TR var. strict → tr hariç → en'e düşer (tr'ye DEĞİL).
        $m = $this->stub(['description_en' => 'English text', 'description_tr' => 'Türkçe metin']);

        $this->assertSame('English text', $m->localized('description', strict: true));
    }

    public function test_strict_olmayan_ad_tr_ye_dusebilir(): void
    {
        app()->setLocale('en');
        // İsim/kimlik alanı strict DEĞİL → boş bırakmaktansa TR'ye düşmek yeğdir.
        $m = $this->stub(['name_tr' => 'Marka Adı']);

        $this->assertSame('Marka Adı', $m->name);
    }

    public function test_strict_aktif_dil_doluysa_onu_kullanir(): void
    {
        app()->setLocale('en');
        $m = $this->stub(['description_en' => 'English desc', 'description_tr' => 'Türkçe']);

        $this->assertSame('English desc', $m->description);
    }

    public function test_localized_kolon_dilinde_tr_kalir(): void
    {
        app()->setLocale('tr');
        // /tr sayfasında TR doğal dildir → strict olsa bile TR döner.
        $m = $this->stub(['description_tr' => 'Türkçe açıklama']);

        $this->assertSame('Türkçe açıklama', $m->description);
    }

    public function test_localized_kolon_yoksa_ham_name_doner(): void
    {
        app()->setLocale('en');
        // name_{locale} kolonu hiç yok; ham 'name' attribute'u var.
        $m = $this->stub(['name' => 'Raw Name']);

        $this->assertSame('Raw Name', $m->name);
    }
}
