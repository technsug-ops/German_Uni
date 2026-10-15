<?php

use App\Support\OfficialInfoRepair;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

/**
 * OSV-3 Paket 1b (database/data/osv/2026-10-14-osv3-package1b-check-date.json): öğrenim vizesi notunda
 * kontrol tarihi ile yürürlük tarihini ayırır. "Güncelleme (14.10.2026): … artık yalnızca portal" ifadesi
 * 14.10.2026'yı başlangıç tarihi gibi okutuyordu; artık "Güncel durum (kontrol tarihi: 14.10.2026)" ve
 * "kuralın başlangıç tarihi resmî sayfada belirtilmiyor". Tarih uydurulmadı. 30 kayıt (TR/EN/DE × 10).
 * Kayıtlar slug + locale ile bulunur; id, slug, çeviri grubu değişmez. Beklenen metin yoksa atlanır; tekrar no-op.
 * Önizleme: /admin/ops/osv-repair?file=2026-10-14-osv3-package1b-check-date
 */
return new class extends Migration
{
    public function up(): void
    {
        $plan = OfficialInfoRepair::apply(OfficialInfoRepair::load(database_path('data/osv/2026-10-14-osv3-package1b-check-date.json')));
        Log::info("OSV-3 paket 1b uygulandı\n" . OfficialInfoRepair::describe($plan));
    }

    public function down(): void
    {
        // Geri alma yok — içerik düzeltmesi.
    }
};
