<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Yeni yazar: Şule Demirel (ApplyToGerman Almanya Öğrenci İstatistikleri Raporu 2026'nın web yazısı için).
 * Yalnız doğrulanabilen bilgi: ad, yazar rolü, LinkedIn profili. Biyografi onay bekliyor (boş bırakılır); unvan,
 * uzmanlık veya deneyim yılı yazılmaz. Hiçbir yazının yazarı değişmez.
 *
 * Güvenlik: aynı slug başka bir adla varsa RuntimeException (ikinci profil açılmaz). Kayıt zaten aynıysa no-op.
 * E-posta zorunlu kolon olduğu için teslim edilemeyen .invalid adresi kullanılır (giriş hesabı değildir).
 */
return new class extends Migration
{
    private const SLUG = 'sule-demirel';

    private const NAME = 'Şule Demirel';

    private const LINKEDIN = 'https://www.linkedin.com/in/%C5%9Fule-demirel-415a66116/';

    public function up(): void
    {
        $existing = DB::table('users')->where('slug', self::SLUG)->first();
        if ($existing) {
            if ($existing->name !== self::NAME) {
                throw new RuntimeException("Yazar eklenmedi: '" . self::SLUG . "' slug'ı başka bir kişiye ait ({$existing->name}).");
            }

            return; // zaten var → no-op
        }
        if (DB::table('users')->where('name', self::NAME)->exists()) {
            throw new RuntimeException('Yazar eklenmedi: ' . self::NAME . ' adıyla farklı slug\'lı bir kayıt var; ikinci profil açılmaz.');
        }

        DB::table('users')->insert([
            'name'          => self::NAME,
            'slug'          => self::SLUG,
            'email'         => 'sule-demirel@authors.applytogerman.invalid',
            'password'      => bcrypt(Str::random(40)),
            'role_label'    => 'Yazar',
            'role_label_en' => 'Author',
            'role_label_de' => 'Autorin',
            'avatar_url'    => 'https://ui-avatars.com/api/?name=%C5%9Eule+Demirel&background=0f766e&color=fff&bold=true&size=200',
            'social_links'  => json_encode(['linkedin' => self::LINKEDIN]),
            'is_author'     => true,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }

    public function down(): void
    {
        // Geri alma yok — yazar profili başka kayıtlarla ilişkilendirilebilir.
    }
};
