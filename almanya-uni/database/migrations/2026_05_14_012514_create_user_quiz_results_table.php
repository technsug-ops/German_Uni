<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_quiz_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('quiz_type', 40)->index();
            $table->json('answers');
            $table->json('result')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'quiz_type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_quiz_results');
    }
};
