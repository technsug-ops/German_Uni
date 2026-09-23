<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Mail düzeni: 'personal' (sade) | 'rich' (logolu şablon).
 *
 * Mevcut üç partnerlik şablonu soğuk kurumsal temas için yazıldı; bülten
 * görünümü bu tür maillerde yanıt oranını düşürdüğü için hepsi 'personal'
 * olarak işaretleniyor. Varsayılan da bilinçli biçimde 'personal'.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('email_templates') && ! Schema::hasColumn('email_templates', 'layout')) {
            Schema::table('email_templates', function (Blueprint $table) {
                $table->string('layout', 20)->default('personal')->after('body');
            });

            DB::table('email_templates')->update(['layout' => 'personal']);
        }

        if (Schema::hasTable('email_messages') && ! Schema::hasColumn('email_messages', 'layout')) {
            Schema::table('email_messages', function (Blueprint $table) {
                $table->string('layout', 20)->nullable()->after('body');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('email_templates') && Schema::hasColumn('email_templates', 'layout')) {
            Schema::table('email_templates', fn (Blueprint $table) => $table->dropColumn('layout'));
        }

        if (Schema::hasTable('email_messages') && Schema::hasColumn('email_messages', 'layout')) {
            Schema::table('email_messages', fn (Blueprint $table) => $table->dropColumn('layout'));
        }
    }
};
