<?php

use Illuminate\Database\Migrations\Migration;

/**
 * Gemini ile üretilmiş eski hero görselleri (19a837c ile kullanımdan kalktı) sunucuda kaldı:
 * deploy paketi yeni dosyaları açar ama repodan silinen dosyaları sunucudan silmez. Burada silinir.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['img/hero/hero-desktop.webp', 'img/hero/hero-mobile.webp'] as $f) {
            if (is_file(public_path($f))) {
                @unlink(public_path($f));
            }
        }
    }

    public function down(): void
    {
        // Geri alma yok.
    }
};
