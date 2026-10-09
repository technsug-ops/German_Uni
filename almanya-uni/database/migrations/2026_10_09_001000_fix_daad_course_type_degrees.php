<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * DAAD courseType → derece eşlemesi düzeltmesi (DAAD API ile 2026-10-09 doğrulandı).
 *
 *  - courseType 6 (ve partner'ın aynı tipi) 'studienkolleg' sayılıyordu; gerçekte yaz/kış okulu, kısa kurs ve değişim
 *    dönemi ("Summer School", "Campus Deutsch", "Exchange Semester"). 130 aktif kaydın hiçbiri gerçek Studienkolleg ya da
 *    hazırlık programı değil (gerçek Studienkolleg'ler `studienkollegs` tablosunda). Studienkolleg filtresini kullanan
 *    öğrenci bunları görüyordu → 'other'.
 *  - courseType 4 = doktora / graduate school (BAGSS, Bonn International Graduate School …) 'other' sayılıyordu → 'phd'.
 *    DAAD kimlikleri API'den (degree[]=4) alındı; partner kaydı DAAD detay URL'siyle eşlenir.
 * İçe aktarmalar da düzeltildi (DaadImport, PartnerImporter). Idempotent.
 */
return new class extends Migration
{
    /** DAAD API degree[]=4 (courseType 4) kurs kimlikleri. */
    private const DOCTORAL_DAAD_IDS = [
        '11081', '9633', '9627', '11223', '6201', '10427', '4794', '7707', '10355', '5257', '5216', '6287', '5483',
        '5418', '9582', '5269', '5224', '5221', '5249', '11107', '5325', '5305', '11162', '5258', '7119',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('programs')) {
            return;
        }

        DB::table('programs')->where('degree', 'studienkolleg')->update(['degree' => 'other', 'updated_at' => now()]);

        DB::table('programs')->where('source', 'daad')->whereIn('source_id', self::DOCTORAL_DAAD_IDS)->where('degree', 'other')
            ->update(['degree' => 'phd', 'updated_at' => now()]);

        foreach (self::DOCTORAL_DAAD_IDS as $id) {
            DB::table('programs')->where('source', 'partner')->where('degree', 'other')
                ->where(fn ($q) => $q->where('source_url', 'like', "%/detail/{$id}/%")->orWhere('source_url', 'like', "%/detail/{$id}"))
                ->update(['degree' => 'phd', 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        // Veri düzeltmesi.
    }
};
