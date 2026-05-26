<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Her üni için scraper config: hangi URL, hangi tip, hangi selector'lar.
        Schema::create('scrape_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('list_url', 500);
            $table->enum('adapter', ['generic_html', 'playwright', 'sitemap', 'custom_php'])->default('generic_html');
            $table->json('config')->nullable(); // selectors, pagination, custom_class, vb.
            $table->unsignedInteger('throttle_ms')->default(3000);
            $table->boolean('respect_robots')->default(true);
            $table->boolean('is_enabled')->default(true);
            $table->string('etag', 120)->nullable();
            $table->string('last_modified_header', 80)->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->unsignedInteger('last_found_count')->nullable();
            $table->string('last_status', 40)->nullable(); // ok, fail, blocked_by_robots, no_change
            $table->text('last_error')->nullable();
            $table->timestamps();

            $table->index(['is_enabled', 'last_run_at']);
        });

        // Her çalıştırmanın audit logu.
        Schema::create('scrape_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scrape_source_id')->constrained()->cascadeOnDelete();
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('finished_at')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->string('status', 40); // running, ok, fail, no_change, blocked
            $table->unsignedInteger('http_requests')->default(0);
            $table->unsignedInteger('items_found')->default(0);
            $table->unsignedInteger('items_new')->default(0);
            $table->unsignedInteger('items_updated')->default(0);
            $table->text('error')->nullable();
            $table->json('meta')->nullable();

            $table->index(['scrape_source_id', 'started_at']);
        });

        // Staging — review öncesi ham veri. Onaylanırsa programs tablosuna kopyalanır.
        Schema::create('scraped_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scrape_source_id')->constrained()->cascadeOnDelete();
            $table->foreignId('university_id')->constrained()->cascadeOnDelete();
            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->string('external_key', 191)->nullable(); // üni iç ID veya URL
            $table->string('source_url', 500)->nullable();
            $table->string('name_de')->nullable();
            $table->string('name_en')->nullable();
            $table->string('degree', 40)->nullable();
            $table->string('language', 20)->nullable();
            $table->unsignedTinyInteger('duration_semesters')->nullable();
            $table->string('admission_mode', 30)->nullable();
            $table->decimal('nc_value', 3, 2)->nullable();
            $table->decimal('tuition_fee_eur', 10, 2)->nullable();
            $table->text('description_de')->nullable();
            $table->json('raw')->nullable(); // ham JSON — review için
            $table->string('content_hash', 64)->nullable(); // diff detection
            $table->enum('review_status', ['pending', 'approved', 'rejected', 'auto_approved'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamp('first_seen_at')->useCurrent();
            $table->timestamp('last_seen_at')->useCurrent();
            $table->timestamps();

            $table->unique(['scrape_source_id', 'external_key'], 'scraped_programs_source_key_unique');
            $table->index(['review_status', 'last_seen_at']);
            $table->index('content_hash');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scraped_programs');
        Schema::dropIfExists('scrape_runs');
        Schema::dropIfExists('scrape_sources');
    }
};
