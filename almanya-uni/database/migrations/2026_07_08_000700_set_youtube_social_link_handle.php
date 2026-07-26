<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * YouTube social link'i @applytogerman kanalına günceller (seed'deki almanyauni yerine).
 * Idempotent: platform='youtube' satırını update eder; yoksa ekler.
 */
return new class extends Migration
{
    private string $url = 'https://youtube.com/@applytogerman';

    public function up(): void
    {
        if (DB::table('social_links')->where('platform', 'youtube')->exists()) {
            DB::table('social_links')->where('platform', 'youtube')->update([
                'url'        => $this->url,
                'is_active'  => true,
                'updated_at' => now(),
            ]);
        } else {
            DB::table('social_links')->insert([
                'platform'   => 'youtube',
                'label'      => 'YouTube',
                'url'        => $this->url,
                'group'      => 'primary',
                'is_active'  => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('social_links')->where('platform', 'youtube')->update([
            'url'        => 'https://youtube.com/@almanyauni',
            'updated_at' => now(),
        ]);
    }
};
