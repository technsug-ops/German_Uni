<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('housing_templates', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 100)->unique();
            $table->string('title_tr');
            $table->string('title_en')->nullable();
            $table->string('title_de')->nullable();
            $table->string('category', 40)->index();
            // wohnungsanfrage | wg-anfrage | dorm-application | besichtigung | absage | bewerbungstext
            $table->text('description_tr')->nullable();   // Şablonun ne için olduğunu anlatır
            $table->text('description_en')->nullable();
            $table->text('description_de')->nullable();
            $table->text('subject_de');                    // E-mail konusu (Almanca)
            $table->text('body_de');                       // Resmi gönderilecek Almanca metin
            $table->text('body_tr_explanation')->nullable(); // Türkçe satır-satır anlatım
            $table->json('placeholders')->nullable();      // {NAME}, {UNI}, {SEMESTER} vs.
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('housing_templates');
    }
};
