<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // BA provider — description → description_tr + description_en/de
        DB::statement("ALTER TABLE blocked_account_providers CHANGE description description_tr LONGTEXT NULL");
        Schema::table('blocked_account_providers', function (Blueprint $t) {
            $t->longText('description_en')->nullable()->after('description_tr');
            $t->longText('description_de')->nullable()->after('description_en');
        });

        // Housing provider
        DB::statement("ALTER TABLE housing_providers CHANGE description description_tr LONGTEXT NULL");
        Schema::table('housing_providers', function (Blueprint $t) {
            $t->longText('description_en')->nullable()->after('description_tr');
            $t->longText('description_de')->nullable()->after('description_en');
        });

        // Events
        DB::statement("ALTER TABLE events CHANGE description_md description_md_tr LONGTEXT NULL");
        Schema::table('events', function (Blueprint $t) {
            $t->string('title_en')->nullable()->after('title_de');
            $t->longText('description_md_en')->nullable()->after('description_md_tr');
            $t->longText('description_md_de')->nullable()->after('description_md_en');
        });
    }

    public function down(): void
    {
        Schema::table('blocked_account_providers', function (Blueprint $t) {
            $t->dropColumn(['description_en', 'description_de']);
        });
        DB::statement("ALTER TABLE blocked_account_providers CHANGE description_tr description LONGTEXT NULL");

        Schema::table('housing_providers', function (Blueprint $t) {
            $t->dropColumn(['description_en', 'description_de']);
        });
        DB::statement("ALTER TABLE housing_providers CHANGE description_tr description LONGTEXT NULL");

        Schema::table('events', function (Blueprint $t) {
            $t->dropColumn(['title_en', 'description_md_en', 'description_md_de']);
        });
        DB::statement("ALTER TABLE events CHANGE description_md_tr description_md LONGTEXT NULL");
    }
};
