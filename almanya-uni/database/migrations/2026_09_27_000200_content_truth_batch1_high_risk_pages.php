<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Content Truth Sprint — Batch 1: yüksek riskli editoryal düzeltmeler (TR/EN/DE, 4 çeviri kümesi, 12 URL).
 *
 *  - news   2026 Sperrkonto haberi: 11.208 € / 934 € → 11.904 € / 992 € (01.09.2024'ten beri) + excerpt/meta.
 *  - bvm    bachelor-vs-master: "genelde 3 hak / hiçbir yerde okuyamazsın / otomatik exmatrikulation" kaldırıldı;
 *           120/240 → 140 Arbeitstage; excerpt/meta; title + meta_title'dan "3 hak / 3-attempt / 3-Versuche" çerçevesi
 *           kaldırıldı (kullanıcı onayı 27.09.2026). Slug'lar BİLİNÇLİ olarak korunur (Batch 4: slug + 301).
 *  - dam    doing-a-masters: KfW "sadece kısıtlı / 2 sömestre" → KfW statü grupları (§ 16b tek başına listelenmez);
 *           120/240 → 140 Arbeitstage (+ Pflichtpraktikum sayılmaz, gönüllü staj genellikle sayılır).
 *  - baf    what-is-bafog: ~934 € → 992 € (WS 2024/25); KfW maddesine § 16b uyarısı.
 * Doğrulanmış kaynaklar (27.09.2026): § 16b Abs. 3 AufenthG (01.03.2024), § 13/13a BAföG, diplo.de Sperrkonto
 * (01.09.2024), HRG § 16 / HG NRW § 64(2) Nr. 4 / BayHIG Art. 84, KfW Merkblatt 174.
 *
 * Yöntem: content_md satır satır; her hedef satır, markdown işaretleri çıkarılmış düz metni canlı sayfadaki blokla
 * BİREBİR aynıysa değiştirilir (liste/başlık/alıntı öneki korunur). Ön kontrol tüm kümelerde yazmadan önce yapılır:
 * her eski blok her dilde tam 1 kez bulunmalı; tamamı uygulanmışsa no-op; kısmi/beklenmeyen durumda RuntimeException
 * ve hiçbir dile yazılmaz. Yazma tek transaction. Burs kaydı (BAföG) bu migration'da YOK → ayrı "Batch 1B":
 * DAAD senkronu (daad:scholarships:sync) origins()->sync ile 211 ülkelik listeyi üç ayda bir geri yüklüyor.
 */
return new class extends Migration
{
    public function up(): void
    {
        $spec = json_decode(<<<'JSON'
{
 "news": {
  "slugs": {
   "tr": "2026-germany-blocked-account-sperrkonto-update-how-much-money-do-you",
   "en": "2026-germany-blocked-account-sperrkonto-update-how-much-money-do-you-en",
   "de": "2026-germany-blocked-account-sperrkonto-update-how-much-money-do-you-de"
  },
  "edits": {
   "tr": [
    {
     "old": "2026 yılı itibarıyla Almanya'da bir öğrenci olarak finansal yeterliliğini göstermek için yıllık yaklaşık 11.208 Euro'yu bloke hesabında bulundurman gerekecek. Bu da aylık yaklaşık 934 Euro'ya denk geliyor. Bu miktar, konaklama, yemek, sağlık sigortası, ulaşım ve günlük harcamaların gibi temel ihtiyaçlarını karşılamak üzere belirlenmiş. Yani, vize başvurunda bu parayı hesabında göstermen şart.",
     "new": "2026 yılında Almanya'da öğrenci olarak finansal yeterliliğini göstermek için bloke hesabında yıllık **11.904 Euro** bulundurman gerekiyor; bu da aylık **992 Euro**'ya denk geliyor. Bu tutar 1 Eylül 2024'ten beri geçerli (öncesinde yıllık 11.208 Euro / aylık 934 Euro idi) ve BAföG'ün aylık azami tutarına bağlı. Bu miktar, konaklama, yemek, sağlık sigortası, ulaşım ve günlük harcamaların gibi temel ihtiyaçlarını karşılamak üzere belirlenmiş. Yani, vize başvurunda bu parayı hesabında göstermen şart. Güncel ayrıntılar için [Sperrkonto rehberimize](/tr/blog/sperrkonto-for-a-german-visa-what-is-it-how-much-and) bakabilirsin.",
     "tag": "p"
    }
   ],
   "en": [
    {
     "old": "Starting in 2026, to show you have enough funds as a student in Germany, you'll need to have approximately 11,208 Euros in your blocked account annually. This works out to about 934 Euros per month. This amount is calculated to cover your basic needs like accommodation, food, health insurance, transportation, and daily expenses. Simply put, you must show this money in your account when you apply for your visa.",
     "new": "In 2026, to show you have enough funds as a student in Germany, you need **€11,904** a year in your blocked account, which works out to **€992** a month. This amount has applied since 1 September 2024 (before that it was €11,208 a year / €934 a month) and is linked to the maximum monthly BAföG rate. It is meant to cover your basic needs like accommodation, food, health insurance, transportation, and daily expenses. Simply put, you must show this money in your account when you apply for your visa. For current details, see our [blocked account guide](/en/blog/sperrkonto-for-a-german-visa-what-is-it-how-much-and-en).",
     "tag": "p"
    }
   ],
   "de": [
    {
     "old": "Ab 2026 musst du als Student in Deutschland jährlich etwa 11.208 Euro auf deinem Sperrkonto vorweisen, um deine finanzielle Absicherung zu belegen. Das entspricht ungefähr 934 Euro pro Monat. Dieser Betrag ist dafür gedacht, deine grundlegenden Bedürfnisse wie Unterkunft, Verpflegung, Krankenversicherung, Transport und tägliche Ausgaben zu decken. Kurz gesagt: Du musst dieses Geld auf deinem Konto zeigen, wenn du dein Visum beantragst.",
     "new": "Für 2026 musst du als Studentin oder Student in Deutschland jährlich **11.904 Euro** auf deinem Sperrkonto nachweisen, also **992 Euro** pro Monat. Dieser Betrag gilt seit dem 1. September 2024 (davor 11.208 Euro pro Jahr bzw. 934 Euro pro Monat) und orientiert sich am BAföG-Höchstsatz. Er soll deine Grundbedürfnisse wie Unterkunft, Verpflegung, Krankenversicherung, Transport und tägliche Ausgaben decken. Kurz gesagt: Diesen Betrag musst du bei deinem Visumantrag auf dem Konto nachweisen. Aktuelle Details findest du in unserem [Sperrkonto-Ratgeber](/de/blog/sperrkonto-for-a-german-visa-what-is-it-how-much-and-de).",
     "tag": "p"
    }
   ]
  },
  "meta": {
   "marker": "11[.,]208|(?<![\\d.,])934(?![\\d])",
   "tr": {
    "excerpt": "Almanya'da okumak isteyenler için 2026 bloke hesap (Sperrkonto) tutarı: yıllık 11.904 Euro, aylık 992 Euro (1 Eylül 2024'ten beri geçerli). Sağlayıcılar, gerekli belgeler ve vize başvurusunda dikkat edilecekler.",
    "meta_description": "2026 bloke hesap (Sperrkonto) tutarı: yıllık 11.904 Euro, aylık 992 Euro (1 Eylül 2024'ten beri). Sağlayıcılar, belgeler ve dikkat edilecekler."
   },
   "en": {
    "excerpt": "Planning to study in Germany in 2026? The blocked account (Sperrkonto) amount is €11,904 a year (€992 a month), in force since 1 September 2024. Providers, documents and the mistakes to avoid.",
    "meta_description": "Studying in Germany in 2026? The blocked account (Sperrkonto) amount is €11,904 a year (€992 a month), in force since 1 September 2024."
   },
   "de": {
    "excerpt": "Du planst, 2026 in Deutschland zu studieren? Für das Sperrkonto brauchst du 11.904 Euro im Jahr (992 Euro im Monat), gültig seit dem 1. September 2024. Anbieter, Unterlagen und typische Fehler.",
    "meta_description": "Studium 2026 in Deutschland? Für das Sperrkonto brauchst du 11.904 Euro im Jahr (992 Euro im Monat), gültig seit dem 1. September 2024."
   }
  }
 },
 "bvm": {
  "slugs": {
   "tr": "bachelor-vs-master-in-germany-difficulty-language-work-for-internationals",
   "en": "bachelors-or-masters-germany-difficulty-language-jobs-3-attempt-rule",
   "de": "bachelor-oder-master-deutschland-schwierigkeit-sprache-job-3-versuche-regel"
  },
  "edits": {
   "tr": [
    {
     "old": "‼️ Kritik kural 1: 3-hak (Drittversuch)",
     "new": "‼️ Kritik kural 1: Sınav hakkı sınırını bil (Prüfungsordnung)",
     "tag": "h2"
    },
    {
     "old": "Almanya'da bir sınava genelde 3 hakkın vardır (ilk + 2 tekrar). Aynı dersi üç kez de geçemezsen, o bölümden exmatrikulation (kaydın silinir) ve çoğu durumda Almanya genelinde o bölümü bir daha okuyamazsın — bu da oturum iznini doğrudan riske atar. Yani \"bakalım sınav nasılmış\" diye girilmez; her deneme değerlidir. (Kurallar üniye/eyalete göre değişir, bazı yerlerde Härtefall/istisna olabilir — kendi sınav yönetmeliğini oku.)",
     "new": "Almanya'da sınav hakları için ülke genelinde geçerli tek bir kural yoktur. Kaç hakkın olduğunu eyaletin yükseköğretim yasası ve özellikle programının sınav yönetmeliği (**Prüfungsordnung**) belirler: Bazı programlarda iki, birçoğunda üç, bazılarında dört hak vardır; bazılarında sabit bir sayı yerine süre sınırı uygulanır. Zorunlu bir sınav için izin verilen bütün hakları kullanıp geçemezsen sınav **kesin olarak başarısız** (*endgültig nicht bestanden*) sayılır. Bunun kaydına etkisi, aynı ya da benzer bir bölümü başka bir yerde okuyup okuyamayacağın ve oturum iznine olası etkisi eyalet hukukuna ve üniversitenin kurallarına göre ayrıca değerlendirilir. Kaydın silinmesi (Exmatrikulation) ayrı bir idari işlemdir ve oturum iznini kendiliğinden sona erdirmez; ancak yabancılar dairesi durumunu yeniden değerlendirebilir. Yani \"bakalım sınav nasılmış\" diye girilmez; **her deneme değerlidir.** Sınav yönetmeliğini erkenden oku; ayrıntılar için [not sistemi ve sınav tekrar hakkı rehberimize](/tr/blog/german-university-grading-system-and-exam-retakes) ve [Exmatrikulation rehberimize](/tr/blog/exmatrikulation-germany-causes-residence-permit-and-coming-back) bak.",
     "tag": "p"
    },
    {
     "old": "Öğrenci oturumu, çalışan olarak yılda 120 tam / 240 yarım gün (veya dönem içi ~20 saat/hafta) izin verir; ama serbest meslek/freelancing (selbstständige Tätigkeit) genelde kapsam dışıdır ve ayrı izin gerektirir, nadiren verilir. \"Online freelance işim var\" diye güvenme — vizenin izin verdiğini teyit et (öğrenci çalışma izni).",
     "new": "Öğrenci oturumu, **çalışan** olarak yılda en fazla **140 iş günü** çalışmana izin verir (Arbeitstagekonto, 1 Mart 2024'ten beri): 4 saate kadar çalışılan gün yarım gün sayılır; ders döneminde en fazla 20 saatlik bir hafta 2,5 gün sayılabilir ve senin lehine olan hesap uygulanır. Ama **serbest meslek/freelancing (selbstständige Tätigkeit)** genelde **kapsam dışıdır** ve ayrı izin gerektirir, nadiren verilir. \"Online freelance işim var\" diye güvenme — vizenin izin verdiğini teyit et ([staj ve çalışma kuralları rehberi](/tr/blog/internship-in-germany-with-b1-b2-german)).",
     "tag": "p"
    },
    {
     "old": "\"Ne kadar zor?\" sorusunun cevabı hazırlığına bağlı. Bachelor-Almanca yolu en zoru; Master-İngilizce + iyi Almanca + para tamponu en yönetilebiliri. 3-hak kuralını ve freelancing yasağını baştan bil. İlgili: Alman üniversiteleri zor mu · Werkstudent gerçeği · geliş-sonrası rehber.",
     "new": "\"Ne kadar zor?\" sorusunun cevabı hazırlığına bağlı. Bachelor-Almanca yolu en zoru; Master-İngilizce + iyi Almanca + para tamponu en yönetilebiliri. Sınav yönetmeliğindeki hak sınırlarını ve freelancing yasağını baştan bil. İlgili: [Not sistemi ve sınav tekrar hakkı](/tr/blog/german-university-grading-system-and-exam-retakes) · [Werkstudent gerçeği](/tr/blog/werkstudent-in-germany-the-real-key-to-the-job-market) · [geliş-sonrası rehber](/tr/blog/germany-life-after-arrival-advice-to-past-self).",
     "tag": "p"
    }
   ],
   "en": [
    {
     "old": "Bachelor's or Master's in Germany? Difficulty, Language, Jobs, and the 3-Attempt Exam Rule",
     "new": "Bachelor's or Master's in Germany? Difficulty, Language, Jobs and Exam-Attempt Rules",
     "tag": "h1"
    },
    {
     "old": "How hard is it to study in Germany as a non-EU student, and is a Bachelor's or Master's degree easier? German-taught Bachelor's often feel like \"hunger games\" compared to more relaxed English-taught Master's. Plus, there's the 3-attempt exam rule (which can ban you from a subject nationwide), freelancing restrictions, and the reality of blocked accounts/part-time jobs.",
     "new": "How hard is it to study in Germany as a non-EU student, and is a Bachelor's or Master's degree easier? German-taught Bachelor's often feel like \"hunger games\" compared to more relaxed English-taught Master's. Plus, there are exam-attempt limits set by your examination regulations, freelancing restrictions, and the reality of blocked accounts/part-time jobs.",
     "tag": "blockquote"
    },
    {
     "old": "‼️ Critical Rule 1: The 3-Attempt Rule (Drittversuch)",
     "new": "‼️ Critical Rule 1: Know your exam-attempt limits (Prüfungsordnung)",
     "tag": "h2"
    },
    {
     "old": "In Germany, you generally get 3 attempts for an exam (first attempt + 2 retakes). If you fail the same course three times, you'll be exmatriculated (your enrollment will be canceled), and in most cases, you won't be able to study that specific subject anywhere in Germany again — which directly jeopardizes your residence permit. So, don't just \"see how the exam goes\"; every attempt counts. (Rules can vary by university and state; some places might have Härtefall/exception clauses — always read your specific examination regulations.)",
     "new": "There is no single Germany-wide rule on exam attempts. How many attempts you get is set by state higher-education law and, above all, by your programme's examination regulations (**Prüfungsordnung**): some programmes allow two attempts, many three, some four, and some use time limits instead of a fixed number. If you use up all attempts allowed for a required exam, it is **finally failed** (*endgültig nicht bestanden*). What this means for your enrolment, whether you can study the same or a related programme elsewhere, and any effect on your residence permit depend on your state's law and your university's rules. Exmatriculation is a separate administrative step and does not by itself end your residence permit, although the immigration office may review your situation. So don't just \"see how the exam goes\"; **every attempt counts.** Read your Prüfungsordnung early and see our [grading and exam-retake guide](/en/blog/german-university-grading-system-and-exam-retakes-en) and [Exmatrikulation guide](/en/blog/exmatrikulation-germany-causes-residence-permit-and-coming-back-en).",
     "tag": "p"
    },
    {
     "old": "A student residence permit allows you to work as an employee for 120 full or 240 half days per year (or roughly 20 hours/week during the semester); however, self-employment/freelancing (selbstständige Tätigkeit) is generally not covered and requires separate permission, which is rarely granted. Don't rely on \"I have an online freelance job\" — confirm that your visa allows it (student work permit in Germany).",
     "new": "A student residence permit allows you to work as an **employee** for up to **140 working days per year** (Arbeitstagekonto, since 1 March 2024): a day with up to 4 hours counts as a half day, and during the lecture period a week with up to 20 hours can be counted as 2.5 days, whichever is more favourable for you. However, **self-employment/freelancing (selbstständige Tätigkeit)** is generally **not covered** and requires separate permission, which is rarely granted. Don't rely on \"I have an online freelance job\" — confirm that your visa allows it ([internship and work rules guide](/en/blog/internship-in-germany-with-b1-b2-german-en)).",
     "tag": "p"
    },
    {
     "old": "The answer to \"How hard is it?\" depends on your preparation. The German-taught Bachelor's path is the toughest; an English Master's with good German skills and a financial buffer is the most manageable. Know the 3-attempt rule and the freelancing ban from the start. Related: Are German universities hard · Werkstudent reality · post-arrival guide.",
     "new": "The answer to \"How hard is it?\" depends on your preparation. The German-taught Bachelor's path is the toughest; an English Master's with good German skills and a financial buffer is the most manageable. Know your exam-attempt limits and the freelancing ban from the start. Related: [Grading and exam retakes](/en/blog/german-university-grading-system-and-exam-retakes-en) · [Werkstudent reality](/en/blog/werkstudent-germany-job-market-experience-grades) · [post-arrival guide](/en/blog/10-things-wish-someone-told-me-before-germany-student-life).",
     "tag": "p"
    }
   ],
   "de": [
    {
     "old": "Bachelor oder Master in Deutschland? Schwierigkeit, Sprache, Job und die 3-Versuche-Regel",
     "new": "Bachelor oder Master in Deutschland? Schwierigkeit, Sprache, Job und Prüfungsversuche",
     "tag": "h1"
    },
    {
     "old": "Wie schwer ist es, als Nicht-EU-Studierender in Deutschland zu studieren, und ist ein Bachelor- oder Masterstudium einfacher? Bachelor auf Deutsch fühlen sich oft wie „Hungerspiele“ an, während Master auf Englisch entspannter sind; dazu kommen die 3-Versuche-Regel (die ein bundesweites Studienverbot bedeuten kann), das Freelancing-Verbot und die Realität von Sperrkonto/Nebenjobs.",
     "new": "Wie schwer ist es, als Nicht-EU-Studierender in Deutschland zu studieren, und ist ein Bachelor- oder Masterstudium einfacher? Bachelor auf Deutsch fühlen sich oft wie „Hungerspiele“ an, während Master auf Englisch entspannter sind; dazu kommen die Grenzen für Prüfungsversuche laut deiner Prüfungsordnung, das Freelancing-Verbot und die Realität von Sperrkonto/Nebenjobs.",
     "tag": "blockquote"
    },
    {
     "old": "‼️ Kritische Regel 1: Die 3-Versuche-Regel (Drittversuch)",
     "new": "‼️ Kritische Regel 1: Kenne deine Prüfungsversuche (Prüfungsordnung)",
     "tag": "h2"
    },
    {
     "old": "In Deutschland hast du in der Regel 3 Versuche für eine Prüfung (Erstversuch + 2 Wiederholungen). Wenn du dreimal dieselbe Prüfung nicht bestehst, wirst du exmatrikuliert (deine Einschreibung wird gelöscht) und kannst in den meisten Fällen dieses Fach nirgendwo in Deutschland mehr studieren – was deine Aufenthaltserlaubnis direkt gefährdet. Gehe also nicht nach dem Motto „mal sehen, wie die Prüfung so ist“ hinein; jeder Versuch zählt. (Die Regeln können je nach Universität/Bundesland variieren, an manchen Orten gibt es Härtefall-Regelungen – lies deine eigene Prüfungsordnung.)",
     "new": "Eine bundesweit einheitliche Regel für Prüfungsversuche gibt es nicht. Wie viele Versuche du hast, legen das Hochschulgesetz deines Bundeslandes und vor allem die Prüfungsordnung deines Studiengangs fest: Manche Studiengänge erlauben zwei Versuche, viele drei, einige vier, und manche arbeiten statt einer festen Zahl mit Fristen. Wenn du alle erlaubten Versuche für eine Pflichtprüfung verbraucht hast, ist die Prüfung **endgültig nicht bestanden**. Was das für deine Einschreibung bedeutet, ob du denselben oder einen verwandten Studiengang anderswo studieren kannst und ob es Folgen für deine Aufenthaltserlaubnis hat, hängt vom Landesrecht und von den Regeln deiner Hochschule ab. Die Exmatrikulation ist ein eigener Verwaltungsschritt und beendet deine Aufenthaltserlaubnis nicht automatisch; die Ausländerbehörde kann deine Situation aber prüfen. Gehe also nicht nach dem Motto „mal sehen, wie die Prüfung so ist“ hinein; **jeder Versuch zählt.** Lies deine Prüfungsordnung frühzeitig und schau in unseren [Ratgeber zu Notensystem und Wiederholungsprüfungen](/de/blog/german-university-grading-system-and-exam-retakes-de) und unseren [Exmatrikulations-Ratgeber](/de/blog/exmatrikulation-germany-causes-residence-permit-and-coming-back-de).",
     "tag": "p"
    },
    {
     "old": "Die studentische Aufenthaltserlaubnis erlaubt dir, als Angestellter jährlich 120 volle oder 240 halbe Tage zu arbeiten (oder während des Semesters ca. 20 Stunden/Woche); aber selbstständige Tätigkeit/Freelancing ist in der Regel nicht abgedeckt und erfordert eine gesonderte Erlaubnis, die selten erteilt wird. Verlasse dich nicht auf „Ich habe einen Online-Freelance-Job“ – stelle sicher, dass dein Visum dies erlaubt (Arbeitserlaubnis für Studierende).",
     "new": "Die studentische Aufenthaltserlaubnis erlaubt dir, als **Angestellter** bis zu **140 Arbeitstage im Jahr** zu arbeiten (Arbeitstagekonto, seit 1. März 2024): Ein Tag mit bis zu 4 Stunden zählt als halber Arbeitstag, und in der Vorlesungszeit kann eine Woche mit bis zu 20 Stunden als 2,5 Arbeitstage gezählt werden – es gilt die für dich günstigere Zählweise. Aber **selbstständige Tätigkeit/Freelancing** ist in der Regel **nicht abgedeckt** und erfordert eine gesonderte Erlaubnis, die selten erteilt wird. Verlasse dich nicht auf „Ich habe einen Online-Freelance-Job“ – stelle sicher, dass dein Visum dies erlaubt ([Ratgeber zu Praktikum und Arbeitsregeln](/de/blog/internship-in-germany-with-b1-b2-german-de)).",
     "tag": "p"
    },
    {
     "old": "Die Antwort auf die Frage „Wie schwer ist es?“ hängt von deiner Vorbereitung ab. Der deutschsprachige Bachelor-Weg ist der schwierigste; ein englischer Master mit guten Deutschkenntnissen und einem finanziellen Puffer ist am besten zu bewältigen. Kenne die 3-Versuche-Regel und das Freelancing-Verbot von Anfang an. Verwandt: Sind deutsche Unis schwer · Werkstudent-Realität · Leitfaden nach der Ankunft.",
     "new": "Die Antwort auf die Frage „Wie schwer ist es?“ hängt von deiner Vorbereitung ab. Der deutschsprachige Bachelor-Weg ist der schwierigste; ein englischer Master mit guten Deutschkenntnissen und einem finanziellen Puffer ist am besten zu bewältigen. Kenne die Grenzen für Prüfungsversuche in deiner Prüfungsordnung und das Freelancing-Verbot von Anfang an. Verwandt: [Notensystem und Wiederholungsprüfungen](/de/blog/german-university-grading-system-and-exam-retakes-de) · [Werkstudent-Realität](/de/blog/werkstudent-deutschland-jobmarkt-erfahrung-noten) · [Leitfaden nach der Ankunft](/de/blog/10-dinge-die-ich-gerne-vor-dem-studium-in-deutschland-gewusst-haette).",
     "tag": "p"
    }
   ]
  },
  "fields": {
   "tr": {
    "title": {
     "old": "Almanya'da Bachelor mı Master mı? Zorluk, Dil, İş ve 3-Hak Sınav Kuralı",
     "new": "Almanya'da Bachelor mı Master mı? Zorluk, Dil, İş ve Sınav Hakları"
    },
    "meta_title": {
     "old": "Almanya Bachelor vs Master: Zorluk, Dil, İş + 3-Hak Kuralı",
     "new": "Almanya Bachelor vs Master: Zorluk, Dil, İş ve Sınav Hakları"
    }
   },
   "en": {
    "title": {
     "old": "Bachelor's or Master's in Germany? Difficulty, Language, Jobs, and the 3-Attempt Exam Rule",
     "new": "Bachelor's or Master's in Germany? Difficulty, Language, Jobs and Exam-Attempt Rules"
    },
    "meta_title": {
     "old": "Bachelor's or Master's in Germany? Difficulty, Language, Jobs, and the 3-Attempt Exam Rule",
     "new": "Bachelor's or Master's in Germany? Difficulty, Language, Jobs and Exam-Attempt Rules"
    }
   },
   "de": {
    "title": {
     "old": "Bachelor oder Master in Deutschland? Schwierigkeit, Sprache, Job und die 3-Versuche-Regel",
     "new": "Bachelor oder Master in Deutschland? Schwierigkeit, Sprache, Job und Prüfungsversuche"
    },
    "meta_title": {
     "old": "Bachelor oder Master in Deutschland? Schwierigkeit, Sprache, Job und die 3-Versuche-Regel",
     "new": "Bachelor oder Master in Deutschland? Schwierigkeit, Sprache, Job und Prüfungsversuche"
    }
   }
  },
  "meta": {
   "marker": "3-[Hh]ak|3-h…|3-[Aa]ttempt|3-Versuche|drei Versuche|three attempts",
   "tr": {
    "excerpt": "Almanya'da AB dışı öğrenci olarak okumak ne kadar zor, Bachelor mı Master mı daha kolay? Almanca Bachelor ile İngilizce Master karşılaştırması; sınav yönetmeliğine göre değişen sınav hakları, freelancing sınırı, bloke hesap ve yan iş gerçeği.",
    "meta_description": "Almanya'da AB dışı öğrenci olarak Bachelor mı Master mı daha kolay? Dil, iş, sınav yönetmeliğine göre sınav hakları, freelancing sınırı ve para gerçeği."
   },
   "en": {
    "excerpt": "How hard is it to study in Germany as a non-EU student, and is a Bachelor's or Master's easier? German-taught Bachelor's vs English-taught Master's, exam-attempt limits set by your examination regulations, freelancing restrictions and money reality.",
    "meta_description": "Bachelor's or Master's in Germany as a non-EU student? Language, jobs, exam-attempt limits set by your Prüfungsordnung, freelancing rules and money."
   },
   "de": {
    "excerpt": "Wie schwer ist ein Studium in Deutschland für Nicht-EU-Studierende, und ist Bachelor oder Master leichter? Deutscher Bachelor vs. englischer Master, Prüfungsversuche laut Prüfungsordnung, Freelancing-Grenzen und Geld.",
    "meta_description": "Bachelor oder Master in Deutschland für Nicht-EU-Studierende? Sprache, Jobs, Prüfungsversuche laut Prüfungsordnung, Freelancing-Regeln und Geld."
   }
  }
 },
 "dam": {
  "slugs": {
   "tr": "doing-a-masters-in-germany-2026-a-z-guide",
   "en": "doing-a-masters-in-germany-2026-a-z-guide-en",
   "de": "doing-a-masters-in-germany-2026-a-z-guide-de"
  },
  "edits": {
   "tr": [
    {
     "old": "Çalışma izni: Master sırasında 120 tam / 240 yarım gün çalışabilirsin",
     "new": "**Çalışma izni:** Master sırasında yılda 140 iş gününe kadar çalışabilirsin (Arbeitstagekonto, 1 Mart 2024'ten beri)",
     "tag": "li"
    },
    {
     "old": "Master sırasında 120 tam / 240 yarım gün çalışma hakkı. Saatlik 13–18 €. Aylık 20-40 saat = 260–700 €, yaşam giderlerinin %30-50'sini karşılar.",
     "new": "Master sırasında yılda **140 iş gününe** kadar çalışma hakkı (4 saate kadar çalışılan gün yarım gün sayılır; ders döneminde en fazla 20 saatlik bir hafta 2,5 gün sayılabilir). Saatlik 13–18 €. Aylık 20-40 saat = 260–700 €, yaşam giderlerinin %30-50'sini karşılar.",
     "tag": "p"
    },
    {
     "old": "AB dışı öğrenciler için kısıtlı. Master'a 2 sömestre içinde başlamış olman + Almanya'da kayıtlı olman gerekiyor. Aylık 100–650 € kredi.",
     "new": "Uygunluk, KfW'nin tanımladığı statü gruplarına bağlıdır (örneğin Alman vatandaşları, en az üç yıldır Almanya'da yaşayan AB vatandaşları, aile üyeleri ve Bildungsinländer). **Yalnızca olağan § 16b öğrenci oturum izni, listede ayrı bir uygun kategori olarak sayılmaz.** Aylık 100–650 €, değişken faiz. Ayrıntılar ve alternatifler: [öğrenci kredisi ve eğitim finansmanı rehberi](/tr/blog/student-loans-and-study-financing-in-germany).",
     "tag": "p"
    },
    {
     "old": "Evet, yılda 120 tam gün (240 yarım gün) çalışma hakkın var. Werkstudent pozisyonları (saatlik 13-18 €) en yaygın. Stajlar (Praktikum) bu kotaya dahil değildir.",
     "new": "Evet. 1 Mart 2024'ten beri yılda **140 iş gününe** kadar çalışabilirsin (Arbeitstagekonto): 4 saate kadar çalışılan gün yarım gün sayılır; ders döneminde en fazla 20 saatlik bir hafta 2,5 gün sayılabilir. Werkstudent pozisyonları (saatlik 13-18 €) en yaygın. Programının parçası olan zorunlu staj (Pflichtpraktikum) bu hesaba sayılmaz; gönüllü staj genellikle sayılır. Ayrıntılar: [staj ve çalışma kuralları rehberi](/tr/blog/internship-in-germany-with-b1-b2-german).",
     "tag": "p"
    }
   ],
   "en": [
    {
     "old": "Work permit: You can work 120 full / 240 half days during your Master's",
     "new": "**Work permit:** During your Master's you can work up to 140 working days a year (Arbeitstagekonto, since 1 March 2024)",
     "tag": "li"
    },
    {
     "old": "Right to work 120 full / 240 half days during your Master's. Hourly 13–18 €. 20-40 hours/month = 260–700 €, covers 30-50% of living expenses.",
     "new": "Right to work up to **140 working days a year** during your Master's (a day with up to 4 hours counts as a half day; during the lecture period a week with up to 20 hours can be counted as 2.5 days). Hourly 13–18 €. 20-40 hours/month = 260–700 €, covers 30-50% of living expenses.",
     "tag": "p"
    },
    {
     "old": "Limited for non-EU students. You need to have started your Master's within 2 semesters + be registered in Germany. Monthly 100–650 € loan.",
     "new": "Eligibility follows KfW's defined status categories (for example German citizens, EU citizens with at least three years' residence in Germany, family members and Bildungsinländer). **An ordinary § 16b student residence permit on its own is not listed as a separate eligible category.** Monthly 100–650 €, variable interest. Details and alternatives: [student loans and study financing guide](/en/blog/student-loans-and-study-financing-in-germany-en).",
     "tag": "p"
    },
    {
     "old": "Yes, you have the right to work 120 full days (240 half days) per year. Werkstudent positions (13-18 €/hour) are the most common. Internships (Praktikum) are not included in this quota.",
     "new": "Yes. Since 1 March 2024 you can work up to **140 working days a year** (Arbeitstagekonto): a day with up to 4 hours counts as a half day, and during the lecture period a week with up to 20 hours can be counted as 2.5 days. Werkstudent positions (13-18 €/hour) are the most common. A mandatory internship (Pflichtpraktikum) that is part of your programme does not count toward this account; a voluntary internship generally does. Details: [internship and work rules guide](/en/blog/internship-in-germany-with-b1-b2-german-en).",
     "tag": "p"
    }
   ],
   "de": [
    {
     "old": "Arbeitserlaubnis: Während des Masters kannst du 120 volle / 240 halbe Tage arbeiten",
     "new": "**Arbeitserlaubnis:** Während des Masters kannst du bis zu 140 Arbeitstage im Jahr arbeiten (Arbeitstagekonto, seit 1. März 2024)",
     "tag": "li"
    },
    {
     "old": "Während des Masters hast du das Recht, 120 volle / 240 halbe Tage zu arbeiten. Stundenlohn 13–18 €. 20-40 Stunden pro Monat = 260–700 €, deckt 30-50 % der Lebenshaltungskosten.",
     "new": "Während des Masters hast du das Recht, bis zu **140 Arbeitstage im Jahr** zu arbeiten (ein Tag mit bis zu 4 Stunden zählt als halber Arbeitstag; in der Vorlesungszeit kann eine Woche mit bis zu 20 Stunden als 2,5 Arbeitstage gezählt werden). Stundenlohn 13–18 €. 20-40 Stunden pro Monat = 260–700 €, deckt 30-50 % der Lebenshaltungskosten.",
     "tag": "p"
    },
    {
     "old": "Für Nicht-EU-Studierende eingeschränkt. Du musst innerhalb von 2 Semestern mit dem Master begonnen haben und in Deutschland eingeschrieben sein. Monatlicher Kredit von 100–650 €.",
     "new": "Die Förderfähigkeit richtet sich nach den von der KfW festgelegten Statusgruppen (zum Beispiel deutsche Staatsangehörige, EU-Bürger mit mindestens drei Jahren Aufenthalt in Deutschland, Familienangehörige und Bildungsinländer). **Eine gewöhnliche Aufenthaltserlaubnis nach § 16b allein wird nicht als eigene förderfähige Gruppe genannt.** 100–650 € pro Monat, variabler Zins. Details und Alternativen: [Ratgeber zu Studienkredit und Studienfinanzierung](/de/blog/student-loans-and-study-financing-in-germany-de).",
     "tag": "p"
    },
    {
     "old": "Ja, du hast das Recht, jährlich 120 volle Tage (240 halbe Tage) zu arbeiten. Werkstudentenpositionen (13-18 € pro Stunde) sind am häufigsten. Praktika sind in diesem Kontingent nicht enthalten.",
     "new": "Ja. Seit dem 1. März 2024 darfst du bis zu **140 Arbeitstage im Jahr** arbeiten (Arbeitstagekonto): Ein Tag mit bis zu 4 Stunden zählt als halber Arbeitstag, und in der Vorlesungszeit kann eine Woche mit bis zu 20 Stunden als 2,5 Arbeitstage gezählt werden. Werkstudentenpositionen (13-18 € pro Stunde) sind am häufigsten. Ein Pflichtpraktikum als Teil deines Studiums wird nicht auf dieses Konto angerechnet; ein freiwilliges Praktikum in der Regel schon. Details: [Ratgeber zu Praktikum und Arbeitsregeln](/de/blog/internship-in-germany-with-b1-b2-german-de).",
     "tag": "p"
    }
   ]
  }
 },
 "baf": {
  "slugs": {
   "tr": "what-is-bafog-eligibility-and-application-for-international-students",
   "en": "what-is-bafog-eligibility-and-application-for-international-students-en",
   "de": "what-is-bafog-eligibility-and-application-for-international-students-de"
  },
  "edits": {
   "tr": [
    {
     "old": "Tutar 2024+ itibarıyla aylık ~934 €'ya kadar (gelir/duruma göre değişir).",
     "new": "Aylık azami tutar **992 €** (2024/25 kış döneminden beri; gelir ve duruma göre değişir).",
     "tag": "li"
    },
    {
     "old": "Studienkredit (KfW vb. öğrenci kredileri),",
     "new": "**Öğrenci kredisi** — ancak dikkat: KfW Studienkredit'in uygunluk listesinde yalnızca olağan § 16b öğrenci oturum izni ayrı bir uygun kategori olarak sayılmaz (ayrıntılar: [öğrenci kredisi rehberi](/tr/blog/student-loans-and-study-financing-in-germany)),",
     "tag": "li"
    }
   ],
   "en": [
    {
     "old": "Up to about €934/month as of 2024+ (varies by income/situation).",
     "new": "Up to **€992/month** (maximum rate since winter semester 2024/25; varies by income/situation).",
     "tag": "li"
    },
    {
     "old": "A student loan (e.g. KfW),",
     "new": "A **student loan** — note that for the KfW Studienkredit an ordinary § 16b student residence permit on its own is not listed as a separate eligible category (details: [student loan guide](/en/blog/student-loans-and-study-financing-in-germany-en)),",
     "tag": "li"
    }
   ],
   "de": [
    {
     "old": "Bis zu rund 934 €/Monat seit 2024+ (je nach Einkommen/Situation).",
     "new": "Bis zu **992 €/Monat** (Höchstsatz seit dem Wintersemester 2024/25; je nach Einkommen/Situation).",
     "tag": "li"
    },
    {
     "old": "ein Studienkredit (z. B. KfW),",
     "new": "ein **Studienkredit** – beachte: Beim KfW-Studienkredit wird eine gewöhnliche Aufenthaltserlaubnis nach § 16b allein nicht als eigene förderfähige Gruppe genannt (Details: [Studienkredit-Ratgeber](/de/blog/student-loans-and-study-financing-in-germany-de)),",
     "tag": "li"
    }
   ]
  }
 }
}
JSON, true, 512, JSON_THROW_ON_ERROR);

        $plans = [];
        $problems = [];
        foreach ($spec as $cluster => $c) {
            $posts = [];
            foreach ($c['slugs'] as $locale => $slug) {
                $post = Post::where('slug', $slug)->where('locale', $locale)->first();
                if (! $post) {
                    $problems[] = "{$cluster}/{$locale}: kayıt yok ({$slug})";
                    continue;
                }
                $posts[$locale] = $post;
            }
            if (count($posts) !== count($c['slugs'])) {
                continue;
            }
            $groups = array_values(array_unique(array_map(fn ($p) => (string) $p->translation_group_id, $posts)));
            if (count($groups) !== 1 || $groups[0] === '') {
                $problems[] = "{$cluster}: çeviri grubu tutarsız";
                continue;
            }

            $pending = 0;
            $applied = 0;
            $changes = [];
            foreach ($posts as $locale => $post) {
                $lines = explode("\n", (string) $post->content_md);
                $norms = array_map(fn ($l) => $this->norm($l), $lines);
                foreach ($c['edits'][$locale] as $e) {
                    $oldHits = array_keys($norms, $this->norm($e['old']), true);
                    $newHits = array_keys($norms, $this->norm($e['new']), true);
                    if (count($oldHits) === 1 && count($newHits) === 0) {
                        $pending++;
                        $i = $oldHits[0];
                        $cr = str_ends_with($lines[$i], "\r") ? "\r" : '';
                        preg_match('/^\s{0,3}(?:#{1,6}\s+|(?:>\s?)+|[-*+]\s+|\d+[.)]\s+)?/u', $lines[$i], $m);
                        $lines[$i] = $m[0].$e['new'].$cr;
                    } elseif (count($oldHits) === 0 && count($newHits) === 1) {
                        $applied++;
                    } else {
                        $problems[] = "{$cluster}/{$locale}: blok eşleşmedi (eski: ".count($oldHits).', yeni: '.count($newHits).') «'.mb_substr($e['old'], 0, 60).'…»';
                    }
                }
                $meta = [];
                // title / meta_title: eski değer BİREBİR doğrulanır (eski → yeni); başka bir değer = beklenmeyen durum.
                foreach ($c['fields'][$locale] ?? [] as $f => $v) {
                    $cur = (string) $post->{$f};
                    if ($cur === $v['new']) {
                        $applied++;
                    } elseif ($cur === $v['old']) {
                        $pending++;
                        $meta[$f] = $v['new'];
                    } else {
                        $problems[] = "{$cluster}/{$locale}: {$f} beklenen eski değerde değil «".mb_substr($cur, 0, 60).'»';
                    }
                }
                foreach (['excerpt', 'meta_description'] as $f) {
                    $want = $c['meta'][$locale][$f] ?? null;
                    if ($want === null) {
                        continue;
                    }
                    $cur = (string) $post->{$f};
                    if ($cur === $want) {
                        $applied++;
                    } elseif (preg_match('/'.$c['meta']['marker'].'/u', $cur)) {
                        $pending++;
                        $meta[$f] = $want;
                    }
                }
                $changes[$locale] = [$post, implode("\n", $lines), $meta];
            }
            if ($pending > 0 && $applied > 0) {
                $problems[] = "{$cluster}: kısmen uygulanmış tutarsız durum ({$applied} uygulanmış, {$pending} bekleyen)";
            }
            if ($pending > 0) {
                $plans[$cluster] = $changes;
            }
        }

        if ($problems) {
            if (app()->runningUnitTests()) {
                return; // Test DB'si sıfırdan kurulur; bu yazıların çoğu orada yok (prod'da sonradan oluşturuldu). Hiçbir şey yazma.
            }
            throw new RuntimeException('Content Truth Batch 1: ön kontrol başarısız, hiçbir kayda yazılmadı. '.implode(' | ', $problems));
        }
        if (! $plans) {
            return; // zaten uygulanmış — no-op
        }

        DB::transaction(function () use ($plans) {
            foreach ($plans as $changes) {
                foreach ($changes as [$post, $md, $meta]) {
                    $post->content_md = $md; // Post::booted() content_html'i yeniden üretir
                    foreach ($meta as $f => $v) {
                        $post->{$f} = $v;
                    }
                    $post->save();
                }
            }
        });
    }

    /** Markdown satırının görünen düz metni (canlı sayfadaki blok metniyle karşılaştırmak için). */
    private function norm(string $s): string
    {
        $s = preg_replace('/^\s{0,3}(?:#{1,6}\s+|(?:>\s?)+|[-*+]\s+|\d+[.)]\s+)?/u', '', $s);
        $s = preg_replace('/!\[([^\]]*)\]\([^)]*\)/u', '$1', $s);
        for ($i = 0; $i < 3; $i++) {
            $s = preg_replace('/\[([^\]]*)\]\([^)]*\)/u', '$1', $s);
        }
        $s = preg_replace('/<[^>]+>/u', '', $s);
        $s = str_replace(['**', '__', '*', '`'], '', $s);
        $s = preg_replace('/\\\\([\\\\`*_{}\[\]()#+\-.!>|"\'])/u', '$1', $s);
        $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/u', ' ', $s));
    }

    public function down(): void
    {
        // Bilinçli olarak boş: forward-only içerik düzeltmesi.
    }
};
