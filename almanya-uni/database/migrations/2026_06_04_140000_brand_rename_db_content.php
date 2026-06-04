<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Marka: DB içeriğindeki "AlmanyaUni" → "ApplyToGerman (AlmanyaUni)".
 * Idempotent: önce NEW→OLD normalize, sonra OLD→NEW (çift sarmayı önler).
 * users.bio (yazar/kurucu bio) + posts (re-seed de düzeltir, bu belt).
 */
return new class extends Migration
{
    private string $old = 'AlmanyaUni';
    private string $new = 'ApplyToGerman (AlmanyaUni)';

    public function up(): void
    {
        $targets = [
            'users' => ['bio', 'headline'],
            'posts' => ['title', 'excerpt', 'content_md', 'content_html', 'meta_title', 'meta_description'],
        ];

        foreach ($targets as $table => $cols) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            foreach ($cols as $col) {
                if (! Schema::hasColumn($table, $col)) {
                    continue;
                }
                // idempotent: REPLACE(REPLACE(col, NEW, OLD), OLD, NEW)
                DB::table($table)
                    ->where($col, 'like', '%' . $this->old . '%')
                    ->update([$col => DB::raw(
                        'REPLACE(REPLACE(`' . $col . '`, '
                        . DB::getPdo()->quote($this->new) . ', '
                        . DB::getPdo()->quote($this->old) . '), '
                        . DB::getPdo()->quote($this->old) . ', '
                        . DB::getPdo()->quote($this->new) . ')'
                    )]);
            }
        }
    }

    public function down(): void
    {
        // Geri alma gereksiz (marka düzeltmesi).
    }
};
