<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->decimal('qs_academic_reputation', 5, 2)->nullable()->after('qs_world_rank')->comment('Academic Reputation (30% weight)');
            $table->decimal('qs_employer_reputation', 5, 2)->nullable()->after('qs_academic_reputation')->comment('Employer Reputation (15%)');
            $table->decimal('qs_citations_per_faculty', 5, 2)->nullable()->after('qs_employer_reputation')->comment('Citations per Faculty (20%)');
            $table->decimal('qs_faculty_student_ratio', 5, 2)->nullable()->after('qs_citations_per_faculty')->comment('Faculty/Student Ratio (10%)');
            $table->decimal('qs_international_faculty', 5, 2)->nullable()->after('qs_faculty_student_ratio')->comment('International Faculty Ratio (5%)');
            $table->decimal('qs_international_students', 5, 2)->nullable()->after('qs_international_faculty')->comment('International Student Ratio (5%)');
            $table->decimal('qs_international_research', 5, 2)->nullable()->after('qs_international_students')->comment('International Research Network (5%)');
            $table->decimal('qs_employment_outcomes', 5, 2)->nullable()->after('qs_international_research')->comment('Employment Outcomes (5%)');
            $table->decimal('qs_sustainability', 5, 2)->nullable()->after('qs_employment_outcomes')->comment('Sustainability (5%)');
            $table->decimal('qs_overall_score', 5, 2)->nullable()->after('qs_sustainability')->comment('Overall QS score (0-100)');
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
