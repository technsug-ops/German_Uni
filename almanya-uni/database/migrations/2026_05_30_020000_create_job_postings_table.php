<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('job_postings')) {
            return;
        }

        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->foreignId('university_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('field_of_study_id')->nullable()->constrained('fields_of_study')->nullOnDelete();

            // 'phd', 'postdoc', 'lecturer', 'professor', 'researcher', 'admin', 'industry'
            $table->string('position_type', 40)->index();
            // 'full_time', 'part_time', 'fixed_term', 'permanent'
            $table->string('employment_type', 40)->default('fixed_term');

            // 'de', 'en', 'both'
            $table->string('language', 10)->default('en');

            // 'TV-L E13', 'TV-L E14', etc. (German public sector pay-scale) — or free text
            $table->string('salary_band', 60)->nullable();
            $table->unsignedInteger('salary_min_eur')->nullable();
            $table->unsignedInteger('salary_max_eur')->nullable();

            // Short summary (cards)
            $table->text('excerpt')->nullable();
            // Full description (Markdown)
            $table->longText('description')->nullable();
            // Bullet-list (Markdown)
            $table->longText('requirements')->nullable();

            $table->date('posted_at')->nullable()->index();
            $table->date('deadline_at')->nullable()->index();

            $table->string('application_url')->nullable();
            $table->string('source_url')->nullable();
            $table->string('source_name', 80)->nullable();

            $table->boolean('is_remote')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);

            $table->unsignedInteger('view_count')->default(0);

            $table->timestamps();

            $table->index(['is_active', 'deadline_at']);
            $table->index(['position_type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_postings');
    }
};
