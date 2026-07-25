<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Yurtdışı diş hekimi Almanya'da çalışabilir mi — Approbation als Zahnarzt & tanınma (2026).
 * Doğrulandı: Zahnmedizin düzenlenmiş meslektir; zaten diş hekimi olanlar Approbation'a başvurur
 * (Gleichwertigkeitsprüfung → Bescheid, eksikse Kenntnisprüfung), C1 Almanca + Fachsprachprüfung şart.
 * AB-dışı (Türk) diploma için denklik değerlendirilir. Süre/adım yıl-hedge; eyalet Approbationsbehörde doğrula.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'e8f20000-2222-4b9d-9fc0-ee06ff0bbb02';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da diş hekimliği **okumak** uluslararası öğrenci için neredeyse imkânsız denecek kadar zordur (NC ~1,0, İngilizce program yok). Ama zaten **diploması olan bir diş hekimiysen** durum tamamen farklıdır: senin yolun okula girmek değil, **tanınma (Approbation als Zahnarzt)** yoludur. Bürokratik, sabır isteyen ama net ve gerçek bir yoldur — ve Almanya'da diş hekimi açığı olduğu için değerlidir. İşte dürüst harita.

## Bu yazı kimin için: zaten diş hekimi olanlar
Bu rehber, **kendi ülkesinde (ör. Türkiye) diş hekimliği diploması almış** ya da almak üzere olan kişiler içindir. Sıfırdan Almanya'da okumayı düşünüyorsan yolun bambaşka; onu ayrı ele aldık: [Almanya'da diş hekimliği okumak](/tr/blog/studying-dentistry-zahnmedizin-in-germany-as-a-foreigner). Zaten hekimsen **okuma yeri kapmakla uğraşmana gerek yok** — doğrudan tanınma sürecine girersin. Zaten diş hekimi olan Türkler için **en gerçekçi yol budur.** Not: Bir AB/AEA ülkesinde okumuş olanlar için süreç çok daha kısadır (otomatik tanıma); Türk diploması ise ayrı bir denklik incelemesinden geçer. Bu yazıda odak, **AB-dışı diploma** sahibi diş hekimidir.

## Approbation vs Berufserlaubnis
Karıştırılan iki farklı lisans var:
- **Approbation:** **süresiz, tam** diş hekimliği lisansı. Bağımsız çalışabilir, kendi muayeneni açabilir, her yerde Zahnarzt olarak çalışabilirsin. Nihai hedef budur.
- **Berufserlaubnis:** **süreli ve sınırlı** izin (genelde en fazla **~2 yıl**, işverene/yere bağlı). Tanınmayı tamamlarken bir **köprü** görevi görür; süreci beklerken kliniğde çalışmaya başlamanı sağlar.

## Approbation süreci: başvuru → Gleichwertigkeitsprüfung → Bescheid
Adımlar kabaca şöyle işler (eyalete göre değişir):
1. **Başvuru:** Çalışmak istediğin eyaletin **Approbationsbehörde**'sine (yetkili makam) diplomanı, transkriptini, staj/pratik belgelerini tercümeli-onaylı verirsin.
2. **Gleichwertigkeitsprüfung (denklik incelemesi):** Makam senin eğitimini Alman diş hekimliği eğitimiyle **karşılaştırır**. AB-dışı (Türk) diploma **otomatik tanınmaz**; içerik ve süre bazında denk olup olmadığı incelenir.
3. **Bescheid (karar):** Sonuç yazılı gelir. **Denk bulunursa** → dil şartını tamamlayınca Approbation. **Önemli eksik (wesentlicher Unterschied) bulunursa** → **Kenntnisprüfung** vermen istenir.

## Eksik varsa: Kenntnisprüfung
Diploman tam denk sayılmazsa devreye **Kenntnisprüfung** (bilgi sınavı) girer — Alman devlet sınavı (Staatsexamen) seviyesinde, pratik ve sözlü ağırlıklı bir diş hekimliği sınavıdır. Aşağıda iki yolun karşılaştırması var:

