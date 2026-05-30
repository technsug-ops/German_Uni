<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Bulk-populate University.image_url for ~220 universities whose slug embeds a
 * Wikidata Q-id (e.g. "universitat-hamburg-q156725"). The mapping was produced
 * by app/Console/Commands/FetchUniversityImagesFromWikidata.php pulling P18
 * (image) from the Wikidata REST API on the local DB; this migration applies
 * the same mapping to production so we don't have to run network-bound jobs
 * on the KAS server.
 *
 * Only updates rows that currently have a NULL/empty image_url — won't trample
 * admin-curated images. JSON source: database/data/wikidata_uni_images_2026_05_30.json
 */
return new class extends Migration
{
    public function up(): void
    {
        $jsonPath = database_path('data/wikidata_uni_images_2026_05_30.json');
        if (! is_file($jsonPath)) {
            // Don't fail the migrator if the data file isn't bundled in this deploy —
            // just skip. Re-deploy with the file present to apply.
            return;
        }
        $map = json_decode(file_get_contents($jsonPath), true) ?? [];
        $applied = 0;
        foreach ($map as $slug => $url) {
            $changed = DB::table('universities')
                ->where('slug', $slug)
                ->where(fn ($q) => $q->whereNull('image_url')->orWhere('image_url', ''))
                ->update(['image_url' => $url]);
            $applied += $changed;
        }
        // Migrations don't echo, but this is harmless and shows up in artisan output.
        if (function_exists('logger')) {
            logger()->info("[wikidata_uni_images] applied=$applied of " . count($map));
        }
    }

    public function down(): void
    {
        // No reverse — we never want to wipe images blindly. Admins can manage
        // image_url through the Filament panel if a rollback is ever needed.
    }
};
