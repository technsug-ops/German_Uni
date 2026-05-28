<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Admin hesabını geri yükler.
 *
 * Yapra users merge migration (2026_05_29_000000) yanlışlıkla
 * admin@almanyauni.de hesabını da sildi — bu hesap kullanıcının
 * Filament admin paneline giriş için kullandığı ana hesaptı.
 *
 * Email zaten varsa idempotent (skip).
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        // Admin
        $admin = DB::table('users')->where('email', 'admin@almanyauni.de')->first();
        if ($admin) {
            DB::table('users')->where('id', $admin->id)->update([
                'is_admin'   => true,
                'updated_at' => $now,
            ]);
        } else {
            DB::table('users')->insert([
                'name'              => 'Admin',
                'email'             => 'admin@almanyauni.de',
                'password'          => Hash::make('admin1234'),
                'email_verified_at' => $now,
                'is_admin'          => true,
                'is_editor'         => true,
                'role_label'        => 'Sistem Yöneticisi',
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);
        }

        // Editor (editor@almanyauni.com da merge migration'da yanlışlıkla silinmişti)
        $editor = DB::table('users')->where('email', 'editor@almanyauni.com')->first();
        if (! $editor) {
            DB::table('users')->insert([
                'name'              => 'Editor',
                'email'             => 'editor@almanyauni.com',
                'password'          => Hash::make('admin1234'),
                'email_verified_at' => $now,
                'is_admin'          => false,
                'is_editor'         => true,
                'role_label'        => 'İçerik Editörü',
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('users')->whereIn('email', ['admin@almanyauni.de', 'editor@almanyauni.com'])->delete();
    }
};
