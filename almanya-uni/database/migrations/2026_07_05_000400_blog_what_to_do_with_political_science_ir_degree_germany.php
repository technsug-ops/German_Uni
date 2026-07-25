<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Siyaset Bilimi / IR diplomasıyla Almanya iş piyasası (2026).
 * Doğrulandı: IR/poli-sci generalist diploma, tek net yol yok, rekabetçi; uzmanlaşma +
 * staj + network + diller kritik. Mezuniyet sonrası 18 ay iş-arama izni. Diplomatlık
 * (Auswärtiges Amt) Alman vatandaşlığı ister. Sayılar 2025/2026 yaklaşık, doğrulanmalı.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'c6d40000-4444-4f7b-9fa0-cc04dd09ff04';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Siyaset Bilimi veya Uluslararası İlişkiler (IR) diploman elinde ve herkes aynı soruyu soruyor: "Bununla ne iş yapacaksın?" Dürüst cevap: tek bir "meslek" yok. Bu bir **generalist** diploma — kapıyı çok sektöre aralar ama hiçbirine seni otomatik sokmaz. Bu yazı, Almanya'daki gerçek iş piyasasını süslemeden anlatır ve uluslararası bir öğrenci olarak nasıl strateji kuracağını gösterir.

## Generalist diploma: seni nereye götürür?

IR/siyaset bilimi sana somut bir teknik beceri (kod yazmak, köprü tasarlamak gibi) vermez. Sana **analiz, yazma, argüman kurma, veriyi yorumlama ve bağlam okuma** becerisi verir. Bu beceriler değerlidir ama piyasada "mühendis" gibi net bir etiketi yoktur. Sonuç: işveren seni bir kalıba oturtamaz, bu yüzden **sen kendini konumlandırmak zorundasın**.

Bunun anlamı şu: iyi haber, önünde çok yol var (kuruluşlar, danışmanlık, gazetecilik, akademi, NGO, kamu). Kötü haber, hiçbiri seni beklemiyor; her birine ayrı bir hikâye, staj ve network ile hazırlanman gerekir.

## Kariyer yolları: nereye gidebilirsin?

Aşağıdaki tablo yaklaşık bir haritadır (2025/2026 itibarıyla, giriş maaşları kaba tahmin — mutlaka doğrula):

| Yol | Örnek işverenler | Giriş maaşı (yaklaşık, yıl) | Gerçek |
|---|---|---|---|
| Uluslararası kuruluşlar | BM, AB kurumları, OECD, IMF | Değişken, sık süreli sözleşme | İngilizce-dostu ama **çok rekabetçi**, staj şart |
| Kalkınma | GIZ, KfW (Bonn/Eschborn) | ~45–55k | Almanca çoğu rolde beklenir |
| Think-tank / araştırma | SWP, DGAP, ECFR (Berlin) | ~40–50k | Uzmanlık + yayın portföyü ister |
| Siyasi danışmanlık / public affairs | Ajanslar, şirket lobisi | ~45–55k | Berlin merkezli, network kritik |
| NGO / vakıflar | Konrad-Adenauer, Friedrich-Ebert Stiftung | Genelde altta | Anlamlı ama maddi tavan düşük |
| Gazetecilik / iletişim | Medya, kurumsal iletişim | Değişken | Portfolyo + dil belirleyici |
| Akademi | Üniversite, doktora | Burs/asistanlık | Uzun yol, süreli sözleşme yaygın |

Kalın gerçek: bu yolların çoğunda **erken kariyerde süreli (befristet) sözleşme** normaldir ve maaşlar mühendislik/BT'nin altındadır. Buna karşılık iş anlamlı ve uluslararası olabilir.

## Rekabet gerçeği + staj/network stratejisi

En sık yapılan hata: "diplomayı alırım, iş kendiliğinden gelir" sanmak. Gelmez. Bu alanda **kimi tanıdığın ve ne yaptığın**, notundan çoğu zaman daha önemlidir.

