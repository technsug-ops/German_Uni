<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da sosyal hizmet okumaya değer mi? Dürüst gerçek (2026).
 * Doğrulandı: Soziale Arbeit düzenlenmiş meslek (staatliche Anerkennung); yüksek talep + stabil ama
 * Almanca C1 en büyük bariyer (danışan işi + SGB), İngilizce program nadir; TVöD-SuE ~42-48k giriş.
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim. slug-bazlı idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = 'c9d40000-4444-4a5f-9f60-cc10dd16aa04';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'universities')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da sosyal hizmet (Soziale Arbeit / Sozialpädagogik) okumaya **değer mi?** Kısa dürüst cevap: **Almancan güçlüyse evet, harika bir seçim; Almancan yoksa çok zor.** Bu yazı reklam değil, dengeli bir gerçeklik kontrolü. Cazibeyi de, en büyük bariyeri de olduğu gibi anlatıyoruz.

## Cazibe: yüksek talep + stabil + anlamlı
Önce iyi haber, çünkü gerçek. Almanya'da sosyal hizmet **düzenlenmiş bir meslektir** (staatliche Anerkennung ile "Staatlich anerkannte:r Sozialarbeiter:in") ve alanda ciddi bir **Fachkräftemangel** (nitelikli eleman açığı) var. Talep çok yüksek: gençlik yardımı (**Jugendamt/Jugendhilfe**), göç ve mülteci danışmanlığı, yaşlı ve engelli desteği, okul sosyal hizmeti, bağımlılık, aile.

- **İş bulma kaygısı çok düşük:** Diplomanı ve staatliche Anerkennung'unu aldıysan iş neredeyse garanti.
- **Stabil ve güvenceli:** Çoğu iş **TVöD-SuE** (Sozial- und Erziehungsdienst) tarifesine bağlı; düzenli zam, güvenceli sözleşme.
- **Anlamlı:** İnsanlarla doğrudan çalışırsın; "işim ne işe yarıyor" sorusunu nadiren sorarsın.
- **Kalıcı oturuma yol:** İstikrarlı, tarifeli bir meslek Almanya'da yerleşme (Niederlassungserlaubnis) için sağlam bir temeldir.

Yani meslek olarak sosyal hizmet **sağlam**. Sorun meslekte değil, ona giden **dilde**.

## Almanca C1 bariyeri — en büyük engel
Burası yazının kalbi ve en dürüst kısmı. Sosyal hizmet **dil-yoğun** bir alandır — belki de en dil-yoğun mesleklerden biri.

- İşin özü **danışan işi (Klientenarbeit)**: insanlarla konuşmak, dinlemek, güven kurmak, çatışma çözmek. Bunu **akıcı, doğal Almancasız** yapamazsın.
- Alman **sosyal hukukunu (Sozialgesetzbuch, SGB)** bilmen gerekir — SGB II, SGB VIII (çocuk/gençlik), SGB IX, SGB XII... Bu metinler ağır hukuki Almancadır.
- Raporlar, dava dosyaları, kurumlar arası yazışma: hepsi yazılı Almanca.

**Gerçek şu:** Bachelor programları neredeyse tamamen **Almanca**dır ve genelde **C1** ister. Ama asıl mesele sınav değil — **çalışırken günlük olarak akıcı Almanca gerekir.** B2 ile diploma alsan bile, kırılgan ailelerle veya travma yaşamış kişilerle çalışırken dil eksiği hem seni hem danışanı zorlar. Bu meslekte Almanca **"olsa iyi olur" değil, mesleğin kendisidir.**

## İngilizce program azlığı
Diğer bazı alanlarda "İngilizce yüksek lisansla gir, sonra Almanca öğrenirim" stratejisi işe yarar. Sosyal hizmette **bu yol büyük ölçüde kapalıdır.**

