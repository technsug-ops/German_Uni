<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tarayıcı web-push abonelikleri (şehir bazlı etkinlik bildirimi).
 * Bir tarayıcı (endpoint) birden çok şehre abone olabilir → (endpoint_hash, city_id) unique.
 * endpoint uzun olduğu için index'i sha256 hash üzerinden kuruyoruz.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();
            $table->text('endpoint');
            $table->char('endpoint_hash', 64);
            $table->string('p256dh');
            $table->string('auth');
            $table->string('locale', 5)->default('tr');
            $table->timestamp('last_notified_at')->nullable();
            $table->timestamps();

            $table->unique(['endpoint_hash', 'city_id']);
            $table->index('city_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_subscriptions');
    }
};
