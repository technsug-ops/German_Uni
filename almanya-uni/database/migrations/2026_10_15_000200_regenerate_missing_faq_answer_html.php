<?php

use App\Models\Faq;
use App\Support\MarkdownRenderer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

/**
 * Cevap metni (answer_md) dolu ama answer_html boş olan SSS'ler sayfada boş cevap gösteriyordu.
 * 15.10.2026'da canlıda doğrulandı (ör. /de/faq/visa/how-long-is-a-german-student-visa-valid-de, /en/faq/housing/…).
 * Kaynak: 12–13 Ekim FAQ onarım partileri metni doğrudan yazıp HTML'i yeniden üretmemişti (yerelde 19 kayıt, DE 10 / EN 9).
 * Burada yalnız answer_html, Faq modelinin kayıt kancasıyla aynı render ile yeniden üretilir; metin, slug, id ve grup değişmez.
 * Boş HTML'i olmayan kayıtlara dokunulmaz; tekrar çalıştırma no-op.
 */
return new class extends Migration
{
    public function up(): void
    {
        $fixed = [];
        Faq::query()->withoutGlobalScopes()
            ->whereRaw("TRIM(COALESCE(answer_md, '')) <> ''")
            ->where(fn ($q) => $q->whereNull('answer_html')->orWhere('answer_html', ''))
            ->orderBy('id')
            ->each(function (Faq $faq) use (&$fixed) {
                $faq->answer_html = app(MarkdownRenderer::class)->render((string) $faq->answer_md);
                $faq->has_answer = true;
                $faq->save();
                $fixed[] = "{$faq->id}:{$faq->locale}:{$faq->slug}";
            });

        Log::info('Boş answer_html yeniden üretildi: ' . count($fixed) . ' SSS ' . implode(', ', $fixed));
    }

    public function down(): void
    {
        // Geri alma yok — eksik render tamamlanır.
    }
};
