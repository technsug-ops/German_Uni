<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE events MODIFY COLUMN title_tr TEXT NULL');
        DB::statement('ALTER TABLE events MODIFY COLUMN title_en TEXT NULL');
        DB::statement('ALTER TABLE events MODIFY COLUMN title_de TEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE events MODIFY COLUMN title_tr VARCHAR(255) NULL');
        DB::statement('ALTER TABLE events MODIFY COLUMN title_en VARCHAR(255) NULL');
        DB::statement('ALTER TABLE events MODIFY COLUMN title_de VARCHAR(255) NULL');
    }
};
