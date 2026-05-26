<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email', 191)->unique();
            $table->string('name', 100)->nullable();
            $table->string('language', 5)->default('tr');         // tr / en / de
            $table->string('source', 50)->default('unknown');     // home / footer / blog / popup / about
            $table->string('referrer_url', 500)->nullable();
            $table->string('confirm_token', 64)->unique();         // double opt-in token
            $table->string('unsubscribe_token', 64)->unique();
            $table->timestamp('confirmed_at')->nullable();         // null → henüz doğrulanmadı
            $table->timestamp('unsubscribed_at')->nullable();
            $table->string('unsubscribe_reason', 255)->nullable();
            $table->string('ip_address', 45)->nullable();          // KVKK için tutulur
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();

            $table->index('language');
            $table->index('source');
            $table->index(['confirmed_at', 'unsubscribed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscribers');
    }
};
