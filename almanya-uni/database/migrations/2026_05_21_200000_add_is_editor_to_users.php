<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'is_editor')) {
                // Editör/Moderatör: admin panele sınırlı erişim (sadece içerik moderasyonu)
                $table->boolean('is_editor')->default(false)->after('is_admin');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_editor')) {
                $table->dropColumn('is_editor');
            }
        });
    }
};
