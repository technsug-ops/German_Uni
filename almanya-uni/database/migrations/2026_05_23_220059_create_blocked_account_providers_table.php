<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blocked_account_providers', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('logo_url')->nullable();
            $table->string('website_url')->nullable();
            $table->string('affiliate_url')->nullable();

            $table->enum('type', ['fintech', 'traditional_bank'])->default('fintech');
            $table->string('backend_bank')->nullable();

            $table->decimal('setup_fee_eur', 8, 2)->nullable();
            $table->decimal('monthly_fee_eur', 8, 2)->nullable();
            $table->decimal('yearly_fee_eur', 8, 2)->nullable();

            $table->unsignedSmallInteger('activation_days_min')->nullable();
            $table->unsignedSmallInteger('activation_days_max')->nullable();

            $table->boolean('combo_insurance')->default(false);
            $table->string('insurance_provider_name')->nullable();
            $table->decimal('insurance_monthly_eur', 8, 2)->nullable();

            $table->unsignedInteger('monthly_withdrawal_limit_eur')->nullable();
            $table->unsignedInteger('required_yearly_deposit_eur')->nullable();

            $table->boolean('has_mobile_app')->default(false);
            $table->boolean('bafin_licensed')->default(false);
            $table->json('supported_languages')->nullable();

            $table->text('description')->nullable();
            $table->longText('description_long')->nullable();
            $table->json('pros')->nullable();
            $table->json('cons')->nullable();
            $table->json('features')->nullable();
            $table->text('visa_recognition_note')->nullable();
            $table->text('turkish_students_note')->nullable();

            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamp('last_verified_at')->nullable();
            $table->timestamps();

            $table->index(['is_published', 'sort_order']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocked_account_providers');
    }
};
