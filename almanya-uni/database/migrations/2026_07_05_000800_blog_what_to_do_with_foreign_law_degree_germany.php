<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Yabancı hukuk diplomasıyla Almanya'da iş piyasası (2026).
 * Doğrulandı: yabancı hukuk derecesi Alman avukatlığına (iki Staatsexamen) kolay
 * transfer olmaz; gerçekçi yol = uzmanlaşma/uluslararası roller (LL.M. + Almanca +
 * network + staj). Mezuniyet sonrası 18 ay iş-arama oturumu. Sayılar 2025/2026 yaklaşık.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'd7e40000-4444-4a8c-9fb0-dd05ee0aaa04';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Elinde Türkiye'den (ya da başka bir ülkeden) bir hukuk diploması var ve Almanya'da bir gelecek arıyorsun. En sık sorulan soru şu: "Bu diplomayla burada ne yapabilirim?" Dürüst cevap, hayal kırıklığı ile fırsatın karışımıdır. Bu yazı, yabancı hukuk diplomasının Alman iş piyasasında gerçekte neye yaradığını, hangi kapıları açtığını ve hangilerini açmadığını anlatır.

## Gerçekçi beklenti: avukatlık değil, uzmanlaşma ve uluslararası roller

En baştan net olalım: **yabancı hukuk diploman seni Almanya'da avukat (Rechtsanwalt / Volljurist) yapmaz.** Alman avukatı olmak için **iki Staatsexamen** (birinci sınav + yaklaşık 2 yıllık **Referendariat** sonrası ikinci sınav) gerekir ve bu yol **tamamen Almanca**, tamamen Alman hukukuna özeldir. Türk (AB-dışı) bir hukukçu bu sisteme doğrudan kaydolamaz; yabancı derece tanınması Staatsexamen yolu için **çok sınırlıdır** (kesin durumunu ilgili eyaletin Justizprüfungsamt'ından doğrula).

Peki bu bir çıkmaz mı? Hayır. Gerçekçi ve değerli olan beklenti şudur: **avukatlık değil, uzmanlaşma ve uluslararası roller.** Almanya'daki hukuk pazarı uluslararası hukuk, ticaret hukuku, compliance ve uluslararası kuruluşlar için yabancı-eğitimli hukukçulara alan açar — özellikle kendi ülkenin hukukunu ve dilini bilmen bir avantaja dönüştüğünde.

## Yollar: LL.M.'den uluslararası firmaya

Yabancı hukuk diplomasını Alman iş piyasasında değere çeviren en yaygın araç bir **LL.M.** (Master of Laws, ~1 yıl, çoğu zaman İngilizce). LL.M. seni avukat yapmaz ama uzmanlık, ağ ve bir Alman diploması kazandırır. İşte başlıca yollar:

| Yol | Ne yapar | Alman niteliği (Staatsexamen) gerekli mi? |
|---|---|---|
| Uluslararası hukuk firması (LL.M. + İngilizce) | Sınır ötesi işlemler, uluslararası müvekkiller | Avukat unvanı için evet; destek/uzman rolde çoğu zaman hayır |
| Compliance / regülasyon | Şirket içi kural uyumu, yaptırımlar, KYC | Genelde hayır |
| Ticaret hukuku (Wirtschaftsjurist tarzı roller) | Sözleşme, şirketler hukuku, in-house destek | Genelde hayır |
| Uluslararası kuruluşlar / STK'lar | İnsan hakları, uluslararası hukuk, politika | Genelde hayır |
| LegalTech / danışmanlık | Süreç, veri, hukuki teknoloji | Hayır |

**Kalın gerçek:** Bu rollerin çoğu "avukat" unvanı istemez; senden istenen uzmanlık, dil ve uluslararası bakıştır. Detaylı kariyer haritası için küme kardeşimiz [avukat olmadan hukukta çalışmak](/tr/blog/working-in-law-in-germany-careers-beyond-becoming-a-lawyer) yazısına bak.

## Almanca öğrenirsen açılan kapılar

