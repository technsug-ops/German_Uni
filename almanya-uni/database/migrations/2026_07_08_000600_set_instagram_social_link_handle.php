<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Instagram social link'i @applytogerman handle'ına günceller (seed'deki almanyauni yerine).
 * Idempotent: platform='instagram' satırını update eder; yoksa ekler.
 */
return new class extends Migration
{
    private string $url = 'https://instagram.com/applytogerman';

    public function up(): void
    {
        if (DB::table('social_links')->where('platform', 'instagram')->exists()) {
            DB::table('social_links')->where('platform', 'instagram')->update([
                'url'        => $this->url,
                'is_active'  => true,
                'updated_at' => now(),
            ]);
        } else {
            DB::table('social_links')->insert([
                'platform'   => 'instagram',
                'label'      => 'Instagram',
                'url'        => $this->url,
                'group'      => 'primary',
                'is_active'  => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('social_links')->where('platform', 'instagram')->update([
            'url'        => 'https://instagram.com/almanyauni',
            'updated_at' => now(),
        ]);
    }
};
