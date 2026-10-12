<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Genel slug yönlendirme tablosu (blog_redirects yalnız yazılar içindi): alan, FAQ konusu, blog kategorisi,
 * ileride FAQ slug'ları. Eski slug → yeni slug; denetleyiciler bulamadığı slug için buraya bakıp 301 döner.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('slug_redirects')) {
            return;
        }
        Schema::create('slug_redirects', function (Blueprint $table) {
            $table->id();
            $table->string('entity', 32);      // field | faq_topic | category | faq
            $table->string('from_slug', 191);
            $table->string('to_slug', 191);
            $table->timestamps();
            $table->unique(['entity', 'from_slug']);
            $table->index(['entity', 'to_slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slug_redirects');
    }
};
