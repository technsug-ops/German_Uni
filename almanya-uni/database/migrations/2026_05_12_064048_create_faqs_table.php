<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faq_topic_id')->constrained()->cascadeOnDelete();
            $table->string('question');
            $table->string('slug')->unique();
            $table->longText('answer_md')->nullable();    // markdown source
            $table->longText('answer_html')->nullable();  // rendered cache
            $table->string('intent', 32)->nullable();     // nasil | hangi | ne-kadar | ne-zaman | var-mi | bilgi
            $table->unsignedSmallInteger('answer_minutes')->default(0); // reading time (0 = unanswered stub)
            $table->boolean('has_answer')->default(false);
            $table->boolean('is_featured')->default(false); // sayfa içi öne çıkar
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['faq_topic_id', 'is_published', 'sort_order']);
            $table->index(['has_answer', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};
