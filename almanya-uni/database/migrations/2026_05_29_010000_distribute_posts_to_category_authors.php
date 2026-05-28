<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * 87 howto post'u kategori bazında 5 yazara dağıtır.
 *
 * Dağılım mantığı (kategori_id → user.email):
 *   1 Almanya'da Eğitim → Halil Yaprakli (kurucu, ana yazılar)
 *   5 Finans            → Halil Yaprakli
 *   6 Vize              → Hakan Kutlu
 *   7 Dil & Sınavlar    → Gamze E.
 *   8 Başvuru           → Elif G.
 *   9 Öğrenci Hayatı    → Caner Türkdoğru
 *
 * Email-based lookup — production'da user ID'leri farklı olabilir.
 * Idempotent: target user yoksa ilgili dağıtım skip edilir.
 */
return new class extends Migration
{
    public function up(): void
    {
        $emailToId = function (string $email): ?int {
            $id = DB::table('users')->where('email', $email)->value('id');
            return $id ? (int) $id : null;
        };

        $founder  = $emailToId('yapra-test1@gmail.com')           // Halil Yaprakli (merge'den sonra)
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id');
        $visa     = $emailToId('hakan@almanyauni.com');            // Hakan Kutlu
        $lang     = $emailToId('gamze@almanyauni.com');            // Gamze E.
        $apply    = $emailToId('elif@almanyauni.com');             // Elif G.
        $life     = $emailToId('caner@almanyauni.com');            // Caner Türkdoğru

        if ($founder) {
            // Almanya'da Eğitim (1) + Finans (5) → Halil
            DB::table('posts')->whereIn('category_id', [1, 5])->update(['user_id' => $founder]);
        }
        if ($visa) {
            DB::table('posts')->where('category_id', 6)->update(['user_id' => $visa]);
        }
        if ($lang) {
            DB::table('posts')->where('category_id', 7)->update(['user_id' => $lang]);
        }
        if ($apply) {
            DB::table('posts')->where('category_id', 8)->update(['user_id' => $apply]);
        }
        if ($life) {
            DB::table('posts')->where('category_id', 9)->update(['user_id' => $life]);
        }

        // Yeni dağıtılan yazarların is_author flag'ini garantile + slug yoksa ata
        foreach ([$visa, $lang, $apply, $life] as $uid) {
            if (! $uid) continue;
            $u = DB::table('users')->where('id', $uid)->first();
            if (! $u) continue;
            $updates = [];
            if (! $u->is_author) $updates['is_author'] = true;
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'slug') && empty($u->slug)) {
                $updates['slug'] = \Illuminate\Support\Str::slug($u->name);
            }
            if (! empty($updates)) {
                $updates['updated_at'] = now();
                DB::table('users')->where('id', $uid)->update($updates);
            }
        }
    }

    public function down(): void
    {
        // Geri alma yok — hepsi tek user'a toplanırdı, anlamsız.
    }
};