- İngilizce sosyal hizmet Bachelor programı Almanya'da **çok nadirdir** — çünkü mesleğin kendisi Alman sistemine (SGB, Jugendamt, yerel kurumlar) gömülüdür.
- İngilizce bir sosyal bilim/uluslararası sosyal hizmet master'ı bulsan bile, **staatliche Anerkennung ve iş piyasası yine Almanca ister.**
- Yani "İngilizce oku, sonra hallederim" burada gerçekçi değil. Bu alanda **Almancaya yatırım yapmadan** ilerlemek neredeyse imkânsız.

Alan konusunda kararsızsan ve İngilizce esneklik istiyorsan, [sosyoloji ve sosyal bilimler](/tr/blog/studying-sociology-and-social-sciences-in-germany-as-a-foreigner) veya [psikoloji diplomasıyla neler yapılır](/tr/blog/what-can-you-do-with-a-psychology-degree-in-germany) yazılarına da bak — kariyer profilleri farklıdır.

## Maaş vs iş güvencesi dengesi
Dürüst olalım: sosyal hizmet **seni zengin etmez.** Ama **aç da bırakmaz** ve iş güvencesi çok yüksektir.

| Kriter | Sosyal hizmet gerçeği (yaklaşık 2026) |
|---|---|
| Giriş maaşı (brüt/yıl) | ~42.000–48.000€ (TVöD-SuE, doğrula) |
| Maaş artışı | Tarifeli, düzenli kademe artışı; sıçramalar sınırlı |
| İş güvencesi | Çok yüksek (kalıcı talep, Fachkräftemangel) |
| Tavan | Mühendislik/BT'ye göre düşük; yönetim/uzmanlıkla artar |
| Anlam / tükenmişlik | Yüksek anlam ama duygusal yük de yüksek |

Karşılaştırma: Yazılım veya mühendislik daha yüksek tavan sunar ama daha rekabetçidir. Sosyal hizmet **düşük tavan + çok yüksek istikrar** dengesidir. "Az ama garanti" mi, "çok ama belirsiz" mi istediğine bağlı. Ayrıca dikkat: **duygusal tükenmişlik (Burnout)** bu alanda gerçek bir risktir — iyi kurumlar süpervizyon (Supervision) sağlar, ama iş kolay değildir.

Maaş ve alanların ayrıntısı için: [Almanya'da sosyal hizmet uzmanı olarak çalışmak: alanlar, maaş, gerçek](/tr/blog/working-as-a-social-worker-in-germany-fields-salary-and-reality).

## Kimler için mantıklı — kimler için değil
Net konuşalım.

**Senin için MANTIKLI, eğer:**
- Almancan **zaten C1 seviyesinde** ya da öğrenmeye ciddi kararlıysan;
- İnsanlarla doğrudan çalışmayı, dinlemeyi, yardım etmeyi seviyorsan;
- **İstikrar ve güvenceyi** yüksek maaşa tercih ediyorsan;
- Almanya'da uzun vadeli **yerleşmeyi** hedefliyorsan.

**Senin için MANTIKSIZ, eğer:**
- Almanca öğrenmeye niyetin yok ve "İngilizce idare ederim" diyorsan — bu alanda idare edemezsin;
- Öncelikli hedefin **yüksek gelir** ise;
- Ofiste veri/teknikle çalışmayı, insan yükünden uzak durmayı istiyorsan;
- Bürokrasi ve duygusal yükü (kriz, travma, çatışma) taşımak istemiyorsan.

Kaba özet: **Almancası güçlü, insanlarla çalışmayı seven biri için Almanya'da sosyal hizmet nadir bulunan stabil ve anlamlı bir yoldur. Almancası olmayan biri için ise bariyer gerçekten yüksektir.**

## Alternatif: zaten uzmansan Anerkennung
Önemli bir ayrım: **Türkiye'de zaten sosyal hizmet veya sosyal pedagoji okuduysan**, sıfırdan Almanya'da okumana gerek yok. Bunun için **Anerkennung** (diploma tanınması) yolu var: diplomanı denklik için sunarsın, eksik varsa uyum/pratik tamamlarsın ve **C1 Almanca** ile staatliche Anerkennung'a ulaşırsın. Bu, yeniden 3-4 yıl okumaktan çok daha hızlıdır.

Detay için: [Yurtdışı sosyal hizmet diplomanı Almanya'da tanıtmak: Anerkennung](/tr/blog/getting-your-foreign-social-work-qualification-recognized-in-germany-anerkennung). Alana yeni başlıyorsan ise sıfırdan okuma rehberi: [Almanya'da sosyal hizmet okumak](/tr/blog/studying-social-work-soziale-arbeit-in-germany-as-a-foreigner).

## Sonuç & dürüst tavsiye
Sosyal hizmet Almanya'da **değerli, stabil ve anlamlı** bir meslektir — ve **kimse sana iş bulamama korkusu yaşatmaz.** Ama tek ve en büyük şart bellidir: **Almanca.** Almancan güçlüyse ya da öğrenmeye kararlıysan bu alan sana harika bir hayat kurabilir. Almancaya yatırım yapmayacaksan, dürüst tavsiyemiz: ya bu alandan vazgeç ya da önce dile odaklan. Okul seçiminde prestij tuzağına düşme — bu alanda önemli olan tanınırlık ve uygulama, sıralama değil: [Almanya'da üniversite prestiji ve sıralamalar nasıl işler](/tr/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one).

