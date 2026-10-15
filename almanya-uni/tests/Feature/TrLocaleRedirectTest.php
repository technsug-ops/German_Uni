<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Dil grubundaki eski TR takma adları (`/tr/ekip`, `/tr/gizlilik`, `/tr/haberler`, `/tr/araclar/*` …) aynı dilde kalır.
 * Önceden hedefler öneksizdi (`/team`): fallback marka varsayılanına (EN) 302'liyor, `/tr/ekip` → `/en/team`'e iniyordu
 * (docs/LEGACY_DOMAIN_REDIRECT_AUDIT.md §1.5). Artık: tek 301, aynı dil, hedef rota mevcut.
 */
class TrLocaleRedirectTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, array{0:string,1:string}> [eski yol, hedef yol] */
    public static function aliases(): array
    {
        return [
            'reklam' => ['/reklam', '/advertise'],
            'tum-sayfalar' => ['/tum-sayfalar', '/all-pages'],
            'danisma-kurulu' => ['/danisma-kurulu', '/advisory-board'],
            'ekip' => ['/ekip', '/team'],
            'yazar' => ['/yazar/filiz-ozkan', '/author/filiz-ozkan'],
            'almanyada-egitim' => ['/almanyada-egitim', '/study-in-germany'],
            'about-germany' => ['/about-germany', '/study-in-germany'],
            'neden-almanya' => ['/neden-almanya', '/study-in-germany'],
            'yazarlar' => ['/yazarlar', '/team'],
            'gizlilik' => ['/gizlilik', '/privacy'],
            'kosullar' => ['/kosullar', '/terms'],
            'cerez-politikasi' => ['/cerez-politikasi', '/cookie-policy'],
            'yasal-uyari' => ['/yasal-uyari', '/disclaimer'],
            'haberler-kategori' => ['/haberler/kategori/practical', '/news/category/practical'],
            'haberler-slug' => ['/haberler/ornek-haber', '/news/ornek-haber'],
            'haberler' => ['/haberler', '/news'],
            'kariyer-pusulasi' => ['/araclar/kariyer-pusulasi', '/tools/career-compass'],
            'bloke-hesap' => ['/tools/bloke-hesap', '/tools/sperrkonto'],
            'araclar-sperrkonto' => ['/araclar/sperrkonto', '/tools/sperrkonto'],
            'vize-randevu' => ['/tools/vize-randevu', '/tools/visa-appointment'],
            'araclar-vize-randevu' => ['/araclar/vize-randevu', '/tools/visa-appointment'],
            'dil-sertifikalari' => ['/tools/dil-sertifikalari', '/tools/language-certificates'],
            'saglik-sigortasi' => ['/tools/saglik-sigortasi', '/tools/health-insurance'],
            'araclar-saglik-sigortasi' => ['/araclar/saglik-sigortasi', '/tools/health-insurance'],
            'saglayicilar' => ['/housing/saglayicilar', '/housing/providers'],
            'etkinlikler' => ['/etkinlikler', '/events'],
            'mentorlar' => ['/mentorlar', '/mentors'],
            'deneyim-paylas' => ['/deneyim-paylas', '/contribute'],
            'iletisim' => ['/iletisim', '/contact'],
        ];
    }

    #[DataProvider('aliases')]
    public function test_alias_redirects_once_within_the_same_locale(string $from, string $to): void
    {
        foreach (['tr', 'en', 'de'] as $locale) {
            $res = $this->get('https://applytogerman.com/' . $locale . $from);
            $res->assertStatus(301);
            $location = preg_replace('#^https?://[^/]+#', '', (string) $res->headers->get('Location'));
            $this->assertSame('/' . $locale . $to, $location, "/{$locale}{$from} aynı dilde kalmalı");
        }
    }

    public function test_every_alias_target_is_a_registered_localized_route(): void
    {
        $routes = app('router')->getRoutes();
        foreach (self::aliases() as [$from, $to]) {
            foreach (['tr', 'en', 'de'] as $locale) {
                $req = \Illuminate\Http\Request::create('/' . $locale . $to, 'GET');
                $route = $routes->match($req);
                $this->assertNotSame('laravel_route_redirect_destination', $route->getName());
                $this->assertFalse($route->isFallback, "/{$locale}{$to} gerçek bir rota olmalı (fallback değil)");
            }
        }
    }

    /** Veri gerektirmeyen statik hedefler boş test DB'sinde de 200 döner (hukuki sayfalar legal_pages satırı ister). */
    public function test_static_targets_render_in_their_own_language(): void
    {
        foreach (['tr', 'en', 'de'] as $locale) {
            foreach (['/iletisim' => '/contact', '/tum-sayfalar' => '/all-pages', '/almanyada-egitim' => '/study-in-germany'] as $from => $to) {
                $first = $this->get('https://applytogerman.com/' . $locale . $from);
                $first->assertStatus(301);
                $this->get('https://applytogerman.com/' . $locale . $to)->assertOk();
            }
        }
    }

    public function test_tr_never_leaks_to_en_and_default_locale_stays_en(): void
    {
        $leak = $this->get('https://applytogerman.com/tr/ekip');
        $this->assertStringNotContainsString('/en/', (string) $leak->headers->get('Location'));

        // Öneksiz takma ad marka varsayılanına (EN) gider; kök 302 davranışı değişmez.
        $this->get('https://applytogerman.com/')->assertStatus(302)->assertRedirectContains('/en');
        $unprefixed = $this->get('https://applytogerman.com/ekip');
        $unprefixed->assertStatus(301);
        $this->assertStringContainsString('/en/team', (string) $unprefixed->headers->get('Location'));
    }
}
