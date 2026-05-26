<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_assets', function (Blueprint $table) {
            $table->json('media')->nullable()->after('body_html');
            $table->string('video_path', 500)->nullable()->after('media');
            $table->string('audio_path', 500)->nullable()->after('video_path');
        });
    }

    public function down(): void
    {
        Schema::table('content_assets', function (Blueprint $table) {
            $table->dropColumn(['media', 'video_path', 'audio_path']);
        });
    }
};
