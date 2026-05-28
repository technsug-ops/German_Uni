<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * events tablosuna host_user_id ekler — etkinlik host'unu User'a bağlar.
 * Legacy "host" text kolonu kalır (fallback için).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('host_user_id')->nullable()->after('host')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['host_user_id']);
            $table->dropColumn('host_user_id');
        });
    }
};
