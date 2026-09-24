<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Hukuki sayfa içeriğini locale başına ayrı kayda taşır.
 *
 * Önceki yapıda tr/en/de aynı `legal_pages` satırındaki `titles`/`descriptions`/
 * `bodies` JSON'larında birlikte duruyordu. Bunun iki somut bedeli oldu:
 *   - Çeviri sızıntıları: EN/DE Çerez Politikası'nda Türkçe cümleler kaldı.
 *   - Bakım: her içerik düzeltmesi devasa JSON'ın içinde blok arayıp değiştirmek
 *     zorundaydı; 2026_09_23_170000 tam da bu yüzden sessizce hiçbir şey yapmadı.
 *
 * Locale-bazlı alanlar (title/description/body) buraya taşınır. Sayfa geneline
 * ait alanlar (`key`, `effective_date`, `is_published`, `sort_order`) ana
 * tabloda kalır — bunların dile göre değişmesi için bir sebep yok.
 *
 * `legal_pages` üzerindeki JSON kolonları BU AŞAMADA DÜŞÜRÜLMEZ; rollback
 * güvenliği için legacy ayna olarak kalır (bkz. LegalPage::syncTranslations).
 * Kaldırılmaları ayrı bir migration'ın işi olacak.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legal_page_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('legal_page_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('title', 150);
            $table->text('description')->nullable()->comment('Per-locale meta description');
            $table->longText('body')->comment('Markdown veya HTML — LegalPage::getRenderedBody karar verir');
            $table->timestamps();

            // Bir sayfanın bir dilde tek içeriği olur. Çift kayıt = hangi metnin
            // yayında olduğunun belirsizleşmesi demek; hukuki sayfada kabul edilemez.
            $table->unique(['legal_page_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_page_translations');
    }
};
