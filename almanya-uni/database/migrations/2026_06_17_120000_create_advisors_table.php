<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Danışma Kurulu — admin'den yönetilen GERÇEK danışmanlar (Studyportals Advisory Board
 * benzeri güven/otorite bölümü). Uydurma kişi konulmaz; doğrulanabilir profil linki
 * (LinkedIn vb.) tutulur. Aktif danışman yoksa site bölümü hiç görünmez.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advisors', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->nullable()->index();
            $t->string('role_title')->nullable();      // ör. "Bilgisayar Bilimleri Profesörü"
            $t->string('affiliation')->nullable();      // ör. "TU München"
            $t->string('photo_url', 500)->nullable();
            $t->text('bio')->nullable();
            $t->string('linkedin_url', 500)->nullable(); // doğrulanabilirlik için
            $t->string('profile_url', 500)->nullable();
            $t->integer('sort_order')->default(10);
            $t->boolean('is_active')->default(false);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advisors');
    }
};
