<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * #12 storytelling: ContentAsset::TYPES yeni formatlarla genişletildi
 * (infographic_data, faq_page, quiz, social_carousel, email_sequence) ama
 * content_assets.asset_type ENUM eski 11 tipte kalmıştı → yeni tip insert
 * "Data truncated" hatası veriyordu. ENUM → VARCHAR(32) (gelecekte her yeni
 * format için ALTER gerekmez; geçerlilik model TYPES + validation katmanında).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE content_assets MODIFY asset_type VARCHAR(32) NOT NULL");
    }

    public function down(): void
    {
        // ENUM'a geri dönmek riskli (yeni tip satırları truncate olur) — no-op.
    }
};
