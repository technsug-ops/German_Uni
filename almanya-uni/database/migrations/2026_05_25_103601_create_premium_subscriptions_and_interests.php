<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Aktif premium abonelikler
        Schema::create('premium_subscriptions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('tier', 20); // premium, pro
            $t->string('status', 20)->default('active'); // active, cancelled, expired, trial
            $t->timestamp('started_at')->useCurrent();
            $t->timestamp('ends_at')->nullable();
            $t->string('payment_provider', 30)->nullable(); // stripe, manual
            $t->string('payment_id', 100)->nullable();
            $t->decimal('amount_eur', 8, 2)->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();

            $t->index(['user_id', 'status']);
        });

        // Premium ilgi/beklemede formu (Stripe açılana kadar lead toplama)
        Schema::create('premium_interests', function (Blueprint $t) {
            $t->id();
            $t->string('email', 150);
            $t->string('name', 100)->nullable();
            $t->string('tier_interest', 20); // premium, pro, undecided
            $t->string('source_page', 200)->nullable(); // /pricing, /journey, etc.
            $t->string('locale', 5)->default('tr');
            $t->string('country', 50)->nullable();
            $t->text('note')->nullable();
            $t->boolean('contacted')->default(false);
            $t->timestamp('contacted_at')->nullable();
            $t->timestamps();

            $t->index(['email', 'tier_interest']);
            $t->index('contacted');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('premium_subscriptions');
        Schema::dropIfExists('premium_interests');
    }
};
