<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Yurt/konut PLATFORMLARI (affiliate adayı): HousingAnywhere, Uniplaces, WG-Gesucht.
 * Gelmeden online rezervasyon platformları. website set; affiliate_url'i admin'den
 * doldur (program kaydı sonrası) → /go/housing tıklama takibi şimdiden çalışır.
 * Idempotent (slug).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('housing_providers')) {
            return;
        }
        $now = now();
        $cities = json_encode(['Berlin', 'München', 'Hamburg', 'Köln', 'Frankfurt', 'Stuttgart'], JSON_UNESCAPED_UNICODE);

        $rows = [
            [
                'slug' => 'housinganywhere', 'name' => 'HousingAnywhere', 'type' => 'platform',
                'website' => 'https://housinganywhere.com', 'affiliate_url' => null,
                'description_tr' => 'Almanya\'ya gelmeden online oda/daire kirala — uluslararası öğrenciler için güvenli rezervasyon, doğrulanmış ilanlar, kira koruması.',
                'description_en' => 'Book a room/apartment online before you arrive — secure booking for international students, verified listings, rent protection.',
                'description_de' => 'Buche online ein Zimmer/Wohnung vor der Anreise — sichere Buchung für internationale Studierende, verifizierte Inserate.',
                'price_min' => 450, 'price_max' => 950, 'is_featured' => 1, 'sort_order' => 1,
            ],
            [
                'slug' => 'uniplaces', 'name' => 'Uniplaces', 'type' => 'platform',
                'website' => 'https://www.uniplaces.com', 'affiliate_url' => null,
                'description_tr' => 'Öğrenci konaklaması platformu — şehir merkezinde mobilyalı oda/stüdyo, gelmeden rezerve et, esnek iptal.',
                'description_en' => 'Student accommodation platform — furnished rooms/studios in city centres, reserve before arrival, flexible cancellation.',
                'description_de' => 'Plattform für Studentenunterkünfte — möblierte Zimmer/Studios in Innenstädten, vor der Anreise reservieren.',
                'price_min' => 400, 'price_max' => 850, 'is_featured' => 1, 'sort_order' => 2,
            ],
            [
                'slug' => 'wg-gesucht', 'name' => 'WG-Gesucht', 'type' => 'platform',
                'website' => 'https://www.wg-gesucht.de', 'affiliate_url' => null,
                'description_tr' => 'Almanya\'nın en büyük WG (paylaşımlı daire) portalı — yerinde oda bulmak için #1. En uygun fiyatlı seçenek (temel Almanca gerekir).',
                'description_en' => 'Germany\'s biggest WG (shared flat) portal — the #1 for finding a room on the ground. The cheapest option (basic German helps).',
                'description_de' => 'Deutschlands größtes WG-Portal — die Nr. 1, um vor Ort ein Zimmer zu finden. Die günstigste Option.',
                'price_min' => 300, 'price_max' => 650, 'is_featured' => 0, 'sort_order' => 3,
            ],
        ];

        foreach ($rows as $r) {
            DB::table('housing_providers')->updateOrInsert(
                ['slug' => $r['slug']],
                array_merge($r, [
                    'cities'     => $cities,
                    'is_active'  => 1,
                    'updated_at' => $now,
                    'created_at' => $now,
                ])
            );
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('housing_providers')) {
            DB::table('housing_providers')->whereIn('slug', ['housinganywhere', 'uniplaces', 'wg-gesucht'])->delete();
        }
    }
};