*Bu yazı 2026 başındaki bilgilere dayanır ve genel bilgilendirme amaçlıdır. Tarifeler (TVöD-SuE), maaşlar, Anerkennung kuralları ve dil şartları değişebilir; kesin ve güncel bilgiyi ilgili eyalet makamı, üniversite ve işverenden doğrula.*
MD;
        $deBody = <<<'MD'
Lohnt es sich, in Deutschland **Soziale Arbeit** (bzw. Sozialpädagogik) zu studieren? Die kurze, ehrliche Antwort: **Wenn dein Deutsch stark ist, ja – eine hervorragende Wahl. Wenn du kein Deutsch kannst, wird es sehr schwer.** Dieser Artikel ist keine Werbung, sondern ein ehrlicher Realitätscheck – mit Reiz und größter Hürde.

## Der Reiz: hohe Nachfrage + stabil + sinnvoll
Zuerst die gute Nachricht, denn sie stimmt. Soziale Arbeit ist in Deutschland ein **reglementierter Beruf** (mit staatlicher Anerkennung zur/zum "Staatlich anerkannte:n Sozialarbeiter:in"), und es herrscht ein spürbarer **Fachkräftemangel**. Die Nachfrage ist sehr hoch: Jugendhilfe (**Jugendamt**), Migrations- und Flüchtlingsberatung, Alten- und Behindertenhilfe, Schulsozialarbeit, Sucht, Familie.

- **Sehr geringe Jobsorge:** Mit Abschluss und staatlicher Anerkennung ist ein Job praktisch sicher.
- **Stabil und sicher:** Die meisten Stellen laufen nach **TVöD-SuE** (Sozial- und Erziehungsdienst): geregelte Steigerungen, sichere Verträge.
- **Sinnvoll:** Du arbeitest direkt mit Menschen – die Frage nach dem Sinn stellt sich selten.
- **Weg zur Niederlassung:** Ein stabiler, tariflicher Beruf ist eine solide Basis für einen dauerhaften Aufenthalt.

Als Beruf ist Soziale Arbeit also **solide**. Das Problem liegt nicht im Beruf, sondern im **Weg dorthin: der Sprache.**

## Die C1-Hürde – das größte Hindernis
Das ist das Herzstück dieses Artikels und der ehrlichste Teil. Soziale Arbeit ist ein **sprachintensives** Feld – vielleicht eines der sprachintensivsten überhaupt.