İngilizce LL.M. ile uluslararası roller mümkündür, ama **Almanca öğrenmek iş piyasasını katbekat büyütür.** Sadece-İngilizce profil seni büyük şehirlerdeki uluslararası firmalar ve global şirketlerle sınırlar. **B2-C1 Almanca** ise sana şunları açar: Alman şirketlerinde in-house/compliance pozisyonları, kamu ve regülasyon kurumları, danışmanlık firmaları ve çok daha geniş bir işveren havuzu.

Kısacası: **İngilizce kapıyı açar, Almanca odaları çoğaltır.** Alman avukatlık pratiği için Almanca zaten şarttır; ama pratik dışındaki hukuk kariyerleri için bile Almanca senin en yüksek getirili yatırımındır (2025/2026 itibarıyla iş ilanlarının çoğunluğu hâlâ Almanca ister; doğrula).

## Mezuniyet sonrası 18 ay iş-arama oturumu

Almanya'nın en güçlü kartlarından biri şu: bir Alman üniversitesinden (örneğin LL.M.) mezun olan uluslararası öğrenciler, **mezuniyet sonrası nitelikli işlerini aramak için ikamet izinlerini yaklaşık 18 aya kadar uzatabilir** (2025/2026 itibarıyla; kesin süre ve şartları yabancılar dairesinden — Ausländerbehörde — doğrula). Bu süre içinde herhangi bir işte çalışabilir ve alanına uygun bir pozisyon bulmaya odaklanabilirsin.

Bu, yabancı hukuk diploman + Almanya'da bir LL.M. stratejisini özellikle mantıklı kılar: diploma seni Alman iş piyasasının içine yerleştirir, 18 aylık pencere ise ilk işini bulmak için gerçek bir tampon sağlar. İki anahtarı — master ve iş-arama vizesi — birlikte ele alan [master vs. iş-arama vizesi](/tr/blog/germany-masters-vs-job-seeker-visa-two-keys-career) yazısı bu planlamada yol gösterir.

## Strateji: LL.M. alanını seç, dil, network, staj

Yabancı hukuk diplomasını işe çevirmenin bir formülü var. Dört sütun üstüne kur:

1. **LL.M. alanını bilinçli seç.** Rastgele bir "genel hukuk" master'ı değil, piyasada karşılığı olan bir uzmanlık: uluslararası ticaret hukuku, IP/teknoloji hukuku, vergi hukuku, rekabet hukuku veya AB hukuku. İngilizce program seçeneklerini [Almancasız İngilizce LL.M.](/tr/blog/english-taught-law-llm-masters-in-germany-without-german) yazısında incele.
2. **Dil.** LL.M. boyunca paralel Almanca öğren (hedef B2-C1). Mezun olduğunda dil de hazır olsun.
3. **Network.** Kariyer günleri, hukuk firması etkinlikleri, mezun ağları, LinkedIn. Almanya'da işlerin çoğu ilanla değil, ilişkiyle bulunur.
4. **Staj / Werkstudent.** Master sırasında bir uluslararası firmada ya da şirketin hukuk departmanında yarı-zamanlı çalış. Bu, mezuniyette "deneyimsiz" olmaman anlamına gelir.

Almancasız avukatlık kısıtlarının tam çerçevesi için [yabancı avukatlık yapabilir mi?](/tr/blog/can-foreigners-practice-law-in-germany-staatsexamen-and-recognition) yazısına bak.

## Dürüst zorluklar

Süslemeyelim. Karşına çıkacaklar:

- **Tanınma engeli:** Yabancı hukuk diploman Alman avukatlığına dönüşmez; en fazla bazı hallerde kendi ülke hukukun için "yabancı hukuk danışmanı" gibi dar bir rol mümkün olabilir.
- **Dil duvarı:** İyi işlerin çoğu Almanca ister; sadece-İngilizce profil seni daraltır.
- **Rekabet:** Alman Volljuristen (iki sınavı geçmiş) çoğu pozisyonda "varsayılan" adaydır; sen farkını uluslararasılık ve uzmanlıkla kanıtlamalısın.
- **Belirsizlik:** Tanınma, vize ve iş piyasası kuralları eyalete ve yıla göre değişir (2025/2026 itibarıyla; resmî kaynaktan doğrula).

