<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('housing_tips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->string('city_name', 100)->nullable();
            $table->string('title');
            $table->string('category', 40)->index(); // wg | private | dorm | scam-warning | landlord-talk | other
            $table->text('content');
            $table->unsignedSmallInteger('upvote_count')->default(0);
            $table->boolean('is_approved')->default(false)->index();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['city_id', 'is_approved']);
            $table->index(['category', 'is_approved']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('housing_tips');
    }
};
