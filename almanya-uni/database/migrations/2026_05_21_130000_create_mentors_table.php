<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mentors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Kimlik
            $table->string('name', 150);
            $table->string('slug', 200)->unique();
            $table->string('headline', 200)->nullable(); // örnek: "AI Engineer @ Google · TUM Mezunu"
            $table->string('avatar_url', 500)->nullable();
            $table->string('current_role', 150)->nullable();
            $table->string('current_company', 150)->nullable();

            // Profil
            $table->text('bio')->nullable();
            $table->string('university', 200)->nullable();
            $table->string('field_of_study', 150)->nullable();
            $table->string('graduation_year', 8)->nullable();
            $table->string('city', 100)->nullable();

            // Kontak
            $table->string('linkedin_url', 500)->nullable();
            $table->string('twitter_url', 500)->nullable();
            $table->string('github_url', 500)->nullable();
            $table->string('website_url', 500)->nullable();
            $table->string('calendly_url', 500)->nullable();
            $table->string('contact_email', 200)->nullable();

            // Mentorship özellikleri
            $table->json('topics')->nullable();     // ['Career', 'AI', 'Startup', 'CV Review']
            $table->json('languages')->nullable();  // ['tr', 'de', 'en']
            $table->string('availability', 100)->nullable(); // "Haftada 2 saat"
            $table->decimal('rate_eur', 8, 2)->default(0); // 0 = ücretsiz
            $table->string('session_duration', 50)->nullable(); // "30 dk" / "1 saat"

            // Yönetim
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sessions_count')->default(0);
            $table->decimal('rating_avg', 3, 2)->nullable();
            $table->unsignedSmallInteger('rating_count')->default(0);
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentors');
    }
};
