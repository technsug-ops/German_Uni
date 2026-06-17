<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Blog slug'ları Türkçe'den İngilizce'ye geçirilirken eski URL'ler 404 vermesin diye
 * eski→yeni slug haritası. BlogController@show, slug bulamazsa burayı kontrol edip 301 atar.
 * (SEO/indeks korunur; Google yeni URL'i 301 üzerinden devralır.)
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('blog_redirects')) {
            return;
        }
        Schema::create('blog_redirects', function (Blueprint $table) {
            $table->id();
            $table->string('from_slug')->unique();
            $table->string('to_slug');
            $table->string('locale', 5)->default('tr');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_redirects');
    }
};
