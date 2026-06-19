<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * NC/Zulassungsmodus backfill — EK parti. resources/data/hk-admission-extra.json
 * (slug => admission_mode) haritasını uygular; programs:backfill-hk-admission ile
 * hk_catalog'dan üretildi. Slug-bazlı (prod ID'lerinden bağımsız).
 *
 * FILL-EMPTY-ONLY: yalnızca admission_mode'u BOŞ (null/'') olan programları işaretler
 * — mevcut (daha güvenilir kaynaklı) verinin üzerine YAZMAZ. Idempotent. Yeni program
 * EKLEMEZ. 2026_06_18_160000 ana migration'ı tamamlar; onunla çakışmaz.
 */
return new class extends Migration
{
    public function up(): void
    {
        $path = resource_path('data/hk-admission-extra.json');
        if (! Schema::hasTable('programs') || ! is_file($path)) {
            return;
        }

        $map = json_decode(file_get_contents($path), true) ?: [];
        if (empty($map)) {
            return;
        }

        $byMode = [];
        foreach ($map as $slug => $mode) {
            $byMode[$mode][] = $slug;
        }

        foreach ($byMode as $mode => $slugs) {
            foreach (array_chunk($slugs, 1000) as $chunk) {
                DB::table('programs')
                    ->whereIn('slug', $chunk)
                    ->where(fn ($q) => $q->whereNull('admission_mode')->orWhere('admission_mode', ''))
                    ->update(['admission_mode' => $mode]);
            }
        }
    }

    public function down(): void
    {
        // Geri alınmaz: hangi slug'ların ÖNCEDEN boş olduğunu güvenle ayırt edemeyiz;
        // yanlışlıkla başka kaynaktan gelen veriyi sıfırlamamak için no-op.
    }
};