- Der Kern ist **Klientenarbeit**: reden, zuhören, Vertrauen aufbauen, Konflikte lösen. Ohne **flüssiges, natürliches Deutsch** geht das nicht.
- Du musst das deutsche **Sozialrecht (Sozialgesetzbuch, SGB)** kennen – SGB II, SGB VIII (Kinder/Jugend), SGB IX, SGB XII. Das ist schweres juristisches Deutsch.
- Berichte, Fallakten, Schriftverkehr mit Behörden: alles schriftliches Deutsch.

**Die Wahrheit:** Bachelor-Programme sind fast vollständig **auf Deutsch** und verlangen meist **C1**. Doch die eigentliche Sache ist nicht die Prüfung – **im Berufsalltag brauchst du täglich flüssiges Deutsch.** Selbst mit einem Abschluss auf B2-Niveau wird die Arbeit mit verletzlichen oder traumatisierten Menschen durch Sprachlücken erschwert – für dich und deine Klient:innen. In diesem Beruf ist Deutsch **kein "nice to have", sondern der Beruf selbst.**

## Wenige englischsprachige Programme
In manchen Fächern funktioniert die Strategie "erst englischer Master, dann Deutsch lernen". In der Sozialen Arbeit ist dieser Weg **weitgehend versperrt.**

- Englischsprachige Bachelor in Sozialer Arbeit sind in Deutschland **sehr selten** – weil der Beruf im deutschen System (SGB, Jugendamt, lokale Träger) verankert ist.
- Selbst mit einem englischen Master in Sozialwissenschaften/Internationaler Sozialer Arbeit verlangen **staatliche Anerkennung und Arbeitsmarkt trotzdem Deutsch.**
- "Englisch studieren, den Rest später regeln" ist hier also nicht realistisch. Ohne **Investition ins Deutsch** kommst du in diesem Feld kaum voran.

Wenn du beim Fach unsicher bist und mehr englische Flexibilität willst, wirf einen Blick auf [Soziologie und Sozialwissenschaften](/de/blog/studying-sociology-and-social-sciences-in-germany-as-a-foreigner-de) oder [was du mit einem Psychologie-Abschluss machen kannst](/de/blog/what-can-you-do-with-a-psychology-degree-in-germany-de) – die Karriereprofile sind anders.

## Gehalt vs. Jobsicherheit
Sei ehrlich: Soziale Arbeit macht dich **nicht reich.** Aber sie lässt dich auch **nicht hungern** – und die Jobsicherheit ist sehr hoch.

| Kriterium | Realität Soziale Arbeit (ca. 2026) |
|---|---|
| Einstiegsgehalt (brutto/Jahr) | ~42.000–48.000€ (TVöD-SuE, prüfen) |
| Gehaltsentwicklung | Tariflich, geregelte Stufen; Sprünge begrenzt |
| Jobsicherheit | Sehr hoch (Dauernachfrage, Fachkräftemangel) |
| Gehaltsdecke | Niedriger als in Technik/IT; steigt mit Leitung/Spezialisierung |
| Sinn / Belastung | Hoher Sinn, aber auch hohe emotionale Last |

Zum Vergleich: Software oder Ingenieurwesen bieten eine höhere Decke, sind aber kompetitiver. Soziale Arbeit ist die Balance aus **niedriger Decke + sehr hoher Stabilität.** Es hängt davon ab, ob du "weniger, aber sicher" oder "mehr, aber unsicher" willst. Beachte außerdem: **emotionale Erschöpfung (Burnout)** ist hier ein echtes Risiko – gute Träger bieten **Supervision**, aber leicht ist die Arbeit nicht.

Mehr zu Gehalt und Feldern: [Als Sozialarbeiter:in in Deutschland arbeiten: Felder, Gehalt, Realität](/de/blog/working-as-a-social-worker-in-germany-fields-salary-and-reality-de).

## Für wen es sinnvoll ist – und für wen nicht
Klartext.

