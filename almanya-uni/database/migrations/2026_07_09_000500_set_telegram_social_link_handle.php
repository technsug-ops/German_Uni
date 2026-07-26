<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Telegram social link'i gerçek kanala günceller (seed'deki almanyauni yerine).
 * Idempotent: platform='telegram' satırını update eder; yoksa ekler.
 */
return new class extends Migration
{
    private string $url = 'https://t.me/ApplyToGermania';

    public function up(): void
    {
        if (DB::table('social_links')->where('platform', 'telegram')->exists()) {
            DB::table('social_links')->where('platform', 'telegram')->update([
                'url'        => $this->url,
                'is_active'  => true,
                'updated_at' => now(),
            ]);
        } else {
            DB::table('social_links')->insert([
                'platform'   => 'telegram',
                'label'      => 'Telegram',
                'url'        => $this->url,
                'group'      => 'community',
                'is_active'  => true,
                'sort_order' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('social_links')->where('platform', 'telegram')->update([
            'url'        => 'https://t.me/almanyauni',
            'updated_at' => now(),
        ]);
    }
};
