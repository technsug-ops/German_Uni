<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Affiliate tıklama takibi — hangi sağlayıcı (Sperrkonto/sigorta) gerçekten
 * tıklanıyor? Gelir optimizasyonunun ölçüm temeli. /go/{type}/{slug} redirect'i
 * dış linke yönlendirmeden önce buraya bir satır yazar (savunmacı — log hatası
 * redirect'i engellemez).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_clicks', function (Blueprint $table) {
            $table->id();
            $table->string('provider_type', 20);          // 'sperrkonto' | 'insurance'
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->string('provider_slug', 120);
            $table->string('context', 32)->nullable();     // 'index' | 'show' | 'comparison' | 'blog'
            $table->string('locale', 8)->nullable();
            $table->string('host', 64)->nullable();        // almanyauni.com | applytogerman.com
            $table->string('ip_hash', 64)->nullable();     // sha256 — yalnız fraud/dedup, kişisel veri değil
            $table->string('user_agent', 255)->nullable();
            $table->string('referer', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['provider_type', 'provider_slug'], 'aff_clicks_provider_idx');
            $table->index('created_at', 'aff_clicks_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_clicks');
    }
};
