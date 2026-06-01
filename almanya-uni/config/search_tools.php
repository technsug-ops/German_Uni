<?php

/**
 * Arama için araç/kavram registry'si — sinonim + çok-dilli kavram eşleştirme.
 * Kullanıcı hangi dilde/varyantta yazarsa yazsın (TR/EN/DE) ilgili aracı bulur.
 * Örn: "sperrkonto" = "blocked account" = "bloke hesap" → Sperrkonto Bulucu.
 *
 * Her giriş: route (locale-aware), icon, title (tr/en/de), keywords (tüm diller).
 * keywords küçük-harf; eşleştirme aksan-duyarsız + substring.
 */
return [
    [
        'route' => 'tools.blocked-account',
        'icon' => '🏦',
        'title' => ['tr' => 'Sperrkonto (Bloke Hesap) Bulucu', 'en' => 'Blocked Account (Sperrkonto) Finder', 'de' => 'Sperrkonto-Finder'],
        'keywords' => ['sperrkonto', 'sperr konto', 'bloke hesap', 'bloke hesabı', 'blocked account', 'blocked-account', 'kontosperrung', 'engellenmiş hesap'],
    ],
    [
        'route' => 'tools.visa-cost',
        'icon' => '🛂',
        'title' => ['tr' => 'Vize Maliyeti Hesaplayıcı', 'en' => 'Visa Cost Calculator', 'de' => 'Visum-Kostenrechner'],
        'keywords' => ['vize maliyeti', 'vize ücreti', 'vize parası', 'vize masrafı', 'visa cost', 'visa fee', 'visum kosten', 'visumkosten', 'vize'],
    ],
    [
        'route' => 'tools.cost-of-living',
        'icon' => '💶',
        'title' => ['tr' => 'Yaşam Maliyeti', 'en' => 'Cost of Living', 'de' => 'Lebenshaltungskosten'],
        'keywords' => ['yaşam maliyeti', 'geçim', 'yaşam gideri', 'cost of living', 'living cost', 'lebenshaltungskosten', 'lebenskosten'],
    ],
    [
        'route' => 'tools.grade-converter',
        'icon' => '🎯',
        'title' => ['tr' => 'Not Çevirici (Bavyera Formülü)', 'en' => 'Grade Converter (Bavarian Formula)', 'de' => 'Notenumrechner (Bayerische Formel)'],
        'keywords' => ['not çevirici', 'not dönüştürücü', 'not hesaplama', 'grade converter', 'gpa', 'note umrechnen', 'notenumrechnung', 'bayerische formel', 'bavyera formülü', 'abitur'],
    ],
    [
        'route' => 'tools.deadlines',
        'icon' => '📅',
        'title' => ['tr' => 'Başvuru Takvimi & Son Tarihler', 'en' => 'Application Deadlines', 'de' => 'Bewerbungsfristen'],
        'keywords' => ['son tarih', 'başvuru takvimi', 'takvim', 'deadline', 'deadlines', 'fristen', 'bewerbungsfristen', 'termin'],
    ],
    [
        'route' => 'tools.budget-planner',
        'icon' => '🧮',
        'title' => ['tr' => 'Bütçe Planlayıcı', 'en' => 'Budget Planner', 'de' => 'Budgetplaner'],
        'keywords' => ['bütçe', 'bütçe planlayıcı', 'budget', 'budget planner', 'finanzplan', 'budgetplaner'],
    ],
    [
        'route' => 'tools.studienkolleg',
        'icon' => '📘',
        'title' => ['tr' => 'Studienkolleg Rehberi', 'en' => 'Studienkolleg Guide', 'de' => 'Studienkolleg-Leitfaden'],
        'keywords' => ['studienkolleg', 'hazırlık', 'vorbereitung', 'kolleg'],
    ],
    [
        'route' => 'tools.inspire-me',
        'icon' => '✨',
        'title' => ['tr' => 'Bana İlham Ver', 'en' => 'Inspire Me', 'de' => 'Inspiriere mich'],
        'keywords' => ['ilham', 'inspire', 'öneri', 'tavsiye', 'inspire me', 'empfehlung'],
    ],
];
