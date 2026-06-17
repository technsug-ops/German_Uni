<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Kurucu avatarı kareye kırpıldı (yüz-merkezli 400x400) ve .png olarak standart.
 * avatar_url'i .png + sürüm parametresiyle güncelle (önbellek kırma — eski .jpg/.png
 * cache'te kalmasın). Idempotent (id=1).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->where('id', 1)
            ->update(['avatar_url' => '/images/team/halil-yaprakli.png?v=2026']);
    }

    public function down(): void
    {
        DB::table('users')->where('id', 1)
            ->update(['avatar_url' => '/images/team/halil-yaprakli.jpg']);
    }
};
