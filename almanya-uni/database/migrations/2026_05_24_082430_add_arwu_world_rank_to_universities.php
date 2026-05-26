<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->unsignedSmallInteger('arwu_world_rank')->nullable()->after('the_world_rank')->comment('Shanghai ARWU world rank');
            $table->string('wikipedia_url_en', 500)->nullable()->change();
            $table->timestamp('rankings_synced_at')->nullable()->after('arwu_world_rank');
            $table->index('arwu_world_rank');
        });
    }

    public function down(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->dropIndex(['arwu_world_rank']);
            $table->dropColumn(['arwu_world_rank', 'rankings_synced_at']);
        });
    }
};
