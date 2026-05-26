<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('high_school_type', 30)->nullable()->after('email');
            $table->string('status', 30)->nullable()->after('high_school_type');
            $table->string('german_level', 10)->nullable()->after('status');
            $table->string('english_level', 10)->nullable()->after('german_level');
            $table->foreignId('target_field_id')->nullable()->after('english_level')
                ->constrained('fields_of_study')->nullOnDelete();
            $table->string('target_degree', 20)->nullable()->after('target_field_id');
            $table->string('target_semester', 20)->nullable()->after('target_degree');
            $table->unsignedMediumInteger('monthly_budget_eur')->nullable()->after('target_semester');
            $table->foreignId('preferred_state_id')->nullable()->after('monthly_budget_eur')
                ->constrained('states')->nullOnDelete();
            $table->text('bio')->nullable()->after('preferred_state_id');
            $table->timestamp('last_active_at')->nullable()->after('bio');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('target_field_id');
            $table->dropConstrainedForeignId('preferred_state_id');
            $table->dropColumn([
                'high_school_type', 'status', 'german_level', 'english_level',
                'target_degree', 'target_semester', 'monthly_budget_eur', 'bio', 'last_active_at',
            ]);
        });
    }
};
