<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Tek marka (2026-10-10): TR/EN/DE'de kullanıcıya görünen marka yalnız ApplyToGerman. Eskiden çeviri metinleri,
 * footer telif satırı ve chatbot adı "ApplyToGerman (AlmanyaUni)" / "AlmanyaUni Asistanı" gösteriyordu.
 *
 * Bilerek korunan teknik izler sayfada kalabilir: almanyauni_* çerez adları ve Organization şemasındaki
 * alternateName (eski alan adı almanyauni.com → applytogerman.com yönlendirmesiyle varlık sürekliliği).
 */
class BrandSingleNameTest extends TestCase
{
    use RefreshDatabase;

    public static function locales(): array
    {
        return [['tr', 'ApplyToGerman Asistanı'], ['en', 'ApplyToGerman Assistant'], ['de', 'ApplyToGerman-Assistent']];
    }

    #[DataProvider('locales')]
    public function test_home_shows_only_applytogerman(string $locale, string $assistant): void
    {
        $html = $this->get("/{$locale}")->assertOk()->getContent();

        $this->assertStringContainsString('<meta property="og:site_name" content="ApplyToGerman">', $html);
        $this->assertStringContainsString('&copy; ' . date('Y') . ' ApplyToGerman.', $html);
        $this->assertMatchesRegularExpression('/"@type":\s*"WebSite",\s*"name":\s*"ApplyToGerman"/', $html);

        if (str_contains($html, 'Assist')) {
            $this->assertStringContainsString($assistant, $html);
        }

        $visible = preg_replace(['/almanyauni_[a-z_]+/', '/"alternateName":\s*"AlmanyaUni"/'], '', $html);
        $this->assertStringNotContainsString('AlmanyaUni', $visible);
    }

    /** Arama sayfasının logosu "Almanya" + "Uni" diye iki span'e bölünmüş yazılıydı; düz metin aramasından kaçıyordu. */
    #[DataProvider('locales')]
    public function test_search_page_logo_is_applytogerman(string $locale): void
    {
        foreach (['', '?q=Berlin'] as $query) {
            $html = $this->get("/{$locale}/search{$query}")->assertOk()->getContent();
            $text = strip_tags($html);

            $this->assertStringContainsString('ApplyToGerman', $text);
            $this->assertStringNotContainsString('AlmanyaUni', $text);
        }
    }

    public function test_lang_files_have_no_dual_brand(): void
    {
        foreach (['tr', 'en', 'de'] as $l) {
            $values = json_decode(file_get_contents(lang_path("{$l}.json")), true);
            foreach ($values as $key => $value) {
                $this->assertStringNotContainsString('AlmanyaUni', $value, "{$l}.json: {$key}");
            }
        }
    }
}
