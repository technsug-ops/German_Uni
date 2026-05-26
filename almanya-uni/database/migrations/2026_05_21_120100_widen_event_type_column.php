<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Enum → VARCHAR(40) - yeni tipleri eklemek için
        DB::statement("ALTER TABLE events MODIFY COLUMN type VARCHAR(40) NOT NULL DEFAULT 'webinar'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE events MODIFY COLUMN type ENUM('webinar','workshop','info_session','qa_live','meetup','open_day','panel','deadline','conference') NOT NULL DEFAULT 'webinar'");
    }
};
