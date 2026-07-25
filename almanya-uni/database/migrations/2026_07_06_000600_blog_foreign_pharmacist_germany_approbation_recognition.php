<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Yurtdışı eczacı Almanya'da Approbation & tanınma (2026). Doğrulandı:
 * Pharmazie düzenlenmiş meslek → Gleichwertigkeitsprüfung, eksikse Kenntnisprüfung/Anpassung,
 * C1 + Fachsprachprüfung, geçici Berufserlaubnis; AB-dışı (Türk) diploma eyalet bazlı değerlendirilir.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'f9a20000-2222-4cae-9fd0-ff07aa0ccc02';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Türkiye'de (veya başka bir ülkede) eczacılık diplomanız zaten var ve Almanya'da mesleğinizi yapmak istiyorsunuz. İyi haber: baştan üniversite okumanız gerekmiyor. Kötü haber: diplomanız otomatik geçmez — Almanya'da eczacılık **düzenlenmiş bir meslektir (reglementierter Beruf)** ve halk eczanesinde ya da hastanede eczacı olarak çalışmak için **Approbation als Apotheker** denen resmi ruhsata ihtiyacınız var. Bu yazı, zaten eczacı olan biri için gerçekçi yolu adım adım anlatıyor.

## Kimler için: zaten eczacı olanlar

Bu yazı, Almanya'da sıfırdan eczacılık okumayı değil, **mevcut eczacılık diplomasını tanıtmayı** hedefleyenler için. Yani:

- Türkiye'de eczacılık fakültesini bitirmiş ve diploması olanlar,
- Kendi ülkesinde ruhsatlı çalışmış eczacılar,
- Almanya'da **halk eczanesi (öffentliche Apotheke)** veya **hastane eczanesinde** eczacı unvanıyla çalışmak isteyenler.