| Nokta | Diploma **denk** bulunursa | **Önemli eksik** varsa |
|---|---|---|
| Ek sınav | Gerekmez | **Kenntnisprüfung** şart |
| İçerik | — | Klinik + teorik diş hekimliği bilgisi |
| Dil | C1 + Fachsprachprüfung | C1 + Fachsprachprüfung |
| Sonuç | Approbation | Geçersen Approbation |

Kenntnisprüfung genelde **konservatif diş hekimliği, cerrahi, protez, çocuk diş hekimliği** gibi alanları kapsar ve kalırsan **sınırlı sayıda tekrar hakkın** olur — bu yüzden hafife alınmamalı.

## C1 Almanca + Fachsprachprüfung
Dil, sürecin bel kemiğidir ve iki katmanlıdır:
- **Genel C1 Almanca:** çoğu eyalet en az **C1** genel dil seviyesi ister (bazıları B2+FSP kabul eder — eyaletine göre değişir).
- **Fachsprachprüfung (FSP):** **diş hekimliği Almancası** sınavı. Bölge **Zahnärztekammer**'i (diş hekimleri odası) düzenler: hasta görüşmesi, meslektaş iletişimi ve belge/anamnez yazımı test edilir. **Goethe/telc sertifikası FSP'nin yerini tutmaz** — FSP ayrı bir sınavdır.

Hastayla güven kurmak, aydınlatılmış onam almak ve tıbbi kayıt tutmak akıcı Almanca gerektirir; bu yüzden dile **erkenden ve ciddi** yüklen.

## AB-dışı (Türk) diploma gerçeği
Açık konuşalım: **AB/AEA diploması otomatik tanınırken, Türk diploması otomatik tanınmaz** — mutlaka Gleichwertigkeitsprüfung'dan geçer. Sonuç iki türlü olabilir: ya doğrudan denklik (sadece dil kalır) ya da Kenntnisprüfung. Bu, kişisel eğitim içeriğine ve makamın değerlendirmesine bağlıdır; **iki Türk diş hekiminin sonucu farklı çıkabilir.** Denklik mantığı, doktorların Approbation süreciyle büyük ölçüde paraleldir — kıyas için: [yabancı doktor olarak Almanya'da çalışmak (Approbation, FSP, KP)](/tr/blog/foreign-doctors-germany-approbation-fsp-kenntnispruefung).

## Süre, bürokrasi ve çalışma izni
Süreler **eyalete ve dosyanın hızına göre çok değişir**; net bir garanti yoktur. Kenntnisprüfung ve FSP randevu kuyrukları bazı eyaletlerde **aylar sürebilir** — esnek isen daha hızlı bir Bundesland stratejik olabilir. Süreci beklerken **Berufserlaubnis** ile kliniğde çalışmaya başlayabilir, hem gelir elde edip hem pratik Almancanı geliştirebilirsin. **Vize/çalışma izni** ayrı bir başlıktır: iş teklifi ve tanınma bir arada ilerler; süreç için [iş teklifiyle Almanya çalışma vizesi](/tr/blog/germany-work-visa-with-job-offer-process-timeline-fast-track) yazımıza bak. **Tüm süre/adım/ücret bilgilerini** başvurudan önce ilgili **Approbationsbehörde / Zahnärztekammer**'den teyit et.

## Belgeler, ücretler ve hazırlık
Başvuru dosyası genelde şunları içerir: **diploma ve transkript** (yeminli tercüme + noter/apostil onayı), **müfredat/ders içeriği (Fächer- und Stundennachweis)**, **adli sicil / iyi hal belgesi (Führungszeugnis)**, **sağlık raporu**, **kimlik** ve varsa **mesleki deneyim belgeleri.** Denklik ve sınav ücretleri eyalete göre değişir ve azımsanmayacak tutabilir; tercüme-onay masraflarını da bütçene ekle. En verimli hazırlık yatırımı **dil + Kenntnisprüfung kurslarıdır** (bazı eyaletlerde hazırlık kursları ve deneme sınavları vardır). Belgelerini erken ve eksiksiz topla — **eksik dosya, süreci aylarca geciktiren en yaygın nedendir.**

