<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da hemşire olarak çalışmak — maaş, dil, gerçek koşullar (2026).
 * Doğrulandı: Pflegefachkraft giriş maaşı ~3.000–3.600€ brüt/ay (TVöD-P/Tarif'e göre değişir, 2025,
 * doğrula); B2 Almanca pratikte şart, iş iletişim-yoğun; vardiya + fiziksel/duygusal zorluk gerçek;
 * ama stabil, çok talep gören meslek, kalıcı oturuma (Niederlassung) giden net yol.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. FK-safe + slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'f4a40000-4444-4c4e-9f70-ff01aa06cc04';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da hemşire olmayı düşünenlerin kafasındaki asıl soru genelde tek: **para ne, dil ne kadar zor, gerçekte nasıl bir hayat?** Bu yazı tam olarak bunu dürüstçe anlatıyor. Almanya'da hemşirelik (Pflege) çok talep gören, stabil ve kalıcı oturuma giden bir meslek — ama vardiyalı, fiziksel ve duygusal olarak yorucu. Süslemeden anlatalım.

## İş piyasası: hemşire her yerde aranıyor

Almanya'nın en büyük yapısal sorunlarından biri **Pflegenotstand** — bakım/hemşire açığı. Hastaneler, huzurevleri (Altenheim), ayakta bakım servisleri (ambulante Pflege) sürekli **Pflegefachkraft** arıyor. Bu, yabancı için nadir bir avantaj: iş bulma sorunu neredeyse yok, sorun **tanınma (Anerkennung)** ve **dil**.

Talep büyük şehirle sınırlı değil; küçük kasabalarda da yoğun. Bu esneklik sana pazarlık gücü verir: işveren seni Almanya'ya getirmek için tanınma sürecini ve dil kursunu sponsorlayabilir. İki temel giriş yolunu bu kümedeki [Almanya'da hemşire olmak rehberinde](/tr/blog/becoming-a-nurse-in-germany-as-a-foreigner) ayrıntılı anlattık.

## Maaş: ne kadar kazanırsın (giriş ~3.000–3.600€ brüt)

En çok merak edilen konu. Tam tanınmış bir **Pflegefachkraft** için giriş maaşı, 2025 itibarıyla yaklaşık **3.000–3.600€ brüt/ay** civarındadır. Rakam işverene, eyalete ve özellikle **Tarif** (toplu sözleşme, kamu için **TVöD-P**) uygulanıp uygulanmadığına göre ciddi değişir. *Kesin rakam için işverenin Tarif'ini doğrula.*

| Durum | Yaklaşık brüt/ay (2025, doğrula) | Not |
|---|---|---|
| Ausbildung (eğitim dönemi) | ~1.100–1.400€ | Maaşlı öğrencilik, yıla göre artar |
| Giriş — Pflegefachkraft | ~3.000–3.600€ | TVöD-P / Tarif'e göre değişir |
| Deneyimli / uzmanlık (ör. yoğun bakım) | ~3.800–4.500€+ | Zusatzqualifikation ile |
| Vardiya/gece/hafta sonu ekleri | + Zulagen | Brüte ek olarak gelir |

Önemli: **brüt ≠ net.** Vergiler ve sosyal kesintiler sonrası net elin daha düşük olur, ama vardiya ekleri (Nacht-/Wochenendzulage) toplamı yukarı çeker. Kamuda (TVöD-P) maaş şeffaf ve kıdemle otomatik artar; özel işverende değişkenlik daha fazla.

## Dil gerçeği: B2 pazarlık konusu değil

Burayı yumuşatmayacağım: **Almanca B2 pratikte şarttır.** Bazı eyalet/işveren seni B1 ile getirip çalıştırırken B2'ye taşıyabilir, ama tam Pflegefachkraft olarak çalışmak için B2 beklenir; bazı durumlarda **Fachsprachprüfung** (mesleki dil sınavı) da istenir.

Neden bu kadar katı? Çünkü bakım işi **iletişim-yoğundur**: hasta anamnezi, doktor talimatı, ilaç dozu, nöbet devri (Übergabe), yakınla konuşma — hepsi Almanca ve hepsi hata payı sıfır olması gereken yerler. Dil zayıfsa iş tehlikeli ve stresli hale gelir. Dil yatırımı, bu meslekte en yüksek getirili yatırımdır. Sıfırdan bir yol haritası için [Almanca öğrenme rehberimize](/tr/blog/learning-german-from-zero-to-c1-a-roadmap-testdafdsh) göz at.

