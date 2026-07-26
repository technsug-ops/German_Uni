<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Post;

/**
 * meta_title (SEO / <title> tag / Google sonuc basligi) alanini da
 * title ile ayni mantikla temizler:
 *   - Parantezli (2026) hedge
 *   - Cumle ici ve parantezsiz 2025/2026/2027 (TR eki dahil)
 *   - Sondaki 'Durust Gercek / Honest Reality / ehrliche Realitat' tagline
 * Anlamli yil referanslari (2020 Reformu vb.) KORUNUR (sadece 2025-27 hedeflenir).
 */
return new class extends Migration
{
    private array $taglines = [
        'Dürüst Gerçek',
        'Die ehrliche Realität',
        'Die ehrliche Wahrheit',
        'The Honest Reality',
        'The Honest Truth',
        'Honest Reality',
    ];

    public function up(): void
    {
        $posts = Post::whereNotNull('meta_title')
            ->where(function ($q) {
                $q->where('meta_title', 'like', '%2025%')
                    ->orWhere('meta_title', 'like', '%2026%')
                    ->orWhere('meta_title', 'like', '%2027%')
                    ->orWhere('meta_title', 'like', '%Honest Reality%')
                    ->orWhere('meta_title', 'like', '%ehrliche Realität%')
                    ->orWhere('meta_title', 'like', '%Dürüst Gerçek%');
            })
            ->get();

        $changed = 0;
        foreach ($posts as $p) {
            $new = $this->clean($p->meta_title);
            if ($new !== $p->meta_title && $new !== '') {
                $p->meta_title = $new;
                $p->save();
                $changed++;
            }
        }
        echo "  temizlenen meta_title: {$changed}\n";
    }

    private function clean(string $t): string
    {
        // 2025/2026/2027 + olasi TR eki ('da, 'ya ...) kaldir (hedge + duz yil + cumle ici)
        $t = preg_replace("/\\b202[567]([’']\\p{L}+)?/u", '', $t);

        // Bosalan parantez ciftlerini temizle:  ()  ( )
        $t = preg_replace('/\(\s*\)/u', '', $t);

        // Sondaki tagline'i kaldir
        $alt = implode('|', array_map(fn ($x) => preg_quote($x, '/'), $this->taglines));
        $t = preg_replace('/\s*[–—\-:|]?\s*(?:' . $alt . ')\s*$/u', '', $t);

        // Ayrac / bosluk temizligi
        $t = preg_replace('/\s+([:;,?!])/u', '$1', $t);
        $t = preg_replace('/\s{2,}/u', ' ', $t);
        $t = preg_replace('/^[\s:;,\-–—|]+/u', '', $t);
        $t = preg_replace('/[\s:;,\-–—|]+$/u', '', $t);
        $t = preg_replace('/([:;,])\s*[–—\-|]\s*/u', '$1 ', $t);

        return trim($t);
    }

    public function down(): void
    {
        // Geri alinamaz.
    }
};
