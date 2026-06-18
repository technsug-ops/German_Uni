<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** cities'e öğrenci kira göstergesi alanları (MLP Studentenwohnreport / Value AG). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            if (! Schema::hasColumn('cities', 'student_rent_warm30')) {
                $table->unsignedSmallInteger('student_rent_warm30')->nullable()->after('avg_rent_min')
                    ->comment('Aylık Warmmiete, 30m² öğrenci örnek-konutu (€)');
            }
            if (! Schema::hasColumn('cities', 'student_rent_index')) {
                $table->decimal('student_rent_index', 4, 1)->nullable()->after('student_rent_warm30')
                    ->comment('Studentenwohnpreisindex yıllık değişim (%)');
            }
            if (! Schema::hasColumn('cities', 'student_rent_source')) {
                $table->string('student_rent_source', 120)->nullable()->after('student_rent_index');
            }
            if (! Schema::hasColumn('cities', 'student_rent_year')) {
                $table->unsignedSmallInteger('student_rent_year')->nullable()->after('student_rent_source');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            foreach (['student_rent_warm30', 'student_rent_index', 'student_rent_source', 'student_rent_year'] as $col) {
                if (Schema::hasColumn('cities', $col)) $table->dropColumn($col);
            }
        });
    }
};
