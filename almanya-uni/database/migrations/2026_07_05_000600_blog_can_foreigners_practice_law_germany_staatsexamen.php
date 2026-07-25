<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Yabancı Almanya'da avukatlık yapabilir mi? Staatsexamen & tanınma (2026).
 *
 * Doğrulandı (BRAK/Rechtsanwaltskammer, EuRAG/§ 206-207 BRAO, Juristenausbildung, 2026):
 *  - Rechtsanwalt (Volljurist) = İKİ Staatsexamen (~4-5 yıl Jura → 1. + ~2 yıl Referendariat → 2.),
 *    tamamen Almanca, Alman hukukuna özel. LL.M. avukat YAPMAZ.
 *  - Yabancı hukuk diploması tanınması çok sınırlı; AB avukatları Eignungsprüfung/EuRAG üzerinden,
 *    AB-DIŞI (ör. Türk) hukukçu doğrudan baroya kaydolamaz. Kendi ülke hukuku için "yabancı hukuk
 *    danışmanı" (§ 206 BRAO) sınırlı bir istisna.
 *
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 * İç-link: 3 küme kardeşi + mevcut study-law + work-visa (locale-doğru, /blog/ formatı).
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'd7e20000-2222-4a8c-9fb0-dd05ee0aaa02';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'law-policy')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
"Almanya'da LL.M. yaparım, sonra orada avukatlık açarım." — Türk hukuk öğrencilerinin en yaygın ve maalesef **en yanlış** planı bu. Kısa ve dürüst cevap: **Bir yabancı, teoride Almanya'da avukat (Rechtsanwalt) olabilir — ama bu, çoğu insanın sandığından ışık yılı uzakta bir yoldur.** Bu yazı hayal kırıklığını baştan yaşatıp seni gerçekçi bir plana oturtmak için.

## 1. En büyük yanılgı: LL.M. seni avukat YAPMAZ
Bunu kalın harflerle söyleyelim: **LL.M. (Master of Laws) Almanya'da avukatlık ruhsatı vermez.** LL.M. bir uzmanlık/akademik derecedir — uluslararası hukuk, ticaret, IP, vergi gibi alanlarda derinleşmek, uluslararası firmalarda veya compliance/danışmanlıkta çalışmak içindir. Ne kadar prestijli olursa olsun (Bucerius, LMU, Heidelberg fark etmez), LL.M. seni **Rechtsanwalt yapmaz.**

Almanya'da "avukat" (Rechtsanwalt) olmanın **tek** kapısı **Volljurist** olmaktır ve bu tamamen ayrı, çok daha uzun bir yoldur. LL.M. ile avukat olma planı, en baştan yanlış haritayla yola çıkmaktır. (İngilizce LL.M. yolunu ayrıca anlattık: [Almancasız İngilizce LL.M. rehberi](/tr/blog/english-taught-law-llm-masters-in-germany-without-german).)

## 2. Gerçek yol: iki Staatsexamen + Referendariat (tamamen Almanca)
Alman avukatı olmak = **Volljurist** olmak ve bu üç aşamalı, acımasız bir süreçtir:

| Aşama | Ne | Süre | Dil |
|---|---|---|---|
| Hukuk öğrenimi | Jura (Alman hukuku) | ~4–5 yıl | **Almanca** |
| **1. Staatsexamen** | İlk devlet sınavı | — | **Almanca** |
| **Referendariat** | Zorunlu staj (mahkeme/savcılık/idare/kanzlei) | ~2 yıl (maaşlı) | **Almanca** |
| **2. Staatsexamen** | İkinci devlet sınavı → Volljurist | — | **Almanca** |

Toplam kabaca **7 yıl** ve **hiçbir aşaması İngilizce değildir.** Sınavlar Alman hukukuna özeldir, yazım üslubu (Gutachtenstil) anadili Almanlar için bile zorlayıcıdır. Yani "avukat olacağım" demek, pratikte **Alman hukukunu sıfırdan, Almanca, yerlilerle aynı sınavda yarışarak** okumak demektir. Bu yolu yapan yabancı sayısı çok azdır — imkânsız değil, ama gerçek maliyeti budur.

