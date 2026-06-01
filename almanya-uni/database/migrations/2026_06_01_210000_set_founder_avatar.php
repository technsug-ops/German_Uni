<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Kurucu (Halil Yaprakli, id=1 / role_label='Kurucu') avatar fotoğrafını ayarlar.
 * Görsel repo'da: public/images/team/halil-yaprakli.png (kendi sunucumuzda barınır).
 * /about teaser + /ekip aynı User kaydından beslendiği için TEK noktadan görünür.
 */
return new class extends Migration
{
    private const PATH = '/images/team/halil-yaprakli.png';

    public function up(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'avatar_url')) {
            return;
        }
        DB::table('users')
            ->where(fn ($q) => $q->where('id', 1)
                ->orWhere('role_label', 'like', '%urucu%'))
            ->update(['avatar_url' => self::PATH]);
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'avatar_url')) {
            DB::table('users')->where('avatar_url', self::PATH)->update(['avatar_url' => null]);
        }
    }
};
