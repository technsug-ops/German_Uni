<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Explicit per-locale label + description for every MenuPage row.
 *
 * User reported: /tr/ekip mega menu showed German labels ("Universitäten",
 * "Studiengänge", "Städte", etc.) instead of Turkish on the TR domain.
 *
 * Root cause: production `menu_pages.label` column had been seeded with
 * German values at some earlier point (or label_de leaked into label_tr
 * fallback). The model accessor falls back to __() against the raw
 * `label` column, so when label = "Universitäten" it tries to find that
 * key in lang/tr.json — misses — and renders the German raw value.
 *
 * This migration sets all three label_* + description_* columns
 * explicitly so the accessor's first-tier lookup hits a valid row
 * regardless of what the legacy `label` column contains.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('menu_pages')) return;

        $entries = [
            // KEŞFET group
            'universities.index' => [
                'label_en' => 'Universities',  'label_tr' => 'Üniversiteler',  'label_de' => 'Universitäten',
                'description_en' => 'All German universities', 'description_tr' => 'Tüm Alman üniversiteleri', 'description_de' => 'Alle deutschen Universitäten',
            ],
            'programs.index' => [
                'label_en' => 'Programs', 'label_tr' => 'Programlar', 'label_de' => 'Studiengänge',
                'description_en' => 'Bachelor + Master', 'description_tr' => 'Lisans + Yüksek lisans', 'description_de' => 'Bachelor + Master',
            ],
            'cities.index' => [
                'label_en' => 'Cities', 'label_tr' => 'Şehirler', 'label_de' => 'Städte',
                'description_en' => 'Student cities', 'description_tr' => 'Öğrenci şehirleri', 'description_de' => 'Studentenstädte',
            ],
            'states.index' => [
                'label_en' => 'States', 'label_tr' => 'Eyaletler', 'label_de' => 'Bundesländer',
                'description_en' => '16 federal states', 'description_tr' => '16 federal eyalet', 'description_de' => '16 Bundesländer',
            ],
            'fields.index' => [
                'label_en' => 'Fields of Study', 'label_tr' => 'Alanlar', 'label_de' => 'Studienfelder',
                'description_en' => 'Academic fields', 'description_tr' => 'Akademik alanlar', 'description_de' => 'Akademische Fachbereiche',
            ],
            'professions.index' => [
                'label_en' => 'Professions', 'label_tr' => 'Meslekler', 'label_de' => 'Berufe',
                'description_en' => 'Profession descriptions', 'description_tr' => 'Meslek tanımları', 'description_de' => 'Berufsbeschreibungen',
            ],
            'map.index' => [
                'label_en' => 'Map', 'label_tr' => 'Harita', 'label_de' => 'Karte',
                'description_en' => 'Interactive map', 'description_tr' => 'İnteraktif harita', 'description_de' => 'Interaktive Karte',
            ],
            'rankings.index' => [
                'label_en' => 'Rankings', 'label_tr' => 'Sıralamalar', 'label_de' => 'Rankings',
                'description_en' => 'Size & prestige', 'description_tr' => 'Boyut & prestij', 'description_de' => 'Größe & Prestige',
            ],
            'compare.index' => [
                'label_en' => 'Compare', 'label_tr' => 'Karşılaştır', 'label_de' => 'Vergleichen',
                'description_en' => 'Compare 2-4 universities', 'description_tr' => '2-4 üniyi yan yana karşılaştır', 'description_de' => '2-4 Universitäten vergleichen',
            ],

            // ARAÇLAR group
            'tools.cost-of-living' => [
                'label_en' => 'Cost of Living', 'label_tr' => 'Yaşam Maliyeti', 'label_de' => 'Lebenshaltungskosten',
                'description_en' => 'Monthly budget by city', 'description_tr' => 'Şehre göre aylık bütçe', 'description_de' => 'Monatliches Budget pro Stadt',
            ],
            'tools.grade-converter' => [
                'label_en' => 'Grade Converter', 'label_tr' => 'Not Dönüştürücü', 'label_de' => 'Notenrechner',
                'description_en' => 'TR/EN → German grade', 'description_tr' => 'TR/EN → Alman notu', 'description_de' => 'TR/EN → deutsche Note',
            ],
            'tools.recommendation' => [
                'label_en' => 'University Match Quiz', 'label_tr' => 'Üniversite Önerisi', 'label_de' => 'Uni-Empfehlung',
                'description_en' => '5 questions → best fit', 'description_tr' => '5 soruda en uygun üniler', 'description_de' => '5 Fragen → beste Treffer',
            ],
            'tools.career-compass' => [
                'label_en' => 'Career Compass', 'label_tr' => 'Kariyer Pusulası', 'label_de' => 'Karriere-Kompass',
                'description_en' => 'RIASEC + values → profession', 'description_tr' => 'RIASEC + değer → meslek', 'description_de' => 'RIASEC + Werte → Beruf',
            ],
            'tools.deadlines' => [
                'label_en' => 'Application Calendar', 'label_tr' => 'Başvuru Takvimi', 'label_de' => 'Bewerbungskalender',
                'description_en' => 'Programs by deadline', 'description_tr' => 'Programları son başvuruya göre', 'description_de' => 'Programme nach Frist',
            ],
            'tools.visa-cost' => [
                'label_en' => 'Visa Cost Calculator', 'label_tr' => 'Vize Maliyeti', 'label_de' => 'Visa-Kosten',
                'description_en' => 'Student visa step by step', 'description_tr' => 'Öğrenci vizesi adım adım', 'description_de' => 'Studierendenvisum Schritt für Schritt',
            ],
            'tools.budget-planner' => [
                'label_en' => 'Budget Planner', 'label_tr' => 'Bütçe Planlayıcı', 'label_de' => 'Budget-Planer',
                'description_en' => 'Income + cost + savings', 'description_tr' => 'Gelir + gider + tasarruf', 'description_de' => 'Einkommen + Kosten + Ersparnis',
            ],
            'tools.blocked-account' => [
                'label_en' => 'Blocked Account (Sperrkonto)', 'label_tr' => 'Bloke Hesap (Sperrkonto)', 'label_de' => 'Sperrkonto',
                'description_en' => 'Compare providers', 'description_tr' => 'Sağlayıcıları karşılaştır', 'description_de' => 'Anbieter vergleichen',
            ],
            'tools.eligibility-checker' => [
                'label_en' => 'Eligibility Checker', 'label_tr' => 'Uygunluk Kontrolü', 'label_de' => 'Zulassungs-Check',
                'description_en' => 'Anabin diploma check', 'description_tr' => 'Anabin diploma kontrolü', 'description_de' => 'Anabin-Diplomprüfung',
            ],
            'tools.studienkolleg' => [
                'label_en' => 'Studienkolleg Finder', 'label_tr' => 'Studienkolleg Bulucu', 'label_de' => 'Studienkolleg-Finder',
                'description_en' => 'Foundation year programs', 'description_tr' => 'Hazırlık yılı programları', 'description_de' => 'Studienkolleg-Programme',
            ],
            'tools.pathway-finder' => [
                'label_en' => 'Pathway Finder', 'label_tr' => 'Yol Bulucu', 'label_de' => 'Weg-Finder',
                'description_en' => 'Find your Germany route', 'description_tr' => 'Almanya rotanı bul', 'description_de' => 'Finde deinen Weg',
            ],
            'tools.inspire-me' => [
                'label_en' => 'Inspire Me', 'label_tr' => 'Bana İlham Ver', 'label_de' => 'Inspiriere mich',
                'description_en' => 'Random discovery', 'description_tr' => 'Rastgele keşif', 'description_de' => 'Zufällige Entdeckung',
            ],
            'tools.professional-recognition' => [
                'label_en' => 'Professional Recognition', 'label_tr' => 'Mesleki Denklik', 'label_de' => 'Berufsanerkennung',
                'description_en' => 'Anerkennung guide', 'description_tr' => 'Anerkennung rehberi', 'description_de' => 'Anerkennung Wegweiser',
            ],

            // FIRSATLAR group
            'scholarships.index' => [
                'label_en' => 'Scholarships', 'label_tr' => 'Burslar', 'label_de' => 'Stipendien',
                'description_en' => 'DAAD + others', 'description_tr' => 'DAAD + diğerleri', 'description_de' => 'DAAD + andere',
            ],
            'scholarships.daad' => [
                'label_en' => 'DAAD Scholarships', 'label_tr' => 'DAAD Bursları', 'label_de' => 'DAAD-Stipendien',
                'description_en' => 'Official DAAD database', 'description_tr' => 'Resmi DAAD veritabanı', 'description_de' => 'Offizielle DAAD-Datenbank',
            ],
            'housing.index' => [
                'label_en' => 'Student Housing', 'label_tr' => 'Öğrenci Yurdu', 'label_de' => 'Studentenwohnen',
                'description_en' => 'Studentenwerk + WG', 'description_tr' => 'Studentenwerk + WG', 'description_de' => 'Studentenwerk + WG',
            ],
            'events.index' => [
                'label_en' => 'Events', 'label_tr' => 'Etkinlikler', 'label_de' => 'Veranstaltungen',
                'description_en' => 'Webinars + meetups', 'description_tr' => 'Webinarlar + buluşmalar', 'description_de' => 'Webinare + Treffen',
            ],
            'mentors.index' => [
                'label_en' => 'Mentors', 'label_tr' => 'Mentorlar', 'label_de' => 'Mentoren',
                'description_en' => 'Connect with mentors', 'description_tr' => 'Mentorlarla bağlan', 'description_de' => 'Mit Mentoren verbinden',
            ],
            'jobs.index' => [
                'label_en' => 'Academic Jobs', 'label_tr' => 'Akademik İş İlanları', 'label_de' => 'Akademische Stellen',
                'description_en' => 'PhD, Postdoc, Lecturer', 'description_tr' => 'PhD, Postdoc, Öğretim görevlisi', 'description_de' => 'PhD, Postdoc, Lehrkraft',
            ],

            // İÇERİK group
            'blog.index' => [
                'label_en' => 'Blog', 'label_tr' => 'Blog', 'label_de' => 'Blog',
                'description_en' => 'Articles & guides', 'description_tr' => 'Makaleler & rehberler', 'description_de' => 'Artikel & Ratgeber',
            ],
            'faqs.index' => [
                'label_en' => 'FAQs', 'label_tr' => 'SSS', 'label_de' => 'FAQ',
                'description_en' => 'Frequently asked questions', 'description_tr' => 'Sıkça sorulan sorular', 'description_de' => 'Häufig gestellte Fragen',
            ],
            'team' => [
                'label_en' => 'Team', 'label_tr' => 'Ekip', 'label_de' => 'Team',
                'description_en' => 'Editors & contributors', 'description_tr' => 'Editörler & katkıcılar', 'description_de' => 'Redaktion & Mitwirkende',
            ],
            'about' => [
                'label_en' => 'About', 'label_tr' => 'Biz Kimiz', 'label_de' => 'Über uns',
                'description_en' => 'Mission & story', 'description_tr' => 'Misyon & hikaye', 'description_de' => 'Mission & Geschichte',
            ],
            'tools.index' => [
                'label_en' => 'All Tools', 'label_tr' => 'Tüm Araçlar', 'label_de' => 'Alle Tools',
                'description_en' => 'Tools index', 'description_tr' => 'Araçlar dizini', 'description_de' => 'Tools-Übersicht',
            ],
        ];

        $updated = 0;
        foreach ($entries as $key => $cols) {
            $affected = DB::table('menu_pages')
                ->where('key', $key)
                ->update(array_merge($cols, ['updated_at' => now()]));
            if ($affected) $updated++;
        }
        // log to migration history isn't useful here; print is suppressed in non-tty contexts.
    }

    public function down(): void
    {
        if (! Schema::hasTable('menu_pages')) return;

        DB::table('menu_pages')->update([
            'label_en' => null, 'label_tr' => null, 'label_de' => null,
            'description_en' => null, 'description_tr' => null, 'description_de' => null,
        ]);
    }
};