**Für dich SINNVOLL, wenn:**
- dein Deutsch **schon C1** ist oder du fest entschlossen bist, es zu lernen;
- du gern direkt mit Menschen arbeitest, zuhörst, hilfst;
- du **Stabilität und Sicherheit** einem hohen Gehalt vorziehst;
- du langfristig in Deutschland **bleiben** willst.

**Für dich NICHT sinnvoll, wenn:**
- du kein Deutsch lernen willst und denkst "mit Englisch komme ich durch" – hier kommst du damit nicht durch;
- dein Hauptziel ein **hohes Einkommen** ist;
- du lieber mit Daten/Technik im Büro arbeitest, weg von der menschlichen Last;
- du Bürokratie und emotionale Belastung (Krise, Trauma, Konflikt) nicht tragen willst.

Kurz gesagt: **Für jemanden mit starkem Deutsch, der gern mit Menschen arbeitet, ist Soziale Arbeit in Deutschland ein selten stabiler und sinnvoller Weg. Für jemanden ohne Deutsch ist die Hürde wirklich hoch.**

## Alternative: Anerkennung, wenn du schon Fachkraft bist
Ein wichtiger Unterschied: Wenn du **in der Türkei bereits Soziale Arbeit oder Sozialpädagogik studiert hast**, musst du nicht bei null neu studieren. Dafür gibt es den Weg der **Anerkennung**: Du reichst dein Diplom zur Gleichwertigkeitsprüfung ein, gleichst fehlende Teile mit Anpassung/Praxis aus und erreichst mit **C1-Deutsch** die staatliche Anerkennung. Das ist viel schneller als 3–4 Jahre neu zu studieren.

Details: [Deine ausländische Sozialarbeits-Qualifikation in Deutschland anerkennen lassen: Anerkennung](/de/blog/getting-your-foreign-social-work-qualification-recognized-in-germany-anerkennung-de). Wenn du neu einsteigst, hier der Studienführer: [Soziale Arbeit in Deutschland studieren](/de/blog/studying-social-work-soziale-arbeit-in-germany-as-a-foreigner-de).

## Fazit & ehrlicher Rat
Soziale Arbeit ist in Deutschland ein **wertvoller, stabiler und sinnvoller** Beruf – und **niemand jagt dir Angst vor Arbeitslosigkeit ein.** Doch die eine, größte Bedingung ist klar: **Deutsch.** Ist dein Deutsch stark oder bist du entschlossen, es zu lernen, kann dieses Feld dir ein gutes Leben aufbauen. Willst du nicht ins Deutsch investieren, lautet unser ehrlicher Rat: Entweder du lässt dieses Feld sein oder du fokussierst zuerst die Sprache. Fall nicht auf die Prestige-Falle herein – hier zählen Anerkennung und Praxis, nicht das Ranking: [Wie Prestige und Rankings in Deutschland funktionieren](/de/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-de).

*Dieser Artikel beruht auf dem Stand Anfang 2026 und dient der allgemeinen Information. Tarife (TVöD-SuE), Gehälter, Anerkennungsregeln und Sprachanforderungen können sich ändern; prüfe genaue und aktuelle Angaben bei der zuständigen Landesbehörde, der Hochschule und Arbeitgebern.*
MD;
        $enBody = <<<'MD'
Is studying **social work** (Soziale Arbeit / Sozialpädagogik) in Germany **worth it?** The short, honest answer: **if your German is strong, yes, it's an excellent choice; if you don't speak German, it's very hard.** This article isn't marketing — it's a balanced reality check that covers both the appeal and the biggest barrier.

## The appeal: high demand + stable + meaningful
First the good news, because it's true. In Germany social work is a **regulated profession** (with state recognition, staatliche Anerkennung, to become a "Staatlich anerkannte:r Sozialarbeiter:in"), and there is a real **skills shortage (Fachkräftemangel)**. Demand is very high: youth welfare (**Jugendamt/Jugendhilfe**), migration and refugee counselling, elderly and disability support, school social work, addiction, family.