Doğru üniversite ve program seçiminin bu zorlukları nasıl etkilediğini görmek istersen: hukuk özelinde derinleşen mevcut [yabancı olarak Almanya'da hukuk okumak](/tr/blog/studying-law-in-germany-as-a-foreigner-staatsexamen-vs-llb-llm) yazısı iyi bir başlangıç.

## Sonuç & dürüst tavsiye

Yabancı hukuk diploman Almanya'da bir "avukatlık bileti" değil, bir **başlangıç sermayesi**dir. Onu şuna çevir: doğru alanda bir LL.M. + ciddi Almanca + aktif network + bir stajla desteklenmiş, mezuniyet sonrası 18 aylık iş-arama penceresini kullanan gerçekçi bir plan. Hedefin "Alman avukatı olmak" değil, **uluslararası hukukta, compliance'ta ya da ticaret hukukunda uzman bir profesyonel olmak** olsun. Bu yolda diploman gerçekten işe yarar.

*Bu yazı 2026 başı itibarıyla genel bilgilendirme amaçlıdır; hukuki veya göç danışmanlığı değildir. Tanınma, vize süreleri, ücretler ve iş-arama izni kuralları eyalete ve zamana göre değişir — kesin bilgiyi ilgili üniversite, eyalet Justizprüfungsamt'ı ve Ausländerbehörde'den doğrula.*
MD;
        $deBody = <<<'MD'
Du hast einen Jura-Abschluss aus der Türkei (oder einem anderen Land) und suchst eine Zukunft in Deutschland. Die häufigste Frage lautet: "Was kann ich mit diesem Abschluss hier machen?" Die ehrliche Antwort ist eine Mischung aus Enttäuschung und Chance. Dieser Artikel zeigt dir, was ein ausländischer Jura-Abschluss auf dem deutschen Arbeitsmarkt wirklich wert ist – welche Türen er öffnet und welche nicht.

## Realistische Erwartung: keine Anwaltschaft, sondern Spezialisierung und internationale Rollen

Sagen wir es gleich zu Beginn klar: **Dein ausländischer Jura-Abschluss macht dich in Deutschland nicht zum Rechtsanwalt (Volljurist).** Dafür brauchst du **zwei Staatsexamen** (erstes Examen + zweites Examen nach dem etwa zweijährigen **Referendariat**), und dieser Weg ist **komplett auf Deutsch** und ganz auf das deutsche Recht zugeschnitten. Ein türkischer (Nicht-EU-)Jurist kann sich nicht direkt in dieses System einschreiben; die Anerkennung ausländischer Abschlüsse für den Staatsexamen-Weg ist **sehr begrenzt** (deine genaue Lage klärst du beim Justizprüfungsamt deines Bundeslandes).

Ist das also eine Sackgasse? Nein. Die realistische und wertvolle Erwartung lautet: **keine Anwaltschaft, sondern Spezialisierung und internationale Rollen.** Der deutsche Rechtsmarkt bietet ausländisch ausgebildeten Juristen Platz im internationalen Recht, Wirtschaftsrecht, in der Compliance und bei internationalen Organisationen – besonders dann, wenn dein Wissen über das Recht und die Sprache deines Heimatlandes zum Vorteil wird.

## Wege: vom LL.M. zur internationalen Kanzlei

Das häufigste Werkzeug, um einen ausländischen Jura-Abschluss auf dem deutschen Arbeitsmarkt in Wert zu verwandeln, ist ein **LL.M.** (Master of Laws, ~1 Jahr, oft auf Englisch). Der LL.M. macht dich nicht zum Anwalt, verschafft dir aber Spezialisierung, ein Netzwerk und einen deutschen Abschluss. Das sind die wichtigsten Wege:

| Weg | Was er bringt | Deutsche Qualifikation (Staatsexamen) nötig? |
|---|---|---|
| Internationale Kanzlei (LL.M. + Englisch) | Grenzüberschreitende Transaktionen, internationale Mandanten | Für den Anwaltstitel ja; in Support-/Fachrollen oft nein |
| Compliance / Regulierung | Regeltreue im Unternehmen, Sanktionen, KYC | Meist nein |
| Wirtschaftsrecht (Wirtschaftsjurist-Rollen) | Verträge, Gesellschaftsrecht, In-house-Support | Meist nein |
| Internationale Organisationen / NGOs | Menschenrechte, Völkerrecht, Politik | Meist nein |
| LegalTech / Beratung | Prozesse, Daten, juristische Technologie | Nein |

**Fettgedruckte Wahrheit:** Die meisten dieser Rollen verlangen keinen Titel "Rechtsanwalt"; gefragt sind Fachwissen, Sprache und internationale Perspektive. Eine ausführliche Karrierekarte findest du in unserem Cluster-Artikel [im Recht arbeiten ohne Anwalt zu werden](/de/blog/working-in-law-in-germany-careers-beyond-becoming-a-lawyer-de).

## Türen, die sich mit Deutsch öffnen

Mit einem englischsprachigen LL.M. sind internationale Rollen möglich, aber **Deutsch zu lernen vervielfacht deinen Arbeitsmarkt.** Ein Nur-Englisch-Profil begrenzt dich auf internationale Kanzleien in Großstädten und globale Konzerne. **Deutsch auf B2-C1** öffnet dir dagegen: In-house-/Compliance-Stellen in deutschen Unternehmen, öffentliche und regulatorische Institutionen, Beratungshäuser und einen viel breiteren Arbeitgeberpool.

Kurz gesagt: **Englisch öffnet die Tür, Deutsch vervielfacht die Räume.** Für die anwaltliche Praxis ist Deutsch ohnehin Pflicht; aber selbst für juristische Karrieren jenseits der Praxis ist Deutsch deine Investition mit der höchsten Rendite (Stand 2025/2026 verlangt die Mehrheit der Stellenanzeigen weiterhin Deutsch; bitte prüfen).

## 18 Monate Jobsuche nach dem Abschluss

Eine der stärksten Karten Deutschlands ist diese: Internationale Absolventen einer deutschen Hochschule (zum Beispiel eines LL.M.) können ihre Aufenthaltserlaubnis **nach dem Abschluss um bis zu etwa 18 Monate verlängern, um eine qualifizierte Stelle zu suchen** (Stand 2025/2026; genaue Dauer und Bedingungen bei der Ausländerbehörde prüfen). In dieser Zeit darfst du jede Arbeit annehmen und dich darauf konzentrieren, eine passende Position zu finden.

Das macht die Strategie "ausländischer Jura-Abschluss + LL.M. in Deutschland" besonders sinnvoll: Der Abschluss setzt dich mitten in den deutschen Arbeitsmarkt, und das 18-Monate-Fenster gibt dir einen echten Puffer für den ersten Job. Der Artikel [Master vs. Jobsuche-Visum](/de/blog/germany-masters-vs-job-seeker-visa-two-keys-career-de) behandelt beide Schlüssel gemeinsam und hilft bei dieser Planung.

## Strategie: LL.M.-Fach wählen, Sprache, Netzwerk, Praktikum

Es gibt eine Formel, um einen ausländischen Jura-Abschluss in einen Job zu verwandeln. Baue auf vier Säulen:

1. **Wähle das LL.M.-Fach bewusst.** Kein beliebiger "allgemeiner" Master, sondern eine marktrelevante Spezialisierung: internationales Wirtschaftsrecht, IP-/Technologierecht, Steuerrecht, Kartellrecht oder EU-Recht. Englischsprachige Programme findest du im Artikel [englischsprachiger LL.M. ohne Deutsch](/de/blog/english-taught-law-llm-masters-in-germany-without-german-de).
2. **Sprache.** Lerne während des LL.M. parallel Deutsch (Ziel B2-C1). Beim Abschluss soll auch die Sprache bereit sein.
3. **Netzwerk.** Karrieretage, Kanzlei-Events, Alumni-Netzwerke, LinkedIn. In Deutschland werden die meisten Jobs über Beziehungen gefunden, nicht über Anzeigen.
4. **Praktikum / Werkstudent.** Arbeite während des Masters Teilzeit in einer internationalen Kanzlei oder in der Rechtsabteilung eines Unternehmens. So bist du beim Abschluss nicht "unerfahren".

Den vollen Rahmen der Beschränkungen ohne Deutsch findest du im Artikel [dürfen Ausländer als Anwalt arbeiten?](/de/blog/can-foreigners-practice-law-in-germany-staatsexamen-and-recognition-de).

## Ehrliche Herausforderungen

Beschönigen wir nichts. Das erwartet dich:

- **Anerkennungshürde:** Dein ausländischer Abschluss wird nicht zur deutschen Anwaltschaft; höchstens ist in Einzelfällen eine enge Rolle als "ausländischer Rechtsberater" für dein Heimatrecht möglich.
- **Sprachbarriere:** Die meisten guten Jobs verlangen Deutsch; ein Nur-Englisch-Profil engt dich ein.
- **Konkurrenz:** Deutsche Volljuristen (mit beiden Examen) sind bei vielen Stellen der "Standard"-Kandidat; du musst deinen Unterschied durch Internationalität und Spezialisierung beweisen.
- **Unsicherheit:** Regeln zu Anerkennung, Visum und Arbeitsmarkt ändern sich je nach Bundesland und Jahr (Stand 2025/2026; bei offizieller Quelle prüfen).

Wenn du sehen willst, wie die Wahl von Uni und Programm diese Herausforderungen beeinflusst, ist der bestehende Artikel [als Ausländer in Deutschland Jura studieren](/de/blog/studying-law-in-germany-as-a-foreigner-staatsexamen-vs-llb-llm-de) ein guter Start.

## Fazit & ehrlicher Rat

Dein ausländischer Jura-Abschluss ist in Deutschland kein "Anwalts-Ticket", sondern ein **Startkapital**. Verwandle ihn in Folgendes: ein LL.M. im richtigen Fach + ernsthaftes Deutsch + aktives Netzwerk + ein Praktikum, gestützt auf das 18-Monate-Fenster für die Jobsuche nach dem Abschluss. Dein Ziel soll nicht "deutscher Anwalt werden" sein, sondern **eine spezialisierte Fachkraft im internationalen Recht, in der Compliance oder im Wirtschaftsrecht zu werden**. Auf diesem Weg ist dein Abschluss wirklich etwas wert.

*Dieser Artikel dient der allgemeinen Information zum Stand Anfang 2026 und ist keine Rechts- oder Migrationsberatung. Anerkennung, Visumsfristen, Gebühren und Regeln zur Arbeitsplatzsuche unterscheiden sich je nach Bundesland und Zeit – prüfe die genauen Angaben bei der jeweiligen Universität, dem Justizprüfungsamt des Bundeslandes und der Ausländerbehörde.*
MD;
        $enBody = <<<'MD'
You hold a law degree from Turkey (or another country) and you are looking for a future in Germany. The most common question is: "What can I do here with this degree?" The honest answer is a mix of disappointment and opportunity. This article explains what a foreign law degree is really worth on the German job market — which doors it opens and which it does not.

## Realistic expectation: not practising law, but specialisation and international roles

Let us be clear from the start: **your foreign law degree will not make you a lawyer (Rechtsanwalt / Volljurist) in Germany.** Becoming a German lawyer requires **two Staatsexamen** (the first exam + the second exam after the roughly two-year **Referendariat**), and this path is **entirely in German** and fully tailored to German law. A Turkish (non-EU) jurist cannot enrol directly in this system; recognition of foreign degrees for the Staatsexamen route is **very limited** (verify your exact situation with the Justizprüfungsamt of the relevant federal state).

Is that a dead end? No. The realistic and valuable expectation is: **not practising law, but specialisation and international roles.** The German legal market makes room for foreign-trained jurists in international law, commercial law, compliance and international organisations — especially when your knowledge of your home country's law and language becomes an advantage.

## Paths: from LL.M. to an international firm

The most common tool for turning a foreign law degree into value on the German job market is an **LL.M.** (Master of Laws, ~1 year, often taught in English). The LL.M. will not make you a lawyer, but it gives you a specialisation, a network and a German qualification. Here are the main paths:

| Path | What it delivers | German qualification (Staatsexamen) required? |
|---|---|---|
| International law firm (LL.M. + English) | Cross-border deals, international clients | Yes for the lawyer title; often no in support/specialist roles |
| Compliance / regulation | Corporate rule-compliance, sanctions, KYC | Usually no |
| Commercial law (Wirtschaftsjurist-style roles) | Contracts, corporate law, in-house support | Usually no |
| International organisations / NGOs | Human rights, international law, policy | Usually no |
| LegalTech / consulting | Processes, data, legal technology | No |

**Bold fact:** Most of these roles do not require the title "Rechtsanwalt"; what they ask for is expertise, language and an international outlook. For a detailed career map, see our cluster sibling [working in law without becoming a lawyer](/en/blog/working-in-law-in-germany-careers-beyond-becoming-a-lawyer-en).

## Doors that open if you learn German

International roles are possible with an English-taught LL.M., but **learning German multiplies your job market.** An English-only profile limits you to international firms in big cities and global companies. **German at B2-C1**, on the other hand, opens up: in-house/compliance positions in German companies, public and regulatory institutions, consulting firms and a far wider pool of employers.

In short: **English opens the door, German multiplies the rooms.** German is mandatory for legal practice anyway; but even for legal careers beyond practice, German is your highest-return investment (as of 2025/2026 the majority of job postings still require German; verify).

## The 18-month job-search window after graduation

One of Germany's strongest cards is this: international graduates of a German university (for example an LL.M.) can **extend their residence permit by up to about 18 months after graduation to look for a qualified job** (as of 2025/2026; confirm the exact duration and conditions with the immigration office, the Ausländerbehörde). During this time you may take any job and focus on finding a position that fits your field.

This makes the "foreign law degree + an LL.M. in Germany" strategy especially sensible: the degree places you inside the German job market, and the 18-month window gives you a real buffer to land your first job. The article [master's vs. job-seeker visa](/en/blog/germany-masters-vs-job-seeker-visa-two-keys-career-en) treats both keys together and guides this planning.

## Strategy: choose your LL.M. field, language, network, internship

There is a formula for turning a foreign law degree into a job. Build on four pillars:

1. **Choose your LL.M. field deliberately.** Not a random "general law" master, but a market-relevant specialisation: international commercial law, IP/technology law, tax law, competition law or EU law. Explore English-taught options in the article [English-taught LL.M. without German](/en/blog/english-taught-law-llm-masters-in-germany-without-german-en).
2. **Language.** Learn German in parallel during the LL.M. (target B2-C1). By graduation, your language should be ready too.
3. **Network.** Career days, law-firm events, alumni networks, LinkedIn. In Germany most jobs are found through relationships, not adverts.
4. **Internship / Werkstudent.** Work part-time during the master at an international firm or in a company's legal department. That way you are not "inexperienced" at graduation.

For the full framework of the without-German constraints on practising, see [can foreigners practise law?](/en/blog/can-foreigners-practice-law-in-germany-staatsexamen-and-recognition-en).

## Honest challenges

Let us not sugar-coat it. Here is what you will face:

- **Recognition hurdle:** Your foreign degree does not convert into the German bar; at most, in some cases, a narrow role as a "foreign legal adviser" for your home-country law may be possible.
- **Language wall:** Most good jobs require German; an English-only profile narrows you down.
- **Competition:** German Volljuristen (who passed both exams) are the "default" candidate for many positions; you must prove your difference through internationality and specialisation.
- **Uncertainty:** Recognition, visa and job-market rules vary by federal state and year (as of 2025/2026; verify with an official source).

If you want to see how the choice of university and programme affects these challenges, the existing article [studying law in Germany as a foreigner](/en/blog/studying-law-in-germany-as-a-foreigner-staatsexamen-vs-llb-llm-en) is a good start.

## Conclusion & honest advice

Your foreign law degree is not a "lawyer's ticket" in Germany, but a **starting capital**. Turn it into this: an LL.M. in the right field + serious German + an active network + an internship, backed by the 18-month post-graduation job-search window. Let your goal be not "becoming a German lawyer", but **becoming a specialised professional in international law, compliance or commercial law**. On that path, your degree really is worth something.

*This article is general information as of early 2026 and is not legal or immigration advice. Recognition, visa deadlines, fees and job-search permit rules vary by federal state and over time — verify the exact details with the relevant university, the state Justizprüfungsamt and the Ausländerbehörde.*
MD;

        $variants = [
            'tr' => ['slug'=>'what-to-do-with-a-foreign-law-degree-in-germany-job-market',    'title'=>'Yabancı Hukuk Diplomasıyla Almanya\'da Ne Yapılır? İş Piyasası (2026)', 'excerpt'=>'Türkiye\'den hukuk diploman Almanya\'da avukatlık kapısını açmaz ama işe yarar: LL.M. ile uzmanlaşma, uluslararası roller, compliance ve ticaret hukuku. Almanca, network, staj ve mezuniyet sonrası 18 aylık iş-arama penceresiyle gerçekçi bir strateji.', 'meta_title'=>'Yabancı Hukuk Diplomasıyla Almanya İş Piyasası (2026)', 'meta_description'=>'Yabancı hukuk diploman Almanya\'da avukat yapmaz ama uzmanlaşma, uluslararası roller ve compliance için değerlidir. LL.M., Almanca, network, staj ve 18 aylık iş-arama stratejisi.', 'body'=>$trBody],
            'de' => ['slug'=>'what-to-do-with-a-foreign-law-degree-in-germany-job-market-de', 'title'=>'Ausländischer Jura-Abschluss in Deutschland: Der Arbeitsmarkt (2026)', 'excerpt'=>'Dein ausländischer Jura-Abschluss macht dich in Deutschland nicht zum Anwalt, ist aber wertvoll: LL.M. zur Spezialisierung, internationale Rollen, Compliance und Wirtschaftsrecht. Realistische Strategie mit Deutsch, Netzwerk, Praktikum und dem 18-Monate-Fenster für die Jobsuche.', 'meta_title'=>'Ausländischer Jura-Abschluss: Arbeitsmarkt Deutschland (2026)', 'meta_description'=>'Ein ausländischer Jura-Abschluss macht dich in Deutschland nicht zum Anwalt, ist aber wertvoll für Spezialisierung, internationale Rollen und Compliance. LL.M., Deutsch, Netzwerk, Praktikum und 18-Monate-Jobsuche.', 'body'=>$deBody],
            'en' => ['slug'=>'what-to-do-with-a-foreign-law-degree-in-germany-job-market-en', 'title'=>'What To Do With a Foreign Law Degree in Germany: The Job Market (2026)', 'excerpt'=>'Your foreign law degree will not make you a lawyer in Germany, but it is valuable: an LL.M. for specialisation, international roles, compliance and commercial law. A realistic strategy with German, networking, an internship and the 18-month post-graduation job-search window.', 'meta_title'=>'Foreign Law Degree in Germany: The Job Market (2026)', 'meta_description'=>'A foreign law degree will not make you a lawyer in Germany but is valuable for specialisation, international roles and compliance. LL.M., German, networking, internship and the 18-month job search.', 'body'=>$enBody],
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
            'what-to-do-with-a-foreign-law-degree-in-germany-job-market',
            'what-to-do-with-a-foreign-law-degree-in-germany-job-market-de',
            'what-to-do-with-a-foreign-law-degree-in-germany-job-market-en',
        ])->delete();
    }
};
