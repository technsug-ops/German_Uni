<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Yüklenen fotoğraflara EK olarak dış resim URL'leri (admin'den girilir).
 * galleryUrls() ikisini birleştirir (önce yüklenenler, sonra URL'ler).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->json('gallery_image_urls')->nullable()->after('gallery_images');
        });
    }

    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn('gallery_image_urls');
        });
    }
};
