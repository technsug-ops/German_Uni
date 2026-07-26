<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Filiz Ozkan: "Dr." unvani (kullanici: PhD sahibi) + olasi DUPLIKE birlestirme.
 *
 * Filiz prod'da zaten var olabilir; onceki migration lokalde yeni bir kayit
 * olusturmus olabilir. Burada tum "Filiz" kullanicilari bulunur, EN ESKI olan
 * (orijinal) canonical secilir, digerlerinin yazilari ona tasinir ve fazlalar
 * silinir. Ardindan canonical duzgun kimlikle guncellenir.
 *
 * Idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $filiz = DB::table('users')
            ->where(function ($q) {
                $q->where('name', 'like', '%Filiz%')
                  ->orWhere('slug', 'like', '%filiz%')
                  ->orWhere('email', 'like', '%filiz%');
            })
            ->orderBy('id')
            ->pluck('id')
            ->all();

        if (empty($filiz)) { echo "  Filiz bulunamadi, atlaniyor\n"; return; }

        $canonical = $filiz[0]; // en eski = orijinal
        $dups = array_slice($filiz, 1);

        foreach ($dups as $dup) {
            DB::table('posts')->where('user_id', $dup)->update(['user_id' => $canonical]);
            DB::table('users')->where('id', $dup)->delete();
        }

        // Canonical'i duzgun kimlikle guncelle (slug artik serbest)
        DB::table('users')->where('id', $canonical)->update([
            'name'          => 'Dr. Filiz Özkan',
            'slug'          => 'filiz-ozkan',
            'role_label'    => 'Master & Kariyer Editörü · Halil’in yardımcısı',
            'role_label_en' => 'Master’s & Careers Editor · Deputy to Halil',
            'role_label_de' => 'Master- & Karriere-Redakteurin · Stellvertreterin von Halil',
            'bio'           => 'Almanya’da yüksek lisans programları ve üniversite sonrası kariyer konularında içerik üretir; Halil’e destek verir. Doktora (PhD) derecesine sahiptir.',
            'bio_en'        => 'Creates content on master’s programs in Germany and post-university careers; supports Halil. Holds a doctorate (PhD).',
            'bio_de'        => 'Erstellt Inhalte zu Masterprogrammen in Deutschland und Karriere nach dem Studium; unterstützt Halil. Promoviert (Dr.).',
            'is_author'     => 1,
            'updated_at'    => now(),
        ]);

        $n = count($dups);
        echo "  Filiz canonical id {$canonical}; birlestirilen duplike: {$n}\n";
    }

    public function down(): void
    {
        // Geri alinamaz.
    }
};
