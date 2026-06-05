<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Çok-kampüslü üniversiteler için pivot.
 *
 * universities.city_id = BİRİNCİL şehir (eyalet sayımı + cities.index için tek-kaynak).
 * university_campuses  = EK kampüs şehirleri (ör. Duisburg-Essen → Duisburg + Essen,
 * FAU Erlangen-Nürnberg, Cottbus-Senftenberg...). Şehir DETAY sayfaları birincil VEYA
 * kampüs eşleşmesini gösterir; eyalet sayımı birincilden tek sayar (çift saymaz).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('university_campuses')) {
            return;
        }
        Schema::create('university_campuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained()->cascadeOnDelete();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['university_id', 'city_id']);
            $table->index('city_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('university_campuses');
    }
};
