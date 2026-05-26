<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->unsignedSmallInteger('qs_world_rank')->nullable()->after('founded_year')->comment('QS World University Rankings position');
            $table->unsignedSmallInteger('the_world_rank')->nullable()->after('qs_world_rank')->comment('Times Higher Education World Rankings position');
            $table->index('qs_world_rank');
            $table->index('the_world_rank');
        });
    }

    public function down(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->dropIndex(['qs_world_rank']);
            $table->dropIndex(['the_world_rank']);
            $table->dropColumn(['qs_world_rank', 'the_world_rank']);
        });
    }
};
