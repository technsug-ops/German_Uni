<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Facebook social link URL'ini gerçek sayfaya günceller (seed'deki placeholder yerine).
 * Idempotent: platform='facebook' satırını update eder; yoksa ekler.
 */
return new class extends Migration
{
    private string $url = 'https://www.facebook.com/profile.php?id=61592307277353';

    public function up(): void
    {
        if (DB::table('social_links')->where('platform', 'facebook')->exists()) {
            DB::table('social_links')->where('platform', 'facebook')->update([
                'url'        => $this->url,
                'is_active'  => true,
                'updated_at' => now(),
            ]);
        } else {
            DB::table('social_links')->insert([
                'platform'   => 'facebook',
                'label'      => 'Facebook',
                'url'        => $this->url,
                'group'      => 'primary',
                'is_active'  => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('social_links')->where('platform', 'facebook')->update([
            'url'        => 'https://facebook.com/almanyauni',
            'updated_at' => now(),
        ]);
    }
};
