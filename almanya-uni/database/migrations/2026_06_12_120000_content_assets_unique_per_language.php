<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * #12 Faz-1.1 (çoklu-dil storytelling): content_assets'te unique index
 * (content_brief_id, asset_type) idi → bir brief+tip için TEK asset, diller
 * çakışıyordu (Duplicate entry). Unique'i (content_brief_id, asset_type,
 * language) yap → her dil için ayrı asset (EN/DE infografikler üretilebilir).
 */
return new class extends Migration
{
    public function up(): void
    {
        // ÖNCE yeni (dilli) unique'i ekle — content_brief_id FK'si bir index ister;
        // yeni index content_brief_id ile BAŞLADIĞI için FK ona dayanabilir. SONRA
        // eski unique'i düşür. Ters sıra → FK eski index'e bağlı olduğundan DROP engellenir.
        if (! $this->indexExists('content_assets_brief_type_lang_unique')) {
            Schema::table('content_assets', function (Blueprint $table) {
                $table->unique(['content_brief_id', 'asset_type', 'language'], 'content_assets_brief_type_lang_unique');
            });
        }

        foreach (['content_assets_brief_type_unique', 'content_assets_content_brief_id_asset_type_unique'] as $idx) {
            if ($this->indexExists($idx)) {
                try {
                    DB::statement("ALTER TABLE content_assets DROP INDEX `{$idx}`");
                } catch (\Throwable $e) {
                    // FK hâlâ bağlıysa veya yoksa geç
                }
            }
        }
    }

    public function down(): void
    {
        try {
            DB::statement("ALTER TABLE content_assets DROP INDEX `content_assets_brief_type_lang_unique`");
        } catch (\Throwable $e) {
        }
    }

    private function indexExists(string $name): bool
    {
        return ! empty(DB::select(
            "SHOW INDEX FROM content_assets WHERE Key_name = ?",
            [$name]
        ));
    }
};
