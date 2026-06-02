<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * source_url / image_url VARCHAR(600) → TEXT.
 * Google News RSS makale URL'leri (redirect token'ları) 700+ karakter olabiliyor
 * → "1406 Data too long for column 'source_url'". Index url_hash'te, bu kolonlarda
 * değil; TEXT'e genişletmek güvenli. URL'ler bütün gerekli (sonradan fetch).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('news_candidates')) return;

        Schema::table('news_candidates', function (Blueprint $table) {
            $table->text('source_url')->nullable()->change();
            $table->text('image_url')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('news_candidates')) return;

        Schema::table('news_candidates', function (Blueprint $table) {
            $table->string('source_url', 600)->nullable()->change();
            $table->string('image_url', 600)->nullable()->change();
        });
    }
};
