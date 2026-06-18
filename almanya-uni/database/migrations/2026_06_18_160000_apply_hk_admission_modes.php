<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Programlara NC/Zulassungsmodus işareti — resources/data/hk-admission.json
 * (slug => admission_mode) haritasını uygular. Slug-bazlı = prod ID'lerinden
 * bağımsız, güvenli. Idempotent (aynı değeri tekrar yazar). YENİ program EKLEMEZ,
 * SİLMEZ — sadece mevcut programların admission_mode'unu işaretler.
 *
 * KAYNAK-BAĞIMSIZ: şu an Hochschulkompass scrape'inden üretildi; resmi HK verisi
 * gelince aynı JSON yeniden üretilip bu migration (veya kopyası) tekrar çalıştırılır.
 */
return new class extends Migration
{
    public function up(): void
    {
        $path = resource_path('data/hk-admission.json');
        if (! Schema::hasTable('programs') || ! is_file($path)) {
            return;
        }

        $map = json_decode(file_get_contents($path), true) ?: [];
        if (empty($map)) {
            return;
        }

        // Moda göre grupla → az sorguyla whereIn update
        $byMode = [];
        foreach ($map as $slug => $mode) {
            $byMode[$mode][] = $slug;
        }

        foreach ($byMode as $mode => $slugs) {
            foreach (array_chunk($slugs, 1000) as $chunk) {
                DB::table('programs')->whereIn('slug', $chunk)->update(['admission_mode' => $mode]);
            }
        }
    }

    public function down(): void
    {
        $path = resource_path('data/hk-admission.json');
        if (! Schema::hasTable('programs') || ! is_file($path)) {
            return;
        }

        $slugs = array_keys(json_decode(file_get_contents($path), true) ?: []);
        foreach (array_chunk($slugs, 1000) as $chunk) {
            DB::table('programs')->whereIn('slug', $chunk)->update(['admission_mode' => null]);
        }
    }
};
