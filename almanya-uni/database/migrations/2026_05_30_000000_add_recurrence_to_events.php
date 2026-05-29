<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Events: recurrence_rule + parent_event_id.
 *
 * Sade RRULE subset:
 *   weekly      → her hafta
 *   biweekly    → iki haftada bir
 *   monthly     → ayda bir
 *
 * "Recurring" admin tarafında: parent event oluşturulur, sonra Filament
 * action "🔁 Seriyi N kopya ileri uzat" ile parent referansıyla child'lar
 * üretilir. Public listelemede child'lar normal event olarak görünür ama
 * "Seri parçası" rozeti taşır.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('recurrence_rule', 16)->nullable()->after('timezone');
            $table->foreignId('parent_event_id')->nullable()->after('recurrence_rule')->constrained('events')->nullOnDelete();
            $table->index(['parent_event_id']);
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['parent_event_id']);
            $table->dropColumn(['recurrence_rule', 'parent_event_id']);
        });
    }
};
