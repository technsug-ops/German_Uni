<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;

/**
 * Garantör blog'u önce TR slug ile yayınlandı; kural: slug'lar İngilizce olmalı
 * (bkz english-key convention). Mevcut prod/lokal kaydı İngilizce slug'a taşı.
 * Idempotent: eski slug yoksa no-op. 160000 migration'ı yeni kurulumda zaten
 * İngilizce slug üretir; bu migration sadece halihazırda yayınlanmış kaydı düzeltir.
 */
return new class extends Migration
{
    public function up(): void
    {
        $old = 'almanya-garantor-belgesi-verpflichtungserklarung-rehberi';
        $new = 'germany-guarantor-declaration-verpflichtungserklarung-guide';

        // Yeni slug zaten varsa (160000 onu oluşturduysa) eski mükerrer kaydı sil.
        if (Post::where('slug', $new)->exists()) {
            Post::where('slug', $old)->delete();
            return;
        }

        Post::where('slug', $old)->update(['slug' => $new]);
    }

    public function down(): void
    {
        // Geri alınmaz — İngilizce slug kuralı kalıcı.
    }
};
