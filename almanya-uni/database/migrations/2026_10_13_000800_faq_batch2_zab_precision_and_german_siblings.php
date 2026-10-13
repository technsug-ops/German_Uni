<?php

use App\Support\FaqEnglishContentBatch;
use Illuminate\Database\Migrations\Migration;

/**
 * FAQ içerik paketi — Batch 2 (database/data/faq-en/2026-10-13-batch2.json):
 *  - ZAB Zeugnisbewertung cümleleri (2 grup × TR/EN/DE): sürenin başlangıç koşulu (tüm belgeler + ücret ZAB'da),
 *    ödeme süresi, Mavi Kart / § 81a istisnası, Zeugnisbewertung ≠ mesleki tanıma. Soru ve slug değişmez.
 *  - Batch 1'de EN'i onarılan 12 grubun DE kardeşleri (soru/cevap Türkçe kalmıştı) Almanca içerik alır;
 *    kayıt id, grup ve slug (zaten İngilizce taban + -de) korunur, URL değişmez.
 * Uyuşmayan kayıt üzerine yazılmaz (atlanır, raporlanır). Rerun no-op. Önizleme: /admin/ops/faq-en-batch?file=2026-10-13-batch2
 */
return new class extends Migration
{
    public function up(): void
    {
        $plan = FaqEnglishContentBatch::plan(FaqEnglishContentBatch::load(database_path('data/faq-en/2026-10-13-batch2.json')));
        FaqEnglishContentBatch::apply($plan);
    }

    public function down(): void
    {
        // Geri alma yok — içerik düzeltmesi.
    }
};
