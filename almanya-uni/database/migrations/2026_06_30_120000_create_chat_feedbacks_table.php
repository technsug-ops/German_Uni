<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Chat 👍/👎 geri bildirim + kalite günlüğü (RAG Faz 5).
 * Her oylanan tur burada saklanır → kötü cevapları görüp retrieval/prompt'u iyileştirmek için.
 * (doc/CHATBOT-RAG-PLAYBOOK.md §10)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('vote');                 // 1 = 👍, -1 = 👎
            $table->text('question');
            $table->longText('answer');
            $table->string('confidence', 8)->nullable(); // high | low
            $table->float('top_score')->nullable();      // en iyi retrieval skoru
            $table->json('sources')->nullable();         // [{title,url}]
            $table->string('locale', 5)->default('tr');
            $table->string('ip_hash', 64)->nullable();   // gizlilik: ham IP saklanmaz
            $table->string('user_agent', 255)->nullable();
            $table->string('status', 16)->default('new'); // new | reviewed | fixed
            $table->timestamps();

            $table->index(['vote', 'created_at']);
            $table->index(['status', 'vote']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_feedbacks');
    }
};
