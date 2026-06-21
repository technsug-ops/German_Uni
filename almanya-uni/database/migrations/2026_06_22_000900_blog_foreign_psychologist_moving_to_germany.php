<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Yurt dışında psikolog/psikoterapist olarak Almanya'ya taşınmak (2026).
 *
 * Doğrulandı (Approbation = sınırsız lisans; Landesprüfungsamt/Landesgesundheitsamt denklik;
 * "Psychotherapeut" yasal korumalı unvan; AB/AEA daha kolay denklik; üçüncü-ülke
 * Gleichwertigkeitsprüfung → uymazsa Eignungsprüfung/Anpassungslehrgang; Heilpraktiker für
 * Psychotherapie sınırlı alternatif rota; non-klinik koçluk/danışmanlık serbest;
 * İngilizce terapi pazarı büyük şehirlerde yüksek talep, çoğunlukla private-pay; Kassensitz
 * darboğazı; Mavi Kart somut iş teklifi ister, serbest meslek = §21 AufenthG):
 *  - Hasta tedavisi (psikoterapi) için Approbation şart; eyalet makamı denkliği yürütür.
 *  - "Psychotherapeut" korumalı unvan — denkliksiz kullanılamaz.
 *  - Üçüncü-ülke: eşdeğerlik kontrolü, uymazsa yeterlilik sınavı/uyum dönemi.
 *  - Heilpraktiker für Psychotherapie: terapi sunulur ama "Psychotherapeut" denemez, GKV genelde ödemez.
 *  - Koçluk/danışmanlık serbest (korumalı klinik unvan yine kullanılamaz).
 *  - İngilizce terapiye büyük şehirlerde yüksek talep; çoğunlukla özel-ödeme (GKV nadiren karşılar).
 *  - Kassensitz darboğazı: kendi muayenehanende GKV faturalamak için (kıt) Kassensitz gerekir.
 *  - Mavi Kart somut iş teklifi ister (serbest/freelance ile alınmaz); serbest meslek = §21 AufenthG.
 *
 * Yazar: Halil Yaprakli. Kategori: visa-residence → vize → ilk. FK-safe + slug-bazlı idempotent.
 * İç-link: foreign-doctors-approbation + work-visa-with-job-offer + zweckwechsel + anabin
 * (her dörtü de tr/de/en mevcut) → her dilde locale-doğru.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = '7c1e9a20-5b3d-4e8a-9f12-1a2b3c4d5e78';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'visa-residence')->value('id')
            ?? DB::table('categories')->where('slug', 'vize')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da terapiste — özellikle **İngilizce konuşan terapiste** — büyük bir ihtiyaç var. Berlin, Münih, Frankfurt'taki yüz binlerce yabancı, kendi dilinde konuşabileceği bir terapist bulmakta zorlanıyor. Ama bir tuzak var: **"Psychotherapeut" Almanya'da yasal korumalı bir unvandır** ve denklik olmadan kullanamazsın. Yurt dışında psikolog/psikoterapistsen ve Almanya'ya taşınmak istiyorsan, bilmen gerekenler.

## Approbation ve denklik
Hasta tedavi etmek (psikoterapi yapmak) için **Approbation** gerekir — bu sınırsız (kısıtsız) bir meslek lisansıdır. Denkliği eyalet makamı yürütür: **Landesprüfungsamt / Landesgesundheitsamt**.

- **AB/AEA/İsviçre'de eğitim aldıysan:** süreç daha pürüzsüz — esas olarak **eşdeğerlik + dil** kanıtı.
- **Üçüncü ülke (Türkiye, ABD, Meksika, Orta Doğu vb.):** önce bir **Gleichwertigkeitsprüfung** (eşdeğerlik kontrolü). Eğitimin eşdeğer değilse ve farklar telafi edilemiyorsa → ya bir **Eignungsprüfung** (yeterlilik sınavı, sözlü + uygulamalı) ya da bir **Anpassungslehrgang** (uyum dönemi).

