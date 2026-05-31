<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Generic key-value ayar deposu.
 *
 * İlk kullanım: pazarlama/analitik entegrasyonları (GA4, Google Ads, GTM,
 * Search Console, Meta Pixel, TikTok Pixel). İleride başka global ayarlar da
 * buraya eklenebilir — `group` ile gruplanır.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->string('group')->default('general')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
