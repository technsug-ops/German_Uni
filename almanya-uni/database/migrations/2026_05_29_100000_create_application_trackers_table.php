<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Application Tracker — kullanıcının 8 adımlı Almanya başvuru yolculuğunu
 * tek dashboard'dan takip eder.
 *
 * Anonim destek: session_token ile (cookie tabanlı), kayıt zorunluluğu yok.
 * Login user'lar için user_id ile bağlanır.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_trackers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('session_token', 64)->nullable()->unique(); // anonim için

            // Profil
            $table->string('country_origin', 4)->default('TR');         // ISO code
            $table->string('degree_level', 24)->default('bachelor');    // bachelor / master / phd
            $table->string('target_semester', 16)->default('winter_2027'); // winter_2027 / summer_2028 / open

            // 8 adım — her birinin status'ü ve opsiyonel notu
            $table->json('steps')->nullable(); // {1: {status:..., note:..., completed_at:...}, ...}

            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_step_at')->nullable();

            $table->timestamps();

            $table->index(['user_id']);
            $table->index(['session_token']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_trackers');
    }
};
