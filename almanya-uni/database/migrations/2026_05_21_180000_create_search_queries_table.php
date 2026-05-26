<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('search_queries', function (Blueprint $table) {
            $table->id();
            $table->string('query', 191)->index();           // normalize (lowercase) sorgu
            $table->string('query_raw', 255);                  // kullanıcının yazdığı orijinal
            $table->unsignedInteger('results_count')->default(0); // toplam sonuç (0 = içerik fırsatı!)
            $table->json('breakdown')->nullable();             // tip bazında sayı (uni/program/...)
            $table->string('session_id', 64)->nullable()->index();
            $table->unsignedSmallInteger('took_ms')->nullable();
            $table->timestamp('created_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_queries');
    }
};
