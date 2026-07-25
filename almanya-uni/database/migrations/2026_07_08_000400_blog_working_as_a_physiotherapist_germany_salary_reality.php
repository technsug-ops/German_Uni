<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da fizyoterapist olarak çalışmak — maaş, dil, gerçek (2026).
 * Doğrulandı: Physiotherapeut giriş maaşı ~2.500–3.200€ brüt/ay (mütevazı, işveren/eyalete göre
 * değişir, 2025, doğrula); talep çok yüksek (yaşlanan nüfus) + iş garantisi güçlü + kalıcı oturum
 * yolu; B2 Almanca hasta iletişimi için şart; iş fiziksel; kendi praxis açma seçeneği var.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'c3d40000-4444-4faf-9f00-cc0add10aa04';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da fizyoterapist olarak çalışmayı düşünenlerin kafasındaki asıl sorular basit: **iş bulabilir miyim, ne kadar kazanırım, dil ne kadar şart, gerçekte nasıl bir hayat?** Bu yazı bunları süslemeden, dürüstçe anlatıyor. Kısa özet: talep çok yüksek, iş neredeyse garanti, kalıcı oturuma giden yol net — ama maaş emeğe göre mütevazı ve B2 Almanca pazarlık konusu değil.

## İş piyasası: fizyoterapist her yerde aranıyor

Almanya nüfusça yaşlanıyor ve bu, fizyoterapiye olan talebi sürekli yukarı itiyor. Özel praxis'ler (Physiopraxis), hastaneler, **rehabilitasyon klinikleri**, huzurevleri ve spor merkezleri sürekli **Physiotherapeut** arıyor. Yabancı için bu nadir bir avantaj: iş bulma neredeyse sorun değil, asıl mesele **tanınma (Anerkennung)** ve **dil**.

