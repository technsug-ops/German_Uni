<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Brief → blog: "Almanca öğrenme yol haritası" tr/en/de (3 yazı, tek
 * translation_group_id). content_html boş bırakılır; blog:render-html (lokal +
 * post-deploy) markdown'dan üretir. Idempotent (slug bazlı). Kategori 8 = rehber.
 */
return new class extends Migration
{
    private string $group = 'a1b2c3d4-0001-4000-8000-000000000001';

    private array $slugs = [
        'sifirdan-c1e-almanca-ogrenme-yol-haritasi',
        'learning-german-from-zero-to-c1-roadmap',
        'deutsch-von-null-auf-c1-lernen-fahrplan',
    ];

    public function up(): void
    {
        $now = now();
        // Taze/CI DB'sinde categories tablosu boş olabilir → FK güvenliği
        $catId = DB::table('categories')->where('id', 8)->exists() ? 8 : null;

        $tr = <<<'MD'
# Sıfırdan C1'e Almanca Öğrenme Yol Haritası (TestDaF/DSH)

> **30 saniyelik özet:** Almanca bir programa sıfırdan C1'e ulaşmak ortalama **9–18 ay** sürer. Almanca bölümler genelde **C1 (TestDaF 4×4 veya DSH-2)** ister; İngilizce bölümlerde Almanca şart değildir ama günlük hayat için **B1** çok işine yarar. Önce programının dilini netleştir, sonra seviye ve sınavı seç.

---

## Hangi seviyeye ihtiyacın var?

| Durumun | Gereken Almanca |
|---|---|
| Almanca lisans / yüksek lisans | **C1** (TestDaF 4×4 veya DSH-2) |
| İngilizce program | Çoğu zaman **şart değil** — ama B1 günlük hayat + iş için altın değerinde |
| Studienkolleg | Genelde girişte **B1–B2** |
| Ausbildung | Çoğu meslekte **B1–B2** |

## Gerçekçi zaman çizelgesi

Sıfırdan başlıyorsan, haftada ~10–15 saat düzenli çalışmayla:

- **A1–A2:** 3–5 ay (temel günlük iletişim)
- **B1:** +3–4 ay (Studienkolleg/günlük hayat eşiği)
- **B2:** +3–4 ay
- **C1:** +3–5 ay (üniversite eşiği)

Yoğun kurslarla (haftada 20+ saat) bu süre yarıya inebilir. Önemli olan **süreklilik** — günde 30 dakika, haftada bir gün 5 saatten daha etkilidir.

## TestDaF mı, DSH mı?

İkisi de Almanca bölümler için kabul edilir; farkı tanımak seçimini kolaylaştırır.

| | TestDaF | DSH |
|---|---|---|
| Nerede | Dünya çapında merkezlerde + online (digital) | Genelde **kabul aldığın üniversitede** |
| Puanlama | 4 beceri, her biri TDN 3/4/5 | DSH-1 / DSH-2 / DSH-3 |
| Üni eşiği | Genelde **4×4** (her beceride TDN 4) | Genelde **DSH-2** |
| Ne zaman | Kabulden **önce** alıp başvuruya ekleyebilirsin | Çoğunlukla kabulden **sonra**, kayıt öncesi |

**Pratik öneri:** Başvuru esnekliği için TestDaF'ı tercih et — sonucu elinde olur, birden çok üniversiteye eklersin. DSH'yi hedef üniversiten özellikle istiyorsa veya oradaysan değerlendir.

## Sıfırdan C1'e pratik plan

1. **Sağlam temel (A1–A2):** Bir kursa (Goethe, telc hazırlık, üniversite dil merkezi) yaz. Dilbilgisi iskeletini burada otur.
2. **Girdi bombardımanı (B1'den itibaren):** Almanca podcast, dizi (altyazılı→altyazısız), haber (Nachrichtenleicht). Günlük "pasif" maruziyet kelime hazneni patlatır.
3. **Aktif üretim (B2–C1):** Tandem partneri / konuşma grubu, haftalık yazma alıştırması (düzeltmeli). C1'de yazma ve konuşma sınavın belirleyicisi.
4. **Sınava özel hazırlık (son 2–3 ay):** TestDaF/DSH formatına özel deneme sınavları. Format tanımak, dil seviyesinden ayrı bir beceridir.

## İşe yarayan ücretsiz kaynaklar

- **Deutsche Welle (DW) — "Nicos Weg":** A1–B1 tam ücretsiz interaktif kurs.
- **Goethe Institut alıştırmaları:** Seviye seviye ücretsiz çalışma setleri.
- **Anki:** Aralıklı tekrar ile kelime ezberi (en verimli yöntem).
- **Nachrichtenleicht / DW Langsam gesprochene Nachrichten:** Yavaş, sade Almanca haber.
- **Tandem / HelloTalk:** Ana dili Almanca olan partnerle karşılıklı pratik.

## Sık yapılan hata

En büyük hata **sadece dilbilgisi çalışıp konuşmayı ertelemek**. Almancayı "biriktirip sonra kullanırım" diye bekletme; A2'den itibaren her gün birkaç cümle de olsa üret. Sınav günü panik yaşamamanın tek yolu, dili aylar boyunca **kullanmış** olmaktır.

> İlgili: İngilizce programlara Almanca şartı yoktur — ama günlük hayat, yurt ve iş için en az B1 hayatını kolaylaştırır.
MD;

        $en = <<<'MD'
# Learning German from Zero to C1: A Roadmap (TestDaF/DSH)

> **30-second summary:** Reaching C1 from scratch for a German-taught programme takes **9–18 months** on average. German-taught degrees usually require **C1 (TestDaF 4×4 or DSH-2)**; English-taught programmes don't require German, but **B1** makes daily life far easier. First confirm your programme's language, then pick the level and exam.

---

## What level do you need?

| Your situation | German required |
|---|---|
| German-taught Bachelor / Master | **C1** (TestDaF 4×4 or DSH-2) |
| English-taught programme | Usually **not required** — but B1 is gold for daily life + work |
| Studienkolleg | Typically **B1–B2** at entry |
| Ausbildung | **B1–B2** for most professions |

## A realistic timeline

Starting from zero, with ~10–15 focused hours per week:

- **A1–A2:** 3–5 months (basic everyday communication)
- **B1:** +3–4 months (Studienkolleg / daily-life threshold)
- **B2:** +3–4 months
- **C1:** +3–5 months (university threshold)

Intensive courses (20+ hours/week) can roughly halve this. The decisive factor is **consistency** — 30 minutes a day beats one 5-hour session per week.

## TestDaF or DSH?

Both are accepted for German-taught degrees; knowing the difference makes the choice easy.

| | TestDaF | DSH |
|---|---|---|
| Where | Test centres worldwide + online (digital) | Usually **at the university that admitted you** |
| Scoring | 4 skills, each TDN 3/4/5 | DSH-1 / DSH-2 / DSH-3 |
| University bar | Usually **4×4** (TDN 4 in each skill) | Usually **DSH-2** |
| When | Can be taken **before** admission and added to your application | Usually **after** admission, before enrolment |

**Practical tip:** For application flexibility, prefer TestDaF — you hold the result and can attach it to several universities. Consider DSH if your target university specifically requires it or you are already there.

## A practical plan from zero to C1

1. **Solid base (A1–A2):** Enrol in a course (Goethe, a telc prep course, a university language centre). Build the grammar skeleton here.
2. **Input flood (from B1):** German podcasts, series (subtitles → none), slow news (Nachrichtenleicht). Daily passive exposure explodes your vocabulary.
3. **Active output (B2–C1):** A tandem partner / speaking group and a weekly corrected writing task. At C1, writing and speaking decide your exam.
4. **Exam-specific prep (final 2–3 months):** Mock tests in the exact TestDaF/DSH format. Knowing the format is a skill separate from your language level.

## Free resources that actually work

- **Deutsche Welle (DW) — "Nicos Weg":** A fully free interactive A1–B1 course.
- **Goethe-Institut exercises:** Free level-by-level practice sets.
- **Anki:** Spaced-repetition vocabulary (the most efficient method).
- **Nachrichtenleicht / DW slow news:** Slow, simplified German news.
- **Tandem / HelloTalk:** Two-way practice with native German speakers.

## The most common mistake

The biggest mistake is **only studying grammar and postponing speaking**. Don't "save up" German for later; from A2 on, produce a few sentences every day. The only way to avoid panic on exam day is to have actually **used** the language for months.

> Related: English-taught programmes don't require German — but at least B1 makes housing, daily life and work much easier.
MD;

        $de = <<<'MD'
# Deutsch von null auf C1 lernen: Ein Fahrplan (TestDaF/DSH)

> **Zusammenfassung in 30 Sekunden:** Von null auf C1 für ein deutschsprachiges Studium dauert im Schnitt **9–18 Monate**. Deutschsprachige Studiengänge verlangen meist **C1 (TestDaF 4×4 oder DSH-2)**; englischsprachige Programme brauchen kein Deutsch, doch **B1** erleichtert den Alltag enorm. Kläre zuerst die Sprache deines Studiengangs, dann wähle Niveau und Prüfung.

---

## Welches Niveau brauchst du?

| Deine Situation | Erforderliches Deutsch |
|---|---|
| Deutschsprachiger Bachelor / Master | **C1** (TestDaF 4×4 oder DSH-2) |
| Englischsprachiges Programm | Meist **nicht erforderlich** — aber B1 ist Gold für Alltag + Job |
| Studienkolleg | In der Regel **B1–B2** beim Einstieg |
| Ausbildung | **B1–B2** für die meisten Berufe |

## Ein realistischer Zeitplan

Von null, mit ~10–15 fokussierten Stunden pro Woche:

- **A1–A2:** 3–5 Monate (grundlegende Alltagskommunikation)
- **B1:** +3–4 Monate (Studienkolleg- / Alltagsschwelle)
- **B2:** +3–4 Monate
- **C1:** +3–5 Monate (Universitätsschwelle)

Intensivkurse (20+ Stunden/Woche) können das etwa halbieren. Entscheidend ist **Beständigkeit** — 30 Minuten täglich schlagen eine einzelne 5-Stunden-Einheit pro Woche.

## TestDaF oder DSH?

Beide werden für deutschsprachige Studiengänge akzeptiert; den Unterschied zu kennen erleichtert die Wahl.

| | TestDaF | DSH |
|---|---|---|
| Wo | Testzentren weltweit + online (digital) | Meist **an der Hochschule, die dich zugelassen hat** |
| Bewertung | 4 Fertigkeiten, je TDN 3/4/5 | DSH-1 / DSH-2 / DSH-3 |
| Uni-Grenze | Meist **4×4** (TDN 4 in jeder Fertigkeit) | Meist **DSH-2** |
| Wann | Kann **vor** der Zulassung abgelegt und der Bewerbung beigefügt werden | Meist **nach** der Zulassung, vor der Einschreibung |

**Praxistipp:** Für Bewerbungsflexibilität bevorzuge TestDaF — du hast das Ergebnis in der Hand und kannst es mehreren Hochschulen beilegen. DSH, wenn deine Zielhochschule sie ausdrücklich verlangt oder du bereits vor Ort bist.

## Ein praktischer Plan von null auf C1

1. **Solide Basis (A1–A2):** Belege einen Kurs (Goethe, ein telc-Vorbereitungskurs, ein Sprachenzentrum). Bau hier das Grammatikgerüst.
2. **Input-Flut (ab B1):** Deutsche Podcasts, Serien (mit → ohne Untertitel), langsame Nachrichten (Nachrichtenleicht). Tägliche passive Exposition lässt deinen Wortschatz explodieren.
3. **Aktive Produktion (B2–C1):** Tandempartner / Sprechgruppe und eine wöchentliche korrigierte Schreibaufgabe. Auf C1 entscheiden Schreiben und Sprechen deine Prüfung.
4. **Prüfungsspezifische Vorbereitung (letzte 2–3 Monate):** Übungstests im exakten TestDaF/DSH-Format. Das Format zu kennen ist eine Fähigkeit, die von deinem Sprachniveau getrennt ist.

## Kostenlose Ressourcen, die wirklich helfen

- **Deutsche Welle (DW) — „Nicos Weg":** Ein komplett kostenloser interaktiver A1–B1-Kurs.
- **Goethe-Institut-Übungen:** Kostenlose Übungssets Niveau für Niveau.
- **Anki:** Vokabeln mit verteilter Wiederholung (die effizienteste Methode).
- **Nachrichtenleicht / DW langsam gesprochene Nachrichten:** Langsame, vereinfachte deutsche Nachrichten.
- **Tandem / HelloTalk:** Wechselseitiges Üben mit deutschen Muttersprachlern.

## Der häufigste Fehler

Der größte Fehler ist, **nur Grammatik zu lernen und das Sprechen aufzuschieben**. Spar dir Deutsch nicht für später auf; produziere ab A2 jeden Tag ein paar Sätze. Der einzige Weg, am Prüfungstag keine Panik zu bekommen, ist, die Sprache monatelang tatsächlich **benutzt** zu haben.

> Verwandt: Englischsprachige Programme verlangen kein Deutsch — aber mindestens B1 erleichtert Wohnen, Alltag und Job erheblich.
MD;

        $rows = [
            ['locale' => 'tr', 'slug' => $this->slugs[0],
                'title' => "Sıfırdan C1'e Almanca Öğrenme Yol Haritası (TestDaF/DSH)",
                'excerpt' => 'Almanca bir programa sıfırdan C1: gerçekçi zaman çizelgesi, TestDaF–DSH karşılaştırması, seviye seviye plan ve işe yarayan ücretsiz kaynaklar.',
                'content_md' => $tr, 'reading_minutes' => 6],
            ['locale' => 'en', 'slug' => $this->slugs[1],
                'title' => 'Learning German from Zero to C1: A Roadmap (TestDaF/DSH)',
                'excerpt' => 'From zero to C1 for a German-taught programme: a realistic timeline, TestDaF vs DSH, a level-by-level plan, and free resources that actually work.',
                'content_md' => $en, 'reading_minutes' => 6],
            ['locale' => 'de', 'slug' => $this->slugs[2],
                'title' => 'Deutsch von null auf C1 lernen: Ein Fahrplan (TestDaF/DSH)',
                'excerpt' => 'Von null auf C1 für ein deutschsprachiges Studium: realistischer Zeitplan, TestDaF vs. DSH, ein Plan Niveau für Niveau und kostenlose Ressourcen.',
                'content_md' => $de, 'reading_minutes' => 6],
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
