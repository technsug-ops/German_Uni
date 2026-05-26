<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->decimal('qs_academic_reputation', 5, 2)->nullable()->after('qs_world_rank');
            $table->decimal('qs_employer_reputation', 5, 2)->nullable()->after('qs_academic_reputation');
            $table->decimal('qs_citations_per_faculty', 5, 2)->nullable()->after('qs_employer_reputation');
            $table->decimal('qs_faculty_student_ratio', 5, 2)->nullable()->after('qs_citations_per_faculty');
            $table->decimal('qs_international_faculty', 5, 2)->nullable()->after('qs_faculty_student_ratio');
            $table->decimal('qs_international_students', 5, 2)->nullable()->after('qs_international_faculty');
            $table->decimal('qs_international_research', 5, 2)->nullable()->after('qs_international_students');
            $table->decimal('qs_employment_outcomes', 5, 2)->nullable()->after('qs_international_research');
            $table->decimal('qs_sustainability', 5, 2)->nullable()->after('qs_employment_outcomes');
            $table->decimal('qs_overall_score', 5, 2)->nullable()->after('qs_sustainability');
        });
    }

    public function down(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->dropColumn([
                'qs_academic_reputation', 'qs_employer_reputation', 'qs_citations_per_faculty',
                'qs_faculty_student_ratio', 'qs_international_faculty', 'qs_international_students',
                'qs_international_research', 'qs_employment_outcomes', 'qs_sustainability',
                'qs_overall_score',
            ]);
        });
    }
};
