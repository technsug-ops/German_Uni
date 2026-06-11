<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Brief → blog: "BAföG nedir? Uluslararası öğrenciler" tr/en/de (3 yazı).
 * Hassas/yasal konu — uygunluk kategorileri dikkatle, "BAföG-Amt'a danış" notu.
 * content_html boş → blog:render-html doldurur. Kategori 8, idempotent.
 */
return new class extends Migration
{
    private string $group = 'a1b2c3d4-0004-4000-8000-000000000004';

    private array $slugs = [
        'bafog-nedir-uluslararasi-ogrenciler-sartlar-basvuru',
        'what-is-bafog-eligibility-application-international-students',
        'was-ist-bafog-voraussetzungen-antrag-internationale-studierende',
    ];

    public function up(): void
    {
        $now = now();
        $catId = DB::table('categories')->where('id', 8)->exists() ? 8 : null;

        $tr = <<<'MD'
# BAföG Nedir? Uluslararası Öğrenciler İçin Şartlar ve Başvuru

> **30 saniyelik özet:** BAföG, Almanya'nın devlet öğrenci desteğidir — yarısı hibe, yarısı faizsiz kredi. Klasik öğrenci vizesiyle gelen çoğu uluslararası öğrenci **BAföG alamaz**, ama bazı gruplar (AB vatandaşları, daimi oturumlular, tanınmış mülteciler, Almanya'da çalışmış olanlar veya ebeveyni çalışanlar vb.) **hak kazanabilir.** "Bana kapalı" diye varsaymadan, kategorine bak.

---

## BAföG tam olarak nedir?

BAföG (Bundesausbildungsförderungsgesetz), ihtiyaç temelli devlet öğrenci desteğidir:

- Tutar 2024+ itibarıyla aylık **~934 €'ya kadar** (gelir/duruma göre değişir).
- **Yarısı hibe, yarısı faizsiz kredi** — geri ödenen kısım sınırlı ve faizsizdir.
- Yaş, gelir (senin + ebeveynin) ve ilerleme şartlarına bağlıdır.

## Kimler hak kazanabilir? (uluslararası açıdan kritik kısım)

Klasik bir öğrenci vizesiyle gelen çoğu uluslararası öğrenci BAföG **alamaz**. Ancak şu gruplar genelde değerlendirilir:

- **AB/EEA vatandaşları** (belirli çalışma/ikamet koşullarıyla),
- **Daimi oturum izni** (Niederlassungserlaubnis) sahipleri,
- **Tanınmış mülteciler** ve belirli insani oturum izinleri,
- Almanya'da **çalışmış** olanlar veya **ebeveyni Almanya'da çalışmış/çalışan** olanlar (belirli sürelerle),
- Bir Alman vatandaşıyla **evli** olanlar — duruma göre.

> Kategoriler ve süreler ayrıntılıdır; uygunluk her zaman **BAföG-Amt** tarafından bireysel değerlendirilir.

## Hak kazanamıyorsan alternatifler

BAföG kapalıysa, finansman için:

- **Burslar** (DAAD, siyasi/dini vakıflar, üniversite içi burslar) — akademik veya ihtiyaç temelli,
- **Werkstudent** işi (haftada ~20 saate kadar, ders dönemi),
- **Studienkredit** (KfW vb. öğrenci kredileri),
- **Stipendium** programları (örn. Deutschlandstipendium).

## Nasıl başvurulur?

1. **BAföG-Amt / Studierendenwerk** üzerinden başvuru (online: BAföG Digital).
2. Kimlik/oturum belgesi, kayıt belgesi (Immatrikulation), gelir kanıtları.
3. Erken başvur — geriye dönük ödeme genelde başvuru ayından itibaridir.

> Önemli: Uygunluk durumun çok kişiseldir. Bu yazı genel bir haritadır; nihai kararı **BAföG-Amt** verir. Burs ve Werkstudent rehberlerimiz, BAföG dışındaki yolları kapatır.
MD;

        $en = <<<'MD'
# What Is BAföG? Eligibility and Application for International Students

> **30-second summary:** BAföG is Germany's state student aid — half grant, half interest-free loan. Most international students on a standard student visa **cannot receive BAföG**, but certain groups (EU citizens, permanent residents, recognised refugees, those who have worked in Germany or whose parents have, etc.) **may qualify.** Don't assume it's closed to you — check your category.

---

## What exactly is BAföG?

BAföG (Bundesausbildungsförderungsgesetz) is need-based state student support:

- Up to about **€934/month** as of 2024+ (varies by income/situation).
- **Half grant, half interest-free loan** — the repaid part is capped and interest-free.
- Depends on age, income (yours + your parents') and study progress.

## Who can qualify? (the part that matters for internationals)

Most international students on a standard student visa **cannot** get BAföG. However, these groups are usually considered:

- **EU/EEA citizens** (under certain work/residence conditions),
- Holders of a **permanent residence permit** (Niederlassungserlaubnis),
- **Recognised refugees** and certain humanitarian residence permits,
- Those who have **worked in Germany**, or whose **parents have worked/work in Germany** (for certain periods),
- People **married to a German citizen** — depending on circumstances.

> The categories and time conditions are detailed; eligibility is always assessed individually by the **BAföG-Amt**.

## Alternatives if you don't qualify

If BAföG is closed to you, consider:

- **Scholarships** (DAAD, political/religious foundations, university scholarships) — academic or need-based,
- A **Werkstudent** job (up to ~20 hours/week during term),
- A **student loan** (e.g. KfW),
- **Stipendium** programmes (e.g. Deutschlandstipendium).

## How to apply

1. Apply via the **BAföG-Amt / Studierendenwerk** (online: BAföG Digital).
2. ID/residence document, enrolment certificate (Immatrikulation), proof of income.
3. Apply early — back-payment generally starts from the application month.

> Important: Eligibility is highly personal. This article is a general map; the final decision is made by the **BAföG-Amt**. Our scholarship and Werkstudent guides cover the routes beyond BAföG.
MD;

        $de = <<<'MD'
# Was ist BAföG? Voraussetzungen und Antrag für internationale Studierende

> **Zusammenfassung in 30 Sekunden:** BAföG ist die staatliche Ausbildungsförderung in Deutschland — zur Hälfte Zuschuss, zur Hälfte zinsloses Darlehen. Die meisten internationalen Studierenden mit Standard-Studentenvisum **erhalten kein BAföG**, doch bestimmte Gruppen (EU-Bürger, Personen mit Niederlassungserlaubnis, anerkannte Geflüchtete, Personen, die in Deutschland gearbeitet haben oder deren Eltern usw.) **können förderfähig sein.** Geh nicht davon aus, dass es für dich verschlossen ist — prüfe deine Kategorie.

---

## Was ist BAföG genau?

BAföG (Bundesausbildungsförderungsgesetz) ist eine bedarfsabhängige staatliche Studienförderung:

- Bis zu rund **934 €/Monat** seit 2024+ (je nach Einkommen/Situation).
- **Halb Zuschuss, halb zinsloses Darlehen** — der Rückzahlungsteil ist gedeckelt und zinsfrei.
- Abhängig von Alter, Einkommen (deinem + dem deiner Eltern) und Studienfortschritt.

## Wer kann förderfähig sein? (der wichtige Teil für Internationale)

Die meisten internationalen Studierenden mit Standard-Studentenvisum erhalten **kein** BAföG. Diese Gruppen werden jedoch in der Regel geprüft:

- **EU-/EWR-Bürger** (unter bestimmten Arbeits-/Aufenthaltsbedingungen),
- Inhaber einer **Niederlassungserlaubnis**,
- **Anerkannte Geflüchtete** und bestimmte humanitäre Aufenthaltstitel,
- Personen, die **in Deutschland gearbeitet** haben oder deren **Eltern in Deutschland gearbeitet haben/arbeiten** (für bestimmte Zeiträume),
- Personen, die **mit einem deutschen Staatsbürger verheiratet** sind — je nach Umständen.

> Die Kategorien und Fristen sind detailliert; die Förderfähigkeit wird stets individuell vom **BAföG-Amt** geprüft.

## Alternativen, wenn du nicht förderfähig bist

Wenn BAföG für dich verschlossen ist, kommen infrage:

- **Stipendien** (DAAD, politische/religiöse Stiftungen, Hochschulstipendien) — leistungs- oder bedarfsbasiert,
- ein **Werkstudentenjob** (bis zu ~20 Stunden/Woche während der Vorlesungszeit),
- ein **Studienkredit** (z. B. KfW),
- **Stipendienprogramme** (z. B. Deutschlandstipendium).

## Wie beantragst du es?

1. Antrag beim **BAföG-Amt / Studierendenwerk** (online: BAföG Digital).
2. Ausweis-/Aufenthaltsdokument, Immatrikulationsbescheinigung, Einkommensnachweise.
3. Früh beantragen — die Nachzahlung beginnt in der Regel ab dem Antragsmonat.

> Wichtig: Die Förderfähigkeit ist sehr individuell. Dieser Artikel ist eine allgemeine Orientierung; die endgültige Entscheidung trifft das **BAföG-Amt**. Unsere Stipendien- und Werkstudenten-Leitfäden decken die Wege jenseits von BAföG ab.
MD;

        $rows = [
            ['locale' => 'tr', 'slug' => $this->slugs[0],
                'title' => 'BAföG Nedir? Uluslararası Öğrenciler İçin Şartlar ve Başvuru',
                'excerpt' => 'BAföG nedir, kimler hak kazanır, uluslararası öğrenciler hangi durumlarda alabilir ve alamayanlar için burs/Werkstudent gibi alternatifler — net rehber.',
                'content_md' => $tr, 'reading_minutes' => 5],
            ['locale' => 'en', 'slug' => $this->slugs[1],
                'title' => 'What Is BAföG? Eligibility and Application for International Students',
                'excerpt' => 'What BAföG is, who qualifies, when international students can receive it, and alternatives like scholarships and Werkstudent jobs for those who cannot — a clear guide.',
                'content_md' => $en, 'reading_minutes' => 5],
            ['locale' => 'de', 'slug' => $this->slugs[2],
                'title' => 'Was ist BAföG? Voraussetzungen und Antrag für internationale Studierende',
                'excerpt' => 'Was BAföG ist, wer förderfähig ist, wann internationale Studierende es erhalten können und Alternativen wie Stipendien und Werkstudentenjobs — ein klarer Leitfaden.',
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
                'category_id' => $catId,
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