- **Very low job anxiety:** with your degree and state recognition, a job is practically guaranteed.
- **Stable and secure:** most jobs follow the **TVöD-SuE** (Sozial- und Erziehungsdienst) pay scale — regular raises, secure contracts.
- **Meaningful:** you work directly with people; you rarely ask "what is my work good for."
- **Path to permanent residence:** a stable, scale-based profession is a solid basis for settling (Niederlassungserlaubnis) in Germany.

So as a profession, social work is **solid**. The problem isn't the job — it's the **road to it: the language.**

## The C1 barrier — the biggest obstacle
This is the heart of the article and the most honest part. Social work is a **language-intensive** field — perhaps one of the most language-intensive professions there is.

- The core is **client work (Klientenarbeit)**: talking, listening, building trust, resolving conflict. You cannot do this without **fluent, natural German.**
- You must know German **social law (Sozialgesetzbuch, SGB)** — SGB II, SGB VIII (children/youth), SGB IX, SGB XII. That's heavy legal German.
- Reports, case files, correspondence with authorities: all written German.

**The reality:** Bachelor programmes are almost entirely **in German** and usually require **C1**. But the real issue isn't the exam — **on the job you need fluent German every single day.** Even if you graduate at a B2 level, language gaps make working with vulnerable or traumatised people harder — for you and for your clients. In this profession German is **not a "nice to have" — it is the profession itself.**

## Few English-taught programmes
In some fields the strategy "get in with an English master's, then learn German" works. In social work that path is **largely closed.**

- English-taught social work bachelors in Germany are **very rare** — because the profession is embedded in the German system (SGB, Jugendamt, local providers).
- Even with an English master's in social sciences / international social work, **state recognition and the job market still demand German.**
- So "study in English, sort it out later" isn't realistic here. Without **investing in German** you can barely move forward in this field.

If you're unsure about the field and want more English flexibility, look at [sociology and social sciences](/en/blog/studying-sociology-and-social-sciences-in-germany-as-a-foreigner-en) or [what you can do with a psychology degree](/en/blog/what-can-you-do-with-a-psychology-degree-in-germany-en) — the career profiles are different.

## Salary vs. job security
Let's be honest: social work will **not make you rich.** But it also **won't leave you hungry** — and job security is very high.

| Criterion | Social work reality (approx. 2026) |
|---|---|
| Entry salary (gross/year) | ~€42,000–48,000 (TVöD-SuE, verify) |
| Salary progression | Scale-based, regular steps; jumps limited |
| Job security | Very high (permanent demand, skills shortage) |
| Ceiling | Lower than engineering/IT; rises with management/specialisation |
| Meaning / burden | High meaning but also high emotional load |

For comparison: software or engineering offer a higher ceiling but are more competitive. Social work is the balance of **low ceiling + very high stability.** It depends on whether you want "less but guaranteed" or "more but uncertain." Also note: **emotional exhaustion (burnout)** is a real risk here — good providers offer **supervision (Supervision)**, but the work is not easy.

More on salary and fields: [Working as a social worker in Germany: fields, salary and reality](/en/blog/working-as-a-social-worker-in-germany-fields-salary-and-reality-en).

## Who it makes sense for — and who it doesn't
Straight talk.

**It MAKES SENSE for you if:**
- your German is **already C1** or you're seriously committed to learning it;
- you enjoy working directly with people, listening, helping;
- you value **stability and security** over a high salary;
- you aim to **settle** in Germany long term.

**It does NOT make sense for you if:**
- you don't intend to learn German and think "I'll get by in English" — you won't get by in this field;
- your primary goal is **high income**;
- you'd rather work with data/technology in an office, away from the human load;
- you don't want to carry bureaucracy and emotional weight (crisis, trauma, conflict).

In short: **for someone with strong German who enjoys working with people, social work in Germany is a rarely stable and meaningful path. For someone without German, the barrier is genuinely high.**

