<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            // Partner ID (UUID) — re-import için unique key
            $table->uuid('partner_id')->nullable()->after('id');
            $table->index('partner_id');

            // Snapshot kaynağındaki üni adı (uni eşleşmediyse manuel review için)
            $table->string('partner_university_name')->nullable()->after('partner_id');

            // Detay
            $table->string('degree_specification', 80)->nullable()->after('degree'); // "Master of Arts (M.A.)"
            $table->string('location', 100)->nullable()->after('study_form');

            // Mali
            $table->unsignedMediumInteger('application_fee_eur')->nullable()->after('tuition_fee_eur');
            $table->unsignedMediumInteger('cost_per_semester_eur')->nullable()->after('application_fee_eur');

            // Başvuru tarihleri
            $table->date('application_deadline_summer')->nullable()->after('admission_summary');
            $table->date('application_deadline_winter')->nullable()->after('application_deadline_summer');

            // NC (Numerus Clausus)
            $table->decimal('nc_value', 4, 2)->nullable()->after('admission_mode');

            // Konular & alanlar (JSON arrays — partner çoklu döndürüyor)
            $table->json('subjects')->nullable()->after('admission_summary');
            $table->json('study_fields_raw')->nullable()->after('subjects');

            // Türkçe içerik — partner'ın sunduğu çeviriler
            $table->text('description_tr')->nullable()->after('source_id');
            $table->text('description_en')->nullable()->after('description_tr');
            $table->text('qualification_requirements_tr')->nullable()->after('description_en');
            $table->text('language_requirements_tr')->nullable()->after('qualification_requirements_tr');
            $table->text('required_documents_tr')->nullable()->after('language_requirements_tr');
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn([
                'partner_id',
                'partner_university_name',
                'degree_specification',
                'location',
                'application_fee_eur',
                'cost_per_semester_eur',
                'application_deadline_summer',
                'application_deadline_winter',
                'nc_value',
                'subjects',
                'study_fields_raw',
                'description_tr',
                'description_en',
                'qualification_requirements_tr',
                'language_requirements_tr',
                'required_documents_tr',
            ]);
        });
    }
};
