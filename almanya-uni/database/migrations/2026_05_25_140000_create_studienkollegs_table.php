<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('studienkollegs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);                              // Resmi kurum adı (Almanca orijinal)
            $table->string('slug', 200)->unique();
            $table->enum('type', ['staatlich', 'privat'])->default('staatlich');
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->string('city_name_cache', 100)->nullable();        // Performance: city table join'siz görüntü
            $table->foreignId('state_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('university_id')->nullable()->constrained()->nullOnDelete();

            // Kurslar/Tracks: T-Kurs (mühendislik/doğa bilimleri), M-Kurs (tıp), W-Kurs (ekonomi/sosyal), G-Kurs (humaniora), S-Kurs (filoloji)
            $table->json('tracks')->nullable();                       // ["T", "M", "W", "G", "S"]

            $table->string('website_url', 500)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('address', 300)->nullable();
            $table->integer('established_year')->nullable();
            $table->integer('capacity_per_year')->nullable();         // Yıllık kontenjan tahmin

            // Ücret / sınav
            $table->integer('semester_fee_eur')->nullable();          // Genelde 0 (staatlich) — privat: 5K-15K
            $table->string('entrance_exam', 50)->nullable();          // 'aufnahmetest', 'feststellungspruefung'

            // Lokalize alanlar — i18n JSON yapı (ileride yeni dil eklenince schema değişmez)
            $table->json('description')->nullable();                  // {tr, en, de, ar, zh, ...}
            $table->json('admission_requirements')->nullable();       // {tr: ['...'], en: ['...']}
            $table->json('notes')->nullable();                        // {tr, en, de}

            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
            $table->index('city_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('studienkollegs');
    }
};
