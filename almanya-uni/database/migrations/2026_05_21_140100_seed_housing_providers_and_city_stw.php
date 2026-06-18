<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        // Re-run safety: ilk insertten sonra hata olduysa kalmış kayıtları temizle
        DB::table('housing_providers')->truncate();

        // ============================================================
        // STUDIERENDENWERK (10 ana şehir — iletişim bilgisi netleştirilmiş)
        // ============================================================
        $stws = [
            ['Studierendenwerk Berlin',            'studierendenwerk-berlin',     'Berlin',     'https://www.stw.berlin',           'info@stw.berlin',           '+49 30 9393-0',     180, 350, 9200, '2-3 sem'],
            ['Studierendenwerk Hamburg',           'studierendenwerk-hamburg',    'Hamburg',    'https://www.stwhh.de',             'info@stwhh.de',             '+49 40 4141-0',     220, 380, 5800, '2-4 sem'],
            ['Studierendenwerk München Oberbayern','studierendenwerk-muenchen',   'München',    'https://www.stw-muenchen.de',      'info@stw-muenchen.de',      '+49 89 38196-0',    260, 450, 9500, '3-7 sem'],
            ['Studierendenwerk Köln',              'studierendenwerk-koeln',      'Köln',       'https://www.kstw.de',              'wohnen@kstw.de',            '+49 221 94265-211', 220, 350, 4200, '2-3 sem'],
            ['Studentenwerk Frankfurt',            'studentenwerk-frankfurt',     'Frankfurt',  'https://www.swffm.de',             'info@swffm.de',             '+49 69 798-0',      260, 400, 3500, '2-4 sem'],
            ['Studierendenwerk Stuttgart',         'studierendenwerk-stuttgart',  'Stuttgart',  'https://www.stud-stg.de',          'info@stud-stg.de',          '+49 711 9545-0',    240, 380, 3800, '2-3 sem'],
            ['Studierendenwerk Düsseldorf',        'studierendenwerk-duesseldorf','Düsseldorf', 'https://www.stw-d.de',             'info@stw-d.de',             '+49 211 9110-0',    230, 360, 2900, '1-3 sem'],
            ['Studentenwerk Dortmund',             'studentenwerk-dortmund',      'Dortmund',   'https://www.stw-dortmund.de',      'info@stw-dortmund.de',      '+49 231 755-0',     190, 300, 3200, '1-2 sem'],
            ['Studentenwerk Essen-Duisburg',       'studentenwerk-essen-duisburg','Essen',      'https://www.stw-edu.de',           'info@stw-edu.de',           '+49 201 8341-0',    180, 280, 2100, '1-2 sem'],
            ['Studentenwerk Leipzig',              'studentenwerk-leipzig',       'Leipzig',    'https://www.studentenwerk-leipzig.de','info@studentenwerk-leipzig.de','+49 341 9659-9',  150, 280, 4800, '1-2 sem'],
        ];

        // Diğer 20 şehir için ek STW (iletişim daha genel, ama doğru website)
        $additionalStws = [
            ['Studentenwerk Bremen',               'studentenwerk-bremen',        'Bremen',         'https://www.stw-bremen.de',         null, null, 210, 330, 2400, '2-3 sem'],
            ['Studentenwerk Dresden',              'studentenwerk-dresden',       'Dresden',        'https://www.studentenwerk-dresden.de', null, null, 160, 280, 5100, '1-2 sem'],
            ['Studentenwerk Hannover',             'studentenwerk-hannover',      'Hannover',       'https://www.studentenwerk-hannover.de', null, null, 220, 340, 3600, '2-3 sem'],
            ['Studentenwerk Erlangen-Nürnberg',    'studentenwerk-erlangen-nuernberg','Nürnberg',   'https://www.werkswelt.de',          null, null, 210, 320, 2800, '2-3 sem'],
            ['Studentenwerk Bochum',               'studentenwerk-bochum',        'Bochum',         'https://www.akafoe.de',             null, null, 190, 290, 2400, '1-2 sem'],
            ['Studentenwerk Wuppertal',            'studentenwerk-wuppertal',     'Wuppertal',      'https://www.hochschul-sozialwerk-wuppertal.de', null, null, 180, 280, 1500, '1-2 sem'],
            ['Studentenwerk Bielefeld',            'studentenwerk-bielefeld',     'Bielefeld',      'https://www.studierendenwerk-bielefeld.de', null, null, 200, 300, 1900, '1-2 sem'],
            ['Studentenwerk Bonn',                 'studentenwerk-bonn',          'Bonn',           'https://www.studierendenwerk-bonn.de', null, null, 220, 350, 2600, '2-3 sem'],
            ['Studentenwerk Münster',              'studentenwerk-muenster',      'Münster',        'https://www.stw-muenster.de',       null, null, 210, 320, 2100, '1-2 sem'],
            ['Studentenwerk Karlsruhe',            'studentenwerk-karlsruhe',     'Karlsruhe',      'https://www.sw-ka.de',              null, null, 210, 330, 2200, '2-3 sem'],
            ['Studierendenwerk Mannheim',          'studierendenwerk-mannheim',   'Mannheim',       'https://www.stw-ma.de',             null, null, 220, 340, 1800, '2-3 sem'],
            ['Studentenwerk Augsburg',             'studentenwerk-augsburg',      'Augsburg',       'https://studierendenwerk-augsburg.de', null, null, 200, 310, 1600, '2-3 sem'],
            ['Studierendenwerk Wiesbaden',         'studierendenwerk-wiesbaden',  'Wiesbaden',      'https://www.studentenwerk-wiesbaden.de', null, null, 230, 360, 1400, '2-3 sem'],
            ['Studentenwerk Mönchengladbach',      'studentenwerk-moenchengladbach','Mönchengladbach','https://www.stw-niederrhein.de',  null, null, 180, 290, 1000, '1-2 sem'],
            ['Studentenwerk Braunschweig',         'studentenwerk-braunschweig',  'Braunschweig',   'https://www.stw-on.de',             null, null, 210, 320, 1800, '2-3 sem'],
            ['Studentenwerk Chemnitz-Zwickau',     'studentenwerk-chemnitz',      'Chemnitz',       'https://www.swcz.de',               null, null, 150, 250, 2100, '1-2 sem'],
            ['Studentenwerk Schleswig-Holstein',   'studentenwerk-sh-kiel',       'Kiel',           'https://www.studentenwerk.sh',      null, null, 200, 300, 1600, '1-2 sem'],
            ['Studierendenwerk Aachen',            'studierendenwerk-aachen',     'Aachen',         'https://www.studierendenwerk-aachen.de', null, null, 240, 380, 2300, '2-4 sem'],
            ['Studentenwerk Gelsenkirchen',        'studentenwerk-westfalen',     'Gelsenkirchen',  'https://www.stw-westfalen.de',      null, null, 170, 260, 1200, '1-2 sem'],
        ];

        foreach (array_merge($stws, $additionalStws) as $i => $s) {
            DB::table('housing_providers')->insert([
                'name'           => $s[0],
                'slug'           => $s[1],
                'type'           => 'studierendenwerk',
                'website'        => $s[3],
                'email'          => $s[4] ?? null,
                'phone'          => $s[5] ?? null,
                'description'    => "Şehirdeki üniversitelerin resmi devlet yurt işletmecisi. Online başvuru → bekleme listesi → kontenjan açılınca teklif. Üniversite kayıt belgesi şart.",
                'price_min'      => $s[6],
                'price_max'      => $s[7],
                'total_capacity' => $s[8],
                'waiting_period' => $s[9],
                'cities'         => json_encode([$s[2]]),
                'features'       => json_encode(['ucuz','sosyal_alan','camasirhane','mutfak_paylasimli','uni_kayit_sart']),
                'is_active'      => true,
                'sort_order'     => $i,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }

        // ============================================================
        // PRIVATE CHAINS (8 şirket)
        // ============================================================
        $privates = [
            [
                'name' => 'The Fizz',
                'slug' => 'the-fizz',
                'website' => 'https://www.the-fizz.com',
                'email' => 'info@the-fizz.com',
                'desc' => 'International Campus AG\'nin premium öğrenci konaklama markası. Tam döşenmiş studio + salon, fitness, internet ve sigortalar dahil. Almanya 8+ şehirde.',
                'price_min' => 600, 'price_max' => 1200,
                'cities' => ['Aachen', 'Berlin', 'Bremen', 'Darmstadt', 'Frankfurt', 'Freiburg', 'Hamburg', 'Hannover', 'München', 'Düsseldorf', 'Wiesbaden', 'Bonn'],
                'features' => ['mobliyali','fitness','internet_dahil','utility_dahil','topluluk_alan','24_7_resepsiyon'],
            ],
            [
                'name' => 'YouniQ',
                'slug' => 'youniq',
                'website' => 'https://www.youniq-students.com',
                'email' => 'info@youniq.de',
                'desc' => 'UPARTMENTS Real Estate\'in modern öğrenci apartmanları. 2.500+ öğrenci kapasitesi. Komisyonsuz, Scout-on-site destek.',
                'price_min' => 400, 'price_max' => 800,
                'cities' => ['Berlin', 'Frankfurt', 'Köln', 'Leipzig', 'München', 'Dresden', 'Stuttgart'],
                'features' => ['mobliyali','fitness','camasirhane','komisyonsuz','scout_destek'],
            ],
            [
                'name' => 'Uniapart',
                'slug' => 'uniapart',
                'website' => 'https://www.uniapart.de',
                'email' => 'info@uniapart.de',
                'desc' => 'Türkiye\'deki "öğrenci pansiyonu" mantığına yakın, uygun fiyatlı, ülke geneli 30+ şehirde mevcut. WG-Zimmer, single oda ve studio seçenekleri.',
                'price_min' => 300, 'price_max' => 600,
                'cities' => ['Hamburg', 'Düsseldorf', 'Dortmund', 'Essen', 'Leipzig', 'Bremen', 'Dresden', 'Hannover', 'Duisburg', 'Bochum', 'Wuppertal', 'Bielefeld', 'Bonn', 'Münster', 'Mannheim', 'Wiesbaden', 'Gelsenkirchen', 'Mönchengladbach', 'Braunschweig', 'Chemnitz', 'Kiel', 'Aachen'],
                'features' => ['wg_zimmer','single','studio','mobliyali_secenek','hizli_basvuru'],
            ],
            [
                'name' => 'Partio Living',
                'slug' => 'partio-living',
                'website' => 'https://www.partio-living.de',
                'email' => 'info@partio-living.de',
                'desc' => 'Bayern + Baden-Württemberg odaklı çağdaş öğrenci apartmanları. Topluluk alışverişi olanağı.',
                'price_min' => 350, 'price_max' => 700,
                'cities' => ['München', 'Stuttgart', 'Nürnberg', 'Augsburg', 'Karlsruhe'],
                'features' => ['mobliyali','cagdas_tasarim','topluluk'],
            ],
            [
                'name' => 'OXXO Living',
                'slug' => 'oxxo-living',
                'website' => 'https://www.oxxo.de',
                'email' => 'info@oxxo.de',
                'desc' => 'Doğu Almanya odaklı ekonomik özel yurt. 1-2 dönem sözleşme esnekliği.',
                'price_min' => 250, 'price_max' => 500,
                'cities' => ['Leipzig', 'Dresden', 'Chemnitz', 'Bochum'],
                'features' => ['mobliyali','ekonomik','esnek_sozlesme'],
            ],
            [
                'name' => 'Nido — The Alp Studios',
                'slug' => 'nido',
                'website' => 'https://www.nido.de',
                'email' => 'info@nido.de',
                'desc' => 'München\'da lüks premium öğrenci apartmanı. Gym + açık havada çalışma alanları.',
                'price_min' => 550, 'price_max' => 950,
                'cities' => ['München'],
                'features' => ['premium','gym','tasarim','co_working'],
            ],
            [
                'name' => 'KKIK & Home4Students',
                'slug' => 'kkik-home4students',
                'website' => 'https://www.home4students.de',
                'email' => null,
                'desc' => 'Kuzey Almanya odaklı (Hamburg, Hannover, Bremen, Kiel, Braunschweig, Dortmund) uygun fiyatlı özel yurt grubu.',
                'price_min' => 200, 'price_max' => 400,
                'cities' => ['Hamburg', 'Hannover', 'Bremen', 'Kiel', 'Braunschweig', 'Dortmund'],
                'features' => ['ekonomik','mobliyali'],
            ],
            [
                'name' => 'Neon Wood',
                'slug' => 'neon-wood',
                'website' => 'https://neonwood.com',
                'email' => null,
                'desc' => 'Berlin merkezli yeni nesil micro-apartment markası. Co-living + akıllı tasarım.',
                'price_min' => 500, 'price_max' => 900,
                'cities' => ['Berlin'],
                'features' => ['co_living','mobliyali','akilli_tasarim'],
            ],
        ];

        $sortOffset = 100;
        foreach ($privates as $i => $p) {
            DB::table('housing_providers')->insert([
                'name'        => $p['name'],
                'slug'        => $p['slug'],
                'type'        => 'private_chain',
                'website'     => $p['website'],
                'email'       => $p['email'],
                'phone'       => null,
                'description' => $p['desc'],
                'price_min'   => $p['price_min'],
                'price_max'   => $p['price_max'],
                'cities'      => json_encode($p['cities']),
                'features'    => json_encode($p['features']),
                'is_featured' => in_array($p['slug'], ['the-fizz', 'youniq', 'uniapart']),
                'is_active'   => true,
                'sort_order'  => $sortOffset + $i,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }

        // ============================================================
        // PLATFORMS (4 portal)
        // ============================================================
        $platforms = [
            ['Student.com',         'student-com',         'https://www.student.com',          'support@student.com', 'Global öğrenci konaklama platformu. Almanya 20+ şehirde Studierendenwerk + özel yurtları toplu listeler.'],
            ['HousingAnywhere',     'housinganywhere',     'https://www.housinganywhere.com',  'support@housinganywhere.com', 'Uluslararası öğrenciye odaklı kiralık platform. Online ödeme + güvenli sözleşme.'],
            ['WG-Gesucht',          'wg-gesucht',          'https://www.wg-gesucht.de',        null, 'Almanya\'nın en büyük WG (paylaşımlı daire) portalı. Türk öğrenciler arası 1 numaralı tercih.'],
            ['ImmobilienScout24',   'immobilienscout24',   'https://www.immobilienscout24.de', null, 'Almanya\'nın en büyük emlak sitesi. Öğrenci için: WG + studio + 1-zimmer Apartment filtresi.'],
        ];

        $sortOffset = 200;
        foreach ($platforms as $i => $pl) {
            DB::table('housing_providers')->insert([
                'name'        => $pl[0],
                'slug'        => $pl[1],
                'type'        => 'platform',
                'website'     => $pl[2],
                'email'       => $pl[3],
                'description' => $pl[4],
                'cities'      => json_encode(['Tüm Almanya']),
                'is_active'   => true,
                'sort_order'  => $sortOffset + $i,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }

        // ============================================================
        // cities tablosuna STW + ort. fiyat + chain slug listesi ekle
        // ============================================================
        // Excel'den 30 şehir verisi:
        $cityHousing = [
            // [city_name, stw_slug, avg_min, avg_max, chain_slugs]
            ['Berlin',          'studierendenwerk-berlin',      250, 350, ['the-fizz','youniq','neon-wood']],
            ['Hamburg',         'studierendenwerk-hamburg',     280, 380, ['the-fizz','uniapart','kkik-home4students']],
            ['München',         'studierendenwerk-muenchen',    300, 450, ['youniq','the-fizz','partio-living','nido']],
            ['Köln',            'studierendenwerk-koeln',       260, 350, ['youniq','uniapart','the-fizz']],
            ['Frankfurt am Main','studentenwerk-frankfurt',     300, 400, ['the-fizz','youniq','uniapart']],
            ['Stuttgart',       'studierendenwerk-stuttgart',   280, 380, ['youniq','partio-living','uniapart']],
            ['Düsseldorf',      'studierendenwerk-duesseldorf', 270, 360, ['the-fizz','uniapart','kkik-home4students']],
            ['Dortmund',        'studentenwerk-dortmund',       220, 300, ['uniapart','oxxo-living','kkik-home4students']],
            ['Essen',           'studentenwerk-essen-duisburg', 210, 280, ['uniapart','kkik-home4students']],
            ['Leipzig',         'studentenwerk-leipzig',        180, 280, ['youniq','the-fizz','uniapart','oxxo-living']],
            ['Bremen',          'studentenwerk-bremen',         240, 330, ['the-fizz','uniapart','kkik-home4students']],
            ['Dresden',         'studentenwerk-dresden',        190, 280, ['youniq','uniapart','oxxo-living']],
            ['Hannover',        'studentenwerk-hannover',       250, 340, ['the-fizz','uniapart','kkik-home4students']],
            ['Nürnberg',        'studentenwerk-erlangen-nuernberg', 240, 320, ['partio-living']],
            ['Duisburg',        'studentenwerk-essen-duisburg', 200, 280, ['uniapart']],
            ['Bochum',          'studentenwerk-bochum',         210, 290, ['uniapart','oxxo-living']],
            ['Wuppertal',       'studentenwerk-wuppertal',      200, 280, ['uniapart']],
            ['Bielefeld',       'studentenwerk-bielefeld',      220, 300, ['uniapart']],
            ['Bonn',            'studentenwerk-bonn',           250, 350, ['uniapart','the-fizz']],
            ['Münster',         'studentenwerk-muenster',       230, 320, ['uniapart']],
            ['Karlsruhe',       'studentenwerk-karlsruhe',      240, 330, ['partio-living','uniapart']],
            ['Mannheim',        'studierendenwerk-mannheim',    250, 340, ['uniapart']],
            ['Augsburg',        'studentenwerk-augsburg',       230, 310, ['partio-living']],
            ['Wiesbaden',       'studierendenwerk-wiesbaden',   270, 360, ['the-fizz','uniapart']],
            ['Gelsenkirchen',   'studentenwerk-westfalen',      190, 260, ['uniapart']],
            ['Mönchengladbach', 'studentenwerk-moenchengladbach', 210, 290, ['uniapart']],
            ['Braunschweig',    'studentenwerk-braunschweig',   240, 320, ['uniapart','kkik-home4students']],
            ['Chemnitz',        'studentenwerk-chemnitz',       170, 250, ['uniapart','oxxo-living']],
            ['Kiel',            'studentenwerk-sh-kiel',        220, 300, ['uniapart','kkik-home4students']],
            ['Aachen',          'studierendenwerk-aachen',      280, 380, ['the-fizz','uniapart']],
        ];

        $providerLookup = DB::table('housing_providers')
            ->whereIn('slug', array_merge(
                array_column($cityHousing, 1),
                array_unique(array_merge(...array_column($cityHousing, 4)))
            ))
            ->get(['slug','name','website','total_capacity','waiting_period'])
            ->keyBy('slug');

        foreach ($cityHousing as $row) {
            [$cityName, $stwSlug, $min, $max, $chains] = $row;

            $stw = $providerLookup[$stwSlug] ?? null;

            DB::table('cities')
                ->where('name_de', $cityName)
                ->update([
                    'stw_name'            => $stw->name ?? null,
                    'stw_website'         => $stw->website ?? null,
                    'stw_capacity'        => $stw->total_capacity ?? null,
                    'stw_waiting'         => $stw->waiting_period ?? null,
                    'avg_rent_min'        => $min,
                    'avg_rent_max'        => $max,
                    'private_chain_slugs' => json_encode($chains),
                ]);
        }
    }

    public function down(): void
    {
        DB::table('housing_providers')->truncate();
        DB::table('cities')->update([
            'stw_name' => null, 'stw_website' => null, 'stw_capacity' => null,
            'stw_waiting' => null, 'avg_rent_min' => null, 'avg_rent_max' => null,
            'private_chain_slugs' => null,
        ]);
    }
};
