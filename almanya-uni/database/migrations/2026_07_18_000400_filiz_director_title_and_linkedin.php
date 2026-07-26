<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Dr. Filiz Ozkan: "yardimci" yerine DUZGUN unvan (Icerik Direktoru) + LinkedIn.
 * (Kullanici: "Halil'in yardimcisi nedir, ona da bir title ver".)
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->where('slug', 'filiz-ozkan')->update([
            'role_label'    => 'İçerik Direktörü',
            'role_label_en' => 'Content Director',
            'role_label_de' => 'Content-Direktorin',
            'bio'           => 'Almanya’da yüksek lisans programları ve üniversite sonrası kariyer konularında uzman; içerik stratejisini yönlendirir. Doktora (PhD) derecesine sahiptir.',
            'bio_en'        => 'Expert in master’s programs in Germany and post-university careers; leads content strategy. Holds a doctorate (PhD).',
            'bio_de'        => 'Expertin für Masterprogramme in Deutschland und Karriere nach dem Studium; leitet die Content-Strategie. Promoviert (Dr.).',
            'social_links'  => json_encode(['linkedin' => 'https://www.linkedin.com/in/dr-filiz-%C3%B6zkan']),
            'updated_at'    => now(),
        ]);
    }

    public function down(): void
    {
        // Geri alinamaz.
    }
};