- **Staj (Praktikum) tek gerçek anahtar.** Öğrenciyken 2–3 staj (think-tank, NGO, GIZ, bir bakanlık, AB projesi) yapmadan mezun olma. İşverenler "yapılmış iş" görmek ister.
- **Network** LinkedIn'de değil, salonlarda kurulur: konferanslar, panel, model BM, gönüllülük. Berlin bu yüzden kritik.
- **Uzmanlaş.** "IR mezunuyum" değil, "AB dijital politikası / MENA güvenliği / iklim finansmanı üzerine çalışıyorum" de. Dar ve derin, geniş ve sığdan iyidir.

## Mezuniyet sonrası: 18 aylık iş-arama izni

Almanya'da bir Alman üniversitesinden mezun olan uluslararası öğrenciler, mezuniyet sonrası **18 aya kadar iş-arama (job-seeker) oturumu** için başvurabilir (2025/2026 itibarıyla; resmi kaynaktan / yabancılar dairesinden doğrula). Bu süre bu alanda **altın değerinde**, çünkü ilk iş bulmak zaman alır.

Stratejik kullanım: bu 18 ayı boş geçirme. Bir yandan başvururken bir yandan **staj/gönüllülük/proje işi** ile CV'ni doldur. Uzun vadede kariyerini şekillendirmek istiyorsan [Master mi yoksa iş-arama vizesi mi: kariyerin iki anahtarı](/tr/blog/germany-masters-vs-job-seeker-visa-two-keys-career) yazısı bu iki yolu karşılaştırıyor.

## Almanca + diller gerçeği

Dürüst ol kendine: **Almanca bu piyasada büyük fark yaratır.** Uluslararası kuruluşlar İngilizce çalışır ama en rekabetçi olanlar da onlardır. Alman kamu kurumları, GIZ'in birçok rolü, siyasi danışmanlık, yerel NGO'lar için **Almanca (B2, ideali C1)** çoğu zaman pratik bir zorunluluktur.

Ek diller (Arapça, Rusça, Fransızca, İspanyolca) IR'de somut bir avantajdır — bölgesel uzmanlıkla birleştiğinde seni ayırır. Türkçe ana dilin de doğru bağlamda (MENA, göç, AB-Türkiye) bir varlıktır.

**Diplomatlık uyarısı:** Alman dış hizmetinde (Auswärtiges Amt) diplomat olmak **Alman vatandaşlığı** ister. Türk vatandaşı bir öğrenci için bu, vatandaşlık almadıkça kapalı bir kapıdır — ama uluslararası kuruluşlar, NGO'lar ve özel sektör açıktır.

## Uluslararası öğrenci stratejisi: 5 net adım

1. **Uzmanlaş** — bir bölge veya politika alanı seç, derinleş.
2. **Dil yatırımı yap** — Almanca B2+'ya çık; ek bir dünya dili ekle.
3. **Staj biriktir** — mezun olmadan en az 2, mümkünse 3.
4. **Berlin/Bonn'a yakın ol** — ekosistem orada.
5. **Doğru master seç** — İngilizce IR/public policy master alan değiştirmeni ya da derinleşmeni sağlar; [İngilizce IR / siyaset bilimi master programları](/tr/blog/english-taught-international-relations-political-science-masters-in-germany) ve [yabancı olarak IR / siyaset bilimi okumak](/tr/blog/studying-international-relations-political-science-in-germany-as-a-foreigner) yazıları başlangıç noktan.

Sektörün gerçek işverenleri ve maaş detayları için [Almanya'da IR ve politikada çalışmak](/tr/blog/working-in-international-relations-policy-organizations-in-germany) yazısına; okul seçiminde isim mi program mı sorusu için [Almanya'da üniversite prestiji ve sıralamalar](/tr/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one) yazısına bak.

## Sonuç & dürüst tavsiye

IR/siyaset bilimi diploması bir bilet değil, bir **başlangıç sermayesidir**. Tek başına iş garantisi vermez; rekabetçi bir piyasada seni öne çıkaran şey uzmanlık + staj + network + dillerdir. Yol anlamlı ve uluslararası olabilir ama maaşlar teknik alanların altında, erken kariyerde süreli sözleşmeler yaygındır. Bunu bilerek girersen hayal kırıklığı yaşamazsın. En net tavsiye: diplomayı beklerken oturma — bugünden uzmanlaş, dil öğren, staj yap, doğru şehirde ol.

