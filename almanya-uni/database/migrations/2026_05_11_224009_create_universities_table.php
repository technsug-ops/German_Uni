<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('universities', function (Blueprint $table) {
            $table->id();
            $table->string('wikidata_id', 20)->nullable()->unique();

            $table->string('name_tr');
            $table->string('name_de');
            $table->string('name_en')->nullable();
            $table->string('slug')->unique();
            $table->string('short_name', 20)->nullable();

            $table->text('description_tr')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_de')->nullable();

            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->string('website_url', 500)->nullable();
            $table->string('logo_url', 500)->nullable();

            $table->enum('type', ['public', 'private', 'applied_sciences', 'art', 'religion'])->default('public');
            $table->smallInteger('founded_year')->nullable();
            $table->unsignedInteger('student_count')->nullable();

            $table->string('wikipedia_url_tr', 500)->nullable();
            $table->string('wikipedia_url_en', 500)->nullable();
            $table->string('wikipedia_url_de', 500)->nullable();

            $table->string('data_source', 50)->default('manual');
            $table->timestamp('last_synced_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('wikidata_id');
            $table->index('slug');
            $table->index('city_id');
            $table->index('type');
            $table->index('data_source');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('universities');
    }
};
