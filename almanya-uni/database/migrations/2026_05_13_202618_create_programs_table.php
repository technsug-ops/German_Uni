<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('university_id')->constrained()->cascadeOnDelete();
            $table->foreignId('field_of_study_id')->nullable()->constrained('fields_of_study')->nullOnDelete();

            $table->string('name_de');
            $table->string('name_en')->nullable();
            $table->string('name_tr')->nullable();
            $table->string('slug', 200);

            $table->string('degree', 40)->index();        // bachelor, master, staatsexamen, phd, diplom, magister
            $table->string('language', 40)->nullable();   // de, en, mixed, other (CSV liste)
            $table->unsignedTinyInteger('duration_semesters')->nullable();
            $table->string('study_form', 30)->nullable(); // full_time, part_time, dual, distance, online

            $table->string('admission_mode', 60)->nullable(); // zulassungsfrei, nc-local, nc-national
            $table->string('admission_summary', 255)->nullable();
            $table->unsignedMediumInteger('tuition_fee_eur')->nullable();

            $table->string('source_url', 500)->nullable();
            $table->string('source', 40)->default('wikidata');
            $table->string('source_id', 60)->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['university_id', 'degree']);
            $table->index(['field_of_study_id', 'degree']);
            $table->index('language');
            $table->index('is_active');
            $table->unique(['university_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
