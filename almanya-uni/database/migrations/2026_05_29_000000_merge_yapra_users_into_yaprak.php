<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * "Yapra Test" → "Yaprak" + #2 (editor) ve #3 (admin) → #1 (ana) merge.
 *
 * Production'da #1 = Yapra Test, #2 = Yapra (editor), #3 = Yapra (admin) varsa
 * hepsi tek bir Yaprak'a birleşir. Idempotent: zaten merged ise hiçbir şey yapmaz.
 *
 * NOT: User ID'leri production'da farklı olabilir. Bu migration email tabanlı
 * eşleştirme yapar; ID assumption'ı yok.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1) Ana hedef user: Yapra Test (email yapra-test1@gmail.com) → "Yaprak" yap
        $main = DB::table('users')->where('email', 'yapra-test1@gmail.com')->first();
        if (! $main) {
            // Fallback: ID=1 olan user
            $main = DB::table('users')->where('id', 1)->first();
            if (! $main) {
                echo "Yaprak target user not found, skipping merge.\n";
                return;
            }
        }
        $mainId = $main->id;

        DB::table('users')->where('id', $mainId)->update([
            'name' => 'Yaprak',
            'slug' => Schema::hasColumn('users', 'slug') ? 'yaprak' : null,
            'role_label'  => $main->role_label  ?: 'Kurucu & Baş Editör',
            'is_author'   => true,
            'bio'         => $main->bio ?: "AlmanyaUni'in kurucusu. Türk öğrencilerin Almanya yolculuğunda doğru ve güncel bilgiye erişimini sağlamak için 2026'da bu platformu kurdu. Resmi kaynaklardan derlenmiş, topluluk deneyimleriyle zenginleştirilmiş içerikleri yazıyor.",
            'updated_at'  => now(),
        ]);

        // 2) Diğer "Yapra" user'larını bul (Yaprak ana user hariç) — merge et + sil
        $duplicates = DB::table('users')
            ->where('id', '!=', $mainId)
            ->where(function ($q) {
                $q->where('name', 'Yapra')
                  ->orWhere('name', 'Yapra Test')
                  ->orWhere('email', 'editor@almanyauni.com')
                  ->orWhere('email', 'admin@almanyauni.de');
            })
            ->pluck('id')
            ->all();

        if (empty($duplicates)) {
            return;
        }

        // İlişkili tabloları taşı (her birinde user_id varsa)
        $tablesUserIdCols = [
            'posts'         => ['user_id'],
            'events'        => ['host_user_id'],
            'post_comments' => ['user_id', 'approved_by'],
            'event_rsvps'   => ['user_id'],
            'favorites'     => ['user_id'],
            'contributions' => ['user_id'],
        ];

        foreach ($tablesUserIdCols as $table => $cols) {
            if (! Schema::hasTable($table)) continue;
            foreach ($cols as $col) {
                if (! Schema::hasColumn($table, $col)) continue;
                DB::table($table)->whereIn($col, $duplicates)->update([$col => $mainId]);
            }
        }

        // Duplicate user'ları sil
        DB::table('users')->whereIn('id', $duplicates)->delete();
    }

    public function down(): void
    {
        // Geri alma yok — merge geri alınamaz (post sahipleri belirsiz olur).
    }
};