**Kritik ayrım:** Eğer amacınız bir ilaç şirketinde (regulatory affairs, pharmacovigilance, kalite) çalışmaksa, o roller için çoğu zaman Approbation ŞART DEĞİLDİR — sanayi yolu daha esnektir. Approbation esas olarak "Apotheker" unvanıyla eczanede/klinikte hastaya doğrudan hizmet ve ilaç teslimi için gerekir. Bu farkı [Almanya'da eczacı olarak çalışmak: eczane, ilaç sanayi ve maaş](/tr/blog/working-as-a-pharmacist-in-germany-pharmacy-industry-and-salary) yazısında ayrıntılı anlattık.

## Approbation süreci: başvuru → Gleichwertigkeitsprüfung → Bescheid

Süreç sağlık mesleklerinde (hekim, diş hekimi, eczacı) benzer mantıkla işler. Ana hat şu:

1. **Yetkili makamı bul.** Approbation eyalet düzeyinde verilir. Başvuruyu yaşamak/çalışmak istediğiniz eyaletin **Approbationsbehörde**'sine (bazı eyaletlerde Landesprüfungsamt / Regierungspräsidium) yaparsınız.
2. **Belgeleri hazırla.** Diploma, transkript, müfredat içeriği (ders saatleri/konular), pasaport, sabıka kaydı, sağlık raporu, dil belgesi. Belgelerin **yeminli Almanca çevirisi** ve çoğu zaman tasdik istenir.
3. **Gleichwertigkeitsprüfung (denklik incelemesi).** Makam, sizin eğitiminizi Alman Pharmazie eğitimiyle karşılaştırır. Süre, saat, içerik "esaslı fark (wesentlicher Unterschied)" içeriyor mu diye bakılır.
4. **Bescheid (karar).** İki olası sonuç:
   - **Denk (gleichwertig):** dil ve diğer koşullar tamamsa doğrudan Approbation yolu açılır.
   - **Esaslı fark var:** farkı kapatmanız istenir → **Kenntnisprüfung** (bilgi sınavı) veya **Anpassungslehrgang** (uyum kursu/telafi).

**Önemli gerçek:** AB/EEA ülkelerinin diplomaları çoğunlukla otomatik tanınır. **Türkiye AB dışı** olduğu için diplomanız "otomatik" değil, **bireysel değerlendirmeye (Einzelfallprüfung)** girer — bu normaldir, ama süreci uzatır.

## Eksikse Kenntnisprüfung + Berufserlaubnis

Denklik tam çıkmazsa, iki kavram hayatınıza girer:

| Kavram | Ne işe yarar | Notlar |
|---|---|---|
| **Kenntnisprüfung** | Alman eczacılık eğitiminin çekirdek konularından sözlü/pratik sınav (farmasötik kimya, farmakoloji, ilaç hukuku vb.) | Esaslı farkı kapatmanın en yaygın yolu; genelde birkaç kez tekrar hakkı var |
| **Anpassungslehrgang** | Belirli süre denetimli uyum kursu/staj + değerlendirme | Kenntnisprüfung'a alternatif olabilir; her eyalette sunulmayabilir |
| **Berufserlaubnis** | **Geçici, sınırlı çalışma izni** (tam Approbation'dan önce) | Süreli ve genelde denetim altında; tam Approbation'ın yerini tutmaz |
| **Fachsprachprüfung** | Mesleki Almanca sınavı (Apothekerkammer) | Genel C1'e ek; hasta/meslektaş iletişimi ölçer |

**Berufserlaubnis** özellikle önemli: bazı eyaletlerde, tam Approbation'ı beklerken **geçici izinle** denetim altında çalışmaya başlayabilirsiniz. Bu, süreci finanse etmenize ve dil/pratik kazanmanıza yardım eder — ama kalıcı değildir, hedef her zaman tam Approbation'dır.

## C1 + Fachsprachprüfung: dil iki katmanlıdır

Eczacılıkta dil, tek bir sertifikayla bitmez. İki katman düşünün:

- **Genel Almanca — C1.** Çoğu Approbationsbehörde tanınma için **C1 seviyesi** (telc/Goethe/ÖSD) bekler. B2 çoğu durumda yetmez.
- **Fachsprachprüfung (mesleki dil sınavı).** İlgili **Apothekerkammer** (eczacı odası) tarafından yapılan, mesleki Almanca sınavı. Reçete danışmanlığı, ilaç etkileşimi anlatımı, meslektaşla iletişim gibi gerçek sahneleri ölçer.

**Dürüst uyarı:** Dil, bu yolda en çok küçümsenen ve en çok takılınan basamaktır. Eczanede hastaya ilaç kullanımını yanlış anlatmak hayati risktir; bu yüzden dil çıtası bilinçli olarak yüksektir. Almanca çalışmasını "tanınma başvurusundan sonra hallederim" diye ertelemeyin — paralel yürütün.

## AB dışı (Türk) diploma gerçeği

Net konuşalım: **Türk diploması AB içi diploma gibi otomatik geçmez.** Bu bir ayrımcılık değil, sistemin yapısı — AB dışı her diploma bireysel incelenir. Pratikte bu şu anlama gelir:

- **Süreç daha uzun** ve daha çok belge ister (müfredat içeriği, ders saatleri sık istenir).
- **Kenntnisprüfung ihtimali daha yüksektir** — çünkü müfredat birebir eşleşmeyebilir.
- **Belge titizliği kritik:** müfredat içeriğini, ders saatlerini ve staj kayıtlarını fakültenizden eksiksiz ve mühürlü almak, dosyanızın en zayıf halkasıdır. Eksik bir belge, süreci aylarca geciktirebilir; bu yüzden ayrılmadan önce mümkün olan her şeyi Türkiye'den toplayın ve yeminli çevirilerini hazırlatın.
- Yine de **yol kapalı değildir.** Türkiye'de eczacılık eğitimi güçlüdür; birçok Türk eczacı bu süreçten geçip Almanya'da çalışmaktadır. Sabır, iyi planlama ve güçlü Almanca ile başarı oranı yüksektir.

Bu tablo, benzer düzenlenmiş sağlık mesleklerinin hepsinde geçerlidir. Aynı mantığı hekimler için [yurtdışı hekimlerin Approbation ve FSP/Kenntnisprüfung süreci](/tr/blog/foreign-doctors-germany-approbation-fsp-kenntnispruefung) ve diş hekimleri için [yurtdışı diş hekimi Almanya'da Approbation ve tanınma](/tr/blog/foreign-dentist-in-germany-approbation-and-recognition) yazılarında da göreceksiniz — süreçler kardeş kardeştir.

## Süre, bürokrasi ve maliyet (hedge'li)

Kesin süre vermek dürüst olmaz, çünkü **eyalete, dosyanın eksiksizliğine ve dil durumunuza göre değişir.** Gerçekçi çerçeve:

- **Değişkenler:** hangi eyalet, belgelerin tam/çevrili olması, denklik sonucunun ne çıktığı, Kenntnisprüfung sırası/kontenjanı, dil seviyeniz.
- **Maliyet kalemleri:** başvuru/sınav harçları, yeminli çeviri ve tasdik, dil kursları ve sınavları, (gerekirse) Kenntnisprüfung hazırlığı, geçim.
- **Vize/oturum:** Yurtdışından geliyorsanız tanınma süreci ile oturum/çalışma izni ayrı işler. İş teklifi temelli çalışma vizesi mantığını [iş teklifiyle Almanya çalışma vizesi: süreç ve hızlı yol](/tr/blog/germany-work-visa-with-job-offer-process-timeline-fast-track) yazısında anlattık.

**En kritik cümle:** Süreyi, harçları ve tam belge listesini **çalışmak istediğiniz eyaletin Approbationsbehörde'sinden ve ilgili Apothekerkammer'den resmi olarak doğrulayın.** İnternetteki "6 ayda biter / 2 yıl sürer" gibi kesin ifadelere değil, size verilen resmi Bescheid'e güvenin.

## Sonuç & dürüst tavsiye

Zaten eczacıysanız, Almanya'da eczacılık yapmak **gerçekçi ve açık bir yoldur** — ama otomatik değildir. Dürüst özet:

- **Yol var:** Gleichwertigkeitsprüfung → gerekirse Kenntnisprüfung → C1 + Fachsprachprüfung → Approbation. Bu sağlık mesleklerinin standart tanınma hattıdır.
- **Türk diploması** bireysel incelenir; sabır, tam dosya ve güçlü Almanca işin belkemiğidir.
- **Sadece sanayi hedefiyse**, Approbation olmadan da (regulatory affairs, pharmacovigilance) yol bulunabilir — [eczane ve ilaç sanayi yollarını](/tr/blog/working-as-a-pharmacist-in-germany-pharmacy-industry-and-salary) karşılaştırın.
- **Karar aşamasındaysanız**, [Almanya'da eczacılık okumaya değer mi?](/tr/blog/is-studying-pharmacy-in-germany-worth-it-honest-reality) ve sıfırdan başlayanlar için [uluslararası öğrenci olarak Almanya'da eczacılık okumak](/tr/blog/studying-pharmacy-pharmazie-in-germany-as-a-foreigner) yazılarına bakın.

En büyük tavsiye: **erken başlayın.** Dili bugün çalışmaya başlayın, hedef eyaleti seçin ve o eyaletin makamıyla temasa geçin. Süreç uzun ama kırılmaz değil.

*Bu yazı 2026 başı itibarıyla genel bilgilendirme amaçlıdır ve resmi danışmanlık değildir. Approbation kuralları, süreler, harçlar ve dil koşulları eyalete göre ve zamanla değişir. Karar vermeden önce ilgili eyaletin **Approbationsbehörde**'si ve **Apothekerkammer**'i üzerinden güncel bilgiyi resmi olarak doğrulayın.*
MD;
        $deBody = <<<'MD'
Du hast bereits ein Pharmaziediplom aus der Türkei (oder einem anderen Land) und möchtest in Deutschland als Apotheker arbeiten. Die gute Nachricht: Du musst nicht bei null studieren. Die schlechte: Dein Diplom gilt nicht automatisch — Pharmazie ist in Deutschland ein **reglementierter Beruf**, und für die Arbeit in einer öffentlichen Apotheke oder Krankenhausapotheke brauchst du die **Approbation als Apotheker**. Dieser Beitrag zeigt dir den realistischen Weg, wenn du schon Apotheker bist.

## Für wen: für bereits ausgebildete Apotheker

Dieser Text richtet sich nicht an Studieninteressierte, sondern an alle, die eine **bestehende Pharmazie-Qualifikation anerkennen lassen** wollen:

- Absolventen einer Pharmaziefakultät (z. B. in der Türkei) mit Diplom,
- Apotheker, die im Herkunftsland approbiert/lizenziert gearbeitet haben,
- alle, die in Deutschland in einer **öffentlichen Apotheke** oder **Krankenhausapotheke** als Apotheker tätig sein wollen.

**Wichtige Unterscheidung:** Wenn dein Ziel ein Pharmaunternehmen ist (Regulatory Affairs, Pharmakovigilanz, Qualität), brauchst du für viele dieser Rollen KEINE Approbation — der Industrieweg ist flexibler. Die Approbation ist vor allem für die direkte Patientenversorgung als "Apotheker" nötig. Diesen Unterschied erklären wir im Beitrag [Als Apotheker in Deutschland arbeiten: Apotheke, Pharmaindustrie und Gehalt](/de/blog/working-as-a-pharmacist-in-germany-pharmacy-industry-and-salary-de).

## Approbationsverfahren: Antrag → Gleichwertigkeitsprüfung → Bescheid

Das Verfahren läuft bei den Heilberufen (Arzt, Zahnarzt, Apotheker) nach ähnlicher Logik:

1. **Finde die zuständige Behörde.** Die Approbation wird auf Länderebene erteilt. Du stellst den Antrag bei der **Approbationsbehörde** des Bundeslandes, in dem du arbeiten willst (je nach Land Landesprüfungsamt / Regierungspräsidium).
2. **Bereite die Unterlagen vor.** Diplom, Notenübersicht, Studieninhalte (Stunden/Fächer), Pass, Führungszeugnis, ärztliches Attest, Sprachnachweis. In der Regel werden **beglaubigte deutsche Übersetzungen** verlangt.
3. **Gleichwertigkeitsprüfung.** Die Behörde vergleicht deine Ausbildung mit dem deutschen Pharmaziestudium und prüft, ob es einen **wesentlichen Unterschied** in Dauer, Umfang oder Inhalt gibt.
4. **Bescheid.** Zwei mögliche Ergebnisse:
   - **Gleichwertig:** Sind Sprache und übrige Voraussetzungen erfüllt, führt der Weg direkt zur Approbation.
   - **Wesentlicher Unterschied:** Du musst ihn ausgleichen → **Kenntnisprüfung** oder **Anpassungslehrgang**.

**Wichtige Realität:** Diplome aus EU/EWR-Staaten werden meist automatisch anerkannt. Weil die **Türkei ein Drittstaat** ist, wird dein Diplom nicht "automatisch", sondern in einer **Einzelfallprüfung** bewertet — das ist normal, verlängert aber das Verfahren.

## Wenn etwas fehlt: Kenntnisprüfung + Berufserlaubnis

Fällt die Gleichwertigkeit nicht vollständig aus, kommen zwei Begriffe ins Spiel:

| Begriff | Wofür | Hinweise |
|---|---|---|
| **Kenntnisprüfung** | Mündlich-praktische Prüfung über Kernfächer der deutschen Pharmazieausbildung (pharmazeutische Chemie, Pharmakologie, Arzneimittelrecht usw.) | Häufigster Weg, den wesentlichen Unterschied auszugleichen; meist mehrfach wiederholbar |
| **Anpassungslehrgang** | Befristete, betreute Anpassung + Bewertung | Kann Alternative zur Kenntnisprüfung sein; nicht in jedem Land angeboten |
| **Berufserlaubnis** | **Befristete, eingeschränkte Arbeitserlaubnis** (vor der vollen Approbation) | Zeitlich begrenzt und meist unter Aufsicht; ersetzt die volle Approbation nicht |
| **Fachsprachprüfung** | Fachsprachliche Prüfung (Apothekerkammer) | Zusätzlich zum allgemeinen C1; misst Patienten-/Kollegenkommunikation |

Die **Berufserlaubnis** ist besonders relevant: In manchen Ländern kannst du mit einer befristeten Erlaubnis bereits unter Aufsicht arbeiten, während du auf die volle Approbation wartest. Das hilft bei Finanzierung und Praxis — ist aber nicht dauerhaft; Ziel bleibt immer die volle Approbation.

## C1 + Fachsprachprüfung: Sprache ist zweistufig

In der Pharmazie ist es mit einem Zertifikat nicht getan. Denk in zwei Stufen:

- **Allgemeindeutsch — C1.** Die meisten Approbationsbehörden erwarten für die Anerkennung **Niveau C1** (telc/Goethe/ÖSD). B2 reicht in den meisten Fällen nicht.
- **Fachsprachprüfung.** Eine fachsprachliche Prüfung der jeweiligen **Apothekerkammer**. Sie prüft reale Szenen: Beratung zu Rezepten, Erklären von Wechselwirkungen, Kommunikation mit Kollegen.

**Ehrliche Warnung:** Sprache ist auf diesem Weg die am meisten unterschätzte und am häufigsten unterschätzte Hürde. Eine falsche Erklärung zur Einnahme ist ein reales Risiko — deshalb ist die Sprachlatte bewusst hoch. Verschiebe das Deutschlernen nicht auf "nach dem Antrag"; mach es parallel.

## Die Realität des Nicht-EU-Diploms (Türkei)

Klartext: **Ein türkisches Diplom gilt nicht automatisch wie ein EU-Diplom.** Das ist keine Diskriminierung, sondern die Struktur des Systems — jedes Drittstaatsdiplom wird einzeln geprüft. Praktisch heißt das:

- **Das Verfahren dauert länger** und verlangt mehr Nachweise (Studieninhalte, Stunden werden oft gefordert).
- **Die Wahrscheinlichkeit einer Kenntnisprüfung ist höher**, weil das Curriculum nicht 1:1 passt.
- Trotzdem ist der **Weg nicht versperrt.** Die Pharmazieausbildung in der Türkei ist stark; viele türkische Apotheker gehen diesen Weg erfolgreich.

Dieses Muster gilt bei allen reglementierten Heilberufen. Dieselbe Logik siehst du für Ärzte im Beitrag [Approbation und FSP/Kenntnisprüfung für ausländische Ärzte](/de/blog/foreign-doctors-germany-approbation-fsp-kenntnispruefung-de) und für Zahnärzte in [Ausländische Zahnärzte in Deutschland: Approbation und Anerkennung](/de/blog/foreign-dentist-in-germany-approbation-and-recognition-de) — die Verfahren sind Geschwister.

## Dauer, Bürokratie und Kosten (mit Vorbehalt)

Eine feste Dauer zu nennen wäre unehrlich, denn sie hängt vom **Bundesland, der Vollständigkeit der Akte und deiner Sprachsituation** ab. Realistischer Rahmen:

- **Variablen:** welches Land, vollständige/übersetzte Unterlagen, Ergebnis der Gleichwertigkeitsprüfung, Termin/Kontingent der Kenntnisprüfung, dein Sprachniveau.
- **Kostenpunkte:** Antrags-/Prüfungsgebühren, beglaubigte Übersetzungen, Sprachkurse und -prüfungen, ggf. Vorbereitung auf die Kenntnisprüfung, Lebenshaltung.
- **Visum/Aufenthalt:** Kommst du aus dem Ausland, sind Anerkennung und Aufenthalts-/Arbeitserlaubnis getrennte Vorgänge. Die Logik des Arbeitsvisums mit Jobangebot erklären wir in [Arbeitsvisum für Deutschland mit Jobangebot: Prozess und Fast Track](/de/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-de).

**Der wichtigste Satz:** Lass Dauer, Gebühren und die vollständige Unterlagenliste **offiziell von der Approbationsbehörde deines Ziel-Bundeslandes und der zuständigen Apothekerkammer bestätigen.** Verlass dich nicht auf "in 6 Monaten erledigt / dauert 2 Jahre" aus dem Internet, sondern auf deinen offiziellen Bescheid.

## Fazit & ehrlicher Rat

Wenn du bereits Apotheker bist, ist die Arbeit in Deutschland ein **realistischer, offener Weg** — aber kein automatischer. Ehrliche Zusammenfassung:

- **Es gibt einen Weg:** Gleichwertigkeitsprüfung → ggf. Kenntnisprüfung → C1 + Fachsprachprüfung → Approbation. Das ist die Standard-Anerkennungslinie der Heilberufe.
- **Das türkische Diplom** wird einzeln geprüft; Geduld, vollständige Akte und starkes Deutsch sind das Rückgrat.
- **Nur Industrie als Ziel?** Dann geht es auch ohne Approbation (Regulatory Affairs, Pharmakovigilanz) — vergleiche die [Apotheken- und Industriewege](/de/blog/working-as-a-pharmacist-in-germany-pharmacy-industry-and-salary-de).
- **Noch in der Entscheidung?** Lies [Lohnt sich ein Pharmaziestudium in Deutschland?](/de/blog/is-studying-pharmacy-in-germany-worth-it-honest-reality-de) und für Neuanfänger [Pharmazie in Deutschland als Ausländer studieren](/de/blog/studying-pharmacy-pharmazie-in-germany-as-a-foreigner-de).

Der beste Rat: **Fang früh an.** Lerne heute Deutsch, wähle dein Ziel-Bundesland und nimm Kontakt mit dessen Behörde auf. Der Weg ist lang, aber nicht unmöglich.

*Dieser Beitrag ist eine allgemeine Information (Stand Anfang 2026) und keine offizielle Beratung. Regeln, Dauer, Gebühren und Sprachanforderungen für die Approbation unterscheiden sich je nach Bundesland und ändern sich mit der Zeit. Bestätige aktuelle Angaben vor jeder Entscheidung offiziell bei der **Approbationsbehörde** und der **Apothekerkammer** des jeweiligen Bundeslandes.*
MD;
        $enBody = <<<'MD'
You already hold a pharmacy degree from Turkey (or another country) and you want to practise in Germany. The good news: you don't have to study from scratch. The bad news: your degree isn't valid automatically — in Germany pharmacy is a **regulated profession (reglementierter Beruf)**, and to work in a community pharmacy or hospital pharmacy you need the **Approbation als Apotheker** (the official licence to practise). This post walks through the realistic path if you are already a pharmacist.

## Who this is for: already-qualified pharmacists

This article is not for prospective students. It's for people who want to have an **existing pharmacy qualification recognised**:

- graduates of a pharmacy faculty (e.g. in Turkey) with a degree,
- pharmacists who were licensed and worked in their home country,
- anyone who wants to work in Germany as a pharmacist in a **community pharmacy (öffentliche Apotheke)** or **hospital pharmacy**.

**Key distinction:** If your goal is a pharmaceutical company (regulatory affairs, pharmacovigilance, quality), many of those roles do NOT require an Approbation — the industry route is more flexible. The Approbation is mainly needed to serve patients directly as an "Apotheker". We cover this difference in [Working as a pharmacist in Germany: pharmacy, industry and salary](/en/blog/working-as-a-pharmacist-in-germany-pharmacy-industry-and-salary-en).

## The Approbation process: application → Gleichwertigkeitsprüfung → Bescheid

The process follows a similar logic across the health professions (doctor, dentist, pharmacist):

1. **Find the responsible authority.** The Approbation is granted at state (Land) level. You apply to the **Approbationsbehörde** of the federal state where you want to work (depending on the state, the Landesprüfungsamt / Regierungspräsidium).
2. **Prepare the documents.** Degree, transcript, course content (hours/subjects), passport, criminal record certificate, health certificate, language proof. **Certified German translations** are usually required.
3. **Gleichwertigkeitsprüfung (equivalence assessment).** The authority compares your training with the German pharmacy programme and checks whether there is a **substantial difference (wesentlicher Unterschied)** in duration, scope or content.
4. **Bescheid (the decision).** Two possible outcomes:
   - **Equivalent:** if language and other conditions are met, the path leads directly to the Approbation.
   - **Substantial difference:** you must close the gap → **Kenntnisprüfung** (knowledge exam) or **Anpassungslehrgang** (adaptation course).

**Important reality:** Degrees from EU/EEA countries are mostly recognised automatically. Because **Turkey is a non-EU (third) country**, your degree is not "automatic" but assessed in an **individual case review (Einzelfallprüfung)** — this is normal, but it lengthens the process.

## If something is missing: Kenntnisprüfung + Berufserlaubnis

If equivalence isn't full, two terms enter your life:

| Term | What it does | Notes |
|---|---|---|
| **Kenntnisprüfung** | Oral/practical exam on core subjects of German pharmacy training (pharmaceutical chemistry, pharmacology, medicines law, etc.) | Most common way to close a substantial difference; usually repeatable |
| **Anpassungslehrgang** | A fixed-term supervised adaptation period + assessment | Can be an alternative to the Kenntnisprüfung; not offered in every state |
| **Berufserlaubnis** | A **temporary, limited permission to work** (before full Approbation) | Time-limited and usually supervised; does not replace full Approbation |
| **Fachsprachprüfung** | Professional-language exam (Apothekerkammer) | On top of general C1; tests patient/colleague communication |

The **Berufserlaubnis** matters a lot: in some states you can start working under supervision with a temporary permission while you wait for the full Approbation. It helps with financing and practice — but it is not permanent; the goal is always the full Approbation.

## C1 + Fachsprachprüfung: language is two layers

In pharmacy, one certificate isn't enough. Think in two layers:

- **General German — C1.** Most Approbationsbehörden expect **level C1** (telc/Goethe/ÖSD) for recognition. B2 is not enough in most cases.
- **Fachsprachprüfung (professional-language exam).** A professional-German exam run by the relevant **Apothekerkammer** (chamber of pharmacists). It tests real scenes: advising on prescriptions, explaining drug interactions, communicating with colleagues.

**Honest warning:** Language is the most underestimated and most-tripped-over step on this path. Explaining a medication's use incorrectly is a real risk, which is exactly why the language bar is deliberately high. Don't postpone German to "after I file the application" — run it in parallel.

## The reality of a non-EU (Turkish) degree

Plain talk: **a Turkish degree does not pass automatically the way an EU degree does.** That's not discrimination — it's the structure of the system, where every non-EU degree is reviewed individually. In practice this means:

- **The process takes longer** and asks for more evidence (course content and hours are often requested).
- **The chance of a Kenntnisprüfung is higher**, because the curriculum may not match 1:1.
- Even so, **the path is not closed.** Pharmacy education in Turkey is strong; many Turkish pharmacists go through this process and work in Germany.

This pattern holds across all regulated health professions. You'll see the same logic for doctors in [Approbation and FSP/Kenntnisprüfung for foreign doctors](/en/blog/foreign-doctors-germany-approbation-fsp-kenntnispruefung-en) and for dentists in [Foreign dentists in Germany: Approbation and recognition](/en/blog/foreign-dentist-in-germany-approbation-and-recognition-en) — the processes are siblings.

## Duration, bureaucracy and cost (hedged)

Giving a fixed timeline would be dishonest, because it depends on the **state, the completeness of your file and your language situation.** A realistic frame:

- **Variables:** which state, complete/translated documents, the equivalence outcome, the Kenntnisprüfung slot/quota, your language level.
- **Cost items:** application/exam fees, certified translations, language courses and exams, (if needed) Kenntnisprüfung preparation, living costs.
- **Visa/residence:** if you come from abroad, recognition and residence/work permit are separate processes. We explain the job-offer work-visa logic in [Germany work visa with a job offer: process and fast track](/en/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-en).

**The most important sentence:** get the duration, fees and full document list **officially confirmed by the Approbationsbehörde of your target state and the relevant Apothekerkammer.** Don't trust "done in 6 months / takes 2 years" from the internet — trust the official Bescheid you receive.

## Conclusion & honest advice

If you are already a pharmacist, practising in Germany is a **realistic, open path** — but not an automatic one. Honest summary:

- **There is a path:** Gleichwertigkeitsprüfung → Kenntnisprüfung if needed → C1 + Fachsprachprüfung → Approbation. This is the standard recognition line of the health professions.
- **A Turkish degree** is reviewed individually; patience, a complete file and strong German are the backbone.
- **Only industry as a goal?** Then it can work without an Approbation (regulatory affairs, pharmacovigilance) — compare the [pharmacy and industry routes](/en/blog/working-as-a-pharmacist-in-germany-pharmacy-industry-and-salary-en).
- **Still deciding?** Read [Is studying pharmacy in Germany worth it?](/en/blog/is-studying-pharmacy-in-germany-worth-it-honest-reality-en) and, for those starting from scratch, [Studying pharmacy in Germany as a foreigner](/en/blog/studying-pharmacy-pharmazie-in-germany-as-a-foreigner-en).

The best advice: **start early.** Learn German today, pick your target state and contact its authority. The path is long, but it is not unbreakable.

*This article is general information (as of early 2026) and not official advice. Approbation rules, timelines, fees and language requirements differ by federal state and change over time. Before making any decision, confirm current details officially with the **Approbationsbehörde** and the **Apothekerkammer** of the relevant federal state.*
MD;

        $variants = [
            'tr' => ['slug'=>'foreign-pharmacist-in-germany-approbation-and-recognition',    'title'=>'Yurtdışı Eczacı Almanya\'da Çalışabilir mi? Approbation ve Tanınma (2026)', 'excerpt'=>'Türkiye\'de eczacılık diplomanız var ve Almanya\'da çalışmak mı istiyorsunuz? Approbation, Gleichwertigkeitsprüfung, Kenntnisprüfung, C1 + Fachsprachprüfung ve AB-dışı diploma gerçeği — zaten eczacı olanlar için adım adım gerçekçi yol.', 'meta_title'=>'Yurtdışı Eczacı Almanya\'da: Approbation & Tanınma (2026)', 'meta_description'=>'Almanya\'da eczacı olarak çalışmak için Approbation süreci: Gleichwertigkeitsprüfung, Kenntnisprüfung, Berufserlaubnis, C1 + Fachsprachprüfung ve Türk diploması gerçeği.', 'body'=>$trBody],
            'de' => ['slug'=>'foreign-pharmacist-in-germany-approbation-and-recognition-de', 'title'=>'Als ausländischer Apotheker in Deutschland: Approbation & Anerkennung (2026)',        'excerpt'=>'Du hast ein Pharmaziediplom aus dem Ausland und willst in Deutschland arbeiten? Approbation, Gleichwertigkeitsprüfung, Kenntnisprüfung, C1 + Fachsprachprüfung und die Realität des Nicht-EU-Diploms — der realistische Weg für bereits ausgebildete Apotheker.',   'meta_title'=>'Ausländischer Apotheker in Deutschland: Approbation (2026)',  'meta_description'=>'Approbationsverfahren für Apotheker: Gleichwertigkeitsprüfung, Kenntnisprüfung, Berufserlaubnis, C1 + Fachsprachprüfung und die Realität des Drittstaatsdiploms.',   'body'=>$deBody],
            'en' => ['slug'=>'foreign-pharmacist-in-germany-approbation-and-recognition-en', 'title'=>'Can a Foreign Pharmacist Work in Germany? Approbation & Recognition (2026)',        'excerpt'=>'Hold a pharmacy degree from Turkey and want to work in Germany? Approbation, Gleichwertigkeitsprüfung, Kenntnisprüfung, C1 + Fachsprachprüfung and the reality of a non-EU degree — the realistic, step-by-step path for already-qualified pharmacists.',   'meta_title'=>'Foreign Pharmacist in Germany: Approbation & Recognition (2026)',  'meta_description'=>'The Approbation process to work as a pharmacist in Germany: Gleichwertigkeitsprüfung, Kenntnisprüfung, Berufserlaubnis, C1 + Fachsprachprüfung and the non-EU degree reality.',   'body'=>$enBody],
        ];

        foreach ($variants as $locale => $v) {
            $html = Str::markdown($v['body'], ['html_input' => 'allow', 'allow_unsafe_links' => false]);
            $payload = [
                'locale'=>$locale, 'translation_group_id'=>$groupId, 'user_id'=>$userId, 'category_id'=>$categoryId,
                'title'=>$v['title'], 'excerpt'=>Str::limit($v['excerpt'],250,'…'),
                'content_md'=>$v['body'], 'content_html'=>$html,
                'meta_title'=>$v['meta_title'], 'meta_description'=>Str::limit($v['meta_description'],158,'…'),
                'reading_minutes'=>max(1,(int)round(str_word_count(strip_tags($html))/200)),
                'is_published'=>true, 'published_at'=>now(),
            ];
            $existing = Post::where('slug', $v['slug'])->first();
            $existing ? $existing->update($payload) : Post::create($payload + ['slug'=>$v['slug']]);
        }
    }

    public function down(): void
    {
        Post::whereIn('slug', ['foreign-pharmacist-in-germany-approbation-and-recognition', 'foreign-pharmacist-in-germany-approbation-and-recognition-de', 'foreign-pharmacist-in-germany-approbation-and-recognition-en'])->delete();
    }
};