## Sonuç & dürüst tavsiye
Zaten diş hekimiysen, Almanya'da çalışmanın **en gerçekçi yolu tanınmadır** — okula yeniden başvurmak değil. Yol bürokratiktir ama kurallıdır ve sonu tam **Approbation als Zahnarzt**'tır. Anahtarlar: **diş hekimliği Almancasına erken yüklen**, diploma denkliğini önceden araştır, randevu için hızlı bir eyalet seç. Approbation'dan sonra ne olduğunu (zorunlu asistanlık, maaş, kendi muayenehane) merak ediyorsan: [Almanya'da diş hekimi olarak çalışmak](/tr/blog/working-as-a-dentist-in-germany-salary-career-and-own-practice). Bu yolun sana değip değmeyeceğini tartan dürüst değerlendirme için: [diş hekimliği okumaya değer mi?](/tr/blog/is-studying-dentistry-in-germany-worth-it-honest-reality).

---
*2026 itibarıyla yürürlükteki genel çerçeve temel alınmıştır; denklik, Kenntnisprüfung, FSP ve dil şartları eyalete (Bundesland) ve diş hekimleri odasına göre değişir — başvurudan önce ilgili Zahnärztekammer / Approbationsbehörde'den teyit et.*
MD;

        $deBody = <<<'MD'
In Deutschland Zahnmedizin zu **studieren** ist für internationale Bewerber fast unmöglich (NC ~1,0, kein englischsprachiges Programm). Bist du aber bereits **approbierter oder ausgebildeter Zahnarzt**, sieht alles anders aus: Dein Weg ist nicht die Studienbewerbung, sondern die **Anerkennung (Approbation als Zahnarzt).** Bürokratisch und geduldfordernd, aber klar und real — und wertvoll, weil Zahnärzte gebraucht werden. Hier die ehrliche Landkarte.

## Für wen dieser Text ist: bereits ausgebildete Zahnärzte
Dieser Leitfaden richtet sich an Menschen, die im Ausland (z. B. in der Türkei) ein **Zahnmedizin-Diplom** erworben haben oder gerade erwerben. Willst du von null in Deutschland studieren, ist dein Weg ein ganz anderer: [Zahnmedizin in Deutschland studieren](/de/blog/studying-dentistry-zahnmedizin-in-germany-as-a-foreigner-de). Bist du schon Zahnarzt, musst du **nicht um einen Studienplatz kämpfen** — du gehst direkt ins Anerkennungsverfahren. Für bereits ausgebildete türkische Zahnärzte ist das **der realistischste Weg.** Hinweis: Für Absolventen eines EU/EWR-Landes ist das Verfahren deutlich kürzer (automatische Anerkennung); ein türkisches Diplom durchläuft eine eigene Gleichwertigkeitsprüfung. Im Fokus dieses Textes steht der Zahnarzt mit **Nicht-EU-Diplom.**

## Approbation vs. Berufserlaubnis
Zwei verschiedene Lizenzen, nicht verwechseln:
- **Approbation:** die **unbefristete, volle** zahnärztliche Lizenz. Du darfst selbstständig arbeiten, eine eigene Praxis eröffnen, überall als Zahnarzt tätig sein. Das ist das eigentliche Ziel.
- **Berufserlaubnis:** eine **befristete, eingeschränkte** Erlaubnis (meist höchstens **~2 Jahre**, an Arbeitgeber/Ort gebunden). Sie ist eine **Brücke**, während du die Anerkennung abschließt, und lässt dich schon in der Praxis/Klinik arbeiten.

