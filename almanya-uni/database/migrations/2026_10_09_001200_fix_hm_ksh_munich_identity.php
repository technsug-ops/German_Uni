<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Hochschule München ile Katholische Stiftungshochschule (KSH) München'in kimlik verisi karışmıştı.
 *
 * KSH kaydı (adı ve programları KSH: Soziale Arbeit, Religionspädagogik, Hebammenkunde …) Hochschule München'in
 * Wikidata'sını (Q314089 = "Hochschule für angewandte Wissenschaften München", web hm.edu — Wikidata'dan 2026-10-09
 * doğrulandı), HRK numarasını, web sitesini ve öğrenci sayısını taşıyordu: KSH sayfası ziyaretçiyi hm.edu'ya gönderiyordu.
 * HM'nin kendi kaydında (partner) bu alanlar boştu.
 *
 * → Kimlik alanları HM kaydına taşınır (benzersiz sütunlar önce KSH'den silinir); KSH'ye kendi sitesi
 * (ksh-muenchen.de, 2026-10-09 erişildi) yazılır, öğrenci sayısı bilinmediği için boş kalır. Yalnız kayıtlar hâlâ
 * beklenen durumdaysa yazılır (idempotent).
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

        $ksh = DB::table('universities')->where('slug', self::KSH)->where('wikidata_id', 'Q314089')->first();
        $hm = DB::table('universities')->where('slug', self::HM)->whereNull('wikidata_id')->whereNull('hs_nummer')->first();
        if (! $ksh || ! $hm) {
            return;
        }

        DB::transaction(function () use ($ksh, $hm) {
            DB::table('universities')->where('id', $ksh->id)->update([
                'wikidata_id' => null, 'hs_nummer' => null, 'website_url' => 'https://www.ksh-muenchen.de/', 'student_count' => null,
                'updated_at' => now(),
            ]);
            DB::table('universities')->where('id', $hm->id)->update([
                'wikidata_id' => $ksh->wikidata_id, 'hs_nummer' => $ksh->hs_nummer, 'website_url' => $ksh->website_url,
                'student_count' => $ksh->student_count, 'updated_at' => now(),
            ]);
        });
    }

    public function down(): void
    {
        // Veri düzeltmesi.
    }
};
