<?php

/**
 * Tools Schema.org registry — WebApplication metadata per tool.
 * Used by <x-tool-schema /> Blade component to emit JSON-LD on each tool page.
 *
 * NOTE: aggregateRating intentionally omitted — only verified ratings allowed (feedback_real_social_proof).
 */
return [
    'cost-of-living' => [
        'route' => 'tools.cost-of-living',
        'name' => [
            'tr' => 'Almanya Yaşam Maliyeti Hesaplayıcısı',
            'en' => 'Germany Cost of Living Calculator',
            'de' => 'Lebenshaltungskosten-Rechner Deutschland',
        ],
        'description' => [
            'tr' => 'Almanya\'da öğrenci olarak aylık masraflarınızı şehir bazında hesaplayın.',
            'en' => 'Calculate your monthly expenses as a student in Germany, city by city.',
            'de' => 'Berechnen Sie Ihre monatlichen Studienausgaben in Deutschland nach Stadt.',
        ],
        'featureList' => [
            'tr' => ['Şehir bazlı maliyet', 'Konut tipi seçimi', 'Yaşam standardı ayarı', 'Anlık sonuç'],
            'en' => ['City-based costs', 'Housing type', 'Lifestyle settings', 'Instant results'],
            'de' => ['Stadtspezifische Kosten', 'Wohnform', 'Lebensstil', 'Sofortige Ergebnisse'],
        ],
    ],

    'grade-converter' => [
        'route' => 'tools.grade-converter',
        'name' => [
            'tr' => 'Not Dönüştürücü (Türkiye → Almanya)',
            'en' => 'Grade Converter (Home → German System)',
            'de' => 'Notenrechner (Heimatland → Deutsches System)',
        ],
        'description' => [
            'tr' => 'Üniversite notunuzu Alman 1-5 sistemine modifiye Bavyera formülü ile çevirin.',
            'en' => 'Convert your university grade to the German 1-5 system via modified Bavarian formula.',
            'de' => 'Wandeln Sie Ihre Hochschulnote über die modifizierte bayerische Formel ins deutsche 1-5-System um.',
        ],
        'featureList' => [
            'tr' => ['Modifiye Bavyera formülü', 'Çoklu ülke desteği', 'Anlık hesap'],
            'en' => ['Modified Bavarian formula', 'Multi-country support', 'Instant calculation'],
            'de' => ['Modifizierte bayerische Formel', 'Mehrere Länder', 'Sofortige Berechnung'],
        ],
    ],

    'recommendation' => [
        'route' => 'tools.recommendation',
        'name' => [
            'tr' => 'Üniversite Eşleştirme Quizi',
            'en' => 'University Match Quiz',
            'de' => 'Universitäts-Match-Quiz',
        ],
        'description' => [
            'tr' => '5 soru → size uygun Alman üniversiteleri (603 aktif üni veritabanından).',
            'en' => '5 questions → German universities that fit you (from 603 active universities).',
            'de' => '5 Fragen → passende deutsche Universitäten (aus 603 aktiven Hochschulen).',
        ],
        'featureList' => [
            'tr' => ['5 soruluk quiz', '603 üni veritabanı', 'Sonuç sayfası kaydedilebilir'],
            'en' => ['5-question quiz', '603-university database', 'Shareable results'],
            'de' => ['5-Fragen-Quiz', 'Datenbank mit 603 Hochschulen', 'Teilbare Ergebnisse'],
        ],
    ],

    'career-compass' => [
        'route' => 'tools.career-compass',
        'name' => [
            'tr' => 'Kariyer Pusulası (RIASEC)',
            'en' => 'Career Compass (RIASEC)',
            'de' => 'Karriere-Kompass (RIASEC)',
        ],
        'description' => [
            'tr' => 'Yetenek (RIASEC) + değer analizi → 3.500+ veri içinden size uyan gerçek meslekler.',
            'en' => 'Talent (RIASEC) + value analysis → real professions from 3,500+ data points.',
            'de' => 'Talent (RIASEC) + Wertanalyse → reale Berufe aus 3.500+ Datenpunkten.',
        ],
        'featureList' => [
            'tr' => ['RIASEC modeli', '3500+ meslek', 'Değer analizi'],
            'en' => ['RIASEC model', '3,500+ professions', 'Value analysis'],
            'de' => ['RIASEC-Modell', '3.500+ Berufe', 'Wertanalyse'],
        ],
    ],

    'deadlines' => [
        'route' => 'tools.deadlines',
        'name' => [
            'tr' => 'Başvuru Takvimi (Almanya Üniversiteleri)',
            'en' => 'Application Calendar (German Universities)',
            'de' => 'Bewerbungskalender (deutsche Hochschulen)',
        ],
        'description' => [
            'tr' => 'Yaklaşan başvuru tarihlerini görün, filtreleyin, takvime ekleyin. 7.000+ program.',
            'en' => 'See upcoming deadlines, filter, add to your calendar. 7,000+ programs.',
            'de' => 'Bevorstehende Fristen sehen, filtern, zum Kalender hinzufügen. 7.000+ Programme.',
        ],
        'featureList' => [
            'tr' => ['7000+ program', 'iCal export', 'Filtre & arama'],
            'en' => ['7,000+ programs', 'iCal export', 'Filter & search'],
            'de' => ['7.000+ Programme', 'iCal-Export', 'Filter & Suche'],
        ],
    ],

    'visa-cost' => [
        'route' => 'tools.visa-cost',
        'name' => [
            'tr' => 'Almanya Öğrenci Vizesi Maliyet Hesaplayıcısı',
            'en' => 'Germany Student Visa Cost Calculator',
            'de' => 'Deutschland Studentenvisum Kostenrechner',
        ],
        'description' => [
            'tr' => 'Almanya öğrenci vize sürecinin TÜM masraflarını adım adım toplayın.',
            'en' => 'Add up ALL costs of the Germany student visa process step by step.',
            'de' => 'Addieren Sie alle Kosten des deutschen Studentenvisums Schritt für Schritt.',
        ],
        'featureList' => [
            'tr' => ['Adım adım maliyet', 'Sperrkonto + sigorta + harç', 'TR/EUR'],
            'en' => ['Step-by-step cost', 'Sperrkonto + insurance + fees', 'TRY/EUR'],
            'de' => ['Schritt-für-Schritt-Kosten', 'Sperrkonto + Versicherung + Gebühren', 'TRY/EUR'],
        ],
    ],

    'budget-planner' => [
        'route' => 'tools.budget-planner',
        'name' => [
            'tr' => 'Aylık Bütçe Planlayıcı (Öğrenci)',
            'en' => 'Monthly Budget Planner (Student)',
            'de' => 'Monatlicher Budgetplaner (Student)',
        ],
        'description' => [
            'tr' => 'Aylık gelir + gider + tasarruf hedefi. Şehir bazlı, yan iş geliri dahil.',
            'en' => 'Monthly income + expenses + savings goal. City-based, includes part-time income.',
            'de' => 'Monatliches Einkommen + Ausgaben + Sparziel. Stadtspezifisch, mit Nebenjob-Einkommen.',
        ],
        'featureList' => [
            'tr' => ['Şehir bazlı', 'Yan iş geliri', 'Tasarruf hedefi'],
            'en' => ['City-based', 'Part-time income', 'Savings goal'],
            'de' => ['Stadtspezifisch', 'Nebenjob-Einkommen', 'Sparziel'],
        ],
    ],

    'blocked-account' => [
        'route' => 'tools.blocked-account',
        'name' => [
            'tr' => 'Bloke Hesap (Sperrkonto) Bulucu',
            'en' => 'Blocked Account (Sperrkonto) Finder',
            'de' => 'Sperrkonto-Finder',
        ],
        'description' => [
            'tr' => 'Almanya öğrenci vizesi için bloke hesap sağlayıcılarını karşılaştır. Fiyat, hız, sigorta kombo.',
            'en' => 'Compare blocked account providers for the Germany student visa. Price, speed, insurance combo.',
            'de' => 'Sperrkonto-Anbieter für das deutsche Studentenvisum vergleichen. Preis, Geschwindigkeit, Versicherung.',
        ],
        'featureList' => [
            'tr' => ['5 sağlayıcı', 'Fiyat karşılaştırma', 'Sigorta kombo'],
            'en' => ['5 providers', 'Price comparison', 'Insurance bundles'],
            'de' => ['5 Anbieter', 'Preisvergleich', 'Versicherungspakete'],
        ],
    ],

    'studienkolleg' => [
        'route' => 'tools.studienkolleg',
        'name' => [
            'tr' => 'Studienkolleg Bulucu',
            'en' => 'Studienkolleg Finder',
            'de' => 'Studienkolleg-Finder',
        ],
        'description' => [
            'tr' => 'Türkiye lise diplomanız ile gitmeniz gereken hazırlık programı (T-/M-/W-/G-/S-Kurs).',
            'en' => 'The foundation program (T-/M-/W-/G-/S-Kurs) you need based on your high school diploma.',
            'de' => 'Das Studienkolleg-Kurs-System (T/M/W/G/S) für ausländische Studienbewerber.',
        ],
        'featureList' => [
            'tr' => ['T/M/W/G/S kurs eşleştirme', 'Kabul kriterleri', 'Resmi kaynaklar'],
            'en' => ['T/M/W/G/S course matching', 'Admission criteria', 'Official sources'],
            'de' => ['T/M/W/G/S-Kurs-Zuordnung', 'Zulassungskriterien', 'Offizielle Quellen'],
        ],
    ],

    'eligibility-checker' => [
        'route' => 'tools.eligibility-checker',
        'name' => [
            'tr' => 'Almanya Üniversite Uygunluk Kontrolü',
            'en' => 'Germany University Eligibility Checker',
            'de' => 'Eignungsprüfung für deutsche Hochschulen',
        ],
        'description' => [
            'tr' => 'Diplomanız Almanya\'da doğrudan üniversiteye yeterli mi yoksa Studienkolleg gerekli mi?',
            'en' => 'Does your diploma qualify you for direct university admission in Germany, or do you need Studienkolleg?',
            'de' => 'Reicht Ihr Abschluss für ein direktes Studium in Deutschland oder ist ein Studienkolleg nötig?',
        ],
        'featureList' => [
            'tr' => ['Diploma karşılaştırma', 'Studienkolleg yönlendirme', 'Anabin uyumlu'],
            'en' => ['Diploma matching', 'Studienkolleg routing', 'Anabin-aligned'],
            'de' => ['Abschlussvergleich', 'Studienkolleg-Routing', 'Anabin-konform'],
        ],
    ],
];
