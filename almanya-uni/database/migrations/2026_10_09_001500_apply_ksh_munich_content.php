<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * KSH München'in içerik bloklarını (tr/en/de) yükler — 2026_10_09_001300'de HM verisiyle üretilmiş bloklar temizlenmişti.
 *
 * Veri: database/data/university-content/ksh-muenchen-2026-10-09.json. Lokalde universities:enrich ile resmî kaynaklar
 * (ksh-muenchen.de portrait / studienganguebersicht / internationale-vollstudierende) + de.wikipedia grounding'li üretildi,
 * sonra elle doğrulandı: C1 + uni-assist VPD (resmî sayfa), 3 fakülte, kuruluş 1971 ve ~2.400 öğrenci (Wikipedia, 2025)
 * doğru; uydurma kampüs linkleri, doğrulanamayan sayılar (program sayıları, HRK üyeliği, "60+ partner"), konu dışı forum
 * iddiaları ve alakasız SSS'ler çıkarıldı, kırık linkler kaldırıldı; en/de content:translate-blocks ile çevrildi.
 * Kullanıcı onayıyla yayınlandı. Yalnız bloklar hâlâ boşsa yazılır (idempotent).
 */
return new class extends Migration
{
    private const FILE = 'database/data/university-content/ksh-muenchen-2026-10-09.json';

    public function up(): void
    {
        if (! Schema::hasTable('universities') || ! is_file(base_path(self::FILE))) {
            return;
        }
        $d = json_decode(file_get_contents(base_path(self::FILE)), true);

        DB::table('universities')->where('slug', $d['slug'])->whereNull('content_blocks')->update([
            'content_blocks' => json_encode($d['content_blocks'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'content_blocks_en' => json_encode($d['content_blocks_en'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'content_blocks_de' => json_encode($d['content_blocks_de'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'image_url' => $d['image_url'],
            'student_count' => $d['student_count'],
            'last_enriched_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // İçerik verisi.
    }
};
