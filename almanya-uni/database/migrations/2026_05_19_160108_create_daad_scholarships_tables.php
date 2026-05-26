<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ana burs tablosu — DAAD scholarship database snapshot
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sap_objid')->unique();   // canonical match key
            $table->unsignedInteger('daad_id')->nullable();      // scholarships.js 'id' field
            $table->unsignedBigInteger('sap_progid')->nullable();
            $table->string('sap_target_system', 32)->nullable();

            $table->text('name_de')->nullable();
            $table->text('name_en')->nullable();
            $table->text('langname_de')->nullable();
            $table->text('langname_en')->nullable();
            $table->text('programmname_de')->nullable();
            $table->text('programmname_en')->nullable();

            $table->unsignedInteger('programmtyp_id')->nullable();
            $table->string('slug', 255)->unique();

            // introduction / qDe / qEn polymorphic: string OR {de,en,...}
            $table->json('introduction_json')->nullable();
            $table->json('q_de_json')->nullable();
            $table->json('q_en_json')->nullable();

            $table->boolean('is_daad')->default(false);
            $table->boolean('is_move')->default(false);
            $table->integer('sorting')->nullable();

            $table->timestamp('last_seen_at')->nullable();        // bu sync'te görüldü
            $table->timestamp('removed_at')->nullable();          // DAAD'den kalktı
            $table->string('detail_url', 500)->nullable();        // DAAD canonical

            $table->timestamps();

            $table->index('is_daad');
            $table->index('removed_at');
            $table->index('programmtyp_id');
        });

        // Lookups
        Schema::create('scholarship_origins_lookup', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->string('name_de', 160)->nullable();
            $table->string('name_en', 160)->nullable();
            $table->string('name_es', 160)->nullable();
            $table->string('sortname', 160)->nullable();
        });

        Schema::create('scholarship_statuses', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->string('name_de', 120)->nullable();
            $table->string('name_en', 120)->nullable();
            $table->string('name_es', 120)->nullable();
            $table->integer('sortierung')->nullable();
        });

        Schema::create('scholarship_subject_groups', function (Blueprint $table) {
            $table->string('code', 4)->primary();
            $table->string('name_de', 160)->nullable();
            $table->string('name_en', 160)->nullable();
            $table->string('name_es', 160)->nullable();
        });

        Schema::create('scholarship_intentions', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->string('name_de', 120)->nullable();
            $table->string('name_en', 120)->nullable();
        });

        // Pivots — M:M
        Schema::create('scholarship_origin', function (Blueprint $table) {
            $table->foreignId('scholarship_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('origin_id');
            $table->primary(['scholarship_id', 'origin_id']);
            $table->index('origin_id');
        });

        Schema::create('scholarship_status', function (Blueprint $table) {
            $table->foreignId('scholarship_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('status_id');
            $table->primary(['scholarship_id', 'status_id']);
            $table->index('status_id');
        });

        Schema::create('scholarship_subject', function (Blueprint $table) {
            $table->foreignId('scholarship_id')->constrained()->cascadeOnDelete();
            $table->string('subject_code', 4);
            $table->primary(['scholarship_id', 'subject_code']);
            $table->index('subject_code');
        });

        Schema::create('scholarship_intention', function (Blueprint $table) {
            $table->foreignId('scholarship_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('intention_id');
            $table->primary(['scholarship_id', 'intention_id']);
            $table->index('intention_id');
        });

        // Deadlines — 1:1 via sap_objid
        Schema::create('scholarship_deadlines', function (Blueprint $table) {
            $table->unsignedBigInteger('sap_objid')->primary();
            $table->text('general_de')->nullable();
            $table->text('general_en')->nullable();
            $table->json('countries_json')->nullable();
            $table->timestamp('last_seen_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarship_deadlines');
        Schema::dropIfExists('scholarship_intention');
        Schema::dropIfExists('scholarship_subject');
        Schema::dropIfExists('scholarship_status');
        Schema::dropIfExists('scholarship_origin');
        Schema::dropIfExists('scholarship_intentions');
        Schema::dropIfExists('scholarship_subject_groups');
        Schema::dropIfExists('scholarship_statuses');
        Schema::dropIfExists('scholarship_origins_lookup');
        Schema::dropIfExists('scholarships');
    }
};
