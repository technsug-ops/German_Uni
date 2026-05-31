<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Enrichment-B PILOT: applies translated EN/DE content_blocks for 3 cities
 * (Berlin, München, Hamburg) so the localized enrichment is visible on EN/DE.
 * Data: migrations/data/city_blocks_pilot.json keyed by slug.
 *
 * NOTE: full rollout (130 cities + 600 unis) is run via the command
 * `php artisan content:translate-blocks` ON the server (avoids shipping huge
 * JSON in migrations). This migration only carries the pilot set.
 * Idempotent: sets a locale column only when currently empty.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('cities', 'content_blocks_en')) return;
        $file = database_path('migrations/data/city_blocks_pilot.json');
        if (! file_exists($file)) return;
        $data = json_decode(file_get_contents($file), true);
        if (! is_array($data)) return;

        foreach ($data as $slug => $loc) {
            $city = DB::table('cities')->where('slug', $slug)->first();
            if (! $city) continue;
            $update = [];
            if (! empty($loc['en']) && empty($city->content_blocks_en)) $update['content_blocks_en'] = json_encode($loc['en'], JSON_UNESCAPED_UNICODE);
            if (! empty($loc['de']) && empty($city->content_blocks_de)) $update['content_blocks_de'] = json_encode($loc['de'], JSON_UNESCAPED_UNICODE);
            if ($update) DB::table('cities')->where('id', $city->id)->update($update);
        }
    }

    public function down(): void
    {
        // No-op.
    }
};
