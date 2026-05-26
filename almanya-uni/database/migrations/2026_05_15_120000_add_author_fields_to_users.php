<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_url', 500)->nullable()->after('email');
            $table->string('role_label', 60)->nullable()->after('avatar_url');
            $table->json('social_links')->nullable()->after('role_label');
            $table->boolean('is_author')->default(false)->after('is_admin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar_url', 'role_label', 'social_links', 'is_author']);
        });
    }
};
