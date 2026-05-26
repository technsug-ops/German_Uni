<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->uuid('partner_id')->nullable()->unique()->after('wikidata_id');
            $table->boolean('is_uni_assist_member')->nullable()->after('type');
            $table->unsignedSmallInteger('uni_assist_id')->nullable()->after('is_uni_assist_member');
        });
    }

    public function down(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->dropColumn(['partner_id', 'is_uni_assist_member', 'uni_assist_id']);
        });
    }
};