## 3. Yabancı hukuk diplomasının tanınması: çok sınırlı
"Ben zaten kendi ülkemde hukuk okudum, denklik alıp avukat olurum" — burada da beklenti gerçeğe çarpıyor:

- **AB üyesi ülke hukukçuları:** AB kuralları (EuRAG) ve **Eignungsprüfung** (uyum sınavı) üzerinden Almanya'da avukatlığa kaydolabilir. Bu görece açık bir yoldur.
- **AB-dışı ülke hukukçuları (ör. Türk):** **Doğrudan Alman barosuna kaydolamaz.** Türkiye'deki hukuk diploman, Almanya'da Rechtsanwalt olmak için doğrudan tanınmaz. Staatsexamen yoluna tam denklik neredeyse hiç verilmez; pratikte yeniden (ya da büyük ölçüde) Alman hukuku okuman beklenir.

Yani AB-dışı bir hukukçu için "denklikle avukatlık" **gerçekçi bir plan değildir.** (Tanınma kuralları eyalete ve dosyaya göre değişir — kesin bilgi için ilgili eyaletin adalet makamına danış.)

## 4. Peki Türk hukukçu için gerçek seçenekler ne?
İyi haber: avukatlık ruhsatı olmadan da hukuk alanında **anlamlı kariyerler** var:

- **Yabancı hukuk danışmanı (§ 206 BRAO):** Belirli koşullarda, **kendi ülkenin hukuku** (ör. Türk hukuku) konusunda Almanya'da danışman olarak kaydolabilirsin. Bu, "Alman avukatı" olmak değildir — Alman hukukunda dava/temsil yapamazsın — ama Türk-Alman ticaret, aile, miras işlerinde gerçek bir niştir.
- **Uluslararası firmalarda LL.M. + İngilizce:** Cross-border işlerde, uluslararası tahkimde, uyum (compliance) ve sözleşme yönetiminde yabancı hukukçular değerlidir.
- **Şirket içi / uluslararası kuruluşlar / LegalTech / akademi:** Ruhsat gerektirmeyen roller. (Detaylı harita: [avukat olmadan hukukta kariyer](/tr/blog/working-in-law-in-germany-careers-beyond-becoming-a-lawyer) ve [yabancı hukuk diplomasıyla iş piyasası](/tr/blog/what-to-do-with-a-foreign-law-degree-in-germany-job-market).)

Kısacası: hedefi "Rechtsanwalt" yerine "hukuk alanında uluslararası kariyer" olarak kurarsan kapılar çok daha açık.

## 5. Almanca gerçeği: C1 taban, tavan değil
Alman hukuk pratiğinin her santimi Almancadır. Staatsexamen yolu için pratikte **anadili-yakını Almanca** (en az C1, gerçekte üstü) gerekir; yasaların dili, mahkeme dili, müvekkil dili hep Almanca. İngilizce sadece uluslararası/LL.M. tarafında iş görür. Almanya iş piyasasına kalıcı girmek istiyorsan — avukatlık olsun olmasın — **Almanca en büyük kaldıracın.**

## 6. Süreç: adımları doğru makamdan teyit et
Genel çerçeve şudur ama **kişisel durumun için mutlaka resmî kaynaktan doğrula:**

1. Hedefini netleştir: **Rechtsanwalt mı, yoksa uzman/uluslararası rol mü?**
2. Rechtsanwalt hedefliyorsan: ilgili eyaletin **Justizprüfungsamt**'ından Staatsexamen yolunu ve (varsa) denklik/kısmi muafiyet şartlarını sor.
3. AB-dışı diploman için: **Rechtsanwaltskammer** (baro) ve eyalet adalet bakanlığından kayıt/§ 206 BRAO danışmanlık seçeneklerini teyit et.
4. Uluslararası kariyer hedefliyorsan: LL.M. + Almanca + staj + network'e yatırım yap; gerekirse [iş teklifiyle çalışma vizesi süreci](/tr/blog/germany-work-visa-with-job-offer-process-timeline-fast-track)'ni incele.

