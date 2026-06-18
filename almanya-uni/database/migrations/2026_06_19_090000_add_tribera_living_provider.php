<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tribera Living — öğrenci konaklama + co-living (Milestone/Basecamp markaları).
 * Almanya: Aachen, Bonn, Dortmund, Göttingen. Idempotent (slug).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('housing_providers')) {
            return;
        }

        DB::table('housing_providers')->updateOrInsert(
            ['slug' => 'tribera-living'],
            [
                'name'           => 'Tribera Living',
                'type'           => 'private_chain',
                'website'        => 'https://triberaliving.com/',
                'affiliate_url'  => null,
                'is_featured'    => 0,
                'sort_order'     => 112,
                'price_min'      => 450,
                'price_max'      => 800,
                'cities'         => json_encode(['Aachen', 'Bonn', 'Dortmund', 'Göttingen'], JSON_UNESCAPED_UNICODE),
                'features'       => json_encode(['mobliyali', 'utility_dahil', 'gym', 'calisma_alani', 'co_living'], JSON_UNESCAPED_UNICODE),
                'description_tr' => 'Öğrenci konaklama + co-living (Milestone/Basecamp markaları) — tam mobilyalı stüdyo/oda, her şey dahil (elektrik, ısınma, su, WiFi), gym + çalışma alanları. Aachen, Bonn, Dortmund, Göttingen.',
                'description_en' => 'Student accommodation + co-living (Milestone/Basecamp brands) — fully furnished studios/rooms, all-inclusive (electricity, heating, water, WiFi), gym + study zones.',
                'description_de' => 'Studentisches Wohnen + Co-Living (Marken Milestone/Basecamp) — voll möblierte Studios/Zimmer, All-inclusive (Strom, Heizung, Wasser, WLAN), Gym + Lernbereiche.',
                'is_active'      => 1,
                'updated_at'     => now(),
                'created_at'     => now(),
            ]
        );
    }

    public function down(): void
    {
        if (Schema::hasTable('housing_providers')) {
            DB::table('housing_providers')->where('slug', 'tribera-living')->delete();
        }
    }
};
