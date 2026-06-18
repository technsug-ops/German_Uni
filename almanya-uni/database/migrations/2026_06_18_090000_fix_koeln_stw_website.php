<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Birkaç barınma sağlayıcısının "Resmi Siteye Git" linki yanlış/eski
     * domaine gidiyordu (Köln tamamen ölü: stw-koeln.de → NXDOMAIN; diğerleri
     * rebrand/eski domain). Resmi kaynaklardan doğrulanan güncel kanonik
     * adreslerle düzeltiyoruz. Seed migration prod'da çalıştığı için mevcut
     * kayıtları burada güncelliyoruz.
     */

    // slug => [website, email|null, phone|null]  (null = mevcut değeri koru)
    private array $fixes = [
        'studierendenwerk-koeln'   => ['https://www.kstw.de', 'wohnen@kstw.de', '+49 221 94265-211'],
        'studierendenwerk-hamburg' => ['https://www.stwhh.de', 'info@stwhh.de', null],
        'studentenwerk-frankfurt'  => ['https://www.swffm.de', 'info@swffm.de', null],
        'studentenwerk-wuppertal'  => ['https://www.hochschul-sozialwerk-wuppertal.de', null, null],
        'studentenwerk-augsburg'   => ['https://studierendenwerk-augsburg.de', null, null],
        'neon-wood'                => ['https://neonwood.com', null, null],
    ];

    public function up(): void
    {
        foreach ($this->fixes as $slug => [$website, $email, $phone]) {
            $update = ['website' => $website];
            if ($email !== null) $update['email'] = $email;
            if ($phone !== null) $update['phone'] = $phone;

            DB::table('housing_providers')->where('slug', $slug)->update($update);
        }

        // cities.stw_website provider'dan kopyalanmıştı — STW olanları yeniden senkronla
        $this->syncCityStwWebsites();
    }

    public function down(): void
    {
        $old = [
            'studierendenwerk-koeln'   => ['https://www.stw-koeln.de', 'info@stw-koeln.de', '+49 221 9227-0'],
            'studierendenwerk-hamburg' => ['https://www.stw-hamburg.de', 'post@stw-hamburg.de', null],
            'studentenwerk-frankfurt'  => ['https://www.swf-frankfurt.de', null, null],
            'studentenwerk-wuppertal'  => ['https://www.studierendenwerk-wuppertal.de', null, null],
            'studentenwerk-augsburg'   => ['https://www.studentenwerk-augsburg.de', null, null],
            'neon-wood'                => ['https://www.neon-wood.com', null, null],
        ];

        foreach ($old as $slug => [$website, $email, $phone]) {
            $update = ['website' => $website];
            if ($email !== null) $update['email'] = $email;
            if ($phone !== null) $update['phone'] = $phone;

            DB::table('housing_providers')->where('slug', $slug)->update($update);
        }

        $this->syncCityStwWebsites();
    }

    private function syncCityStwWebsites(): void
    {
        $stwSlugs = [
            'studierendenwerk-koeln', 'studierendenwerk-hamburg',
            'studentenwerk-frankfurt', 'studentenwerk-wuppertal', 'studentenwerk-augsburg',
        ];

        foreach ($stwSlugs as $slug) {
            $p = DB::table('housing_providers')->where('slug', $slug)->first(['name', 'website']);
            if ($p) {
                DB::table('cities')->where('stw_name', $p->name)->update(['stw_website' => $p->website]);
            }
        }
    }
};
