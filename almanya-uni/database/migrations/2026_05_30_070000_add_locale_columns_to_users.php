<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Free-text user fields (bio, role_label) were single-column TR-only.
 * /en/ekip and /de/ekip rendered them in Turkish because no per-language
 * alternative existed. This adds locale columns following the same
 * pattern used for Event.title_{tr,en,de} and Scholarship taxonomy.
 *
 * A separate Gemini-based command populates the *_en / *_de values.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) return;

        Schema::table('users', function (Blueprint $t) {
            if (! Schema::hasColumn('users', 'role_label_en')) $t->string('role_label_en')->nullable()->after('role_label');
            if (! Schema::hasColumn('users', 'role_label_de')) $t->string('role_label_de')->nullable()->after('role_label_en');
            if (! Schema::hasColumn('users', 'bio_en'))        $t->text('bio_en')->nullable()->after('bio');
            if (! Schema::hasColumn('users', 'bio_de'))        $t->text('bio_de')->nullable()->after('bio_en');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) return;
        Schema::table('users', function (Blueprint $t) {
            foreach (['role_label_en', 'role_label_de', 'bio_en', 'bio_de'] as $col) {
                if (Schema::hasColumn('users', $col)) $t->dropColumn($col);
            }
        });
    }
};
