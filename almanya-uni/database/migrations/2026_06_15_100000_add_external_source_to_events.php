<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Etkinliklere dış-kaynak (Ticketmaster) importu için alan + "Kültür & Konser"
 * kategorisi ekler. source+external_id unique → import idempotent (dedup).
 * city_id → şehir bazlı bildirim/filtre için (location_city string'i korunur).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('source', 32)->default('manual')->after('type')->index();
            $table->string('external_id')->nullable()->after('source');
            $table->foreignId('city_id')->nullable()->after('location_city')
                ->constrained('cities')->nullOnDelete();
            // Aynı dış etkinlik iki kez girilmesin. Manuel kayıtlarda external_id NULL →
            // MySQL unique index birden çok NULL'a izin verir, çakışma olmaz.
            $table->unique(['source', 'external_id']);
        });

        // "Kültür & Konser" kategorisi (Event::TYPES'taki culture tipleriyle eşleşir).
        if (! DB::table('event_categories')->where('slug', 'culture')->exists()) {
            DB::table('event_categories')->insert([
                'slug'        => 'culture',
                'name_tr'     => 'Kültür & Konser',
                'name_en'     => 'Culture & Concerts',
                'name_de'     => 'Kultur & Konzerte',
                'icon'        => '🎵',
                'color'       => '#E11D48',
                'description' => 'Konserler, festivaller, tiyatro ve sergiler — Almanya genelinde.',
                'sort_order'  => 8,
                'is_active'   => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropUnique(['source', 'external_id']);
            $table->dropConstrainedForeignId('city_id');
            $table->dropColumn(['source', 'external_id']);
        });
        // Kategori non-destructive bırakılır (içeriğe bağlı olabilir).
    }
};
