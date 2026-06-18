<?php

use App\Models\City;
use Illuminate\Database\Migrations\Migration;

/**
 * MLP 2025'te olmayan ek üni şehirleri (Augsburg, Ingolstadt — Bayern; Erfurt — Thüringen)
 * için TAHMİNİ 2025 değeri. Yöntem: ZEIT 2019 €/m² Kaltmiete × bölgesel dönüşüm oranı
 * (Bayern R≈58, Doğu/Thüringen R≈63,4) — her iki veride bulunan bölge şehirlerinden türetilmiş.
 * Kassel/Gießen/Marburg ile aynı yaklaşım; source alanında "Tahmini" işaretli.
 */
return new class extends Migration
{
    public function up(): void
    {
        $est = [
            'Augsburg' => 589,
            'Ingolstadt' => 651,
            'Erfurt' => 468,
        ];
        foreach ($est as $name => $rent) {
            $city = City::where('name_de', $name)->orWhere('name_tr', $name)->first();
            if (! $city) continue;
            $city->forceFill([
                'student_rent_warm30' => $rent,
                'student_rent_index' => null,
                'student_rent_source' => 'Tahmini · ZEIT 2019 + bölgesel büyüme oranı',
                'student_rent_year' => 2025,
            ])->saveQuietly();
        }
    }

    public function down(): void
    {
        foreach (['Augsburg', 'Ingolstadt', 'Erfurt'] as $name) {
            $c = City::where('name_de', $name)->first();
            if ($c) $c->forceFill(['student_rent_warm30' => null, 'student_rent_index' => null, 'student_rent_source' => null, 'student_rent_year' => null])->saveQuietly();
        }
    }
};