## Der Weg: Antrag → Gleichwertigkeitsprüfung → Bescheid
Die Schritte laufen grob so (je nach Bundesland unterschiedlich):
1. **Antrag:** Du reichst bei der **Approbationsbehörde** deines Wunsch-Bundeslandes Diplom, Transcript und Praxisnachweise ein (übersetzt, beglaubigt).
2. **Gleichwertigkeitsprüfung:** Die Behörde **vergleicht** deine Ausbildung mit der deutschen Zahnmedizin. Ein Nicht-EU-Diplom (türkisch) wird **nicht automatisch anerkannt**; geprüft wird, ob es inhaltlich und im Umfang gleichwertig ist.
3. **Bescheid:** Das Ergebnis kommt schriftlich. **Gleichwertig** → nach der Sprachvoraussetzung die Approbation. **Wesentlicher Unterschied** → du musst die **Kenntnisprüfung** ablegen.

## Bei Lücken: die Kenntnisprüfung
Gilt dein Diplom nicht als voll gleichwertig, folgt die **Kenntnisprüfung** — auf dem Niveau des deutschen Staatsexamens, praktisch und mündlich geprägt. Der Vergleich:

| Punkt | Diplom **gleichwertig** | **Wesentlicher Unterschied** |
|---|---|---|
| Zusatzprüfung | Nicht nötig | **Kenntnisprüfung** Pflicht |
| Inhalt | — | Klinisches + theoretisches Fachwissen |
| Sprache | C1 + Fachsprachprüfung | C1 + Fachsprachprüfung |
| Ergebnis | Approbation | Approbation bei Bestehen |

Die Kenntnisprüfung deckt meist **konservierende Zahnheilkunde, Chirurgie, Prothetik und Kinderzahnheilkunde** ab, und die **Wiederholungen sind begrenzt** — nimm sie also ernst.

## C1-Deutsch + Fachsprachprüfung
Die Sprache ist das Rückgrat und zweischichtig:
- **Allgemeines C1-Deutsch:** die meisten Länder verlangen mindestens **C1** (manche akzeptieren B2 + FSP — je nach Bundesland).
- **Fachsprachprüfung (FSP):** Prüfung im **zahnmedizinischen Deutsch**. Sie wird von der regionalen **Zahnärztekammer** abgenommen: Patientengespräch, Kollegen-Kommunikation und Dokumentation/Anamnese. **Ein Goethe-/telc-Zertifikat ersetzt die FSP nicht** — sie ist eine eigene Prüfung.

Vertrauen zum Patienten aufbauen, aufklären und dokumentieren geht nur mit flüssigem Deutsch; investiere daher **früh und ernsthaft** in die Sprache.

## Die Realität des Nicht-EU-Diploms (türkisch)
Klartext: **Ein EU/EWR-Diplom wird automatisch anerkannt, ein türkisches nicht** — es durchläuft immer die Gleichwertigkeitsprüfung. Zwei Ergebnisse sind möglich: direkte Gleichwertigkeit (nur Sprache fehlt) oder Kenntnisprüfung. Das hängt vom individuellen Ausbildungsinhalt und der Bewertung ab; **zwei türkische Zahnärzte können unterschiedliche Bescheide bekommen.** Die Logik ähnelt stark dem Approbationsweg der Ärzte — zum Vergleich: [als ausländischer Arzt in Deutschland arbeiten (Approbation, FSP, KP)](/de/blog/foreign-doctors-germany-approbation-fsp-kenntnispruefung-de).

