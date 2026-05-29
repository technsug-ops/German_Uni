<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Seeds EN + DE translations of the uni-assist (post #77) blog post.
 *
 * Source is the existing Turkish post; translations were generated locally
 * via Gemini and stored in post77_translations_data.json. This migration
 * inserts both translation rows into production under the same
 * translation_group_id as the TR source — so the existing
 * Post-translation accessor / FAQ locale redirect logic picks them up.
 *
 * Idempotent: keyed by translation_group_id + locale.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('posts')) return;

        $dataFile = database_path('migrations/post77_translations_data.json');
        if (! file_exists($dataFile)) {
            // Data file missing on this environment — skip (e.g. dev clones)
            return;
        }

        $rows = json_decode(file_get_contents($dataFile), true);
        if (! is_array($rows)) return;

        foreach ($rows as $row) {
            $groupId = $row['translation_group_id'] ?? null;
            $locale  = $row['locale']               ?? null;
            if (! $groupId || ! $locale) continue;

            // Idempotency: already-seeded sibling
            $exists = DB::table('posts')
                ->where('translation_group_id', $groupId)
                ->where('locale', $locale)
                ->exists();
            if ($exists) continue;

            // Ensure slug doesn't collide with another post (rare on prod)
            $slug = $row['slug'];
            $base = $slug;
            $n = 1;
            while (DB::table('posts')->where('slug', $slug)->exists()) {
                $slug = $base . '-' . (++$n);
            }

            DB::table('posts')->insert([
                'translation_group_id' => $groupId,
                'locale'               => $locale,
                'slug'                 => $slug,
                'title'                => $row['title'],
                'excerpt'              => $row['excerpt'],
                'content_md'           => $row['content_md'],
                'reading_minutes'      => $row['reading_minutes'] ?? null,
                'user_id'              => $row['user_id']  ?? null,
                'category_id'          => $row['category_id'] ?? null,
                'is_published'         => true,
                'published_at'         => now(),
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Non-destructive: keep translations on rollback rather than deleting
        // user-facing content.
    }
};
