<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Blog JSON-LD'sindeki var-olmayan kapak görseli URL'lerini (applytogerman.com/images/...
 * — AI uydurması, hiçbir yerde yok) gerçek og-default.png ile değiştirir. content_md VE
 * content_html (blog pre-render'dan gösterilir). Böylece structured-data görseli kırık kalmaz.
 */
return new class extends Migration
{
    public function up(): void
    {
        $pattern = '#https?://(?:www\.)?applytogerman\.com/images/[^"\'\s)]+#i';
        $replacement = 'https://applytogerman.com/og-default.png';

        foreach (['content_md', 'content_html'] as $col) {
            DB::table('posts')
                ->where($col, 'like', '%applytogerman.com/images/%')
                ->select('id', $col)->orderBy('id')
                ->each(function ($post) use ($col, $pattern, $replacement) {
                    $new = preg_replace($pattern, $replacement, $post->{$col});
                    if ($new !== null && $new !== $post->{$col}) {
                        DB::table('posts')->where('id', $post->id)->update([$col => $new]);
                    }
                });
        }
    }

    public function down(): void
    {
        // İçerik düzeltmesi — geri alınmaz.
    }
};