*Bu yazı 2026 başı itibarıyla genel bilgilendirme amaçlıdır; maaşlar, oturum süreleri ve başvuru kuralları değişebilir. Güncel bilgiyi resmi kaynaklardan (DAAD, uni-assist, ilgili yabancılar dairesi ve işverenler) doğrula.*
MD;

        $deBody = <<<'MD'
Du hast deinen Abschluss in Politikwissenschaft oder Internationalen Beziehungen (IB) in der Tasche – und alle fragen: „Was machst du damit?" Die ehrliche Antwort: Es gibt nicht **den einen** Beruf. Das ist ein **Generalisten-Abschluss** – er öffnet viele Türen, aber schiebt dich durch keine automatisch. Dieser Artikel zeigt den echten deutschen Arbeitsmarkt ohne Beschönigung und wie du als internationale:r Student:in eine Strategie baust.

## Generalisten-Abschluss: Wohin bringt er dich?

IB/Politikwissenschaft gibt dir keine konkrete technische Fertigkeit (wie Programmieren oder Brückenbau). Er gibt dir **Analyse, Schreiben, Argumentation, Dateninterpretation und Kontextverständnis**. Diese Fähigkeiten sind wertvoll, haben aber kein klares Etikett wie „Ingenieur". Ergebnis: Der Arbeitgeber kann dich nicht in eine Schublade stecken – also musst **du dich selbst positionieren**.

Die gute Nachricht: viele Wege stehen offen (Organisationen, Beratung, Journalismus, Wissenschaft, NGOs, öffentlicher Sektor). Die schlechte: keiner wartet auf dich; auf jeden musst du dich mit eigener Geschichte, Praktikum und Netzwerk vorbereiten.

## Karrierewege: Wohin kannst du gehen?

Die Tabelle ist eine grobe Karte (Stand 2025/2026, Einstiegsgehälter grobe Schätzung – bitte prüfen):

| Weg | Beispiel-Arbeitgeber | Einstiegsgehalt (ca., Jahr) | Realität |
|---|---|---|---|
| Internationale Organisationen | UN, EU-Institutionen, OECD, IWF | variabel, oft befristet | englischfreundlich, aber **sehr umkämpft**, Praktikum Pflicht |
| Entwicklung | GIZ, KfW (Bonn/Eschborn) | ~45–55k | Deutsch in den meisten Rollen erwartet |
| Think-Tank / Forschung | SWP, DGAP, ECFR (Berlin) | ~40–50k | verlangt Spezialisierung + Publikationen |
| Politikberatung / Public Affairs | Agenturen, Unternehmenslobby | ~45–55k | Berlin-zentriert, Netzwerk entscheidend |
| NGO / Stiftungen | Konrad-Adenauer-, Friedrich-Ebert-Stiftung | eher unteres Ende | sinnstiftend, aber niedrige Gehaltsdecke |
| Journalismus / Kommunikation | Medien, Unternehmenskommunikation | variabel | Portfolio + Sprache entscheiden |
| Wissenschaft | Universität, Promotion | Stipendium/Stelle | langer Weg, befristete Verträge üblich |

Fette Wahrheit: In den meisten dieser Wege ist ein **befristeter Vertrag am Anfang** normal, und die Gehälter liegen unter Ingenieurwesen/IT. Dafür kann die Arbeit sinnvoll und international sein.

## Wettbewerbsrealität + Praktikum/Netzwerk-Strategie

Der häufigste Fehler: zu glauben, „Ich hole den Abschluss, der Job kommt von selbst". Tut er nicht. In diesem Feld zählen **wen du kennst und was du gemacht hast** oft mehr als deine Note.

- **Praktikum ist der einzige echte Schlüssel.** Mach als Student:in 2–3 Praktika (Think-Tank, NGO, GIZ, ein Ministerium, ein EU-Projekt), bevor du abschließt. Arbeitgeber wollen „gemachte Arbeit" sehen.
- **Netzwerk** entsteht nicht auf LinkedIn, sondern in Sälen: Konferenzen, Panels, Model UN, Ehrenamt. Deshalb ist Berlin entscheidend.
- **Spezialisiere dich.** Nicht „IB-Absolvent", sondern „ich arbeite zu EU-Digitalpolitik / MENA-Sicherheit / Klimafinanzierung". Schmal und tief schlägt breit und flach.

