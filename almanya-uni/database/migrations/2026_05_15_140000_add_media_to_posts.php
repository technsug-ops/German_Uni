<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('featured_image_caption', 255)->nullable()->after('featured_image');
            $table->string('audio_url', 500)->nullable()->after('featured_image_caption');
            $table->unsignedInteger('audio_duration_seconds')->nullable()->after('audio_url');
            $table->string('video_url', 500)->nullable()->after('audio_duration_seconds');
            $table->json('gallery_images')->nullable()->after('video_url');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn([
                'featured_image_caption',
                'audio_url',
                'audio_duration_seconds',
                'video_url',
                'gallery_images',
            ]);
        });
    }
};
