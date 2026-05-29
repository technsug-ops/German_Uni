<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Some posts have HTML entities like &quot; / &amp; / &#039; stored in
 * the excerpt column. Blade's `{{ }}` escapes them again on display, so
 * the user sees the literal "&quot;" string instead of a quote mark.
 *
 * This migration html_entity_decode()s the excerpt column on rows where
 * the encoded form is present. Idempotent — won't double-decode rows
 * that have already been cleaned.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('posts')) return;

        $affected = DB::table('posts')
            ->where(function ($q) {
                $q->where('excerpt', 'like', '%&quot;%')
                  ->orWhere('excerpt', 'like', '%&amp;%')
                  ->orWhere('excerpt', 'like', '%&#039;%')
                  ->orWhere('excerpt', 'like', '%&apos;%')
                  ->orWhere('excerpt', 'like', '%&lt;%')
                  ->orWhere('excerpt', 'like', '%&gt;%')
                  ->orWhere('excerpt', 'like', '%&#x27;%');
            })
            ->get(['id', 'excerpt']);

        foreach ($affected as $row) {
            $clean = html_entity_decode((string) $row->excerpt, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if ($clean !== $row->excerpt) {
                DB::table('posts')->where('id', $row->id)->update(['excerpt' => $clean]);
            }
        }
    }

    public function down(): void
    {
        // No-op — re-encoding would defeat the purpose.
    }
};
