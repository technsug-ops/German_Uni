<?php

use App\Models\City;
use Illuminate\Database\Migrations\Migration;

/**
 * MLP 2025'te olmayan Hessen üni şehirleri (Gießen, Marburg, Kassel) için TAHMİNİ 2025 değeri.
 * Yöntem: ZEIT 2019 €/m² Kaltmiete × bölgesel dönüşüm oranı (R≈51,7) — hem 2019→2025 büyümeyi
 * hem €/m²-Kalt → 30m²-Warm dönüşümünü yakalayan, her iki veride bulunan Hessen/civar şehirlerinden
 * türetilen medyan oran. Tahmini olduğu source alanında işaretli.
 */
return new class extends Migration
{
    public function up(): void
    {
        $est = [
            'Gießen' => 473,
            'Marburg' => 489,
            'Kassel' => 389,
        ];
        foreach ($est as $name => $rent) {
            $city = City::where('name_de', $name)->orWhere('name_tr', $name)->first();
            if (! $city) continue;
            $city->forceFill([
                'student_rent_warm30' => $rent,
                'student_rent_index' => null, // tahmini — trend yok
                'student_rent_source' => 'Tahmini · ZEIT 2019 + bölgesel büyüme oranı',
                'student_rent_year' => 2025,
            ])->saveQuietly();
        }
    }

    public function down(): void
    {
        foreach (['Gießen', 'Marburg', 'Kassel'] as $name) {
            $c = City::where('name_de', $name)->first();
            if ($c) $c->forceFill(['student_rent_warm30' => null, 'student_rent_index' => null, 'student_rent_source' => null, 'student_rent_year' => null])->saveQuietly();
        }
    }
};
