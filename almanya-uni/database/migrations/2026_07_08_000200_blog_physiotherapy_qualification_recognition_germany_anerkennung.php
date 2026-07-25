<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Yurtdışı fizyoterapi diplomasının Almanya'da tanınması (Anerkennung) (2026).
 * Doğrulandı: Physiotherapeut düzenlenmiş sağlık mesleği — "Erlaubnis zum Führen der
 * Berufsbezeichnung" için staatliche Anerkennung şart; denklikte fark varsa Kenntnisprüfung
 * veya Anpassungslehrgang; AB-dışı (Türk) diploma denklik değerlendirilir; B2 + bazen
 * Fachsprachprüfung; §16d tanınma vizesi. Süre/adım eyalete göre değişir; resmi
 * anerkennung-in-deutschland.de'den doğrula.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'c3d20000-2222-4faf-9f00-cc0add10aa02';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Türkiye'de (ya da başka bir ülkede) fizyoterapi okuyup diploma aldıysan, Almanya'da sıfırdan 3 yıllık eğitime başlamana gerek yok. Mevcut niteliğini **tanıtma (Anerkennung)** süreciyle geçerli hale getirip tam yetkili **Physiotherapeut** olarak çalışabilirsin. Bu, zaten fizyoterapist olan Türkler için bu kümedeki en gerçekçi yol — ama süreç bürokratik ve dile bağlı. Bu yazıda adım adım, dürüstçe anlatıyorum.

## Kimler için: zaten fizyoterapist olanlar

Almanya'da fizyoterapist unvanıyla çalışmak için **"Erlaubnis zum Führen der Berufsbezeichnung Physiotherapeut/in"** (meslek unvanını kullanma izni) gerekir. Fizyoterapi **düzenlenmiş bir sağlık mesleğidir (reglementierter Beruf)** — diploma tek başına yetmez, devlet tanıması **şarttır**.

Bu yol senin için, eğer:
- Ülkende **tamamlanmış bir fizyoterapi eğitimin/diploman** varsa,
- Almanya'da sıfırdan [Ausbildung veya Bachelor eğitimi](/tr/blog/physiotherapy-training-and-study-in-germany-for-internationals) yapmak yerine mevcut niteliğini kullanmak istiyorsan,
- Ve dil şartını (genelde B2) karşılamaya hazırsan.

