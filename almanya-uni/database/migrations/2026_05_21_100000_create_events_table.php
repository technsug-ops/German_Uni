<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            // Etkinlik tipi
            $table->enum('type', [
                'webinar',          // 🎙️ Canlı online seminer
                'workshop',         // 🛠️ Atölye / interaktif
                'info_session',     // 📋 Bilgilendirme oturumu
                'qa_live',          // ❓ Canlı soru-cevap
                'meetup',           // 👥 Yüz yüze tanışma (offline)
                'open_day',         // 🏛️ Üni tanıtım günü (offline)
                'panel',            // 🎤 Mezunlar/uzmanlar paneli
                'deadline',         // 📅 Başvuru deadline hatırlatması
                'conference',       // 🎯 Konferans
            ])->default('webinar')->index();

            // İçerik
            $table->string('title_tr', 200);
            $table->string('title_de', 200)->nullable();
            $table->string('slug', 220)->unique();
            $table->text('description_md')->nullable();
            $table->string('host', 150)->nullable(); // konuşmacı / sponsor / üniversite

            // Tarih
            $table->dateTime('starts_at')->index();
            $table->dateTime('ends_at')->nullable();
            $table->string('timezone', 32)->default('Europe/Berlin');

            // Lokasyon
            $table->string('mode', 16)->default('online'); // online | offline | hybrid
            $table->string('online_url', 500)->nullable();
            $table->string('location_name', 200)->nullable();
            $table->string('location_city', 100)->nullable();

            // Kayıt
            $table->string('registration_url', 500)->nullable();
            $table->unsignedSmallInteger('max_attendees')->nullable();
            $table->unsignedSmallInteger('registered_count')->default(0);
            $table->boolean('registration_required')->default(true);
            $table->decimal('price_eur', 8, 2)->default(0);

            // Banner / görsel
            $table->string('banner_url', 500)->nullable();
            $table->string('banner_color', 32)->nullable(); // hex code

            // Flags
            $table->boolean('is_featured')->default(false)->index(); // top banner'da
            $table->boolean('is_active')->default(true)->index();

            // SEO
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_description', 500)->nullable();

            $table->timestamps();

            $table->index(['is_active', 'is_featured', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