## Dauer, Bürokratie und Aufenthalt
Die Dauer **variiert stark je nach Bundesland und Aktenlage**; eine Garantie gibt es nicht. Wartezeiten auf FSP-/Kenntnisprüfungs-Termine können **Monate** betragen — bist du flexibel, kann ein schnelleres Bundesland strategisch sein. Während du wartest, kannst du mit der **Berufserlaubnis** schon in der Klinik arbeiten — so verdienst du und verbesserst gleichzeitig dein praktisches Deutsch. **Visum/Aufenthalt** ist ein eigenes Thema: Jobangebot und Anerkennung laufen zusammen; siehe [Arbeitsvisum mit Jobangebot](/de/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-de). **Alle Angaben zu Dauer, Schritten und Gebühren** vor der Bewerbung bei der zuständigen **Approbationsbehörde / Zahnärztekammer** bestätigen.

## Unterlagen, Gebühren und Vorbereitung
Der Antrag umfasst meist: **Diplom und Transcript** (beglaubigte Übersetzung + Notar/Apostille), **Fächer- und Stundennachweis**, **Führungszeugnis**, **ärztliches Attest**, **Ausweis** und ggf. **Nachweise über Berufserfahrung.** Die Gebühren für Anerkennung und Prüfung variieren je nach Bundesland und können beträchtlich sein; plane auch Übersetzungs- und Beglaubigungskosten ein. Die effektivste Vorbereitung ist die Investition in **Sprache + Kenntnisprüfungs-Kurse** (in manchen Ländern gibt es Vorbereitungskurse und Probeprüfungen). Sammle deine Unterlagen früh und vollständig — **eine unvollständige Akte ist der häufigste Grund für monatelange Verzögerungen.**

## Ehrliches Fazit
Bist du schon Zahnarzt, ist die **Anerkennung der realistischste Weg** nach Deutschland — nicht die erneute Studienbewerbung. Der Weg ist bürokratisch, aber geregelt und endet in der vollen **Approbation als Zahnarzt.** Schlüssel: **früh in zahnmedizinisches Deutsch investieren**, die Gleichwertigkeit vorab klären, ein schnelles Bundesland für den Termin wählen. Was nach der Approbation kommt (Pflicht-Assistenzzeit, Gehalt, eigene Praxis): [als Zahnarzt in Deutschland arbeiten](/de/blog/working-as-a-dentist-in-germany-salary-career-and-own-practice-de). Und ob sich der Weg lohnt: [lohnt sich Zahnmedizin?](/de/blog/is-studying-dentistry-in-germany-worth-it-honest-reality-de).

---
*Stand 2026; Gleichwertigkeit, Kenntnisprüfung, FSP und Sprachanforderungen variieren je nach Bundesland und Zahnärztekammer — vor der Bewerbung bei der zuständigen Zahnärztekammer / Approbationsbehörde bestätigen.*
MD;

        $enBody = <<<'MD'
**Studying** dentistry in Germany is close to impossible for an international applicant (NC ~1,0, no English-taught programme). But if you're already a **qualified dentist**, everything changes: your path isn't a student application, it's **recognition (Approbation als Zahnarzt).** Bureaucratic and patience-testing, but clear and real — and valuable, because Germany needs dentists. Here's the honest map.

## Who this is for: dentists who already qualified
This guide is for people who earned (or are about to earn) a **dentistry degree abroad**, e.g. in Turkey. If you want to study from scratch in Germany, your route is entirely different: [studying dentistry in Germany](/en/blog/studying-dentistry-zahnmedizin-in-germany-as-a-foreigner-en). If you're already a dentist, you **don't need to fight for a study place** — you go straight into the recognition process. For Turkish dentists who already qualified, this is **the most realistic path.** Note: for graduates of an EU/EEA country the process is far shorter (automatic recognition); a Turkish diploma goes through its own equivalence check. This article focuses on the dentist holding a **non-EU diploma.**

## Approbation vs. Berufserlaubnis
Two different licences — don't confuse them:
- **Approbation:** the **full, unlimited** dental licence. You can practise independently, open your own practice, work anywhere as a dentist. This is the real goal.
- **Berufserlaubnis:** a **temporary, limited** permit (usually up to **~2 years**, tied to an employer/location). It's a **bridge** while you complete recognition, letting you start working in the practice/clinic sooner.

