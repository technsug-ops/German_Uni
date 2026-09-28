<?php

use App\Services\FaqAtlas\FaqAtlasImporter;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

/**
 * SSS Kalite Atlası — 2026-09-28 denetim anlık görüntüsünün ilk içe aktarımı (forward-only).
 * Checksum/şema hatası → RuntimeException, hiçbir şey yazılmaz. Eşleşmeyen kayıtlar resolved=false ile yazılır ve
 * log'a düşer (tahmin yok). İkinci çalıştırma no-op. SSS kayıtlarına yazılmaz.
 * PHPUnit altında atlanır: test DB'si boş SSS tablosuyla kurulur; testler kendi veri setini içe aktarır.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (app()->runningUnitTests()) {
            return;
        }
        $r = app(FaqAtlasImporter::class)->import(resource_path('data/faq-atlas/atlas-2026-09-28.json'));
        Log::info('faq-atlas import', array_diff_key($r, ['unresolved' => 1, 'notes' => 1]) + ['unresolved_count' => count($r['unresolved'])]);
        if ($r['unresolved']) {
            Log::warning('faq-atlas import: eşleşmeyen kümeler', ['unresolved' => $r['unresolved']]);
        }
    }

    public function down(): void
    {
        // Bilinçli olarak boş: denetim anlık görüntüsü forward-only.
    }
};
