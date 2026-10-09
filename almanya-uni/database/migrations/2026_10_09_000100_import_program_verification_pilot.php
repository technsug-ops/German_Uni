<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Pilot doğrulama kayıtlarını (20 program, resmî kaynak, 2026-10-02) canlıya yükler.
 *
 * Prod'da SSH/artisan yok; bu yüzden `programs:verification-import` bu migration'dan çağrılır.
 * Komutun güvenceleri aynen geçerli: eşleştirme import kaynağı + dış kimlik + name_de + derece +
 * üniversite adı ile yapılır (yerel ID'ye güvenilmez), belirsiz/farklı kayıt atlanır, mevcut program
 * verisi değişmez, tekrar çalıştırmak aynı sonucu verir. Boş test DB'sinde her satır atlanır.
 * Sonuç tablosu laravel.log'a yazılır.
 */
return new class extends Migration
{
    private const FILE = 'database/data/program-verification/pilot-2026-10-02.json';

    public function up(): void
    {
        if (! Schema::hasTable('programs') || ! Schema::hasTable('program_verifications') || ! is_file(base_path(self::FILE))) {
            return;
        }

        try {
            Artisan::call('programs:verification-import', ['file' => self::FILE]);
            Log::info('program verification pilot import', ['output' => Artisan::output()]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public function down(): void
    {
        // Doğrulama kayıtları veri; geri alma elle ve bilinçli yapılır.
    }
};
