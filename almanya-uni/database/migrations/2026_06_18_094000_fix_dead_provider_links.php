<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Açılmayan (ölü) sağlayıcı linkleri. Kullanıcı tüm 6'sının açılmadığını teyit etti.
 *  - 4'ü doğru işletmeci kuruma yönlendirildi (resmi kaynakla doğrulanan operatör):
 *      Mönchengladbach → Studierendenwerk Düsseldorf (stw-d.de)
 *      Gelsenkirchen   → AKAFÖ (akafoe.de)
 *      Wiesbaden       → Studierendenwerk Frankfurt (swffm.de)
 *      YOUNIQ          → kanonik youniq-living.com
 *  - 2'si çalışan/doğrulanmış link bulunamadığı için gizlendi (is_active=0):
 *      KKIK & Home4Students (home4students.de — SSL süresi dolmuş, kurum eşleşmiyor)
 *      OXXO Living (oxxo.de — öğrenci yurdu işletmecisi olduğu doğrulanamadı)
 */
return new class extends Migration
{
    private array $relink = [
        'studentenwerk-moenchengladbach' => 'https://www.stw-d.de',
        'studentenwerk-westfalen'        => 'https://www.akafoe.de',
        'studierendenwerk-wiesbaden'     => 'https://www.swffm.de',
        'youniq'                         => 'https://www.youniq-living.com',
    ];

    private array $stwSlugs = [
        'studentenwerk-moenchengladbach', 'studentenwerk-westfalen', 'studierendenwerk-wiesbaden',
    ];

    private array $hide = ['kkik-home4students', 'oxxo-living'];

    public function up(): void
    {
        if (! Schema::hasTable('housing_providers')) {
            return;
        }

        foreach ($this->relink as $slug => $website) {
            DB::table('housing_providers')->where('slug', $slug)->update(['website' => $website]);
        }

        $this->syncCityStwWebsites();

        DB::table('housing_providers')->whereIn('slug', $this->hide)->update(['is_active' => 0]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('housing_providers')) {
            return;
        }

        $old = [
            'studentenwerk-moenchengladbach' => 'https://www.stw-niederrhein.de',
            'studentenwerk-westfalen'        => 'https://www.stw-westfalen.de',
            'studierendenwerk-wiesbaden'     => 'https://www.studentenwerk-wiesbaden.de',
            'youniq'                         => 'https://www.youniq-students.com',
        ];

        foreach ($old as $slug => $website) {
            DB::table('housing_providers')->where('slug', $slug)->update(['website' => $website]);
        }

        $this->syncCityStwWebsites();

        DB::table('housing_providers')->whereIn('slug', $this->hide)->update(['is_active' => 1]);
    }

    private function syncCityStwWebsites(): void
    {
        foreach ($this->stwSlugs as $slug) {
            $p = DB::table('housing_providers')->where('slug', $slug)->first(['name', 'website']);
            if ($p) {
                DB::table('cities')->where('stw_name', $p->name)->update(['stw_website' => $p->website]);
            }
        }
    }
};
