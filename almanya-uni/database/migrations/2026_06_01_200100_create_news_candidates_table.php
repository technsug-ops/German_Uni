<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Haber "gelen kutusu" — yayın ÖNCESİ adaylar. 3 kaynak (origin):
 *   auto   : news:fetch ile RSS/Atom kaynaklardan çekilen
 *   link   : admin URL yapıştırır → içerik çekilir
 *   manual : admin görsel + yazı + kaynak elle girer
 * Onaylanınca (status=published) bir type='news' Post grubuna dönüşür.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('news_candidates')) return;

        Schema::create('news_candidates', function (Blueprint $table) {
            $table->id();
            $table->string('origin', 12)->default('manual')->index();   // auto | link | manual
            $table->string('status', 16)->default('pending')->index();  // pending | approved | rejected | published

            // Kaynak / orijinal
            $table->string('source_name', 120)->nullable();
            $table->string('source_url', 600)->nullable();
            $table->string('url_hash', 40)->nullable()->index();        // dedupe (auto/link)
            $table->string('orig_title', 300)->nullable();
            $table->text('raw_excerpt')->nullable();
            $table->longText('fetched_content')->nullable();            // link modunda çekilen ham metin
            $table->string('image_url', 600)->nullable();
            $table->date('event_date')->nullable();

            // Sınıflandırma
            $table->foreignId('suggested_category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('primary_locale', 5)->default('tr');
            // Admin önceliği — yayınlanan Post'lara taşınır. 0 = öncelik yok (en yeni en önde).
            $table->smallInteger('priority')->default(0);

            // Editöryel taslak (AI veya manuel) — birincil dilde, ÖZGÜN (telif-güvenli)
            $table->string('draft_title', 300)->nullable();
            $table->text('draft_excerpt')->nullable();
            $table->longText('draft_md')->nullable();

            $table->json('meta')->nullable();
            $table->string('published_group_id', 64)->nullable();       // yayınlanınca translation_group_id
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_candidates');
    }
};
