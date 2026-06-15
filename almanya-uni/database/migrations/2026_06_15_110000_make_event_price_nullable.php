<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * price_eur nullable — dış kaynaklı (Ticketmaster) etkinliklerin bir kısmında bilet
 * fiyatı API'de yok. null = "fiyat bilinmiyor" (yanlışlıkla "Ücretsiz" gösterilmesini önler).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->decimal('price_eur', 8, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        // Geri alırken null'ları 0'a çek, sonra NOT NULL'a döndür.
        \Illuminate\Support\Facades\DB::table('events')->whereNull('price_eur')->update(['price_eur' => 0]);
        Schema::table('events', function (Blueprint $table) {
            $table->decimal('price_eur', 8, 2)->default(0)->nullable(false)->change();
        });
    }
};
