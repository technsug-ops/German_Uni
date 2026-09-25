<?php

namespace Tests\Feature;

use App\Services\Content\BlogAutoLinker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * BlogAutoLinker — üniversite kısaltmaları yanlış linklenmesin.
 *
 * Canlıda SCHUFA yazılarında "DSK" (Datenschutzkonferenz) → Deutsche Sporthochschule Köln,
 * Almanca "zu tun" → TU Nürnberg (short_name TUN) linkleniyordu: kısa adlar büyük/küçük harf
 * duyarsız eşleşiyordu.
 */
class BlogAutoLinkerAbbreviationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();

        foreach ([
            ['TUN', 'technische-universitat-nurnberg-q56187114', 'Technische Universität Nürnberg'],
            ['DSK', 'deutsche-sporthochschule-koln-q315229', 'Deutsche Sporthochschule Köln'],
            ['RWTH', 'rwth-aachen-university', 'RWTH Aachen University'],
        ] as [$short, $slug, $name]) {
            DB::table('universities')->insert([
                'name_de' => $name, 'name_tr' => $name, 'slug' => $slug,
                'short_name' => $short, 'is_active' => 1,
            ]);
        }
    }

    private function link(string $html, string $locale = 'de'): string
    {
        return (new BlogAutoLinker)->process($html, null, true, $locale);
    }

    public function test_kucuk_harfli_kelime_kisaltma_linki_almaz(): void
    {
        $out = $this->link('<p>Damit haben Sie nichts zu tun, der Vermieter entscheidet.</p>');

        $this->assertStringNotContainsString('technische-universitat-nurnberg', $out);
        $this->assertStringContainsString('zu tun', $out);
    }

    public function test_buyuk_harfli_kisaltma_hala_linklenir(): void
    {
        $out = $this->link('<p>Viele studieren an der RWTH und an der TUN.</p>');

        $this->assertStringContainsString('href="/universities/rwth-aachen-university"', $out);
        $this->assertStringContainsString('href="/universities/technische-universitat-nurnberg-q56187114"', $out);
    }

    public function test_cakisan_kisaltma_dsk_linklenmez(): void
    {
        foreach (['tr', 'en', 'de'] as $locale) {
            $out = $this->link('<p>Laut Orientierungshilfe der DSK darf keine Ausweiskopie verlangt werden.</p>', $locale);

            $this->assertStringNotContainsString('deutsche-sporthochschule-koln', $out, $locale);
            $this->assertStringContainsString('DSK', $out, $locale);
        }
    }

    public function test_kisaltma_farkli_harf_duzeninde_linklenmez(): void
    {
        $out = $this->link('<p>Die Rwth-Schreibweise ist falsch, rwth auch.</p>');

        $this->assertStringNotContainsString('rwth-aachen-university', $out);
    }
}
