<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Şehirlere admin'den yönetilebilir medya: galeri görselleri (upload) + video.
 * Hero artık ilk galeri görselini kullanır (güvenilir self-host); bozuk Wikimedia
 * landmark havuzuna bağımlı değil. Admin yöneticisi zamanla doldurur.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->json('gallery_images')->nullable()->after('image_url');
            $table->string('video_url')->nullable()->after('gallery_images');
        });
    }

    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn(['gallery_images', 'video_url']);
        });
    }
};
