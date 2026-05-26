<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_audits', function (Blueprint $table) {
            $table->id();
            $table->string('template', 60)->index(); // home, university_detail, city_detail, program_detail, blog_index, ...
            $table->string('sample_url', 500); // gerçek bir sayfa URL'i (analiz edilen)
            $table->string('page_title', 255)->nullable();
            $table->unsignedInteger('content_length')->default(0); // metin uzunluğu (char)
            $table->unsignedSmallInteger('h1_count')->default(0);
            $table->unsignedSmallInteger('h2_count')->default(0);
            $table->unsignedSmallInteger('image_count')->default(0);
            $table->unsignedSmallInteger('internal_link_count')->default(0);
            $table->json('keywords_found')->nullable(); // sayfa metninde bulunanlar
            $table->json('keywords_missing')->nullable(); // forum/telegram'da yüksek, sayfada yok
            $table->json('high_value_gaps')->nullable(); // en kritik eksikler (skorlu)
            $table->unsignedTinyInteger('opportunity_score')->default(0); // 0-100
            $table->text('ai_suggestions')->nullable(); // AI'ın "şu bölümler eklenebilir" önerisi
            $table->json('ai_meta')->nullable(); // tokens, model, vs.
            $table->timestamp('last_audited_at')->nullable();
            $table->timestamps();

            $table->index(['template', 'opportunity_score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_audits');
    }
};
