<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Brief → blog: "Anmeldung adım adım" tr/en/de (3 yazı, tek group).
 * content_html boş → blog:render-html doldurur. Kategori 8, idempotent.
 */
return new class extends Migration
{
    private string $group = 'a1b2c3d4-0002-4000-8000-000000000002';

    private array $slugs = [
        'anmeldung-adim-adim-almanyada-sehir-kaydi-burgeramt',
        'anmeldung-step-by-step-city-registration-germany',
        'anmeldung-schritt-fuer-schritt-buergeramt',
    ];

    public function up(): void
    {
        $now = now();

        $tr = <<<'MD'
# Anmeldung Adım Adım: Almanya'da Şehir Kaydı (Bürgeramt)

> **30 saniyelik özet:** Anmeldung, yeni adresini resmi olarak kaydettirmendir — genelde taşındıktan sonra **14 gün** içinde Bürgeramt'ta yapılır. Anmeldung olmadan **banka hesabı, sağlık sigortası, oturum izni ve vergi numarası** çoğu zaman ilerlemez. En kritik belge ev sahibinden alınan **Wohnungsgeberbestätigung**.

---

## Anmeldung nedir, neden önemli?

Anmeldung = ikamet kaydı; aldığın belgeye **Meldebescheinigung** denir. Bu kayıt:

- Vergi kimlik numaranı (**Steuer-ID**) tetikler (birkaç hafta içinde posta ile gelir),
- Banka hesabı, telefon kontratı, kütüphane kartı için ön şarttır,
- Oturum izni (**Aufenthaltstitel**) başvurusunda istenir,
- Radyo-TV katkısı (Rundfunkbeitrag) kaydını başlatır.

Kısacası Anmeldung, Alman bürokrasisinin giriş kapısıdır.

## Gerekli belgeler

1. **Geçerli pasaport / kimlik**
2. **Wohnungsgeberbestätigung** — ev sahibinin (veya ana kiracının) imzaladığı, o adreste oturduğunu doğrulayan belge. **En çok unutulan ve en kritik belge budur.**
3. **Anmeldeformular** — Bürgeramt sitesinden indirilir; çoğu şehir online doldurmaya da izin verir.
4. (Bazı şehirler) vize / oturum belgesi.

## Adım adım

1. **Randevu al (Termin):** Şehrin Bürgeramt sitesinden online randevu. Büyük şehirlerde (Berlin, München) randevular haftalar önceden dolar — taşınmadan önce bakmaya başla.
2. **Wohnungsgeberbestätigung'u imzalat:** Taşınır taşınmaz ev sahibinden iste.
3. **Formu doldur:** Anmeldeformular'ı eksiksiz doldur.
4. **Bürgeramt'a git:** Belgelerle randevuna git; 10–15 dakikada Meldebescheinigung elinde çıkar.
5. **Steuer-ID'yi bekle:** Birkaç hafta içinde posta ile gelir.

## Yaygın tuzaklar

- **"Randevu çıkmıyor":** Geciktirme — başka ilçenin (Bezirk) Bürgeramt'ı boş olabilir, iptal takibi yap; bazı şehirlerde randevusuz saatler (offene Sprechstunde) vardır.
- **WG'de oturuyorsan:** Ana kiracı Wohnungsgeberbestätigung'u imzalamalı; sözlü anlaşma yetmez.
- **14 günü kaçırmak:** Teorik para cezası (Bußgeld) riski var; pratikte tolere edilse de oturum/banka işlerini geciktirir — erken yap.
- **Geçici adres:** Otel/Airbnb'de Anmeldung yapılmaz; kalıcı adres + Wohnungsgeberbestätigung şart.

## Sonraki adımlar

Anmeldung sonrası sıra: sağlık sigortasını tamamlama, banka hesabını aktive etme, oturum izni randevusu. Meldebescheinigung'un birkaç kopyasını sakla — neredeyse her resmi işlemde istenir.

> İlgili: Vize ve Sperrkonto süreçlerini halletmeden gelme; Anmeldung bunların üstüne gelen ilk yerel adımdır.
MD;

        $en = <<<'MD'
# Anmeldung Step by Step: Registering Your Address in Germany (Bürgeramt)

> **30-second summary:** Anmeldung is officially registering your new address — usually done at the Bürgeramt within **14 days** of moving in. Without it, a **bank account, health insurance, residence permit and tax ID** often can't move forward. The most critical document is the **Wohnungsgeberbestätigung** from your landlord.

---

## What is Anmeldung and why does it matter?

Anmeldung = address registration; the document you receive is the **Meldebescheinigung**. This registration:

- Triggers your tax ID (**Steuer-ID**), which arrives by post within a few weeks,
- Is a prerequisite for a bank account, phone contract and library card,
- Is required when applying for a residence permit (**Aufenthaltstitel**),
- Starts your broadcasting-fee (Rundfunkbeitrag) registration.

In short, Anmeldung is the gateway into German bureaucracy.

## Documents you need

1. **Valid passport / ID**
2. **Wohnungsgeberbestätigung** — a form signed by your landlord (or main tenant) confirming you live at the address. **This is the most forgotten and most critical document.**
3. **Anmeldeformular** — downloaded from the Bürgeramt website; many cities allow filling it online.
4. (Some cities) visa / residence document.

## Step by step

1. **Book an appointment (Termin):** Online via your city's Bürgeramt site. In big cities (Berlin, Munich) slots fill weeks ahead — start looking before you move.
2. **Get the Wohnungsgeberbestätigung signed:** Ask your landlord as soon as you move in.
3. **Fill in the form:** Complete the Anmeldeformular in full.
4. **Go to the Bürgeramt:** Attend your appointment with the documents; you walk out with the Meldebescheinigung in 10–15 minutes.
5. **Wait for the Steuer-ID:** It arrives by post within a few weeks.

## Common pitfalls

- **"No appointments available":** Don't delay — another district's (Bezirk) Bürgeramt may be free, track cancellations; some cities have walk-in hours (offene Sprechstunde).
- **Living in a WG (shared flat):** The main tenant must sign the Wohnungsgeberbestätigung; a verbal agreement isn't enough.
- **Missing the 14 days:** There's a theoretical fine (Bußgeld); in practice it's often tolerated but it delays your bank/residence steps — do it early.
- **Temporary address:** You can't register at a hotel/Airbnb; you need a permanent address + Wohnungsgeberbestätigung.

## Next steps

After Anmeldung: complete your health insurance, activate your bank account, book the residence-permit appointment. Keep several copies of the Meldebescheinigung — almost every official process asks for it.

> Related: don't arrive before sorting your visa and Sperrkonto; Anmeldung is the first local step that comes on top of those.
MD;

        $de = <<<'MD'
# Anmeldung Schritt für Schritt: Wohnsitz in Deutschland anmelden (Bürgeramt)

> **Zusammenfassung in 30 Sekunden:** Die Anmeldung ist die offizielle Registrierung deiner neuen Adresse — meist innerhalb von **14 Tagen** nach dem Einzug beim Bürgeramt. Ohne sie kommen **Bankkonto, Krankenversicherung, Aufenthaltstitel und Steuer-ID** oft nicht voran. Das wichtigste Dokument ist die **Wohnungsgeberbestätigung** vom Vermieter.

---

## Was ist die Anmeldung und warum ist sie wichtig?

Anmeldung = Wohnsitzregistrierung; das Dokument, das du erhältst, ist die **Meldebescheinigung**. Diese Registrierung:

- Löst deine Steuer-ID (**Steuer-Identifikationsnummer**) aus, die innerhalb weniger Wochen per Post kommt,
- Ist Voraussetzung für Bankkonto, Handyvertrag und Bibliotheksausweis,
- Wird beim Antrag auf einen Aufenthaltstitel verlangt,
- Startet deine Anmeldung zum Rundfunkbeitrag.

Kurz gesagt: Die Anmeldung ist das Eingangstor in die deutsche Bürokratie.

## Benötigte Dokumente

1. **Gültiger Reisepass / Ausweis**
2. **Wohnungsgeberbestätigung** — ein vom Vermieter (oder Hauptmieter) unterschriebenes Formular, das bestätigt, dass du an der Adresse wohnst. **Das ist das am häufigsten vergessene und wichtigste Dokument.**
3. **Anmeldeformular** — von der Bürgeramt-Website; viele Städte erlauben das Ausfüllen online.
4. (Manche Städte) Visum / Aufenthaltsdokument.

## Schritt für Schritt

1. **Termin buchen:** Online über die Bürgeramt-Seite deiner Stadt. In Großstädten (Berlin, München) sind Termine Wochen im Voraus weg — fang vor dem Umzug an zu suchen.
2. **Wohnungsgeberbestätigung unterschreiben lassen:** Bitte deinen Vermieter direkt nach dem Einzug darum.
3. **Formular ausfüllen:** Fülle das Anmeldeformular vollständig aus.
4. **Zum Bürgeramt gehen:** Erscheine mit den Dokumenten zum Termin; nach 10–15 Minuten hast du die Meldebescheinigung in der Hand.
5. **Auf die Steuer-ID warten:** Sie kommt innerhalb weniger Wochen per Post.

## Häufige Stolperfallen

- **„Keine Termine frei":** Nicht aufschieben — das Bürgeramt eines anderen Bezirks kann frei sein, verfolge Stornierungen; manche Städte haben offene Sprechstunden.
- **Leben in einer WG:** Der Hauptmieter muss die Wohnungsgeberbestätigung unterschreiben; eine mündliche Absprache reicht nicht.
- **Die 14 Tage verpassen:** Es gibt ein theoretisches Bußgeld; in der Praxis oft toleriert, verzögert aber deine Bank-/Aufenthaltsschritte — mach es früh.
- **Vorübergehende Adresse:** Im Hotel/Airbnb kannst du dich nicht anmelden; du brauchst eine feste Adresse + Wohnungsgeberbestätigung.

## Nächste Schritte

Nach der Anmeldung: Krankenversicherung abschließen, Bankkonto aktivieren, Termin für den Aufenthaltstitel buchen. Bewahre mehrere Kopien der Meldebescheinigung auf — fast jeder offizielle Vorgang verlangt sie.

> Verwandt: Komm nicht an, bevor Visum und Sperrkonto geregelt sind; die Anmeldung ist der erste lokale Schritt, der danach kommt.
MD;

        $rows = [
            ['locale' => 'tr', 'slug' => $this->slugs[0],
                'title' => "Anmeldung Adım Adım: Almanya'da Şehir Kaydı (Bürgeramt)",
                'excerpt' => 'Almanya\'da şehir kaydı (Anmeldung) nedir, hangi belgeler gerekir, Wohnungsgeberbestätigung nereden alınır ve 14 günü kaçırmamak için adım adım rehber.',
                'content_md' => $tr, 'reading_minutes' => 5],
            ['locale' => 'en', 'slug' => $this->slugs[1],
                'title' => 'Anmeldung Step by Step: Registering Your Address in Germany (Bürgeramt)',
                'excerpt' => 'What Anmeldung (city registration) is, which documents you need, where to get the Wohnungsgeberbestätigung, and a step-by-step guide to not miss the 14-day window.',
                'content_md' => $en, 'reading_minutes' => 5],
            ['locale' => 'de', 'slug' => $this->slugs[2],
                'title' => 'Anmeldung Schritt für Schritt: Wohnsitz in Deutschland anmelden (Bürgeramt)',
                'excerpt' => 'Was die Anmeldung ist, welche Dokumente du brauchst, woher die Wohnungsgeberbestätigung kommt und eine Schritt-für-Schritt-Anleitung, um die 14-Tage-Frist nicht zu verpassen.',
                'content_md' => $de, 'reading_minutes' => 5],
        ];

        foreach ($rows as $r) {
            if (DB::table('posts')->where('slug', $r['slug'])->where('locale', $r['locale'])->exists()) {
                continue;
            }
            DB::table('posts')->insert([
                'locale' => $r['locale'],
                'translation_group_id' => $this->group,
                'type' => 'blog',
                'category_id' => 8,
                'title' => $r['title'],
                'slug' => $r['slug'],
                'excerpt' => $r['excerpt'],
                'content_md' => $r['content_md'],
                'content_html' => null,
                'reading_minutes' => $r['reading_minutes'],
                'meta_title' => $r['title'],
                'meta_description' => $r['excerpt'],
                'is_published' => true,
                'published_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('posts')->where('translation_group_id', $this->group)->delete();
    }
};
