<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * İlk gerçek kayıtlar — web doğrulamalı (2026-06-03). Uydurma YOK.
 * Dil kursları: Goethe-Institut, DeutschAkademie, did, VHS, Lingoda, DW, Babbel, DKFA.
 * Yeminli tercüme: Beglaubigung24, Embassy Translations, Sworn Translators, Fachübersetzungsdienst.
 * Idempotent (slug). affiliate_url boş → website kullanılır. Admin'den düzenlenir.
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        if (Schema::hasTable('language_courses')) {
            // [slug, name, type, website, cities[], levels[], desc_tr, featured, sort]
            $courses = [
                ['goethe-institut', 'Goethe-Institut', 'private', 'https://www.goethe.de/de/spr/kur.html',
                    ['Online', 'Berlin', 'München', 'Frankfurt', 'Mannheim'], ['A1','A2','B1','B2','C1','C2'],
                    'Almanya\'nın resmi kültür enstitüsü — dünya standardı Almanca kursları ve Goethe-Zertifikat sınavları (A1–C2). Vize ve üniversite için en çok tanınan sertifika.', true, 10],
                ['deutschakademie', 'DeutschAkademie', 'private', 'https://www.deutschakademie.de/',
                    ['Berlin', 'München', 'Online'], ['A1','A2','B1','B2','C1','C2'],
                    'Yoğun ve akşam Almanca kursları + ücretsiz online gramer alıştırmaları. Yılda 10.000+ öğrenci.', false, 20],
                ['did-deutsch-institut', 'did deutsch-institut', 'private', 'https://www.did.de/',
                    ['Berlin', 'München', 'Frankfurt'], ['A1','A2','B1','B2','C1','C2'],
                    'Yoğun Almanca kursları, sınav hazırlığı ve konaklama seçenekleriyle dil okulu.', false, 30],
                ['volkshochschule-vhs', 'Volkshochschule (VHS)', 'private', 'https://www.vhs.de/',
                    ['Tüm şehirler'], ['A1','A2','B1','B2','C1','C2'],
                    'Belediye halk eğitim merkezleri — en ekonomik kurslar ve resmi entegrasyon kursları (Integrationskurs). Her şehirde mevcut.', false, 40],
                ['dkfa-muenchen', 'Deutschkurse bei der Universität München (DKFA)', 'university', 'https://www.dkfa.de/',
                    ['München'], ['A1','A2','B1','B2','C1','C2'],
                    'Münih Üniversitesi bünyesinde Almanca kursları — DSH/TestDaF sınav hazırlığı dahil, üniversite girişine yönelik.', true, 5],
                ['lingoda', 'Lingoda', 'online', 'https://www.lingoda.com/de/deutsch-lernen/',
                    ['Online'], ['A1','A2','B1','B2','C1'],
                    'Native öğretmenlerle canlı online grup dersleri (A1–C1), esnek takvim ve sertifika.', false, 50],
                ['deutsche-welle', 'Deutsche Welle — Deutsch lernen', 'online', 'https://learngerman.dw.com/',
                    ['Online'], ['A1','A2','B1','B2','C1'],
                    'Devlet yayıncısı Deutsche Welle\'nin ÜCRETSİZ, seviyeli online Almanca kursları (Nicos Weg vb.).', true, 60],
                ['babbel', 'Babbel', 'online', 'https://www.babbel.com/learn-german',
                    ['Online'], ['A1','A2','B1','B2'],
                    'Uygulama tabanlı Almanca öğrenme — günlük kısa dersler, konuşma pratiği (A1–B2).', false, 70],
            ];
            foreach ($courses as [$slug, $name, $type, $web, $cities, $levels, $desc, $feat, $sort]) {
                DB::table('language_courses')->updateOrInsert(['slug' => $slug], [
                    'name' => $name, 'type' => $type, 'website' => $web,
                    'cities' => json_encode($cities, JSON_UNESCAPED_UNICODE),
                    'levels' => json_encode($levels), 'description_tr' => $desc,
                    'is_featured' => $feat, 'is_active' => true, 'sort_order' => $sort,
                    'updated_at' => $now, 'created_at' => $now,
                ]);
            }
        }

        if (Schema::hasTable('translation_offices')) {
            // [slug, name, type, website, languages[], features[], desc_tr, featured, sort]
            $offices = [
                ['beglaubigung24', 'Beglaubigung24', 'online', 'https://beglaubigung24.de/',
                    ['TR-DE', 'DE-TR'], ['yeminli', 'diploma', 'ekspres', 'kargo', 'online_siparis'],
                    'Yeminli tercümanlarca onaylı (beglaubigte) çeviri — diploma/belge. Online sipariş, hızlı teslim, PDF + posta. Türkçe-Almanca mevcut.', true, 10],
                ['embassy-translations', 'Embassy Translations', 'online', 'https://www.beglaubigte-uebersetzung.eu/',
                    ['TR-DE', 'DE-TR'], ['yeminli', '50_dil', 'online_siparis'],
                    '50+ dilde yeminli/onaylı çeviri — Türkçe dahil. Diploma, doğum belgesi, sözleşme.', false, 20],
                ['sworn-translators', 'Sworn Translators', 'agency', 'https://sworntranslators.de/',
                    ['TR-DE', 'DE-TR'], ['yeminli', 'mahkeme_onayli', 'diploma'],
                    'Mahkemece yeminli (vereidigt) tercümanlar — diploma ve resmi belge onaylı çevirisi.', false, 30],
                ['fachuebersetzungsdienst', 'Fachübersetzungsdienst', 'agency', 'https://www.fachuebersetzungsdienst.com/',
                    ['TR-DE', 'DE-TR'], ['yeminli', 'onayli', 'uzman'],
                    'Almanya genelinde onaylı/yeminli çeviri hizmeti — diploma, sertifika, hukuki belgeler.', false, 40],
            ];
            foreach ($offices as [$slug, $name, $type, $web, $langs, $features, $desc, $feat, $sort]) {
                DB::table('translation_offices')->updateOrInsert(['slug' => $slug], [
                    'name' => $name, 'type' => $type, 'website' => $web, 'is_sworn' => true,
                    'languages' => json_encode($langs, JSON_UNESCAPED_UNICODE),
                    'features' => json_encode($features, JSON_UNESCAPED_UNICODE),
                    'description_tr' => $desc,
                    'is_featured' => $feat, 'is_active' => true, 'sort_order' => $sort,
                    'updated_at' => $now, 'created_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Seed geri alma gereksiz (admin'den yönetilir).
    }
};
