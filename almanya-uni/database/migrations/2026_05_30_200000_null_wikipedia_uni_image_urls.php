<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Partner import seeded ~393 universities with auto-picked Wikipedia commons
 * URLs that turned out to be generic city landmarks, random portraits, or
 * unrelated photos (e.g. Brandenburg gate reused across many Berlin schools,
 * Reinhard Klimmt portrait on Saarland uni, ASIMO on Bilişim, …).
 *
 * We replaced render with a 3-layer fallback (own > city-pool > gradient),
 * so the right move now is to NULL the auto-picked URLs. Admins can re-attach
 * a properly curated photo per row later. URLs that aren't Wikipedia (likely
 * already manually set) are left alone.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('universities')
            ->where('image_url', 'like', '%upload.wikimedia.org%')
            ->update(['image_url' => null]);
    }

    public function down(): void {}
};
