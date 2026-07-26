<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

/**
 * blog:verify (Gemini + Google) fakt-denetimi buldu: Blue Card maaş eşikleri 2025 rakamlarıyla.
 * 2026 resmi güncelleme: genel eşik 48.300 -> 50.700€, darboğaz/yeni-mezun 43.760 -> 45.934€.
 * Spesifik resmi rakamlar (baska baglamda gecmez) -> guvenli strtr; content_html yeniden render.
 * Tum locale'lerde (TR/DE/EN) rakamlar ayni -> tek gecis hepsini duzeltir. Idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $repl = [
            '48.300' => '50.700', '48,300' => '50,700',   // Blue Card genel esik
            '43.760' => '45.934', '43,760' => '45,934',   // Blue Card darbogaz/yeni-mezun
        ];
        $needles = array_keys($repl);

        $posts = Post::where(function ($q) use ($needles) {
            foreach ($needles as $n) $q->orWhere('content_md', 'like', '%' . $n . '%');
        })->get();

        $fixed = 0;
        foreach ($posts as $p) {
            $md = strtr((string) $p->content_md, $repl);
            if ($md !== $p->content_md) {
                $p->content_md = $md;
                $p->content_html = Str::markdown($md, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
                $p->save();
                $fixed++;
            }
        }
        echo "Blue Card esik duzeltildi: {$fixed} post\n";
    }

    public function down(): void
    {
        // Geri alma: yeni rakamlari eskiye cevir (idempotent olmayan; yalniz gerekirse).
        $repl = [
            '50.700' => '48.300', '50,700' => '48,300',
            '45.934' => '43.760', '45,934' => '43,760',
        ];
        $needles = array_keys($repl);
        Post::where(function ($q) use ($needles) {
            foreach ($needles as $n) $q->orWhere('content_md', 'like', '%' . $n . '%');
        })->get()->each(function ($p) use ($repl) {
            $md = strtr((string) $p->content_md, $repl);
            if ($md !== $p->content_md) {
                $p->content_md = $md;
                $p->content_html = Str::markdown($md, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
                $p->save();
            }
        });
    }
};