## Nach dem Abschluss: 18 Monate zur Jobsuche

Internationale Absolvent:innen einer deutschen Hochschule können nach dem Abschluss eine Aufenthaltserlaubnis zur **Arbeitsuche von bis zu 18 Monaten** beantragen (Stand 2025/2026; bei der Ausländerbehörde / offiziellen Quelle prüfen). Diese Zeit ist in diesem Feld **Gold wert**, denn der erste Job braucht Zeit.

Strategisch nutzen: Verbring diese 18 Monate nicht untätig. Bewirb dich – und fülle parallel deinen Lebenslauf mit **Praktikum/Ehrenamt/Projektarbeit**. Wenn du deine Laufbahn langfristig planen willst, vergleicht der Artikel [Master oder Jobsuche-Visum: die zwei Schlüssel zur Karriere](/de/blog/germany-masters-vs-job-seeker-visa-two-keys-career-de) beide Wege.

## Deutsch + Sprachen: die Realität

Sei ehrlich zu dir: **Deutsch macht auf diesem Markt einen großen Unterschied.** Internationale Organisationen arbeiten auf Englisch – sind aber die umkämpftesten. Für deutsche Behörden, viele GIZ-Rollen, Politikberatung und lokale NGOs ist **Deutsch (B2, ideal C1)** oft eine praktische Voraussetzung.

Zusatzsprachen (Arabisch, Russisch, Französisch, Spanisch) sind in den IB ein echter Vorteil – kombiniert mit Regionalexpertise heben sie dich ab. Auch deine Muttersprache Türkisch ist im richtigen Kontext (MENA, Migration, EU-Türkei) ein Pluspunkt.

**Diplomaten-Hinweis:** Diplomat:in im Auswärtigen Amt zu werden, verlangt die **deutsche Staatsbürgerschaft**. Für eine:n türkische:n Student:in ist diese Tür geschlossen, solange keine Einbürgerung erfolgt – aber internationale Organisationen, NGOs und die Privatwirtschaft sind offen.

## Strategie für internationale Studierende: 5 Schritte

1. **Spezialisiere dich** – wähle eine Region oder ein Politikfeld, geh in die Tiefe.
2. **Investiere in Sprache** – bring Deutsch auf B2+; füge eine Weltsprache hinzu.
3. **Sammle Praktika** – mindestens 2, besser 3, vor dem Abschluss.
4. **Sei nah an Berlin/Bonn** – dort ist das Ökosystem.
5. **Wähle den richtigen Master** – ein englischsprachiger IB/Public-Policy-Master lässt dich wechseln oder vertiefen; [englischsprachige IB / Politik-Master](/de/blog/english-taught-international-relations-political-science-masters-in-germany-de) und [IB / Politikwissenschaft als Ausländer:in studieren](/de/blog/studying-international-relations-political-science-in-germany-as-a-foreigner-de) sind dein Startpunkt.

Für echte Arbeitgeber und Gehaltsdetails der Branche siehe [Arbeiten in IB und Politik in Deutschland](/de/blog/working-in-international-relations-policy-organizations-in-germany-de); zur Frage „Name oder Programm" bei der Wahl der Hochschule [Universitätsprestige und Rankings in Deutschland](/de/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-de).

## Fazit & ehrlicher Rat

Ein IB/Politik-Abschluss ist kein Ticket, sondern ein **Startkapital**. Er garantiert allein keinen Job; in einem umkämpften Markt hebt dich Spezialisierung + Praktikum + Netzwerk + Sprachen hervor. Der Weg kann sinnvoll und international sein, aber die Gehälter liegen unter technischen Feldern, und befristete Verträge sind am Anfang üblich. Wer das weiß, wird nicht enttäuscht. Der klarste Rat: Warte nicht auf den Abschluss – spezialisiere dich heute, lerne Sprachen, mach Praktika, sei in der richtigen Stadt.

