<?php

use App\Support\OfficialInfoRepair;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

/**
 * OSV-4 Paket 2 (database/data/osv/2026-10-15-osv4-package2.json) — resmî kaynağa dayalı hedefli içerik onarımı
 * (kaynaklar 15.10.2026'da yeniden okundu; kontrol tarihi yürürlük tarihi değildir):
 *  - Blue Card 2026: 50.700 € / indirimli 45.934,20 € (BMI ilanı BAnz AT 18.12.2025 B3); indirimli eşik § 18g Abs. 1 S. 2
 *    (belirli meslek grupları + diploması ≤ 3 yıllık yeni mezunlar, BA onayıyla); "otomatik Blue Card" kaldırıldı;
 *    eski yıl rakamları (45.300 / 41.041 / 43.759,80 / 48.300 / 56.400 / 44.000) güncel değer olarak kalkar; doğru 2026
 *    değerindeki "2025" etiketleri düzelir; yerleşim izni 27 / 21 ay (§ 18c Abs. 2).
 *  - İş arama: "6 ay, çalışma yasak" anlatan rehber (TR/EN/DE) Chancenkarte (§ 20a) + § 20 (18 ay) olarak yeniden yazılır
 *    (yalnız mevcut metin beklenen sha256 ise; slug/id/çeviri grubu değişmez). Chancenkarte geçim tutarı doğrulanamadı → yazılmadı.
 *  - Öğrenci çalışması: 140 iş günü (§ 16b Abs. 3); "120 gün", "yılda en fazla 20 saat/hafta", "§ 20 Abs. 3 koşulsuz" kalkar.
 *    Doğru tarihsel anlatımlara ("120'den 140'a çıktı") dokunulmaz.
 *  - Geçim kanıtı: öğrenim 2026 için 992 €/ay (BAnz AT 29.08.2025 B1); hazırlık amaçlı olmayan dil kursu Türkiye
 *    temsilcilikleri en az 1.091 €/ay, Sperrkonto VEYA Verpflichtungserklärung (Merkblatt 44F). Sözlük tooltip'i kayıtlı HTML'de.
 *  - iDATA ↔ Konsolosluk Hizmet Portalı ayrımı (öğrenim / öğrenim yeri arama yalnız portal).
 * Kayıtlar slug + locale ile bulunur; beklenen metin yoksa atlanır (ezilmez). İkinci çalıştırma no-op.
 * Önizleme: /admin/ops/osv-repair?file=2026-10-15-osv4-package2
 */
return new class extends Migration
{
    public function up(): void
    {
        $plan = OfficialInfoRepair::apply(OfficialInfoRepair::load(database_path('data/osv/2026-10-15-osv4-package2.json')));
        Log::info("OSV-4 paket 2 uygulandı\n" . OfficialInfoRepair::describe($plan));
    }

    public function down(): void
    {
        // Geri alma yok — içerik düzeltmesi.
    }
};
