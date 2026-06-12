<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Profesyonel başvuru belgesi şablonları (premium içerik — ROADMAP gelir #1).
 * Lebenslauf, Motivationsschreiben, Empfehlungsschreiben, başvuru e-postaları…
 * Doldurulabilir [PLACEHOLDER] gövde + locale-aware başlık/açıklama/rehber.
 * İçerik ayrı bir data-migration'da updateOrInsert ile gelir (prod webhook).
 * is_premium işaretli ama henüz gating YOK ("önce sadece içerik" kararı).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 120)->unique();
            $table->string('category', 40)->index();   // application | finance | housing | career
            $table->string('doc_type', 20)->nullable(); // cv | letter | email — ikon/etiket

            // Locale-aware kimlik + katalog
            $table->string('title_tr')->nullable();
            $table->string('title_en')->nullable();
            $table->string('title_de')->nullable();
            $table->text('description_tr')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_de')->nullable();

            // Şablon gövdesi — [PLACEHOLDER] işaretli. body_de ana; body_en opsiyonel
            // (örn. İngilizce programlar için Motivation Letter).
            $table->longText('body_de')->nullable();
            $table->longText('body_en')->nullable();

            // Nasıl doldurulur / ipuçları (markdown) — prose, strict locale
            $table->longText('guide_tr')->nullable();
            $table->longText('guide_en')->nullable();
            $table->longText('guide_de')->nullable();

            // [{key, label_tr, label_en, label_de, hint_tr?}]
            $table->json('placeholders')->nullable();

            $table->boolean('is_premium')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_templates');
    }
};
