<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Content factory: 5 yeni how-to brief (Almanca öğrenme, randevu, vize
 * görüşmesi, BAföG, Anmeldung). Brief'ler İÇ planlama dokümanıdır (tek dil,
 * Türkçe — modelin AUDIENCES/TONES'u da Türkçe). Bunlardan üretilecek BLOG
 * yazıları tr/en/de olarak yayınlanacak (notes'ta belirtildi). Idempotent.
 */
return new class extends Migration
{
    private array $slugs = [
        'almanca-ogrenme-yol-haritasi-testdaf-dsh',
        'almanya-randevu-rehberi-konsolosluk-burgeramt',
        'ogrenci-vizesi-gorusmesi-sorular-hazirlik',
        'bafog-nedir-uluslararasi-ogrenci-sartlar-basvuru',
        'anmeldung-adim-adim-sehir-kaydi-burgeramt',
    ];

    public function up(): void
    {
        $now = now();

        $briefs = [
            [
                'title' => "Sıfırdan C1'e Almanca Öğrenme Yol Haritası (TestDaF/DSH)",
                'slug' => 'almanca-ogrenme-yol-haritasi-testdaf-dsh',
                'audience' => 'aday_ogrenci',
                'topic' => 'Dil / Almanca',
                'primary_keyword' => 'almanca öğrenme',
                'secondary_keywords' => ['testdaf hazırlık', 'dsh sınavı', 'b1 b2 c1 almanca', 'goethe telc'],
                'pain_point' => 'Üniversite için hangi seviyenin, ne kadar sürede ve hangi sınavla yeteceğini bilememek; kaynak kalabalığında kaybolmak.',
                'source_questions' => ['Üniversite için hangi Almanca seviyesi gerekir?', 'TestDaF mı DSH mı?', 'Sıfırdan C1 ne kadar sürer?', 'Hangi ücretsiz kaynaklar işe yarar?'],
                'target_word_count' => 1800,
                'brand_tone' => 'instructive',
                'notes' => 'How-to. Seviye tablosu + zaman çizelgesi + TestDaF/DSH karşılaştırması. tr/en/de yayınlanacak.',
            ],
            [
                'title' => 'Almanya Randevu Rehberi: Konsolosluk & Bürgeramt Randevusu Nasıl Alınır?',
                'slug' => 'almanya-randevu-rehberi-konsolosluk-burgeramt',
                'audience' => 'aday_ogrenci',
                'topic' => 'Vize / Bürokrasi',
                'primary_keyword' => 'almanya randevu',
                'secondary_keywords' => ['konsolosluk randevusu', 'bürgeramt termin', 'vize randevusu', 'termin bulma'],
                'pain_point' => 'Randevuların aylarca dolu görünmesi; iptal-takip yöntemini ve doğru portalı bilmemek.',
                'source_questions' => ['Vize randevusu nereden alınır?', 'Randevu çıkmıyorsa ne yapılır?', 'Bürgeramt Termin nasıl bulunur?', 'İptal takibi nasıl yapılır?'],
                'target_word_count' => 1500,
                'brand_tone' => 'instructive',
                'notes' => 'How-to. Konsolosluk (iData/VFS) vs şehir Bürgeramt ayrımı net. tr/en/de yayınlanacak.',
            ],
            [
                'title' => 'Öğrenci Vizesi Görüşmesi: Sık Sorulan Sorular ve Hazırlık',
                'slug' => 'ogrenci-vizesi-gorusmesi-sorular-hazirlik',
                'audience' => 'aday_ogrenci',
                'topic' => 'Vize / Bürokrasi',
                'primary_keyword' => 'vize görüşmesi',
                'secondary_keywords' => ['öğrenci vizesi mülakat', 'vize soruları', 'sperrkonto vize', 'vize ret sebepleri'],
                'pain_point' => 'Görüşmede ne sorulacağını ve hangi belge eksikliğinin redde yol açtığını bilememe kaygısı.',
                'source_questions' => ['Vize görüşmesinde ne sorulur?', 'Hangi belgeler şart?', 'Ret sebepleri neler?', 'Görüşme hangi dilde yapılır?'],
                'target_word_count' => 1600,
                'brand_tone' => 'instructive',
                'notes' => 'How-to + checklist. uni-assist/VPD ve Sperrkonto yazılarına iç link. tr/en/de yayınlanacak.',
            ],
            [
                'title' => 'BAföG Nedir? Uluslararası Öğrenciler İçin Şartlar ve Başvuru',
                'slug' => 'bafog-nedir-uluslararasi-ogrenci-sartlar-basvuru',
                'audience' => 'mevcut_ogrenci',
                'topic' => 'Finansman / Burs',
                'primary_keyword' => 'bafög',
                'secondary_keywords' => ['bafög şartları', 'uluslararası öğrenci bafög', 'bafög başvuru', 'öğrenci desteği almanya'],
                'pain_point' => 'BAföG\'ün çoğu uluslararası öğrenciye kapalı sanılması; kimlerin gerçekten hak kazandığının net olmaması.',
                'source_questions' => ['Uluslararası öğrenci BAföG alabilir mi?', 'BAföG şartları neler?', 'Ne kadar ödeniyor?', 'Nasıl başvurulur?'],
                'target_word_count' => 1500,
                'brand_tone' => 'instructive',
                'notes' => 'Hassas konu — yasal şartlar doğru olmalı (kaynak: bafög.de). Stipendium/burs yazılarına link. tr/en/de yayınlanacak.',
            ],
            [
                'title' => "Anmeldung Adım Adım: Almanya'da Şehir Kaydı (Bürgeramt)",
                'slug' => 'anmeldung-adim-adim-sehir-kaydi-burgeramt',
                'audience' => 'mevcut_ogrenci',
                'topic' => 'Yerleşme / İlk Hafta',
                'primary_keyword' => 'anmeldung',
                'secondary_keywords' => ['şehir kaydı almanya', 'wohnungsgeberbestätigung', 'bürgeramt anmeldung', 'adres kaydı'],
                'pain_point' => 'İlk hafta kaosu: Anmeldung olmadan banka/sigorta/oturum açılmıyor ama randevu ve belge sırası karışık.',
                'source_questions' => ['Anmeldung nedir, neden şart?', 'Hangi belgeler gerekir?', 'Wohnungsgeberbestätigung nereden alınır?', 'Geç kalırsam ceza var mı?'],
                'target_word_count' => 1500,
                'brand_tone' => 'instructive',
                'notes' => 'How-to + checklist. Mentor "Anmeldung & first week" konusuyla uyumlu. tr/en/de yayınlanacak.',
            ],
        ];

        foreach ($briefs as $b) {
            if (DB::table('content_briefs')->where('slug', $b['slug'])->exists()) {
                continue;
            }
            $b['secondary_keywords'] = json_encode($b['secondary_keywords'], JSON_UNESCAPED_UNICODE);
            $b['source_questions'] = json_encode($b['source_questions'], JSON_UNESCAPED_UNICODE);
            $b['status'] = 'draft';
            $b['created_at'] = $now;
            $b['updated_at'] = $now;
            DB::table('content_briefs')->insert($b);
        }
    }

    public function down(): void
    {
        DB::table('content_briefs')->whereIn('slug', $this->slugs)->delete();
    }
};
