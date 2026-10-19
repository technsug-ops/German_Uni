<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * İçerik güncelleme tarihi — Faz 1 şeması (docs/SEO_CONTENT_UPDATED_AT_DESIGN.md, _PHASE0_RESULT.md §3.1).
 * Yalnız ekleme: tüm sütunlar boş bırakılabilir; mevcut veri ve sorgular değişmez. Okuma noktaları (sitemap,
 * JSON-LD) Faz 2'de, kanıta dayalı ilk değerler Faz 3'te. updated_at'e dokunulmaz.
 *
 *  - content_updated_at    kesin son içerik değişikliği (bundan sonra modelde kaydedilir; geçmiş için Faz 3)
 *  - content_updated_floor kanıtlanmış alt sınır (gerçek tarih ≥ bu); kesin tarih değildir
 *  - content_date_source   recorded | migration_deploy | snapshot_floor | published_fallback
 *  - content_hash          içerik alanlarının sha256'sı
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['posts', 'faqs'] as $table) {
            if (Schema::hasColumn($table, 'content_updated_at')) {
                continue;
            }
            Schema::table($table, function (Blueprint $t) {
                $t->timestamp('content_updated_at')->nullable()->index();
                $t->timestamp('content_updated_floor')->nullable();
                $t->string('content_date_source', 32)->nullable();
                $t->char('content_hash', 64)->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach (['posts', 'faqs'] as $table) {
            if (! Schema::hasColumn($table, 'content_updated_at')) {
                continue;
            }
            Schema::table($table, function (Blueprint $t) {
                $t->dropIndex(['content_updated_at']);
                $t->dropColumn(['content_updated_at', 'content_updated_floor', 'content_date_source', 'content_hash']);
            });
        }
    }
};
