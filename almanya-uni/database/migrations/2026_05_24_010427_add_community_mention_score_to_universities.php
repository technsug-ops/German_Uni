<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->unsignedInteger('community_mention_score')->default(0)->after('the_world_rank')->comment('Telegram + Forum mention count (computed periodically)');
            $table->timestamp('community_mention_updated_at')->nullable()->after('community_mention_score');
            $table->index('community_mention_score');
        });
    }

    public function down(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->dropIndex(['community_mention_score']);
            $table->dropColumn(['community_mention_score', 'community_mention_updated_at']);
        });
    }
};
