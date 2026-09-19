<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Exmatrikulation — sebepleri, oturum iznine etkisi ve geri dönüş yolları.
 *
 * Grounding (resmî kaynaklar + topluluk, Eylül 2026):
 *   - § 16b AufenthG: oturum izni öğrenim amacına bağlı; ilk verme/uzatmada kural olarak 2 yıl;
 *     yılda 140 tam iş günü çalışma hakkı (HiWi/öğrenci asistanlığı bu hesabın dışında).
 *     gesetze-im-internet.de/aufenthg_2004/__16b.html
 *   - Mezuniyet sonrası iş arama: § 20 Abs. 3 AufenthG — 18 aya kadar, serbest çalışma izinli.
 *     Sadece BAŞARIYLA TAMAMLAYANLAR için; exmatrikule olan öğrenci bu hakka sahip değildir.
 *   - Üniversite kayıt silmeyi ABH'ye bildirir; öğrenim amacı düşünce oturumun dayanağı kalkar.
 *   - Sınav hakları ve "endgültig nicht bestanden" sonuçları Prüfungsordnung'a ve eyalet yüksek
 *     öğretim kanununa bağlıdır — bu yüzden yazıda tek bir ulusal kural olarak SUNULMADI.
 * Topluluk verisi (r/germany, reddit_kb):
 *   - "Ex matriculation=deportation?" — Rückmeldung unutulunca gelen exmatrikulation telefonla
 *     düzeltilebilmiş ("no big deal"); en yaygın ve en kolay geri alınabilir sebep budur.
 *   - "What happens when you fail an exam 3 times" — Prüfungsordnung belirler; sonuç genelde
 *     exmatrikulation + aynı/bağlantılı alanda başka DEVLET üniversitesine kayıt engeli.
 *   - "Exmatriculated after 3 years – should I sue?" — disiplin/kopya kaynaklı vakalar.
 * Yazar: Halil Yaprakli. Kategori: basvuru. Slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'fda77ed3-3095-4c0a-b621-dad819b33710';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'basvuru')->value('id')
            ?? DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Posta kutusundan çıkan mektupta tek bir cümle var: *"Sie sind exmatrikuliert."* Kaydın silindi. İlk düşünce genelde aynı oluyor: **"Vizem gitti mi, sınır dışı mı edileceğim?"**

Cevap, mektubun **hangi sebeple** geldiğine bağlı. Exmatrikulation sebeplerinin bir kısmı bir telefon görüşmesi ve bir ödemeyle geri alınabilir; bir kısmı ise Almanya'daki akademik planını kalıcı olarak değiştirir. Aradaki farkı bilmek, ilk 48 saatte doğru hamleyi yapmanı sağlar.

## Önce sebebi tespit et

| Sebep | Ne kadar ciddi | İlk hamle |
|---|---|---|
| **Rückmeldung / dönem katkı payı ödenmedi** | Düşük — en yaygın sebep, çoğu zaman geri alınabilir | Öğrenci işlerini hemen ara, ödemeyi yap, yazılı başvuru ver |
| **Sağlık sigortası bildirimi düşmüş** | Düşük–orta | Sigortandan yeni bildirim (Versicherungsbescheinigung) iste |
| **Kendi isteğinle çıkış** | Orta — hukuken sorunsuz ama oturumun dayanağı kalkar | Ausländerbehörde'ye önceden bildir |
| **Sınav hakkının bitmesi (endgültig nicht bestanden)** | Yüksek — alan değişimi gerekebilir | Prüfungsamt'la görüş, itiraz süresini kaçırma |
| **Disiplin / kopya** | Yüksek — hukuki süreç | Öğrenci danışmanlığı + avukat |
| **Beurlaubung sonrası geri dönmemek** | Orta | Kayıt yenileme şartlarını sor |

Mektup bir **idari işlemdir** (Verwaltungsakt) ve altında itiraz yolunu gösteren bir bölüm (*Rechtsbehelfsbelehrung*) bulunur. Orada yazan süre — çoğu üniversitede bir ay — senin gerçek takvimin. O süre geçtikten sonra itiraz yolu büyük ölçüde kapanır.

## En yaygın senaryo: unutulan Rückmeldung

Almanya'daki en sık kayıt silme sebebi akademik başarısızlık değil, **unutulan dönem yenileme.** Semesterbeitrag'ı ödemeyi unutursun, hatırlatma e-postası spam'e düşer, birkaç hafta sonra kayıt silme mektubu gelir.

İyi haber: bu sebep genellikle **geri alınabilir.** Toplulukta çok sayıda öğrenci aynı deneyimi paylaşıyor — panikle başlayan süreç, öğrenci işlerini arayıp katkı payını ödeyince ve kısa bir dilekçe verince kapanıyor. Yapman gerekenler:

1. **Aynı gün** öğrenci işleriyle (Studierendensekretariat) iletişime geç.
2. Katkı payını gecikme bedeliyle birlikte öde, dekontu sakla.
3. Kaydın yeniden açılması için yazılı başvuru (*Antrag auf Wiedereinschreibung* veya *Rücknahme der Exmatrikulation*) ver.
4. Bir gecikme sebebin varsa (hastalık, yurt dışı, banka sorunu) belgele.

