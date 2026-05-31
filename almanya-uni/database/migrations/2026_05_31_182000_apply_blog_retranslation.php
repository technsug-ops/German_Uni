<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Applies full + du/native re-translations for 13 EN/DE blog posts whose
 * earlier translation was cut off by the translator's source/token cap (they
 * ended mid-sentence). Source TR posts were complete; these are the fixed,
 * full-length localized versions. Data: migrations/data/blog_retranslation.json
 * keyed by "locale::slug". Saved via the model so content_html regenerates.
 *
 * Idempotent: updates only when stored content_md differs from the target.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('posts')) return;

        $file = database_path('migrations/data/blog_retranslation.json');
        if (! file_exists($file)) return;

        $data = json_decode(file_get_contents($file), true);
        if (! is_array($data)) return;

        foreach ($data as $rec) {
            $post = Post::where('slug', $rec['slug'])->where('locale', $rec['locale'])->first();
            if (! $post) continue;
            if ((string) $post->content_md === (string) ($rec['content_md'] ?? '')) continue;

            $post->title            = $rec['title']            ?? $post->title;
            $post->excerpt          = $rec['excerpt']          ?? $post->excerpt;
            $post->meta_title       = $rec['meta_title']       ?? $post->meta_title;
            $post->meta_description = $rec['meta_description'] ?? $post->meta_description;
            $post->content_md       = $rec['content_md']       ?? $post->content_md;
            $post->save(); // regenerates content_html
        }
    }

    public function down(): void
    {
        // No-op.
    }
};
