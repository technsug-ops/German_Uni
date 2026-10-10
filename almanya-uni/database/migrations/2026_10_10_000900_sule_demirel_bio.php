<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Şule Demirel biyografisi (kullanıcı onayı 2026-10-10). Kaynak: ApplyToGerman'daki unvanı ve kullanıcının paylaştığı
 * LinkedIn başlığı ("İş Geliştirme Sorumlusu @ DreamToMove | Eğitim Acenteleri İçin B2B Çözüm Ortaklığı").
 * Akademik unvan, uzmanlık iddiası veya deneyim yılı yok.
 */
return new class extends Migration
{
    private const BIO = [
        'bio'    => 'Şule Demirel, ApplyToGerman\'da Türkiye Öğrenci Direktörü. Aynı zamanda DreamToMove\'da eğitim acenteleri için B2B çözüm ortaklığı alanında iş geliştirme sorumlusu olarak çalışıyor.',
        'bio_en' => 'Şule Demirel is Director, Students from Türkiye at ApplyToGerman. She also works in business development at DreamToMove, a B2B partner for education agencies.',
        'bio_de' => 'Şule Demirel ist bei ApplyToGerman Direktorin für Studierende aus der Türkei. Außerdem ist sie bei DreamToMove, einem B2B-Partner für Bildungsagenturen, für Business Development zuständig.',
    ];

    public function up(): void
    {
        $user = DB::table('users')->where('slug', 'sule-demirel')->first();
        if (! $user) {
            if (app()->environment('testing')) {
                return;
            }
            throw new RuntimeException('Biyografi eklenmedi: sule-demirel yazar kaydı bulunamadı.');
        }
        if ($user->name !== 'Şule Demirel') {
            throw new RuntimeException("Biyografi eklenmedi: sule-demirel slug'ı başka bir kişiye ait ({$user->name}).");
        }
        if (collect(self::BIO)->every(fn ($v, $k) => $user->{$k} === $v)) {
            return; // zaten uygulanmış → no-op
        }
        // Yalnız boş ya da bu migration'ın yazdığı biyografi güncellenir; admin'den elle girilmiş metin ezilmez.
        foreach (array_keys(self::BIO) as $k) {
            if ($user->{$k} !== null && $user->{$k} !== '' && $user->{$k} !== self::BIO[$k]) {
                throw new RuntimeException("Biyografi eklenmedi: {$k} alanında başka bir metin var; elle girilmiş biyografi ezilmez.");
            }
        }
        DB::table('users')->where('id', $user->id)->update(self::BIO + ['updated_at' => now()]);
    }

    public function down(): void
    {
        // İçerik verisi.
    }
};