## Alternative: Anerkennung if you're already a professional
An important distinction: if you **already studied social work or social pedagogy in Turkey**, you don't need to study from scratch in Germany. There's the **Anerkennung** (qualification recognition) route: you submit your degree for an equivalence assessment, close any gaps with adaptation/practice, and reach state recognition with **C1 German**. That's much faster than studying 3–4 years again.

Details: [Getting your foreign social work qualification recognized in Germany: Anerkennung](/en/blog/getting-your-foreign-social-work-qualification-recognized-in-germany-anerkennung-en). If you're starting fresh, here's the study guide: [Studying social work in Germany](/en/blog/studying-social-work-soziale-arbeit-in-germany-as-a-foreigner-en).

## Conclusion & honest advice
Social work in Germany is a **valuable, stable and meaningful** profession — and **no one will scare you with fear of unemployment.** But the one, biggest condition is clear: **German.** If your German is strong or you're determined to learn it, this field can build you a good life. If you won't invest in German, our honest advice is: either drop this field or focus on the language first. Don't fall for the prestige trap — here recognition and practice matter, not the ranking: [How university prestige and rankings work in Germany](/en/blog/how-university-prestige-and-rankings-work-in-germany-choosing-the-right-one-en).

*This article reflects information as of early 2026 and is for general guidance only. Pay scales (TVöD-SuE), salaries, recognition rules and language requirements can change; verify exact and current details with the relevant state authority, the university and employers.*
MD;

        $variants = [
            'tr' => ['slug'=>'is-studying-social-work-in-germany-worth-it-honest-reality',    'title'=>'Almanya\'da Sosyal Hizmet Okumaya Değer mi? Dürüst Gerçek (2026)', 'excerpt'=>'Almanya\'da sosyal hizmet (Soziale Arbeit) okumaya değer mi? Yüksek talep ve stabil bir meslek ama Almanca C1 en büyük bariyer. Kimler için mantıklı, kimler için değil — dürüst bir gerçeklik kontrolü.', 'meta_title'=>'Almanya\'da Sosyal Hizmet Okumaya Değer mi? (2026)', 'meta_description'=>'Sosyal hizmet Almanya\'da stabil ve yüksek talepli ama Almanca C1 şart, İngilizce program nadir. Kimler için mantıklı, kimler için değil — dürüst gerçek.', 'body'=>$trBody],
            'de' => ['slug'=>'is-studying-social-work-in-germany-worth-it-honest-reality-de', 'title'=>'Lohnt sich Soziale Arbeit in Deutschland? Die ehrliche Realität (2026)', 'excerpt'=>'Lohnt sich ein Studium der Sozialen Arbeit in Deutschland? Hohe Nachfrage und stabil, aber Deutsch (C1) ist die größte Hürde. Für wen es sinnvoll ist – ein ehrlicher Realitätscheck.', 'meta_title'=>'Lohnt sich Soziale Arbeit in Deutschland? (2026)', 'meta_description'=>'Soziale Arbeit ist stabil und stark nachgefragt, doch C1-Deutsch ist Pflicht und englische Programme sind selten. Für wen es sinnvoll ist – und für wen nicht.', 'body'=>$deBody],
            'en' => ['slug'=>'is-studying-social-work-in-germany-worth-it-honest-reality-en', 'title'=>'Is Studying Social Work in Germany Worth It? The Honest Reality (2026)', 'excerpt'=>'Is studying social work in Germany worth it? High demand and stable, but German (C1) is the biggest barrier. Who it makes sense for — an honest reality check.', 'meta_title'=>'Is Studying Social Work in Germany Worth It? (2026)', 'meta_description'=>'Social work in Germany is stable and in high demand, but C1 German is required and English programmes are rare. Who it makes sense for — and who it doesn\'t.', 'body'=>$enBody],
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
            'is-studying-social-work-in-germany-worth-it-honest-reality',
            'is-studying-social-work-in-germany-worth-it-honest-reality-de',
            'is-studying-social-work-in-germany-worth-it-honest-reality-en',
        ])->delete();
    }
};
