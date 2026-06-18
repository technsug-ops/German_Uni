<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Deutschlandticket fiyat tazeleme: €49 → €69.
 *  1) city_cost_data.transport = 49 (Deutschlandticket aylık) → 69.
 *  2) cities.content_blocks içinde "Deutschlandticket 49€/49 €" → 69.
 * Değer/içerik-bazlı (prod ID'lerinden bağımsız). Expatrio vb. sağlayıcı
 * ücretlerindeki 49 € BLOG postlarında — onlara DOKUNMAZ (sadece transport + DT prose).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('city_cost_data')) {
            DB::table('city_cost_data')->where('transport', 49)->update(['transport' => 69]);
        }

        if (Schema::hasTable('cities')) {
            $rows = DB::table('cities')
                ->where('content_blocks', 'like', '%Deutschlandticket%49%')
                ->get(['id', 'content_blocks']);

            foreach ($rows as $c) {
                $new = str_replace(
                    ['Deutschlandticket 49€', 'Deutschlandticket 49 €', 'Deutschlandticket 49euro'],
                    ['Deutschlandticket 69€', 'Deutschlandticket 69 €', 'Deutschlandticket 69euro'],
                    (string) $c->content_blocks
                );
                if ($new !== $c->content_blocks) {
                    DB::table('cities')->where('id', $c->id)->update(['content_blocks' => $new]);
                }
            }
        }
    }

    public function down(): void
    {
        // Fiyat tazeleme — geri alınmaz (69 değerleri her zaman DT olmayabilir).
    }
};
