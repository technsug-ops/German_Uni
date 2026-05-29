<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('popups')) return;

        Schema::table('popups', function (Blueprint $t) {
            if (! Schema::hasColumn('popups', 'media_type')) {
                $t->enum('media_type', ['text', 'image', 'video'])
                    ->default('text')
                    ->after('theme')
                    ->comment('text | image | video — controls which media field is rendered');
            }
            if (! Schema::hasColumn('popups', 'video_url')) {
                $t->string('video_url', 500)->nullable()->after('image_url')
                    ->comment('YouTube/Vimeo embed URL or direct .mp4 path');
            }
            if (! Schema::hasColumn('popups', 'video_autoplay')) {
                $t->boolean('video_autoplay')->default(false)->after('video_url');
            }
            if (! Schema::hasColumn('popups', 'video_muted')) {
                $t->boolean('video_muted')->default(true)->after('video_autoplay')
                    ->comment('Autoplay zorunlu mute gerektirir — modern tarayıcı kuralı');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('popups')) return;
        Schema::table('popups', function (Blueprint $t) {
            foreach (['media_type', 'video_url', 'video_autoplay', 'video_muted'] as $col) {
                if (Schema::hasColumn('popups', $col)) $t->dropColumn($col);
            }
        });
    }
};
