<?php

/**
 * Almanya eğitim sistemi entity glossary (semantic SEO).
 *
 * Her entity için: title, kısa tanım, detaylı içerik, ilgili terimler,
 * resmi kaynak, FAQ. JSON-LD DefinedTerm + FAQPage markup oluşturulur.
 *
 * Slug → entity key. URL: /{locale}/sozluk/{slug}
 *
 * İçerikler TR-default. EN/DE fallback aynı (sonradan çevrilebilir).
 */
return [

    'aps' => [
        'slug' => 'aps',
        'icon' => '📜',
        'title' => [
            'tr' => 'APS (Akademische Prüfstelle)',
            'en' => 'APS (Akademische Prüfstelle)',
            'de' => 'APS — Akademische Prüfstelle',
        ],
        'short' => [
            'tr' => 'Çin, Hindistan, Pakistan, Vietnam ve Moğolistan vatandaşları için Almanya başvurusunda zorunlu olan diploma doğrulama belgesi.',
            'en' => 'Mandatory diploma verification certificate for applicants from China, India, Pakistan, Vietnam, and Mongolia applying to German universities.',
            'de' => 'Verpflichtende Zeugnisprüfung für Bewerber aus China, Indien, Pakistan, Vietnam und der Mongolei.',
        ],
        'body' => [
            'tr' => "APS (Akademische Prüfstelle), Almanya'nın Pekin/Yeni Delhi/İslamabad/Hanoi/Ulaanbaatar büyükelçiliklerinde çalışan bir kurumdur. Çin, Hindistan, Pakistan, Vietnam ve Moğolistan vatandaşlarının yükseköğretim belgelerinin gerçekliğini doğrular.\n\nSüreç 2-3 ay sürer ve **başvuru öncesinde** tamamlanmış olmalıdır. Belgeler APS tarafından incelenip onaylanır; öğrenci bir **APS sertifikası** alır. Bu sertifika olmadan Alman üniversitelerine başvuru kabul edilmez.\n\nMaliyet: 250 USD (Çin), 22-100 EUR (diğer ülkeler). Türk öğrenciler için APS GEREKMEZ.",
            'en' => "APS (Akademische Prüfstelle / Academic Examination Office) operates at German embassies in Beijing, New Delhi, Islamabad, Hanoi, and Ulaanbaatar. It verifies the authenticity of higher education credentials from China, India, Pakistan, Vietnam, and Mongolia.\n\nThe process takes 2-3 months and **must be completed before applying** to German universities. Without an APS certificate, German universities will not process your application.\n\nCost: 250 USD (China), 22-100 EUR (other countries). Turkish nationals do NOT need APS.",
            'de' => "Die APS prüft die Echtheit von Bildungsnachweisen aus China, Indien, Pakistan, Vietnam und der Mongolei. Das Verfahren dauert 2-3 Monate und muss vor der Bewerbung abgeschlossen sein.",
        ],
        'related' => ['anabin', 'uni-assist', 'studienkolleg'],
        'official_url' => 'https://www.apsdeutschland.de/',
        'faq' => [
            [
                'q' => ['tr' => 'Türk öğrencilere APS gerekir mi?', 'en' => 'Do Turkish students need APS?', 'de' => 'Brauchen türkische Studenten APS?'],
                'a' => ['tr' => 'Hayır, Türk öğrencilere APS gerekmez. APS yalnızca Çin, Hindistan, Pakistan, Vietnam ve Moğolistan vatandaşları için zorunludur.', 'en' => 'No, Turkish students do not need APS. APS is mandatory only for citizens of China, India, Pakistan, Vietnam, and Mongolia.', 'de' => 'Nein. APS ist nur für Bürger aus China, Indien, Pakistan, Vietnam und der Mongolei erforderlich.'],
            ],
            [
                'q' => ['tr' => 'APS süreci ne kadar sürer?', 'en' => 'How long does the APS process take?', 'de' => 'Wie lange dauert das APS-Verfahren?'],
                'a' => ['tr' => 'Ortalama 2-3 ay. Yoğun dönemlerde 4 aya kadar uzayabilir. Başvuru deadline\'larından önce tamamlamak için planlanmalı.', 'en' => 'Typically 2-3 months. May extend to 4 months in peak periods. Plan accordingly before application deadlines.', 'de' => 'Etwa 2-3 Monate, in Spitzenzeiten bis zu 4 Monate.'],
            ],
            [
                'q' => ['tr' => 'APS maliyeti nedir?', 'en' => 'How much does APS cost?', 'de' => 'Was kostet das APS?'],
                'a' => ['tr' => 'Çin için 250 USD, diğer ülkeler için 22-100 EUR arasında değişir.', 'en' => '250 USD for China, 22-100 EUR for other countries.', 'de' => '250 USD für China, 22-100 EUR für andere Länder.'],
            ],
        ],
    ],

    'uni-assist' => [
        'slug' => 'uni-assist',
        'icon' => '📋',
        'title' => [
            'tr' => 'Uni-Assist',
            'en' => 'Uni-Assist',
            'de' => 'Uni-Assist',
        ],
        'short' => [
            'tr' => 'Almanya\'daki yaklaşık 180 üniversite için uluslararası öğrenci başvurularını merkezi olarak işleyen platform.',
            'en' => 'Central platform processing international student applications for approximately 180 German universities.',
            'de' => 'Zentrale Plattform für internationale Bewerbungen an etwa 180 deutschen Hochschulen.',
        ],
        'body' => [
            'tr' => "Uni-Assist e.V., Berlin merkezli bir uygulamaları işleme servisidir. Yaklaşık **180 Alman üniversitesi** uluslararası öğrenci başvurularını Uni-Assist üzerinden alır.\n\nSüreç:\n1. Online hesap aç (uni-assist.de)\n2. Belgeleri yükle (lise/lisans diplomaları, transkript, dil sertifikası, pasaport)\n3. **Ücret öde**: ilk başvuru 75 EUR, sonraki her başvuru 30 EUR (aynı dönemde)\n4. Posta ile orijinal/noter onaylı belgeleri gönder\n5. Uni-Assist VPD (Vorprüfungsdokumentation) hazırlar — bu belge senin notunun Alman sistemine çevrilmiş halini gösterir\n6. Uni-Assist seçtiğin üniversitelere başvurunu iletir\n\n**Süre:** 4-6 hafta (sezon sonunda uzayabilir). **Deadline öncesi 6-8 hafta önceden başvurmak güvenli.**\n\nBazı üniversiteler (TUM, RWTH Aachen vb.) Uni-Assist kullanmaz, **doğrudan başvuru** alır. Her üniversitenin Uni-Assist üyesi olup olmadığını kontrol etmek gerek.",
            'en' => "Uni-Assist e.V. is a Berlin-based application processing service. Approximately **180 German universities** receive international student applications through Uni-Assist.\n\nProcess:\n1. Create online account (uni-assist.de)\n2. Upload documents (high school/Bachelor diplomas, transcripts, language certificate, passport)\n3. **Pay fees**: first application 75 EUR, each subsequent 30 EUR (same semester)\n4. Mail original/certified documents\n5. Uni-Assist prepares VPD (Vorprüfungsdokumentation) — your grade converted to German system\n6. Uni-Assist forwards your application to selected universities\n\n**Duration:** 4-6 weeks (may extend at end of season). **Apply 6-8 weeks before deadline.**\n\nSome universities (TUM, RWTH Aachen) do not use Uni-Assist; they accept **direct applications**. Always check each university's policy.",
            'de' => "Uni-Assist verarbeitet internationale Bewerbungen für ca. 180 deutsche Hochschulen. Erste Bewerbung 75 EUR, jede weitere 30 EUR pro Semester. Dauer 4-6 Wochen.",
        ],
        'related' => ['anabin', 'aps', 'studienkolleg'],
        'official_url' => 'https://www.uni-assist.de/',
        'faq' => [
            [
                'q' => ['tr' => 'Uni-Assist ücreti ne kadar?', 'en' => 'How much does Uni-Assist cost?', 'de' => 'Was kostet Uni-Assist?'],
                'a' => ['tr' => 'İlk başvuru 75 EUR, aynı dönem içinde her ek başvuru 30 EUR.', 'en' => 'First application 75 EUR, each additional in the same semester 30 EUR.', 'de' => 'Erste Bewerbung 75 EUR, jede weitere im selben Semester 30 EUR.'],
            ],
            [
                'q' => ['tr' => 'Hangi üniversiteler Uni-Assist kullanmaz?', 'en' => 'Which universities skip Uni-Assist?', 'de' => 'Welche Hochschulen nutzen kein Uni-Assist?'],
                'a' => ['tr' => 'TUM (Münih), RWTH Aachen, LMU\'nun bazı bölümleri ve birkaç ünlü üniversite doğrudan başvuru kabul eder. Her üniversitenin sayfasında kontrol et.', 'en' => 'TUM Munich, RWTH Aachen, some LMU programs and a few elite universities accept direct applications. Check each university\'s page.', 'de' => 'TUM München, RWTH Aachen und einige Programme der LMU akzeptieren Direktbewerbungen.'],
            ],
            [
                'q' => ['tr' => 'Uni-Assist süreci ne kadar sürer?', 'en' => 'How long does Uni-Assist processing take?', 'de' => 'Wie lange dauert die Bearbeitung?'],
                'a' => ['tr' => '4-6 hafta. Yaz/kış başvuru sezonu sonunda 8 haftaya kadar uzayabilir.', 'en' => '4-6 weeks. May extend to 8 weeks at the end of the application season.', 'de' => '4-6 Wochen, in Spitzenzeiten bis 8 Wochen.'],
            ],
        ],
    ],

    'sperrkonto' => [
        'slug' => 'sperrkonto',
        'icon' => '🏦',
        'title' => [
            'tr' => 'Sperrkonto (Bloke Hesap)',
            'en' => 'Sperrkonto (Blocked Account)',
            'de' => 'Sperrkonto',
        ],
        'short' => [
            'tr' => 'Almanya öğrenci vizesi için zorunlu olan ve aylık çekim limiti olan banka hesabı. 2026 yılı için yıllık tutar 11.904 EUR.',
            'en' => 'Mandatory bank account for German student visa with monthly withdrawal limit. 2026 annual amount: €11,904.',
            'de' => 'Vorgeschriebenes Bankkonto für das Studienvisum mit monatlichem Auszahlungslimit. 2026: 11.904 EUR jährlich.',
        ],
        'body' => [
            'tr' => "Sperrkonto, Almanya öğrenci vizesi başvurusunda **maddi yeterlilik belgesi** olarak kullanılan özel bir banka hesabıdır. Hesabı açtıktan sonra hesaptaki para 1 yıl boyunca **bloke** edilir; aylık olarak öğrenciye sabit bir miktar (2026 için **992 EUR/ay**, yıllık **11.904 EUR**) açılır.\n\nNeden bloke? Almanya konsoloslukları, öğrencinin **1 yıl yaşam masrafını karşılayabileceğini** kanıtlamasını istiyor. Bloke hesap bu güvenceyi veriyor.\n\n**Popüler sağlayıcılar:**\n- **Expatrio** — Hızlı, online (€89 setup ücret + €5/ay)\n- **Fintiba** — Kuruluştan beri lider (€89 + €5/ay)\n- **Deutsche Bank** — Geleneksel banka, Türkiye'den açılabilir (€150)\n- **Coracle**, **ICICI Bank**, **kredisetzer.de** — alternatif sağlayıcılar\n\n**Süreç:**\n1. Sağlayıcı seç + online başvuru\n2. Pasaport + üniversite kabul mektubu yükle\n3. **11.904 EUR transfer et** (Türkiye'den SWIFT veya Wise)\n4. **Bloke hesap onayı** belgesi al — bunu vize randevusunda kullan\n5. Almanya'ya gidince hesap aktif olur, ilk ay 992 EUR çekebilirsin\n\n**Karşılaştırma için:** /tr/tools/sperrkonto sayfasında 5 sağlayıcı yan yana.",
            'en' => "A Sperrkonto (blocked account) is a special bank account used as **proof of financial capacity** for the German student visa. After deposit, the money is **locked** for 1 year; a fixed monthly amount is unlocked (2026: **€992/month**, yearly **€11,904**).\n\nWhy blocked? German consulates require proof that you can cover **1 year of living expenses**.\n\n**Popular providers:**\n- **Expatrio** — Fast, fully online (€89 setup + €5/month)\n- **Fintiba** — Market leader since launch (€89 + €5/month)\n- **Deutsche Bank** — Traditional, opens from Türkiye (€150)\n- **Coracle**, **ICICI Bank**, **kredisetzer.de** — alternatives\n\n**Process:**\n1. Choose provider + apply online\n2. Upload passport + university acceptance letter\n3. **Transfer €11,904** (SWIFT or Wise from Türkiye)\n4. Receive **blocked account confirmation** — use for visa appointment\n5. Account activates upon arrival; first month €992 available",
            'de' => "Ein Sperrkonto dient als Finanzierungsnachweis für das Studienvisum. 2026: 11.904 EUR jährlich, 992 EUR/Monat freigegeben.",
        ],
        'related' => ['blue-card', 'studienkolleg', 'ects'],
        'official_url' => 'https://www.auswaertiges-amt.de/',
        'faq' => [
            [
                'q' => ['tr' => '2026 Sperrkonto tutarı ne kadar?', 'en' => 'What is the 2026 Sperrkonto amount?', 'de' => 'Wie hoch ist das Sperrkonto 2026?'],
                'a' => ['tr' => '11.904 EUR yıllık, 992 EUR aylık. Bu rakam yıllık Federal Hükümet kararıyla güncellenir.', 'en' => '€11,904 annually, €992 monthly. This amount is updated yearly by the Federal Government.', 'de' => '11.904 EUR jährlich, 992 EUR monatlich.'],
            ],
            [
                'q' => ['tr' => 'En hızlı Sperrkonto sağlayıcısı hangisi?', 'en' => 'Which Sperrkonto provider is fastest?', 'de' => 'Welcher Anbieter ist am schnellsten?'],
                'a' => ['tr' => 'Expatrio ve Fintiba 24-48 saatte hesap açar (online süreç). Deutsche Bank 1-2 hafta sürer.', 'en' => 'Expatrio and Fintiba open accounts within 24-48 hours (fully online). Deutsche Bank takes 1-2 weeks.', 'de' => 'Expatrio und Fintiba: 24-48 Stunden. Deutsche Bank: 1-2 Wochen.'],
            ],
            [
                'q' => ['tr' => 'Bursum varsa Sperrkonto gerekir mi?', 'en' => 'Do I need a Sperrkonto if I have a scholarship?', 'de' => 'Brauche ich ein Sperrkonto mit Stipendium?'],
                'a' => ['tr' => 'DAAD, EPOS gibi burslar resmi kaynak olarak kabul edilir; Sperrkonto yerine **burs onay mektubu** kullanılabilir. Burs tutarı asgari 992 EUR/ay olmalı.', 'en' => 'DAAD, EPOS, and similar scholarships qualify as official financial proof; the **scholarship award letter** can replace the Sperrkonto. Monthly amount must be at least €992.', 'de' => 'Anerkannte Stipendien (DAAD, EPOS) können das Sperrkonto ersetzen, wenn mindestens 992 EUR/Monat.'],
            ],
        ],
    ],

    'studienkolleg' => [
        'slug' => 'studienkolleg',
        'icon' => '📚',
        'title' => [
            'tr' => 'Studienkolleg',
            'en' => 'Studienkolleg',
            'de' => 'Studienkolleg',
        ],
        'short' => [
            'tr' => 'Diploması doğrudan tanınmayan uluslararası öğrenciler için 1 yıllık üniversite hazırlık programı. Sonunda Feststellungsprüfung (FSP) sınavı.',
            'en' => '1-year university preparation program for international students whose diploma is not directly recognized. Ends with Feststellungsprüfung (FSP) exam.',
            'de' => 'Einjähriger Vorbereitungskurs für ausländische Studierende, deren Zeugnis nicht direkt anerkannt wird. Abschluss mit Feststellungsprüfung.',
        ],
        'body' => [
            'tr' => "Studienkolleg, lise diploması Almanya'da **doğrudan tanınmayan** (Anabin'de H- veya H+- işaretli) uluslararası öğrenciler için **1 yıllık** hazırlık programıdır.\n\nProgramın amacı: öğrenciyi Alman üniversite seviyesine hazırlamak. Sonunda **Feststellungsprüfung (FSP)** sınavı yapılır — başarılı olunca öğrenci Alman lise diplomasına eşdeğer bir belge alır.\n\n**5 ana branş (Schwerpunktkurs):**\n- **T-Kurs** — Teknik / Mühendislik / Doğa Bilimleri\n- **M-Kurs** — Tıp / Biyoloji / Eczacılık\n- **W-Kurs** — İktisat / İşletme / Sosyal Bilimler\n- **G-Kurs** — Beşeri Bilimler / Dil / Felsefe\n- **S-Kurs** — Dil ve Edebiyat (modern diller)\n\n**Türk öğrenciler için:** Lise diploma Anabin'de **H+** işaretli — Studienkolleg GEREKMEZ, doğrudan başvuru yapılabilir.\n\nKimler Studienkolleg'e gider: Hindistan, Pakistan, Çin, Vietnam, Mısır, Suriye, Nijerya, Bangladeş, Sri Lanka gibi ülke vatandaşları (Anabin H-).\n\n**Studienkolleg türleri:** Devlet (ücretsiz, dönemde ~250 EUR sosyal katkı), Özel (yıllık 5.000-12.000 EUR). Devlet Studienkolleg'leri çok rekabetçi.",
            'en' => "Studienkolleg is a **1-year university preparation program** for international students whose high school diploma is **not directly recognized** in Germany (Anabin marked H- or H+-).\n\nGoal: prepare students for German university level. Ends with **Feststellungsprüfung (FSP)** exam.\n\n**5 main tracks (Schwerpunktkurs):**\n- **T-Kurs** — Technical / Engineering / Sciences\n- **M-Kurs** — Medicine / Biology / Pharmacy\n- **W-Kurs** — Economics / Business / Social Sciences\n- **G-Kurs** — Humanities / Languages / Philosophy\n- **S-Kurs** — Language and Literature\n\n**Turkish students:** Turkish high school diploma is Anabin **H+** — Studienkolleg NOT required, direct application possible.\n\nWho needs Studienkolleg: India, Pakistan, China, Vietnam, Egypt, Syria, Nigeria, Bangladesh, Sri Lanka (Anabin H-).\n\n**Types:** Public (free, ~€250 social contribution/semester), Private (yearly €5,000-12,000). Public Studienkollegs are highly competitive.",
            'de' => "Das Studienkolleg ist ein einjähriger Vorbereitungskurs für ausländische Studierende mit nicht anerkanntem Zeugnis. Abschluss: Feststellungsprüfung (FSP).",
        ],
        'related' => ['aps', 'uni-assist', 'anabin'],
        'official_url' => 'https://www.studienkollegs.de/',
        'faq' => [
            [
                'q' => ['tr' => 'Türk öğrencilere Studienkolleg gerekir mi?', 'en' => 'Do Turkish students need Studienkolleg?', 'de' => 'Brauchen türkische Studenten Studienkolleg?'],
                'a' => ['tr' => 'Hayır. Türk lise diploması Anabin H+ — doğrudan Alman üniversitesine başvurulabilir.', 'en' => 'No. Turkish high school diploma is Anabin H+ — direct application is possible.', 'de' => 'Nein, türkische Schulzeugnisse sind H+ und direkt anerkannt.'],
            ],
            [
                'q' => ['tr' => 'Studienkolleg ücretsiz mi?', 'en' => 'Is Studienkolleg free?', 'de' => 'Ist das Studienkolleg kostenlos?'],
                'a' => ['tr' => 'Devlet Studienkolleg\'leri ücretsiz (dönem başına ~250 EUR sosyal katkı). Özel Studienkolleg\'ler yıllık 5.000-12.000 EUR.', 'en' => 'Public Studienkollegs are free (~€250 semester fee). Private ones cost €5,000-12,000 yearly.', 'de' => 'Staatliche Studienkollegs sind kostenfrei. Private kosten 5.000-12.000 EUR jährlich.'],
            ],
            [
                'q' => ['tr' => 'Feststellungsprüfung (FSP) nedir?', 'en' => 'What is Feststellungsprüfung (FSP)?', 'de' => 'Was ist die Feststellungsprüfung?'],
                'a' => ['tr' => 'Studienkolleg sonunda yapılan bitirme sınavı. Almanca + branş dersleri sınanır. Başarılı olunca Alman lise diplomasına eşdeğer.', 'en' => 'Final exam at the end of Studienkolleg. Tests German + subject courses. Pass = equivalent to German high school diploma.', 'de' => 'Abschlussprüfung des Studienkollegs in Deutsch und Fachfächern.'],
            ],
        ],
    ],

    'blue-card' => [
        'slug' => 'blue-card',
        'icon' => '💳',
        'title' => [
            'tr' => 'Mavi Kart (Blue Card EU)',
            'en' => 'EU Blue Card',
            'de' => 'Blaue Karte EU',
        ],
        'short' => [
            'tr' => 'Yüksek nitelikli AB dışı çalışanlar için AB çapında geçerli oturma + çalışma izni. Yüksek maaş eşiği var.',
            'en' => 'EU-wide residence + work permit for highly qualified non-EU workers. Has minimum salary threshold.',
            'de' => 'EU-weite Aufenthalts- und Arbeitserlaubnis für qualifizierte Drittstaatsangehörige.',
        ],
        'body' => [
            'tr' => "Mavi Kart, **AB dışı yüksek nitelikli çalışanların** Almanya'ya (ve AB'ye) gelmesini kolaylaştıran bir oturma + çalışma iznidir. Almanya mezunu olan **uluslararası öğrenciler için en avantajlı yol**.\n\n**Şartlar:**\n1. Almanya veya AB tanınan üniversiteden alınmış **lisans veya yüksek lisans diploması**\n2. Diplomayla ilgili alanda **iş teklifi**\n3. **Yıllık brüt maaş** en az:\n   - **2026 normal eşik: 48.300 EUR**\n   - **Talep edilen meslekler (Mangelberufe — IT, mühendislik, doktor, matematik, doğa bilimleri): 43.759.20 EUR**\n   - **Yeni mezunlar (mezuniyetten sonra 3 yıl içinde): 43.759.20 EUR**\n\n**Avantajları:**\n- 4 yıl geçerli (ya da iş sözleşmesi süresi + 3 ay)\n- 21 ayda kalıcı oturma (B1 Almanca ile), 33 ayda B1 olmadan\n- Aile birleşimi hızlı, eş çalışma izni otomatik\n- AB'de **18 ay çalışma sonrası başka AB ülkesine taşınma hakkı**\n- 6 ay AB dışında kalma izni\n\nİş bulma: **Almanya'dan iş arama vizesi (Job-Seeker Visa) ile 6 ay arayabilirsin.** Mezuniyetten sonra **18 ay** Almanya'da iş arama izni var (öğrenci vizesinin uzatılmış hali).",
            'en' => "The EU Blue Card is a residence + work permit facilitating entry of **highly qualified non-EU workers** to Germany (and the EU). Most advantageous path for **international graduates of German universities**.\n\n**Requirements:**\n1. Recognized **Bachelor's or Master's degree** (from Germany or EU-recognized institution)\n2. **Job offer** in field related to degree\n3. **Annual gross salary** at least:\n   - **Standard 2026 threshold: €48,300**\n   - **Shortage occupations (Mangelberufe — IT, engineering, medicine, math, sciences): €43,759.20**\n   - **Recent graduates (within 3 years post-graduation): €43,759.20**\n\n**Benefits:**\n- 4-year validity (or work contract duration + 3 months)\n- Permanent residence in 21 months (with B1 German), 33 months without\n- Fast family reunification, automatic spouse work permit\n- After **18 months work, can move to another EU country**\n- Can stay 6 months outside EU\n\nFinding work: Job-Seeker Visa allows 6 months job search. **Post-graduation: 18 months job search** in Germany.",
            'de' => "Die Blaue Karte EU ist eine Aufenthalts- und Arbeitserlaubnis für qualifizierte Drittstaatsangehörige. Mindestgehalt 2026: 48.300 EUR (Mangelberufe: 43.759,20 EUR).",
        ],
        'related' => ['ects', 'sperrkonto', 'daad'],
        'official_url' => 'https://www.bamf.de/EN/Themen/MigrationAufenthalt/ZuwandererDrittstaaten/Mitarbeit/BlaueKarte/',
        'faq' => [
            [
                'q' => ['tr' => '2026 Mavi Kart maaş eşiği nedir?', 'en' => 'What is the 2026 Blue Card salary threshold?', 'de' => 'Wie hoch ist die Gehaltsgrenze 2026?'],
                'a' => ['tr' => 'Genel meslekler için yıllık brüt 48.300 EUR. Talep edilen meslekler ve yeni mezunlar için 43.759,20 EUR.', 'en' => 'General: €48,300 gross annually. Shortage occupations and new graduates: €43,759.20.', 'de' => 'Regelfall 48.300 EUR, Engpassberufe und Berufseinsteiger 43.759,20 EUR.'],
            ],
            [
                'q' => ['tr' => 'Mavi Kart kaç yıl geçerli?', 'en' => 'How long is the Blue Card valid?', 'de' => 'Wie lange gilt die Blaue Karte?'],
                'a' => ['tr' => '4 yıl, ya da iş sözleşmesi süresi + 3 ay (hangisi kısa ise).', 'en' => '4 years or duration of work contract + 3 months (whichever is shorter).', 'de' => '4 Jahre oder Vertragsdauer + 3 Monate.'],
            ],
            [
                'q' => ['tr' => 'Mavi Kart ile kalıcı oturma kaç yıl?', 'en' => 'Permanent residence with Blue Card?', 'de' => 'Wann gibt es Niederlassungserlaubnis?'],
                'a' => ['tr' => 'B1 Almanca ile **21 ay** çalıştıktan sonra. Almanca olmadan **33 ay**.', 'en' => 'After **21 months** of work with B1 German. **33 months** without German.', 'de' => '21 Monate mit B1-Deutsch, 33 Monate ohne.'],
            ],
        ],
    ],

    'daad' => [
        'slug' => 'daad',
        'icon' => '🎖️',
        'title' => [
            'tr' => 'DAAD (Alman Akademik Değişim Servisi)',
            'en' => 'DAAD — German Academic Exchange Service',
            'de' => 'DAAD — Deutscher Akademischer Austauschdienst',
        ],
        'short' => [
            'tr' => 'Alman üniversitelerinin ortak temsilcisi. Uluslararası öğrencilere burs, bilgi ve danışmanlık sağlar. 100+ ülkede 70+ ofis.',
            'en' => 'Joint organization of German universities. Provides scholarships, information, and counseling to international students. 70+ offices in 100+ countries.',
            'de' => 'Gemeinschaftseinrichtung deutscher Hochschulen. Bietet Stipendien, Information und Beratung.',
        ],
        'body' => [
            'tr' => "DAAD (Deutscher Akademischer Austauschdienst), 1925'te kurulmuş, **Alman üniversiteleri ve öğrenci dernekleri tarafından ortaklaşa yürütülen** uluslararası akademik değişim kurumudur. Federal Hükümet ve AB'den fon alır.\n\n**Hizmetleri:**\n\n1. **Burs Programları** — En önemli kaynak. Türk öğrencilere açık 50+ farklı burs:\n   - **DAAD Master Stipendium** — 992 EUR/ay + sigorta + uçak + Almanca kurs\n   - **DAAD PhD Stipendium** — 1.300 EUR/ay + araştırma desteği\n   - **EPOS (Development-Related Postgraduate Courses)** — Gelişmekte olan ülkeler için Master\n   - **Helmut-Schmidt-Programm** — Kamu politikası alanında\n   - **Konferans seyahat fonları**, **kısa süreli araştırma bursları**\n   - **Faculty exchange programları**\n\n2. **Veritabanı:**\n   - **DAAD International Programmes** — Tüm İngilizce/Almanca programlar (2.500+)\n   - **DAAD Scholarship Database** — 250+ burs\n\n3. **Bilgi merkezleri** — Türkiye'de İstanbul ofisi (Galatasaray Üniversitesi yakını), Ankara'da DAAD Lektörü. Ücretsiz danışmanlık.\n\n4. **Sertifika programları** — German Universities Centre of Excellence, summer schools, language courses.\n\n**Burs başvurusu için kritik:**\n- Akademik başarı (GPA en az 75/100 veya 2.5/4.0)\n- Motivasyon mektubu güçlü\n- 2 referans mektubu\n- Almanca veya İngilizce dil sertifikası\n- DAAD bursu kazanmak Sperrkonto'ya gerek bırakmaz",
            'en' => "DAAD, founded in 1925, is the **joint organization of German universities and student bodies** for international academic exchange. Funded by the Federal Government and EU.\n\n**Services:**\n\n1. **Scholarship Programs** — Most important resource. 50+ scholarships open to international students:\n   - **DAAD Master Scholarship** — €992/month + insurance + flight + German course\n   - **DAAD PhD Scholarship** — €1,300/month + research support\n   - **EPOS** — Master's for developing countries\n   - **Helmut-Schmidt Program** — Public policy\n   - **Conference travel grants**, **short-term research grants**\n\n2. **Databases:**\n   - **DAAD International Programmes** — 2,500+ English/German programs\n   - **DAAD Scholarship Database** — 250+ scholarships\n\n3. **Information Centers** — Istanbul office (near Galatasaray University). Free counseling.\n\n4. **Certificate programs**, summer schools, language courses.\n\n**Critical for scholarship application:**\n- Academic excellence (GPA ≥75/100 or 2.5/4.0)\n- Strong motivation letter\n- 2 reference letters\n- German or English certificate\n- DAAD scholarship eliminates need for Sperrkonto",
            'de' => "Der DAAD ist die zentrale Organisation für internationalen akademischen Austausch in Deutschland. Bietet 250+ Stipendien.",
        ],
        'related' => ['aps', 'uni-assist', 'sperrkonto'],
        'official_url' => 'https://www.daad.de/',
        'faq' => [
            [
                'q' => ['tr' => 'DAAD bursu Sperrkonto yerine geçer mi?', 'en' => 'Can DAAD replace Sperrkonto?', 'de' => 'Ersetzt DAAD das Sperrkonto?'],
                'a' => ['tr' => 'Evet, DAAD onay mektubu konsolosluk tarafından maddi yeterlilik belgesi olarak kabul edilir.', 'en' => 'Yes. The DAAD award letter is accepted by consulates as proof of financial means.', 'de' => 'Ja, das DAAD-Förderschreiben gilt als Finanzierungsnachweis.'],
            ],
            [
                'q' => ['tr' => 'DAAD bursunu kim kazanır?', 'en' => 'Who wins DAAD scholarships?', 'de' => 'Wer bekommt DAAD-Stipendien?'],
                'a' => ['tr' => 'Yüksek akademik başarı, ilgili alan + güçlü motivasyon. GPA 80+/100, dil sertifikası, akademik referanslar, projeli motivasyon mektubu kazanan profil.', 'en' => 'High academic achievement, relevant field, strong motivation. Winners typically have GPA 80+/100, language certificate, academic references, project-based motivation letter.', 'de' => 'Hohe akademische Leistung, fachliche Eignung, starke Motivation.'],
            ],
            [
                'q' => ['tr' => 'DAAD başvuru ne zaman?', 'en' => 'DAAD deadlines?', 'de' => 'DAAD-Fristen?'],
                'a' => ['tr' => 'Master için genelde Kasım-Aralık (sonraki Eylül için). EPOS için Eylül-Ekim. Her programın ayrı deadline\'ı var.', 'en' => 'Master: usually Nov-Dec (for following September). EPOS: Sep-Oct. Each program has its own deadline.', 'de' => 'Master meist November-Dezember für das folgende Wintersemester.'],
            ],
        ],
    ],

    'anabin' => [
        'slug' => 'anabin',
        'icon' => '🔍',
        'title' => [
            'tr' => 'Anabin (KMK Diploma Veritabanı)',
            'en' => 'Anabin — KMK Diploma Database',
            'de' => 'Anabin — KMK Datenbank',
        ],
        'short' => [
            'tr' => 'Almanya Eğitim Bakanları Konferansı\'nın (KMK) işlettiği resmi diploma denklik veritabanı. Hangi ülke diplomasının Almanya\'da nasıl tanındığını gösterir.',
            'en' => 'Official diploma recognition database run by the German Conference of Education Ministers (KMK). Shows how each country\'s diploma is recognized in Germany.',
            'de' => 'Offizielle Datenbank der Kultusministerkonferenz für Bildungsabschlüsse.',
        ],
        'body' => [
            'tr' => "Anabin (KMK Zentralstelle für ausländisches Bildungswesen), Almanya'da uluslararası diplomaların **resmi denklik veritabanı**dır. Üniversiteler, konsolosluklar ve İŞKUR-benzeri kurumlar buradaki bilgileri kullanır.\n\n**Anabin sınıflandırması:**\n\n| Kod | Anlamı |\n|---|---|\n| **H+** | Tam tanınır — Alman lise diplomasına eşdeğer, doğrudan başvuru |\n| **H+-** | Kısmi tanınır — Bazı bölümler için geçerli, üniversite kararı |\n| **H-** | Doğrudan tanınmaz — Studienkolleg veya 1-2 dönem üniversite eğitimi gerekli |\n\n**Türk lise diploması: H+** (doğrudan başvuru yapılabilir).\n\n**Aranabilir bilgiler:**\n1. **Ülke** (Türkiye → tüm üniversiteler ve diploma türleri)\n2. **Üniversite** (örn. İTÜ → kayıtlı mı? hangi bölümler tanınır?)\n3. **Diploma türü** (Lise, Bachelor, Master, PhD)\n4. **Bölüm adı** (Engineering, Medicine vb. ile özel kurallar)\n\n**Kullanım:**\n1. anabin.kmk.org → Hochschulabschlüsse oder Schulabschlüsse\n2. Ülke seç + üniversite/lise türü\n3. Sonuç: H+, H+-, H- ve açıklama\n\n**Önemli:** Anabin **bilgilendirme** içindir. Final karar üniversitenin Internationales Büro'sundadır.",
            'en' => "Anabin (KMK Zentralstelle für ausländisches Bildungswesen) is Germany's **official diploma recognition database**, run by the Conference of Education Ministers. Universities, consulates, and employment offices use this data.\n\n**Anabin classification:**\n\n| Code | Meaning |\n|---|---|\n| **H+** | Fully recognized — equivalent to German Abitur, direct application possible |\n| **H+-** | Partially recognized — valid for some fields, university decides |\n| **H-** | Not directly recognized — Studienkolleg or 1-2 semesters of university required |\n\n**Turkish high school diploma: H+** (direct application).\n\n**Searchable:**\n1. **Country** (Türkiye → all universities and diploma types)\n2. **University** (e.g. ITU → registered? which programs recognized?)\n3. **Diploma type** (High school, Bachelor, Master, PhD)\n4. **Field name** (Engineering, Medicine with special rules)\n\n**Important:** Anabin is **informational**. Final decision rests with the university's International Office.",
            'de' => "Anabin ist die KMK-Datenbank zur Anerkennung ausländischer Bildungsabschlüsse. Klassifizierungen: H+ (anerkannt), H+- (teilweise), H- (nicht direkt anerkannt).",
        ],
        'related' => ['aps', 'studienkolleg', 'uni-assist'],
        'official_url' => 'https://anabin.kmk.org/',
        'faq' => [
            [
                'q' => ['tr' => 'Türk diploması Anabin\'de hangi sınıfta?', 'en' => 'How is Turkish diploma classified in Anabin?', 'de' => 'Wie ist das türkische Zeugnis klassifiziert?'],
                'a' => ['tr' => 'Türk lise diploması H+ (doğrudan tanınır). Üniversite diploması da çoğunlukla H+ (kayıtlı üniversiteler için).', 'en' => 'Turkish high school diploma is H+ (directly recognized). University diplomas are also mostly H+ for registered universities.', 'de' => 'Türkische Schulzeugnisse sind H+ (direkt anerkannt).'],
            ],
            [
                'q' => ['tr' => 'Anabin\'de üniversitem yoksa ne yapmalıyım?', 'en' => 'My university is not in Anabin — what to do?', 'de' => 'Meine Hochschule fehlt — was tun?'],
                'a' => ['tr' => 'Üniversitenin Internationales Büro\'suna belgelerini yollayıp manuel denklik talep et. APS gerekirse APS başvurusu da yap (Çin/Hindistan/Pakistan/Vietnam vatandaşları).', 'en' => 'Submit documents to the university\'s International Office for manual recognition. If APS-required nationality, also apply for APS.', 'de' => 'Direkt beim Internationalen Büro der Hochschule beantragen.'],
            ],
            [
                'q' => ['tr' => 'H+- ne anlama gelir?', 'en' => 'What does H+- mean?', 'de' => 'Was bedeutet H+-?'],
                'a' => ['tr' => 'Kısmen tanınır — yalnızca BAZI bölümler için doğrudan başvuru yapılabilir. Hangi bölümler dahil olduğu Anabin sayfasında listelenir.', 'en' => 'Partially recognized — direct application possible for SOME fields. Eligible fields are listed in Anabin.', 'de' => 'Teilweise anerkannt für bestimmte Fachrichtungen.'],
            ],
        ],
    ],

    'ects' => [
        'slug' => 'ects',
        'icon' => '🎓',
        'title' => [
            'tr' => 'ECTS (Avrupa Kredi Transfer Sistemi)',
            'en' => 'ECTS — European Credit Transfer System',
            'de' => 'ECTS — Europäisches Kreditpunktesystem',
        ],
        'short' => [
            'tr' => '30 Avrupa ülkesinde geçerli, üniversite kredilerini standartlaştıran sistem. 1 ECTS = 25-30 saat öğrenci iş yükü.',
            'en' => 'Credit standardization system used in 30 European countries. 1 ECTS = 25-30 hours of student workload.',
            'de' => 'Europäisches System zur Anrechnung von Studienleistungen. 1 ECTS = 25-30 Stunden Workload.',
        ],
        'body' => [
            'tr' => "ECTS (European Credit Transfer and Accumulation System), Avrupa'daki yükseköğretim kurumları arasında **kredi transferi** ve **akademik tanınma** için standart sistemdir. Bologna Süreci'nin parçası.\n\n**Temel kurallar:**\n- **1 ECTS = 25-30 saat** öğrenci iş yükü (ders + sınav hazırlığı + ev ödevi dahil)\n- **1 akademik yıl = 60 ECTS** (Almanya'da 2 dönem)\n- **1 dönem = 30 ECTS**\n- **Bachelor = 180-240 ECTS** (3-4 yıl)\n- **Master = 60-120 ECTS** (1-2 yıl)\n- **PhD = ülkesine göre ECTS hesaplanmaz**\n\n**Türkiye'den Almanya'ya:** Türk üniversiteleri **çoğunlukla ECTS uyumlu** — kredi karşılığı 1:1 transfer olur. Yatay geçişlerde her ders için ECTS karşılığı kontrol edilir.\n\n**Önemli:** Bazı Türk üniversiteleri AKTS (Türkçe ECTS karşılığı) kullanır — Almanca AKTS = ECTS değildir, kredi yapısı farklı olabilir.\n\n**Transcript of Records:** Üniversite, her dersin **ECTS değerini + notunu** transkriptte gösterir. Bu Almanya'ya başvuruda zorunlu.\n\n**ECTS Grading Scale:**\n- A — En iyi %10\n- B — Sonraki %25\n- C — Sonraki %30\n- D — Sonraki %25\n- E — Son %10 (geçti)\n- FX/F — Kaldı\n\n**Türkiye AA-FF → ECTS** dönüşümü için /tr/tools/grade-converter aracını kullan.",
            'en' => "ECTS (European Credit Transfer and Accumulation System) is the standard for **credit transfer** and **academic recognition** across European higher education. Part of the Bologna Process.\n\n**Core rules:**\n- **1 ECTS = 25-30 hours** of student workload\n- **1 academic year = 60 ECTS** (2 semesters in Germany)\n- **1 semester = 30 ECTS**\n- **Bachelor = 180-240 ECTS** (3-4 years)\n- **Master = 60-120 ECTS** (1-2 years)\n\n**From Türkiye to Germany:** Turkish universities are **mostly ECTS-compliant** — credits transfer 1:1. For lateral transfers, each course's ECTS is checked.\n\n**Important:** Some Turkish universities use AKTS (Turkish ECTS equivalent) — not identical to German ECTS, structure may differ.\n\n**Transcript of Records:** University shows each course's **ECTS value + grade**. Required for German applications.\n\n**ECTS Grading Scale:**\n- A — Top 10%\n- B — Next 25%\n- C — Next 30%\n- D — Next 25%\n- E — Last 10% (passed)\n- FX/F — Failed\n\nUse /en/tools/grade-converter for Türkiye AA-FF → ECTS conversion.",
            'de' => "ECTS ist das Europäische Kreditpunktesystem (1 Punkt = 25-30 Stunden Arbeitsaufwand). 60 ECTS = 1 akademisches Jahr.",
        ],
        'related' => ['uni-assist', 'anabin', 'studienkolleg'],
        'official_url' => 'https://education.ec.europa.eu/levels/higher-education/inclusion-connectivity/european-credit-transfer-accumulation-system',
        'faq' => [
            [
                'q' => ['tr' => '1 ECTS ne kadar iş yükü?', 'en' => 'How much workload is 1 ECTS?', 'de' => 'Wie viele Stunden ist 1 ECTS?'],
                'a' => ['tr' => 'Yaklaşık 25-30 saat (ders saati + sınav hazırlığı + ödev + okuma dahil).', 'en' => 'Approximately 25-30 hours (class time + exam prep + assignments + reading).', 'de' => 'Etwa 25-30 Stunden Workload.'],
            ],
            [
                'q' => ['tr' => 'Türk AKTS = Alman ECTS mı?', 'en' => 'Is Turkish AKTS = German ECTS?', 'de' => 'Ist AKTS = ECTS?'],
                'a' => ['tr' => 'Çoğu Türk üniversitesinde evet — Bologna uyumlu. Ama her dersin gerçek iş yükü kontrol edilmeli; bazı eski programlarda farklılık olabilir.', 'en' => 'Mostly yes in Bologna-compliant Turkish universities. But actual workload should be verified; older programs may differ.', 'de' => 'Bei Bologna-konformen Hochschulen meist gleichwertig, aber Workload kann variieren.'],
            ],
            [
                'q' => ['tr' => 'Yüksek lisans için kaç ECTS gerekir?', 'en' => 'How many ECTS for Master\'s?', 'de' => 'Wie viele ECTS für den Master?'],
                'a' => ['tr' => 'Almanya\'da Master programları 60-120 ECTS arasında. 4 dönem (2 yıl) = 120 ECTS yaygın.', 'en' => 'Master programs in Germany: 60-120 ECTS. 4 semesters (2 years) = 120 ECTS is common.', 'de' => 'Masterprogramme umfassen 60-120 ECTS, meist 120 in 2 Jahren.'],
            ],
        ],
    ],

];
