<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_engagements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->string('session_id', 64)->index();
            $table->unsignedTinyInteger('scroll_depth')->default(0); // 0-100 (max ulaşılan %)
            $table->unsignedSmallInteger('seconds')->default(0);     // sayfada geçen toplam saniye
            $table->boolean('completed')->default(false);            // %90+ scroll = okundu sayılır
            $table->timestamps();

            $table->unique(['post_id', 'session_id']); // session başına tek kayıt (güncellenir)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_engagements');
    }
};
