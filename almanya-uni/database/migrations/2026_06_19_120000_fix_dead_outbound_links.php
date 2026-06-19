<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * links:check-external denetçisinin (19.06.2026) bulduğu ölü/erişilemez dış
 * linkleri resmî güncel adreslerle düzeltir. Slug/id-keyed, idempotent.
 *
 *  - Studierendenwerk'ler domain değiştirmiş (Studentenwerk→Studierendenwerk):
 *      München  stw-muenchen.de  → studierendenwerk-muenchen-oberbayern.de
 *      Stuttgart stud-stg.de     → studierendenwerk-stuttgart.de
 *      Dortmund  stw-dortmund.de → stwdo.de
 *  - YouniQ artık Yugo (youniq-living.com → yugo.com).
 *  - VHS portalı volkshochschule.de.
 *  - Lingoda/AOK/Care Concept eski derin linkleri 404 → çalışan resmî sayfa.
 *  - Deutsche Bank Temmuz 2022'den beri Sperrkonto SUNMUYOR → listeden gizle.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── housing_providers (slug) — Studierendenwerk domain güncellemeleri + YouniQ→Yugo ──
        $housing = [
            'studierendenwerk-muenchen'   => 'https://www.studierendenwerk-muenchen-oberbayern.de',
            'studierendenwerk-stuttgart'  => 'https://www.studierendenwerk-stuttgart.de',
            'studentenwerk-dortmund'      => 'https://www.stwdo.de',
            'youniq'                      => 'https://yugo.com/en-us/global/germany',
        ];
        foreach ($housing as $slug => $url) {
            DB::table('housing_providers')->where('slug', $slug)->update(['website' => $url]);
        }
        // Dortmund resmî adı artık "Studierendenwerk Dortmund".
        DB::table('housing_providers')->where('slug', 'studentenwerk-dortmund')
            ->where('name', 'Studentenwerk Dortmund')
            ->update(['name' => 'Studierendenwerk Dortmund']);

        // ── cities.stw_website senkronu (varsa eski domainleri taşı) ──
        if (Schema::hasTable('cities') && Schema::hasColumn('cities', 'stw_website')) {
            $domainMap = [
                'stw-muenchen.de'  => 'https://www.studierendenwerk-muenchen-oberbayern.de',
                'stud-stg.de'      => 'https://www.studierendenwerk-stuttgart.de',
                'stw-dortmund.de'  => 'https://www.stwdo.de',
            ];
            foreach ($domainMap as $old => $new) {
                DB::table('cities')->where('stw_website', 'like', '%' . $old . '%')
                    ->update(['stw_website' => $new]);
            }
        }

        // ── language_courses (id) — Lingoda + VHS ──
        DB::table('language_courses')->where('id', 6)->update(['website' => 'https://www.lingoda.com/en/german/']);
        DB::table('language_courses')->where('id', 4)->update(['website' => 'https://www.volkshochschule.de/']);

        // ── health_insurance_providers (slug) — AOK + Care Concept ──
        DB::table('health_insurance_providers')->where('slug', 'aok')
            ->update(['website_url' => 'https://www.aok.de/pk/leistungen/studium-beruf/information-for-international-students/']);
        DB::table('health_insurance_providers')->where('slug', 'care-concept')
            ->update(['website_url' => 'https://www.care-concept.de/index_eng.htm?navilang=eng']);

        // ── blocked_account_providers — Deutsche Bank artık Sperrkonto sunmuyor → gizle ──
        $db = ['website_url' => 'https://www.deutsche-bank.de/pk/konto-und-karte/bankgeschaefte-erledigen/internationale-studenten1.html'];
        if (Schema::hasColumn('blocked_account_providers', 'is_published')) {
            $db['is_published'] = 0;
        }
        DB::table('blocked_account_providers')->where('slug', 'deutsche-bank')->update($db);
    }

    public function down(): void
    {
        // İçerik düzeltmesi — geri alınmaz (eski linkler ölüydü).
    }
};
