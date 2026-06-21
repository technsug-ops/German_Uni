<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Blog (TR+DE+EN): Almanya'da psikoterapist olmak — 2020 reformu, Approbation & Kassensitz (2026).
 *
 * Doğrulandı (Psychotherapeutengesetz 1.9.2020 reformu, BPtK, 2026):
 *  - Yeni yasa 1 Eylül 2020'de yürürlüğe girdi; eski "psikoloji oku → sonra 3-5 yıl Ausbildung" modelini değiştirdi.
 *  - Yeni doğrudan yol: Polyvalenter B.Sc. Psychologie (3y, klinik profil şart) → M.Sc. Klinische
 *    Psychologie & Psychotherapie (2y) → Approbationsprüfung → lisanslı Psychotherapeut → ~5 yıl
 *    Weiterbildung → Fachkunde'li Fachpsychotherapeut (VT/tiefenpsych./analitik/sistemik).
 *  - Nüans: Approbation ≠ bağımsız çalışma. Master+Approbation sonrası Weiterbildung İÇİNDE hasta görürsün;
 *    bağımsız muayenehane + kasa faturalandırması için Fachkunde (~5y) + Kassensitz şart.
 *  - Kassensitz: Bedarfsplanung ile kısıtlı, pahalı/bekleme listeli; sadece KENDİ kasa-muayenehanen için gerekir,
 *    klinik/başkasının muayenehanesinde çalışmak için DEĞİL. Bekleme sürelerinin asıl sebebi budur.
 *  - psikolog ≠ psikoterapist: tek başına psikoloji diploması terapi yapma hakkı vermez.
 *
 * Yazar: Halil Yaprakli. Kategori: almanyada-egitim → law-policy → first. FK-safe + slug-bazlı idempotent.
 * İç-link: studying-psychology + foreign-doctors-approbation + anabin (hepsi tr/de/en mevcut) → locale-doğru.
 */