## The process: application → Gleichwertigkeitsprüfung → Bescheid
The steps run roughly like this (varies by state):
1. **Application:** you submit your diploma, transcript and practice records (translated, certified) to the **Approbationsbehörde** of the state where you want to work.
2. **Gleichwertigkeitsprüfung (equivalence check):** the authority **compares** your training with German dentistry. A non-EU (Turkish) diploma is **not recognised automatically**; it's assessed for equivalence in content and scope.
3. **Bescheid (decision):** the result comes in writing. **If equivalent** → Approbation once the language requirement is met. **If a substantial difference (wesentlicher Unterschied) is found** → you must sit the **Kenntnisprüfung.**

## If there are gaps: the Kenntnisprüfung
If your diploma isn't deemed fully equivalent, the **Kenntnisprüfung** (knowledge exam) applies — at the level of the German state exam, largely practical and oral. The comparison:

| Aspect | Diploma **equivalent** | **Substantial difference** |
|---|---|---|
| Extra exam | Not required | **Kenntnisprüfung** required |
| Content | — | Clinical + theoretical knowledge |
| Language | C1 + Fachsprachprüfung | C1 + Fachsprachprüfung |
| Result | Approbation | Approbation if you pass |

The Kenntnisprüfung usually covers **conservative dentistry, surgery, prosthetics and paediatric dentistry**, and **retakes are limited** — so don't underestimate it.

## C1 German + Fachsprachprüfung
Language is the backbone, and it's two-layered:
- **General C1 German:** most states require at least **C1** (some accept B2 + FSP — it varies by Bundesland).
- **Fachsprachprüfung (FSP):** an exam in **dental German**. It's run by the regional **Zahnärztekammer** (dental chamber): patient interview, peer communication and documentation/history-taking. **A Goethe/telc certificate does not replace the FSP** — it's a separate exam.

Building trust with patients, obtaining informed consent and keeping records all demand fluent German, so invest in the language **early and seriously.**

## The reality of a non-EU (Turkish) diploma
Plain talk: **an EU/EEA diploma is recognised automatically, a Turkish one is not** — it always goes through the Gleichwertigkeitsprüfung. Two outcomes are possible: direct equivalence (only language left) or the Kenntnisprüfung. It depends on your individual training content and the authority's assessment; **two Turkish dentists can get different decisions.** The logic closely mirrors doctors' Approbation route — for comparison: [working as a foreign doctor in Germany (Approbation, FSP, KP)](/en/blog/foreign-doctors-germany-approbation-fsp-kenntnispruefung-en).

## Timeline, bureaucracy and residence
The timeline **varies a lot by state and how fast your file moves**; there's no guarantee. Waiting times for FSP/Kenntnisprüfung slots can run into **months** — if you're flexible, a faster Bundesland can be strategic. While you wait, a **Berufserlaubnis** lets you start working in the clinic — earning an income and sharpening your practical German at the same time. **Visa/residence** is a separate topic: a job offer and recognition move together; see [Germany work visa with a job offer](/en/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-en). **Confirm all timeline, step and fee details** with the relevant **Approbationsbehörde / Zahnärztekammer** before applying.

## Documents, fees and preparation
The application file usually includes: your **diploma and transcript** (certified translation + notary/apostille), a **subject and hours record (Fächer- und Stundennachweis)**, a **police clearance certificate (Führungszeugnis)**, a **medical certificate**, **ID** and, if any, **proof of work experience.** Recognition and exam fees vary by state and can be significant; budget for translation and certification costs too. The most effective preparation is investing in **language + Kenntnisprüfung courses** (some states offer prep courses and mock exams). Gather your documents early and in full — **an incomplete file is the most common reason for months of delay.**

