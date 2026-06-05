<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\StudentDorm;
use Illuminate\Database\Seeder;

class StudentDormSeeder extends Seeder
{
    /**
     * Almanya'nın büyük öğrenci şehirleri için Studierendenwerk yurtları.
     * Kaynaklar: studierendenwerke.de, her bölgenin resmi sitesi (Mayıs 2026 itibarıyla manuel curate).
     */
    public function run(): void
    {
        $rows = [
            [
                'city' => 'berlin-q64',
                'organization' => 'Studierendenwerk Berlin',
                'website' => 'https://www.stw.berlin/wohnen.html',
                'application' => 'https://www.stw.berlin/wohnen/online-bewerbung.html',
                'waitlist' => '6-18 ay',
                'rent_min' => 220, 'rent_max' => 480,
                'amenities' => ['möbliert', 'Internet', 'Gemeinschaftsküche'],
                'notes_tr' => 'Berlin\'de yurt rekabeti çok yoğun. Başvuruyu üniversite kaydı kesinleşmeden önce, mümkün olduğunca erken yap (Mart-Nisan). Genellikle 6-18 ay bekleme listesi var. Kabul edilince ödeme yapmadan sözleşme imzalanmaz.',
                'notes_de' => 'Möblierte Studentenzimmer (oft in WG) und Apartments. Online-Bewerbung mit Wartelistenposition. Frühe Bewerbung kritisch.',
            ],
            [
                'city' => 'munchen-q1726',
                'organization' => 'Studierendenwerk München Oberbayern',
                'website' => 'https://www.studierendenwerk-muenchen-oberbayern.de/wohnen/',
                'application' => 'https://www.studierendenwerk-muenchen-oberbayern.de/wohnen/online-bewerbung/',
                'waitlist' => '12-24 ay',
                'rent_min' => 280, 'rent_max' => 550,
                'amenities' => ['möbliert', 'Internet'],
                'notes_tr' => 'München Almanya\'nın en pahalı şehri. Yurt başvurusunu üniversite kabul mektubu gelmeden ön-başvuru ile yapmaya çalış. Bekleme listesi çok uzun (1-2 yıl). Yedek plan olarak özel WG ara.',
            ],
            [
                'city' => 'hamburg-q1055',
                'organization' => 'Studierendenwerk Hamburg',
                'website' => 'https://www.studierendenwerk-hamburg.de/wohnen/',
                'application' => 'https://www.studierendenwerk-hamburg.de/wohnen/online-bewerbung/',
                'waitlist' => '6-12 ay',
                'rent_min' => 240, 'rent_max' => 500,
                'amenities' => ['möbliert', 'Internet'],
                'notes_tr' => 'Hamburg orta-yüksek rekabetli. Yurtlar yıl boyu başvuruya açık. Üniversite onayı sonrası hemen başvuru yap.',
            ],
            [
                'city' => 'frankfurt-am-main-q1794',
                'organization' => 'Studierendenwerk Frankfurt am Main',
                // Domain değişti: studierendenwerkfrankfurt.de (ölü) → swffm.de (2026 doğrulandı)
                'website' => 'https://www.swffm.de/wohnen/wohnheime',
                'application' => 'https://www.swffm.de/wohnen/wohnheime',
                'waitlist' => '6-12 ay',
                'rent_min' => 230, 'rent_max' => 470,
                'amenities' => ['möbliert', 'Internet'],
                'notes_tr' => 'Frankfurt\'ta yurt başvurusu için TOEFL/IELTS gibi belgelerin sertifika kopyalarını da hazır bulundur.',
            ],
            [
                'city' => 'koln-q365',
                'organization' => 'Kölner Studierendenwerk',
                'website' => 'https://www.kstw.de/wohnen',
                'application' => 'https://www.kstw.de/wohnen/online-bewerbung',
                'waitlist' => '6-12 ay',
                'rent_min' => 220, 'rent_max' => 450,
                'amenities' => ['möbliert', 'Internet'],
                'notes_tr' => 'Köln\'de yurt rekabeti orta. Şehir merkezine yakın yurtlar çabuk dolar.',
            ],
            [
                'city' => 'stuttgart-q1022',
                'organization' => 'Studierendenwerk Stuttgart',
                'website' => 'https://www.studierendenwerk-stuttgart.de/wohnen/',
                'application' => 'https://www.studierendenwerk-stuttgart.de/wohnen/online-bewerbung/',
                'waitlist' => '6-12 ay',
                'rent_min' => 250, 'rent_max' => 480,
                'amenities' => ['möbliert', 'Internet', 'Waschküche'],
                'notes_tr' => 'Stuttgart yurtları teknik üniversiteler (Uni Stuttgart, HFT) civarında yoğunlaşır.',
            ],
            [
                'city' => 'heidelberg-q2966',
                'organization' => 'Studierendenwerk Heidelberg',
                'website' => 'https://www.stw.uni-heidelberg.de/wohnen',
                'application' => 'https://www.stw.uni-heidelberg.de/wohnen/online-bewerbung',
                'waitlist' => '6-15 ay',
                'rent_min' => 230, 'rent_max' => 460,
                'amenities' => ['möbliert', 'Internet'],
                'notes_tr' => 'Heidelberg küçük ama çok talep gören bir şehir. Yurt yetersiz, özel WG\'ye de erken başla.',
            ],
            [
                'city' => 'bonn-q586',
                'organization' => 'Studierendenwerk Bonn',
                'website' => 'https://www.studierendenwerk-bonn.de/wohnen/',
                'application' => 'https://www.studierendenwerk-bonn.de/wohnen/online-bewerbung/',
                'waitlist' => '4-10 ay',
                'rent_min' => 220, 'rent_max' => 430,
                'amenities' => ['möbliert', 'Internet'],
                'notes_tr' => 'Bonn yurt başvurusu görece kolay. Şehir merkezi küçük, ulaşım iyi.',
            ],
            [
                'city' => 'mainz-q1720',
                'organization' => 'Studierendenwerk Mainz',
                'website' => 'https://www.studierendenwerk-mainz.de/wohnen/',
                'application' => 'https://www.studierendenwerk-mainz.de/wohnen/online-bewerbung/',
                'waitlist' => '4-10 ay',
                'rent_min' => 215, 'rent_max' => 420,
                'amenities' => ['möbliert', 'Internet'],
                'notes_tr' => 'Mainz orta zorluk. Frankfurt\'a yakın, ulaşım iyi.',
            ],
            [
                'city' => 'freiburg-im-breisgau-q2833',
                'organization' => 'Studierendenwerk Freiburg',
                'website' => 'https://www.swfr.de/wohnen',
                'application' => 'https://www.swfr.de/wohnen/online-bewerbung',
                'waitlist' => '6-12 ay',
                'rent_min' => 230, 'rent_max' => 450,
                'amenities' => ['möbliert', 'Internet'],
                'notes_tr' => 'Freiburg küçük ama yoğun talep gören şehir. Yurt başvurusu için erken hareket et.',
            ],
            [
                'city' => 'tubingen-q3806',
                'organization' => 'Studierendenwerk Tübingen-Hohenheim',
                'website' => 'https://www.my-stuwe.de/wohnen/',
                'application' => 'https://www.my-stuwe.de/wohnen/online-bewerbung/',
                'waitlist' => '4-10 ay',
                'rent_min' => 220, 'rent_max' => 420,
                'amenities' => ['möbliert', 'Internet'],
                'notes_tr' => 'Tübingen sadece üniversite şehri, yurt rekabeti yüksek ama bekleme listesi makul.',
            ],
            [
                'city' => 'karlsruhe-q1040',
                'organization' => 'Studierendenwerk Karlsruhe',
                'website' => 'https://www.sw-ka.de/de/wohnen/',
                'application' => 'https://www.sw-ka.de/de/wohnen/online-bewerbung/',
                'waitlist' => '4-9 ay',
                'rent_min' => 210, 'rent_max' => 400,
                'amenities' => ['möbliert', 'Internet'],
                'notes_tr' => 'Karlsruhe görece kolay yurt bulunur, KIT yakınında yoğun.',
            ],
            [
                'city' => 'munster-q2742',
                'organization' => 'Studierendenwerk Münster',
                'website' => 'https://www.stw-muenster.de/wohnen',
                'application' => 'https://www.stw-muenster.de/wohnen/online-bewerbung',
                'waitlist' => '4-10 ay',
                'rent_min' => 220, 'rent_max' => 420,
                'amenities' => ['möbliert', 'Internet'],
                'notes_tr' => 'Münster öğrenci şehri, yurt sistemi gelişmiş.',
            ],
            [
                'city' => 'leipzig-q2079',
                'organization' => 'Studentenwerk Leipzig',
                'website' => 'https://www.studentenwerk-leipzig.de/wohnen',
                'application' => 'https://www.studentenwerk-leipzig.de/wohnen/online-bewerbung',
                'waitlist' => '2-6 ay',
                'rent_min' => 190, 'rent_max' => 370,
                'amenities' => ['möbliert', 'Internet'],
                'notes_tr' => 'Leipzig ucuz ve görece kolay yurt. Doğu Almanya\'nın en hareketli öğrenci şehri.',
            ],
            [
                'city' => 'dresden-q1731',
                'organization' => 'Studentenwerk Dresden',
                'website' => 'https://www.studentenwerk-dresden.de/wohnen/',
                'application' => 'https://www.studentenwerk-dresden.de/wohnen/online-bewerbung/',
                'waitlist' => '2-6 ay',
                'rent_min' => 180, 'rent_max' => 360,
                'amenities' => ['möbliert', 'Internet'],
                'notes_tr' => 'Dresden ucuz, yurt başvurusu nispeten kolay.',
            ],
        ];

        foreach ($rows as $i => $r) {
            $city = City::where('slug', $r['city'])->first();
            if (! $city) continue;

            StudentDorm::updateOrCreate(
                ['city_id' => $city->id, 'organization' => $r['organization']],
                [
                    'city_name'       => $city->name_de,
                    'website_url'     => $r['website'],
                    'application_url' => $r['application'],
                    'waitlist_avg'    => $r['waitlist'],
                    'rent_min_eur'    => $r['rent_min'],
                    'rent_max_eur'    => $r['rent_max'],
                    'amenities'       => $r['amenities'],
                    'notes_tr'        => $r['notes_tr'] ?? null,
                    'notes_de'        => $r['notes_de'] ?? null,
                    'sort_order'      => $i,
                    'is_active'       => true,
                ]
            );
        }

        $this->command?->info('StudentDorm seeded: ' . StudentDorm::count() . ' rows.');
    }
}
