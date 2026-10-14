<?php

use App\Models\Faq;
use App\Models\Post;
use App\Support\EmojiCleaner;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

/**
 * SSS ve yazı içeriğindeki emojileri anlamı koruyarak kaldırır (2026-10-14, docs/AI_VISUAL_AUDIT_2026-10-14.md §2).
 * Kurallar App\Support\EmojiCleaner'da: satır başı ✅/⚠️ → madde/paragraf, karışık ✅/❌ blokları → ✓/✗,
 * tablo hücresi ✅/❌ → Evet/Hayır (Yes/No, Ja/Nein), 💡/⚖️ etiketlerinde yalnız emoji kalkar, süs emojileri silinir.
 *
 * Model üzerinden kaydedilir: saving kancası answer_html / content_html'i Markdown'dan yeniden üretir.
 * Slug, URL, çeviri grubu ve yayın durumu değişmez. Emojisiz kayıt atlanır; tekrar çalıştırma no-op.
 * Sonrasında KB yeniden gömülmeli: /admin/ops/kb-embed?source=faq,post (içerik hash'i değişti).
 */
return new class extends Migration
{
    public function up(): void
    {
        $faqs = 0;
        $posts = 0;

        Faq::query()->withoutGlobalScopes()->orderBy('id')->chunkById(200, function ($rows) use (&$faqs) {
            foreach ($rows as $faq) {
                $faq->question = EmojiCleaner::cleanInline($faq->question);
                $faq->answer_md = EmojiCleaner::cleanMarkdown($faq->answer_md, (string) $faq->locale);
                if ($faq->isDirty()) {
                    $faq->save();
                    $faqs++;
                }
            }
        });

        Post::query()->withoutGlobalScopes()->orderBy('id')->chunkById(100, function ($rows) use (&$posts) {
            foreach ($rows as $post) {
                foreach (['title', 'excerpt', 'meta_title', 'meta_description'] as $f) {
                    $post->{$f} = EmojiCleaner::cleanInline($post->{$f});
                }
                $post->content_md = EmojiCleaner::cleanMarkdown($post->content_md, (string) $post->locale);
                if ($post->isDirty()) {
                    $post->save();
                    $posts++;
                }
            }
        });

        Log::info("İçerik emojileri temizlendi: faqs={$faqs}, posts={$posts}");
    }

    public function down(): void
    {
        // Geri alma yok — içerik düzeltmesi.
    }
};
