<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** cities'e MLP Tablo 5-1 detay kolonları: Kaltmiete(30m²), WG-Zimmer(warm/kalt), 3-yıl endeks. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            foreach ([
                'student_rent_kalt30' => 'Kaltmiete 30m² öğrenci örnek-konutu (€)',
                'student_rent_wg_warm' => 'WG-Zimmer 20m² Warmmiete (€)',
                'student_rent_wg_kalt' => 'WG-Zimmer 20m² Kaltmiete (€)',
            ] as $col => $cmt) {
                if (! Schema::hasColumn('cities', $col)) {
                    $table->unsignedSmallInteger($col)->nullable()->comment($cmt);
                }
            }
            if (! Schema::hasColumn('cities', 'student_rent_index_3yr')) {
                $table->decimal('student_rent_index_3yr', 4, 1)->nullable()
                    ->comment('Studentenwohnpreisindex 3-yıl ort. yıllık değişim (%)');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            foreach (['student_rent_kalt30', 'student_rent_wg_warm', 'student_rent_wg_kalt', 'student_rent_index_3yr'] as $col) {
                if (Schema::hasColumn('cities', $col)) $table->dropColumn($col);
            }
        });
    }
};
