<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Yurtdışı sosyal hizmet/sosyal pedagoji diplomasının Almanya'da tanınması (Anerkennung) (2026).
 * Doğrulandı: Soziale Arbeit düzenlenmiş bir unvan (Staatlich anerkannte:r Sozialarbeiter:in) — tanınma
 * eyaletin (Bundesland) yetkili makamı üzerinden yürür; denklikte önemli fark varsa Ausgleichsmaßnahme
 * (Anpassungslehrgang / Eignungsprüfung) ile kapatılır; alan dil-yoğun → genelde C1 Almanca beklenir;
 * yurtdışı akademik diploma tanınması için §16d oturumu bir seçenek olabilir. Süre/adım/terminoloji
 * eyalete göre değişir; resmi anerkennung-in-deutschland.de ve eyalet makamından doğrula.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'c9d20000-2222-4a5f-9f60-cc10dd16aa02';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Türkiye'de (ya da başka bir ülkede) **sosyal hizmet, sosyal pedagoji ya da sosyal çalışma** okuyup diploma aldıysan, Almanya'da sıfırdan üniversiteye başlamana gerek yok. Mevcut niteliğini **tanıtma (Anerkennung)** süreciyle Almanya'da geçerli hale getirip, düzenlenmiş **Staatlich anerkannte:r Sozialarbeiter:in** unvanına giden yolu açabilirsin. Cazip bir yol — ama bürokratik ve tamamen dile bağlı. Bu yazıda adım adım, dürüstçe anlatıyorum.

## Kimler için: zaten sosyal hizmet/pedagoji okuyanlar

Bu yazı **sıfırdan okuyacaklar için değil**. Eğer Almanya'da baştan bir program düşünüyorsan, [Almanya'da sosyal hizmet (Soziale Arbeit) okumak](/tr/blog/studying-social-work-soziale-arbeit-in-germany-as-a-foreigner) yazısı senin başlangıç noktan.

Bu tanınma yolu senin için, eğer:
- Ülkende **tamamlanmış bir sosyal hizmet / sosyal pedagoji lisansın (diploman)** varsa,
- Almanya'da yeniden 3-4 yıl okumak yerine **mevcut niteliğini kullanmak** istiyorsan,
- Ve alanın en zor eşiğini — **güçlü Almanca** — karşılamaya hazırsan.

**Kalın gerçek:** Almanya'da "Soziale Arbeit" tek başına diplomayla değil, çoğu kamu/refah işinde beklenen **staatliche Anerkennung** (devlet tanınması) ile tam yetkili hale gelir. Yani hedef sadece diplomanın denkliği değil, çalışabildiğin **korunmuş unvandır**.

## Süreç: başvuru → denklik değerlendirmesi → Bescheid

Süreç kabaca şöyle işler (adımlar, terminoloji ve süre **eyalete göre değişir** — aşağıyı yol haritası olarak oku, resmi kaynaktan doğrula):

