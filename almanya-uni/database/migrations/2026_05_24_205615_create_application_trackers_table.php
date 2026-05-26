<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_trackers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('steps_completed')->nullable(); // ['eligibility', 'university_match', ...]
            $table->json('steps_data')->nullable();      // {eligibility: {country, verdict, ...}, ...}
            $table->string('target_intake', 20)->nullable(); // winter-2026, summer-2027
            $table->foreignId('target_university_id')->nullable()->constrained('universities')->nullOnDelete();
            $table->string('target_degree', 20)->nullable(); // bachelor, master, phd
            $table->timestamp('started_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->boolean('email_reminders')->default(true);
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_trackers');
    }
};
