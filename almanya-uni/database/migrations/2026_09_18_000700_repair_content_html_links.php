<?php

use Illuminate\Database\Migrations\Migration;

/**
 * NO-OP — bu geçiş 2026_09_18_000900_repair_links_chunked_with_log tarafından kapsanıyor.
 *
 * NEDEN BOŞALTILDI: iç link onarımı geliştikçe her adım için ayrı bir "tekrar çalıştır"
 * migration'ı eklendi (000300 → 000900). Hepsi AYNI komutu (content:repair-post-links)
 * çağırıyordu; prod'da dördü peş peşe koşunca deploy'un response-sonrası migrate adımı
 * süre/bellek sınırına takılıp ölüyor, migration kayda geçmediği için her deployda aynı
 * yerde yeniden takılıyordu (000500'den sonrası prod'a hiç uygulanamadı).
 *
 * Çözüm: ara geçişler boşaltıldı; onarımı tek ve bellek-sabit (chunkById) olan 000900 yapıyor.
 * Bu dosyalar silinmiyor — bazı ortamlarda kayıtlı, silmek migrate:status'ü bozar.
 */
return new class extends Migration
{
    public function up(): void
    {
        // kasıtlı olarak boş — bkz. yukarıdaki açıklama
    }

    public function down(): void
    {
        //
    }
};
