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

    'health-insurance' => [
        'route' => 'tools.health-insurance',
        'name' => [
            'tr' => 'Sağlık Sigortası Karşılaştırma',
            'en' => 'Health Insurance Comparison',
            'de' => 'Krankenversicherung im Vergleich',
        ],
        'description' => [
            'tr' => 'Almanya\'da öğrenci sağlık sigortası: GKV, PKV ve expat planlarını karşılaştır, sana uygun olanı bul.',
            'en' => 'Student health insurance in Germany: compare GKV, PKV and expat plans and find the right one for you.',
            'de' => 'Studentische Krankenversicherung in Deutschland: Vergleiche GKV, PKV und Expat-Tarife und finde den passenden.',
        ],
        'featureList' => [
            'tr' => ['GKV / PKV / expat', '7 sağlayıcı', 'Karar yardımcısı'],
            'en' => ['GKV / PKV / expat', '7 providers', 'Decision helper'],
            'de' => ['GKV / PKV / Expat', '7 Anbieter', 'Entscheidungshilfe'],
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

    'visa-appointment' => [
        'route' => 'tools.visa-appointment',
        'locales' => ['tr'], // iData yalnızca Türkiye'den başvuranlar için → sayfa + schema sadece /tr
        'name' => [
            'tr' => 'Vize Randevusu (iData)',
            'en' => 'Visa Appointment (iData)',
            'de' => 'Visumtermin (iData)',
        ],
        'description' => [
            'tr' => 'Almanya öğrenci vizesi için iData randevu süreci: nasıl alınır, ne zaman açılır, ipuçları.',
            'en' => 'The iData appointment process for the Germany student visa: how to book, when slots open, tips.',
            'de' => 'Der iData-Terminprozess für das deutsche Studentenvisum: Buchung, Slot-Öffnung und Tipps.',
        ],
        'featureList' => [
            'tr' => ['iData adım adım', 'Randevu ipuçları', 'Şehir bazlı'],
            'en' => ['iData step by step', 'Booking tips', 'By city'],
            'de' => ['iData Schritt für Schritt', 'Termin-Tipps', 'Nach Stadt'],
        ],
    ],

    'language-certificates' => [
        'route' => 'tools.language-certificates',
        'name' => [
            'tr' => 'Almanca Dil Sertifikaları',
            'en' => 'German Language Certificates',
            'de' => 'Deutsche Sprachzertifikate',
        ],
        'description' => [
            'tr' => 'Üniversite başvurusu için hangi Almanca sertifikası (TestDaF, DSH, telc, Goethe) gerekli — seviye karşılaştırması.',
            'en' => 'Which German certificate (TestDaF, DSH, telc, Goethe) you need for university admission, with a level comparison.',
            'de' => 'Welches Sprachzertifikat (TestDaF, DSH, telc, Goethe) du für die Zulassung brauchst — mit Niveauvergleich.',
        ],
        'featureList' => [
            'tr' => ['TestDaF / DSH / telc / Goethe', 'Seviye karşılaştırma', 'Kabul eşikleri'],
            'en' => ['TestDaF / DSH / telc / Goethe', 'Level comparison', 'Admission thresholds'],
            'de' => ['TestDaF / DSH / telc / Goethe', 'Niveauvergleich', 'Zulassungsgrenzen'],
        ],
    ],

    'pathway-finder' => [
        'route' => 'tools.pathway-finder',
        'name' => [
            'tr' => 'Rota Bulucu',
            'en' => 'Pathway Finder',
            'de' => 'Weg-Finder',
        ],
        'description' => [
            'tr' => 'Profiline göre Almanya\'ya en uygun yol: doğrudan üniversite, Studienkolleg, Ausbildung ya da dil yolu.',
            'en' => 'The best route to Germany for your profile: direct university, Studienkolleg, Ausbildung or language path.',
            'de' => 'Der beste Weg nach Deutschland für dein Profil: Direktstudium, Studienkolleg, Ausbildung oder Sprachweg.',
        ],
        'featureList' => [
            'tr' => ['Profil bazlı quiz', '4 rota', 'Kişisel sonuç'],
            'en' => ['Profile-based quiz', '4 routes', 'Personal result'],
            'de' => ['Profilbasiertes Quiz', '4 Wege', 'Persönliches Ergebnis'],
        ],
    ],

    'inspire-me' => [
        'route' => 'tools.inspire-me',
        'name' => [
            'tr' => 'Bana İlham Ver',
            'en' => 'Inspire Me',
            'de' => 'Inspirier mich',
        ],
        'description' => [
            'tr' => 'Ne okuyacağına karar veremedin mi? İlgi alanına göre Almanya\'dan program ve şehir keşfet.',
            'en' => 'Not sure what to study? Discover programs and cities in Germany based on your interests.',
            'de' => 'Noch unentschlossen? Entdecke Programme und Städte in Deutschland nach deinen Interessen.',
        ],
        'featureList' => [
            'tr' => ['İlgi bazlı keşif', 'Program + şehir', 'Rastgele ilham'],
            'en' => ['Interest-based discovery', 'Programs + cities', 'Random inspiration'],
            'de' => ['Interessenbasiert', 'Programme + Städte', 'Zufalls-Inspiration'],
        ],
    ],
];
