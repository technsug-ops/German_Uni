<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->boolean('is_official')->default(false)->after('is_active');
            $table->index(['is_official', 'is_active']);
        });

        // HRK üye = resmi onaylı; veya HS-Nummer mevcut = HRK kaydı var; veya partner_id var.
        DB::statement('UPDATE universities SET is_official = 1
                       WHERE hrk_member = 1 OR hs_nummer IS NOT NULL OR partner_id IS NOT NULL');
    }

    public function down(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->dropIndex(['is_official', 'is_active']);
            $table->dropColumn('is_official');
        });
    }
};
