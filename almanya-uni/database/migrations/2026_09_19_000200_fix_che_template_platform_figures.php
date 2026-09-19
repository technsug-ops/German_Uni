<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * CHE mektubundaki platform rakamları gerçek verilerle güncellendi.
 *
 * Şablon taslak yazılırken "rund 480 Hochschulen und etwa 7.000 Studiengänge" deniyordu;
 * gerçek sayılar (2026-09): 463 aktif üniversite, 14.522 aktif program. Veri lisansı
 * istediğimiz bir kuruma yanlış — üstelik kendi ölçeğimizi YARIYA indiren — rakam
 * göndermek hem hatalı hem de gereksiz zayıf bir başvuru olurdu.
 *
 * Seed migration'ı prod'da çalıştığı için şablon orada duruyor; bu migration metni
 * yerinde düzeltir (idempotent: eski ifade yoksa dokunmaz).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('email_templates')) {
            return;
        }

        $row = DB::table('email_templates')->where('key', 'data-request-che-de')->first();
        if (! $row) {
            return;
        }

        $fixed = str_replace(
            '480 Hochschulen und etwa 7.000 Studiengänge',
            '460 Hochschulen und über 14.000 Studiengänge',
            (string) $row->body
        );

        if ($fixed !== $row->body) {
            DB::table('email_templates')->where('id', $row->id)->update([
                'body'       => $fixed,
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        //
    }
};
