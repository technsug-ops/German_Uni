<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('university_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Yazar bilgileri (login değilse anon)
            $table->string('author_name', 100)->nullable();
            $table->string('author_email', 150)->nullable();   // Doğrulama + dupe önleme
            $table->string('author_program', 150)->nullable(); // Hangi programa kayıtlı
            $table->enum('author_status', ['current_student', 'alumni', 'admitted', 'applicant'])->nullable();
            $table->integer('study_year')->nullable();         // Hangi yıl başladı (privacy: sadece yıl)

            // İçerik
            $table->tinyInteger('rating')->unsigned();         // 1-5
            $table->string('title', 200);
            $table->text('body');
            $table->string('locale', 5)->default('en');        // Hangi dilde yazılmış (i18n display)

            // Moderation + güven
            $table->enum('status', ['pending', 'approved', 'rejected', 'spam'])->default('pending');
            $table->boolean('is_verified')->default(false);     // Email doğrulandı mı
            $table->string('verification_token', 64)->nullable()->unique();
            $table->timestamp('verified_at')->nullable();
            $table->string('moderation_note', 500)->nullable();
            $table->foreignId('moderated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('moderated_at')->nullable();

            // UGC kalitesi
            $table->unsignedInteger('helpful_count')->default(0);
            $table->unsignedInteger('unhelpful_count')->default(0);
            $table->unsignedInteger('reported_count')->default(0);

            // Tracking
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();

            $table->timestamps();

            $table->index(['university_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->unique(['university_id', 'author_email'], 'unique_email_per_uni'); // 1 email = 1 review/uni
        });

        // Helpful votes — kim hangi review'a oy verdi (dupe önleme)
        Schema::create('university_review_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('university_reviews')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_token', 64)->nullable();  // Anon kullanıcı için
            $table->enum('vote', ['helpful', 'unhelpful', 'report']);
            $table->timestamps();

            $table->index(['review_id', 'vote']);
            $table->unique(['review_id', 'user_id', 'vote'], 'unique_user_vote');
            $table->unique(['review_id', 'session_token', 'vote'], 'unique_session_vote');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('university_review_votes');
        Schema::dropIfExists('university_reviews');
    }
};
