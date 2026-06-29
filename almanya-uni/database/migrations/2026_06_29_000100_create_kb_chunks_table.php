<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * RAG chatbot bilgi tabanı — vektörlenmiş içerik parçaları.
 * Kaynak: FAQ + blog + program + üni + şehir. (doc/CHATBOT-RAG-PLAYBOOK.md)
 *
 * embedding: float32 little-endian, L2-normalize → benzerlik = nokta çarpımı.
 * content_hash: artımlı embed — içerik değişmediyse yeniden çağrı yapma.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kb_chunks', function (Blueprint $table) {
            $table->id();
            $table->string('source_type', 16);          // faq|post|program|university|city
            $table->unsignedBigInteger('source_id');
            $table->string('locale', 5)->default('tr');
            $table->unsignedInteger('chunk_index')->default(0);
            $table->string('title');                     // atıf/gösterim başlığı
            $table->string('url', 512);                  // public link (locale-aware)
            $table->longText('content');                 // embed edilen ham metin
            $table->unsignedInteger('token_estimate')->default(0);
            $table->binary('embedding')->nullable();     // float32 LE, L2-normalize
            $table->unsignedSmallInteger('dims')->default(0);
            $table->string('model', 40)->nullable();
            $table->char('content_hash', 64)->nullable();
            $table->timestamp('embedded_at')->nullable();
            $table->timestamps();

            $table->index(['source_type', 'source_id', 'locale', 'chunk_index'], 'kb_src_idx');
            $table->index(['source_type', 'locale'], 'kb_type_locale_idx');
            $table->index('content_hash', 'kb_hash_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kb_chunks');
    }
};
