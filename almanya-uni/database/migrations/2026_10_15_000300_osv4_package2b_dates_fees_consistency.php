<?php

use App\Support\OfficialInfoRepair;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

/**
 * OSV-4 Paket 2b (database/data/osv/2026-10-15-osv4-package2b.json) — son tutarlılık düzeltmeleri
 * (kaynaklar 15.10.2026'da okundu; kontrol tarihi yürürlük tarihi değildir):
 *  - Geçim kanıtı: "01.09.2024'ten beri 992 €" resmî kaynakla doğrulanamadı (BMI ilanları takvim yılı bazlı:
 *    2024 = 934 €, BAnz AT 30.08.2023 B3; 2026 = 992 €, BAnz AT 29.08.2025 B1) → "2026 için 992 €/ay".
 *    Öğrenim vizesi SSS'sindeki "Eylül 2026 itibarıyla / currently (September 2026) / Stand September 2026"
 *    → resmî kaynak + kontrol tarihi + "başlangıç tarihi resmî sayfada yok".
 *  - iDATA SSS (TR/EN/DE): 75 € ulusal vize harcı (§ 46 Abs. 2 Nr. 1 AufenthV) iDATA bedelinden (39,92 € ve/veya
 *    12 €, TL) ayrı; öğrenim/öğrenim yeri arama vizesinde iDATA yok; kamu kaynaklı burs muafiyeti (§ 52 Abs. 5 AufenthV).
 *  - Öğrenci çalışması: 140 iş günü 01.03.2024'ten beri (BGBl. 2023 I Nr. 217, Art. 2 Nr. 6 + Art. 12 Abs. 1);
 *    yarım gün eski kuralda da vardı (BT-Drs. 20/6500 S. 78) → SSS başlığındaki tarih kaynaklı cümleye taşınır.
 *  - Dil kursu: +%10 yasada (AufenthG § 2 Abs. 3 S. 6) → "temsilcilik hesaplıyor" yerine yasa + kaynak.
 * Kayıtlar slug + locale ile bulunur; beklenen metin yoksa atlanır (ezilmez). İkinci çalıştırma no-op.
 * Önizleme: /admin/ops/osv-repair?file=2026-10-15-osv4-package2b
 */
return new class extends Migration
{
    public function up(): void
    {
        $plan = OfficialInfoRepair::apply(OfficialInfoRepair::load(database_path('data/osv/2026-10-15-osv4-package2b.json')));
        Log::info("OSV-4 paket 2b uygulandı\n" . OfficialInfoRepair::describe($plan));
    }

    public function down(): void
    {
        // Geri alma yok — içerik düzeltmesi.
    }
};
