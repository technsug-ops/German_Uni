<?php

namespace Tests\Unit;

use App\Support\TranslationInvariants as TI;
use PHPUnit\Framework\TestCase;

/** OSV Faz 0 (G9): çeviri sonrası rakam/tutar/tarih/§/URL karşılaştırması. */
class TranslationInvariantsTest extends TestCase
{
    public function test_thousand_and_decimal_separators_are_normalised(): void
    {
        $this->assertSame('1091', TI::normaliseNumber('1.091'));
        $this->assertSame('1091', TI::normaliseNumber('1,091'));
        $this->assertSame('1091', TI::normaliseNumber('1 091'));
        $this->assertSame('45934.2', TI::normaliseNumber('45.934,20'));
        $this->assertSame('45934.2', TI::normaliseNumber('45,934.20'));
        $this->assertSame('1.5', TI::normaliseNumber('1,5'));
        $this->assertSame('1.5', TI::normaliseNumber('1.5'));
        $this->assertSame('992', TI::normaliseNumber('992,00'));
        $this->assertSame('11904', TI::normaliseNumber('11.904'));
        $this->assertSame('1000000', TI::normaliseNumber('1.000.000'));
    }

    public function test_tr_to_en_and_de_with_locale_formatting_passes(): void
    {
        $tr = "**Güncel durum (kontrol tarihi: 14.10.2026):** 2026 Blue Card eşiği 50.700 €, darboğaz mesleklerinde 45.934,20 € "
            . "(§ 18g Abs. 1 AufenthG). Sperrkonto 11.904 € (aylık 992 €). 1.091 başvuru, 140 tam gün / 280 yarım gün. "
            . "Kaynak: [BAMF](https://www.bamf.de/DE/Themen/blaue-karte.html), 2026-01-01.";
        $en = "**Current status (checked 14.10.2026):** the 2026 Blue Card threshold is €50,700, for shortage occupations €45,934.20 "
            . "(§ 18g(1) AufenthG). Blocked account €11,904 (€992 per month). 1,091 applications, 140 full days / 280 half days. "
            . "Source: [BAMF](https://www.bamf.de/DE/Themen/blaue-karte.html), 2026-01-01.";
        $de = "**Aktueller Stand (geprüft am 14.10.2026):** Die Blue-Card-Schwelle 2026 liegt bei 50.700 €, in Engpassberufen bei 45.934,20 € "
            . "(§ 18g Absatz 1 AufenthG). Sperrkonto 11 904 EUR (monatlich 992 Euro). 1.091 Anträge, 140 ganze / 280 halbe Tage. "
            . "Quelle: [BAMF](https://www.bamf.de/DE/Themen/blaue-karte.html), 2026-01-01.";

        $this->assertSame([], TI::compare($tr, $en), TI::summarize(TI::compare($tr, $en)));
        $this->assertSame([], TI::compare($tr, $de), TI::summarize(TI::compare($tr, $de)));
        $this->assertTrue(TI::ok($tr, $en));
    }

    public function test_changed_number_amount_date_paragraph_and_url_are_reported(): void
    {
        $src = 'Öğrenciler yılda 140 tam gün çalışabilir. Vize harcı 75 €, iDATA 39,92 €. Kontrol: 14.10.2026. § 16b Abs. 3 AufenthG. https://www.auswaertiges-amt.de/de/2715934-2715934';

        $d = TI::compare($src, 'Students may work 120 full days. Visa fee 75 €, iDATA 39,92 €. Checked: 14.10.2026. § 16b Abs. 3 AufenthG. https://www.auswaertiges-amt.de/de/2715934-2715934');
        $this->assertSame(['numbers' => ['missing' => ['140'], 'extra' => ['120']]], $d);

        $d = TI::compare($src, 'Students may work 140 full days. Visa fee €30, iDATA €39.92. Checked: 14.10.2026. § 16b Abs. 3 AufenthG. https://www.auswaertiges-amt.de/de/2715934-2715934');
        $this->assertSame(['amounts' => ['missing' => ['€75'], 'extra' => ['€30']]], $d);

        $d = TI::compare($src, 'Students may work 140 full days. Visa fee €75, iDATA €39.92. Checked: 15.10.2026. § 16b Abs. 3 AufenthG. https://www.auswaertiges-amt.de/de/2715934-2715934');
        $this->assertSame(['dates' => ['missing' => ['2026-10-14'], 'extra' => ['2026-10-15']]], $d);

        $d = TI::compare($src, 'Students may work 140 full days. Visa fee €75, iDATA €39.92. Checked: 14.10.2026. § 16 AufenthG. https://www.auswaertiges-amt.de/de/2715934-2715934');
        $this->assertSame(['paragraphs' => ['missing' => ['§16b(3)'], 'extra' => ['§16']]], $d);

        $d = TI::compare($src, 'Students may work 140 full days. Visa fee €75, iDATA €39.92. Checked: 14.10.2026. § 16b Abs. 3 AufenthG. https://www.auswaertiges-amt.de/en/');
        $this->assertSame(['https://www.auswaertiges-amt.de/de/2715934-2715934'], $d['urls']['missing']);
    }

    public function test_spelled_out_small_numbers_are_not_required_but_amounts_are(): void
    {
        // tek haneli çıplak sayı yazıyla yazılabilir
        $this->assertTrue(TI::ok('Almanya\'da 2 yıl kalabilirsin; 3 adım var.', 'You can stay in Germany for two years; there are three steps.'));
        $this->assertTrue(TI::ok('Zwei Jahre und drei Schritte.', 'İki yıl ve üç adım.'));
        // ama tutar, iki+ haneli sayı ve tarih yazıyla değiştirilemez
        $this->assertFalse(TI::ok('Harç 75 €.', 'The fee is seventy-five euros.'));
        $this->assertFalse(TI::ok('Haftada 20 saat.', 'Twenty hours a week.'));
        $this->assertFalse(TI::ok('Kontrol tarihi: 14.10.2026.', 'Checked on 14 October 2026.'));
    }

    public function test_multiset_counts_matter_and_unexpected_only_flags_additions(): void
    {
        $this->assertFalse(TI::ok('20 saat ve 20 saat', '20 hours'), 'çoklu küme: tekrar sayısı korunur');
        $this->assertSame([], TI::unexpected('Blue Card 2026: 50.700 €, 45.934,20 €.', 'Blue Card 2026: €50,700'), 'özet eksik bırakabilir');
        $this->assertSame(['amounts' => ['missing' => [], 'extra' => ['€48300']]], TI::unexpected('Blue Card 2026: 50.700 €.', 'Blue Card 2026: €48,300'));
    }

    public function test_extract_categories(): void
    {
        $x = TI::extract('Ab 01.07.2025 gilt § 16b (3); Fee EUR 1.234,50; see https://example.org/a/b. Plus 2024-12-31 and 300-450.');
        $this->assertSame(['https://example.org/a/b'], $x['urls']);
        $this->assertSame(['2024-12-31', '2025-07-01'], $x['dates']);
        $this->assertSame(['§16b(3)'], $x['paragraphs']);
        $this->assertSame(['€1234.5'], $x['amounts']);
        $this->assertSame(['300', '450'], $x['numbers']);
    }
}
