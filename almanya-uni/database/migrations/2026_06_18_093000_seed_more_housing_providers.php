<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Ek ev-bulma kaynakları: 3 platform (Wunderflats, Spotahome, Studiwohnen) +
 * 4 özel öğrenci apartman zinciri (The Flag, Staytoo, SMARTments student,
 * Campo Novo). HousingAnywhere/Uniplaces/The Fizz/Neon Wood zaten kayıtlı.
 * Idempotent (slug). affiliate_url admin'den doldurulur → /go/housing takibi hazır.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('housing_providers')) {
            return;
        }
        $now = now();

        $rows = [
            // ---------------- PLATFORMS ----------------
            [
                'slug' => 'wunderflats', 'name' => 'Wunderflats', 'type' => 'platform',
                'website' => 'https://wunderflats.com/de', 'is_featured' => 1, 'sort_order' => 4,
                'price_min' => 700, 'price_max' => 1500,
                'cities' => ['Berlin', 'München', 'Hamburg', 'Köln', 'Frankfurt', 'Stuttgart', 'Düsseldorf', 'Leipzig'],
                'features' => ['mobliyali', 'utility_dahil', 'online_rezervasyon', 'esnek_sozlesme', 'dogrulanmis_ilan'],
                'description_tr' => 'Mobilyalı daire kiralama platformu — gelmeden online, doğrulanmış ilanlar, aylık esnek sözleşme, her şey dahil fiyat. Öğrenci için ideal (ort. 1 sömestr).',
                'description_en' => 'Furnished apartment rental platform — book online before arrival, verified listings, flexible monthly leases, all-inclusive pricing. Ideal for students.',
                'description_de' => 'Plattform für möblierte Wohnungen — online vor der Anreise buchen, verifizierte Inserate, flexible Monatsmietverträge, All-inclusive-Preis.',
            ],
            [
                'slug' => 'spotahome', 'name' => 'Spotahome', 'type' => 'platform',
                'website' => 'https://www.spotahome.com/de', 'is_featured' => 0, 'sort_order' => 5,
                'price_min' => 500, 'price_max' => 1200,
                'cities' => ['Berlin', 'München', 'Frankfurt', 'Hamburg', 'Köln', 'Stuttgart', 'Düsseldorf'],
                'features' => ['online_rezervasyon', 'dogrulanmis_ilan', 'guvenli_odeme', 'mobliyali'],
                'description_tr' => 'Orta-uzun dönem kiralık platformu — gelmeden online rezerve et, doğrulanmış ilanlar (video/foto turu), güvenli ödeme.',
                'description_en' => 'Mid-to-long-term rental platform — book online from abroad, verified listings (video/photo tours), secure payment.',
                'description_de' => 'Plattform für mittel- bis langfristige Mieten — online aus dem Ausland buchen, verifizierte Inserate, sichere Zahlung.',
            ],
            [
                'slug' => 'studiwohnen', 'name' => 'Studiwohnen', 'type' => 'platform',
                'website' => 'https://studiwohnen.com/', 'is_featured' => 1, 'sort_order' => 6,
                'price_min' => 250, 'price_max' => 500,
                'cities' => ['Berlin', 'München', 'Hamburg', 'Köln', 'Frankfurt', 'Stuttgart', 'Düsseldorf', 'Leipzig'],
                'features' => ['yurt_portali', 'karsilastirma', 'filtreleme', 'ucuz'],
                'description_tr' => '1.500+ öğrenci yurdunu (DE/AT/CH) tek yerden ara ve karşılaştır — şehir, fiyat, oda tipine göre filtrele. Devlet + özel yurtların toplu portalı.',
                'description_en' => 'Search and compare 1,500+ student residences (DE/AT/CH) in one place — filter by city, price and room type. Aggregates public and private dorms.',
                'description_de' => 'Durchsuche und vergleiche 1.500+ Studentenwohnheime (DE/AT/CH) an einem Ort — filtere nach Stadt, Preis und Zimmertyp.',
            ],

            // ---------------- PRIVATE CHAINS ----------------
            [
                'slug' => 'the-flag', 'name' => 'THE FLAG Student', 'type' => 'private_chain',
                'website' => 'https://student.the-flag.de/', 'is_featured' => 0, 'sort_order' => 108,
                'price_min' => 650, 'price_max' => 1100,
                'cities' => ['Frankfurt', 'Münster', 'Hamburg', 'Köln'],
                'features' => ['mobliyali', 'utility_dahil', 'tasarim', 'genc_profesyonel'],
                'description_tr' => 'Öğrenci ve genç profesyoneller için tam donanımlı tasarım apartmanlar — her şey dahil. Frankfurt, Münster, Hamburg, Köln.',
                'description_en' => 'Fully equipped design apartments for students and young professionals — all-inclusive. Frankfurt, Münster, Hamburg, Cologne.',
                'description_de' => 'Voll ausgestattete Design-Apartments für Studierende und Young Professionals — All-inclusive.',
            ],
            [
                'slug' => 'staytoo', 'name' => 'Staytoo', 'type' => 'private_chain',
                'website' => 'https://www.staytoo.de/', 'is_featured' => 0, 'sort_order' => 109,
                'price_min' => 500, 'price_max' => 850,
                'cities' => ['Berlin', 'Nürnberg', 'Leipzig', 'Darmstadt', 'Frankfurt', 'Mainz', 'Aachen', 'Bonn'],
                'features' => ['mobliyali', 'utility_dahil', 'all_inclusive', 'cagdas_tasarim'],
                'description_tr' => 'Her şey dahil (su, elektrik, internet, ısınma) mobilyalı öğrenci apartmanları — tek sabit fiyat, çağdaş tasarım.',
                'description_en' => 'All-inclusive (water, electricity, internet, heating) furnished student apartments — one fixed price, modern design.',
                'description_de' => 'All-inclusive möblierte Studentenapartments (Wasser, Strom, Internet, Heizung) — ein Fixpreis.',
            ],
            [
                'slug' => 'smartments-student', 'name' => 'SMARTments student', 'type' => 'private_chain',
                'website' => 'https://smartments-student.com/de/', 'is_featured' => 0, 'sort_order' => 110,
                'price_min' => 450, 'price_max' => 800,
                'cities' => ['Berlin', 'Hamburg', 'München', 'Leipzig', 'Frankfurt', 'Nürnberg'],
                'features' => ['mobliyali', 'utility_dahil', 'internet_dahil', 'mikro_apart'],
                'description_tr' => 'Üniversiteye yakın, tam mobilyalı mikro-apartmanlar — işletme giderleri + hızlı internet dahil, min. 6 ay. (GBI markası)',
                'description_en' => 'Fully furnished micro-apartments near universities — service charges + high-speed internet included, min. 6 months.',
                'description_de' => 'Voll möblierte Mikro-Apartments in Uninähe — Nebenkosten + High-Speed-Internet inklusive, mind. 6 Monate.',
            ],
            [
                'slug' => 'campo-novo', 'name' => 'CAMPO NOVO', 'type' => 'private_chain',
                'website' => 'https://www.campo-novo-group.de/', 'is_featured' => 0, 'sort_order' => 111,
                'price_min' => 400, 'price_max' => 700,
                'cities' => ['Mainz', 'Stuttgart', 'Berlin', 'Bonn', 'Freiburg', 'Pforzheim', 'Mannheim', 'Karlsruhe'],
                'features' => ['mobliyali', 'kampuse_yakin', 'ankastre_mutfak', 'wg_secenek'],
                'description_tr' => 'Kampüse yakın modern öğrenci apartmanları — tekli apart veya paylaşımlı (WG), kaliteli mobilya + ankastre mutfak.',
                'description_en' => 'Modern campus-near student apartments — single units or shared (WG), quality furniture + fitted kitchen.',
                'description_de' => 'Moderne campusnahe Studentenapartments — Einzelapartments oder WG, hochwertige Möblierung + Einbauküche.',
            ],
        ];

        foreach ($rows as $r) {
            $cities   = json_encode($r['cities'], JSON_UNESCAPED_UNICODE);
            $features = json_encode($r['features'], JSON_UNESCAPED_UNICODE);
            unset($r['cities'], $r['features']);

            DB::table('housing_providers')->updateOrInsert(
                ['slug' => $r['slug']],
                array_merge($r, [
                    'cities'       => $cities,
                    'features'     => $features,
                    'affiliate_url' => null,
                    'is_active'    => 1,
                    'updated_at'   => $now,
                    'created_at'   => $now,
                ])
            );
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('housing_providers')) {
            DB::table('housing_providers')->whereIn('slug', [
                'wunderflats', 'spotahome', 'studiwohnen',
                'the-flag', 'staytoo', 'smartments-student', 'campo-novo',
            ])->delete();
        }
    }
};
