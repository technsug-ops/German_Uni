<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da Otomotiv Mühendisliği (Fahrzeugtechnik) okumak.
 * Doğrulandı: Fahrzeugtechnik = Maschinenbau'nun uzmanlaşması; alt dallar araç dinamiği, powertrain,
 * E-Mobilität, otonom/ADAS, gömülü yazılım. Sektör EV + yazılım-tanımlı araca kayıyor (geçişte).
 * Güçlü okullar RWTH Aachen (ika), TUM, KIT, TU Braunschweig (NFF), Uni Stuttgart (FKFS), HS Esslingen,
 * TH Ingolstadt. Devler VW/BMW/Mercedes + Bosch/Continental/ZF/Schaeffler. Duales Studium yaygın.
 * Sperrkonto 2026 ~992€/ay = ~11.904€/yıl; Blue Card ~50.700€ / darboğaz ~45.934€. Hepsi hedge'li.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = '7d7c0000-4444-4b2e-8c30-ee43dd52aa01';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya, dünya otomotiv sanayisinin **kalbi**: **Volkswagen Grubu (VW/Audi/Porsche), BMW ve Mercedes-Benz** gibi üreticiler ve **Bosch, Continental, ZF, Schaeffler** gibi dev tedarikçiler burada. Otomotiv, ülkenin en büyük sanayi dalı ve **Fahrzeugtechnik** (araç/otomotiv mühendisliği) okumak, uluslararası bir öğrenci için hem güçlü hem de heyecan verici bir tercih. Ama dürüst gerçeği baştan söyleyeyim: sektör tarihinin en büyük dönüşümünü yaşıyor — elektrikli araç, otonom sürüş ve yazılım-tanımlı araca geçişte. Bu geçiş, doğru uzmanlaşmayı seçen için büyük bir fırsat. Bu yazıda bir yabancı öğrenci olarak Almanya'da otomotiv mühendisliği okumanın nasıl işlediğini baştan sona anlatıyorum.

## Fahrzeugtechnik nedir? Maschinenbau ile ilişkisi

Fahrzeugtechnik, çoğu zaman **Maschinenbau**'nun (makine mühendisliği) bir uzmanlaşması olarak okutulur. Yani sağlam bir mühendislik temeli (mekanik, termodinamik, malzeme, matematik) üzerine araç odaklı dersler eklenir. Otomotiv mühendisliği tek bir dar konu değil; çok sayıda alt dala açılan geniş bir alan:

- **Araç dinamiği & şasi:** süspansiyon, fren, direksiyon, sürüş güvenliği ve konforu.
- **Güç aktarma (powertrain):** motor, şanzıman, tahrik sistemleri — artık ağırlıkla elektrikli tahrik.
- **Elektrikli araç (E-Mobilität):** batarya sistemleri, elektrik motoru, şarj teknolojisi, termal yönetim.
- **Otonom sürüş & ADAS:** sürücü destek sistemleri, sensör füzyonu, sürüş algoritmaları.
- **Gömülü yazılım & mekatronik:** aracın "beyni" haline gelen kontrol yazılımı ve elektronik.
- **Hafif tasarım & üretim:** malzeme optimizasyonu, imalat ve montaj süreçleri.

