<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

/**
 * blog:verify buldu: Sperrkonto (bloke hesap) tutari eski. Guncel resmi (2025/2026):
 * aylik 992€, yillik 11.904€. Bazi yazilarda ~930€/ay ve ~11.900€/yil kullanilmis.
 * GUVENLI desenler: "930€+" (+ garantisi -> maas degil; "§930 BGB" €'suz -> etkilenmez),
 * "11.900" (hep Sperrkonto baglami). content_html yeniden render. Idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $repl = [
            '930€+'  => '992€+',
            '930 €+' => '992 €+',
            '11.900' => '11.904',
        ];
        $posts = Post::where('content_md', 'like', '%930€+%')
            ->orWhere('content_md', 'like', '%930 €+%')
            ->orWhere('content_md', 'like', '%11.900%')
            ->get();

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
        echo "Sperrkonto tutari duzeltildi: {$fixed} post\n";
    }

    public function down(): void
    {
        // Geri alma minimal: 11.904 -> 11.900 (930/992 ayirt edilemez, birakildi).
        Post::where('content_md', 'like', '%11.904%')->get()->each(function ($p) {
            $md = str_replace('11.904', '11.900', (string) $p->content_md);
            if ($md !== $p->content_md) {
                $p->content_md = $md;
                $p->content_html = Str::markdown($md, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
                $p->save();
            }
        });
    }
};