return new class extends Migration
{
    public function up(): void
    {
        $groupId = '7c1e9a20-5b3d-4e8a-9f12-1a2b3c4d5e77';

        $userId = DB::table('users')->where('email', 'yapra-test1@gmail.com')->value('id')
            ?? DB::table('users')->where('slug', 'halil-yaprakli')->value('id')
            ?? DB::table('users')->where('name', 'Halil Yaprakli')->value('id')
            ?? DB::table('users')->orderBy('id')->value('id');

        $categoryId = DB::table('categories')->where('slug', 'almanyada-egitim')->value('id')
            ?? DB::table('categories')->where('slug', 'law-policy')->value('id')
            ?? DB::table('categories')->orderBy('id')->value('id');

        $trBody = <<<'MD'
Almanya'da kafa karıştıran ilk gerçek şu: **psikolog ≠ psikoterapist.** Psikoloji diploması tek başına terapi yapma hakkı vermez — psikologların çoğu klinik dışı alanlarda (İK, araştırma, danışmanlık, iş dünyası) çalışır. Hasta tedavi etmek isteyen kişinin izlediği yol ise **1 Eylül 2020**'de tamamen değişti.

## Eski model neden değişti?
2020'den önce yol şuydu: önce psikoloji oku, **sonra** master sonrası 3-5 yıllık (çoğu zaman ücretsiz, hatta paralı) bir **Ausbildung** yap. Yeni **Psychotherapeutengesetz** bu modeli kaldırdı ve terapistliği **doğrudan bir okul yoluna** çevirdi.

## Yeni yol: 4 adım
1. **Polyvalenter B.Sc. Psychologie** (3 yıl) — devam edebilmek için **klinik profil** içermesi şart.
2. **M.Sc. Klinische Psychologie & Psychotherapie** (2 yıl) — ağırlıklı klinik; bachelorun aksine polivalans yok.
3. **Approbationsprüfung** (devlet lisans sınavı) — master'ın hemen ardından girilir; geçince **lisanslı Psychotherapeut** olursun. "Psychotherapeut" artık korumalı, birleşik bir unvandır.
4. **Weiterbildung** (~5 yıl, ücretli çalışma) → bir yöntemde **Fachkunde** ile **Fachpsychotherapeut** (Verhaltenstherapie/BDT, derinlik-psikolojik, analitik, sistemik).

| Adım | Süre | Sana ne sağlar |
|---|---|---|
| B.Sc. Psychologie (klinik profil) | 3 yıl | Temel; master'a erişim |
| M.Sc. Klinische Psych. & Psychotherapie | 2 yıl | Approbation sınavına erişim |
| Approbation | sınav | Lisanslı terapist; Weiterbildung İÇİNDE hasta görürsün |
| Weiterbildung + Fachkunde | ~5 yıl | Bir yöntemde tam yetkinlik |
| + Kassensitz | (ayrı) | Kendi kasa-muayenehaneni açma hakkı |

## "Master + Approbation bitti, artık serbest miyim?"
Hayır — Reddit'te en çok karıştırılan nokta tam burası. Master + **Approbation** sonrası **lisanslısın** ve **Weiterbildung kapsamında** hasta tedavi edebilirsin. Ama **bağımsız çalışmak ve yasal sağlık sigortasına (GKV) fatura kesmek** için hem **Fachkunde** (o ~5 yıllık Weiterbildung) hem de bir **Kassensitz** gerekir.

## Kassensitz darboğazı
**Kassensitz**, yasal sigortayla çalışma "koltuğu"dur — **Bedarfsplanung** (ihtiyaç planlaması) ile sıkı sıkıya sınırlıdır, pahalıdır ve genelde bekleme listelidir. **Sadece kendi sigorta-faturalı muayenehaneni** açacaksan gerekir. Bir klinikte ya da başkasının muayenehanesinde çalışacaksan **gerekmez**. Hastaların terapiye aylarca beklemesinin asıl sebebi terapist azlığı değil — **bu koltukların kısıtlı olmasıdır.**

## Gerçekçi takvim
Lise sonrası kabaca: 3 (B.Sc.) + 2 (M.Sc.) + ~5 (Weiterbildung) = **~10 yıl** toplam, kendi kasa-muayenehanen için artı Kassensitz beklemesi.

## Özet
Almanya'da psikoterapist olmak uzun ama net bir yoldur: doğru bachelor (klinik profil) → klinik master → Approbation → Weiterbildung. Master sonrası lisanslısın ama bağımsız+kasalı çalışmak Fachkunde + Kassensitz ister. Yabancıysan diploma denkliğini erken kontrol et.

İlgili: [yabancı olarak psikoloji okumak](/tr/blog/studying-psychology-in-germany-international-students-nc-language) · [Almanya'da yabancı doktor / Approbation](/tr/blog/foreign-doctors-germany-approbation-fsp-kenntnispruefung) · [Anabin](/tr/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma).

---
*2026 itibarıyla yürürlükteki kurallar temel alınmıştır; Weiterbildung ve Kassensitz şartları eyalet/oda (KV) ve yönteme göre değişir — başvurudan önce ilgili Psychotherapeutenkammer'den teyit et.*
MD;

        $deBody = <<<'MD'
Die erste verwirrende Wahrheit in Deutschland: **Psychologe ≠ Psychotherapeut.** Ein Psychologie-Abschluss allein berechtigt nicht zur Therapie — viele Psycholog:innen arbeiten außerhalb der Klinik (HR, Forschung, Beratung, Wirtschaft). Der Weg für alle, die Patient:innen behandeln wollen, hat sich am **1. September 2020** komplett geändert.

## Warum das alte Modell geändert wurde
Vor 2020 galt: erst Psychologie studieren, **danach** eine 3-5-jährige (oft unbezahlte, teils kostenpflichtige) **Ausbildung** nach dem Master. Das neue **Psychotherapeutengesetz** schaffte dieses Modell ab und machte den Beruf zu einem **direkten Studienweg**.

## Der neue Weg: 4 Schritte
1. **Polyvalenter B.Sc. Psychologie** (3 Jahre) — muss ein **klinisches Profil** enthalten, um zu qualifizieren.
2. **M.Sc. Klinische Psychologie & Psychotherapie** (2 Jahre) — stark klinisch; anders als der Bachelor keine Polyvalenz.
3. **Approbationsprüfung** (staatliche Prüfung) — direkt nach dem Master; danach bist du **approbierte:r Psychotherapeut:in**. „Psychotherapeut" ist nun ein einheitlicher, geschützter Titel.
4. **Weiterbildung** (~5 Jahre, bezahlte Arbeit) → **Fachpsychotherapeut:in** mit **Fachkunde** in einem Verfahren (Verhaltenstherapie, tiefenpsychologisch, analytisch, systemisch).

| Schritt | Dauer | Was es dir erlaubt |
|---|---|---|
| B.Sc. Psychologie (klinisches Profil) | 3 Jahre | Grundlage; Zugang zum Master |
| M.Sc. Klinische Psych. & Psychotherapie | 2 Jahre | Zugang zur Approbationsprüfung |
| Approbation | Prüfung | Approbiert; Behandlung INNERHALB der Weiterbildung |
| Weiterbildung + Fachkunde | ~5 Jahre | Volle Kompetenz in einem Verfahren |
| + Kassensitz | (separat) | Eigene Kassenpraxis eröffnen |

## „Master + Approbation geschafft — bin ich jetzt selbstständig?"
Nein — genau hier liegt die größte Verwirrung. Nach Master + **Approbation** bist du **approbiert** und darfst **innerhalb der Weiterbildung** Patient:innen behandeln. Aber um **selbstständig zu arbeiten und mit der GKV abzurechnen**, brauchst du sowohl die **Fachkunde** (die ~5-jährige Weiterbildung) als auch einen **Kassensitz**.

## Der Kassensitz-Engpass
Ein **Kassensitz** ist ein „Sitz" für die Versorgung gesetzlich Versicherter — durch die **Bedarfsplanung** streng begrenzt, teuer und meist mit Warteliste. Er wird **nur für die eigene Kassenpraxis** benötigt. In einer Klinik oder in der Praxis einer anderen Person angestellt? Dann **nicht nötig**. Dass Patient:innen monatelang auf einen Therapieplatz warten, liegt nicht am Therapeutenmangel — sondern an der **Begrenzung dieser Sitze.**

## Realistischer Zeitplan
Nach dem Abi grob: 3 (B.Sc.) + 2 (M.Sc.) + ~5 (Weiterbildung) = **~10 Jahre** insgesamt, plus Warten auf einen Kassensitz für die eigene Praxis.

## Fazit
Psychotherapeut:in in Deutschland zu werden ist ein langer, aber klarer Weg: richtiger Bachelor (klinisches Profil) → klinischer Master → Approbation → Weiterbildung. Nach dem Master bist du approbiert, aber selbstständig+mit Kasse erfordert Fachkunde + Kassensitz. Als Ausländer:in: Anerkennung früh prüfen.

Verwandt: [Psychologie als Ausländer studieren](/de/blog/studying-psychology-in-germany-international-students-nc-language-de) · [Ausländische Ärzte / Approbation](/de/blog/foreign-doctors-germany-approbation-fsp-kenntnispruefung-de) · [Anabin](/de/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma-de).

---
*Stand 2026; Weiterbildung und Kassensitz variieren je nach Land/Kammer (KV) und Verfahren — vor der Bewerbung bei der zuständigen Psychotherapeutenkammer bestätigen.*
MD;

        $enBody = <<<'MD'
The first confusing truth in Germany: **psychologist ≠ psychotherapist.** A psychology degree alone does NOT let you do therapy — many psychologists work outside the clinic (HR, research, consulting, business). The path for anyone who wants to treat patients changed completely on **1 September 2020**.

## Why the old model changed
Before 2020 the route was: study psychology first, **then** do a 3-5-year (often unpaid, sometimes fee-paying) **Ausbildung** after the master. The new **Psychotherapeutengesetz** abolished that model and turned the profession into a **direct studies route**.

## The new path: 4 steps
1. **Polyvalenter B.Sc. Psychologie** (3 years) — must include a **clinical profile** to qualify.
2. **M.Sc. Klinische Psychologie & Psychotherapie** (2 years) — heavily clinical; unlike the bachelor, no polyvalence.
3. **Approbationsprüfung** (state licensing exam) — taken right after the master; pass it and you become a licensed **Psychotherapeut**. "Psychotherapeut" is now a unified, protected title.
4. **Weiterbildung** (~5 years, paid work) → **Fachpsychotherapeut** with **Fachkunde** in a procedure (Verhaltenstherapie/CBT, depth-psychological, analytic, systemic).

| Step | Duration | What it lets you do |
|---|---|---|
| B.Sc. Psychologie (clinical profile) | 3 years | Foundation; access to the master |
| M.Sc. Klinische Psych. & Psychotherapie | 2 years | Access to the Approbation exam |
| Approbation | exam | Licensed; treat WITHIN the Weiterbildung |
| Weiterbildung + Fachkunde | ~5 years | Full competence in a procedure |
| + Kassensitz | (separate) | Open your own insurance-billing practice |

## "I passed the master + Approbation — am I done?"
No — this is exactly where the (Reddit) confusion lives. After the master + **Approbation** you ARE licensed and may treat patients **within the Weiterbildung**. But to practise **independently and bill statutory health insurance (GKV)**, you need both the **Fachkunde** (that ~5-year Weiterbildung) AND a **Kassensitz**.

## The Kassensitz bottleneck
A **Kassensitz** is a statutory-insurance practice "seat" — strictly limited by **Bedarfsplanung** (needs planning), expensive and usually waitlisted. It is needed **only for your OWN insurance-billing practice**. Employed in a clinic or in someone else's practice? Then it's **not needed**. The reason patients wait months for therapy isn't a lack of therapists — it's the **limited number of these seats.**

## Realistic timeline
After secondary school, roughly: 3 (B.Sc.) + 2 (M.Sc.) + ~5 (Weiterbildung) = **~10 years** total, plus waiting for a Kassensitz if you want your own practice.

## Bottom line
Becoming a psychotherapist in Germany is a long but clear path: the right bachelor (clinical profile) → clinical master → Approbation → Weiterbildung. After the master you're licensed, but practising independently with insurance billing requires Fachkunde + a Kassensitz. If you're a foreigner, check your degree recognition early.

Related: [studying psychology as a foreigner](/en/blog/studying-psychology-in-germany-international-students-nc-language-en) · [foreign doctors in Germany / Approbation](/en/blog/foreign-doctors-germany-approbation-fsp-kenntnispruefung-en) · [Anabin](/en/blog/what-are-anabin-h-h-h-how-is-your-turkish-diploma-en).

---
*Based on rules in force as of 2026; Weiterbildung and Kassensitz conditions vary by state/chamber (KV) and procedure — confirm with the relevant Psychotherapeutenkammer before applying.*
MD;

        $variants = [
            'tr' => [
                'slug'  => 'becoming-a-psychotherapist-in-germany-2020-reform-approbation',
                'title' => 'Almanya\'da Psikoterapist Olmak (2026): 2020 Reformu, Approbation ve Kassensitz',
                'excerpt' => 'Almanya\'da psikoterapist olmak: psikolog ≠ psikoterapist, 1 Eylül 2020 reformu, yeni doğrudan yol (Polyvalenter B.Sc. → klinik M.Sc. → Approbation → Weiterbildung), "master sonrası serbest miyim?" nüansı (Approbation vs Fachkunde) ve Kassensitz darboğazı — dürüst rehber.',
                'meta_title' => 'Almanya\'da Psikoterapist Olmak — 2020 Reformu, Approbation, Kassensitz (2026)',
                'meta_description' => 'Psikolog ≠ psikoterapist. 2020 reformu, yeni yol (B.Sc.→M.Sc.→Approbation→Weiterbildung), Fachkunde vs Approbation ve Kassensitz darboğazı — Almanya\'da psikoterapist olma 2026 rehberi.',
                'body' => $trBody,
            ],
            'de' => [
                'slug'  => 'becoming-a-psychotherapist-in-germany-2020-reform-approbation-de',
                'title' => 'Psychotherapeut werden in Deutschland (2026): Die Reform 2020, Approbation & Kassensitz',
                'excerpt' => 'Psychotherapeut:in werden in Deutschland: Psychologe ≠ Psychotherapeut, die Reform vom 1. September 2020, der neue Direktweg (Polyvalenter B.Sc. → klinischer M.Sc. → Approbation → Weiterbildung), die Nuance „nach dem Master selbstständig?" (Approbation vs Fachkunde) und der Kassensitz-Engpass — ein ehrlicher Leitfaden.',
                'meta_title' => 'Psychotherapeut werden in Deutschland — Reform 2020, Approbation, Kassensitz (2026)',
                'meta_description' => 'Psychologe ≠ Psychotherapeut. Reform 2020, neuer Weg (B.Sc.→M.Sc.→Approbation→Weiterbildung), Fachkunde vs Approbation und der Kassensitz-Engpass — Leitfaden 2026 für Deutschland.',
                'body' => $deBody,
            ],
            'en' => [
                'slug'  => 'becoming-a-psychotherapist-in-germany-2020-reform-approbation-en',
                'title' => 'Becoming a Psychotherapist in Germany (2026): the 2020 Reform, Approbation & Kassensitz',
                'excerpt' => 'Becoming a psychotherapist in Germany: psychologist ≠ psychotherapist, the 1 September 2020 reform, the new direct route (Polyvalenter B.Sc. → clinical M.Sc. → Approbation → Weiterbildung), the "am I done after the master?" nuance (Approbation vs Fachkunde) and the Kassensitz bottleneck — an honest guide.',
                'meta_title' => 'Becoming a Psychotherapist in Germany — 2020 Reform, Approbation, Kassensitz (2026)',
                'meta_description' => 'Psychologist ≠ psychotherapist. The 2020 reform, the new path (B.Sc.→M.Sc.→Approbation→Weiterbildung), Fachkunde vs Approbation and the Kassensitz bottleneck — a 2026 guide to Germany.',
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
            'becoming-a-psychotherapist-in-germany-2020-reform-approbation',
            'becoming-a-psychotherapist-in-germany-2020-reform-approbation-de',
            'becoming-a-psychotherapist-in-germany-2020-reform-approbation-en',
        ])->delete();
    }
};
