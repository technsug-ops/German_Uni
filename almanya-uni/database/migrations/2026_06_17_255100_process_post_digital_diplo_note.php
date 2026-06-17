<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

/**
 * Vize süreci post'una resmi online başvuru portalı digital.diplo.de (Auslandsportal)
 * notunu ekler. Idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $post = Post::where('slug', 'germany-student-visa-consulate-process-before-you-go')->first();
        if (! $post) {
            return;
        }
        $md = (string) $post->content_md;
        if (str_contains($md, 'digital.diplo.de')) {
            return; // zaten eklenmiş
        }

        $anchor = 'İletişim bilgileri için [Almanya konsoloslukları iletişim rehberimize](/tr/blog/germany-consulates-turkey-contact-ankara-istanbul-izmir) bak.';
        $add = $anchor . ' Ulusal vize başvuruları için resmi online portal: **[digital.diplo.de](https://digital.diplo.de) (Auslandsportal)** — bazı konsolosluklarda (ör. İstanbul, İzmir) başvuru buradan yapılıp iDATA randevusu portal üzerinden veriliyor (bkz. [İzmir Auslandsportal notu](/tr/blog/izmir-consulate-auslandsportal-digital-diplo-visa-process)).';

        if (! str_contains($md, $anchor)) {
            return; // beklenen metin yok, dokunma
        }
        $md = str_replace($anchor, $add, $md);

        $post->content_md = $md;
        $post->content_html = Str::markdown($md, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
        $post->save();
    }

    public function down(): void
    {
        // İçerik notu — geri alınmaz.
    }
};
