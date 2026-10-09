<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * 2026_10_09_000400 DAAD "Master of Public Policy" kaydını (Willy Brandt School, Universität Erfurt'un programı) çift
 * olduğu için pasifleştirmiş ama FH Erfurt'ta bırakmıştı. Pasif program sayfası artık aktif kopyasına 301 verdiği için
 * (Program::activeCounterpart, aynı üniversite şartı) kopya bulunamıyor ve sayfa 410 dönüyordu → kaydı Universität
 * Erfurt'a bağla; sayfa oradaki aktif kayda yönlenir. Yalnız kayıt hâlâ beklenen durumdaysa yazılır.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('programs')) {
            return;
        }

        $from = DB::table('universities')->where('name_de', 'Fachhochschule Erfurt')->pluck('id');
        $to = DB::table('universities')->where('name_de', 'Universität Erfurt')->pluck('id');
        if ($from->count() !== 1 || $to->count() !== 1) {
            return;
        }

        DB::table('programs')
            ->where('source', 'daad')->where('source_id', '3726')
            ->where('name_de', 'Master of Public Policy')->where('degree', 'master')
            ->where('is_active', false)->where('university_id', $from->first())
            ->update(['university_id' => $to->first(), 'updated_at' => now()]);
    }

    public function down(): void
    {
        // Veri düzeltmesi.
    }
};
