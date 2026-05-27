<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legal_pages', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique()->comment('privacy, terms, cookies, impressum, disclaimer');
            $table->json('titles')->comment('Per-locale title: {"tr":"...","en":"...","de":"..."}');
            $table->json('descriptions')->nullable()->comment('Per-locale meta description');
            $table->json('bodies')->comment('Per-locale markdown body');
            $table->date('effective_date')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_pages');
    }
};
