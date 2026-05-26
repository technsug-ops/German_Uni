<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scraped_programs', function (Blueprint $table) {
            $table->string('study_form', 60)->nullable()->after('admission_mode');
            $table->string('deadline_raw', 255)->nullable()->after('study_form');
            $table->string('tuition_raw', 255)->nullable()->after('deadline_raw');
            $table->string('semester_fee_raw', 255)->nullable()->after('tuition_raw');
            $table->unsignedSmallInteger('ects_credits')->nullable()->after('semester_fee_raw');
        });
    }

    public function down(): void
    {
        Schema::table('scraped_programs', function (Blueprint $table) {
            $table->dropColumn(['study_form', 'deadline_raw', 'tuition_raw', 'semester_fee_raw', 'ects_credits']);
        });
    }
};
