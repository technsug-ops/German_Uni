<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Çok-kutulu mail: her giden/gelen mail hangi kutuya ait (admin / partnerships / …).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('email_messages') && ! Schema::hasColumn('email_messages', 'mailbox')) {
            Schema::table('email_messages', function (Blueprint $table) {
                $table->string('mailbox')->nullable()->after('direction')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('email_messages') && Schema::hasColumn('email_messages', 'mailbox')) {
            Schema::table('email_messages', function (Blueprint $table) {
                $table->dropColumn('mailbox');
            });
        }
    }
};
