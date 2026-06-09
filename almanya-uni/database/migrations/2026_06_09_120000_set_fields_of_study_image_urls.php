<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Set field-of-study card images.
     *
     * Idempotent: uses DB::table()->update() (NOT Eloquent save(), which would
     * trigger Scout indexing). Safe to re-run.
     */
    public function up(): void
    {
        $tbl = 'fields_of_study';

        // Only slugs whose image was successfully downloaded & verified
        // (public/img/fields/{slug}.jpg). Stock photos, Unsplash free license.
        $slugs = [
            'muhendislik',
            'bilisim',
            'matematik-doga',
            'tip-saglik',
            'hukuk-ekonomi',
            'sosyal-bilimler',
            'sanat-tasarim',
            'dil-kultur',
            'tarim-ormancilik',
            'veteriner-spor',
        ];

        foreach ($slugs as $slug) {
            DB::table($tbl)
                ->where('slug', $slug)
                ->update(['image_url' => "/img/fields/{$slug}.jpg"]);
        }
    }

    public function down(): void
    {
        $tbl = 'fields_of_study';

        $slugs = [
            'muhendislik',
            'bilisim',
            'matematik-doga',
            'tip-saglik',
            'hukuk-ekonomi',
            'sosyal-bilimler',
            'sanat-tasarim',
            'dil-kultur',
            'tarim-ormancilik',
            'veteriner-spor',
        ];

        foreach ($slugs as $slug) {
            DB::table($tbl)
                ->where('slug', $slug)
                ->where('image_url', "/img/fields/{$slug}.jpg")
                ->update(['image_url' => null]);
        }
    }
};
