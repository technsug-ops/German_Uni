<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * blocked_account_providers.backend_bank içindeki Türkçe "eski" kelimesi /en /de'de
 * sızıyordu (ör. "UniCredit (eski Aion Bank)"). Banka adı global olarak alakalı —
 * yalnızca kelime Türkçeydi → nötr "ex-" ile değiştir. Idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('blocked_account_providers')) {
            return;
        }

        DB::table('blocked_account_providers')
            ->where('backend_bank', 'like', '%eski %')
            ->update(['backend_bank' => DB::raw("REPLACE(backend_bank, 'eski ', 'ex-')")]);
    }

    public function down(): void
    {
        // Geri alma gereksiz (kozmetik dil düzeltmesi).
    }
};
