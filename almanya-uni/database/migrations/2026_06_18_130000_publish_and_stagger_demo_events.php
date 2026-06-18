<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Kendi (source=manual) topluluk etkinliklerimiz pasif (taslak) ve hepsi tek tarihe
 * kümelenmişti → /events boş görünüyordu. Hepsini yayına al + tarihleri bugünden
 * itibaren 2'şer gün arayla ileriye yay (akşam 18:30) ki sayfa dolu ve kalıcı kalsın.
 * Dış konserler (ticketmaster) bu migration'dan ETKİLENMEZ.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('events')) {
            return;
        }

        $ids = DB::table('events')->where('source', 'manual')->orderBy('id')->pluck('id');

        $i = 0;
        foreach ($ids as $id) {
            $start = now()->startOfDay()->addDays(3 + $i * 2)->setTime(18, 30);

            DB::table('events')->where('id', $id)->update([
                'starts_at'  => $start->toDateTimeString(),
                'ends_at'    => $start->copy()->addHours(2)->toDateTimeString(),
                'is_active'  => true,
                'updated_at' => now(),
            ]);

            $i++;
        }
    }

    public function down(): void
    {
        // Demo etkinlik yayını + tarih kaydırma — orijinal tarihler saklanmadı, geri alma no-op.
    }
};
