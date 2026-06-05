<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * QA raporu (Merve Hediye Özen, 2026-06-04) veri düzeltmeleri:
 *
 *  1) Sayfa 5: "Universität Potsdam" kart görseli yanlış — bir politikacı portresi
 *     (enrichment yanlış Wikipedia thumbnail'ı çekmiş). image_url temizle →
 *     CoverImage fallback (şehir manzarası/gradient) devreye girer.
 *
 *  2) Sayfa 32: Araçlar menüsünde "Üniversite Önerisi" açıklaması "5 soru" diyor
 *     ama quiz 8 adım. MenuPageSeeder zaten "8 soruda eşleşme" diyor; prod verisi
 *     bayat. Hedefli güncelle (is_enabled'a DOKUNMA → menü aç/kapa ayarları korunur).
 *
 * Idempotent + güvenli (kolon varlığı kontrol edilir).
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1) Potsdam yanlış görsel
        DB::table('universities')
            ->where('name_de', 'Universität Potsdam')
            ->update(['image_url' => null]);

        // 2) Menü açıklaması (sadece description — is_enabled korunur)
        if (Schema::hasTable('menu_pages')) {
            DB::table('menu_pages')
                ->where('key', 'tools.recommendation')
                ->update(['description' => '8 soruda eşleşme']);
        }

        // 3) Frankfurt yurdu (sayfa 26): domain değişti studierendenwerkfrankfurt.de
        //    (ölü) → swffm.de. website + application_url'i güncelle.
        if (Schema::hasTable('student_dorms')) {
            DB::table('student_dorms')
                ->where('organization', 'Studierendenwerk Frankfurt am Main')
                ->update([
                    'website_url'     => 'https://www.swffm.de/wohnen/wohnheime',
                    'application_url' => 'https://www.swffm.de/wohnen/wohnheime',
                ]);
        }
    }

    public function down(): void
    {
        // Geri alınamaz (yanlış veriyi geri koymak istemeyiz) — no-op.
    }
};
