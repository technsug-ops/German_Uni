<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Konu (alan) bazlı sıralama verisi — kaynaktan bağımsız depo.
 *
 * NEDEN: Alan sıralamalarımız şu an üniversitenin GENEL dünya sırasını kullanıyor
 * (QS/THE/ARWU). Bu, Fachhochschule'leri sistematik olarak cezalandırıyor — dünya
 * sıralamalarında yer almadıkları için kalite bileşeninden sıfır alıyorlar. Oysa
 * mühendislikte hedef kitlemiz için en ilgili kurumlar onlar.
 *
 * Tablo üç farklı kaynak biçimini birden taşıyabilecek şekilde tasarlandı:
 *   - CHE Hochschulranking → sayısal sıra YOK, üç grup var  → `tier` (top/mid/bottom)
 *   - ShanghaiRanking GRAS / QS by subject → bantlı sıralar → `rank_low` + `rank_high`
 *     ("101-150" tek kolona sıkıştırılırsa bilgi kaybolur; sıralamada orta nokta kullanılır)
 *   - Tekil sıra (ilk 50) → rank_low = rank_high
 *
 * `scope`: CHE aynı konuyu Uni/FH × Bachelor/Master olarak ayrı ölçüyor (örn "fh-bachelor").
 * `source_subject`: kaynağın kendi etiketi ("Engineering - Mechanical", "Mechatronik") —
 * bizim geniş alanlarımızla çoka-bir eşleşir, denetlenebilir kalması için saklanıyor.
 *
 * VERİ HENÜZ YOK: CHE lisanslı bir üründür, resmî erişim talebi gönderilecek
 * (bkz. doc/CHE-DATENANFRAGE.md). Tablo şimdiden kuruluyor ki veri geldiğinde yalnızca
 * doldurma işi kalsın. Puanlama entegrasyonu bilinçli olarak ERTELENDİ — gerçek veriye
 * karşı test edilemeyen bir skor değişikliği bu oturumda iki kez regresyon üretti.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('university_subject_ranks')) {
            return;
        }

        Schema::create('university_subject_ranks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('university_id')->constrained()->cascadeOnDelete();

            // DİKKAT: tablo adı `fields_of_study`, ama constrained() Laravel'in çoğullama
            // kuralıyla `field_of_studies` arar → boş DB'de FK kurulamaz, migration patlar
            // (CI safety-net testleri bunu yakaladı). Tablo adı AÇIKÇA verilmeli.
            $table->foreignId('field_of_study_id')->constrained('fields_of_study')->cascadeOnDelete();

            $table->string('source', 16)->comment('che | gras | qs | the');
            $table->string('source_subject', 120)->comment('Kaynağın kendi konu etiketi');
            $table->string('scope', 32)->nullable()->comment('örn. uni-bachelor, fh-master');

            $table->unsignedSmallInteger('rank_low')->nullable()->comment('Bantlı sıranın alt ucu');
            $table->unsignedSmallInteger('rank_high')->nullable()->comment('Bantlı sıranın üst ucu');
            $table->string('tier', 8)->nullable()->comment('CHE: top | mid | bottom');

            $table->unsignedSmallInteger('year');
            $table->string('source_url', 500)->nullable();

            $table->timestamps();

            // Aynı kurum + alan + kaynak + kaynak-konusu + yıl tek satır olmalı;
            // yeniden içe aktarmada updateOrInsert için doğal anahtar.
            $table->unique(
                ['university_id', 'field_of_study_id', 'source', 'source_subject', 'year'],
                'usr_natural_unique'
            );
            $table->index(['field_of_study_id', 'source'], 'usr_field_source_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('university_subject_ranks');
    }
};
