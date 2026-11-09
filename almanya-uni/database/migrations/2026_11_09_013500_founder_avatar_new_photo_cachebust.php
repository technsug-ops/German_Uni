<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Kurucu (Halil Yaprakli, id=1) yeni profesyonel portre — aynı dosya adı
 * (public/images/team/halil-yaprakli.png, 400x400 yüz-merkezli) üzerine yazıldı.
 * Tarayıcı/CDN önbelleği eski fotoğrafı göstermesin diye sürüm parametresi değişir.
 * Idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->where('id', 1)
            ->update(['avatar_url' => '/images/team/halil-yaprakli.png?v=2026-11']);
    }

    public function down(): void
    {
        DB::table('users')->where('id', 1)
            ->update(['avatar_url' => '/images/team/halil-yaprakli.png?v=2026']);
    }
};
