<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

/**
 * İÇERİK TAMAMLAMA (EN) + OLGU DÜZELTMESİ (TR/DE): öğrenci vizesi rehberi.
 *
 * 1) EN sürümü taslaktı: 192 kelime / 1.352 karakter — TR ve DE ~14-17k karakter. Yani sitenin
 *    en çok aranan sayfalarından birinin İngilizcesi pratikte yoktu. EN gövdesi TR/DE ile aynı
 *    yapıda (7 bölüm + SSS) tam olarak yazıldı.
 *
 * 2) Üç dilde de duran eskimiş rakam düzeltildi: öğrencinin çalışma hakkı 2024 Fachkräfte-
 *    einwanderungsgesetz sonrası **140 tam gün / 280 yarım gün** (§ 16b Abs. 3 AufenthG,
 *    gesetze-im-internet.de/aufenthg_2004/__16b.html). Yazıda hâlâ eski 120/240 rakamı vardı.
 *    Ayrıca "ders saatlerinde çalışma yasak" ifadesi yanlıştı: yasak değil, yıllık gün üst sınırı
 *    var; üniversite içi HiWi işleri bu hesabın dışında tutulur.
 *
 * Doğrulanan rakamlar (Eylül 2026): Sperrkonto 992 €/ay = 11.904 €/yıl · ulusal (D) vize harcı
 * 75 € (Schengen 90 € değil) · Remonstration 1 Temmuz 2025'te kaldırıldı.
 * Slug/başlık/kategori/çeviri grubu korunuyor; yalnızca gövde yazılıyor.
 */
