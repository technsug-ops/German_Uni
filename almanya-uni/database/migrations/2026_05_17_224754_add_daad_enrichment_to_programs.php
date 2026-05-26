<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->string('image_url', 500)->nullable()->after('description_en');
            $table->string('language_level_de', 60)->nullable()->after('language_requirements_tr');
            $table->string('language_level_en', 60)->nullable()->after('language_level_de');
            $table->boolean('is_online')->nullable()->after('study_form');
            $table->text('financial_support')->nullable()->after('cost_per_semester_eur');
            $table->text('support_info')->nullable()->after('financial_support');
            $table->string('start_semester', 60)->nullable()->after('duration_semesters');

            $table->index('is_online');
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropIndex(['is_online']);
            $table->dropColumn([
                'image_url', 'language_level_de', 'language_level_en',
                'is_online', 'financial_support', 'support_info', 'start_semester',
            ]);
        });
    }
};
