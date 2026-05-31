<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Applies completed + full TR/EN/DE content for 9 blog posts whose TR source
 * was cut off at generation (truncated mid-section, or carrying a broken
 * trailing JSON-LD comment). TR tails were completed grounded in the post's
 * own facts (no fabricated figures); EN/DE re-translated full + "du" register.
 * Data: migrations/data/blog_completion.json keyed by "locale::slug".
 *
 * Idempotent: updates only when stored content_md differs. Saved via the model
 * so content_html regenerates.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('posts')) return;
        $file = database_path('migrations/data/blog_completion.json');
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
            $post->save();
        }
    }

    public function down(): void
    {
        // No-op.
    }
};
