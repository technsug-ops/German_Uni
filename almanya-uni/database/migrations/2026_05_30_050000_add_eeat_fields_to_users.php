<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) return;

        Schema::table('users', function (Blueprint $t) {
            // E-E-A-T enrichment columns
            if (! Schema::hasColumn('users', 'expertise'))         $t->json('expertise')->nullable()->after('bio');
            if (! Schema::hasColumn('users', 'education'))         $t->json('education')->nullable()->after('expertise');
            if (! Schema::hasColumn('users', 'member_of'))         $t->json('member_of')->nullable()->after('education');
            if (! Schema::hasColumn('users', 'languages_spoken')) $t->json('languages_spoken')->nullable()->after('member_of');
            if (! Schema::hasColumn('users', 'awards'))            $t->json('awards')->nullable()->after('languages_spoken');
            if (! Schema::hasColumn('users', 'featured_in'))       $t->json('featured_in')->nullable()->after('awards');
            if (! Schema::hasColumn('users', 'years_experience'))  $t->unsignedSmallInteger('years_experience')->nullable()->after('featured_in');
        });

        // Seed Halil Yaprakli (founder) with concrete, verifiable E-E-A-T data.
        $halil = DB::table('users')
            ->where('slug', 'halil-yaprakli')
            ->orWhere('name', 'like', '%Halil%Yaprakli%')
            ->orWhere('name', 'like', '%Halil%Yapra%')
            ->first();

        if ($halil) {
            DB::table('users')->where('id', $halil->id)->update([
                'expertise' => json_encode([
                    'German higher education system',
                    'University application strategy',
                    'Studienkolleg & Anabin recognition',
                    'Sperrkonto & student visa preparation',
                    'TestAS and TestDaF guidance',
                    'Career planning in Germany',
                ], JSON_UNESCAPED_UNICODE),
                'languages_spoken' => json_encode(['tr', 'en', 'de'], JSON_UNESCAPED_UNICODE),
                'years_experience' => 8,
            ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) return;

        Schema::table('users', function (Blueprint $t) {
            foreach (['expertise', 'education', 'member_of', 'languages_spoken', 'awards', 'featured_in', 'years_experience'] as $col) {
                if (Schema::hasColumn('users', $col)) $t->dropColumn($col);
            }
        });
    }
};
