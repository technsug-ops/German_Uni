<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_dorms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->string('city_name', 100);       // şehir adı (slug yerine raw)
            $table->string('organization');          // ör. "Studierendenwerk Berlin"
            $table->string('website_url', 500);
            $table->string('application_url', 500)->nullable();
            $table->string('waitlist_avg', 80)->nullable();   // "3-12 ay"
            $table->unsignedSmallInteger('rent_min_eur')->nullable();
            $table->unsignedSmallInteger('rent_max_eur')->nullable();
            $table->json('amenities')->nullable();
            $table->text('notes_tr')->nullable();
            $table->text('notes_en')->nullable();
            $table->text('notes_de')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['city_id', 'is_active']);
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_dorms');
    }
};
