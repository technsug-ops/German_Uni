<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Converts 12 German (locale=de) blog posts from the formal "Sie" register to
 * the consistent informal "du" register (native, SEO-aware) — matching the
 * site-wide register decision (study-in-germany.com/de uses "du") and
 * doc/I18N-STYLE-GUIDE.md.
 *
 * The rewritten content (title, excerpt, meta_title, meta_description,
 * content_md) lives in database/migrations/data/blog_du_conversion.json,
 * keyed by slug. We save via the Post model so content_html is regenerated
 * (MarkdownRenderer + BlogAutoLinker) automatically.
 *
 * Idempotent & non-destructive: a post is updated only when its current
 * content_md DIFFERS from the target — so a re-run, or an environment already
 * carrying the du version, is a no-op, and a later human edit is never blindly
 * reverted on re-run (the migration runs once anyway).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('posts')) return;

        $file = database_path('migrations/data/blog_du_conversion.json');
        if (! file_exists($file)) return; // data absent on this environment — skip

        $data = json_decode(file_get_contents($file), true);
        if (! is_array($data)) return;

        foreach ($data as $slug => $fields) {
            // Defensive: a single bad record must never throw and abort the whole
            // `migrate` run (that would block later SCHEMA migrations → prod 500).
            try {
                $post = Post::where('slug', $slug)->where('locale', 'de')->first();
                if (! $post) continue;

                // Only rewrite if the stored content still differs (i.e. still formal).
                if ((string) $post->content_md === (string) ($fields['content_md'] ?? '')) {
                    continue;
                }

                $post->title            = $fields['title']            ?? $post->title;
                $post->excerpt          = $fields['excerpt']          ?? $post->excerpt;
                $post->meta_title       = $fields['meta_title']       ?? $post->meta_title;
                $post->meta_description = $fields['meta_description'] ?? $post->meta_description;
                $post->content_md       = $fields['content_md']       ?? $post->content_md;
                $post->save(); // regenerates content_html via model boot
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("convert_blog_posts_to_du skip {$slug}: " . $e->getMessage());
            }
        }
    }

    public function down(): void
    {
        // No-op: the formal-Sie originals are intentionally not restored.
    }
};