## Bottom line & honest advice
If you're already a dentist, **recognition is the most realistic way** into Germany — not reapplying to university. The path is bureaucratic but rule-based, and it ends in the full **Approbation als Zahnarzt.** The keys: **invest in dental German early**, check equivalence in advance, and pick a fast state for your appointment. For what comes after Approbation (mandatory assistant years, pay, your own practice): [working as a dentist in Germany](/en/blog/working-as-a-dentist-in-germany-salary-career-and-own-practice-en). And whether the whole route is worth it: [is studying dentistry worth it?](/en/blog/is-studying-dentistry-in-germany-worth-it-honest-reality-en).

---
*Based on the general framework in force as of 2026; equivalence, Kenntnisprüfung, FSP and language requirements vary by Bundesland and dental chamber — confirm with the relevant Zahnärztekammer / Approbationsbehörde before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'foreign-dentist-in-germany-approbation-and-recognition',
                'title' => 'Yurtdışı Diş Hekimi Almanya\'da Çalışabilir mi? Approbation ve Tanınma (2026)',
                'excerpt' => 'Zaten diş hekimiysen Almanya\'da çalışmanın en gerçekçi yolu tanınmadır: Approbation vs Berufserlaubnis, başvuru → Gleichwertigkeitsprüfung → Bescheid, eksikse Kenntnisprüfung, C1 Almanca + Fachsprachprüfung ve AB-dışı (Türk) diploma gerçeği.',
                'meta_title' => 'Yurtdışı Diş Hekimi Almanya\'da: Approbation & Tanınma',
                'meta_description' => 'Yurtdışı diş hekimi olarak Almanya\'da çalışma rehberi: Approbation vs Berufserlaubnis, Gleichwertigkeitsprüfung, Kenntnisprüfung, C1 + Fachsprachprüfung ve Türk diploma denkliği.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'foreign-dentist-in-germany-approbation-and-recognition-de',
                'title' => 'Als ausländischer Zahnarzt in Deutschland arbeiten? Approbation & Anerkennung (2026)',
                'excerpt' => 'Bist du schon Zahnarzt, ist die Anerkennung der realistischste Weg nach Deutschland: Approbation vs Berufserlaubnis, Antrag → Gleichwertigkeitsprüfung → Bescheid, bei Lücken Kenntnisprüfung, C1-Deutsch + Fachsprachprüfung und die Realität des Nicht-EU-Diploms.',
                'meta_title' => 'Ausländischer Zahnarzt in Deutschland: Approbation & Anerkennung',
                'meta_description' => 'Leitfaden für ausländische Zahnärzte in Deutschland: Approbation vs Berufserlaubnis, Gleichwertigkeitsprüfung, Kenntnisprüfung, C1 + Fachsprachprüfung und Nicht-EU-Diplom-Anerkennung.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'foreign-dentist-in-germany-approbation-and-recognition-en',
                'title' => 'Can a Foreign Dentist Work in Germany? Approbation & Recognition (2026)',
                'excerpt' => 'If you\'re already a dentist, recognition is the most realistic way into Germany: Approbation vs Berufserlaubnis, application → Gleichwertigkeitsprüfung → Bescheid, Kenntnisprüfung if there are gaps, C1 German + Fachsprachprüfung and the non-EU (Turkish) diploma reality.',
                'meta_title' => 'Foreign Dentist in Germany: Approbation & Recognition (2026)',
                'meta_description' => 'Guide for foreign dentists in Germany: Approbation vs Berufserlaubnis, Gleichwertigkeitsprüfung, Kenntnisprüfung, C1 + Fachsprachprüfung and non-EU (Turkish) diploma recognition.',
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
            $existing ? $existing->update($payload) : Post::create($payload + ['slug' => $v['slug']]);
        }
    }

    public function down(): void
    {
        Post::whereIn('slug', [
            'foreign-dentist-in-germany-approbation-and-recognition',
            'foreign-dentist-in-germany-approbation-and-recognition-de',
            'foreign-dentist-in-germany-approbation-and-recognition-en',
        ])->delete();
    }
};