Burada tek gerçek risk **zaman kaybetmek**: yeni dönem başladıktan sonra geri dönüş çok daha zorlaşır.

## Zor senaryo: sınav hakkının bitmesi

Bir dersi üçüncü kez geçemediğinde ortaya çıkan sonuç Almanca'da *endgültig nicht bestanden* olarak geçer. Buradaki kuralları **ulusal bir yasa değil, bölümünün Prüfungsordnung'u ve eyaletin yüksek öğretim kanunu** belirler — sınav hakkı sayısı, tekrar sınavı imkânı ve istisnalar üniversiteden üniversiteye değişir.

Yine de değişmeyen iki nokta var:

- Sonuç genellikle **kaydın silinmesidir.**
- Etkisi çoğu zaman **tek üniversiteyle sınırlı kalmaz:** aynı veya yakın akraba bir programa başka bir Alman **devlet üniversitesinde** kayıt olmak da engellenebilir. Bu, konuyu "başka şehirde yeniden denerim" cümlesiyle geçiştirilemeyecek kadar ciddi kılar.

Bu durumda sırayla bakılacak yollar:

- **Sonucun usulünü sorgula:** sınav koşullarında hata, mazeret bildirimi (hastalık raporu) zamanında verilmiş mi, tekrar hakkı doğru sayılmış mı.
- **Härtefallantrag / ek deneme:** birçok Prüfungsordnung istisnai bir dördüncü deneme öngörür.
- **İtiraz (Widerspruch):** mektuptaki süre içinde, yazılı ve gerekçeli.
- **Destek noktaları:** Prüfungsamt, Fachstudienberatung, AStA'nın öğrenci hukuk danışmanlığı. Bu hizmetler ücretsizdir ve bu dosyaları sık görürler.

## Asıl mesele: oturum iznin

Uluslararası öğrenci için kritik kısım burası. **§ 16b AufenthG** ile aldığın oturum izni öğrenim amacına bağlıdır — kaydın silinince iznin dayanağı ortadan kalkar. Üniversiteler kayıt silmeyi **Ausländerbehörde'ye bildirir**, yani "belki fark etmez" diye beklemek işe yaramaz.

Doğru davranış tek cümle: **kendin, hemen ve yazılı olarak bildir.** Sistemler sorunu değil, sessizliği daha sert cezalandırır; erken ve belgeli bilgilendirme bu sürecin en ucuz korumasıdır.

Elindeki seçenekler:

