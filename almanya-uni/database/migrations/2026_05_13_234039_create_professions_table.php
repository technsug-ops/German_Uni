<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('professions', function (Blueprint $table) {
            $table->id();

            // BERUFENET kimlik
            $table->unsignedInteger('berufenet_id')->unique();    // Bundesagentur Berufs-ID
            $table->string('kldb_code', 10)->nullable()->index(); // Klassifikation der Berufe 2010

            // İsim
            $table->string('name_de');           // "Wirtschaftsinformatiker/in"
            $table->string('short_name', 200)->nullable(); // Kurzbezeichnung
            $table->string('slug', 200)->unique();
            $table->string('name_tr')->nullable(); // Türkçe çeviri (sonra)

            // Kategori
            $table->string('cluster', 50)->nullable()->index();        // BKGR
            $table->string('cluster_label', 100)->nullable();          // örn. "IT"
            $table->foreignId('field_of_study_id')->nullable()->constrained('fields_of_study')->nullOnDelete();

            // Tip — BERUFENET 4 kategori sunar
            $table->string('type', 30)->nullable()->index();
            // ausbildung | studienberuf | weiterbildung | grundberuf

            // İçerik
            $table->text('description_de')->nullable();   // Beschreibung
            $table->text('description_tr')->nullable();   // Sonradan çeviri
            $table->text('steckbrief')->nullable();        // Kısa özet
            $table->json('info_fields')->nullable();       // {Aufgaben, Anforderungen, vs.}

            // Görsel
            $table->string('image_url', 500)->nullable();

            // Sync meta
            $table->timestamp('last_synced_at')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index('field_of_study_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professions');
    }
};