## Çalışma koşulları: vardiya, fiziksel ve duygusal yük (dürüst)

Şimdi kimsenin süslemeden söylemediği kısım:

- **Vardiya (Schichtdienst):** Sabah, öğleden sonra, gece; hafta sonu ve tatil dahil. Sosyal hayatın buna göre şekillenir.
- **Fiziksel yük:** Hasta kaldırma-çevirme, ayakta uzun saatler; sırt sağlığına dikkat gerekir.
- **Duygusal yük:** Ağır hasta, ölüm, zaman baskısı, personel azlığından kaynaklı yoğunluk. Tükenmişlik (Burnout) bu meslekte gerçek bir risktir.
- **Sorumluluk:** İlaç, dokümantasyon, hasta güvenliği — hata payı düşük.

Bunları biliyorsan hazırlıklı gidersin. Birçok hemşire için yük gerçek ama anlamlı: işin karşılığını hem maaş hem de topluma katkı olarak görürler. Yine de romantize etmiyoruz — bu ağır bir iş.

## Kalıcı oturuma giden yol (Niederlassung)

Zorlukların karşılığında hemşirelik, Almanya'da **kalıcılık** açısından güçlü bir meslektir. Nitelikli işçi olarak çalışmaya başladıktan sonra, tanınan meslek + istikrarlı iş + dil ile **Niederlassungserlaubnis** (süresiz oturum) yolun net şekilde açıktır; şartlar sağlanınca zamanla vatandaşlığa da uzanır.

Bu, birçok akademik yola göre daha öngörülebilir bir "kalma" hikâyesi sunar: meslek talep görüyor, işsizlik riski düşük, oturum yenileme sorunsuz. Tanınma sürecinin kendisi için [Anerkennung rehberimizi](/tr/blog/getting-your-foreign-nursing-qualification-recognized-in-germany-anerkennung), sıfırdan maaşlı eğitim için [Ausbildung yazımızı](/tr/blog/nursing-ausbildung-in-germany-for-internationals-paid-training) oku. *Kesin oturum/süre şartlarını resmi kaynaktan (Ausländerbehörde) doğrula.*

## Uyum & strateji: önce dil, sonra ağ

Pratik sıralama:

1. **Dili öne al.** B2'ye ulaşmadan hiçbir şey tam oturmaz. En büyük darboğaz burası.
2. **İşveren ağını kur.** Uluslararası alım yapan hastane/bakım zincirleri (ör. büyük klinik grupları) tanınma + dil + gelişi sponsorlayabilir. Bu, süreci ciddi hızlandırır.
3. **Vizeyi doğru seç.** Tanınma amaçlı §16d veya iş teklifiyle nitelikli işçi oturumu — Almanya'da **hızlandırılmış nitelikli işçi prosedürü** var. İş teklifiyle vize sürecini [work-visa rehberinde](/tr/blog/germany-work-visa-with-job-offer-process-timeline-fast-track) anlattık; öğrenciysen [Zweckwechsel yazımıza](/tr/blog/changing-student-visa-to-work-permit-germany-zweckwechsel) bak.
4. **Beklentini gerçekçi tut.** Bürokrasi yavaş olabilir; erken başla, evrakı eksiksiz hazırla.

## Sonuç & dürüst tavsiye

Almanya'da hemşirelik **iyi para, garanti iş, kalıcı oturuma net yol** demek — ama bedeli vardiya, fiziksel ve duygusal yük, ve ciddi bir dil emeği. Bu mesleği **maaş için değil, işi taşıyabileceğin için** seçmelisin; o zaman getirileri fazlasıyla değer.

Dürüst tavsiyem: **önce B2'ye odaklan**, uluslararası alım yapan bir işverenle temas kur ve tanınma sürecini erken başlat. Rakamlar ve vize adımları güncellenir — maaş için Tarif'i, vize/tanınma için resmi kaynağı doğrula.

