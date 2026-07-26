<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Ekip sayfasi hiyerarsisi: Halil = Kurucu (en ust), Ayesha = Editor (tutarlilik).
 * Team gruplama role_label icindeki 'kurucu'/'editör'/'direktör' kelimelerine bakiyor.
 * (Filiz zaten 'Icerik Direktoru' — controller onu editorlerin basina koyar.)
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->where('slug', 'halil-yaprakli')->update([
            'role_label'    => 'Kurucu & CEO',
            'role_label_en' => 'Founder & CEO',
            'role_label_de' => 'Gründer & CEO',
            'updated_at'    => now(),
        ]);

        DB::table('users')->where('slug', 'ayesha-khan')->update([
            'role_label'    => 'Uluslararası Öğrenci & Burs Editörü',
            'role_label_en' => 'International Students & Scholarships Editor',
            'role_label_de' => 'Redakteurin Internationale Studierende & Stipendien',
            'updated_at'    => now(),
        ]);
    }

    public function down(): void
    {
        // Geri alinamaz.
    }
};
