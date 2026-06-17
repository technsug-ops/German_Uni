<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Kurucu (Halil Yaprakli) fotoğrafı güncellendi: eski .png yerine yeni .jpg
 * (public/images/team/halil-yaprakli.jpg). Uzantı değiştiği için tarayıcı önbelleği
 * doğal olarak kırılır — eski foto cache'te kalmaz. Idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->where('avatar_url', '/images/team/halil-yaprakli.png')
            ->update(['avatar_url' => '/images/team/halil-yaprakli.jpg']);
    }

    public function down(): void
    {
        DB::table('users')
            ->where('avatar_url', '/images/team/halil-yaprakli.jpg')
            ->update(['avatar_url' => '/images/team/halil-yaprakli.png']);
    }
};
