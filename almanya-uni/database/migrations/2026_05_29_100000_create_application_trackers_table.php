<?php

use Illuminate\Database\Migrations\Migration;

/**
 * No-op migration — stub to satisfy production migration history.
 *
 * Background: I (Claude) originally created this migration in commit f8eb0a9
 * to spin up a fresh application_trackers table, not realising the table
 * already existed from 2026-05-24. The user pointed out the duplicate and
 * I reverted the work in 0b13914 — deleting this file from the repo.
 *
 * Production deploy copied the original file across before the revert was
 * pushed, and deploy doesn't auto-remove files that disappear from the repo.
 * So the orphan file kept attempting Schema::create on every `migrate` run
 * and raising "Table already exists" (the 500 the user reported on 2026-05-29).
 *
 * Re-adding the file as a no-op stub lets the migration runner record it
 * as completed once. From then on it is skipped on subsequent runs.
 * The underlying table, controller, model, and view all came from the
 * 2026_05_24_205615 migration and are completely untouched here.
 */
return new class extends Migration
{
    public function up(): void
    {
        // intentionally empty
    }

    public function down(): void
    {
        // intentionally empty
    }
};
