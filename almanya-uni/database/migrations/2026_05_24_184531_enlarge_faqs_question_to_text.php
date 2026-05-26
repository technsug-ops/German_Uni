<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // EN/DE çevirisi TR'den uzun olabiliyor — varchar(255) truncation veriyordu.
        DB::statement('ALTER TABLE faqs MODIFY COLUMN question TEXT NOT NULL');
        DB::statement('ALTER TABLE faqs MODIFY COLUMN answer_md LONGTEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE faqs MODIFY COLUMN question VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE faqs MODIFY COLUMN answer_md TEXT NULL');
    }
};
