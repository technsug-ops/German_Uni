<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->unsignedSmallInteger('hs_nummer')->nullable()->unique()->after('wikidata_id');
            $table->string('hochschultyp', 60)->nullable()->after('type');
            $table->string('traegerschaft', 60)->nullable()->after('hochschultyp');
            $table->string('promotion_recht', 80)->nullable()->after('traegerschaft');
            $table->string('habilitation_recht', 20)->nullable()->after('promotion_recht');
            $table->boolean('hrk_member')->nullable()->after('habilitation_recht');
            $table->string('phone', 40)->nullable()->after('website_url');
            $table->string('street', 200)->nullable()->after('phone');
            $table->string('postal_code', 10)->nullable()->after('street');

            $table->index('hs_nummer');
            $table->index('hochschultyp');
            $table->index('traegerschaft');
        });
    }

    public function down(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->dropIndex(['hs_nummer']);
            $table->dropIndex(['hochschultyp']);
            $table->dropIndex(['traegerschaft']);
            $table->dropColumn([
                'hs_nummer', 'hochschultyp', 'traegerschaft',
                'promotion_recht', 'habilitation_recht', 'hrk_member',
                'phone', 'street', 'postal_code',
            ]);
        });
    }
};
