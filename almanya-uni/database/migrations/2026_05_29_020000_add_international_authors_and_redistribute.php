<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * 2 uluslararası yazar ekler + post'ları yeniden dağıtır.
 *
 * Yeni yazarlar (mock bio, gerçek sosyal link YOK — feedback uyumu):
 *   - Anna Schmidt (DE) → "Almanya'da Eğitim" kategorisi
 *   - Ayesha Khan (PK) → "Finans" kategorisi
 *
 * Mevcut Halil Yaprakli (founder) editorial role'e geçer — kategori atanmaz.
 * Diğer 4 mock (Elif/Gamze/Hakan/Caner) önceki dağılımdaki kategorilerinde kalır.
 *
 * Idempotent: target user yoksa create eder, varsa update etmez.
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        // 1) Anna Schmidt — Almanya'da Eğitim editörü
        $annaId = DB::table('users')->where('email', 'anna@almanyauni.de')->value('id');
        if (! $annaId) {
            $annaId = DB::table('users')->insertGetId([
                'name'        => 'Anna Schmidt',
                'slug'        => Schema::hasColumn('users', 'slug') ? 'anna-schmidt' : null,
                'email'       => 'anna@almanyauni.de',
                'password'    => bcrypt(Str::random(40)),
                'role_label'  => 'İçerik Editörü · Alman akademik sistemi',
                'bio'         => 'Berlin\'de yaşayan Alman akademisyen. Studienkolleg, Hochschulzulassung ve Almanya yükseköğretim sistemi konularında uzman içerik üretiyor.',
                'avatar_url'  => 'https://ui-avatars.com/api/?name=Anna+Schmidt&background=4f46e5&color=fff&bold=true&size=200',
                'is_author'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }

        // 2) Ayesha Khan — Finans editörü (uluslararası perspektif)
        $ayeshaId = DB::table('users')->where('email', 'ayesha@almanyauni.de')->value('id');
        if (! $ayeshaId) {
            $ayeshaId = DB::table('users')->insertGetId([
                'name'        => 'Ayesha Khan',
                'slug'        => Schema::hasColumn('users', 'slug') ? 'ayesha-khan' : null,
                'email'       => 'ayesha@almanyauni.de',
                'password'    => bcrypt(Str::random(40)),
                'role_label'  => 'İçerik Editörü · Uluslararası öğrenci finansı',
                'bio'         => 'Pakistan asıllı, Münih TUM\'da uluslararası master öğrencisi. Sperrkonto, Krankenkasse ve Schufa konularını AB-dışı öğrenci perspektifiyle yazıyor.',
                'avatar_url'  => 'https://ui-avatars.com/api/?name=Ayesha+Khan&background=db2777&color=fff&bold=true&size=200',
                'is_author'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }

        // 3) Yeniden dağıtım: kategori → yazar
        //    Halil Yaprakli'nin Eğitim + Finans postları yeni 2 yazara devredilir
        DB::table('posts')->where('category_id', 1)->update(['user_id' => $annaId]);   // Almanya'da Eğitim
        DB::table('posts')->where('category_id', 5)->update(['user_id' => $ayeshaId]); // Finans
    }

    public function down(): void
    {
        // Geri alma yok — post sahipleri belirsiz kalır.
    }
};
