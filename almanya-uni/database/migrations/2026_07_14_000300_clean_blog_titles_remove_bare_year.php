<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Post;

/**
 * Basliktaki parantezsiz "guncel yil" tarih etiketini de temizler:
 *   "Almanya Ogrenci Vizesi 2026: Adimlar"   -> "Almanya Ogrenci Vizesi: Adimlar"
 *   "2026 Guide to Tax..."                   -> "Guide to Tax..."
 *   "TestDaF veya DSH: 2026'da Dogru Sinav"  -> "TestDaF veya DSH: Dogru Sinav"  (TR eki dahil)
 *
 * DIKKAT: Yalnizca 2025/2026/2027 hedeflenir. Anlamli yil referanslari
 * ("2020 Reformu / the 2020 Reform / Reform 2020" gibi) KORUNUR.
 */
return new class extends Migration
{
    public function up(): void
    {
        $posts = Post::where('title', 'like', '%2025%')
            ->orWhere('title', 'like', '%2026%')
            ->orWhere('title', 'like', '%2027%')
            ->get();

        $changed = 0;
        foreach ($posts as $p) {
            $t = $p->title;

            // 2025/2026/2027 + olasi TR eki ('da, 'ya, 'li ...) kaldir
            $t = preg_replace("/\\b202[567]([’']\\p{L}+)?/u", '', $t);

            // Bosta kalan ayraclari duzelt
            $t = preg_replace('/\s+([:;,?!])/u', '$1', $t);   // " :" -> ":"
            $t = preg_replace('/\s{2,}/u', ' ', $t);          // cift bosluk
            $t = preg_replace('/^[\s:;,\-–—|]+/u', '', $t);   // basta ayrac
            $t = preg_replace('/[\s:;,\-–—|]+$/u', '', $t);   // sonda ayrac
            // ": -" / ":—" gibi ardisik ayraclari sadelestir
            $t = preg_replace('/([:;,])\s*[–—\-|]\s*/u', '$1 ', $t);
            $t = trim($t);

            if ($t !== $p->title && $t !== '') {
                $p->title = $t;
                $p->save();
                $changed++;
            }
        }
        echo "  temizlenen baslik (bare year): {$changed}\n";
    }

    public function down(): void
    {
        // Geri alinamaz.
    }
};
