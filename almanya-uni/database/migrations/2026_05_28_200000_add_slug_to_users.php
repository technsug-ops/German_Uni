<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('slug', 100)->nullable()->unique()->after('name');
        });

        // Mevcut author user'lara slug ata (is_author / is_editor / is_admin olanlar)
        $users = DB::table('users')
            ->where(function ($q) {
                $q->where('is_author', true)
                  ->orWhere('is_editor', true)
                  ->orWhere('is_admin', true);
            })
            ->whereNull('slug')
            ->get(['id', 'name']);

        $usedSlugs = [];
        foreach ($users as $u) {
            $base = Str::slug($u->name);
            if (! $base) continue;
            $slug = $base;
            $i = 2;
            while (in_array($slug, $usedSlugs) || DB::table('users')->where('slug', $slug)->exists()) {
                $slug = $base . '-' . $i;
                $i++;
            }
            $usedSlugs[] = $slug;
            DB::table('users')->where('id', $u->id)->update(['slug' => $slug]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
