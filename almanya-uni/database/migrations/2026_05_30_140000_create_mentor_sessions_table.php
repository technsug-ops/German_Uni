<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('mentor_sessions')) return;

        Schema::create('mentor_sessions', function (Blueprint $t) {
            $t->id();

            // Relations
            $t->foreignId('mentor_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Booking details
            $t->timestamp('scheduled_at');
            $t->unsignedSmallInteger('duration_minutes')->default(30);

            // Auto-generated Jitsi room (UUID-based, hard to guess)
            $t->string('jitsi_room_id', 64)->unique();

            // Optional: external booking provider (Cal.com / Calendly) reference
            $t->string('external_provider', 30)->nullable()->comment('cal_com | calendly | in_app');
            $t->string('external_booking_id', 100)->nullable();

            // User-supplied topic / question
            $t->string('topic', 200)->nullable();
            $t->text('notes')->nullable();
            $t->string('preferred_language', 5)->default('tr');

            // Status
            $t->enum('status', ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'])->default('pending');
            $t->text('cancellation_reason')->nullable();

            // Post-session
            $t->unsignedTinyInteger('rating')->nullable()->comment('1-5 stars');
            $t->text('feedback')->nullable();

            $t->timestamps();

            $t->index(['mentor_id', 'scheduled_at']);
            $t->index(['user_id', 'scheduled_at']);
            $t->index(['status', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentor_sessions');
    }
};
