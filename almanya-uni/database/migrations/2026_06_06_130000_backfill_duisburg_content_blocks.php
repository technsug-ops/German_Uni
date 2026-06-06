<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Duisburg içerik backfill'i.
 *
 * Duisburg campus-only şehir (tek üni'si Duisburg-Essen'in EK kampüsü, birincil=Essen).
 * cities:enrich batch'i eskiden campus-only şehirleri atlıyordu (3cdaf39'da düzeltildi),
 * bu yüzden prod'da content_blocks hiç üretilmedi → sayfa "İçerik Henüz Hazırlanmadı".
 *
 * Lokal'de --slug ile üretilmiş 15 bloklu TR içeriği (database/data/...json) prod'a taşır,
 * böylece Gemini/route'a gerek kalmadan kesin dolar. EN/DE null kalır (TR-only enrichment).
 *
 * Idempotent: yalnızca content_blocks BOŞKEN doldurur → prod'da zaten enrich edilmişse
 * dokunmaz. Eloquent save() YASAK (Scout index sync prod'da FAIL) → saf DB::table.
 */
return new class extends Migration
{
    public function up(): void
    {
        $file = database_path('data/duisburg_content_blocks.json');
        if (! is_file($file)) {
            return;
        }

        $payload = json_decode(file_get_contents($file), true);
        if (! is_array($payload) || empty($payload['slug']) || empty($payload['content_blocks'])) {
            return;
        }

        $city = DB::table('cities')->where('slug', $payload['slug'])->first();
        if (! $city) {
            return;
        }

        // Yalnızca içerik boşsa doldur (prod'da elle/route ile enrich edilmişse DOKUNMA).
        $existing = $city->content_blocks ?? null;
        if (! empty($existing) && $existing !== '[]' && $existing !== 'null') {
            return;
        }

        DB::table('cities')->where('id', $city->id)->update([
            'content_blocks'   => json_encode($payload['content_blocks'], JSON_UNESCAPED_UNICODE),
            'image_url'        => $city->image_url ?: ($payload['image_url'] ?? null),
            'last_enriched_at' => now(),
            'updated_at'       => now(),
        ]);
    }

    public function down(): void
    {
        // Geri alma yok — içerik backfill'i kalıcı.
    }
};
