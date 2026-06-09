<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_insurance_providers', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('logo_url')->nullable();
            $table->string('website_url')->nullable();
            $table->string('affiliate_url')->nullable();

            // public = gesetzlich (GKV) · private = PKV (digital/expat-friendly) · expat = incoming/travel
            $table->enum('type', ['public', 'private', 'expat'])->default('public');

            // Fiyatlandırma — aylık (tipik öğrenci) + opsiyonel üst sınır (aralık)
            $table->decimal('monthly_fee_eur', 8, 2)->nullable();
            $table->decimal('monthly_fee_max_eur', 8, 2)->nullable();

            // Yaş / dönem limiti (public öğrenci tarifesi ~30 yaş / 14 dönem)
            $table->unsignedSmallInteger('age_limit')->nullable();

            // Kabul edilebilirlik
            $table->boolean('accepted_for_visa')->default(true);
            $table->boolean('accepted_for_enrollment')->default(false); // üniversite kaydı için geçerli mi (public = evet)

            // Kapsam öne çıkanları
            $table->boolean('covers_dental')->default(false);
            $table->boolean('covers_pregnancy')->default(false);
            $table->boolean('covers_mental_health')->default(false);
            $table->boolean('covers_repatriation')->default(false);

            // Pratik
            $table->boolean('digital_signup')->default(false);
            $table->boolean('english_support')->default(false);
            $table->json('supported_languages')->nullable();

            // Kime uygun (kısa etiket) — örn "EU/EHIC olmayan", "30 yaş üstü", "Sprachkurs/Studienkolleg öncesi"
            $table->string('best_for')->nullable();

            // Çok dilli açıklama (LocalizableContent: description_{tr,en,de})
            $table->text('description_tr')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_de')->nullable();
            $table->longText('description_long')->nullable();

            $table->json('pros')->nullable();
            $table->json('cons')->nullable();
            $table->json('features')->nullable();

            $table->text('visa_recognition_note')->nullable();
            $table->text('turkish_students_note')->nullable();

            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamp('last_verified_at')->nullable();
            $table->timestamps();

            $table->index(['is_published', 'sort_order']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_insurance_providers');
    }
};
