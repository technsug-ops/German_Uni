<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Post;

/**
 * Blog basliklarindan (title alani) sondaki "(2026)" tarihini ve
 * sondaki "Durust Gercek / The Honest Reality / Die ehrliche Realitat"
 * tagline'ini temizler. Tarih zaten yazi sonunda belirtiliyor.
 *
 * DIKKAT: Tagline yalnizca basligin SONUNDA (yil etiketinden hemen once)
 * ise silinir. Tip/DE yazilarinda oldugu gibi baslikta BASTA gecen
 * "The Honest Reality of..." / "Die ehrliche Realitat des..." ifadeleri
 * gercek basligin parcasidir ve korunur.
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
        $posts = Post::where('title', 'like', '%(2026)%')
            ->orWhere('title', 'like', '%Dürüst Gerçek%')
            ->orWhere('title', 'like', '%ehrliche Realität%')
            ->orWhere('title', 'like', '%Honest Reality%')
            ->get();

        $changed = 0;
        foreach ($posts as $p) {
            $new = $this->clean($p->title);
            if ($new !== $p->title && $new !== '') {
                $p->title = $new;
                $p->save();
                $changed++;
            }
        }
        echo "  temizlenen baslik: {$changed}\n";
    }

    private function clean(string $t): string
    {
        // 1) Sondaki yil etiketini kaldir:  ... (2026)
        $t = preg_replace('/\s*\((?:19|20)\d{2}\)\s*$/u', '', $t);

        // 2) Yalnizca SONDAKI tagline'i kaldir (ayrac + tagline + son)
        $alt = implode('|', array_map(fn ($x) => preg_quote($x, '/'), $this->taglines));
        $t = preg_replace('/\s*[–—\-:|]?\s*(?:' . $alt . ')\s*$/u', '', $t);

        // 3) Bir de yil etiketi tagline'in ardindan gelmisse (nadiren) tekrar sil
        $t = preg_replace('/\s*\((?:19|20)\d{2}\)\s*$/u', '', $t);

        return rtrim($t);
    }

    public function down(): void
    {
        // Geri alinamaz (orijinal basliklar tekrar uretilemez).
    }
};
