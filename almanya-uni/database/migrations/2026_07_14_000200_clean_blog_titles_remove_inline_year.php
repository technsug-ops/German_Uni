<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Post;

/**
 * Basligin ORTASINDA gecen yil etiketini de temizler:
 *   "Almanya'da Tip Okumak (2026): NC, Dil..." -> "Almanya'da Tip Okumak: NC, Dil..."
 * (2026_07_14_000100 yalnizca SONDAKI yili siliyordu.)
 */
return new class extends Migration
{
    public function up(): void
    {
        $posts = Post::where('title', 'like', '%(2026)%')
            ->orWhere('title', 'like', '%(2025)%')
            ->orWhere('title', 'like', '%(2027)%')
            ->get();

        $changed = 0;
        foreach ($posts as $p) {
            // "(YYYY)" etiketini kaldir
            $t = preg_replace('/\s*\((?:19|20)\d{2}\)/u', '', $p->title);
            // " :" -> ":" ve cift bosluklari topla
            $t = preg_replace('/\s+:/u', ':', $t);
            $t = preg_replace('/\s{2,}/u', ' ', $t);
            $t = trim($t);

            if ($t !== $p->title && $t !== '') {
                $p->title = $t;
                $p->save();
                $changed++;
            }
        }
        echo "  temizlenen baslik (inline): {$changed}\n";
    }

    public function down(): void
    {
        // Geri alinamaz.
    }
};
