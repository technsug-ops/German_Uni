<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('professions', function (Blueprint $table) {
            $table->json('info_fields_tr')->nullable()->after('info_fields');
            $table->json('info_fields_en')->nullable()->after('info_fields_tr');
        });
    }

    public function down(): void
    {
        Schema::table('professions', function (Blueprint $table) {
            $table->dropColumn(['info_fields_tr', 'info_fields_en']);
        });
    }
};
