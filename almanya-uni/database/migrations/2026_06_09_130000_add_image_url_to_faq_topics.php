<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a hero image column to faq_topics and seed per-topic stock photos.
     *
     * Photos: Pexels (free for commercial use, no attribution required),
     * stored locally at public/img/faq-topics/{slug}.jpg.
     *
     * Idempotent value-set uses DB::table()->update() (NOT Eloquent save(),
     * which would trigger Scout indexing). Topics without a verified local
     * image are left null → gradient fallback in the view.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('faq_topics', 'image_url')) {
            Schema::table('faq_topics', function (Blueprint $table) {
                $table->string('image_url')->nullable()->after('color');
            });
        }

        // Only slugs whose image was downloaded & verified (>20KB, valid JPEG).
        $slugs = [
            'vize',
            'dil',
            'master',
            'uni-assist',
            'studienkolleg',
            'yurt',
            'para',
            'sehir',
            'is',
            'sigorta',
            'randevu',
            'anmeldung',
            'burs',
            'denklik',
        ];

        foreach ($slugs as $slug) {
            DB::table('faq_topics')
                ->where('slug', $slug)
                ->update(['image_url' => "/img/faq-topics/{$slug}.jpg"]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('faq_topics', 'image_url')) {
            Schema::table('faq_topics', function (Blueprint $table) {
                $table->dropColumn('image_url');
            });
        }
    }
};
