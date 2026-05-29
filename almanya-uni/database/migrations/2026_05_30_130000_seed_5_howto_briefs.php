<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Seeds the 5 new howto briefs (Almanca / Termin / Vize görüşmesi /
 * BAföG / Konut) that were created locally via the artisan command
 * `content:seed-howto-briefs`.
 *
 * Data ships in a sibling JSON file. Idempotent (slug-keyed).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('content_briefs')) return;

        $dataFile = database_path('migrations/howto_briefs_2026_05_29_data.json');
        if (! file_exists($dataFile)) return;

        $rows = json_decode(file_get_contents($dataFile), true);
        if (! is_array($rows)) return;

        foreach ($rows as $row) {
            $slug = $row['slug'] ?? null;
            if (! $slug) continue;

            // Idempotent: skip if slug already there
            if (DB::table('content_briefs')->where('slug', $slug)->exists()) {
                continue;
            }

            DB::table('content_briefs')->insert([
                'title'              => $row['title']              ?? '',
                'slug'               => $slug,
                'audience'           => $row['audience']           ?? 'aday_ogrenci',
                'topic'              => $row['topic']              ?? null,
                'primary_keyword'    => $row['primary_keyword']    ?? '',
                'secondary_keywords' => is_array($row['secondary_keywords'] ?? null)
                    ? json_encode($row['secondary_keywords'], JSON_UNESCAPED_UNICODE)
                    : ($row['secondary_keywords'] ?? '[]'),
                'pain_point'         => $row['pain_point']         ?? '',
                'source_questions'   => is_array($row['source_questions'] ?? null)
                    ? json_encode($row['source_questions'], JSON_UNESCAPED_UNICODE)
                    : ($row['source_questions'] ?? '[]'),
                'target_word_count'  => $row['target_word_count']  ?? 1500,
                'brand_tone'         => $row['brand_tone']         ?? 'casual',
                'notes'              => $row['notes']              ?? '',
                'status'             => $row['status']             ?? 'draft',
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Non-destructive: keep seeded briefs even on rollback.
    }
};
