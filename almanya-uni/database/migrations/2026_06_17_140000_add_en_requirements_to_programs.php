<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Program gerekliliklerine İngilizce sütunlar — DAAD International Programmes detay
 * sayfalarından çekilen YAPISAL, program-spesifik veri (İngilizce). Mevcut *_tr alanları
 * partner/manuel TR içerik tutuyordu; DAAD verisi İngilizce olduğundan _en'de durur ve
 * görüntüleme locale-aware olur (TR sayfada _tr → yoksa _en → yoksa genel rehber).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $t) {
            $t->text('qualification_requirements_en')->nullable()->after('qualification_requirements_tr');
            $t->text('language_requirements_en')->nullable()->after('language_requirements_tr');
            $t->text('required_documents_en')->nullable()->after('required_documents_tr');
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $t) {
            $t->dropColumn(['qualification_requirements_en', 'language_requirements_en', 'required_documents_en']);
        });
    }
};