Talep büyük şehirlerle sınırlı değil; küçük kasabalarda da yoğun. Bu sana pazarlık gücü verir — bazı işverenler seni Almanya'ya getirmek için tanınma sürecini ve dili destekleyebilir. Almanya'da fizyoterapist olmanın genel çerçevesini bu kümedeki [Almanya'da fizyoterapist olmak rehberinde](/tr/blog/becoming-a-physiotherapist-in-germany-as-a-foreigner) anlattık.

## Maaş: ne kadar kazanırsın (giriş ~2.500–3.200€ brüt)

En çok merak edilen ve en dürüst konuşmamız gereken konu. Tam tanınmış bir fizyoterapist için giriş maaşı, 2025 itibarıyla yaklaşık **2.500–3.200€ brüt/ay** civarındadır. Açık konuşalım: bu, mesleğin gerektirdiği eğitim ve fiziksel emeğe göre **mütevazı** bir rakamdır ve hemşirelikle benzer veya biraz altındadır. Rakam işverene, eyalete ve **Tarif** (toplu sözleşme) uygulanıp uygulanmadığına göre değişir. *Kesin rakam için işverenin sözleşmesini doğrula.*

| Durum | Yaklaşık brüt/ay (2025, doğrula) | Not |
|---|---|---|
| Giriş — Physiotherapeut | ~2.500–3.200€ | İşveren/eyalete göre değişir |
| Deneyimli / uzmanlık (manuel terapi, rehab) | ~3.200–3.800€+ | Zusatzqualifikation ile |
| Klinik/hastane (Tarif'li) | Tarif'e göre | Kamuda daha şeffaf |
| Kendi praxis (bağımsız) | Değişken | Ciro − gider; risk + potansiyel yukarı |

Önemli: **brüt ≠ net.** Vergiler ve sosyal kesintiler sonrası net elin daha düşük olur. Maaşın mütevazı olması mesleğin bilinen zayıf noktasıdır; ama iş güvencesi, talep ve kalıcılık bunu bir ölçüde dengeler. Daha yüksek gelir isteyen birçok fizyoterapist zamanla uzmanlık sertifikaları alır veya kendi praxis'ini açar.

## Dil gerçeği: B2 pazarlık konusu değil

Burayı yumuşatmayacağım: **Almanca B2 pratikte şarttır.** Bazı işveren/eyalet seni B1 ile getirip B2'ye taşıyabilir, ama tam fizyoterapist olarak çalışmak için B2 beklenir; bazı durumlarda **Fachsprachprüfung** (mesleki dil sınavı) da istenir.

Neden bu kadar katı? Çünkü fizyoterapi **hasta iletişimi**nin ta kendisidir: anamnez alma, ağrıyı ve şikâyeti anlama, egzersizi açıklama, hastayı motive etme, doktor/ekip ile koordinasyon — hepsi Almanca. Dil zayıfsa tedavi eksik kalır ve iş çok stresli hale gelir. Dil yatırımı bu meslekte en yüksek getirili adımdır. Tanınma tarafında dilin rolünü [Anerkennung rehberimizde](/tr/blog/getting-your-foreign-physiotherapy-qualification-recognized-in-germany-anerkennung) ayrıntılı işledik.

## Çalışma koşulları: fiziksel ve tempolu (dürüst)

Kimsenin süslemeden söylemediği kısım:

- **Fiziksel iş:** Gün boyu ayakta, manuel terapi, hasta pozisyonlama; kendi vücut mekaniğine dikkat etmezsen bel/sırt yükü gerçektir.
- **Tempo:** Özel praxis'lerde randevular sık ve sıkışık olabilir (bazen 20 dakikada bir hasta); zaman baskısı hissedilir.
- **Ortam çeşidi:** Praxis, hastane, rehab kliniği, evde bakım — her birinin ritmi farklı. Rehab klinikleri genelde daha planlı, özel praxis daha yoğun.
- **Duygusal taraf:** İyileşen hastayı görmek tatmin edici; ama kronik ağrı ve ilerlemeyen vakalar yıpratıcı olabilir.

Bunları bilirsen hazırlıklı gidersin. Birçok fizyoterapist için iş anlamlı ve tatmin edici — ama romantize etmiyoruz, fiziksel olarak yorucu bir meslek.

## Kendi praxis'in + kalıcı oturuma giden yol

Maaşın mütevazı olmasının önemli bir dengeleyicisi: fizyoterapide **kendi işini kurma** seçeneği gerçektir. Tanınma ve gerekli koşullar sağlandıktan sonra kendi **Physiopraxis**'ini açabilir, kendi hastanı ve gelirini yönetebilirsin. Bu, tavan geliri ciddi yükseltir — karşılığında girişimci riski ve bürokrasi gelir.

Kalıcılık açısından da fizyoterapi güçlü bir meslek: nitelikli işçi olarak çalışmaya başladıktan sonra tanınan meslek + istikrarlı iş + dil ile **Niederlassungserlaubnis** (süresiz oturum) yolu net şekilde açıktır ve şartlar sağlanınca zamanla vatandaşlığa uzanır. Bu, birçok akademik yola göre daha öngörülebilir bir "kalma" hikâyesidir: meslek talep görüyor, işsizlik riski düşük. *Kesin oturum/süre şartlarını resmi kaynaktan (Ausländerbehörde) doğrula.*

## Strateji: önce dil, sonra işveren ağı

Pratik sıralama:

1. **Dili öne al.** B2'ye ulaşmadan tanınma da iş de tam oturmaz. En büyük darboğaz burası.
2. **İşveren ağını kur.** Uluslararası alım yapan rehab klinikleri ve praxis zincirleri tanınma + dil + gelişi destekleyebilir; bu süreci ciddi hızlandırır.
3. **Vizeyi doğru seç.** Tanınma amaçlı (§16d benzeri) ya da iş teklifiyle nitelikli işçi oturumu — Almanya'da hızlandırılmış nitelikli işçi prosedürü var. İş teklifiyle vize sürecini [work-visa rehberinde](/tr/blog/germany-work-visa-with-job-offer-process-timeline-fast-track) anlattık.
4. **Beklentini gerçekçi tut.** Bürokrasi yavaş olabilir; erken başla, evrakı eksiksiz hazırla. Maaşı mütevazı gördüğün için hayal kırıklığı yaşamamak adına gelir planını baştan doğru kur.

Benzer bir sağlık mesleği olarak, karşılaştırma yapmak istersen [Almanya'da hemşire olmak yazımıza](/tr/blog/becoming-a-nurse-in-germany-as-a-foreigner) da göz atabilirsin — maaş ve dil dinamikleri oldukça benzer.

## Sonuç & dürüst tavsiye

Almanya'da fizyoterapi **garanti iş, güçlü talep ve kalıcı oturuma net yol** demek — ama bedeli mütevazı bir maaş, fiziksel yorgunluk ve ciddi bir dil emeğidir. Bu mesleği **sırf para için değil, işi sevdiğin ve taşıyabildiğin için** seçmelisin; o zaman iş güvencesi ve kalıcılık avantajı fazlasıyla değer kazanır.

Dürüst tavsiyem: **önce B2'ye odaklan**, uluslararası alım yapan bir işverenle temas kur, tanınma sürecini erken başlat ve uzun vadede uzmanlık veya kendi praxis'ini planla. Rakamlar ve vize adımları güncellenir — maaş için işveren sözleşmesini, vize/tanınma için resmi kaynağı doğrula. Eğitim yolunun kendisi için [fizyoterapi eğitimi yazımıza](/tr/blog/physiotherapy-training-and-study-in-germany-for-internationals) bak.

*Bu içerik 2026 başı itibarıyla genel bilgilendirme amaçlıdır; maaş, Tarif, vize ve tanınma kuralları değişir. Kesin bilgi için işvereninin sözleşmesini, ilgili Anerkennungsstelle'yi, anerkennung-in-deutschland.de'yi ve Ausländerbehörde'yi doğrula.*
MD;

        $deBody = <<<'MD'
Wenn du überlegst, als Physiotherapeut in Deutschland zu arbeiten, hast du wahrscheinlich genau diese Fragen im Kopf: **Finde ich einen Job, wie viel verdiene ich, wie wichtig ist die Sprache, und wie ist der Alltag wirklich?** Dieser Artikel beantwortet das ehrlich und ohne Beschönigung. Kurz gesagt: Die Nachfrage ist hoch, ein Job ist fast sicher, der Weg zur Niederlassung ist klar — aber das Gehalt ist gemessen an der Arbeit bescheiden und Deutsch auf B2 ist nicht verhandelbar.

## Arbeitsmarkt: Physiotherapeuten werden überall gesucht

Deutschland altert, und das treibt die Nachfrage nach Physiotherapie stetig nach oben. Private Praxen (Physiopraxis), Krankenhäuser, **Reha-Kliniken**, Altenheime und Sportzentren suchen ständig **Physiotherapeuten**. Für dich als Ausländer ist das ein seltener Vorteil: Einen Job zu finden ist kaum das Problem — das Problem sind **Anerkennung** und **Sprache**.

Die Nachfrage ist nicht auf Großstädte beschränkt; auch in kleineren Orten ist sie hoch. Das gibt dir Verhandlungsmacht — manche Arbeitgeber unterstützen deine Anerkennung und Sprache, um dich nach Deutschland zu holen. Den Gesamtrahmen erklären wir im [Leitfaden „Physiotherapeut in Deutschland werden"](/de/blog/becoming-a-physiotherapist-in-germany-as-a-foreigner-de).

## Gehalt: Was du verdienst (Einstieg ~2.500–3.200€ brutto)

Das meistgefragte Thema, bei dem wir am ehrlichsten sein müssen. Für einen voll anerkannten Physiotherapeuten liegt das Einstiegsgehalt Stand 2025 bei etwa **2.500–3.200€ brutto/Monat**. Sagen wir es offen: Das ist gemessen an Ausbildung und körperlichem Einsatz **bescheiden** und liegt ähnlich wie oder etwas unter der Pflege. Die Zahl schwankt je nach Arbeitgeber, Bundesland und ob ein **Tarif** gilt. *Prüfe die genaue Zahl im Vertrag deines Arbeitgebers.*

| Situation | ca. brutto/Monat (2025, prüfen) | Hinweis |
|---|---|---|
| Einstieg — Physiotherapeut | ~2.500–3.200€ | je nach Arbeitgeber/Bundesland |
| Erfahren / Fachbereich (Manuelle Therapie, Reha) | ~3.200–3.800€+ | mit Zusatzqualifikation |
| Klinik/Krankenhaus (mit Tarif) | nach Tarif | im öffentlichen Dienst transparenter |
| Eigene Praxis (selbstständig) | variabel | Umsatz − Kosten; Risiko + Potenzial nach oben |

Wichtig: **Brutto ≠ Netto.** Nach Steuern und Sozialabgaben bleibt weniger übrig. Das bescheidene Gehalt ist die bekannte Schwachstelle des Berufs; Jobsicherheit, Nachfrage und Bleibeperspektive gleichen das teils aus. Wer mehr verdienen will, macht mit der Zeit Fachqualifikationen oder eröffnet eine eigene Praxis.

## Die Sprach-Realität: B2 ist nicht verhandelbar

Das beschönige ich nicht: **Deutsch auf B2-Niveau ist in der Praxis Pflicht.** Manche Arbeitgeber/Bundesländer holen dich mit B1 und bringen dich auf B2, aber um voll als Physiotherapeut zu arbeiten, wird B2 erwartet; manchmal auch eine **Fachsprachprüfung**.

Warum so streng? Weil Physiotherapie **Patientenkommunikation** in Reinform ist: Anamnese, Schmerzen und Beschwerden verstehen, Übungen erklären, Patienten motivieren, Abstimmung mit Arzt/Team — alles auf Deutsch. Ist die Sprache schwach, bleibt die Behandlung unvollständig und die Arbeit wird sehr stressig. Die Investition in die Sprache ist hier der Schritt mit dem höchsten Ertrag. Die Rolle der Sprache bei der Anerkennung behandeln wir im [Anerkennungs-Leitfaden](/de/blog/getting-your-foreign-physiotherapy-qualification-recognized-in-germany-anerkennung-de).

## Arbeitsbedingungen: körperlich und im Takt (ehrlich)

Der Teil, den niemand unbeschönigt sagt:

- **Körperliche Arbeit:** den ganzen Tag auf den Beinen, Manuelle Therapie, Patienten lagern; achtest du nicht auf deine Körpermechanik, ist die Rücken-/Nackenbelastung real.
- **Takt:** In Privatpraxen können Termine dicht getaktet sein (manchmal alle 20 Minuten ein Patient); der Zeitdruck ist spürbar.
- **Verschiedene Settings:** Praxis, Krankenhaus, Reha-Klinik, Hausbesuche — jedes hat seinen eigenen Rhythmus. Reha-Kliniken sind meist planbarer, Privatpraxen intensiver.
- **Emotionale Seite:** Einen genesenden Patienten zu sehen ist erfüllend; chronische Schmerzen und stagnierende Fälle können zehren.

Wenn du das weißt, gehst du vorbereitet hinein. Für viele Physiotherapeuten ist die Arbeit sinnvoll und erfüllend — aber wir romantisieren nichts, es ist ein körperlich fordernder Beruf.

## Eigene Praxis + Weg zur Niederlassung

Ein wichtiger Ausgleich zum bescheidenen Gehalt: In der Physiotherapie ist die Option der **Selbstständigkeit** real. Nach Anerkennung und erfüllten Voraussetzungen kannst du eine eigene **Physiopraxis** eröffnen und deine Patienten und dein Einkommen selbst steuern. Das hebt das Verdienstpotenzial deutlich — im Gegenzug kommen Unternehmerrisiko und Bürokratie.

Auch beim Bleiben ist Physiotherapie ein starker Beruf: Sobald du als Fachkraft arbeitest, ist der Weg zur **Niederlassungserlaubnis** (unbefristeter Aufenthalt) mit anerkanntem Beruf + stabilem Job + Sprache klar — und bei erfüllten Voraussetzungen führt er mit der Zeit zur Einbürgerung. Das ist eine planbarere „Bleiben"-Geschichte als viele akademische Wege: Der Beruf ist gefragt, das Arbeitslosigkeitsrisiko gering. *Prüfe die genauen Aufenthaltsvoraussetzungen bei der Ausländerbehörde.*

## Strategie: erst die Sprache, dann das Netzwerk

Die praktische Reihenfolge:

1. **Stelle die Sprache voran.** Ohne B2 sitzt weder Anerkennung noch Job richtig. Das ist der größte Engpass.
2. **Baue ein Arbeitgeber-Netzwerk auf.** International rekrutierende Reha-Kliniken und Praxisketten können Anerkennung + Sprache + Umzug unterstützen; das beschleunigt den Prozess deutlich.
3. **Wähle das richtige Visum.** §16d zur Anerkennung oder ein Fachkräfteaufenthalt mit Jobangebot — es gibt ein beschleunigtes Fachkräfteverfahren. Den Visumsweg mit Jobangebot beschreiben wir im [Arbeitsvisum-Leitfaden](/de/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-de).
4. **Halte deine Erwartungen realistisch.** Bürokratie kann langsam sein; fang früh an und bereite deine Unterlagen vollständig vor. Plane dein Einkommen von Anfang an richtig, um vom bescheidenen Gehalt nicht enttäuscht zu werden.

Als verwandter Gesundheitsberuf lohnt zum Vergleich ein Blick in unseren Artikel [Pflegekraft in Deutschland werden](/de/blog/becoming-a-nurse-in-germany-as-a-foreigner-de) — Gehalt und Sprachdynamik sind sehr ähnlich.

## Fazit & ehrlicher Rat

Physiotherapie in Deutschland bedeutet **gesicherte Arbeit, starke Nachfrage und einen klaren Weg zum unbefristeten Aufenthalt** — der Preis dafür sind ein bescheidenes Gehalt, körperliche Ermüdung und ein ernsthafter Sprachaufwand. Wähle diesen Beruf **nicht nur wegen des Geldes, sondern weil du die Arbeit liebst und tragen kannst**; dann zahlen sich Jobsicherheit und Bleibeperspektive mehr als aus.

Mein ehrlicher Rat: **Konzentriere dich zuerst auf B2**, knüpfe Kontakt zu einem international rekrutierenden Arbeitgeber, starte die Anerkennung früh und plane langfristig eine Spezialisierung oder eine eigene Praxis. Zahlen und Visumsschritte ändern sich — prüfe das Gehalt im Arbeitsvertrag und Visum/Anerkennung an offizieller Stelle. Zum Ausbildungsweg selbst siehe unseren Artikel [Physiotherapie-Ausbildung und -Studium](/de/blog/physiotherapy-training-and-study-in-germany-for-internationals-de).

*Dieser Inhalt dient der allgemeinen Information Stand Anfang 2026; Gehalt, Tarif, Visums- und Anerkennungsregeln ändern sich. Für verbindliche Angaben prüfe den Vertrag deines Arbeitgebers, die zuständige Anerkennungsstelle, anerkennung-in-deutschland.de und die Ausländerbehörde.*
MD;

        $enBody = <<<'MD'
If you're thinking about working as a physiotherapist in Germany, you probably have exactly these questions: **can I find a job, how much will I earn, how important is the language, and what's the daily reality?** This article answers that honestly, with no sugar-coating. In short: demand is high, a job is almost guaranteed, the path to permanent residence is clear — but the salary is modest relative to the work, and German at B2 is not negotiable.

## The job market: physiotherapists are wanted everywhere

Germany is ageing, and that keeps pushing demand for physiotherapy upward. Private practices (Physiopraxis), hospitals, **rehabilitation clinics**, care homes and sports centres are constantly looking for a **Physiotherapeut**. As a foreigner that's a rare advantage: finding a job is barely the problem — the problems are **recognition (Anerkennung)** and **language**.

Demand isn't limited to big cities; it's high in smaller towns too. That gives you bargaining power — some employers may support your recognition and language to bring you to Germany. We cover the overall picture in our [guide to becoming a physiotherapist in Germany](/en/blog/becoming-a-physiotherapist-in-germany-as-a-foreigner-en).

## Salary: what you earn (entry ~€2,500–3,200 gross)

The most-asked topic, and the one where we have to be most honest. For a fully recognised physiotherapist, the entry salary as of 2025 is roughly **€2,500–3,200 gross/month**. Let's be blunt: that is **modest** given the training and physical effort the job requires, and it's similar to or slightly below nursing. The figure varies by employer, federal state and whether a **Tarif** (collective agreement) applies. *Check the exact figure in your employer's contract.*

| Situation | approx. gross/month (2025, verify) | Note |
|---|---|---|
| Entry — Physiotherapeut | ~€2,500–3,200 | varies by employer/state |
| Experienced / specialist (manual therapy, rehab) | ~€3,200–3,800+ | with extra qualification |
| Clinic/hospital (with Tarif) | per Tarif | more transparent in public sector |
| Own practice (self-employed) | variable | revenue − costs; risk + upside |

Important: **gross ≠ net.** After taxes and social contributions you keep less. The modest salary is the profession's known weak spot; job security, demand and the staying perspective offset it in part. Those who want to earn more take specialist qualifications over time or open their own practice.

## The language reality: B2 is not negotiable

I won't soften this: **German at B2 level is required in practice.** Some employers/states bring you in at B1 and move you to B2, but to work fully as a physiotherapist, B2 is expected; sometimes a **Fachsprachprüfung** (professional language exam) too.

Why so strict? Because physiotherapy *is* **patient communication**: taking a history, understanding pain and complaints, explaining exercises, motivating patients, coordinating with the doctor/team — all in German. If your language is weak, treatment stays incomplete and the work becomes very stressful. Investing in the language is the highest-return step in this profession. We cover the role of language on the recognition side in our [Anerkennung guide](/en/blog/getting-your-foreign-physiotherapy-qualification-recognized-in-germany-anerkennung-en).

## Working conditions: physical and fast-paced (honest)

The part nobody says without sugar-coating:

- **Physical work:** on your feet all day, manual therapy, positioning patients; if you don't watch your own body mechanics, the back/neck load is real.
- **Pace:** in private practices appointments can be tightly scheduled (sometimes a patient every 20 minutes); the time pressure is noticeable.
- **Varied settings:** practice, hospital, rehab clinic, home visits — each has its own rhythm. Rehab clinics are usually more planned, private practices more intense.
- **Emotional side:** seeing a patient recover is fulfilling; chronic pain and stalled cases can wear you down.

If you know this, you go in prepared. For many physiotherapists the work is meaningful and satisfying — but we're not romanticising it, it's a physically demanding profession.

## Your own practice + the path to permanent residence

An important counterweight to the modest salary: in physiotherapy the option of **self-employment** is real. After recognition and once the conditions are met, you can open your own **Physiopraxis** and manage your own patients and income. That raises your earning potential significantly — in return come entrepreneurial risk and bureaucracy.

On staying, too, physiotherapy is a strong profession: once you work as a skilled worker, the path to a **Niederlassungserlaubnis** (permanent residence) is clear with a recognised profession + stable job + language — and once the conditions are met, it leads to citizenship over time. That's a more predictable "staying" story than many academic routes: the profession is in demand, unemployment risk is low. *Verify the exact residence requirements with the Ausländerbehörde.*

## Strategy: language first, then network

The practical order:

1. **Put the language first.** Without B2, neither recognition nor the job settles properly. This is the biggest bottleneck.
2. **Build an employer network.** Internationally recruiting rehab clinics and practice chains can support recognition + language + relocation; that speeds up the process significantly.
3. **Choose the right visa.** §16d for recognition, or a skilled-worker residence with a job offer — Germany has an accelerated skilled-worker procedure. We describe the job-offer visa route in the [work-visa guide](/en/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-en).
4. **Keep your expectations realistic.** Bureaucracy can be slow; start early and prepare your documents completely. Plan your finances correctly from the start so the modest salary doesn't disappoint you.

As a related health profession, if you want to compare, take a look at our article [becoming a nurse in Germany](/en/blog/becoming-a-nurse-in-germany-as-a-foreigner-en) — the salary and language dynamics are very similar.

## Conclusion & honest advice

Physiotherapy in Germany means **secured work, strong demand and a clear path to permanent residence** — the price is a modest salary, physical fatigue and serious language effort. Choose this profession **not just for the money, but because you love and can carry the work**; then the job security and staying advantage pay off more than enough.

My honest advice: **focus on B2 first**, make contact with an internationally recruiting employer, start the recognition early, and plan a specialisation or your own practice for the long term. Numbers and visa steps change — verify the salary in your employment contract, and the visa/recognition with an official source. For the training route itself, see our article on [physiotherapy training and study](/en/blog/physiotherapy-training-and-study-in-germany-for-internationals-en).

*This content is general information as of early 2026; salary, Tarif, visa and recognition rules change. For binding details, verify your employer's contract, the responsible Anerkennungsstelle, anerkennung-in-deutschland.de and the Ausländerbehörde.*
MD;

        $variants = [
            'tr' => ['slug'=>'working-as-a-physiotherapist-in-germany-salary-language-and-reality',    'title'=>'Almanya\'da Fizyoterapist Olarak Çalışmak: Maaş, Dil ve Gerçek (2026)', 'excerpt'=>'Almanya\'da fizyoterapist olarak çalışmak: giriş maaşı ~2.500–3.200€ brüt (2025, mütevazı, doğrula), B2 dil gerçeği, fiziksel iş, çok yüksek talep ve kalıcı oturuma giden net yol — ayrıca kendi praxis seçeneği. Dürüst bir bakış.', 'meta_title'=>'Almanya\'da Fizyoterapist Olarak Çalışmak: Maaş & Dil (2026)', 'meta_description'=>'Almanya\'da fizyoterapi maaşı (~2.500–3.200€ brüt, 2025), B2 dil şartı, fiziksel koşullar, kendi praxis ve kalıcı oturuma giden yol — dürüst rehber.', 'body'=>$trBody],
            'de' => ['slug'=>'working-as-a-physiotherapist-in-germany-salary-language-and-reality-de', 'title'=>'Als Physiotherapeut in Deutschland arbeiten: Gehalt, Sprache & Realität (2026)', 'excerpt'=>'Als Physiotherapeut in Deutschland arbeiten: Einstiegsgehalt ~2.500–3.200€ brutto (2025, bescheiden, prüfen), die B2-Sprach-Realität, körperliche Arbeit, hohe Nachfrage und klarer Weg zur Niederlassung — plus eigene Praxis. Ein ehrlicher Blick.', 'meta_title'=>'Als Physiotherapeut in Deutschland: Gehalt & Sprache (2026)', 'meta_description'=>'Physiotherapie-Gehalt in Deutschland (~2.500–3.200€ brutto, 2025), B2-Pflicht, körperliche Bedingungen, eigene Praxis und Weg zur Niederlassung — ehrlicher Leitfaden.', 'body'=>$deBody],
            'en' => ['slug'=>'working-as-a-physiotherapist-in-germany-salary-language-and-reality-en', 'title'=>'Working as a Physiotherapist in Germany: Salary, Language & Reality (2026)', 'excerpt'=>'Working as a physiotherapist in Germany: entry salary ~€2,500–3,200 gross (2025, modest, verify), the B2 language reality, physical work, very high demand and a clear path to permanent residence — plus your own practice. An honest look.', 'meta_title'=>'Working as a Physiotherapist in Germany: Salary & Language (2026)', 'meta_description'=>'Physiotherapy salary in Germany (~€2,500–3,200 gross, 2025), B2 requirement, physical conditions, own practice and path to permanent residence — an honest guide.', 'body'=>$enBody],
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
            'working-as-a-physiotherapist-in-germany-salary-language-and-reality',
            'working-as-a-physiotherapist-in-germany-salary-language-and-reality-de',
            'working-as-a-physiotherapist-in-germany-salary-language-and-reality-en',
        ])->delete();
    }
};
