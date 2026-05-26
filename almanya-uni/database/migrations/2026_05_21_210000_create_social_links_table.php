<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            $table->string('platform', 30)->unique();  // icon_key: instagram/twitter/youtube/...
            $table->string('label', 40);
            $table->string('url')->nullable();
            $table->string('group', 20)->default('primary'); // primary | community
            $table->boolean('is_active')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed — 8 platform. URL'i olanlar aktif başlar, boş olanlar admin doldurana dek pasif.
        $now = now();
        $rows = [
            ['instagram', 'Instagram',   'https://instagram.com/almanyauni', 'primary',   1],
            ['twitter',   'X (Twitter)', 'https://x.com/almanyauni',         'primary',   2],
            ['youtube',   'YouTube',     'https://youtube.com/@almanyauni',  'primary',   3],
            ['spotify',   'Spotify',     null,                                'primary',   4],
            ['facebook',  'Facebook',    'https://facebook.com/almanyauni',  'primary',   5],
            ['whatsapp',  'WhatsApp',    null,                                'community', 6],
            ['discord',   'Discord',     null,                                'community', 7],
            ['telegram',  'Telegram',    'https://t.me/almanyauni',          'community', 8],
        ];
        foreach ($rows as [$platform, $label, $url, $group, $sort]) {
            DB::table('social_links')->insert([
                'platform'   => $platform,
                'label'      => $label,
                'url'        => $url,
                'group'      => $group,
                'is_active'  => $url !== null, // url varsa aktif
                'sort_order' => $sort,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('social_links');
    }
};
