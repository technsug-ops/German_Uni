<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Şule Demirel profil fotoğrafı (kullanıcının verdiği LinkedIn profil resmi, 200×200; repo'da
 * public/img/team/sule-demirel.jpg — LinkedIn CDN adresi süreli olduğu için kopya kullanılır).
 * Yalnız otomatik baş harf avatarının yerine geçer; admin'den elle yüklenmiş fotoğraf ezilmez.
 */
return new class extends Migration
{
    private const PHOTO = 'https://applytogerman.com/img/team/sule-demirel.jpg';

    public function up(): void
    {
        $user = DB::table('users')->where('slug', 'sule-demirel')->first();
        if (! $user) {
            if (app()->environment('testing')) {
                return;
            }
            throw new RuntimeException('Fotoğraf eklenmedi: sule-demirel yazar kaydı bulunamadı.');
        }
        if ($user->avatar_url === self::PHOTO) {
            return; // zaten uygulanmış → no-op
        }
        if ($user->avatar_url && ! str_starts_with($user->avatar_url, 'https://ui-avatars.com/')) {
            return; // admin'den elle yüklenmiş fotoğraf var → ona dokunulmaz (deploy'u da durdurmaz)
        }
        DB::table('users')->where('id', $user->id)->update(['avatar_url' => self::PHOTO, 'updated_at' => now()]);
    }

    public function down(): void
    {
        // İçerik verisi.
    }
};