1. **Yetkili makamı bul.** Sosyal hizmet düzenlemesi eyalet (Bundesland) bazındadır; her eyaletin kendi **tanıma/Anerkennung makamı** vardır. Hangi eyalette çalışmak istiyorsan oraya başvurursun.
2. **Başvuru + belgeler.** Diploma, transkript (ders içerikleri/saatleri/staj), kimlik, varsa iş tecrüben, **yeminli çeviriler** ve genelde apostil/tasdik istenir.
3. **Denklik değerlendirmesi.** Makam, senin eğitimini Alman **Soziale Arbeit** lisansıyla (çoğu FH/HAW'da uygulamalı, sık sık **Anerkennungsjahr/pratik** dahil) **içerik ve saat** açısından karşılaştırır.
4. **Karar (Bescheid).** Yazılı bir karar alırsın:
   - **Tam denklik:** unvana doğrudan yaklaşırsın (kalan koşullar dil ve varsa pratik olabilir).
   - **Önemli farklar (wesentliche Unterschiede):** bir **telafi önlemi (Ausgleichsmaßnahme)** ile kapatman gerekir (aşağıdaki tablo).

Bu mantık, komşu düzenlenmiş meslek olan hemşirelikle çok benzer; kıyas için [yurtdışı hemşirelik diplomanı tanıtmak (Anerkennung)](/tr/blog/getting-your-foreign-nursing-qualification-recognized-in-germany-anerkennung) yazısı da işine yarar.

## Eksik varsa: Anpassungslehrgang mı Eignungsprüfung mi?

Bescheid "önemli farklar var" derse, farkı iki telafi yolundan biriyle kapatırsın. Hangisinin sunulacağı ve seçim hakkının olup olmadığı **eyalete/duruma göre değişir**:

| Kriter | **Anpassungslehrgang** (uyum dönemi) | **Eignungsprüfung** (yeterlik sınavı) |
|---|---|---|
| Ne | Denetimli çalışma/eğitim dönemi | Farkı ölçen teorik/pratik sınav |
| Süre | Genelde birkaç ay – ~1 yıla kadar (fark kadar) | Kısa (hazırlık ayları + sınav) |
| Avantaj | İş başında öğrenir, uygulama edinirsin | Hızlı olabilir |
| Dezavantaj | Uzayabilir; kurum/işveren bağı gerekir | Sınav riski, yoğun hazırlık |
| Sonuç | Tamamlayınca tanınmaya yaklaşırsın | Başarınca tanınmaya yaklaşırsın |

**Kalın gerçek:** Hangisinin sana uygun olduğu diploma-fark miktarına, Almanca seviyene ve bir kurum/işveren bulup bulamamana bağlı. Kesin süreyi ve önlemi sana ancak **kendi Bescheid'ın** söyler — genel forum bilgisi değil.

## C1 Almanca: pazarlık payı en az olan şart

Dosyanı ne kadar iyi hazırlarsan hazırla, **dil olmadan bu meslek yürümez**. Sosyal hizmet **danışan işidir**: aile görüşmesi, kriz, kurum yazışması, mahkeme/Jugendamt raporu ve hepsinin temeli olan **Alman sosyal hukuku (SGB)**.

- Alan **dil-yoğun**; birçok işveren ve tanıma yolu pratikte **C1** seviyesinde Almanca bekler (bazı yerler B2 ile başlatıp C1'i çalışırken tamamlatabilir — **doğrula**).
- İngilizce ilerlemek burada **gerçekçi değil**: İngilizce sosyal hizmet programı Almanya'da nadir, danışan ve kurum işi zaten Almanca.

Dil planını erken kur; bu, tüm sürecin **en büyük darboğazı**.

## AB-dışı (Türk) diploma gerçeği + vize

Türk diploması **AB-dışı** sayılır; bu, tanınmanın imkânsız olduğu anlamına gelmez ama **değerlendirme genelde daha ayrıntılıdır** ve telafi önlemi çıkma ihtimali AB içi diplomalara göre daha yüksektir. Buna baştan hazırlıklı ol.

Vize/oturum tarafında:
- Tanınman **Almanya'da** bir telafi önlemi (Anpassungslehrgang/Eignungsprüfung) gerektiriyorsa, yurtdışı mesleki niteliklerin tanınması için özel bir oturum türü olan **§16d** bir seçenek olabilir.
- Elinde **iş teklifi** varsa, nitelikli işçi oturumu ve **hızlandırılmış nitelikli işçi prosedürü** yolu da mümkün olabilir. Bu mantık için [iş teklifiyle Almanya çalışma vizesi](/tr/blog/germany-work-visa-with-job-offer-process-timeline-fast-track) yazısına bak.

*Vize/oturum kuralları sık değişir — kesin şartı Alman konsolosluğu ve resmi eyalet tanıma makamı üzerinden doğrula.*

## Süre & bürokrasi gerçeği (hedge'li)

Dürüst olalım: **tanınma bürokrasisi yavaş olabilir.** Belge toplama, yeminli çeviri, apostil, makamın değerlendirmesi ve gerekiyorsa telafi önlemi derken süreç aylara — bazen daha uzuna — yayılabilir. Süre;
- hangi eyalete başvurduğuna,
- belgelerinin ne kadar eksiksiz olduğuna,
- telafi önlemi gerekip gerekmediğine,
- Almancanı ne kadar hızlı yükselttiğine

göre ciddi değişir. Kimse sana "şu kadar haftada biter" diye **garanti veremez** — verilirse şüphelen. Baştan eksiksiz ve düzenli bir dosya, en çok zaman kazandıran şeydir.

## Sonuç & dürüst tavsiye

Yurtdışı sosyal hizmet diploman Almanya'da **değerli** — alan yüksek talepli (Fachkräftemangel) ve stabil. Ama başarı iki şeye bağlı: **temiz bir tanınma dosyası** ve **C1 Almanca**. Benim tavsiyem:

1. Erkenden **hedef eyaleti** ve onun **tanıma makamını** belirle.
2. **Belgelerini** (transkript, saatler, staj, yeminli çeviriler) baştan derli topla.
3. **C1 Almancaya paralel** başla — bu, alanın en büyük darboğazı.
4. Telafi önlemi çıkarsa (Anpassungslehrgang/Eignungsprüfung), **Bescheid'ın ne dediğine** göre karar ver, foruma göre değil.

Tanınma bittiğinde çalışma tarafındaki gerçeği (alanlar, TVöD-SuE maaşı, koşullar) [Almanya'da sosyal hizmet uzmanı olarak çalışmak](/tr/blog/working-as-a-social-worker-in-germany-fields-salary-and-reality) yazısında bulabilirsin. Bütün bu yolun sana değip değmeyeceğini tartan dürüst değerlendirme içinse [Almanya'da sosyal hizmet okumaya değer mi?](/tr/blog/is-studying-social-work-in-germany-worth-it-honest-reality) yazısına bak.

*Bu yazı 2026 başı itibarıyla genel bilgilendirme amaçlıdır; süreç, süre, terminoloji, dil eşikleri ve vize şartları eyalete ve zamana göre değişir. Bağlayıcı bilgi için resmi eyalet tanıma makamı ve anerkennung-in-deutschland.de kaynaklarını doğrula.*
MD;

        $deBody = <<<'MD'
Wenn du **Soziale Arbeit, Sozialpädagogik oder ein verwandtes Fach** im Ausland studiert und abgeschlossen hast, musst du in Deutschland nicht bei null anfangen. Über die **Anerkennung** deiner ausländischen Qualifikation kannst du den Weg zur geschützten Berufsbezeichnung **Staatlich anerkannte:r Sozialarbeiter:in** öffnen. Ein attraktiver Weg – aber bürokratisch und stark sprachabhängig. Hier erkläre ich ihn dir ehrlich, Schritt für Schritt.

## Für wen: wer bereits Soziale Arbeit/Sozialpädagogik studiert hat

Dieser Beitrag ist **nicht** für Neuanfänger. Wenn du in Deutschland von vorne studieren willst, ist der Beitrag [Soziale Arbeit in Deutschland studieren](/de/blog/studying-social-work-soziale-arbeit-in-germany-as-a-foreigner-de) dein Startpunkt.

Dieser Anerkennungsweg ist für dich, wenn du:
- ein **abgeschlossenes Studium in Sozialer Arbeit / Sozialpädagogik** aus deinem Heimatland hast,
- deine **vorhandene Qualifikation nutzen** willst, statt 3–4 Jahre neu zu studieren,
- und bereit bist, die größte Hürde des Felds zu nehmen: **starkes Deutsch**.

**Klartext:** In Deutschland macht dich nicht das Diplom allein, sondern die **staatliche Anerkennung** in den meisten öffentlichen/wohlfahrtlichen Stellen voll einsatzfähig. Ziel ist also nicht nur die Gleichwertigkeit deines Abschlusses, sondern die **geschützte Bezeichnung**, mit der du arbeiten darfst.

## Der Ablauf: Antrag → Gleichwertigkeitsprüfung → Bescheid

Der Ablauf sieht ungefähr so aus (Schritte, Terminologie und Dauer **hängen vom Bundesland ab** – lies das als Orientierung und prüfe amtlich nach):

1. **Zuständige Stelle finden.** Die Regelung der Sozialen Arbeit liegt bei den Bundesländern; jedes Land hat seine eigene **Anerkennungsstelle**. Du bewirbst dich dort, wo du arbeiten möchtest.
2. **Antrag + Unterlagen.** Diplom, Transcript (Inhalte/Stunden/Praktika), Ausweis, ggf. Berufserfahrung, **beglaubigte Übersetzungen** und oft Apostille.
3. **Gleichwertigkeitsprüfung.** Die Stelle vergleicht dein Studium inhaltlich und nach Stunden mit dem deutschen **Soziale-Arbeit**-Studium (meist an FH/HAW, praxisnah, oft mit **Anerkennungsjahr/Praxis**).
4. **Bescheid.** Du bekommst eine schriftliche Entscheidung:
   - **Volle Gleichwertigkeit:** du kommst der Bezeichnung direkt nahe (offen bleiben ggf. Sprache und Praxis).
   - **Wesentliche Unterschiede:** du musst sie mit einer **Ausgleichsmaßnahme** schließen (siehe Tabelle).

Diese Logik ähnelt stark dem verwandten reglementierten Beruf Pflege; zum Vergleich hilft der Beitrag [ausländischen Pflegeabschluss anerkennen lassen (Anerkennung)](/de/blog/getting-your-foreign-nursing-qualification-recognized-in-germany-anerkennung-de).

## Bei Unterschieden: Anpassungslehrgang oder Eignungsprüfung?

Wenn der Bescheid "wesentliche Unterschiede" feststellt, schließt du die Lücke über einen von zwei Wegen. Welcher angeboten wird und ob du ein Wahlrecht hast, **variiert je nach Bundesland**:

| Kriterium | **Anpassungslehrgang** (Anpassungszeit) | **Eignungsprüfung** |
|---|---|---|
| Was | Betreute Praxis-/Lernphase | Theoretische/praktische Prüfung der Unterschiede |
| Dauer | Meist einige Monate – bis ~1 Jahr | Kurz (Vorbereitung + Prüfung) |
| Vorteil | Lernen in der Praxis, Erfahrung | Kann schnell gehen |
| Nachteil | Kann länger dauern, braucht Einrichtung/Arbeitgeber | Prüfungsrisiko, intensive Vorbereitung |
| Ergebnis | Bringt dich der Anerkennung näher | Bei Bestehen näher an der Anerkennung |

**Klartext:** Was zu dir passt, hängt vom Umfang der Unterschiede, deinem Deutschniveau und davon ab, ob du eine Einrichtung findest. Die genaue Dauer und Maßnahme sagt dir nur **dein eigener Bescheid** – nicht ein Forum.

## C1 Deutsch: die Anforderung mit dem geringsten Spielraum

Egal wie gut deine Akte ist – **ohne Sprache läuft dieser Beruf nicht**. Soziale Arbeit ist **Arbeit mit Menschen**: Familiengespräch, Krise, Behördenschriftverkehr, Bericht ans Jugendamt und als Basis das deutsche **Sozialrecht (SGB)**.

- Das Feld ist **sprachintensiv**; viele Arbeitgeber und Anerkennungswege erwarten praktisch **C1** Deutsch (manche starten mit B2 und lassen C1 berufsbegleitend nachholen – **prüfe das nach**).
- Auf Englisch voranzukommen ist hier **unrealistisch**: englischsprachige Studiengänge der Sozialen Arbeit sind selten, und die Arbeit mit Klienten und Ämtern läuft ohnehin auf Deutsch.

Plane die Sprache früh – sie ist der **größte Engpass** des gesamten Wegs.

## Nicht-EU-Diplom (Türkei) + Visum

Ein türkisches Diplom gilt als **Nicht-EU-Abschluss**; das macht die Anerkennung nicht unmöglich, aber die **Prüfung ist oft detaillierter** und die Wahrscheinlichkeit einer Ausgleichsmaßnahme höher als bei EU-Abschlüssen. Sei darauf von Anfang an vorbereitet.

Beim Visum/Aufenthalt:
- Wenn deine Anerkennung eine Ausgleichsmaßnahme **in Deutschland** (Anpassungslehrgang/Eignungsprüfung) erfordert, kann **§16d** – der Aufenthalt zur Anerkennung ausländischer Berufsqualifikationen – eine Option sein.
- Mit einem **Jobangebot** kann auch ein Fachkräfteaufenthalt und das **beschleunigte Fachkräfteverfahren** infrage kommen. Die Logik erklärt der Beitrag [Arbeitsvisum mit Jobangebot](/de/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-de).

*Visa-/Aufenthaltsregeln ändern sich häufig – prüfe die genauen Bedingungen beim deutschen Konsulat und bei der zuständigen Anerkennungsstelle des Bundeslandes.*

## Dauer & Bürokratie – ehrlich (mit Vorbehalt)

Ehrlich: **die Anerkennungsbürokratie kann langsam sein.** Unterlagen sammeln, beglaubigte Übersetzungen, Apostille, die Prüfung der Stelle und ggf. die Ausgleichsmaßnahme – das kann sich über Monate ziehen, manchmal länger. Die Dauer hängt ab von:
- welchem Bundesland du beantragst,
- wie vollständig deine Unterlagen sind,
- ob eine Ausgleichsmaßnahme nötig ist,
- wie schnell du dein Deutsch hochziehst.

Niemand kann dir eine feste Frist **garantieren** – sei skeptisch, wenn es doch jemand tut. Eine von Anfang an vollständige, saubere Akte spart am meisten Zeit.

## Fazit & ehrlicher Rat

Dein ausländischer Abschluss in Sozialer Arbeit ist in Deutschland **wertvoll** – das Feld ist gefragt (Fachkräftemangel) und stabil. Der Erfolg hängt an zwei Dingen: einer **sauberen Anerkennungsakte** und **C1-Deutsch**. Mein Rat:

1. Lege früh dein **Ziel-Bundesland** und dessen **Anerkennungsstelle** fest.
2. Sammle deine **Unterlagen** (Transcript, Stunden, Praktika, Übersetzungen) von Anfang an ordentlich.
3. Starte **parallel mit C1-Deutsch** – der größte Engpass des Felds.
4. Kommt eine Ausgleichsmaßnahme, entscheide nach deinem **Bescheid**, nicht nach dem Forum.

Die Realität auf der Arbeitsseite (Felder, TVöD-SuE-Gehalt, Bedingungen) findest du im Beitrag [als Sozialarbeiter:in in Deutschland arbeiten](/de/blog/working-as-a-social-worker-in-germany-fields-salary-and-reality-de). Ob sich der ganze Weg lohnt, wägt der Beitrag [lohnt sich ein Studium der Sozialen Arbeit in Deutschland?](/de/blog/is-studying-social-work-in-germany-worth-it-honest-reality-de) ab.

*Dieser Beitrag ist eine allgemeine Information mit Stand Anfang 2026; Ablauf, Dauer, Terminologie, Sprachschwellen und Visabedingungen ändern sich je nach Bundesland und Zeitpunkt. Verbindliche Auskünfte findest du bei der zuständigen Anerkennungsstelle des Bundeslandes und unter anerkennung-in-deutschland.de.*
MD;

        $enBody = <<<'MD'
If you studied and completed a degree in **social work, social pedagogy or a related field** abroad, you don't have to start university again in Germany. Through the **recognition process (Anerkennung)** you can open the path to the protected professional title **Staatlich anerkannte:r Sozialarbeiter:in** (state-recognized social worker). It's an attractive route – but bureaucratic and heavily language-dependent. Here is the honest, step-by-step version.

## Who it's for: people who already studied social work/pedagogy

This post is **not** for beginners. If you want to study from scratch in Germany, the post [studying social work (Soziale Arbeit) in Germany](/en/blog/studying-social-work-soziale-arbeit-in-germany-as-a-foreigner-en) is your starting point.

This recognition route is for you if you:
- have a **completed degree in social work / social pedagogy** from your home country,
- want to **use your existing qualification** instead of studying 3–4 years again,
- and are ready to clear the field's hardest hurdle: **strong German**.

**Blunt fact:** in Germany it's not the diploma alone but the **state recognition (staatliche Anerkennung)** that makes you fully employable in most public and welfare-sector roles. So the goal isn't only the equivalence of your degree, but the **protected title** you're allowed to work under.

## The process: application → equivalence assessment → Bescheid

The process roughly works like this (steps, terminology and timeline **depend on the federal state** – read this as a roadmap and verify officially):

1. **Find the competent authority.** Social work is regulated at the state (Bundesland) level; each state has its own **recognition authority (Anerkennungsstelle)**. You apply in the state where you want to work.
2. **Application + documents.** Diploma, transcript (content/hours/internships), ID, any work experience, **certified translations** and often an apostille.
3. **Equivalence assessment.** The authority compares your studies by **content and hours** with the German **Soziale Arbeit** degree (mostly at FH/HAW, practice-oriented, often including an **Anerkennungsjahr/practical phase**).
4. **Decision (Bescheid).** You receive a written decision:
   - **Full equivalence:** you're close to the title directly (language and practice may still be open).
   - **Substantial differences (wesentliche Unterschiede):** you must close them with a **compensation measure (Ausgleichsmaßnahme)** (see table).

This logic closely resembles the neighboring regulated profession of nursing; for comparison, see [getting your foreign nursing qualification recognized (Anerkennung)](/en/blog/getting-your-foreign-nursing-qualification-recognized-in-germany-anerkennung-en).

## If there are gaps: Anpassungslehrgang vs Eignungsprüfung

If the Bescheid finds "substantial differences", you close the gap via one of two paths. Which is offered, and whether you get a choice, **varies by state**:

| Criterion | **Anpassungslehrgang** (adaptation period) | **Eignungsprüfung** (aptitude test) |
|---|---|---|
| What | Supervised practice/learning phase | Theoretical/practical exam on the differences |
| Duration | Usually a few months – up to ~1 year | Short (prep + exam) |
| Upside | Learn on the job, gain experience | Can be fast |
| Downside | Can take longer, needs an employer/facility | Exam risk, intense prep |
| Result | Brings you closer to recognition | Closer to recognition on passing |

**Blunt fact:** what suits you depends on the size of the differences, your German level, and whether you can secure a facility/employer. Only **your own Bescheid** tells you the exact measure and timeline – not a forum.

## C1 German: the requirement with the least wiggle room

No matter how strong your file is, **this profession does not work without language**. Social work is **work with people**: family meetings, crises, correspondence with authorities, reports to the Jugendamt, and, underlying all of it, German **social law (SGB)**.

- The field is **language-intensive**; many employers and recognition routes effectively expect **C1** German (some start at B2 and let you reach C1 while working – **verify this**).
- Progressing in English is **unrealistic** here: English-taught social work programs are rare in Germany, and client and agency work runs in German anyway.

Plan language early – it's the **biggest bottleneck** of the whole route.

## Non-EU (Turkish) diploma reality + visa

A Turkish diploma counts as a **non-EU qualification**; that doesn't make recognition impossible, but the **assessment is often more detailed** and a compensation measure is more likely than with EU degrees. Be prepared for that from the start.

On the visa/residence side:
- If your recognition requires a compensation measure **in Germany** (Anpassungslehrgang/Eignungsprüfung), **§16d** – the residence for recognition of foreign professional qualifications – can be an option.
- With a **job offer**, a skilled-worker residence and the **accelerated skilled-worker procedure** may also apply. For that logic, see [Germany work visa with a job offer](/en/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-en).

*Visa/residence rules change often – verify the exact conditions with the German consulate and the competent state recognition authority.*

## Duration & bureaucracy – honestly (with hedges)

Let's be honest: **recognition bureaucracy can be slow.** Gathering documents, certified translations, apostilles, the authority's assessment and, if needed, the compensation measure can stretch over months – sometimes longer. The duration depends on:
- which state you apply in,
- how complete your documents are,
- whether a compensation measure is needed,
- how fast you push your German up.

Nobody can **guarantee** you a fixed timeline – be skeptical if someone does. A complete, clean file from the start saves the most time.

## Conclusion & honest advice

Your foreign social work degree is **valuable** in Germany – the field is in demand (Fachkräftemangel) and stable. Success rests on two things: a **clean recognition file** and **C1 German**. My advice:

1. Decide early on your **target state** and its **recognition authority**.
2. Gather your **documents** (transcript, hours, internships, translations) neatly from the start.
3. Start **C1 German in parallel** – the field's biggest bottleneck.
4. If a compensation measure is required, decide based on what your **Bescheid** says, not the forum.

For the reality on the working side (fields, TVöD-SuE salary, conditions), see [working as a social worker in Germany](/en/blog/working-as-a-social-worker-in-germany-fields-salary-and-reality-en). And to weigh whether the whole route is worth it, see [is studying social work in Germany worth it?](/en/blog/is-studying-social-work-in-germany-worth-it-honest-reality-en).

*This post is general information as of early 2026; the process, timelines, terminology, language thresholds and visa conditions vary by federal state and over time. For binding information, verify with the competent state recognition authority and anerkennung-in-deutschland.de.*
MD;

        $variants = [
            'tr' => ['slug'=>'getting-your-foreign-social-work-qualification-recognized-in-germany-anerkennung',    'title'=>'Yurtdışı Sosyal Hizmet Diplomanı Almanya\'da Tanıtmak: Anerkennung (2026)', 'excerpt'=>'Yurtdışında sosyal hizmet/sosyal pedagoji okuduysan diplomanı Almanya\'da nasıl tanıtırsın? Yetkili makama başvuru, denklik değerlendirmesi, Bescheid, Anpassungslehrgang vs Eignungsprüfung, C1 Almanca, AB-dışı diploma gerçeği ve §16d — adım adım, dürüst rehber.', 'meta_title'=>'Almanya Sosyal Hizmet Diploma Tanınması (Anerkennung) 2026', 'meta_description'=>'Yurtdışı sosyal hizmet diplomanı Almanya\'da tanıtma rehberi: yetkili makam, denklik, Bescheid, Anpassungslehrgang vs Eignungsprüfung, C1 Almanca ve §16d.', 'body'=>$trBody],
            'de' => ['slug'=>'getting-your-foreign-social-work-qualification-recognized-in-germany-anerkennung-de', 'title'=>'Anerkennung ausländischer Abschlüsse in Sozialer Arbeit in Deutschland (2026)', 'excerpt'=>'Wie wird dein ausländischer Abschluss in Sozialer Arbeit/Sozialpädagogik in Deutschland anerkannt? Antrag bei der Anerkennungsstelle, Gleichwertigkeitsprüfung, Bescheid, Anpassungslehrgang vs Eignungsprüfung, C1-Deutsch, Nicht-EU-Realität und §16d – ehrlich und Schritt für Schritt.', 'meta_title'=>'Anerkennung ausländischer Abschlüsse Soziale Arbeit 2026', 'meta_description'=>'Leitfaden zur Anerkennung ausländischer Abschlüsse in Sozialer Arbeit: Anerkennungsstelle, Gleichwertigkeit, Bescheid, Anpassungslehrgang vs Eignungsprüfung, C1 und §16d.', 'body'=>$deBody],
            'en' => ['slug'=>'getting-your-foreign-social-work-qualification-recognized-in-germany-anerkennung-en', 'title'=>'Getting Your Foreign Social Work Qualification Recognized in Germany: Anerkennung (2026)', 'excerpt'=>'If you studied social work/social pedagogy abroad, how do you get your degree recognized in Germany? Applying to the competent authority, equivalence assessment, Bescheid, Anpassungslehrgang vs Eignungsprüfung, C1 German, the non-EU diploma reality and §16d — an honest, step-by-step guide.', 'meta_title'=>'Foreign Social Work Qualification Recognition Germany (Anerkennung) 2026', 'meta_description'=>'Guide to getting a foreign social work qualification recognized in Germany: recognition authority, equivalence, Bescheid, Anpassungslehrgang vs Eignungsprüfung, C1 German and §16d.', 'body'=>$enBody],
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
            'getting-your-foreign-social-work-qualification-recognized-in-germany-anerkennung',
            'getting-your-foreign-social-work-qualification-recognized-in-germany-anerkennung-de',
            'getting-your-foreign-social-work-qualification-recognized-in-germany-anerkennung-en',
        ])->delete();
    }
};
