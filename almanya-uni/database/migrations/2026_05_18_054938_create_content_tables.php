<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_briefs', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('slug', 220)->unique();
            $table->enum('audience', [
                'aday_ogrenci', 'veli', 'mevcut_ogrenci', 'phd_adayi', 'genel',
            ])->index();
            $table->string('topic', 60)->nullable()->index(); // vize, dil, randevu, vb.
            $table->string('primary_keyword', 200)->nullable();
            $table->json('secondary_keywords')->nullable();
            $table->text('pain_point')->nullable();
            $table->json('source_questions')->nullable(); // gerçek topluluk soruları array
            $table->unsignedSmallInteger('target_word_count')->default(1500);
            $table->enum('brand_tone', ['formal', 'casual', 'instructive', 'inspirational'])->default('instructive');
            $table->enum('status', ['draft', 'in_progress', 'ready', 'published', 'archived'])
                ->default('draft')->index();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('content_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_brief_id')->constrained()->cascadeOnDelete();
            $table->enum('asset_type', [
                'blog',          // Markdown blog post
                'video_script',  // YouTube
                'tiktok',        // TikTok 30-60s
                'instagram',     // Carousel veya reel
                'twitter',       // X thread
                'podcast',       // Podcast outline
                'visual_brief',  // AI image prompts
            ])->index();
            $table->json('spec')->nullable(); // format-specific input params
            $table->mediumText('body_md')->nullable();
            $table->mediumText('body_html')->nullable();
            $table->enum('generated_by', ['manual', 'ai_gemini', 'ai_claude', 'ai_openai'])->default('manual');
            $table->mediumText('prompt_used')->nullable();
            $table->enum('status', ['draft', 'ready', 'scheduled', 'published'])->default('draft')->index();
            $table->timestamp('published_at')->nullable();
            $table->string('published_url', 500)->nullable();
            $table->json('published_meta')->nullable(); // views, likes, shares ileride
            $table->timestamps();

            $table->unique(['content_brief_id', 'asset_type'], 'content_assets_brief_type_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_assets');
        Schema::dropIfExists('content_briefs');
    }
};