Bu yazı garanti veremez — kurallar eyalete ve dosyaya göre değişir; her adımı **Justizprüfungsamt / Rechtsanwaltskammer**'den yazılı teyit et.

## Sonuç & dürüst tavsiye
Dürüst olalım: **AB-dışı bir yabancının Almanya'da klasik anlamda "avukat açması" gerçekçi değildir.** Rechtsanwalt olmak iki Staatsexamen + Referendariat + anadili-yakını Almanca demektir; LL.M. bunu vermez; Türk diploması doğrudan tanınmaz. Ama bu bir çıkmaz değil, sadece **doğru hedefi seçme** meselesi: yabancı hukuk danışmanlığı, uluslararası firmalar, compliance, LegalTech ve akademi hukukçular için gerçek ve değerli yollardır. Önce "avukat olmak" saplantısını bırak; sonra sana en çok kapıyı açacak uzmanlığı seç.

Başlamadan önce mutlaka oku: [Almanya'da yabancı olarak hukuk okumak](/tr/blog/studying-law-in-germany-as-a-foreigner-staatsexamen-vs-llb-llm).

---
*2026 itibarıyla geçerli genel çerçeve temel alınmıştır; tanınma, baro kaydı ve Staatsexamen kuralları eyalete ve bireysel dosyaya göre değişir — karar vermeden önce ilgili eyaletin Justizprüfungsamt'ı ve Rechtsanwaltskammer'inden teyit al.*
MD;

        $deBody = <<<'MD'
„Ich mache einen LL.M. und eröffne dann in Deutschland eine Kanzlei." — das ist der häufigste und leider **falscheste** Plan internationaler Jurastudierender. Kurz und ehrlich: **Ein Ausländer kann theoretisch in Deutschland Anwalt (Rechtsanwalt) werden — aber dieser Weg ist Lichtjahre von dem entfernt, was die meisten annehmen.** Dieser Beitrag nimmt die Enttäuschung vorweg und bringt dich zu einem realistischen Plan.

## 1. Der größte Irrtum: ein LL.M. macht dich NICHT zum Anwalt
Sagen wir es in Fettschrift: **Ein LL.M. (Master of Laws) verleiht in Deutschland keine Anwaltszulassung.** Der LL.M. ist ein Spezialisierungs-/Akademiegrad — für internationales Recht, Wirtschaftsrecht, IP, Steuerrecht, für Arbeit in internationalen Kanzleien oder in Compliance/Beratung. Egal wie renommiert (ob Bucerius, LMU oder Heidelberg): der LL.M. **macht dich nicht zum Rechtsanwalt.**

Der **einzige** Weg zum „Rechtsanwalt" in Deutschland führt über den **Volljuristen** — ein völlig eigener, viel längerer Weg. Mit einem LL.M. Anwalt werden zu wollen heißt, von Anfang an mit der falschen Karte loszulaufen. (Den englischsprachigen LL.M.-Weg erklären wir separat: [LL.M. ohne Deutsch](/de/blog/english-taught-law-llm-masters-in-germany-without-german-de).)

## 2. Der echte Weg: zwei Staatsexamina + Referendariat (komplett auf Deutsch)
Deutscher Anwalt zu werden = **Volljurist** zu werden, und das ist ein dreistufiger, harter Prozess:

| Stufe | Was | Dauer | Sprache |
|---|---|---|---|
| Jurastudium | Jura (deutsches Recht) | ~4–5 Jahre | **Deutsch** |
| **1. Staatsexamen** | Erste juristische Prüfung | — | **Deutsch** |
| **Referendariat** | Pflichtstationen (Gericht/StA/Verwaltung/Kanzlei) | ~2 Jahre (bezahlt) | **Deutsch** |
| **2. Staatsexamen** | Zweite Prüfung → Volljurist | — | **Deutsch** |

Insgesamt grob **7 Jahre**, und **keine Stufe ist auf Englisch.** Die Prüfungen sind auf deutsches Recht zugeschnitten, der Gutachtenstil fordert selbst Muttersprachler heraus. „Ich werde Anwalt" bedeutet praktisch: **deutsches Recht von Grund auf, auf Deutsch, in denselben Prüfungen wie die Einheimischen.** Nur sehr wenige Ausländer gehen diesen Weg — nicht unmöglich, aber das sind die echten Kosten.

## 3. Anerkennung eines ausländischen Jura-Abschlusses: sehr begrenzt
„Ich habe ja zu Hause Jura studiert, ich lasse es anerkennen und werde Anwalt" — auch hier trifft die Erwartung auf die Realität:

- **Juristen aus EU-Ländern:** Über EU-Regeln (EuRAG) und die **Eignungsprüfung** ist die Zulassung in Deutschland möglich. Ein vergleichsweise offener Weg.
- **Juristen aus Nicht-EU-Ländern (z. B. Türkei):** **Keine direkte Zulassung** zur deutschen Anwaltschaft. Dein türkischer Jura-Abschluss wird für den Beruf des Rechtsanwalts nicht direkt anerkannt. Eine volle Anrechnung auf den Staatsexamensweg gibt es praktisch kaum; erwartet wird meist, dass du deutsches Recht (weitgehend) neu studierst.

Für Nicht-EU-Juristen ist „Anwalt per Anerkennung" also **kein realistischer Plan.** (Anerkennungsregeln variieren nach Bundesland und Einzelfall — kläre Verbindliches mit der zuständigen Justizbehörde.)

## 4. Was sind die echten Optionen, z. B. für türkische Juristen?
Gute Nachricht: Auch ohne Anwaltszulassung gibt es **sinnvolle juristische Karrieren**:

- **Ausländischer Rechtsberater (§ 206 BRAO):** Unter bestimmten Voraussetzungen kannst du dich in Deutschland als Berater für das **Recht deines Heimatstaates** (z. B. türkisches Recht) registrieren lassen. Das ist kein „deutscher Anwalt" — du darfst nicht im deutschen Recht vertreten — aber eine echte Nische im deutsch-türkischen Handels-, Familien- und Erbrecht.
- **Internationale Kanzleien mit LL.M. + Englisch:** Bei grenzüberschreitenden Mandaten, in der internationalen Schiedsgerichtsbarkeit, in Compliance und Vertragsmanagement sind ausländische Juristen wertvoll.
- **Inhouse / internationale Organisationen / LegalTech / Wissenschaft:** Rollen ohne Zulassung. (Detaillierte Karte: [Karriere ohne Anwaltszulassung](/de/blog/working-in-law-in-germany-careers-beyond-becoming-a-lawyer-de) und [Arbeitsmarkt mit ausländischem Jura-Abschluss](/de/blog/what-to-do-with-a-foreign-law-degree-in-germany-job-market-de).)

Kurz: Setzt du dein Ziel auf „internationale juristische Karriere" statt „Rechtsanwalt", öffnen sich weit mehr Türen.

## 5. Die Sprach-Realität: C1 ist der Boden, nicht die Decke
Jeder Zentimeter der deutschen Rechtspraxis ist auf Deutsch. Für den Staatsexamensweg brauchst du praktisch **nahezu muttersprachliches Deutsch** (mindestens C1, real darüber); Gesetzessprache, Gerichtssprache, Mandantensprache — alles Deutsch. Englisch trägt nur auf der internationalen/LL.M.-Seite. Wenn du dauerhaft in den deutschen Arbeitsmarkt willst — mit oder ohne Anwaltszulassung — ist **Deutsch dein größter Hebel.**

## 6. Der Ablauf: bestätige die Schritte bei der richtigen Stelle
Der grobe Rahmen sieht so aus, aber **kläre deinen Einzelfall unbedingt offiziell:**

1. Ziel klären: **Rechtsanwalt oder Spezialisten-/internationale Rolle?**
2. Bei Ziel Rechtsanwalt: beim **Justizprüfungsamt** deines Bundeslandes den Staatsexamensweg und (falls vorhanden) Anrechnungs-/Teilbefreiungsregeln erfragen.
3. Für deinen Nicht-EU-Abschluss: bei der **Rechtsanwaltskammer** und dem Justizministerium des Landes die Zulassungs-/§-206-BRAO-Optionen bestätigen.
4. Bei internationaler Karriere: in LL.M. + Deutsch + Praktika + Netzwerk investieren; ggf. den [Arbeitsvisum-Prozess mit Jobangebot](/de/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-de) prüfen.

Dieser Beitrag kann nichts garantieren — Regeln variieren nach Land und Fall; lass jeden Schritt vom **Justizprüfungsamt / der Rechtsanwaltskammer** schriftlich bestätigen.

## Fazit & ehrlicher Rat
Sagen wir es offen: **Dass ein Nicht-EU-Ausländer in Deutschland im klassischen Sinne „eine Kanzlei eröffnet", ist unrealistisch.** Rechtsanwalt heißt zwei Staatsexamina + Referendariat + nahezu muttersprachliches Deutsch; der LL.M. gibt das nicht; ein türkischer Abschluss wird nicht direkt anerkannt. Doch das ist keine Sackgasse, sondern eine Frage des **richtigen Ziels**: ausländische Rechtsberatung, internationale Kanzleien, Compliance, LegalTech und Wissenschaft sind echte, wertvolle Wege. Lass zuerst die Fixierung auf „Anwalt werden" los; wähle dann die Spezialisierung, die dir die meisten Türen öffnet.

Vor dem Start unbedingt lesen: [Als Ausländer Jura in Deutschland studieren](/de/blog/studying-law-in-germany-as-a-foreigner-staatsexamen-vs-llb-llm-de).

---
*Grundlage ist der allgemeine Rahmen mit Stand 2026; Anerkennung, Kammerzulassung und Staatsexamensregeln variieren nach Bundesland und Einzelfall — hole vor einer Entscheidung eine Bestätigung beim Justizprüfungsamt und der Rechtsanwaltskammer deines Bundeslandes ein.*
MD;

        $enBody = <<<'MD'
"I'll do an LL.M. and then open a law practice in Germany." — this is the most common, and sadly the **most wrong**, plan among international law students. Short and honest: **A foreigner can, in theory, become a lawyer (Rechtsanwalt) in Germany — but that path is light-years from what most people imagine.** This post gets the disappointment out of the way early and steers you toward a realistic plan.

## 1. The biggest misconception: an LL.M. does NOT make you a lawyer
Let's say it in bold: **an LL.M. (Master of Laws) grants no licence to practise law in Germany.** The LL.M. is a specialisation/academic degree — for international law, commercial law, IP, tax; for work in international firms or in compliance/consulting. However prestigious (Bucerius, LMU, Heidelberg — it makes no difference), the LL.M. **will not make you a Rechtsanwalt.**

The **only** door to becoming a "lawyer" (Rechtsanwalt) in Germany runs through the **Volljurist** qualification — a completely separate, much longer path. Planning to become a lawyer via an LL.M. means setting off with the wrong map. (We cover the English-taught LL.M. route separately: [LL.M. without German](/en/blog/english-taught-law-llm-masters-in-germany-without-german-en).)

## 2. The real path: two Staatsexamina + Referendariat (entirely in German)
Becoming a German lawyer = becoming a **Volljurist**, a brutal three-stage process:

| Stage | What | Duration | Language |
|---|---|---|---|
| Law studies | Jura (German law) | ~4–5 years | **German** |
| **1st Staatsexamen** | First state exam | — | **German** |
| **Referendariat** | Compulsory traineeship (court/prosecutor/administration/firm) | ~2 years (paid) | **German** |
| **2nd Staatsexamen** | Second state exam → Volljurist | — | **German** |

Roughly **7 years** in total, and **no stage is in English.** The exams are built around German law, and the Gutachtenstil (legal-opinion writing style) challenges even native speakers. "I'll become a lawyer" means, in practice, **studying German law from scratch, in German, competing in the same exams as the locals.** Very few foreigners take this path — not impossible, but that's the true cost.

## 3. Recognition of a foreign law degree: very limited
"But I already studied law back home; I'll get it recognised and become a lawyer" — here too, expectation meets reality:

- **Lawyers from EU countries:** via EU rules (EuRAG) and the **Eignungsprüfung** (aptitude test), admission in Germany is possible. A comparatively open route.
- **Lawyers from non-EU countries (e.g. Turkey):** **no direct admission** to the German bar. Your Turkish law degree is not directly recognised for practising as a Rechtsanwalt. Full credit toward the Staatsexamen path is almost never granted; in practice you're expected to (largely) re-study German law.

So for a non-EU lawyer, "practising via recognition" is **not a realistic plan.** (Recognition rules vary by state and by individual case — get binding information from the relevant state justice authority.)

## 4. So what are the real options, e.g. for a Turkish lawyer?
Good news: even without a licence, there are **meaningful legal careers**:

- **Foreign legal consultant (§ 206 BRAO):** under certain conditions you can register in Germany as a consultant for the **law of your home country** (e.g. Turkish law). This is not being a "German lawyer" — you can't represent clients in German law — but it's a real niche in German-Turkish trade, family, and inheritance matters.
- **International firms with an LL.M. + English:** on cross-border deals, in international arbitration, in compliance and contract management, foreign-trained lawyers are valued.
- **In-house / international organisations / LegalTech / academia:** roles that need no licence. (Detailed map: [careers beyond becoming a lawyer](/en/blog/working-in-law-in-germany-careers-beyond-becoming-a-lawyer-en) and [the job market with a foreign law degree](/en/blog/what-to-do-with-a-foreign-law-degree-in-germany-job-market-en).)

In short: set your target on "an international legal career" rather than "Rechtsanwalt," and far more doors open.

## 5. The language reality: C1 is the floor, not the ceiling
Every inch of German legal practice is in German. For the Staatsexamen path you effectively need **near-native German** (at least C1, realistically above); the language of statutes, of the court, of the client — all German. English only carries weight on the international/LL.M. side. If you want to enter the German job market for the long term — with or without a licence — **German is your single biggest lever.**

## 6. The process: confirm each step with the right authority
The rough framework is below, but **verify your own case officially:**

1. Clarify your goal: **Rechtsanwalt, or a specialist/international role?**
2. If aiming for Rechtsanwalt: ask your state's **Justizprüfungsamt** about the Staatsexamen path and any credit/partial-exemption rules.
3. For your non-EU degree: confirm admission and § 206 BRAO consultant options with the **Rechtsanwaltskammer** (bar) and the state justice ministry.
4. If aiming for an international career: invest in an LL.M. + German + internships + network; if needed, review the [work-visa-with-job-offer process](/en/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-en).

This post can't guarantee anything — rules vary by state and by case; have every step confirmed in writing by the **Justizprüfungsamt / Rechtsanwaltskammer.**

## Conclusion & honest advice
Let's be blunt: **a non-EU foreigner "opening a law practice" in Germany in the classic sense is unrealistic.** Being a Rechtsanwalt means two Staatsexamina + Referendariat + near-native German; the LL.M. doesn't provide that; a Turkish degree isn't directly recognised. But this is no dead end — only a matter of **choosing the right goal**: foreign legal consulting, international firms, compliance, LegalTech, and academia are real, valuable paths for lawyers. First drop the fixation on "becoming a lawyer"; then pick the specialisation that opens the most doors for you.

Before you start, definitely read: [studying law in Germany as a foreigner](/en/blog/studying-law-in-germany-as-a-foreigner-staatsexamen-vs-llb-llm-en).

---
*Based on the general framework in force as of 2026; recognition, bar admission and Staatsexamen rules vary by state and by individual case — before deciding, confirm with your state's Justizprüfungsamt and Rechtsanwaltskammer.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'can-foreigners-practice-law-in-germany-staatsexamen-and-recognition',
                'title' => 'Yabancı Almanya\'da Avukatlık Yapabilir mi? Staatsexamen ve Tanınma Gerçeği (2026)',
                'excerpt' => 'En büyük yanılgı: LL.M. seni Almanya\'da avukat YAPMAZ. Rechtsanwalt olmak = iki Staatsexamen + Referendariat (~7 yıl, tamamen Almanca). AB-dışı (Türk) hukuk diploması doğrudan tanınmaz; gerçek seçenekler yabancı hukuk danışmanlığı, uluslararası firmalar ve compliance. Dürüst rehber.',
                'meta_title' => 'Yabancı Almanya\'da Avukat Olabilir mi? Staatsexamen & Tanınma (2026)',
                'meta_description' => 'LL.M. avukat yapmaz: Rechtsanwalt = iki Staatsexamen + Referendariat, tamamen Almanca. AB-dışı diploma tanınması çok sınırlı; Türk hukukçu için gerçek seçenekler — 2026.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'can-foreigners-practice-law-in-germany-staatsexamen-and-recognition-de',
                'title' => 'Können Ausländer in Deutschland als Anwalt arbeiten? Staatsexamen & Anerkennung (2026)',
                'excerpt' => 'Der größte Irrtum: Ein LL.M. macht dich in Deutschland NICHT zum Anwalt. Rechtsanwalt = zwei Staatsexamina + Referendariat (~7 Jahre, komplett auf Deutsch). Ein Nicht-EU-Abschluss (z. B. türkisch) wird nicht direkt anerkannt; echte Optionen: ausländische Rechtsberatung, internationale Kanzleien, Compliance.',
                'meta_title' => 'Ausländer als Anwalt in Deutschland? Staatsexamen & Anerkennung (2026)',
                'meta_description' => 'Ein LL.M. macht keinen Anwalt: Rechtsanwalt = zwei Staatsexamina + Referendariat, komplett auf Deutsch. Nicht-EU-Anerkennung sehr begrenzt; echte Optionen für Juristen — 2026.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'can-foreigners-practice-law-in-germany-staatsexamen-and-recognition-en',
                'title' => 'Can Foreigners Practise Law in Germany? Staatsexamen & Recognition (2026)',
                'excerpt' => 'The biggest misconception: an LL.M. does NOT make you a lawyer in Germany. Becoming a Rechtsanwalt = two Staatsexamina + Referendariat (~7 years, entirely in German). A non-EU (e.g. Turkish) law degree isn\'t directly recognised; the real options are foreign legal consulting, international firms and compliance.',
                'meta_title' => 'Can Foreigners Practise Law in Germany? Staatsexamen & Recognition (2026)',
                'meta_description' => 'An LL.M. won\'t make you a lawyer: Rechtsanwalt = two Staatsexamina + Referendariat, entirely in German. Non-EU recognition is very limited; the real options for lawyers — 2026.',
                'body' => $enBody,
            ],
        ];

        foreach ($variants as $locale => $v) {
            $html = Str::markdown($v['body'], ['html_input' => 'allow', 'allow_unsafe_links' => false]);
            $payload = [
                'locale'           => $locale,
                'translation_group_id' => $groupId,
                'user_id'          => $userId,
                'category_id'      => $categoryId,
                'title'            => $v['title'],
                'excerpt'          => Str::limit($v['excerpt'], 250, '…'),
                'content_md'       => $v['body'],
                'content_html'     => $html,
                'meta_title'       => $v['meta_title'],
                'meta_description' => Str::limit($v['meta_description'], 158, '…'),
                'reading_minutes'  => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
                'is_published'     => true,
                'published_at'     => now(),
            ];

            $existing = Post::where('slug', $v['slug'])->first();
            if ($existing) {
                $existing->update($payload);
            } else {
                Post::create($payload + ['slug' => $v['slug']]);
            }
        }
    }

    public function down(): void
    {
        Post::whereIn('slug', [
            'can-foreigners-practice-law-in-germany-staatsexamen-and-recognition',
            'can-foreigners-practice-law-in-germany-staatsexamen-and-recognition-de',
            'can-foreigners-practice-law-in-germany-staatsexamen-and-recognition-en',
        ])->delete();
    }
};
