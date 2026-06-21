<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Deutschlandticket 1 Ocak 2026'dan beri aylık 63 € (49 € → 58 € → 63 €).
 * Maliyet hesaplayıcıdaki "Transport (semester / Deutschlandticket)" kalemi
 * city_cost_data.transport'tan geliyordu ve şehir şehir tutarsız/eski değerler
 * (0,18,...,69,75) içeriyordu. Deutschlandticket ülke çapında SABİT olduğundan
 * tüm şehirlerde 63 €'ya sabitlenir. Idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('city_cost_data')) return;
        if (! Schema::hasColumn('city_cost_data', 'transport')) return;

        DB::table('city_cost_data')->update([
            'transport'  => 63,
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Eski değerler şehir-spesifik ve tutarsızdı; geri almak anlamsız (no-op).
    }
};