(Hekimlerdeki Approbation süreciyle paralel; bkz: [Almanya'da yabancı doktor / Approbation](/tr/blog/foreign-doctors-germany-approbation-fsp-kenntnispruefung).)

## Korumalı unvan + alternatif rotalar
Denkliğin yoksa kendine **"Psychotherapeut" diyemezsin** — bu suç sayılır. İki meşru alternatif var:

- **Heilpraktiker für Psychotherapie:** sınırlı bir rota. Psikoterapi *sunabilirsin*, ama kendine **"Psychotherapeut" diyemezsin** ve yasal sağlık sigortası (GKV) genelde *seni karşılamaz* (hasta cebinden öder).
- **Klinik-dışı koçluk / danışmanlık:** **tamamen serbest, denklik gerekmez.** Kurumsal koçluk, EAP, yaşam koçluğu yapabilirsin — yeter ki korumalı klinik unvanları kullanmayasın.

## İngilizce terapi pazarı — gerçek fırsat
İşin parlayan tarafı bu: büyük şehirlerde **İngilizce konuşan terapiste yüksek talep** var ve birçok yabancı bir türlü bulamıyor. Çoğunlukla **özel ödeme (private-pay)** ile çalışır, çünkü yasal sigorta İngilizce terapiyi nadiren karşılar. Yani Approbation + özel-ödeme ile, GKV'ye hiç girmeden iyi bir pratik kurabilirsin.

Yine de **Kassensitz** darboğazı: kendi muayenehanende yasal sigortaya faturalamak için (kıt ve pahalı) bir **Kassensitz** gerekir. Bir klinikte ya da başka bir muayenehanede **çalışan** olursan bu sorunu atlarsın.

## Hedefe göre ne gerekir?
| Hedef | Gereken |
|---|---|
| Terapi yap + sigortaya faturala | Approbation + Fachkunde + **Kassensitz** |
| Terapi yap (özel/İngilizce) | Approbation + **özel ödeme** |
| Koçluk / kurumsal | Resmî bir şey yok — ama **korumalı unvan kullanma** |
| Vize | Mavi Kart **iş teklifi ister**; serbest meslek = **§21 AufenthG** |

## Vize: Mavi Kart bir iş ister
**AB vatandaşıysan vize gerekmez.** Değilsen:

- **AB Mavi Kart somut bir iş teklifi ister** — serbest/freelance olarak alamazsın. Yani bir klinik/muayenehane seni işe alacaksa Mavi Kart mantıklı.
- **Kendi muayenehaneni / serbest çalışmayı** hedefliyorsan, bu farklı bir izindir: **serbest/bağımsız faaliyet (§21 AufenthG)**.

(İş teklifiyle çalışma vizesi: [süreç ve süre](/tr/blog/germany-work-visa-with-job-offer-process-timeline-fast-track). Zaten öğrenciysen Mavi Kart'a geçiş: [Zweckwechsel](/tr/blog/changing-student-visa-to-work-permit-germany-zweckwechsel). Diploma denkliği için: [Anabin](/tr/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma).)

## Sonuç
Almanya'nın sana ihtiyacı var — ama kapı **unvanla** kilitli. Hasta tedavi etmek istiyorsan Approbation şart; üçüncü ülke eğitimliysen eşdeğerlik/yeterlilik sınavına hazırlan. En hızlı pratik yol genelde: **bir klinikte işe gir** (Mavi Kart + Kassensitz derdi yok) ve İngilizce konuşan, özel-ödemeli danışan kitlesini hedefle. Koçluk/kurumsal tarafa açıksan, denkliksiz de hemen başlayabilirsin.

İlgili: [Yabancı doktor / Approbation](/tr/blog/foreign-doctors-germany-approbation-fsp-kenntnispruefung) · [İş teklifiyle çalışma vizesi](/tr/blog/germany-work-visa-with-job-offer-process-timeline-fast-track) · [Anabin & denklik](/tr/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma).

---
*2026 itibarıyla yürürlükteki kurallar temel alınmıştır; denklik, unvan ve Kassensitz şartları eyalete ve duruma göre değişir — başvurudan önce ilgili Landesprüfungsamt / Landesgesundheitsamt'tan teyit et.*
MD;

        $deBody = <<<'MD'
Deutschland braucht dringend Therapeut*innen — besonders **englischsprachige**. Hunderttausende Expats in Berlin, München und Frankfurt finden niemanden, der ihre Sprache spricht. Aber es gibt eine Hürde: **„Psychotherapeut" ist in Deutschland ein gesetzlich geschützter Titel** und ohne Anerkennung darfst du ihn nicht führen. Wenn du im Ausland Psychologe/Psychotherapeut bist und nach Deutschland ziehen willst, das musst du wissen.

## Approbation und Anerkennung
Um Patient*innen zu behandeln (Psychotherapie auszuüben), brauchst du die **Approbation** — eine uneingeschränkte Berufserlaubnis. Die Anerkennung läuft über die Landesbehörde: **Landesprüfungsamt / Landesgesundheitsamt**.

- **Ausbildung in EU/EWR/Schweiz:** reibungsloser — im Kern **Gleichwertigkeit + Sprache**.
- **Drittstaat (Türkei, USA, Mexiko, Naher Osten usw.):** zuerst eine **Gleichwertigkeitsprüfung**. Ist die Ausbildung nicht gleichwertig und lassen sich die Unterschiede nicht ausgleichen → entweder eine **Eignungsprüfung** (mündlich + praktisch) oder ein **Anpassungslehrgang** (Anpassungszeitraum).

(Parallel zur Approbation bei Ärzt*innen; siehe: [Ausländische Ärzte / Approbation](/de/blog/foreign-doctors-germany-approbation-fsp-kenntnispruefung-de).)

## Geschützter Titel + Alternativen
Ohne Anerkennung darfst du dich **nicht „Psychotherapeut" nennen** — das ist strafbar. Zwei legitime Alternativen:

- **Heilpraktiker für Psychotherapie:** ein eingeschränkter Weg. Du *darfst* Psychotherapie anbieten, dich aber **nicht „Psychotherapeut" nennen**, und die gesetzliche Krankenkasse (GKV) übernimmt das in der Regel *nicht* (Selbstzahler).
- **Nicht-klinisches Coaching / Counselling:** **völlig unreguliert, keine Anerkennung nötig.** Corporate Coaching, EAP, Life-Coaching sind möglich — solange du keine geschützten klinischen Titel verwendest.

## Der englischsprachige Markt — die echte Chance
Hier liegt das Potenzial: In den Großstädten gibt es **hohe Nachfrage nach englischsprachigen Therapeut*innen**, und viele Expats finden einfach keine. Meist als **Selbstzahler (private-pay)**, weil die gesetzliche Kasse englischsprachige Therapie selten übernimmt. Mit Approbation + Selbstzahler kannst du also eine gute Praxis aufbauen, ganz ohne GKV.

Trotzdem der **Kassensitz**-Engpass: Um in der eigenen Praxis mit der gesetzlichen Kasse abzurechnen, brauchst du einen (knappen, teuren) **Kassensitz**. Als **Angestellte*r** in einer Klinik oder einer anderen Praxis umgehst du das.

## Was brauchst du wofür?
| Ziel | Erforderlich |
|---|---|
| Therapie + Kassenabrechnung | Approbation + Fachkunde + **Kassensitz** |
| Therapie (privat/englisch) | Approbation + **Selbstzahler** |
| Coaching / Corporate | Nichts Formales — aber **kein geschützter Titel** |
| Visum | Blaue Karte braucht **Jobangebot**; Selbstständigkeit = **§21 AufenthG** |

## Visum: Die Blaue Karte braucht einen Job
**EU-Bürger brauchen kein Visum.** Sonst:

- **Die EU Blaue Karte verlangt ein konkretes Jobangebot** — als Selbstständige*r/Freelancer bekommst du sie nicht. Wenn dich also eine Klinik/Praxis anstellt, ist die Blaue Karte sinnvoll.
- Zielst du auf die **eigene Praxis / selbstständige Tätigkeit**, ist das ein anderer Aufenthaltstitel: **freiberufliche/selbständige Tätigkeit (§21 AufenthG)**.

(Arbeitsvisum mit Jobangebot: [Ablauf und Dauer](/de/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-de). Schon Student? Wechsel zur Blauen Karte: [Zweckwechsel](/de/blog/changing-student-visa-to-work-permit-germany-zweckwechsel-de). Zur Anerkennung: [Anabin](/de/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma-de).)

## Fazit
Deutschland braucht dich — aber die Tür ist über den **Titel** verriegelt. Willst du Patient*innen behandeln, ist die Approbation Pflicht; bei einer Drittstaat-Ausbildung bereite dich auf Gleichwertigkeit/Eignungsprüfung vor. Der schnellste praktische Weg ist oft: **Anstellung in einer Klinik** (kein Blaue-Karte-/Kassensitz-Problem) plus eine englischsprachige Selbstzahler-Klientel. Für Coaching/Corporate kannst du sogar ohne Anerkennung sofort starten.

Verwandt: [Ausländische Ärzte / Approbation](/de/blog/foreign-doctors-germany-approbation-fsp-kenntnispruefung-de) · [Arbeitsvisum mit Jobangebot](/de/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-de) · [Anabin & Anerkennung](/de/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma-de).

---
*Stand 2026; Anerkennung, Titel und Kassensitz variieren je nach Bundesland und Einzelfall — vor der Bewerbung beim zuständigen Landesprüfungsamt / Landesgesundheitsamt bestätigen.*
MD;

        $enBody = <<<'MD'
Germany badly needs therapists — especially **English-speaking** ones. Hundreds of thousands of expats in Berlin, Munich and Frankfurt can't find anyone who speaks their language. But there's a catch: **"Psychotherapeut" is a legally protected title in Germany**, and you may not use it without recognition. If you're a psychologist/psychotherapist abroad and want to move to Germany, here's what you need to know.

## Approbation and recognition
To treat patients (to practise psychotherapy) you need the **Approbation** — an unrestricted licence to practise. Recognition is handled by the state authority: the **Landesprüfungsamt / Landesgesundheitsamt**.

- **Trained in the EU/EEA/Switzerland:** smoother — essentially **equivalence + language**.
- **Third country (Turkey, USA, Mexico, the Middle East, etc.):** first a **Gleichwertigkeitsprüfung** (equivalence check). If your training isn't equivalent and the gaps can't be compensated → either an **Eignungsprüfung** (aptitude test, oral + practical) or an **Anpassungslehrgang** (adaptation period).

(This parallels the doctors' Approbation route; see: [Foreign doctors / Approbation](/en/blog/foreign-doctors-germany-approbation-fsp-kenntnispruefung-en).)

## Protected title + the alternatives
Without recognition you **may not call yourself "Psychotherapeut"** — that's an offence. Two legitimate alternatives:

- **Heilpraktiker für Psychotherapie:** a limited route. You *may* offer psychotherapy, but you may **NOT** call yourself "Psychotherapeut", and statutory health insurance (GKV) usually *won't* cover you (patients pay out of pocket).
- **Non-clinical coaching / counselling:** **completely unregulated, no recognition needed.** Corporate coaching, EAP and life-coaching are all open — as long as you don't use the protected clinical titles.

## The English-speaking market — the real opportunity
This is where it shines: big cities have **high demand for English-speaking therapists**, and many expats simply can't find one. It's mostly **private-pay**, because statutory insurance rarely covers English-language therapy. So with Approbation + private-pay you can build a solid practice without ever touching the GKV.

Still, the **Kassensitz** bottleneck: to bill statutory insurance from your own practice you need a (scarce, expensive) **Kassensitz**. Working as an **employee** in a clinic or another practice avoids this entirely.

## What you need, by goal
| Goal | What you need |
|---|---|
| Do therapy + bill insurance | Approbation + Fachkunde + **Kassensitz** |
| Do therapy (private/English) | Approbation + **private-pay** |
| Coaching / corporate | Nothing formal — but **no protected title** |
| Visa | Blue Card needs a **job offer**; self-employment = **§21 AufenthG** |

## Visa: the Blue Card needs a job
**EU citizens need no visa.** Otherwise:

- **The EU Blue Card requires a concrete job offer** — you can't get it as a self-employed person/freelancer. So if a clinic/practice is hiring you, the Blue Card makes sense.
- If you're aiming for your **own practice / self-employment**, that's a different permit: **freelance/self-employed activity (§21 AufenthG)**.

(Work visa with a job offer: [process and timeline](/en/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-en). Already a student? Switching to the Blue Card: [Zweckwechsel](/en/blog/changing-student-visa-to-work-permit-germany-zweckwechsel-en). For recognition: [Anabin](/en/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma-en).)

## Bottom line
Germany needs you — but the door is locked by the **title**. If you want to treat patients, the Approbation is non-negotiable; if you trained in a third country, prepare for the equivalence/aptitude path. The fastest practical route is often: **get employed by a clinic** (no Blue Card or Kassensitz headache) and target an English-speaking, private-pay clientele. If you're open to coaching/corporate work, you can even start right away with no recognition at all.

Related: [Foreign doctors / Approbation](/en/blog/foreign-doctors-germany-approbation-fsp-kenntnispruefung-en) · [Work visa with a job offer](/en/blog/germany-work-visa-with-job-offer-process-timeline-fast-track-en) · [Anabin & recognition](/en/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma-en).

---
*Based on rules in force as of 2026; recognition, title and Kassensitz requirements vary by federal state and individual case — confirm with the relevant Landesprüfungsamt / Landesgesundheitsamt before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'foreign-psychologist-psychotherapist-moving-to-germany-recognition',
                'title' => 'Yabancı Psikolog/Psikoterapist Olarak Almanya\'ya Taşınmak (2026): Denklik, Unvan ve İngilizce Terapi Pazarı',
                'excerpt' => 'Yurt dışında psikolog/psikoterapistsen ve Almanya\'ya taşınmak istiyorsan: hasta tedavisi için Approbation (Landesprüfungsamt denkliği), AB vs üçüncü-ülke (eşdeğerlik → Eignungsprüfung), "Psychotherapeut" korumalı unvanı, Heilpraktiker & koçluk alternatifleri, yüksek talepli İngilizce özel-ödeme pazarı, Kassensitz darboğazı ve vize (Mavi Kart iş ister, serbest meslek §21).',
                'meta_title' => 'Yabancı Psikolog Olarak Almanya\'ya Taşınmak — Denklik & Unvan (2026)',
                'meta_description' => 'Almanya\'da psikolog/psikoterapist: Approbation denkliği, AB/üçüncü-ülke, korumalı unvan, Heilpraktiker & koçluk, İngilizce özel-ödeme pazarı, Kassensitz ve vize (Mavi Kart/§21) — 2026 rehberi.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'foreign-psychologist-psychotherapist-moving-to-germany-recognition-de',
                'title' => 'Als ausländische*r Psycholog*in/Psychotherapeut*in nach Deutschland (2026): Anerkennung, Titel & der englischsprachige Markt',
                'excerpt' => 'Du bist im Ausland Psychologe/Psychotherapeut und willst nach Deutschland: Für die Patientenbehandlung brauchst du die Approbation (Anerkennung über das Landesprüfungsamt), EU vs Drittstaat (Gleichwertigkeit → Eignungsprüfung), der geschützte Titel „Psychotherapeut", die Alternativen Heilpraktiker & Coaching, der gefragte englischsprachige Selbstzahler-Markt, der Kassensitz-Engpass und das Visum (Blaue Karte braucht Job, Selbstständigkeit §21).',
                'meta_title' => 'Als ausländischer Psychologe nach Deutschland — Anerkennung & Titel (2026)',
                'meta_description' => 'Psychologe/Psychotherapeut in Deutschland: Approbation, EU/Drittstaat, geschützter Titel, Heilpraktiker & Coaching, englischsprachiger Selbstzahler-Markt, Kassensitz und Visum (Blaue Karte/§21) — Leitfaden 2026.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'foreign-psychologist-psychotherapist-moving-to-germany-recognition-en',
                'title' => 'Moving to Germany as a Foreign Psychologist/Psychotherapist (2026): Recognition, Title & the English-Speaking Market',
                'excerpt' => 'A psychologist/psychotherapist abroad who wants to move to Germany: to treat patients you need the Approbation (recognition via the Landesprüfungsamt), EU vs third-country (equivalence → Eignungsprüfung), the protected title "Psychotherapeut", the Heilpraktiker & coaching alternatives, the in-demand English-speaking private-pay market, the Kassensitz bottleneck, and the visa (Blue Card needs a job, self-employment §21).',
                'meta_title' => 'Move to Germany as a Foreign Psychologist — Recognition & Title (2026)',
                'meta_description' => 'Psychologist/psychotherapist in Germany: Approbation, EU/third-country, the protected title, Heilpraktiker & coaching, the English-speaking private-pay market, Kassensitz and visa (Blue Card/§21) — a 2026 guide.',
                'body' => $enBody,
            ],
        ];

        foreach ($variants as $locale => $v) {
            $html = Str::markdown($v['body'], ['html_input' => 'allow', 'allow_unsafe_links' => false]);
            $payload = [
                'locale'           => $locale,
                'translation_group_id' => $groupId,
                'user_id'          => $userId,
                'category_id'      => $categoryId,
                'title'            => $v['title'],
                'excerpt'          => Str::limit($v['excerpt'], 250, '…'),
                'content_md'       => $v['body'],
                'content_html'     => $html,
                'meta_title'       => $v['meta_title'],
                'meta_description' => Str::limit($v['meta_description'], 158, '…'),
                'reading_minutes'  => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
                'is_published'     => true,
                'published_at'     => now(),
            ];

            $existing = Post::where('slug', $v['slug'])->first();
            if ($existing) {
                $existing->update($payload);
            } else {
                Post::create($payload + ['slug' => $v['slug']]);
            }
        }
    }

    public function down(): void
    {
        Post::whereIn('slug', [
            'foreign-psychologist-psychotherapist-moving-to-germany-recognition',
            'foreign-psychologist-psychotherapist-moving-to-germany-recognition-de',
            'foreign-psychologist-psychotherapist-moving-to-germany-recognition-en',
        ])->delete();
    }
};