*Bu içerik 2026 başı itibarıyla genel bilgilendirme amaçlıdır; maaş, Tarif, vize ve tanınma kuralları değişir. Kesin bilgi için işvereninin Tarif sözleşmesini, ilgili Anerkennungsstelle'yi, anerkennung-in-deutschland.de'yi ve Ausländerbehörde'yi doğrula.*
MD;

        $deBody = <<<'MD'
Wenn du überlegst, als Pflegekraft nach Deutschland zu kommen, hast du wahrscheinlich genau drei Fragen im Kopf: **Wie viel verdiene ich, wie schwer ist die Sprache, und wie ist der Alltag wirklich?** Dieser Artikel beantwortet genau das — ehrlich. Pflege ist ein gefragter, stabiler Beruf mit klarem Weg zur Niederlassung — aber Schichtdienst, körperlich und emotional fordernd. Ohne Beschönigung.

## Arbeitsmarkt: Pflegekräfte werden überall gesucht

Eines der größten strukturellen Probleme Deutschlands ist der **Pflegenotstand** — der Mangel an Pflegepersonal. Krankenhäuser, Altenheime und ambulante Pflegedienste suchen ständig **Pflegefachkräfte**. Für dich als Ausländer ist das ein seltener Vorteil: Einen Job zu finden ist kaum das Problem — das Problem sind **Anerkennung** und **Sprache**.

