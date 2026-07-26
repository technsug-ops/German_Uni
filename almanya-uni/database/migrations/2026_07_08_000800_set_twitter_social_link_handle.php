<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * X (Twitter) social link'i @applytogerman handle'ına günceller (seed'deki almanyauni yerine).
 * Idempotent: platform='twitter' satırını update eder; yoksa ekler.
 */
return new class extends Migration
{
    private string $url = 'https://x.com/applytogerman';

    public function up(): void
    {
        if (DB::table('social_links')->where('platform', 'twitter')->exists()) {
            DB::table('social_links')->where('platform', 'twitter')->update([
                'url'        => $this->url,
                'is_active'  => true,
                'updated_at' => now(),
            ]);
        } else {
            DB::table('social_links')->insert([
                'platform'   => 'twitter',
                'label'      => 'X (Twitter)',
                'url'        => $this->url,
                'group'      => 'primary',
                'is_active'  => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('social_links')->where('platform', 'twitter')->update([
            'url'        => 'https://x.com/almanyauni',
            'updated_at' => now(),
        ]);
    }
};