*Dieser Artikel dient der allgemeinen Information zu Beginn 2026; Gehälter, Aufenthaltsdauern und Bewerbungsregeln können sich ändern. Prüfe aktuelle Angaben bei offiziellen Quellen (DAAD, uni-assist, zuständige Ausländerbehörde und Arbeitgeber).*
MD;

        $enBody = <<<'MD'
You have your degree in Political Science or International Relations (IR), and everyone asks the same thing: "What will you actually do with that?" The honest answer: there is no single "job." This is a **generalist** degree — it opens many doors but pushes you through none of them automatically. This article lays out the real German job market without sugar-coating, and how to build a strategy as an international student.

## The generalist degree: where does it take you?

IR/political science doesn't hand you a concrete technical skill (like coding or building bridges). It gives you **analysis, writing, argumentation, data interpretation and reading context**. These skills are valuable but carry no clear label like "engineer." The result: an employer can't slot you into a box — so **you have to position yourself**.

The good news: many paths are open (organizations, consulting, journalism, academia, NGOs, the public sector). The bad news: none is waiting for you; each requires its own story, internship and network to be ready for.

## Career paths: where can you go?

The table is a rough map (as of 2025/2026, entry salaries are ballpark — please verify):

| Path | Example employers | Entry salary (approx., year) | Reality |
|---|---|---|---|
| International organizations | UN, EU institutions, OECD, IMF | variable, often fixed-term | English-friendly but **very competitive**, internships required |
| Development | GIZ, KfW (Bonn/Eschborn) | ~45–55k | German expected in most roles |
| Think tanks / research | SWP, DGAP, ECFR (Berlin) | ~40–50k | demands specialization + a publication record |
| Policy consulting / public affairs | agencies, corporate lobbying | ~45–55k | Berlin-centered, network is decisive |
| NGOs / foundations | Konrad-Adenauer, Friedrich-Ebert Stiftung | usually lower end | meaningful, but low salary ceiling |
| Journalism / communications | media, corporate comms | variable | portfolio + language decide |
| Academia | university, PhD | scholarship/position | long road, fixed-term contracts common |

Bold truth: in most of these paths a **fixed-term (befristet) contract at the start** is normal, and salaries sit below engineering/IT. In exchange, the work can be meaningful and international.

## The competition reality + internship/network strategy

The most common mistake is believing "I'll get the degree and the job will come by itself." It won't. In this field, **who you know and what you've done** often matters more than your grade.

- **Internships (Praktikum) are the one real key.** Do 2–3 internships as a student (think tank, NGO, GIZ, a ministry, an EU project) before you graduate. Employers want to see "work already done."
- **Networks** are built not on LinkedIn but in rooms: conferences, panels, Model UN, volunteering. That's why Berlin matters.
- **Specialize.** Not "IR graduate" but "I work on EU digital policy / MENA security / climate finance." Narrow and deep beats broad and shallow.

## After graduation: an 18-month job-search stay

International graduates of a German university can apply for a **job-seeker residence permit of up to 18 months** after graduation (as of 2025/2026; verify with the immigration office / an official source). That window is **worth gold** in this field, because landing the first job takes time.

Use it strategically: don't spend those 18 months idle. Apply — and in parallel fill your CV with **internships/volunteering/project work**. If you want to shape your career long-term, [Master's vs. job-seeker visa: the two keys to your career](/en/blog/germany-masters-vs-job-seeker-visa-two-keys-career-en) compares both routes.

## German + languages: the reality

Be honest with yourself: **German makes a big difference in this market.** International organizations work in English — but they are also the most competitive. For German public bodies, many GIZ roles, policy consulting and local NGOs, **German (B2, ideally C1)** is often a practical requirement.

Additional languages (Arabic, Russian, French, Spanish) are a genuine advantage in IR — combined with regional expertise they set you apart. Your native Turkish, too, is an asset in the right context (MENA, migration, EU–Turkey).

**Diplomat note:** becoming a diplomat in the German foreign service (Auswärtiges Amt) requires **German citizenship**. For a Turkish student that door is closed unless you naturalize — but international organizations, NGOs and the private sector are open.

## Strategy for international students: 5 clear steps

