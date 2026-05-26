<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Provider tablosu: Studierendenwerk + Private chain + Platform
        Schema::create('housing_providers', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('type', 30)->index(); // studierendenwerk | private_chain | platform
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('logo_url')->nullable();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('price_min')->nullable(); // EUR/ay
            $table->unsignedSmallInteger('price_max')->nullable();
            $table->json('cities')->nullable();        // string[] of city names where available
            $table->json('features')->nullable();      // ["fitness","internet_dahil","movbliyali",...]
            $table->unsignedSmallInteger('total_capacity')->nullable(); // STW kapasite
            $table->string('waiting_period')->nullable(); // "1-2 sem", "3-7 sem"
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // 2) cities tablosuna STW + ortalama fiyat alanları ekle
        Schema::table('cities', function (Blueprint $table) {
            $table->string('stw_name')->nullable()->after('population');
            $table->string('stw_website')->nullable()->after('stw_name');
            $table->unsignedSmallInteger('stw_capacity')->nullable()->after('stw_website');
            $table->string('stw_waiting')->nullable()->after('stw_capacity'); // "2-3 sem"
            $table->unsignedSmallInteger('avg_rent_min')->nullable()->after('stw_waiting');
            $table->unsignedSmallInteger('avg_rent_max')->nullable()->after('avg_rent_min');
            $table->json('private_chain_slugs')->nullable()->after('avg_rent_max'); // provider slugs
        });
    }

    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn([
                'stw_name', 'stw_website', 'stw_capacity', 'stw_waiting',
                'avg_rent_min', 'avg_rent_max', 'private_chain_slugs',
            ]);
        });
        Schema::dropIfExists('housing_providers');
    }
};
