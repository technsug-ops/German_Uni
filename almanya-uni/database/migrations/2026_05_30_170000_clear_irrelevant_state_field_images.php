<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('states')->whereNotNull('image_url')->update(['image_url' => null]);
        DB::table('fields_of_study')->whereNotNull('image_url')->update(['image_url' => null]);
    }

    public function down(): void
    {
    }
};