1. **Specialize** — pick a region or policy field and go deep.
2. **Invest in language** — bring German to B2+; add a world language.
3. **Stack internships** — at least 2, ideally 3, before you graduate.
4. **Stay near Berlin/Bonn** — the ecosystem is there.
5. **Choose the right master's** — an English-taught IR/public policy master's lets you switch or deepen; [English-taught IR / political science master's](/en/blog/english-taught-international-relations-political-science-masters-in-germany-en) and [studying IR / political science as a foreigner](/en/blog/studying-international-relations-political-science-in-germany-as-a-foreigner-en) are your starting point.

For the sector's real employers and salary detail, see [working in IR and policy in Germany](/en/blog/working-in-international-relations-policy-organizations-in-germany-en); on the "name vs. program" question when picking a school, see [university prestige and rankings in Germany](/en/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-en).

## Conclusion & honest advice

An IR/political science degree is not a ticket but **starting capital**. On its own it guarantees no job; in a competitive market what sets you apart is specialization + internships + network + languages. The path can be meaningful and international, but salaries sit below technical fields and fixed-term contracts are common early on. Go in knowing that and you won't be disappointed. The clearest advice: don't sit and wait for the degree — specialize today, learn languages, do internships, be in the right city.

*This article is general information as of early 2026; salaries, residence-permit durations and application rules can change. Verify current details with official sources (DAAD, uni-assist, the relevant immigration office and employers).*
MD;

        $variants = [
            'tr' => ['slug'=>'what-to-do-with-a-political-science-ir-degree-in-germany-job-market',    'title'=>'Almanya\'da Siyaset Bilimi / IR Diplomasıyla Ne Yapılır? İş Piyasası (2026)', 'excerpt'=>'IR/siyaset bilimi generalist bir diploma: tek net kariyer yolu yok, rekabetçi bir piyasa. Kariyer yolları, maaş gerçeği, 18 aylık iş-arama izni ve uluslararası öğrenci için uzmanlaşma + staj + network + dil stratejisi.', 'meta_title'=>'Siyaset Bilimi / IR Diplomasıyla Almanya İş Piyasası (2026)', 'meta_description'=>'IR/siyaset bilimi diplomasıyla Almanya\'da ne iş yapılır? Kariyer yolları tablosu, maaş gerçeği, 18 aylık iş-arama izni ve dürüst strateji.', 'body'=>$trBody],
            'de' => ['slug'=>'what-to-do-with-a-political-science-ir-degree-in-germany-job-market-de', 'title'=>'Was mit einem Politik-/IB-Abschluss in Deutschland? Der Arbeitsmarkt (2026)', 'excerpt'=>'IB/Politikwissenschaft ist ein Generalisten-Abschluss: kein einziger klarer Karriereweg, ein umkämpfter Markt. Karrierewege, Gehaltsrealität, 18 Monate Jobsuche und eine Strategie aus Spezialisierung + Praktikum + Netzwerk + Sprachen.', 'meta_title'=>'Politik-/IB-Abschluss: Arbeitsmarkt in Deutschland (2026)', 'meta_description'=>'Was macht man mit einem IB-/Politik-Abschluss in Deutschland? Karrierewege-Tabelle, Gehaltsrealität, 18 Monate Jobsuche und ehrliche Strategie.', 'body'=>$deBody],
            'en' => ['slug'=>'what-to-do-with-a-political-science-ir-degree-in-germany-job-market-en', 'title'=>'What to Do With a Political Science / IR Degree in Germany? The Job Market (2026)', 'excerpt'=>'IR/political science is a generalist degree: no single clear career path, a competitive market. Career paths, the salary reality, the 18-month job search and a strategy of specialization + internships + network + languages.', 'meta_title'=>'Political Science / IR Degree: Germany Job Market (2026)', 'meta_description'=>'What can you do with an IR/political science degree in Germany? Career-paths table, salary reality, the 18-month job search and honest strategy.', 'body'=>$enBody],
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
            'what-to-do-with-a-political-science-ir-degree-in-germany-job-market',
            'what-to-do-with-a-political-science-ir-degree-in-germany-job-market-de',
            'what-to-do-with-a-political-science-ir-degree-in-germany-job-market-en',
        ])->delete();
    }
};
