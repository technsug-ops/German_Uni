<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Şehir bazlı etkinlik bildirimi aboneliği (double opt-in).
 * Bir kullanıcı/email birden çok şehre abone olabilir → (email, city_id) unique.
 * Onaylı + iptal edilmemiş aboneler haftalık digest alır (events:notify-subscribers).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_city_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email')->index();
            $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();
            $table->string('locale', 5)->default('tr');
            $table->string('confirm_token', 48)->nullable();
            $table->string('unsubscribe_token', 48)->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamp('last_notified_at')->nullable();
            $table->string('source', 50)->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();

            $table->unique(['email', 'city_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_city_subscriptions');
    }
};
