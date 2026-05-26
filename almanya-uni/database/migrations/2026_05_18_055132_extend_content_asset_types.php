<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ENUM'u genişlet (eski video_script + yeni türler birlikte; data migration için)
        DB::statement("ALTER TABLE content_assets MODIFY asset_type ENUM(
            'blog', 'video_script', 'youtube_long', 'youtube_short',
            'tiktok', 'instagram', 'twitter', 'linkedin', 'pinterest',
            'podcast', 'newsletter', 'visual_brief'
        ) NOT NULL");

        DB::table('content_assets')->where('asset_type', 'video_script')->update(['asset_type' => 'youtube_long']);

        // video_script'i enum'dan çıkar
        DB::statement("ALTER TABLE content_assets MODIFY asset_type ENUM(
            'blog', 'youtube_long', 'youtube_short',
            'tiktok', 'instagram', 'twitter', 'linkedin', 'pinterest',
            'podcast', 'newsletter', 'visual_brief'
        ) NOT NULL");
    }

    public function down(): void
    {
        DB::table('content_assets')->whereIn('asset_type', ['youtube_short', 'linkedin', 'pinterest', 'newsletter'])->delete();
        DB::statement("ALTER TABLE content_assets MODIFY asset_type ENUM(
            'blog', 'video_script', 'tiktok', 'instagram', 'twitter', 'podcast', 'visual_brief'
        ) NOT NULL");
    }
};