Genel mühendislik eğitiminin nasıl kurulduğunu ve temel derslerin neye benzediğini merak ediyorsan, [Almanya'da mühendislik okumak: yabancı öğrenci rehberi](/tr/blog/studying-engineering-in-germany-as-a-foreigner) yazısı iyi bir başlangıç noktası; otomotiv, bu temelin üzerine kurulan bir uzmanlaşma.

## Büyük dönüşüm: EV, otonom ve yazılım-tanımlı araç

Bunu dürüstçe söylemek gerekiyor: klasik "içten yanmalı motor mühendisi" rolleri **daralıyor**, buna karşılık **elektrikli araç, batarya, yazılım ve mekatronik** tarafı hızla büyüyor. Sektör; elektrikli araç, otonom sürüş ve **yazılım-tanımlı araç (software-defined vehicle)** üçlüsüne doğru kayıyor. Bu bir geçiş dönemi — belirsizlik de var, ağrılı yeniden yapılanmalar da. Ama nitelikli mühendis, özellikle yazılım ve EV tarafında, hâlâ çok aranıyor.

Türk öğrenciye pratik tavsiyem: klasik makine tarafını sevsen bile, ders/proje seçimlerinde **EV, gömülü yazılım ve mekatroniğe** ağırlık ver. Bu, seni geleceğin talep gören profiline yaklaştırır. Sektörün nereye gittiğini ve kariyer tablosunu [Almanya'da otomotiv mühendisliğiyle çalışmak: kariyer ve maaş](/tr/blog/working-in-automotive-engineering-in-germany-careers-and-salary) yazısında derinlemesine ele alıyorum.

## Tanınan okullar

Otomotiv mühendisliğinde Almanya'nın çok güçlü bir okul havuzu var. Zirvede araştırma üniversiteleri, sanayiye yakın uygulamalı yüksekokullar (HAW) bir arada:

| Okul | Tür | Öne çıkan |
|---|---|---|
| **RWTH Aachen** | Kamu TU | Otomotiv/mühendislikte zirve; **ika** (araç mühendisliği enstitüsü); sanayiyle çok güçlü bağ |
| **TU München (TUM)** | Kamu TU | En prestijli teknik üniversitelerden; güçlü araç ve mekatronik araştırması |
| **Uni Stuttgart** | Kamu üniversite | **FKFS** araç araştırma; Mercedes/Porsche/Bosch'a coğrafi ve sektörel yakınlık |
| **KIT Karlsruhe** | Kamu TU | Güçlü makine + araç mühendisliği; geniş araştırma |
| **TU Braunschweig** | Kamu TU | **NFF** otomotiv araştırma merkezi; sektör bağlantılı |
| **HS Esslingen** | Kamu HAW | Uygulamalı otomotivde güçlü ("Automotive Systems"); Stuttgart bölgesine yakın |
| **TH Ingolstadt** | Kamu HAW | Audi'ye yakın; uygulamalı, sanayi entegreli |

**RWTH Aachen, TUM ve Uni Stuttgart** genelde otomotiv için ilk akla gelen isimler; ama unutma, uygulamalı yüksekokullar (Esslingen, Ingolstadt gibi) sektörle çok iç içe ve staj/işe geçişte çok güçlü. İsim peşinde koşmadan önce Almanya'da "prestij"in nasıl işlediğini anlamak önemli — bunu dürüstçe [Almanya'da üniversite prestiji ve sıralamalar nasıl işler](/tr/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one) yazısında anlattım.

## İkili eğitim (duales Studium)

Otomotivde çok değerli bir yol: **duales Studium** (ikili eğitim). Bir firmayla (OEM ya da tedarikçi) sözleşme yapar, okul ile işyeri arasında dönüşümlü okursun — hem maaş alır hem de mezuniyette güçlü bir sektör ağı ve iş teklifiyle çıkarsın. Almanca gerektiren ve rekabetçi bir yol ama otomotiv firmalarıyla çok yaygın ve değerli. Türk öğrenciler için genellikle iyi Almanca ve erken başvuru şart.

## Almanca vs İngilizce

- **Bachelor:** çoğu program **Almanca** yürür; pratikte **Almanca C1** (TestDaF/DSH) beklenir. Otomotiv sanayisinin iç dili büyük ölçüde Almanca olduğu için, Almanca uzun vadede büyük avantaj.
- **Master:** İngilizce seçenekler var — "Automotive Engineering", "Automotive Systems", "Vehicle Engineering", "E-Mobility" gibi programlar (örn. RWTH, Uni Stuttgart, HS Esslingen). Almancan yoksa yol kapalı değil; ayrıntıları [Almancasız Almanya'da İngilizce otomotiv & araç mühendisliği master programları](/tr/blog/english-taught-automotive-and-vehicle-engineering-masters-in-germany) yazısında ele alıyorum. Yine de staj ve sanayi için Almancayı ihmal etme.

## Başvuru: uni-assist ve belgeler

- **uni-assist:** birçok kamu üniversite/HAW yabancı başvuruları **uni-assist** üzerinden toplar (diploma denkliği ve ön-değerlendirme). Bazı okullar doğrudan kendi portalından alır — okulun sayfasını mutlaka kontrol et.
- **Belgeler:** lise/lisans diploması ve transkript, dil kanıtı (Almanca C1 **veya** İngilizce programlar için IELTS/TOEFL), motivasyon mektubu, CV. Master için ilgili bir lisans (makine, mekatronik, elektrik-elektronik, otomotiv) genelde şart.
- **Dönemler:** kış dönemi başvuruları çoğunlukla **15 Temmuz** civarında kapanır; İngilizce master'larda tarihler farklı olabilir. Erken başla.
- **APS (Türkiye için genelde gerekmez):** APS prosedürü Çin/Hindistan/Vietnam gibi ülkeler için geçerli; Türk öğrenciler için standart uni-assist yolu işler — yine de güncel duruma bak.

## Ücret & yaşam maliyeti

- **Harç:** kamu üniversite/HAW'larda **öğrenim ücreti yok**; sadece dönemlik katkı ~**150–350€** (semester ticket dâhil olabilir). İstisna: **Baden-Württemberg** (Stuttgart, KIT, Esslingen dâhil), AB dışı öğrencilerden ~**1.500€/dönem** alır. Özel okullar yılda **binlerce euro**. *2025/2026 itibarıyla, yaklaşık; doğrula.*
- **Sperrkonto (bloke hesap):** vize için genelde ~**992€/ay = ~11.904€/yıl** göstermen istenir. *2025/2026 itibarıyla, yaklaşık; resmî kaynaktan doğrula.*
- **Burs:** **DAAD** en bilinen kaynak; ayrıca Deutschlandstipendium ve vakıf bursları.
- **Mezuniyet sonrası & Blue Card:** iş bulunca Blue Card için 2026 genel maaş eşiği ~**50.700€/yıl**; darboğaz meslek/yeni mezun eşiği ~**45.934€/yıl**. *Yaklaşık; resmî kaynaktan doğrula.* İyi haber: otomotiv genelde iyi öder ve bu eşikler rahat aşılabilir.

## Sonuç & dürüst tavsiye

Almanya'da otomotiv mühendisliği okumak, sektörün fiilen dünya lideri olduğu bir ülkede güçlü ve heyecan verici bir tercih — üstelik kamu tarafında çok ekonomik. Dürüst tavsiyem:

1. **Geleceğe göre uzmanlaş:** klasik içten yanmalı roller daralıyor; **EV/batarya, yazılım/ADAS, mekatroniğe** yönel. Talep ve maaş orada.
2. **Sanayiyle erken temas kur:** staj, Werkstudent ya da duales Studium ile daha okurken ayağını sektöre bas — otomotivde ağ her şeydir.
3. **Okulunu amacına göre seç:** araştırma/zirve istiyorsan RWTH/TUM/Stuttgart; uygulamalı ve sanayiye yakın istiyorsan Esslingen/Ingolstadt gibi HAW'lar.
4. **Almancayı ihmal etme:** İngilizce master mümkün ama sanayinin iç dili ve iş bulmada Almanca büyük fark yaratır.

Diplomayla somut iş yollarını [otomotiv mühendisliği diplomasıyla iş piyasası](/tr/blog/what-to-do-with-an-automotive-engineering-degree-in-germany-job-market) yazısında ele aldım. Kararını marka hissine değil, **sektörün gittiği yöne (EV/yazılım) ve seni istihdam edilebilir kılacak uzmanlaşmaya** göre ver.

*Bu yazı 2026 başı itibarıyla hazırlanmıştır. Öğrenim ücretleri, başvuru koşulları, Sperrkonto tutarı, Blue Card maaş eşikleri ve piyasa/sektör rakamları eyalete, okula ve yıla göre değişir. Otomotiv sektörü ayrıca hızlı bir dönüşümde; başvurmadan önce ilgili okulun ve resmî kurumların güncel bilgilerini mutlaka doğrula.*
MD;

        $deBody = <<<'MD'
Deutschland ist das **Herz der weltweiten Automobilindustrie**: Hersteller wie der **Volkswagen-Konzern (VW/Audi/Porsche), BMW und Mercedes-Benz** sowie Zulieferriesen wie **Bosch, Continental, ZF und Schaeffler** sind hier zu Hause. Die Automobilbranche ist der größte Industriezweig des Landes, und **Fahrzeugtechnik** zu studieren ist für internationale Studierende eine starke und spannende Wahl. Aber die ehrliche Wahrheit gleich vorweg: Die Branche erlebt den größten Umbruch ihrer Geschichte — den Übergang zu Elektromobilität, autonomem Fahren und dem software-definierten Fahrzeug. Dieser Übergang ist eine große Chance für alle, die die richtige Spezialisierung wählen. In diesem Artikel erkläre ich von Anfang bis Ende, wie ein Fahrzeugtechnik-Studium in Deutschland als internationale:r Studierende:r funktioniert.

## Was ist Fahrzeugtechnik? Der Bezug zum Maschinenbau

Fahrzeugtechnik wird meist als **Spezialisierung des Maschinenbaus** gelehrt. Auf einer soliden Ingenieurbasis (Mechanik, Thermodynamik, Werkstoffe, Mathematik) kommen fahrzeugspezifische Fächer hinzu. Fahrzeugtechnik ist kein einzelnes enges Thema, sondern ein breites Feld mit vielen Teilgebieten:

- **Fahrzeugdynamik & Fahrwerk:** Federung, Bremsen, Lenkung, Fahrsicherheit und -komfort.
- **Antriebsstrang (Powertrain):** Motor, Getriebe, Antriebssysteme — zunehmend elektrisch.
- **Elektromobilität (E-Mobilität):** Batteriesysteme, Elektromotor, Ladetechnik, Thermomanagement.
- **Autonomes Fahren & ADAS:** Fahrerassistenzsysteme, Sensorfusion, Fahralgorithmen.
- **Embedded Software & Mechatronik:** Steuerungssoftware und Elektronik, das "Gehirn" des Fahrzeugs.
- **Leichtbau & Produktion:** Werkstoffoptimierung, Fertigungs- und Montageprozesse.

Wenn dich interessiert, wie das Ingenieurstudium generell aufgebaut ist, ist [Ingenieurwesen in Deutschland studieren: Leitfaden](/de/blog/studying-engineering-in-germany-as-a-foreigner-de) ein guter Startpunkt; die Fahrzeugtechnik ist eine Spezialisierung, die darauf aufbaut.

## Der große Umbruch: EV, autonom und software-definiertes Fahrzeug

Das muss man ehrlich sagen: Klassische "Verbrennungsmotor-Ingenieur"-Rollen **schrumpfen**, während die Seite **Elektromobilität, Batterie, Software und Mechatronik** rasant wächst. Die Branche verschiebt sich zum Dreiklang Elektroauto, autonomes Fahren und **software-definiertes Fahrzeug (software-defined vehicle)**. Es ist eine Übergangsphase — mit Unsicherheit und schmerzhaften Umstrukturierungen. Aber qualifizierte Ingenieur:innen, besonders auf der Software- und EV-Seite, sind weiterhin sehr gefragt.

Mein praktischer Rat: Auch wenn du die klassische Maschinenseite magst, setze bei Kursen/Projekten Schwerpunkte auf **EV, Embedded Software und Mechatronik**. Das bringt dich näher an das gefragte Profil der Zukunft. Wohin die Branche geht und wie das Karrierebild aussieht, behandle ich ausführlich in [In der Fahrzeugtechnik in Deutschland arbeiten: Karriere und Gehalt](/de/blog/working-in-automotive-engineering-in-germany-careers-and-salary-de).

## Anerkannte Hochschulen

In der Fahrzeugtechnik hat Deutschland einen sehr starken Hochschulpool — an der Spitze Forschungsuniversitäten, dazu praxisnahe Hochschulen für angewandte Wissenschaften (HAW):

| Hochschule | Typ | Besonderheit |
|---|---|---|
| **RWTH Aachen** | Staatliche TU | Spitze in Fahrzeugtechnik/Ingenieurwesen; **ika** (Institut für Kraftfahrzeuge); sehr starke Branchennähe |
| **TU München (TUM)** | Staatliche TU | eine der renommiertesten TUs; starke Fahrzeug- und Mechatronikforschung |
| **Uni Stuttgart** | Staatliche Uni | **FKFS**-Fahrzeugforschung; geografische und branchennahe Nähe zu Mercedes/Porsche/Bosch |
| **KIT Karlsruhe** | Staatliche TU | starker Maschinenbau + Fahrzeugtechnik; breite Forschung |
| **TU Braunschweig** | Staatliche TU | **NFF**-Forschungszentrum Automobil; branchennah |
| **HS Esslingen** | Staatliche HAW | stark in angewandter Fahrzeugtechnik ("Automotive Systems"); nahe der Region Stuttgart |
| **TH Ingolstadt** | Staatliche HAW | nahe Audi; praxisnah, industrieintegriert |

**RWTH Aachen, TUM und Uni Stuttgart** sind meist die ersten Namen, die bei Automobil einfallen; aber vergiss nicht: HAWs (wie Esslingen, Ingolstadt) sind eng mit der Branche verzahnt und beim Übergang zu Praktikum/Job sehr stark. Bevor du einem Namen hinterherläufst, ist es wichtig zu verstehen, wie "Prestige" in Deutschland funktioniert — das erkläre ich ehrlich in [Wie Uni-Prestige und Rankings in Deutschland funktionieren](/de/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-de).

## Duales Studium

Ein in der Automobilbranche sehr wertvoller Weg: das **duale Studium**. Du schließt einen Vertrag mit einem Unternehmen (OEM oder Zulieferer) und studierst im Wechsel zwischen Hochschule und Betrieb — du bekommst ein Gehalt und verlässt das Studium mit einem starken Branchennetzwerk und oft einem Jobangebot. Es ist ein anspruchsvoller Weg, der Deutsch verlangt, aber bei Automobilfirmen sehr verbreitet und wertvoll. Für internationale Studierende sind meist gutes Deutsch und eine frühe Bewerbung Voraussetzung.

## Deutsch vs. Englisch

- **Bachelor:** Die meisten Programme laufen auf **Deutsch**; praktisch wird **Deutsch C1** (TestDaF/DSH) erwartet. Da die Innensprache der Automobilindustrie weitgehend Deutsch ist, ist Deutsch langfristig ein großer Vorteil.
- **Master:** Es gibt englischsprachige Optionen — Programme wie "Automotive Engineering", "Automotive Systems", "Vehicle Engineering" oder "E-Mobility" (z. B. RWTH, Uni Stuttgart, HS Esslingen). Ohne Deutsch ist der Weg nicht versperrt; die Details behandle ich in [Englischsprachige Automobil- & Fahrzeugtechnik-Master in Deutschland](/de/blog/english-taught-automotive-and-vehicle-engineering-masters-in-germany-de). Vernachlässige Deutsch dennoch nicht für Praktikum und Industrie.

## Bewerbung: uni-assist und Unterlagen

- **uni-assist:** Viele staatliche Unis/HAWs bündeln internationale Bewerbungen über **uni-assist** (Zeugnisbewertung und Vorprüfung). Manche nehmen direkt über ihr Portal an — prüfe unbedingt die Seite der Hochschule.
- **Unterlagen:** Schul-/Bachelorzeugnis und Transcript, Sprachnachweis (Deutsch C1 **oder** IELTS/TOEFL für englische Programme), Motivationsschreiben, CV. Für den Master ist meist ein einschlägiger Bachelor (Maschinenbau, Mechatronik, Elektrotechnik, Fahrzeugtechnik) Voraussetzung.
- **Fristen:** Bewerbungen fürs Wintersemester schließen meist um den **15. Juli**; bei englischen Mastern können die Termine abweichen. Fang früh an.
- **APS:** Das APS-Verfahren gilt für Länder wie China/Indien/Vietnam; prüfe deine aktuelle Länderregelung.

## Kosten & Lebenshaltung

- **Gebühren:** an staatlichen Unis/HAWs gibt es **keine Studiengebühren**; nur ein Semesterbeitrag von ~**150–350€** (ggf. inkl. Semesterticket). Ausnahme: **Baden-Württemberg** (inkl. Stuttgart, KIT, Esslingen) verlangt von Nicht-EU-Studierenden ~**1.500€/Semester**. Private Hochschulen: mehrere **Tausend Euro** pro Jahr. *Stand 2025/2026, ungefähr; bitte prüfen.*
- **Sperrkonto:** fürs Visum musst du meist ~**992€/Monat = ~11.904€/Jahr** nachweisen. *Stand 2025/2026, ungefähr; aus offizieller Quelle prüfen.*
- **Stipendien:** **DAAD** ist die bekannteste Quelle; außerdem das Deutschlandstipendium und Stiftungsstipendien.
- **Nach dem Abschluss & Blue Card:** mit einem Job liegt die allgemeine Blue-Card-Gehaltsschwelle 2026 bei ~**50.700€/Jahr**; Engpassberufe/Berufseinsteiger:innen ~**45.934€/Jahr**. *Ungefähr; aus offizieller Quelle prüfen.* Die gute Nachricht: Die Automobilbranche zahlt meist gut, und diese Schwellen werden oft problemlos überschritten.

## Fazit & ehrlicher Rat

Fahrzeugtechnik in Deutschland zu studieren ist in einem Land, das faktisch Weltmarktführer der Branche ist, eine starke und spannende Wahl — und auf der staatlichen Seite sehr günstig. Mein ehrlicher Rat:

1. **Spezialisiere dich auf die Zukunft:** klassische Verbrenner-Rollen schrumpfen; setze auf **EV/Batterie, Software/ADAS, Mechatronik**. Dort sind Nachfrage und Gehalt.
2. **Knüpfe früh Industriekontakte:** mit Praktikum, Werkstudentenjob oder dualem Studium stehst du schon im Studium mit einem Fuß in der Branche — in der Automobilbranche ist das Netzwerk alles.
3. **Wähle die Hochschule nach deinem Ziel:** für Forschung/Spitze RWTH/TUM/Stuttgart; für praxisnah und industrienah HAWs wie Esslingen/Ingolstadt.
4. **Vernachlässige Deutsch nicht:** ein englischer Master ist möglich, aber die Innensprache der Industrie und die Jobsuche machen mit Deutsch einen großen Unterschied.

Konkrete Berufswege mit dem Abschluss behandle ich in [Was tun mit einem Fahrzeugtechnik-Abschluss in Deutschland](/de/blog/what-to-do-with-an-automotive-engineering-degree-in-germany-job-market-de). Triff deine Entscheidung nicht nach dem Markengefühl, sondern nach **der Richtung, in die die Branche geht (EV/Software), und der Spezialisierung, die dich beschäftigungsfähig macht**.

*Dieser Artikel wurde Anfang 2026 erstellt. Studiengebühren, Bewerbungsbedingungen, Sperrkonto-Betrag, Blue-Card-Gehaltsschwellen und Markt-/Branchenzahlen variieren je nach Bundesland, Hochschule und Jahr. Die Automobilbranche befindet sich zudem in einem schnellen Umbruch; prüfe vor der Bewerbung unbedingt die aktuellen Angaben der jeweiligen Hochschule und offizieller Stellen.*
MD;

        $enBody = <<<'MD'
Germany is the **heart of the global automotive industry**: manufacturers like the **Volkswagen Group (VW/Audi/Porsche), BMW and Mercedes-Benz**, plus supplier giants like **Bosch, Continental, ZF and Schaeffler**, are all based here. The automotive sector is the country's largest industrial branch, and studying **Fahrzeugtechnik** (automotive/vehicle engineering) is a strong and exciting choice for an international student. But let me give you the honest truth up front: the industry is going through the biggest transformation in its history — the shift to electric vehicles, autonomous driving and the software-defined vehicle. That transition is a huge opportunity for anyone who picks the right specialisation. In this article I explain from start to finish how studying automotive engineering in Germany works as an international student.

## What is Fahrzeugtechnik? Its relation to Maschinenbau

Fahrzeugtechnik is usually taught as a **specialisation of Maschinenbau** (mechanical engineering). On a solid engineering foundation (mechanics, thermodynamics, materials, mathematics), vehicle-focused subjects are added. Automotive engineering isn't a single narrow topic; it's a broad field that opens into many sub-areas:

- **Vehicle dynamics & chassis:** suspension, brakes, steering, driving safety and comfort.
- **Powertrain:** engine, transmission, drive systems — increasingly electric.
- **Electric mobility (E-Mobilität):** battery systems, electric motor, charging technology, thermal management.
- **Autonomous driving & ADAS:** driver-assistance systems, sensor fusion, driving algorithms.
- **Embedded software & mechatronics:** the control software and electronics that are becoming the "brain" of the vehicle.
- **Lightweight design & production:** material optimisation, manufacturing and assembly processes.

If you're curious how engineering education is structured in general, [Studying engineering in Germany: a foreigner's guide](/en/blog/studying-engineering-in-germany-as-a-foreigner-en) is a good starting point; automotive is a specialisation built on top of that foundation.

## The big transformation: EV, autonomous and the software-defined vehicle

This needs to be said honestly: classic "combustion-engine engineer" roles are **shrinking**, while the **electric vehicle, battery, software and mechatronics** side is growing fast. The industry is shifting toward the triad of electric cars, autonomous driving and the **software-defined vehicle**. It's a transition period — with uncertainty and painful restructuring. But qualified engineers, especially on the software and EV side, remain in high demand.

My practical advice: even if you love the classic mechanical side, weight your courses/projects toward **EV, embedded software and mechatronics**. That moves you closer to the in-demand profile of the future. Where the industry is heading and what the career picture looks like, I cover in depth in [Working in automotive engineering in Germany: careers and salary](/en/blog/working-in-automotive-engineering-in-germany-careers-and-salary-en).

## Recognised schools

In automotive engineering, Germany has a very strong pool of schools — research universities at the top, alongside practice-oriented universities of applied sciences (HAW):

| School | Type | Highlight |
|---|---|---|
| **RWTH Aachen** | Public TU | Top-tier in automotive/engineering; **ika** (institute for automotive engineering); very strong industry ties |
| **TU München (TUM)** | Public TU | one of the most prestigious TUs; strong vehicle and mechatronics research |
| **Uni Stuttgart** | Public university | **FKFS** vehicle research; geographic and industry proximity to Mercedes/Porsche/Bosch |
| **KIT Karlsruhe** | Public TU | strong mechanical + automotive engineering; broad research |
| **TU Braunschweig** | Public TU | **NFF** automotive research centre; industry-connected |
| **HS Esslingen** | Public HAW | strong in applied automotive ("Automotive Systems"); near the Stuttgart region |
| **TH Ingolstadt** | Public HAW | near Audi; applied, industry-integrated |

**RWTH Aachen, TUM and Uni Stuttgart** are usually the first names that come to mind for automotive; but don't forget that HAWs (like Esslingen, Ingolstadt) are tightly linked with industry and very strong for the transition into internships/jobs. Before chasing a name, it's important to understand how "prestige" works in Germany — I explain this honestly in [How university prestige and rankings work in Germany](/en/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-en).

## Dual study (duales Studium)

A very valuable path in automotive: the **duales Studium** (dual study). You sign a contract with a company (an OEM or supplier) and study alternating between the school and the workplace — you earn a salary and leave your studies with a strong industry network and often a job offer. It's a demanding path that requires German, but it's very common and valued with automotive companies. For international students, good German and an early application are usually prerequisites.

## German vs English

- **Bachelor's:** most programs run in **German**; in practice you're expected to have **German C1** (TestDaF/DSH). Since the internal language of the automotive industry is largely German, German is a big long-term advantage.
- **Master's:** there are English-taught options — programs like "Automotive Engineering", "Automotive Systems", "Vehicle Engineering" or "E-Mobility" (e.g. RWTH, Uni Stuttgart, HS Esslingen). Without German the path isn't closed; I cover the details in [English-taught automotive & vehicle engineering master's in Germany](/en/blog/english-taught-automotive-and-vehicle-engineering-masters-in-germany-en). Still, don't neglect German for internships and industry.

## Applying: uni-assist and documents

- **uni-assist:** many public universities/HAWs bundle international applications through **uni-assist** (certificate evaluation and pre-checking). Some accept directly via their own portal — always check the school's page.
- **Documents:** school/bachelor's certificate and transcript, language proof (German C1 **or** IELTS/TOEFL for English programs), motivation letter, CV. For a master's, a relevant bachelor's (mechanical, mechatronics, electrical, automotive) is usually required.
- **Deadlines:** winter-semester applications usually close around **15 July**; for English master's the dates can differ. Start early.
- **APS:** the APS procedure applies to countries like China/India/Vietnam; check your current country rule.

## Fees & living costs

- **Fees:** public universities/HAWs charge **no tuition**; only a semester contribution of ~**€150–350** (may include a semester ticket). Exception: **Baden-Württemberg** (including Stuttgart, KIT, Esslingen) charges non-EU students ~**€1,500/semester**. Private schools: several **thousand euros** per year. *As of 2025/2026, approximate; verify.*
- **Sperrkonto (blocked account):** for the visa you're usually asked to show ~**€992/month = ~€11,904/year**. *As of 2025/2026, approximate; verify from official sources.*
- **Scholarships:** **DAAD** is the best-known source; also the Deutschlandstipendium and foundation scholarships.
- **After graduation & Blue Card:** with a job, the 2026 general Blue Card salary threshold is ~**€50,700/year**; the shortage-occupation/new-graduate threshold is ~**€45,934/year**. *Approximate; verify from official sources.* The good news: automotive generally pays well, and these thresholds are often comfortably exceeded.

## Conclusion & honest advice

Studying automotive engineering in Germany, in a country that is effectively the world leader of the industry, is a strong and exciting choice — and, on the public side, very affordable. My honest advice:

1. **Specialise for the future:** classic combustion roles are shrinking; lean into **EV/battery, software/ADAS, mechatronics**. That's where demand and pay are.
2. **Build industry contacts early:** through an internship, Werkstudent job or dual study, get one foot in the industry while still studying — in automotive, the network is everything.
3. **Choose your school by your goal:** for research/top tier, RWTH/TUM/Stuttgart; for applied and industry-close, HAWs like Esslingen/Ingolstadt.
4. **Don't neglect German:** an English master's is possible, but the industry's internal language and the job search make a big difference with German.

I cover the concrete job paths with the degree in [What to do with an automotive engineering degree in Germany](/en/blog/what-to-do-with-an-automotive-engineering-degree-in-germany-job-market-en). Make your decision not on brand feeling, but on **the direction the industry is heading (EV/software) and the specialisation that will make you employable**.

*This article was prepared in early 2026. Tuition fees, application conditions, the Sperrkonto amount, Blue Card salary thresholds and market/industry figures vary by state, school and year. The automotive industry is also in rapid transition; always verify the current information from the relevant school and official bodies before applying.*
MD;

        $variants = [
            'tr' => ['slug'=>'studying-automotive-engineering-fahrzeugtechnik-in-germany-as-a-foreigner',    'title'=>'Almanya\'da Otomotiv Mühendisliği (Fahrzeugtechnik) Okumak: Rehber', 'excerpt'=>'Almanya\'da otomotiv mühendisliği (Fahrzeugtechnik) okumak: alan kapsamı & alt dallar, Maschinenbau ilişkisi, EV/otonom dönüşümü, tanınan okullar (RWTH Aachen/TUM/Uni Stuttgart/KIT/Braunschweig/Esslingen, tablo), duales Studium, Almanca vs İngilizce, uni-assist başvurusu, ücret & Sperrkonto ve Blue Card gerçeği.', 'meta_title'=>'Almanya\'da Otomotiv Mühendisliği (Fahrzeugtechnik) Okumak', 'meta_description'=>'Almanya\'da otomotiv mühendisliği okumak: EV/otonom dönüşümü, RWTH/TUM/Stuttgart, duales Studium, Almanca vs İngilizce, uni-assist, ücret ve Blue Card gerçeği.', 'body'=>$trBody],
            'de' => ['slug'=>'studying-automotive-engineering-fahrzeugtechnik-in-germany-as-a-foreigner-de', 'title'=>'Fahrzeugtechnik in Deutschland studieren: Leitfaden', 'excerpt'=>'Fahrzeugtechnik in Deutschland studieren: Feldüberblick & Teilgebiete, Bezug zum Maschinenbau, EV-/Autonom-Umbruch, anerkannte Hochschulen (RWTH Aachen/TUM/Uni Stuttgart/KIT/Braunschweig/Esslingen, Tabelle), duales Studium, Deutsch vs. Englisch, uni-assist-Bewerbung, Kosten & Sperrkonto und die Blue-Card-Realität.', 'meta_title'=>'Fahrzeugtechnik in Deutschland studieren: Leitfaden', 'meta_description'=>'Fahrzeugtechnik in Deutschland studieren: EV-/Autonom-Umbruch, RWTH/TUM/Stuttgart, duales Studium, Deutsch vs. Englisch, uni-assist, Kosten und Blue-Card-Realität.', 'body'=>$deBody],
            'en' => ['slug'=>'studying-automotive-engineering-fahrzeugtechnik-in-germany-as-a-foreigner-en', 'title'=>'Studying Automotive Engineering (Fahrzeugtechnik) in Germany: A Guide', 'excerpt'=>'Studying automotive engineering (Fahrzeugtechnik) in Germany: field overview & sub-areas, relation to Maschinenbau, the EV/autonomous transformation, recognised schools (RWTH Aachen/TUM/Uni Stuttgart/KIT/Braunschweig/Esslingen, table), duales Studium, German vs English, uni-assist application, fees & Sperrkonto and the Blue Card reality.', 'meta_title'=>'Studying Automotive Engineering (Fahrzeugtechnik) in Germany', 'meta_description'=>'Studying automotive engineering in Germany: the EV/autonomous transformation, RWTH/TUM/Stuttgart, duales Studium, German vs English, uni-assist, fees and the Blue Card reality.', 'body'=>$enBody],
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
            'studying-automotive-engineering-fahrzeugtechnik-in-germany-as-a-foreigner',
            'studying-automotive-engineering-fahrzeugtechnik-in-germany-as-a-foreigner-de',
            'studying-automotive-engineering-fahrzeugtechnik-in-germany-as-a-foreigner-en',
        ])->delete();
    }
};
