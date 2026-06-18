<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

/** Kira coğrafyası yazısına interaktif harita CTA'sı ekle. */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'germany-rent-by-city-cheapest-most-expensive-and-what-drives-rent';
        $post = Post::where('slug', $slug)->first();
        if (! $post) return;

        $cta = "\n\n👉 **[İnteraktif harita: Almanya öğrenci kira haritası](/tr/student-rent-map)** — 38 üniversite şehrini kiraya göre renkli haritada karşılaştır (kaynak: MLP Studentenwohnreport 2025).";
        $anchor = 'güncel veriyle gösterir.';
        if (str_contains($post->content_md, $anchor) && ! str_contains($post->content_md, '/tr/student-rent-map')) {
            $md = str_replace($anchor, $anchor . $cta, $post->content_md);
            $post->content_md = $md;
            $post->content_html = Str::markdown($md, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
            $post->save();
        }
    }

    public function down(): void { /* içerik — geri alma yok */ }
};
