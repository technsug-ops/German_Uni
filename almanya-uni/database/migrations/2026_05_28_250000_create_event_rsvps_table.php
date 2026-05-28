<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Event RSVP'leri — Meetup tarzı katılım kaydı.
 *
 * status: going | maybe | cancelled
 *
 * Login + anonim (email + name) destekli.
 * Unique: (event_id, user_id) — login user tek RSVP yapabilir.
 * Unique: (event_id, email) — anonim de tek RSVP yapabilir (email başına).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_rsvps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('attendee_name', 80)->nullable();
            $table->string('attendee_email', 150)->nullable();
            $table->string('status', 16)->default('going')->index();
            $table->text('note')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();

            $table->index(['event_id', 'status']);
            $table->unique(['event_id', 'user_id'], 'event_user_unique');
            $table->unique(['event_id', 'attendee_email'], 'event_email_unique');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->unsignedInteger('maybe_count')->default(0)->after('registered_count');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('maybe_count');
        });
        Schema::dropIfExists('event_rsvps');
    }
};