| Durum | Yol | Not |
|---|---|---|
| Yeni bir bölüme kayıt yaptırabiliyorsun | § 16b kapsamında öğrenim devam eder | ABH onayı gerekir; amaç değişikliği makul sürede tamamlanabilmeli |
| Meslek eğitimine geçmek istiyorsun | § 16a — Ausbildung | Sözleşme şart; ayrıntılar [Ausbildung'a geçiş yazımızda](/tr/blog/switching-from-study-to-ausbildung-germany-residence-permit) |
| Mezun oldun, sonra kaydın kapandı | § 20 Abs. 3 — 18 aya kadar iş arama | **Sadece başarıyla tamamlayanlar için**; ayrıntı: [iş arama vizesi rehberi](/tr/blog/germany-job-seeker-visa-2026-complete-guide-for-graduates) |
| İş teklifin var | Çalışma iznine geçiş (Zweckwechsel) | Şartlar: [Zweckwechsel yazımız](/tr/blog/changing-student-visa-to-work-permit-germany-zweckwechsel) |
| Hiçbiri yok | Çıkış süresi verilir | Danışmanlık şart; gönüllü çıkış, yasak kaydından iyidir |

Önemli bir yanlış anlamayı düzeltelim: **18 aylık iş arama izni mezunlar içindir.** Diploma almadan kaydı silinen bir öğrenci bu hakka sahip değildir — bu yüzden "önce mezuniyeti kurtar, sonra kariyeri düşün" sırası bu kadar önemli.

Oturum başvurun ABH'de bekliyorsa ve süre daralıyorsa, koruma mekanizmasını [Ausländerbehörde gecikmesi yazımızda](/tr/blog/auslanderbehorde-delay-fiktionsbescheinigung-untatigkeitsklage) anlattık.

## Sessizce değişen yan etkiler

Kayıt silindiğinde sadece öğrencilik statün bitmez; ona bağlı birkaç şey de biter:

- **Sağlık sigortası:** öğrenci tarifesi sona erer; gönüllü sigortalılığa veya başka bir modele geçmen gerekir ve **prim ciddi biçimde artar.** Sağlayıcı karşılaştırması için [sigorta rehberimiz](/tr/blog/germany-student-health-insurance-2026-tk-vs-dak-vs-mawista-vs).
- **Çalışma hakkı:** öğrencilere tanınan **yılda 140 tam iş günü** kuralı öğrenci statüsüne bağlıdır; statü bitince bu izin de dayanaksız kalır.
- **Semesterticket, yurt sözleşmesi, kütüphane ve öğrenci indirimleri:** yurt sözleşmelerinin çoğu kayıtlı öğrenci olma şartına bağlıdır.
- **Vergi ve gelir tarafı:** öğrenciye özel sigorta muafiyetleri düşer; ayrıntılar [vergi ve sigorta rehberimizde](/tr/blog/2026-guide-to-tax-and-health-insurance-for-students-in-germany).

## Geri dönüş planı: 4 haftalık takvim

1. **Gün 1–2:** Mektubu oku, itiraz süresini bir yere yaz. Öğrenci işlerini ara, sebebi netleştir.
2. **Gün 2–5:** Geri alınabilir bir sebepse (ödeme, sigorta bildirimi) evrakı tamamla ve yazılı başvuruyu ver.
3. **İlk hafta:** Ausländerbehörde'ye durumu yazılı bildir; hangi çözümü hedeflediğini yaz (yeni bölüm, Ausbildung, iş).
4. **1–3. hafta:** Akademik alternatifleri araştır: aynı üniversitede yakın bölüm, başka eyalette FH programı, Ausbildung. Bölüm/başvuru takvimlerini kontrol et.
5. **4. hafta:** Kararını belgele — kabul yazısı, sözleşme veya başvuru kanıtı ABH dosyana girsin.

FH'lerin (uygulamalı bilimler üniversiteleri) başvuru takvimleri ve kabul koşulları üniversitelerden farklıdır; alan değiştirmek zorunda kaldığında en hızlı geri dönüş kapısı çoğu zaman buradan açılır.

## Sıkça Sorulanlar

### Exmatrikulation sınır dışı edilmek demek mi?
Hayır. Kayıt silme akademik bir işlemdir; sınır dışı ise ayrı bir idari süreçtir. Ama § 16b iznin dayanağı kalktığı için **harekete geçmezsen** çıkış yükümlülüğü doğabilir. Belirleyici olan, ABH'ye zamanında ve makul bir plan sunup sunmadığın.

### Bir dersten üç kez kaldım. Aynı bölümü başka üniversitede okuyabilir miyim?
Genelde hayır: "endgültig nicht bestanden" sonucu, aynı veya yakından ilişkili programa başka bir Alman devlet üniversitesinde kaydı da engelleyebilir. Kesin kapsam bölümünün Prüfungsordnung'una ve eyalet mevzuatına bağlıdır — kararı okumadan alan değiştirme planı yapma.

### Rückmeldung'u unuttum, kaydım silindi. Geri alınır mı?
Çoğu zaman evet. Katkı payını gecikme bedeliyle öde ve kaydın yeniden açılması için yazılı başvuru ver. Bunu **aynı hafta** yapmak, bir ay sonra yapmaktan çok daha yüksek olasılıkla sonuç verir.

### Kendi isteğimle kaydımı sildirirsem oturumum ne olur?
Öğrenim amacı sona erdiği için izninin dayanağı düşer. Bunu **önceden** ABH'ye bildir ve hangi statüye geçeceğini (yeni bölüm, Ausbildung, iş) göster. Habersiz çıkış, dosyanı en çok zorlaştıran şeydir.

### Üniversiteye dava açabilir miyim?
Sınav ve kayıt işlemlerine karşı itiraz ve idare mahkemesi yolu açıktır. Ama süreler kısadır ve dosya teknikitir: önce Widerspruch, gerekiyorsa dava. AStA'nın ücretsiz hukuk danışmanlığıyla başlamak en mantıklı ilk adımdır.

### Tekrar kayıt olursam oturum iznim otomatik geri gelir mi?
Otomatik değil. Yeni kayıt belgesini (Immatrikulationsbescheinigung) ABH'ye sunman ve iznin yeni amaç üzerinden düzenlenmesini talep etmen gerekir. Ara dönemde statünün nasıl korunacağını mutlaka sor.

## Sonuç ve dürüst tavsiye

Exmatrikulation mektubu kötü bir haberdir ama çoğu zaman sanıldığı kadar kesin değildir. En yaygın sebebi — unutulan dönem yenileme — bir ödeme ve bir dilekçeyle kapanır. En ağır sebebi — sınav hakkının bitmesi — genellikle alan değiştirmeyi gerektirir, ama Almanya'daki eğitim hayatını bitirmek zorunda değildir.

Belirleyici olan iki şey: **mektuptaki itiraz süresini kaçırmamak** ve **Ausländerbehörde'yi kendin bilgilendirmek.** Bu ikisini yapan öğrencilerin çoğu, bir dönem kaybıyla yola devam eder. Bu ikisini atlayanlar için asıl sorun akademik değil, hukuki hale gelir.

*Bu yazıdaki kurallar 2026 Eylül itibarıyla geçerlidir; sınav hakları ve kayıt kuralları eyalete, üniversiteye ve bölüm yönetmeliğine göre değişir. Kendi Prüfungsordnung'unu ve şehrinin Ausländerbehörde sayfasını esas al.*
MD;

        $deBody = <<<'MD'
Im Briefkasten liegt ein Schreiben mit einem einzigen entscheidenden Satz: *„Sie sind exmatrikuliert."* Der erste Gedanke ist meistens derselbe: **„Ist mein Aufenthaltstitel weg? Werde ich abgeschoben?"**

Die Antwort hängt davon ab, **aus welchem Grund** der Brief kam. Manche Gründe lassen sich mit einem Anruf und einer Überweisung zurücknehmen; andere verändern deinen akademischen Weg in Deutschland dauerhaft. Diesen Unterschied zu kennen, entscheidet über die richtigen ersten 48 Stunden.

## Zuerst den Grund bestimmen

| Grund | Wie ernst | Erster Schritt |
|---|---|---|
| **Rückmeldung / Semesterbeitrag nicht gezahlt** | gering — der häufigste Grund, meist rücknehmbar | Sofort das Studierendensekretariat kontaktieren, zahlen, schriftlichen Antrag stellen |
| **Nachweis der Krankenversicherung entfallen** | gering bis mittel | Neue Versicherungsbescheinigung anfordern |
| **Exmatrikulation auf eigenen Antrag** | mittel — rechtlich unproblematisch, aber der Aufenthaltszweck entfällt | Ausländerbehörde vorab informieren |
| **Endgültig nicht bestandene Prüfung** | hoch — oft ist ein Fachwechsel nötig | Prüfungsamt kontaktieren, Rechtsbehelfsfrist beachten |
| **Ordnungsrechtliche Gründe / Täuschung** | hoch — rechtliches Verfahren | Studienberatung und Anwalt |
| **Nach Beurlaubung nicht zurückgemeldet** | mittel | Bedingungen der Wiedereinschreibung erfragen |

Der Bescheid ist ein **Verwaltungsakt** und enthält eine *Rechtsbehelfsbelehrung*. Die dort genannte Frist — an den meisten Hochschulen ein Monat — ist dein eigentlicher Kalender. Danach ist der Rechtsweg weitgehend versperrt.

## Der häufigste Fall: die vergessene Rückmeldung

Der häufigste Exmatrikulationsgrund in Deutschland ist nicht akademisches Scheitern, sondern die **vergessene Rückmeldung.** Der Semesterbeitrag bleibt unbezahlt, die Erinnerungsmail landet im Spam, Wochen später kommt der Bescheid.

Die gute Nachricht: Dieser Grund lässt sich meist **rückgängig machen.** In der Community berichten viele Studierende von genau diesem Ablauf — Panik am Anfang, erledigt nach einem Anruf im Sekretariat, der Zahlung und einem kurzen Antrag. Was zu tun ist:

1. **Noch am selben Tag** das Studierendensekretariat kontaktieren.
2. Den Beitrag samt Säumniszuschlag zahlen und den Beleg aufbewahren.
3. Einen schriftlichen *Antrag auf Wiedereinschreibung* beziehungsweise auf *Rücknahme der Exmatrikulation* stellen.
4. Einen Verzögerungsgrund (Krankheit, Auslandsaufenthalt, Bankproblem) belegen, falls vorhanden.

Das eigentliche Risiko hier ist **verlorene Zeit**: Ist das neue Semester erst einmal angelaufen, wird die Rückkehr deutlich schwerer.

## Der schwierige Fall: endgültig nicht bestanden

Wer eine Prüfung im dritten Versuch nicht besteht, erhält das Ergebnis *endgültig nicht bestanden*. Die Regeln dazu stehen nicht in einem Bundesgesetz, sondern in **der Prüfungsordnung deines Studiengangs und im Hochschulgesetz deines Bundeslandes** — Versuchszahl, Wiederholungsmöglichkeiten und Ausnahmen unterscheiden sich von Hochschule zu Hochschule.

Zwei Punkte gelten dennoch fast überall:

- Die Folge ist in der Regel die **Exmatrikulation.**
- Die Wirkung bleibt oft **nicht auf eine Hochschule beschränkt:** Auch die Einschreibung in denselben oder einen eng verwandten Studiengang an einer anderen **staatlichen** Hochschule kann gesperrt sein. „Ich versuche es einfach in einer anderen Stadt" ist deshalb keine tragfähige Antwort.

Die Wege, die in dieser Lage zu prüfen sind:

- **Verfahrensfehler prüfen:** Prüfungsbedingungen, rechtzeitig eingereichter Rücktritt wegen Krankheit, korrekte Zählung der Versuche.
- **Härtefallantrag / zusätzlicher Versuch:** Viele Prüfungsordnungen sehen einen ausnahmsweisen vierten Versuch vor.
- **Widerspruch:** innerhalb der Frist aus dem Bescheid, schriftlich und begründet.
- **Anlaufstellen:** Prüfungsamt, Fachstudienberatung, Rechtsberatung des AStA — kostenlos und mit Routine in genau diesen Fällen.

## Der Kern der Sache: dein Aufenthaltstitel

Für internationale Studierende ist das der kritische Teil. Die Aufenthaltserlaubnis nach **§ 16b AufenthG** ist an den Studienzweck gebunden — fällt die Immatrikulation weg, entfällt die Grundlage. Hochschulen **melden Exmatrikulationen der Ausländerbehörde**; abzuwarten und zu hoffen, dass es niemand bemerkt, funktioniert nicht.

Die richtige Haltung in einem Satz: **selbst, sofort und schriftlich informieren.** Behörden sanktionieren Schweigen härter als das Problem selbst; eine frühe, dokumentierte Meldung ist der günstigste Schutz in diesem Verfahren.

Deine Optionen:

| Situation | Weg | Hinweis |
|---|---|---|
| Du kannst dich in einen neuen Studiengang einschreiben | Studium nach § 16b läuft weiter | Zustimmung der ABH nötig; der Zweck muss in angemessener Zeit erreichbar bleiben |
| Du willst in eine Ausbildung wechseln | § 16a — Berufsausbildung | Vertrag erforderlich; Details im [Ausbildungs-Artikel](/de/blog/switching-from-study-to-ausbildung-germany-residence-permit-de) |
| Du hast abgeschlossen, danach endete die Immatrikulation | § 20 Abs. 3 — bis zu 18 Monate Arbeitsplatzsuche | **Nur nach erfolgreichem Abschluss**; siehe [Ratgeber zur Arbeitsplatzsuche](/de/blog/germany-job-seeker-visa-2026-complete-guide-for-graduates-de) |
| Du hast ein Jobangebot | Wechsel in eine Beschäftigungserlaubnis | Voraussetzungen im [Zweckwechsel-Artikel](/de/blog/changing-student-visa-to-work-permit-germany-zweckwechsel-de) |
| Nichts davon trifft zu | Ausreisefrist wird gesetzt | Beratung einholen; freiwillige Ausreise ist besser als ein Einreiseverbot |

Ein verbreitetes Missverständnis gehört korrigiert: **Die 18 Monate zur Arbeitsplatzsuche gelten für Absolventinnen und Absolventen.** Wer ohne Abschluss exmatrikuliert wird, hat diesen Anspruch nicht — deshalb ist die Reihenfolge „erst den Abschluss retten, dann die Karriere planen" so entscheidend.

Wenn dein Verlängerungsantrag bei der ABH liegt und die Zeit knapp wird, erklärt unser Artikel zu [Verzögerungen der Ausländerbehörde](/de/blog/auslanderbehorde-delay-fiktionsbescheinigung-untatigkeitsklage-de) den Schutzmechanismus.

## Nebenwirkungen, die leise eintreten

Mit der Exmatrikulation endet nicht nur der Studierendenstatus, sondern auch einiges, was daran hängt:

- **Krankenversicherung:** Der studentische Tarif endet; du musst in die freiwillige Versicherung oder ein anderes Modell wechseln, und **der Beitrag steigt deutlich.** Anbietervergleich: [Versicherungsratgeber](/de/blog/germany-student-health-insurance-2026-tk-vs-dak-vs-mawista-vs-de).
- **Arbeitserlaubnis:** Die Regel von **140 vollen Arbeitstagen pro Jahr** hängt am Studierendenstatus; entfällt der Status, entfällt die Grundlage.
- **Semesterticket, Wohnheimvertrag, Bibliothek und Vergünstigungen:** Wohnheimverträge setzen fast immer eine gültige Immatrikulation voraus.
- **Steuern und Einkommen:** Studentische Befreiungen entfallen; Details im [Steuer- und Versicherungsratgeber](/de/blog/2026-guide-to-tax-and-health-insurance-for-students-in-germany-de).

## Rückkehrplan: vier Wochen

1. **Tag 1–2:** Bescheid lesen, Rechtsbehelfsfrist notieren. Sekretariat anrufen, Grund klären.
2. **Tag 2–5:** Bei einem rücknehmbaren Grund (Zahlung, Versicherungsnachweis) Unterlagen vervollständigen und Antrag stellen.
3. **Erste Woche:** Die Ausländerbehörde schriftlich informieren und die angestrebte Lösung benennen (neuer Studiengang, Ausbildung, Beschäftigung).
4. **Woche 1–3:** Akademische Alternativen prüfen: verwandter Studiengang an derselben Hochschule, FH-Programm in einem anderen Bundesland, Ausbildung. Bewerbungsfristen kontrollieren.
5. **Woche 4:** Entscheidung belegen — Zulassung, Vertrag oder Bewerbungsnachweis gehört in deine ABH-Akte.

Fachhochschulen haben andere Fristen und Zulassungsbedingungen als Universitäten; wenn ein Fachwechsel unvermeidlich ist, führt der schnellste Weg zurück häufig über sie.

## Häufige Fragen

### Bedeutet Exmatrikulation Abschiebung?
Nein. Die Exmatrikulation ist ein hochschulrechtlicher Vorgang, die Abschiebung ein davon getrenntes Verfahren. Weil aber die Grundlage deines § 16b-Titels entfällt, kann eine Ausreisepflicht entstehen, **wenn du nicht handelst.** Entscheidend ist, ob du der ABH rechtzeitig einen tragfähigen Plan vorlegst.

### Ich bin dreimal durchgefallen. Kann ich dasselbe Fach anderswo studieren?
In der Regel nicht: Ein endgültiges Nichtbestehen kann die Einschreibung in denselben oder einen eng verwandten Studiengang an anderen staatlichen Hochschulen sperren. Der genaue Umfang steht in deiner Prüfungsordnung und im Landesrecht — lies den Bescheid, bevor du umplanst.

### Ich habe die Rückmeldung vergessen. Lässt sich das heilen?
Meistens ja. Beitrag samt Säumniszuschlag zahlen und die Wiedereinschreibung schriftlich beantragen. **In derselben Woche** gestellt, ist die Chance deutlich höher als einen Monat später.

### Was passiert mit meinem Aufenthalt, wenn ich mich selbst exmatrikuliere?
Der Studienzweck endet, damit entfällt die Grundlage des Titels. Informiere die ABH **vorab** und zeige, in welchen Status du wechseln willst. Ein unangekündigter Abgang erschwert deine Akte am meisten.

### Kann ich gegen die Hochschule klagen?
Gegen Prüfungs- und Exmatrikulationsentscheidungen stehen Widerspruch und Verwaltungsgerichtsweg offen. Die Fristen sind kurz und die Materie technisch: zuerst Widerspruch, dann gegebenenfalls Klage. Der Einstieg über die kostenlose AStA-Rechtsberatung ist der sinnvollste erste Schritt.

### Kommt mein Titel automatisch zurück, wenn ich mich wieder einschreibe?
Nicht automatisch. Du musst die neue Immatrikulationsbescheinigung bei der ABH einreichen und die Erteilung beziehungsweise Fortführung des Titels beantragen. Frage ausdrücklich, wie dein Status in der Zwischenzeit gesichert wird.

## Fazit und ehrlicher Rat

Ein Exmatrikulationsbescheid ist eine schlechte Nachricht, aber selten so endgültig, wie er sich liest. Der häufigste Grund — die vergessene Rückmeldung — erledigt sich mit einer Zahlung und einem Antrag. Der schwerste Grund — das endgültige Nichtbestehen — erzwingt meist einen Fachwechsel, aber nicht das Ende deines Studiums in Deutschland.

Zwei Dinge entscheiden: **die Frist im Bescheid einhalten** und **die Ausländerbehörde selbst informieren.** Wer beides tut, kommt meist mit einem verlorenen Semester davon. Wer beides auslässt, macht aus einem akademischen Problem ein rechtliches.

*Die dargestellten Regeln gelten mit Stand September 2026; Prüfungsrechte und Einschreibungsregeln unterscheiden sich nach Bundesland, Hochschule und Prüfungsordnung. Maßgeblich sind deine eigene Prüfungsordnung und die Seite deiner Ausländerbehörde.*
MD;

        $enBody = <<<'MD'
There is one sentence in the letter that matters: *"Sie sind exmatrikuliert."* You have been de-registered. The first thought is usually the same: **"Is my residence permit gone? Will I be deported?"**

The answer depends on **why** the letter came. Some grounds for de-registration can be reversed with a phone call and a payment; others permanently change your academic path in Germany. Knowing the difference is what makes the first 48 hours count.

## First, identify the ground

| Ground | How serious | First move |
|---|---|---|
| **Rückmeldung / semester fee unpaid** | Low — the most common ground, usually reversible | Call the registrar's office today, pay, file a written request |
| **Health insurance notification lapsed** | Low to medium | Ask your insurer for a fresh Versicherungsbescheinigung |
| **De-registration at your own request** | Medium — legally clean, but your residence purpose ends | Notify the Ausländerbehörde in advance |
| **Final failure of an exam (endgültig nicht bestanden)** | High — a change of subject is often required | Contact the Prüfungsamt; do not miss the appeal deadline |
| **Disciplinary grounds / cheating** | High — a legal process | Student advice service plus a lawyer |
| **Not returning after a leave semester** | Medium | Ask about re-enrolment conditions |

The notice is an **administrative act** and carries a *Rechtsbehelfsbelehrung* — instructions on how to challenge it. The deadline stated there, one month at most universities, is your real calendar. After it passes, the route to appeal largely closes.

## The most common case: the forgotten Rückmeldung

The most frequent cause of de-registration in Germany is not academic failure but a **missed re-registration.** The semester fee goes unpaid, the reminder lands in spam, and weeks later the notice arrives.

The good news: this ground is usually **reversible.** Community threads are full of exactly this arc — panic at first, resolved after a call to the registrar, the payment and a short written request. What to do:

1. Contact the Studierendensekretariat **the same day**.
2. Pay the fee including any late charge and keep the receipt.
3. File a written request for re-enrolment (*Antrag auf Wiedereinschreibung* or *Rücknahme der Exmatrikulation*).
4. Document any reason for the delay: illness, travel, a banking problem.

The only real risk here is **lost time**: once the new semester is under way, coming back becomes considerably harder.

## The hard case: final failure of an exam

Failing a course on the third attempt produces the result *endgültig nicht bestanden*. The rules are set not by federal law but by **your programme's examination regulations (Prüfungsordnung) and your state's higher education act** — the number of attempts, repeat options and exceptions differ between universities.

Two points hold almost everywhere:

- The consequence is normally **de-registration.**
- The effect often **does not stop at one university:** enrolling in the same or a closely related programme at another German **public** university can also be barred. "I will just try in another city" is therefore not a workable answer.

The routes worth checking in this situation:

- **Test the procedure:** exam conditions, whether a withdrawal on medical grounds was filed in time, whether attempts were counted correctly.
- **Hardship application / extra attempt:** many examination regulations provide for an exceptional fourth attempt.
- **Widerspruch (formal objection):** within the deadline in the notice, in writing and with reasons.
- **Where to get help:** the Prüfungsamt, subject-specific student advice, and the AStA's legal counselling — free, and familiar with these files.

## The heart of the matter: your residence permit

For international students this is the critical part. A residence permit under **Section 16b AufenthG** is tied to the purpose of study — once your enrolment ends, the basis for the permit falls away. Universities **report de-registrations to the Ausländerbehörde**, so waiting and hoping nobody notices does not work.

The right posture in one sentence: **inform them yourself, immediately, in writing.** Systems punish silence more harshly than the underlying problem; early, documented disclosure is the cheapest protection this procedure offers.

Your options:

| Situation | Route | Note |
|---|---|---|
| You can enrol in a new programme | Study continues under Section 16b | The authority must consent; the purpose must remain achievable within a reasonable time |
| You want to switch to vocational training | Section 16a — Ausbildung | A contract is required; details in our [Ausbildung switch guide](/en/blog/switching-from-study-to-ausbildung-germany-residence-permit-en) |
| You graduated, then enrolment ended | Section 20(3) — up to 18 months to find work | **Only after successful completion**; see our [job-seeker guide](/en/blog/germany-job-seeker-visa-2026-complete-guide-for-graduates-en) |
| You have a job offer | Switch to a work permit | Requirements in our [Zweckwechsel article](/en/blog/changing-student-visa-to-work-permit-germany-zweckwechsel-en) |
| None of the above | A departure deadline is set | Get advice; voluntary departure beats an entry ban |

One widespread misunderstanding deserves correcting: **the 18-month job-search permit is for graduates.** A student de-registered without a degree has no such entitlement — which is exactly why the order "rescue the degree first, plan the career second" matters so much.

If your extension application is sitting with the authority and time is running short, our article on [Ausländerbehörde delays](/en/blog/auslanderbehorde-delay-fiktionsbescheinigung-untatigkeitsklage-en) explains the protection that applies.

## The side effects that arrive quietly

De-registration ends more than your student status; several things attached to it end too:

- **Health insurance:** the student tariff ends; you move to voluntary insurance or another model, and **the premium rises substantially.** For a provider comparison, see our [insurance guide](/en/blog/germany-student-health-insurance-2026-tk-vs-dak-vs-mawista-vs-en).
- **Work allowance:** the **140 full working days per year** rule is tied to student status; without the status, the basis disappears.
- **Semester ticket, dorm contract, library and student discounts:** most dorm contracts require valid enrolment.
- **Tax and income side:** student-specific exemptions fall away; details in our [tax and insurance guide](/en/blog/2026-guide-to-tax-and-health-insurance-for-students-in-germany-en).

## A four-week plan to come back

1. **Days 1–2:** Read the notice and write down the appeal deadline. Call the registrar and pin down the ground.
2. **Days 2–5:** If the ground is reversible (payment, insurance proof), complete the paperwork and file the request.
3. **Week 1:** Notify the Ausländerbehörde in writing and name the solution you are pursuing: new programme, Ausbildung, employment.
4. **Weeks 1–3:** Research academic alternatives: a related programme at the same university, a university of applied sciences in another state, vocational training. Check application deadlines.
5. **Week 4:** Document your decision — an offer letter, contract or proof of application belongs in your file with the authority.

Universities of applied sciences (Fachhochschulen) run on different deadlines and admission rules than universities; when a change of subject becomes unavoidable, they are often the fastest way back in.

## Frequently asked questions

### Does Exmatrikulation mean deportation?
No. De-registration is a university matter; deportation is a separate administrative process. But because the basis for your Section 16b permit falls away, an obligation to leave can arise **if you do nothing.** What matters is whether you present the authority with a workable plan in time.

### I failed a course three times. Can I study the same subject elsewhere?
Usually not: a final failure can bar enrolment in the same or a closely related programme at other public universities in Germany. The exact scope sits in your examination regulations and state law — read the decision before you replan.

### I forgot the Rückmeldung and was de-registered. Can it be undone?
Most of the time, yes. Pay the fee plus any late charge and request re-enrolment in writing. Doing this **in the same week** is far more likely to work than a month later.

### What happens to my permit if I de-register voluntarily?
The purpose of your stay ends, so the basis for the permit falls away. Tell the authority **in advance** and show which status you intend to move into: a new programme, training, or employment. An unannounced exit is what complicates a file most.

### Can I take the university to court?
Objection and administrative court proceedings are available against examination and de-registration decisions. Deadlines are short and the material is technical: Widerspruch first, litigation if necessary. Starting with the AStA's free legal counselling is the most sensible first step.

### If I re-enrol, does my residence permit come back automatically?
Not automatically. You must submit the new enrolment certificate to the authority and apply for the permit to be issued or continued. Ask explicitly how your status is secured in the meantime.

## Conclusion and honest advice

A de-registration notice is bad news, but it is rarely as final as it reads. Its most common cause — a forgotten re-registration — is settled with a payment and a request. Its most serious cause — a final exam failure — usually forces a change of subject, but not the end of studying in Germany.

Two things decide the outcome: **meeting the deadline in the notice** and **informing the Ausländerbehörde yourself.** Students who do both usually continue with one lost semester. Those who skip both turn an academic problem into a legal one.

*The rules described are current as of September 2026; examination rights and enrolment rules vary by state, university and programme regulations. Your own Prüfungsordnung and your local Ausländerbehörde's page are the authoritative sources.*
MD;

        $variants = [
            'tr' => [
                'slug' => 'exmatrikulation-germany-causes-residence-permit-and-coming-back',
                'title' => 'Exmatrikulation: Kaydın Silinmesi — Sebepleri, Oturuma Etkisi ve Geri Dönüş',
                'excerpt' => 'Kayıt silme mektubu geldiğinde ilk 48 saat belirleyici. Unutulan Rückmeldung nasıl geri alınır, üçüncü sınav hakkı bitince ne olur, § 16b oturum iznine etkisi nedir ve elindeki gerçek seçenekler hangileri — dört haftalık geri dönüş planıyla.',
                'meta_title' => 'Exmatrikulation: Sebepleri, Oturum İznine Etkisi ve Çözüm',
                'meta_description' => 'Almanya\'da kaydın silinmesi: Rückmeldung nasıl geri alınır, endgültig nicht bestanden sonuçları, § 16b oturum izni etkisi ve geri dönüş planı (2026).',
                'body' => $trBody,
            ],
            'de' => [
                'slug' => 'exmatrikulation-germany-causes-residence-permit-and-coming-back-de',
                'title' => 'Exmatrikuliert: Gründe, Folgen für den Aufenthaltstitel und der Weg zurück',
                'excerpt' => 'Wenn der Exmatrikulationsbescheid kommt, entscheiden die ersten 48 Stunden. Wie sich eine vergessene Rückmeldung heilen lässt, was nach dem endgültigen Nichtbestehen passiert, welche Folgen § 16b AufenthG hat — mit einem Vier-Wochen-Plan zurück ins Studium.',
                'meta_title' => 'Exmatrikulation: Gründe, Aufenthaltstitel und Rückkehr',
                'meta_description' => 'Exmatrikuliert in Deutschland: Rückmeldung heilen, endgültig nicht bestanden, Folgen für den § 16b-Aufenthaltstitel und ein konkreter Rückkehrplan (2026).',
                'body' => $deBody,
            ],
            'en' => [
                'slug' => 'exmatrikulation-germany-causes-residence-permit-and-coming-back-en',
                'title' => 'De-registered in Germany: Causes, Residence Permit Consequences and the Way Back',
                'excerpt' => 'When the de-registration notice arrives, the first 48 hours decide the outcome. How to reverse a missed Rückmeldung, what happens after a final exam failure, how Section 16b affects your permit, and the options you actually have — with a four-week plan to return.',
                'meta_title' => 'Exmatrikulation in Germany: Causes, Permit Impact, Way Back',
                'meta_description' => 'De-registered in Germany: reversing a missed Rückmeldung, final exam failure, Section 16b residence permit consequences and a concrete plan to return (2026).',
                'body' => $enBody,
            ],
        ];

        foreach ($variants as $locale => $v) {
            $html = Str::markdown($v['body'], ['html_input' => 'allow', 'allow_unsafe_links' => false]);
            $payload = [
                'locale' => $locale, 'translation_group_id' => $groupId, 'user_id' => $userId, 'category_id' => $categoryId,
                'title' => $v['title'], 'excerpt' => Str::limit($v['excerpt'], 250, '…'),
                'content_md' => $v['body'], 'content_html' => $html,
                'meta_title' => $v['meta_title'], 'meta_description' => Str::limit($v['meta_description'], 158, '…'),
                'reading_minutes' => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
                'is_published' => true, 'published_at' => now(),
            ];
            $existing = Post::where('slug', $v['slug'])->first();
            $existing ? $existing->update($payload) : Post::create($payload + ['slug' => $v['slug']]);
        }
    }

    public function down(): void
    {
        Post::whereIn('slug', [
            'exmatrikulation-germany-causes-residence-permit-and-coming-back',
            'exmatrikulation-germany-causes-residence-permit-and-coming-back-de',
            'exmatrikulation-germany-causes-residence-permit-and-coming-back-en',
        ])->delete();
    }
};
