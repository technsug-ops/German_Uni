<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('city_cost_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('tier', 20)->index(); // very_expensive, expensive, mid, affordable
            $table->unsignedSmallInteger('rent_wg');           // shared apartment room (EUR/month)
            $table->unsignedSmallInteger('rent_studio');        // 1-zimmer studio (EUR/month)
            $table->unsignedSmallInteger('rent_apartment');     // 2-zimmer apartment (EUR/month)
            $table->unsignedSmallInteger('food');               // groceries+eating out (EUR/month)
            $table->unsignedSmallInteger('transport');          // semester ticket avg (EUR/month)
            $table->unsignedSmallInteger('utilities');          // internet + electricity + phone
            $table->unsignedSmallInteger('health_insurance');   // student tariff (EUR/month)
            $table->unsignedSmallInteger('entertainment');      // gym + cinema + social (EUR/month)
            $table->unsignedSmallInteger('misc');               // clothing + supplies (EUR/month)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('city_cost_data');
    }
};