return new class extends Migration
{
    public function up(): void
    {
        $enBody = <<<'MD'
> **Short answer:** Three documents decide a German student visa application from Türkiye: your **admission letter** (Zulassung), a **blocked account** (Sperrkonto, **€11,904** for the year — €992 per month, 2026), and **health insurance** (roughly €80–125 per month). Expect **4–12 weeks** for a consulate appointment and **6–12 weeks** for a decision. The national visa fee is **€75**. Remonstration was abolished on 1 July 2025; if you are refused, what remains is a new application or a claim before the Berlin Administrative Court.

---

## Which visa do you need to study in Germany?

As a Turkish citizen staying longer than 90 days, you need a **national (D-type) visa**. You cannot start a degree on a Schengen tourist visa — even if you enter the country, that status is **incompatible with work rights and long-term residence**, and you risk being turned away at the border.

There are three national visa types:

| Visa type | When it applies | Duration | What comes next |
|---|---|---|---|
| **§16b – Studienvisum** (student visa) | You already hold a university admission | 1 year, extendable | Converted into a residence permit (Aufenthaltstitel) in Germany |
| **§17 – Studienbewerbervisum** (applicant visa) | No admission yet; you will apply from Germany | 3–9 months | Converted into a student visa once you are admitted |
| **§16f – Sprachkursvisum** (language course visa) | Intensive language course only | up to 12 months | Not tied to a university; a language school admission is enough |

This guide focuses on the **§16b student visa**, which is what around nine out of ten Turkish students use.

---

## 1. Which mission are you supposed to apply to?

Germany runs **three consulates and one embassy** handling visas in Türkiye:

| Mission | Area of responsibility (based on your registered residence) |
|---|---|
| **Ankara — Embassy of the Federal Republic of Germany** | Ankara, Central Anatolia, Black Sea region, Eastern Anatolia |
| **Istanbul — Consulate General** | Marmara region: Tekirdağ, Edirne, Bursa, Bilecik, Kocaeli, Yalova, Sakarya |
| **İzmir — Consulate General** | Aegean and Mediterranean provinces |
| **Antalya** | Antalya province only |

**Applying to the wrong mission gets you rejected.** Jurisdiction follows your registered address — living in Ankara does not let you apply in İzmir because appointments there look easier.

> 💡 Verify the current jurisdiction on **`auswaertiges-amt.de`** or `germany.diplo.de/tr-en`. These boundaries change from time to time.

---

## 2. The appointment system

German missions in Türkiye manage appointments through an external service provider (iDATA for some missions, VFS Global for others). The appointment itself is **booked online and free of charge** — only the visa fee is paid at the mission.

### Steps

1. Create an account on the provider named on **your** mission's official page — do not trust third-party sites.
2. Choose the visa category: **national visa — study**.
3. Pick the mission, date and time.
4. Print the confirmation; you need it to enter the building.

### Typical waiting times (2026 average)

| Mission | Typical wait |
|---|---|
| Istanbul | 4–8 weeks |
| Ankara | 6–12 weeks |
| İzmir | 4–10 weeks |

> ⚠️ **September and October are the worst months**, because everyone is chasing the winter semester. Start tracking slots about six months ahead and aim to apply at least 12 weeks before you need to travel. Slots are released in batches several times a week — check in the morning and again in the early afternoon. For a fuller strategy, see our [consulate appointment guide](/en/blog/germany-consulate-visa-appointment-2026-waiting-times-and-city-strategy-en).

---

## 3. Documents — the complete list

Every item below is genuinely required. A missing document is a reason to refuse the file, sometimes on the spot.

### A) Application form and photos

- **Videx application form** (filled in online, printed out)
- **Two biometric photos** (35×45 mm, taken within the last six months)
- **Passport** valid for at least 12 months with two blank pages
- **A copy of the passport** data page

### B) Admission letter (Zulassung)

The **Zulassungsbescheid** from your university, in German or English. The mission wants the original.

Is yours a **conditional admission** (bedingte Zulassung), issued "subject to a language certificate"? Some missions accept it, others insist on an unconditional admission. Check before you book.

> 💡 No admission yet? The **§17 applicant visa** lets you run the application process from Germany for 3–9 months and convert to a student visa once you are admitted.

### C) Proof of finances — the decisive document

The **Sperrkonto** (blocked account) is the route about 95% of Turkish students take.

Required for 2026: **€11,904** for the year, released to you at **€992 per month**.

| Provider | Setup fee | Monthly fee | Typical time from Türkiye |
|---|---|---|---|
| **Expatrio** | €49 | €5 | 1–3 business days |
| **Fintiba** | €89 | €4.90 | 1–3 business days |
| **Coracle** | €79 | €5.50 | 2–5 business days |
| **Deutsche Bank** | none | none | 3–5 weeks (branch appointment required) |

Once the account is funded you receive a **Sperrkontobestätigung** — submit the original to the mission. Fees change; check the provider's current pricing. Our [blocked account guide](/en/blog/sperrkonto-for-a-german-visa-what-is-it-how-much-and-en) goes through the process in detail.

**Alternatives, all used less often:**

- **Verpflichtungserklärung** — a formal undertaking signed by a sponsor in Germany at their local Ausländerbehörde, covering your living costs and health insurance.
- **A scholarship** — DAAD or another recognised award, provided the monthly amount reaches the same threshold.
- **A bank guarantee** from a German bank, which is rare in practice.

### D) Health insurance

You need cover that is **valid in Germany** from the day you arrive. Three common paths:

1. **A Turkish travel policy** for the first weeks only — replaced when you enrol.
2. **International private cover** (Mawista, Care Concept and similar), roughly €80–110 per month, and accepted for the visa.
3. **German statutory insurance** (TK, AOK, Barmer), around €120–130 per month including long-term care contributions, applied for from Türkiye.

> 💡 Many students start on private cover and switch to statutory insurance when they enrol. Compare providers in our [student health insurance guide](/en/blog/germany-student-health-insurance-2026-tk-vs-dak-vs-mawista-vs-en).

### E) Educational documents

- **High-school diploma and transcript**, apostilled and sworn-translated into German or English
- For a bachelor's: school grades and your **YKS result document**
- For a master's: **bachelor's degree and transcript**, apostilled and translated
- **VPD** from uni-assist, where your university requires one
- **Language certificate** (TestDaF, DSH, IELTS, TOEFL) if you already hold one

> 📝 The apostille chain runs **notary → district governorate (Kaymakamlık) → sworn translator** and takes roughly 5–10 business days. Start it early; it is the step people underestimate.

### F) Letter of motivation

One to two pages in German or English. It is not always on the official checklist, but it is requested in the overwhelming majority of student cases. Cover:

- why Germany, and why this programme at this university
- how your academic background leads into it
- what you plan to do after graduating — **be concrete about your plans, including how the degree serves your career; vague answers are what raise doubts about your intention to leave at the end of your studies**
- how your studies are financed

### G) Everything else

- **CV** in Europass format
- **Proof of residence** (a recent registration document)
- **Copies of previous passport pages** showing your travel history

---

## 4. The appointment day

**Arrive an hour early.** Inside the building the sequence is:

1. **Security** — passport and printed appointment confirmation
2. **Interview with a visa officer** (10–30 minutes)
3. **Biometrics** — fingerprints and photo
4. **Payment of the €75 fee**, in local currency, cash or card depending on the mission
5. **Submission of documents** — originals plus two sets of copies
6. **Your passport stays at the mission** for the duration of the decision

### Questions you should expect

- Why this university and this city?
- How is your study financed, and by whom?
- Who do you know in Germany?
- What do you plan to do after graduation?
- Where have you travelled before?

> ⚠️ **The decisive question is the last-but-one.** An answer that sounds like "I want to stay in Germany" invites a refusal on the ground of doubtful return intent. Answer concretely instead: the degree you are taking, the field you will work in afterwards, and why that skill set matters where you intend to build your career.

---

## 5. How long the decision takes

After the interview the file goes to the relevant authorities in Germany. Realistic ranges:

| Scenario | Duration |
|---|---|
| Complete file, no queries | 6–8 weeks |
| Additional documents requested | 8–12 weeks |
| Peak season (August–October) | 10–14 weeks |
| Complex case, refusal risk | 12+ weeks |

You are notified by e-mail or SMS and collect your passport from the mission, or receive it by courier where that is offered.

### The visa arrived — what now?

The visa is stuck into your passport and you must **enter Germany within three months**. If you do not, it lapses.

---

## 6. What if you are refused?

Refusal rates for student applications sit in the range of **5–15%**. The usual grounds:

| Ground | How to close it |
|---|---|
| Finances insufficient or unclear | Strengthen the blocked account, document where the money came from |
| Doubts about your intention to return | Rewrite the motivation letter around a concrete career plan after graduation |
| Study objective unclear | Explain the link between your background, the programme and your goal |
| Missing documents or contradictions | Complete the file and explain every inconsistency |
| Language ability questionable | Add a certificate or a documented course plan |

### After a refusal: remonstration has been abolished

For years you could file a **Remonstration** — a free second review of the decision. That procedure was **abolished worldwide and for all visa types on 1 July 2025**. It was never a statutory remedy; it was a review the Foreign Office granted voluntarily. Two routes remain:

- **A new application.** There is no waiting period; you may reapply immediately. But an application that does not address the stated ground will most likely fail again. A new appointment and a new fee apply.
- **A claim before the Berlin Administrative Court**, filed within **one month** of the day after notification. The **Verwaltungsgericht Berlin** is the only competent court, regardless of where you applied. It takes months and rarely saves the semester — it is for decisions that are clearly wrong.

**Practical advice:** in most student cases a new application is the right move. Our guide on [what to do after a refusal](/en/blog/what-to-do-after-german-student-visa-refusal-remonstration-appeal-guide-en) covers how to close each ground, how to keep your admission alive, and how litigation actually works.

---

## 7. After you arrive in Germany

The visa covers your first year. During it you convert to an **Aufenthaltstitel** (residence permit).

### The first 14 days

1. **Anmeldung** — register your address at the Bürgeramt with your passport, tenancy agreement and the landlord's confirmation (Wohnungsgeberbestätigung). See our [Anmeldung walkthrough](/en/blog/anmeldung-step-by-step-registering-your-address-in-germany-burgeramt-en).
2. **Open a bank account** — N26, Deutsche Bank or a local Sparkasse. Most of them want your registration certificate first.
3. **Register with a health insurer** — you need their certificate to enrol at the university.

### The first three months

4. **Enrol at the university (Immatrikulation)** — pay the semester contribution and collect your student card.
5. **Apply for your residence permit** at the Ausländerbehörde **before the visa expires.** That date matters more than anything else in this section: applying in time keeps your status valid even if the authority takes months to decide. If yours goes quiet, read [what to do when the Ausländerbehörde does not answer](/en/blog/auslanderbehorde-delay-fiktionsbescheinigung-untatigkeitsklage-en).

The residence permit is normally issued for **two years** and can be extended. It gives you:

- access to €992 per month from your blocked account
- the right to work **140 full days or 280 half days per year**; student assistant (HiWi) jobs at your own university are generally not counted against this allowance
- freedom of movement within the Schengen area

---

## Frequently asked questions

### I have a Schengen visa — can I start studying on it?
No. A Schengen visa is for short stays and does not permit study-length residence. You need a national D visa.

### €11,904 is a lot. Can I show less?
That figure is the federal minimum for 2026 (€992 per month) and missions do not negotiate it. A Verpflichtungserklärung can replace the blocked account, but consulates generally treat a funded Sperrkonto as the safest evidence.

### Where may the money in the blocked account come from?
A family transfer is the most common source and is entirely normal. What matters is that the origin is lawful and traceable. Very large one-off transfers attract compliance questions, so transfer in reasonable instalments and keep the paperwork.

### Once my biometrics are taken, do I have to travel to Germany?
No. If the visa is granted you must enter Germany within three months, otherwise it lapses. Nothing forces you to use it.

### Can my family come with me to the appointment?
No. Only the applicant enters the interview. Family members wait outside.

### Do I have to speak German at the interview?
No — it is conducted in English or Turkish. A few sentences in German leave a good impression if you are learning, but nothing more is expected.

### Is the fee refunded if I am refused?
No. The €75 covers processing whatever the outcome. A new application means paying it again.

### I hold dual citizenship (Turkish and EU). Which passport do I use?
Travel on your EU passport: as an EU citizen you need no visa, you enter directly and apply for your residence documentation in Germany. That route is considerably faster.

### What exactly is the §17 applicant visa?
It is a 3–9 month visa for people **without an admission yet**, allowing you to pursue applications from inside Germany and convert to a student visa once admitted. The financial and insurance requirements are the same.

### Does a refusal stay on my record?
Yes, refusals are recorded and visible to missions in later applications. It is not an automatic bar: each application is assessed on its own. What matters is whether you have resolved the original ground.

### Will working cost me my visa?
You may work **140 full days or 280 half days per year** on a student residence permit. Exceeding that breaches the conditions of your stay. A Werkstudent position of around 20 hours per week during term is the standard arrangement, and HiWi jobs at your university are treated separately.

### Can I travel abroad while the mission holds my passport?
No. While your passport is with the mission you cannot leave the country. If an emergency comes up you can request it back, which pauses the processing of your file.

---

## Your next step

The process is long, but it is predictable if you work backwards from the semester start:

1. **Six to eight months ahead:** secure an admission — browse programmes in our [university directory](/universities).
2. **Three to four months ahead:** open the blocked account, arrange insurance, start the apostille chain.
3. **Two to three months ahead:** book the consulate appointment.
4. **After the visa:** Anmeldung, enrolment, and the residence permit application before the visa expires.

*Figures in this guide are current as of September 2026: the blocked account amount, the €75 national visa fee and processing times are reviewed regularly and can change. Always confirm the document list on the official page of the mission responsible for your residence before you apply.*
MD;

        $enPost = Post::where('slug', 'germany-student-visa-2026-application-steps-documents-rejection-en')->first();
        if ($enPost) {
            $html = Str::markdown($enBody, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
            $enPost->update([
                'title' => 'Germany Student Visa 2026: Application Steps, Documents and Refusals',
                'excerpt' => 'The complete German student visa route from Türkiye: which mission is competent, how appointments work, every required document, what happens on the day, realistic decision times, and what to do after a refusal now that remonstration has been abolished.',
                'content_md' => $enBody,
                'content_html' => $html,
                'meta_title' => 'Germany Student Visa 2026: Steps, Documents, Refusals',
                'meta_description' => 'German student visa from Türkiye: blocked account (€11,904), insurance, documents, appointment waits, €75 fee and what to do if you are refused (2026).',
                'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            ]);
        }

        // TR/DE: eskimiş çalışma günü rakamı (120/240 → 140/280) + yanlış "ders saatlerinde yasak" ifadesi.
        $fixes = [
            'germany-student-visa-2026-application-steps-documents-rejection' => [
                '- 120 tam gün / 240 yarım gün çalışma izni'
                    => '- Yılda 140 tam gün / 280 yarım gün çalışma izni (üniversite içi HiWi işleri bu hesabın dışında)',
                '**120 tam gün / 240 yarım gün** çalışma hakkın var. Aşarsan vize uyumsuzluğu doğar. Ders saatlerinde çalışma yasak. **Werkstudent pozisyonları (haftada 20 saat)** uygundur.'
                    => 'Yılda **140 tam gün / 280 yarım gün** çalışma hakkın var (§ 16b Abs. 3 AufenthG). Bu sınırı aşmak oturum şartlarına aykırıdır. Üniversitenin kendi HiWi işleri bu hesaba kural olarak dahil edilmez. Dönem içinde **haftada 20 saatlik Werkstudent** düzeni standart uygulamadır.',
            ],
            'germany-student-visa-2026-application-steps-documents-rejection-de' => [
                '- Arbeitserlaubnis für 120 volle Tage / 240 halbe Tage'
                    => '- Arbeitserlaubnis für 140 volle Tage / 280 halbe Tage pro Jahr (HiWi-Stellen an der eigenen Hochschule zählen in der Regel nicht mit)',
                'Du hast das Recht, **120 volle Tage / 240 halbe Tage** zu arbeiten. Wenn du dies überschreitest, entsteht eine Visumsinkompatibilität. Arbeiten während der Vorlesungszeiten ist verboten. **Werkstudentenpositionen (20 Stunden pro Woche)** sind geeignet.'
                    => 'Du darfst **140 volle Tage oder 280 halbe Tage pro Jahr** arbeiten (§ 16b Abs. 3 AufenthG). Wer das überschreitet, verstößt gegen die Bedingungen des Aufenthalts. HiWi-Stellen an der eigenen Hochschule zählen in der Regel nicht mit. Während der Vorlesungszeit ist eine **Werkstudentenstelle mit rund 20 Stunden pro Woche** der Standardfall.',
            ],
        ];

        foreach ($fixes as $slug => $pairs) {
            $post = Post::where('slug', $slug)->first();
            if (! $post) {
                continue;
            }
            $md = str_replace(array_keys($pairs), array_values($pairs), $post->content_md);
            if ($md === $post->content_md) {
                continue;
            }
            $html = Str::markdown($md, ['html_input' => 'allow', 'allow_unsafe_links' => false]);
            $post->update([
                'content_md' => $md,
                'content_html' => $html,
                'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            ]);
        }
    }

    public function down(): void
    {
        // Geri alma yok: eski EN gövdesi 192 kelimelik bir taslaktı, TR/DE'deki rakam ise yanlıştı.
    }
};