Sıfırdan başlayanlarla tanınma yolunu karşılaştıran genel çerçeve için [Almanya'da fizyoterapist olmak: yabancılar için rehber](/tr/blog/becoming-a-physiotherapist-in-germany-as-a-foreigner) yazısına da bakabilirsin. Süreç, hemşirelikteki [diploma tanınması (Anerkennung)](/tr/blog/getting-your-foreign-nursing-qualification-recognized-in-germany-anerkennung) mantığına çok benzer.

## Süreç: başvuru → denklik değerlendirmesi → Bescheid

Süreç kabaca şöyle işler (adımlar ve süre **eyalete göre değişir**, aşağıyı yol haritası olarak oku, resmi kaynaktan doğrula):

1. **Yetkili makamı bul.** Her eyaletin (Bundesland) sağlık meslekleri için kendi **tanıma makamı (Anerkennungsstelle / Landesprüfungsamt)** var. Hangi eyalette çalışmak istiyorsan oranın makamına başvurursun.
2. **Başvuru + belgeler.** Diploma, transkript (ders içerikleri ve saatleri), kimlik, meslek tecrüben, yeminli çeviriler ve genelde apostil/tasdik istenir.
3. **Denklik değerlendirmesi (Gleichwertigkeitsprüfung).** Makam, senin fizyoterapi eğitimini Alman eğitimiyle **içerik ve saat** açısından karşılaştırır.
4. **Karar (Bescheid).** Sonuçta yazılı bir karar alırsın:
   - **Tam denklik (volle Gleichwertigkeit):** doğrudan izni alırsın.
   - **Kısmi denklik (wesentliche Unterschiede):** önemli farklar var → bir **telafi önlemi (Ausgleichsmaßnahme)** ile kapatman gerekir (aşağıdaki tablo).

## Eksik varsa: Kenntnisprüfung mü Anpassungslehrgang mı?

Eğer Bescheid "önemli farklar var" derse, iki telafi yolundan biriyle bunu kapatırsın. Çoğu eyalette **seçim hakkı sende olabilir**, ama detay eyalete/duruma göre değişir:

| Kriter | **Kenntnisprüfung** (bilgi sınavı) | **Anpassungslehrgang** (uyum kursu) |
|---|---|---|
| Ne | Teorik + pratik sınav | Denetimli çalışma/eğitim dönemi |
| Süre | Kısa (hazırlık ayları + sınav) | Genelde birkaç ay – ~1 yıla kadar (fark kadar) |
| Avantaj | Hızlı olabilir | Sınav baskısı yok, iş başında öğrenirsin |
| Dezavantaj | Sınav riski, yoğun hazırlık | Daha uzun sürebilir, bir praxis/klinik bağı gerekir |
| Sonuç | Başarınca tam tanınma | Tamamlayınca tam tanınma |

**Kalın gerçek:** Hangisinin sana uygun olduğu diploma-fark miktarına, Almanca seviyene ve seni alacak bir praxis/klinik bulup bulamamana bağlı. Kesin süreyi sana ancak **kendi Bescheid'ın** söyler — kimse baştan garanti veremez.

## Dil: B2 (+ bazen Fachsprachprüfung)

Tanınma dosyanı ne kadar iyi hazırlarsan hazırla, **dil olmadan çalışamazsın**. Fizyoterapi tamamen iletişime dayalı bir meslek: hasta anamnezi, tedavi anlatımı, egzersiz talimatı, doktor ve dokümantasyon.

- **Genel Almanca:** çoğu eyalet/işveren **B2** ister (bazıları B1'le başlatıp B2'yi çalışırken tamamlatabilir).
- **Fachsprachprüfung (mesleki dil sınavı):** bazı eyaletlerde sağlık meslekleri için **mesleki Almanca sınavı** ayrıca istenebilir — tıbbi/anatomik terminoloji, hasta görüşmesi.

Dil planını erken kur; pratikte İngilizce fizyoterapi işi yok denecek kadar az, dolayısıyla Almanca senin için pazarlık konusu değil, ön koşul.

## AB-dışı (Türk) diploma gerçeği + vize

Türk diploması **AB dışı** sayılır. Bu, sürecin bittiği anlamına gelmez — tam tersine denklik yolu açıktır — ama pratikte iki şeyi bilmekte fayda var:

- **Denklik daha ayrıntılı incelenebilir.** AB içi otomatik tanıma sende geçerli olmaz; makam eğitimini içerik ve saat olarak dikkatle karşılaştırır, bu yüzden bir telafi önlemi (Kenntnisprüfung/Anpassungslehrgang) çıkma ihtimali AB-içi başvuruculara göre daha yüksektir.
- **Belge tasdiki önemli.** Yeminli çeviri, apostil ve bazen ek denklik belgeleri istenir; dosyayı baştan eksiksiz hazırlamak en çok zaman kazandıran şeydir.

**Vize tarafı:** Türkiye'den geliyorsan ve tanınman **Almanya'da** bir telafi önlemi gerektiriyorsa, bunun için tasarlanmış bir oturum türü var: **§16d (Aufenthalt zur Anerkennung ausländischer Berufsqualifikationen)** — telafi önlemini Almanya'da tamamlaman için. Alternatif olarak doğrudan **iş teklifiyle** nitelikli işçi oturumu da mümkün olabilir; Almanya'da **hızlandırılmış nitelikli işçi prosedürü** var. İş teklifiyle vize sürecinin genel mantığı için [iş teklifiyle Almanya çalışma vizesi](/tr/blog/germany-work-visa-with-job-offer-process-timeline-fast-track) yazısına bak.

*Vize/oturum ve tanınma kuralları sık değişir — kesin şartı Alman konsolosluğu, eyaletin tanıma makamı ve anerkennung-in-deutschland.de üzerinden doğrula.*

## Süre & bürokrasi gerçeği

Dürüst olalım: **tanınma bürokrasisi yavaş olabilir.** Belge toplama, yeminli çeviri, apostil, makamın değerlendirmesi ve gerekirse telafi önlemi derken süreç aylara — bazen daha uzuna — yayılabilir. Süre;
- hangi eyalete başvurduğuna,
- belgelerinin ne kadar eksiksiz olduğuna,
- telafi önlemi gerekip gerekmediğine

göre ciddi değişir. Kimse sana "şu kadar haftada biter" diye **garanti veremez** — verilirse şüphelen. En büyük hızlandırıcı, dosyanı baştan eksiksiz ve düzenli hazırlamaktır.

## Sonuç & dürüst tavsiye

Zaten fizyoterapistsen, yurtdışı diploman Almanya'da **değerli** — yaşlanan nüfus yüzünden tanınmış fizyoterapiste talep yüksek ve iş garantisi güçlü. Ama süreç iki şeye bağlı: **temiz bir tanınma dosyası** ve **dil**. Benim tavsiyem:

1. Erkenden **hedef eyaleti** ve onun **tanıma makamını** belirle.
2. **Belgelerini** (transkript, saatler, yeminli çeviriler) baştan derli topla.
3. **Almancaya paralel** başla — B2 (+ bazen mesleki dil) en büyük darboğaz.
4. Telafi önlemi çıkarsa (Kenntnisprüfung/Anpassungslehrgang), Bescheid'ın ne dediğine göre kararını ver.

Çalışma tarafındaki gerçek (maaş, dil, koşullar) için [Almanya'da fizyoterapist olarak çalışmak: maaş, dil ve gerçek](/tr/blog/working-as-a-physiotherapist-in-germany-salary-language-and-reality) yazısına da göz at.

*Bu yazı 2026 başı itibarıyla genel bilgilendirme amaçlıdır; süreç, süre, dil eşikleri ve vize şartları eyalete ve zamana göre değişir. Bağlayıcı bilgi için resmi tanıma makamını (Anerkennungsstelle/Landesprüfungsamt) ve anerkennung-in-deutschland.de kaynaklarını doğrula.*
MD;
        $deBody = <<<'MD'
Wenn du deine Physiotherapie-Ausbildung im Ausland abgeschlossen hast, musst du in Deutschland nicht bei null anfangen. Mit der **Anerkennung** deiner ausländischen Qualifikation kannst du als vollwertige/r **Physiotherapeut/in** arbeiten. Für Menschen, die bereits Physiotherapeut sind, ist das der realistischste Weg – aber er ist bürokratisch und stark sprachabhängig. Hier erkläre ich ihn dir ehrlich, Schritt für Schritt.

## Für wen: wer bereits Physiotherapeut ist

Um in Deutschland unter der Berufsbezeichnung zu arbeiten, brauchst du die **"Erlaubnis zum Führen der Berufsbezeichnung Physiotherapeut/in"**. Physiotherapie ist ein **reglementierter Gesundheitsberuf** – ein Diplom allein reicht nicht, die staatliche Anerkennung ist **Pflicht**.

Dieser Weg ist für dich, wenn du:
- eine **abgeschlossene Physiotherapie-Ausbildung/ein Diplom** aus deinem Heimatland hast,
- deine vorhandene Qualifikation nutzen willst, statt in Deutschland eine neue [Ausbildung oder ein Bachelorstudium](/de/blog/physiotherapy-training-and-study-in-germany-for-internationals-de) zu machen,
- und bereit bist, die Sprachanforderung (meist B2) zu erfüllen.

Einen Überblick über beide Wege findest du im Beitrag [Physiotherapeut in Deutschland werden](/de/blog/becoming-a-physiotherapist-in-germany-as-a-foreigner-de). Der Ablauf ähnelt stark der [Anerkennung ausländischer Pflegeabschlüsse](/de/blog/getting-your-foreign-nursing-qualification-recognized-in-germany-anerkennung-de).

## Der Ablauf: Antrag → Gleichwertigkeitsprüfung → Bescheid

Der Ablauf sieht ungefähr so aus (Schritte und Dauer **hängen vom Bundesland ab** – lies das als Orientierung und prüfe amtlich nach):

1. **Zuständige Stelle finden.** Jedes Bundesland hat für Gesundheitsberufe seine eigene **Anerkennungsstelle / sein Landesprüfungsamt**. Du bewirbst dich dort, wo du arbeiten möchtest.
2. **Antrag + Unterlagen.** Diplom, Transcript (Inhalte/Stunden), Ausweis, Berufserfahrung, beglaubigte Übersetzungen und oft Apostille.
3. **Gleichwertigkeitsprüfung.** Die Stelle vergleicht deine Ausbildung inhaltlich und nach Stundenzahl mit der deutschen Physiotherapie-Ausbildung.
4. **Bescheid.** Du bekommst eine schriftliche Entscheidung:
   - **Volle Gleichwertigkeit:** direkte Erlaubnis.
   - **Wesentliche Unterschiede:** du musst sie mit einer **Ausgleichsmaßnahme** schließen (siehe Tabelle).

## Bei Unterschieden: Kenntnisprüfung oder Anpassungslehrgang?

Wenn der Bescheid "wesentliche Unterschiede" feststellt, schließt du die Lücke über einen von zwei Wegen. In vielen Bundesländern hast du evtl. ein **Wahlrecht**, aber die Details variieren:

| Kriterium | **Kenntnisprüfung** | **Anpassungslehrgang** |
|---|---|---|
| Was | Theoretische + praktische Prüfung | Betreute Praxis-/Lernphase |
| Dauer | Kurz (Vorbereitung + Prüfung) | Meist einige Monate – bis ~1 Jahr |
| Vorteil | Kann schnell gehen | Kein Prüfungsdruck, Lernen in der Praxis |
| Nachteil | Prüfungsrisiko, intensive Vorbereitung | Kann länger dauern, braucht eine Praxis/Klinik |
| Ergebnis | Bei Bestehen volle Anerkennung | Nach Abschluss volle Anerkennung |

**Klartext:** Was zu dir passt, hängt vom Umfang der Unterschiede, deinem Deutschniveau und davon ab, ob du eine Praxis/Klinik findest. Die genaue Dauer sagt dir nur **dein eigener Bescheid** – niemand kann sie vorab garantieren.

## Sprache: B2 (+ ggf. Fachsprachprüfung)

Egal wie gut deine Akte ist – **ohne Sprache kannst du nicht arbeiten**. Physiotherapie ist reine Kommunikation: Anamnese, Behandlungsplan, Übungsanleitung, Ärzte, Dokumentation.

- **Allgemeines Deutsch:** die meisten Bundesländer/Arbeitgeber verlangen **B2** (manche starten mit B1, B2 wird berufsbegleitend nachgeholt).
- **Fachsprachprüfung:** in einigen Bundesländern zusätzlich für Gesundheitsberufe verlangt – medizinische/anatomische Terminologie, Patientengespräch.

Plane die Sprache früh; englischsprachige Physiotherapie-Jobs gibt es praktisch nicht, Deutsch ist also keine Option, sondern Voraussetzung.

## Nicht-EU-Diplom (türkisch) – Realität + Visum

Ein türkisches Diplom gilt als **Nicht-EU-Abschluss**. Das heißt nicht, dass der Weg versperrt ist – im Gegenteil, die Anerkennung ist möglich – aber zwei Dinge solltest du wissen:

- **Die Gleichwertigkeit wird genauer geprüft.** Es gibt keine automatische EU-Anerkennung; die Stelle vergleicht deine Ausbildung sorgfältig, daher ist eine Ausgleichsmaßnahme (Kenntnisprüfung/Anpassungslehrgang) wahrscheinlicher als bei EU-Bewerbern.
- **Beglaubigung zählt.** Beglaubigte Übersetzung, Apostille und teils zusätzliche Nachweise werden verlangt; eine von Anfang an vollständige Akte spart am meisten Zeit.

**Visum:** Wenn du aus dem Ausland kommst und deine Anerkennung eine Ausgleichsmaßnahme **in Deutschland** erfordert, gibt es dafür einen eigenen Aufenthaltstitel: **§16d (Aufenthalt zur Anerkennung ausländischer Berufsqualifikationen)**. Alternativ ist ein Aufenthalt als Fachkraft direkt **mit Jobangebot** möglich; Deutschland hat ein **beschleunigtes Fachkräfteverfahren**. Die Logik des Visums mit Jobangebot erklärt der Beitrag [Arbeitsvisum mit Jobangebot](/de/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-de).

*Visa-/Aufenthalts- und Anerkennungsregeln ändern sich häufig – prüfe die genauen Bedingungen beim deutschen Konsulat, bei der zuständigen Anerkennungsstelle und unter anerkennung-in-deutschland.de.*

## Dauer & Bürokratie – ehrlich

Ehrlich: **die Anerkennungsbürokratie kann langsam sein.** Unterlagen sammeln, beglaubigte Übersetzungen, Apostille, die Prüfung der Stelle und ggf. die Ausgleichsmaßnahme – das kann sich über Monate ziehen. Die Dauer hängt ab von:
- welchem Bundesland du beantragst,
- wie vollständig deine Unterlagen sind,
- ob eine Ausgleichsmaßnahme nötig ist.

Niemand kann dir eine feste Frist **garantieren** – sei skeptisch, wenn es doch jemand tut. Der größte Beschleuniger ist eine von Anfang an vollständige, saubere Akte.

## Fazit & ehrlicher Rat

Wenn du bereits Physiotherapeut bist, ist dein ausländisches Diplom in Deutschland **wertvoll** – wegen der alternden Bevölkerung ist die Nachfrage hoch und die Jobsicherheit stark. Der Erfolg hängt an zwei Dingen: einer **sauberen Anerkennungsakte** und der **Sprache**. Mein Rat:

1. Lege früh dein **Ziel-Bundesland** und dessen **Anerkennungsstelle** fest.
2. Sammle deine **Unterlagen** (Transcript, Stunden, Übersetzungen) von Anfang an ordentlich.
3. Starte **parallel mit Deutsch** – B2 (+ ggf. Fachsprache) ist der größte Engpass.
4. Kommt eine Ausgleichsmaßnahme, entscheide nach deinem Bescheid.

Die Realität auf der Arbeitsseite (Gehalt, Sprache, Bedingungen) findest du im Beitrag [als Physiotherapeut in Deutschland arbeiten](/de/blog/working-as-a-physiotherapist-in-germany-salary-language-and-reality-de).

*Dieser Beitrag ist eine allgemeine Information mit Stand Anfang 2026; Ablauf, Dauer, Sprachschwellen und Visabedingungen ändern sich je nach Bundesland und Zeitpunkt. Verbindliche Auskünfte findest du bei der zuständigen Anerkennungsstelle und unter anerkennung-in-deutschland.de.*
MD;
        $enBody = <<<'MD'
If you finished your physiotherapy education abroad, you don't have to start over with three years of training in Germany. Through the **recognition process (Anerkennung)** you can have your existing qualification validated and work as a fully licensed **Physiotherapeut** (physiotherapist). For people who are already physiotherapists, this is the most realistic route in this cluster – but it is bureaucratic and heavily language-dependent. Here is the honest, step-by-step version.

## Who it's for: people who are already physiotherapists

To work under the professional title in Germany you need the **"Erlaubnis zum Führen der Berufsbezeichnung Physiotherapeut/in"** (permission to use the job title). Physiotherapy is a **regulated health profession (reglementierter Beruf)** – a diploma alone is not enough; state recognition is **mandatory**.

This route is for you if you:
- have a **completed physiotherapy education/diploma** from your home country,
- want to use your existing qualification instead of doing a fresh [Ausbildung or Bachelor's](/en/blog/physiotherapy-training-and-study-in-germany-for-internationals-en) in Germany,
- and are ready to meet the language requirement (usually B2).

For an overview of both routes, see [becoming a physiotherapist in Germany as a foreigner](/en/blog/becoming-a-physiotherapist-in-germany-as-a-foreigner-en). The process closely mirrors the [recognition of foreign nursing qualifications](/en/blog/getting-your-foreign-nursing-qualification-recognized-in-germany-anerkennung-en).

## The process: apply → equivalence assessment → Bescheid

The process roughly works like this (steps and timeline **depend on the federal state** – read this as a roadmap and verify officially):

1. **Find the competent authority.** Each federal state (Bundesland) has its own **recognition authority (Anerkennungsstelle / Landesprüfungsamt)** for health professions. You apply in the state where you want to work.
2. **Application + documents.** Diploma, transcript (content/hours), ID, work experience, certified translations and often an apostille.
3. **Equivalence assessment (Gleichwertigkeitsprüfung).** The authority compares your training with German physiotherapy training by **content and hours**.
4. **Decision (Bescheid).** You receive a written decision:
   - **Full equivalence:** direct permission.
   - **Substantial differences:** you must close them with a **compensation measure (Ausgleichsmaßnahme)** (see table).

## If there are gaps: Kenntnisprüfung vs Anpassungslehrgang

If the Bescheid finds "substantial differences", you close the gap via one of two paths. In many states you may have a **choice**, but details vary:

| Criterion | **Kenntnisprüfung** (knowledge exam) | **Anpassungslehrgang** (adaptation course) |
|---|---|---|
| What | Theoretical + practical exam | Supervised practice/learning phase |
| Duration | Short (prep + exam) | Usually a few months – up to ~1 year |
| Upside | Can be fast | No exam pressure, learn on the job |
| Downside | Exam risk, intense prep | Can take longer, needs a practice/clinic |
| Result | Full recognition on passing | Full recognition on completion |

**Blunt fact:** what suits you depends on the size of the differences, your German level, and whether you can secure a practice/clinic. Only **your own Bescheid** tells you the exact requirement – nobody can guarantee it upfront.

## Language: B2 (+ sometimes Fachsprachprüfung)

No matter how strong your file is, **without language you cannot work**. Physiotherapy is all communication: taking a history, explaining treatment, guiding exercises, doctors, documentation.

- **General German:** most states/employers require **B2** (some start at B1 and let you reach B2 while working).
- **Fachsprachprüfung (professional language exam):** in some states an additional requirement for health professions – medical/anatomical terminology, patient interviews.

Plan language early; English-only physiotherapy jobs barely exist, so German isn't optional – it's a precondition.

## Non-EU (Turkish) diploma reality + visa

A Turkish diploma counts as a **non-EU qualification**. That doesn't mean the door is closed – recognition is very much possible – but two things are worth knowing:

- **Equivalence is examined more closely.** There is no automatic EU recognition; the authority compares your training carefully, so a compensation measure (Kenntnisprüfung/Anpassungslehrgang) is more likely than for EU applicants.
- **Certification matters.** Certified translation, apostille and sometimes extra documents are required; a complete file from the start saves the most time.

**Visa side:** If you come from abroad and your recognition requires a compensation measure **in Germany**, there is a dedicated residence permit: **§16d (residence for the recognition of foreign professional qualifications)**. Alternatively, a skilled-worker residence directly **with a job offer** may be possible; Germany has an **accelerated skilled-worker procedure**. For the logic of the job-offer visa, see [Germany work visa with a job offer](/en/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-en).

*Visa/residence and recognition rules change often – verify the exact conditions with the German consulate, the competent Anerkennungsstelle and anerkennung-in-deutschland.de.*

## Duration & bureaucracy – honestly

Let's be honest: **recognition bureaucracy can be slow.** Gathering documents, certified translations, apostilles, the authority's assessment and, if needed, the compensation measure can stretch over months – sometimes longer. The duration depends on:
- which state you apply in,
- how complete your documents are,
- whether a compensation measure is needed.

Nobody can **guarantee** you a fixed timeline – be skeptical if someone does. The biggest accelerator is a complete, clean file from the start.

## Conclusion & honest advice

If you are already a physiotherapist, your foreign diploma is **valuable** in Germany – because of the ageing population, demand is high and job security is strong. Success rests on two things: a **clean recognition file** and **language**. My advice:

1. Decide early on your **target state** and its **Anerkennungsstelle**.
2. Gather your **documents** (transcript, hours, translations) neatly from the start.
3. Start **German in parallel** – B2 (+ sometimes professional language) is the biggest bottleneck.
4. If a compensation measure is required, decide based on what your Bescheid says.

For the reality on the working side (salary, language, conditions), see [working as a physiotherapist in Germany: salary, language and reality](/en/blog/working-as-a-physiotherapist-in-germany-salary-language-and-reality-en).

*This post is general information as of early 2026; the process, timelines, language thresholds and visa conditions vary by federal state and over time. For binding information, verify with the competent recognition authority (Anerkennungsstelle/Landesprüfungsamt) and anerkennung-in-deutschland.de.*
MD;

        $variants = [
            'tr' => ['slug'=>'getting-your-foreign-physiotherapy-qualification-recognized-in-germany-anerkennung',    'title'=>'Yurtdışı Fizyoterapi Diplomanı Almanya\'da Tanıtmak: Anerkennung (2026)', 'excerpt'=>'Yurtdışı fizyoterapi diploman Almanya\'da nasıl tanınır? Başvuru, denklik değerlendirmesi, Bescheid, Kenntnisprüfung vs Anpassungslehrgang, B2/Fachsprachprüfung, AB-dışı (Türk) diploma gerçeği ve §16d tanınma vizesi — adım adım, dürüst rehber.', 'meta_title'=>'Almanya Fizyoterapi Diploma Tanınması (Anerkennung) 2026', 'meta_description'=>'Yurtdışı fizyoterapi diplomanı Almanya\'da tanıtma rehberi: denklik, Bescheid, Kenntnisprüfung vs Anpassungslehrgang, B2, Türk diploması gerçeği ve §16d vizesi.', 'body'=>$trBody],
            'de' => ['slug'=>'getting-your-foreign-physiotherapy-qualification-recognized-in-germany-anerkennung-de', 'title'=>'Anerkennung ausländischer Physiotherapie-Abschlüsse in Deutschland (2026)', 'excerpt'=>'Wie wird dein ausländischer Physiotherapie-Abschluss in Deutschland anerkannt? Antrag, Gleichwertigkeitsprüfung, Bescheid, Kenntnisprüfung vs Anpassungslehrgang, B2/Fachsprachprüfung, Nicht-EU-Diplom und §16d – ehrlich und Schritt für Schritt.', 'meta_title'=>'Anerkennung ausländischer Physiotherapie-Abschlüsse 2026', 'meta_description'=>'Leitfaden zur Anerkennung ausländischer Physiotherapie-Abschlüsse: Gleichwertigkeit, Bescheid, Kenntnisprüfung vs Anpassungslehrgang, B2 und §16d-Visum.', 'body'=>$deBody],
            'en' => ['slug'=>'getting-your-foreign-physiotherapy-qualification-recognized-in-germany-anerkennung-en', 'title'=>'Getting Your Foreign Physiotherapy Qualification Recognized in Germany: Anerkennung (2026)', 'excerpt'=>'How is a foreign physiotherapy qualification recognized in Germany? Application, equivalence assessment, Bescheid, Kenntnisprüfung vs Anpassungslehrgang, B2/Fachsprachprüfung, the non-EU (Turkish) diploma reality and the §16d recognition visa — an honest, step-by-step guide.', 'meta_title'=>'Foreign Physiotherapy Qualification Recognition Germany 2026', 'meta_description'=>'Guide to getting a foreign physiotherapy qualification recognized in Germany: equivalence, Bescheid, Kenntnisprüfung vs Anpassungslehrgang, B2 and the §16d visa.', 'body'=>$enBody],
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
        Post::whereIn('slug', [
            'getting-your-foreign-physiotherapy-qualification-recognized-in-germany-anerkennung',
            'getting-your-foreign-physiotherapy-qualification-recognized-in-germany-anerkennung-de',
            'getting-your-foreign-physiotherapy-qualification-recognized-in-germany-anerkennung-en',
        ])->delete();
    }
};
