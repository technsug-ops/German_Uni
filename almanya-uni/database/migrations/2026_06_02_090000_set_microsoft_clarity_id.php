<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Microsoft Clarity proje kimliğini ayarlar (boşsa). Panelden
 * (/admin → Ayarlar → Entegrasyonlar) değiştirilebilir. Çerez onayına bağlı yüklenir.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('settings')) return;
        if (! Setting::get('microsoft_clarity_id')) {
            Setting::set('microsoft_clarity_id', 'x0g83n2ju1', 'integrations');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('settings')) {
            Setting::set('microsoft_clarity_id', null, 'integrations');
        }
    }
};
