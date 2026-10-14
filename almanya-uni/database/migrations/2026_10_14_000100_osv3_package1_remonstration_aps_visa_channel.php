<?php

use App\Support\OfficialInfoRepair;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

/**
 * OSV-3 Paket 1 (database/data/osv/2026-10-14-osv3-package1.json) — resmî kaynağa dayalı hedefli içerik onarımı:
 *  - Remonstrasyon: 01.07.2025'te kaldırılan usulü güncel seçenek gibi öneren FAQ/yazı parçaları → yeni başvuru veya
 *    ret kararındaki Rechtsbehelfsbelehrung'a göre dava (süre/mahkeme uydurulmaz). Doğru tarihsel anlatımlara dokunulmaz.
 *  - APS: "Türkiye'den başvuranlara zorunlu" iddiası → yalnız Çin/Vietnam/Hindistan belgeleri; belge ülkesi belirleyici.
 *    Yanlış öncüllü APS rehber grubu gövdesi yerinde değişir (yalnız mevcut metin beklenen sha256 ise).
 *    Sözlük tooltip'i kayıtlı content_html içinde düzeltilir.
 *  - Vize: Türkiye'de öğrenim / öğrenim yeri arama vizesi yalnız Konsolosluk Hizmet Portalı; iDATA bedelleri 39,92 € ve/veya 12 € (TL).
 * Kayıtlar slug + locale ile bulunur; id, çeviri grubu, slug, canonical ve hreflang değişmez. Beklenen metin yoksa atlanır
 * (ezilmez). İkinci çalıştırma no-op. Önizleme: /admin/ops/osv-repair
 */
return new class extends Migration
{
    public function up(): void
    {
        $plan = OfficialInfoRepair::apply(OfficialInfoRepair::load(database_path('data/osv/2026-10-14-osv3-package1.json')));
        Log::info("OSV-3 paket 1 uygulandı\n" . OfficialInfoRepair::describe($plan));
    }

    public function down(): void
    {
        // Geri alma yok — içerik düzeltmesi.
    }
};
