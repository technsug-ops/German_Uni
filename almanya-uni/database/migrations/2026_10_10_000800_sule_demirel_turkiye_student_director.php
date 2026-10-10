<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Şule Demirel: unvan "Türkiye Öğrenci Direktörü" (kullanıcı kararı, 2026-10-10). Ekip sayfası unvanında "direktör"
 * geçenleri editör grubunun başında gösterir (AboutController::team) → katkı sağlayanlardan üst pozisyona çıkar.
 * Biyografi ve fotoğraf bu migration'da değişmez.
 */
return new class extends Migration
{
    public function up(): void
    {
        $user = DB::table('users')->where('slug', 'sule-demirel')->first();
        if (! $user) {
            if (app()->environment('testing')) {
                return;
            }
            throw new RuntimeException('Unvan güncellenmedi: sule-demirel yazar kaydı bulunamadı.');
        }
        if ($user->name !== 'Şule Demirel') {
            throw new RuntimeException("Unvan güncellenmedi: sule-demirel slug'ı başka bir kişiye ait ({$user->name}).");
        }

        $roles = [
            'role_label'    => 'Türkiye Öğrenci Direktörü',
            'role_label_en' => 'Director, Students from Türkiye',
            'role_label_de' => 'Direktorin für Studierende aus der Türkei',
        ];
        if (collect($roles)->every(fn ($v, $k) => $user->{$k} === $v)) {
            return; // zaten uygulanmış → no-op
        }
        DB::table('users')->where('id', $user->id)->update($roles + ['updated_at' => now()]);
    }

    public function down(): void
    {
        // İçerik verisi.
    }
};
