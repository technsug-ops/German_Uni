<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->string('wikidata_id', 20)->nullable()->unique();
            $table->string('name_tr');
            $table->string('name_de');
            $table->string('name_en')->nullable();
            $table->string('slug')->unique();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('wikidata_id');
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
