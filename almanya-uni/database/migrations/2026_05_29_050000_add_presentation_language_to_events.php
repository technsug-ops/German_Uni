<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Events: presentation_language kolonu — etkinliğin hangi dilde sunulduğu.
 * Değerler: 'tr', 'en', 'de', 'multi' (örn. Türkçe + Almanca karışık)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('presentation_language', 16)->nullable()->default('tr')->after('duration_minutes');
        });

        // Mevcut Mentor Webinar (Halil host) → Türkçe
        DB::table('events')->where('host', 'Halil')->update(['presentation_language' => 'tr']);
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('presentation_language');
        });
    }
};
