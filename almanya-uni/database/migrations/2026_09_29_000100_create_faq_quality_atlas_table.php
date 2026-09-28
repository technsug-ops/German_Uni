<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * SSS Kalite Atlası — küme (TR/EN/DE çeviri grubu) başına DENETİM ANLIK GÖRÜNTÜSÜ.
 * Satırlar değişmez denetim kaydıdır: yeni denetim = yeni audit_label (eskiler üzerine yazılmaz).
 * SSS gövdeleri burada tutulmaz; soru/cevap/canlı durum faqs tablosundan canlı okunur.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faq_quality_atlas', function (Blueprint $table) {
            $table->id();
            $table->string('audit_label', 20);
            $table->uuid('translation_group_id')->nullable();

            $table->string('tr_slug', 255);
            $table->string('en_slug', 255)->nullable();
            $table->string('de_slug', 255)->nullable();
            $table->string('tr_question_at_audit', 500)->nullable();

            // Denetim anındaki SSS id'leri — yalnız teşhis; canlı kimlik translation_group_id + locale
            $table->unsignedBigInteger('tr_faq_id')->nullable();
            $table->unsignedBigInteger('en_faq_id')->nullable();
            $table->unsignedBigInteger('de_faq_id')->nullable();

            $table->string('topic', 80);
            $table->string('risk_level', 2);            // P0 / P1 / P2
            $table->char('tr_quality', 1);              // A / B / C / D
            $table->string('tr_status', 12);            // COMPLETE / EMPTY / PARTIAL / MISSING / BROKEN / STALE
            $table->string('en_status', 12);
            $table->string('de_status', 12);

            $table->json('tr_issues')->nullable();
            $table->string('authoritative_internal_source', 255)->nullable();
            $table->boolean('external_source_needed')->default(false);
            $table->string('translation_strategy', 40)->nullable();
            $table->unsignedTinyInteger('parity_score');
            $table->json('quality_ready_locales')->nullable();
            $table->string('recommended_action', 500)->nullable();
            $table->string('proposed_batch', 16);       // A..E / OUTSIDE_BATCH
            $table->integer('priority_score')->nullable();
            $table->json('duplicate_of')->nullable();   // TR slug'ları
            $table->boolean('junk')->default(false);
            $table->text('notes')->nullable();
            $table->boolean('chatbot_risk')->default(false);
            $table->json('stale_markers')->nullable();

            $table->timestamp('audited_at');
            $table->boolean('resolved')->default(false);
            $table->string('unresolved_reason', 500)->nullable();
            $table->timestamps();

            $table->unique(['audit_label', 'tr_slug']);
            $table->index('audit_label');
            $table->index('translation_group_id');
            $table->index('risk_level');
            $table->index('tr_quality');
            $table->index('topic');
            $table->index('proposed_batch');
            $table->index('parity_score');
            $table->index('resolved');
            $table->index('chatbot_risk');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faq_quality_atlas');
    }
};
