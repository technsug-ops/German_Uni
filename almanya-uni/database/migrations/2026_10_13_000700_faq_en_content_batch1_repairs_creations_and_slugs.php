<?php

use App\Support\FaqEnglishContentBatch;
use Illuminate\Database\Migrations\Migration;

/**
 * EN FAQ içerik paketi — Batch 1 (database/data/faq-en/2026-10-13-batch1.json):
 *  - 17 mevcut EN kaydın (soru/cevap Türkçe kalmış) onarımı; kayıt id ve çeviri grubu korunur,
 *  - 10 TR-only gruba yayında EN kardeş (TR kaydın grubu/konusu),
 *  - etkilenen tüm grupların slug'ları İngilizce kurala (TR taban / EN -en / DE -de, ≤ 80) çekilir;
 *    eski slug'lar dile özgü 301, eski yönlendirmelerin hedefi son adrese güncellenir (zincir yok),
 *    kb_chunks.url yeniden yazılır.
 * Ön doğrulama: beklenen eski metinle eşleşmeyen kayıt varsa hiçbir şey yazılmaz. Rerun no-op.
 * Önizleme (yazmaz): /admin/ops/faq-en-batch
 */
return new class extends Migration
{
    public function up(): void
    {
        $plan = FaqEnglishContentBatch::plan(FaqEnglishContentBatch::load(database_path('data/faq-en/2026-10-13-batch1.json')));
        FaqEnglishContentBatch::apply($plan); // sorun varsa RuntimeException, hiçbir şey yazılmaz
    }

    public function down(): void
    {
        // Geri alma yok — içerik ve URL değişikliği kalıcı, yönlendirmeler korunur.
    }
};
