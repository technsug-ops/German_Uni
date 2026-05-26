<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_pages', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('Unique identifier (route name or external slug)');
            $table->enum('link_type', ['route', 'url'])->default('route');
            $table->string('url')->nullable()->comment('For external/static URLs like /forum/');
            $table->string('label');
            $table->string('icon', 16)->nullable();
            $table->string('description')->nullable();
            $table->string('badge', 32)->nullable();
            $table->enum('group', ['kesfet', 'araclar', 'firsatlar', 'icerik', 'standalone'])->default('kesfet');
            $table->boolean('is_enabled')->default(true);
            $table->boolean('protect_route')->default(true)->comment('If true, middleware blocks the URL when disabled');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['group', 'is_enabled', 'sort_order']);
            $table->index('is_enabled');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_pages');
    }
};