Die Nachfrage ist nicht auf Großstädte beschränkt; auch in kleineren Orten ist sie hoch. Das gibt dir Verhandlungsmacht: Ein Arbeitgeber kann deine Anerkennung und deinen Sprachkurs finanzieren, um dich nach Deutschland zu holen. Die zwei Einstiegswege haben wir im [Leitfaden „Als Pflegekraft nach Deutschland"](/de/blog/becoming-a-nurse-in-germany-as-a-foreigner-de) ausführlich erklärt.

## Gehalt: Was du verdienst (Einstieg ~3.000–3.600€ brutto)

Das meistgestellte Thema. Für eine voll anerkannte **Pflegefachkraft** liegt das Einstiegsgehalt Stand 2025 bei etwa **3.000–3.600€ brutto/Monat**. Die Zahl schwankt stark je nach Arbeitgeber, Bundesland und vor allem, ob ein **Tarif** gilt (im öffentlichen Dienst **TVöD-P**). *Prüfe die genaue Zahl anhand des Tarifs deines Arbeitgebers.*

| Situation | ca. brutto/Monat (2025, prüfen) | Hinweis |
|---|---|---|
| Ausbildung (Ausbildungszeit) | ~1.100–1.400€ | Bezahlte Ausbildung, steigt pro Jahr |
| Einstieg — Pflegefachkraft | ~3.000–3.600€ | je nach TVöD-P / Tarif |
| Erfahren / Fachbereich (z. B. Intensiv) | ~3.800–4.500€+ | mit Zusatzqualifikation |
| Schicht-/Nacht-/Wochenendzulagen | + Zulagen | kommen zum Brutto hinzu |

Wichtig: **Brutto ≠ Netto.** Nach Steuern und Sozialabgaben bleibt weniger übrig, aber Zulagen (Nacht-/Wochenendzuschläge) heben die Summe wieder an. Im öffentlichen Dienst (TVöD-P) ist das Gehalt transparent und steigt automatisch mit der Erfahrungsstufe; bei privaten Arbeitgebern ist die Bandbreite größer.

## Die Sprach-Realität: B2 ist nicht verhandelbar

Das beschönige ich nicht: **Deutsch auf B2-Niveau ist in der Praxis Pflicht.** Manche Bundesländer/Arbeitgeber holen dich mit B1 und bringen dich auf B2, aber um voll als Pflegefachkraft zu arbeiten, wird B2 erwartet; manchmal auch eine **Fachsprachprüfung**.

Warum so streng? Weil Pflege **kommunikationsintensiv** ist: Anamnese, Arztanordnung, Medikamentendosis, Übergabe, Gespräch mit Angehörigen — alles auf Deutsch und alles ohne Fehlertoleranz. Ist die Sprache schwach, wird die Arbeit gefährlich und stressig. Die Investition in die Sprache ist in diesem Beruf die mit dem höchsten Ertrag. Für einen Fahrplan von null siehe unseren [Leitfaden zum Deutschlernen](/de/blog/learning-german-from-zero-to-c1-a-roadmap-testdafdsh-de).

## Arbeitsbedingungen: Schicht, körperliche und emotionale Last (ehrlich)

Jetzt der Teil, den niemand unbeschönigt sagt:

- **Schichtdienst:** Früh, spät, nachts; inklusive Wochenende und Feiertag. Dein Sozialleben richtet sich danach.
- **Körperliche Belastung:** Patienten heben und drehen, lange auf den Beinen; achte auf deinen Rücken.
- **Emotionale Belastung:** Schwerkranke, Tod, Zeitdruck, Enge durch Personalmangel. Burnout ist in diesem Beruf ein reales Risiko.
- **Verantwortung:** Medikamente, Dokumentation, Patientensicherheit — geringe Fehlertoleranz.

Wenn du das weißt, gehst du vorbereitet hinein. Für viele Pflegekräfte ist die Last real, aber sinnvoll: Sie sehen den Gegenwert im Gehalt und im Beitrag für die Gesellschaft. Trotzdem romantisieren wir nichts — das ist harte Arbeit.

## Der Weg zur Niederlassung

Als Gegenwert für die Härte ist Pflege ein starker Beruf, wenn es ums **Bleiben** in Deutschland geht. Sobald du als Fachkraft arbeitest, ist der Weg zur **Niederlassungserlaubnis** (unbefristeter Aufenthalt) mit anerkanntem Beruf + stabilem Job + Sprache klar — und bei erfüllten Voraussetzungen führt er mit der Zeit auch zur Einbürgerung.

Das ist eine planbarere „Bleiben"-Geschichte als viele akademische Wege: Der Beruf ist gefragt, das Arbeitslosigkeitsrisiko gering, die Aufenthaltsverlängerung unproblematisch. Zum Anerkennungsprozess selbst lies unseren [Anerkennungs-Leitfaden](/de/blog/getting-your-foreign-nursing-qualification-recognized-in-germany-anerkennung-de), zur bezahlten Ausbildung von null unseren [Ausbildungs-Artikel](/de/blog/nursing-ausbildung-in-germany-for-internationals-paid-training-de). *Prüfe die genauen Aufenthaltsvoraussetzungen bei der Ausländerbehörde.*

## Integration & Strategie: erst die Sprache, dann das Netzwerk

Die praktische Reihenfolge:

1. **Stelle die Sprache voran.** Ohne B2 sitzt nichts richtig. Das ist der größte Engpass.
2. **Baue ein Arbeitgeber-Netzwerk auf.** Kliniken/Pflegeketten, die international rekrutieren, können Anerkennung + Sprache + Umzug sponsern. Das beschleunigt den Prozess deutlich.
3. **Wähle das richtige Visum.** §16d zur Anerkennung oder ein Fachkräfteaufenthalt mit Jobangebot — es gibt in Deutschland ein **beschleunigtes Fachkräfteverfahren**. Den Visumsweg mit Jobangebot beschreiben wir im [Arbeitsvisum-Leitfaden](/de/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-de); als Studierender siehe unseren [Zweckwechsel-Artikel](/de/blog/changing-student-visa-to-work-permit-germany-zweckwechsel-de).
4. **Halte deine Erwartungen realistisch.** Bürokratie kann langsam sein; fang früh an und bereite deine Unterlagen vollständig vor.

## Fazit & ehrlicher Rat

Pflege in Deutschland bedeutet **gutes Geld, gesicherte Arbeit und einen klaren Weg zum unbefristeten Aufenthalt** — der Preis dafür sind Schichtdienst, körperliche und emotionale Last und ein ernsthafter Sprachaufwand. Wähle diesen Beruf **nicht wegen des Gehalts, sondern weil du die Arbeit tragen kannst**; dann lohnt sich der Ertrag mehr als genug.

Mein ehrlicher Rat: **Konzentriere dich zuerst auf B2**, knüpfe Kontakt zu einem international rekrutierenden Arbeitgeber und starte die Anerkennung früh. Zahlen und Visumsschritte ändern sich — prüfe das Gehalt am Tarif und Visum/Anerkennung an offizieller Stelle.

*Dieser Inhalt dient der allgemeinen Information Stand Anfang 2026; Gehalt, Tarif, Visums- und Anerkennungsregeln ändern sich. Für verbindliche Angaben prüfe den Tarifvertrag deines Arbeitgebers, die zuständige Anerkennungsstelle, anerkennung-in-deutschland.de und die Ausländerbehörde.*
MD;

        $enBody = <<<'MD'
If you're thinking about becoming a nurse in Germany, you probably have exactly three questions in mind: **what's the pay, how hard is the language, and what's the daily reality?** This article answers exactly that — honestly. Nursing (Pflege) in Germany is a high-demand, stable profession with a clear path to permanent residence — but it's shift work, physically and emotionally demanding. No sugar-coating.

## The job market: nurses are wanted everywhere

One of Germany's biggest structural problems is the **Pflegenotstand** — the shortage of care staff. Hospitals, care homes (Altenheim) and outpatient care services are constantly looking for a **Pflegefachkraft**. As a foreigner that's a rare advantage: finding a job is barely the problem — the problems are **recognition (Anerkennung)** and **language**.

Demand isn't limited to big cities; it's high in smaller towns too. That gives you bargaining power: an employer may sponsor your recognition and language course to bring you to Germany. We covered the two entry routes in detail in our [guide to becoming a nurse in Germany](/en/blog/becoming-a-nurse-in-germany-as-a-foreigner-en).

## Salary: what you earn (entry ~€3,000–3,600 gross)

The most-asked topic. For a fully recognised **Pflegefachkraft**, the entry salary as of 2025 is roughly **€3,000–3,600 gross/month**. The figure varies a lot by employer, federal state and above all whether a **Tarif** (collective agreement, **TVöD-P** in the public sector) applies. *Check the exact figure against your employer's Tarif.*

| Situation | approx. gross/month (2025, verify) | Note |
|---|---|---|
| Ausbildung (training period) | ~€1,100–1,400 | Paid training, rises each year |
| Entry — Pflegefachkraft | ~€3,000–3,600 | depends on TVöD-P / Tarif |
| Experienced / specialist (e.g. ICU) | ~€3,800–4,500+ | with extra qualification |
| Shift/night/weekend supplements | + Zulagen | added on top of gross |

Important: **gross ≠ net.** After taxes and social contributions you keep less, but supplements (night/weekend bonuses) push the total back up. In the public sector (TVöD-P) pay is transparent and rises automatically with your experience level; with private employers the range is wider.

## The language reality: B2 is not negotiable

I won't soften this: **German at B2 level is required in practice.** Some states/employers bring you in at B1 and move you to B2, but to work fully as a Pflegefachkraft, B2 is expected; sometimes a **Fachsprachprüfung** (professional language exam) too.

Why so strict? Because care work is **communication-intensive**: patient history, doctor's orders, medication dosage, handover (Übergabe), talking to relatives — all in German and all with zero margin for error. If your language is weak, the work becomes dangerous and stressful. Investing in the language is the highest-return investment in this profession. For a from-scratch roadmap, see our [guide to learning German](/en/blog/learning-german-from-zero-to-c1-a-roadmap-testdafdsh-en).

## Working conditions: shifts, physical and emotional load (honest)

Now the part nobody says without sugar-coating:

- **Shift work (Schichtdienst):** early, late, nights; including weekends and holidays. Your social life is shaped around it.
- **Physical load:** lifting and turning patients, long hours on your feet; look after your back.
- **Emotional load:** seriously ill patients, death, time pressure, intensity from understaffing. Burnout is a real risk in this profession.
- **Responsibility:** medication, documentation, patient safety — low margin for error.

If you know this, you go in prepared. For many nurses the load is real but meaningful: they see the return in both pay and contribution to society. Still, we're not romanticising it — this is hard work.

## The path to permanent residence (Niederlassung)

In return for the hardship, nursing is a strong profession when it comes to **staying** in Germany. Once you work as a skilled worker, the path to a **Niederlassungserlaubnis** (permanent residence) is clear with a recognised profession + stable job + language — and once the conditions are met, it leads to citizenship over time too.

That's a more predictable "staying" story than many academic routes: the profession is in demand, unemployment risk is low, and residence renewal is straightforward. For the recognition process itself, read our [Anerkennung guide](/en/blog/getting-your-foreign-nursing-qualification-recognized-in-germany-anerkennung-en); for paid training from scratch, our [Ausbildung article](/en/blog/nursing-ausbildung-in-germany-for-internationals-paid-training-en). *Verify the exact residence requirements with the Ausländerbehörde.*

## Integration & strategy: language first, then network

The practical order:

1. **Put the language first.** Nothing settles properly without B2. This is the biggest bottleneck.
2. **Build an employer network.** Hospitals/care chains that recruit internationally can sponsor recognition + language + relocation. That speeds up the process significantly.
3. **Choose the right visa.** §16d for recognition, or a skilled-worker residence with a job offer — Germany has an **accelerated skilled-worker procedure**. We describe the job-offer visa route in the [work-visa guide](/en/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-en); if you're a student, see our [Zweckwechsel article](/en/blog/changing-student-visa-to-work-permit-germany-zweckwechsel-en).
4. **Keep your expectations realistic.** Bureaucracy can be slow; start early and prepare your documents completely.

## Conclusion & honest advice

Nursing in Germany means **good money, secured work and a clear path to permanent residence** — the price is shift work, physical and emotional load, and serious language effort. Choose this profession **not for the salary, but because you can carry the work**; then the return is more than worth it.

My honest advice: **focus on B2 first**, make contact with an internationally recruiting employer, and start the recognition early. Numbers and visa steps change — verify the salary against the Tarif, and the visa/recognition with an official source.

*This content is general information as of early 2026; salary, Tarif, visa and recognition rules change. For binding details, verify your employer's collective agreement, the responsible Anerkennungsstelle, anerkennung-in-deutschland.de and the Ausländerbehörde.*
MD;

        $variants = [
            'tr' => ['slug'=>'working-as-a-nurse-in-germany-salary-language-and-reality',    'title'=>'Almanya\'da Hemşire Olarak Çalışmak: Maaş, Dil ve Gerçek (2026)', 'excerpt'=>'Almanya\'da hemşire olarak çalışmak: giriş maaşı ~3.000–3.600€ brüt (2025, doğrula), B2 dil gerçeği, vardiya ve fiziksel/duygusal yük, ama stabil iş ve kalıcı oturuma giden net yol. Dürüst bir bakış.', 'meta_title'=>'Almanya\'da Hemşire Olarak Çalışmak: Maaş & Dil (2026)', 'meta_description'=>'Almanya\'da hemşirelik maaşı (~3.000–3.600€ brüt, 2025), B2 dil şartı, vardiya ve çalışma koşulları, kalıcı oturuma giden yol — dürüst rehber.', 'body'=>$trBody],
            'de' => ['slug'=>'working-as-a-nurse-in-germany-salary-language-and-reality-de', 'title'=>'Als Pflegekraft in Deutschland arbeiten: Gehalt, Sprache & Realität (2026)', 'excerpt'=>'Als Pflegekraft in Deutschland arbeiten: Einstiegsgehalt ~3.000–3.600€ brutto (2025, prüfen), die B2-Sprach-Realität, Schichtdienst und körperliche/emotionale Last — aber stabiler Job und klarer Weg zur Niederlassung. Ein ehrlicher Blick.', 'meta_title'=>'Als Pflegekraft in Deutschland: Gehalt & Sprache (2026)', 'meta_description'=>'Pflege-Gehalt in Deutschland (~3.000–3.600€ brutto, 2025), B2-Pflicht, Schichtdienst und Arbeitsbedingungen, Weg zur Niederlassung — ehrlicher Leitfaden.', 'body'=>$deBody],
            'en' => ['slug'=>'working-as-a-nurse-in-germany-salary-language-and-reality-en', 'title'=>'Working as a Nurse in Germany: Salary, Language & Reality (2026)', 'excerpt'=>'Working as a nurse in Germany: entry salary ~€3,000–3,600 gross (2025, verify), the B2 language reality, shift work and physical/emotional load — but a stable job and clear path to permanent residence. An honest look.', 'meta_title'=>'Working as a Nurse in Germany: Salary & Language (2026)', 'meta_description'=>'Nursing salary in Germany (~€3,000–3,600 gross, 2025), B2 requirement, shift work and conditions, path to permanent residence — an honest guide.', 'body'=>$enBody],
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
            'working-as-a-nurse-in-germany-salary-language-and-reality',
            'working-as-a-nurse-in-germany-salary-language-and-reality-de',
            'working-as-a-nurse-in-germany-salary-language-and-reality-en',
        ])->delete();
    }
};
