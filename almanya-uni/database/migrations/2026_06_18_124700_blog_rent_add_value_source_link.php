<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

/** Kira makalesine: Value Marktdaten canlı ülke-geneli harita kaynağı (link, görsel host etmeden) + kendi haritamız vurgusu. */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'germany-rent-by-city-cheapest-most-expensive-and-what-drives-rent';
        $post = Post::where('slug', $slug)->first();
        if (! $post) return;

        $anchor = '(kaynak: MLP Studentenwohnreport 2025).';
        $addition = "\n\n> 🗺️ **Almanya geneli güncel kira haritası (her Gemeinde, 2026):** Ülke çapında ilçe-bazlı m² kira seviyelerini interaktif görmek için [Value Marktdaten haritası](https://www.value-marktdaten.de/) (eski empirica-systeme; bu yazının veri kaynağı). Güneyde (Bavyera, Baden-Württemberg) koyu kırmızı = pahalı, Doğu Almanya açık ton = ucuz örüntüsü net görülür. *Görsel/veri telifi Value Marktdaten & GeoBasis-DE/BKG'ye aittir.*";

        if (str_contains($post->content_md, $anchor) && ! str_contains($post->content_md, 'value-marktdaten.de')) {
            $md = str_replace($anchor, $anchor . $addition, $post->content_md);
            $post->content_md = $md;
            $post->content_html = Str::markdown($md, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
            $post->save();
        }
    }

    public function down(): void { /* içerik */ }
};
