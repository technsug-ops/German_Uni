<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * 2026_10_09_001200'ün devamı: KSH München'in içerik blokları (3 dil) ve görselleri Hochschule München verisinden
 * üretilmişti — "18.535 öğrenci" (HM), resmî site hm.edu, HM logosu, HM'nin Hochschulkompass linki, kapakta HM binası
 * ("Roter Würfel"). Blokları cümle cümle ayıklamak güvenilir değil → bloklar temizlenir (sayfa temel şablonla render
 * olur, 2026-10-09'da 3 dilde denendi) ve doğru kaynakla yeniden üretilmeye bırakılır. HM logosu/görseli, bu alanları
 * boş olan HM kaydına taşınır. Yalnız kayıt hâlâ beklenen durumdaysa yazılır.
 */
return new class extends Migration
{
    private const KSH = 'katholische-stiftungshochschule-fur-angewandte-wissenschaften-munchen-partner-019ddbbb';

    private const HM = 'hochschule-munchen-university-of-applied-sciences-partner-019ddbba';

    public function up(): void
    {
        if (! Schema::hasTable('universities')) {
            return;
        }

        $ksh = DB::table('universities')->where('slug', self::KSH)->where('website_url', 'https://www.ksh-muenchen.de/')->first();
        if (! $ksh) {
            return;
        }

        $update = [];
        foreach (['content_blocks', 'content_blocks_en', 'content_blocks_de'] as $c) {
            if (is_string($ksh->{$c}) && str_contains($ksh->{$c}, 'hm.edu')) {
                $update[$c] = null;
            }
        }

        $hm = DB::table('universities')->where('slug', self::HM)->first();
        if ($hm && is_string($ksh->logo_url) && str_contains($ksh->logo_url, 'HM%20Logo')) {
            $update['logo_url'] = null;
            $update['image_url'] = null;
            $moved = array_filter([
                'logo_url' => blank($hm->logo_url) ? $ksh->logo_url : null,
                'image_url' => blank($hm->image_url) ? $ksh->image_url : null,
            ]);
            if ($moved) {
                DB::table('universities')->where('id', $hm->id)->update($moved + ['updated_at' => now()]);
            }
        }

        if ($update) {
            DB::table('universities')->where('id', $ksh->id)->update($update + ['updated_at' => now()]);
        }
    }

    public function down(): void
    {
        // Veri düzeltmesi.
    }
};
