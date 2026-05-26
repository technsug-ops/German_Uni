<?php

namespace Database\Seeders;

use App\Models\HousingTemplate;
use Illuminate\Database\Seeder;

class HousingTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'slug' => 'wg-anfrage',
                'category' => 'wg-anfrage',
                'title_tr' => 'WG İlanına Başvuru (Wohngemeinschaft)',
                'title_de' => 'WG-Anfrage: Bewerbungsschreiben',
                'title_en' => 'WG (Shared apartment) Inquiry',
                'description_tr' => 'WG-Gesucht, ImmobilienScout24 veya benzeri sitelerde gördüğün WG ilanına ilk e-postan. Almanlar genelde bu mesaja göre seni eler.',
                'subject_de' => 'Bewerbung als neue/r Mitbewohner/in - {NAME}, {ALTER} Jahre',
                'body_de' => "Hallo {VORNAME_ANRED},\n\nmein Name ist {NAME}, ich bin {ALTER} Jahre alt und komme aus der Türkei. Aktuell studiere ich {STUDIENGANG} im {SEMESTER}. Semester an der {UNI}.\n\nIch bin auf der Suche nach einem ruhigen, freundlichen WG-Zimmer ab {EINZUGSDATUM} und Eure Anzeige hat mich sofort angesprochen.\n\nEin bisschen zu mir: Ich bin {EIGENSCHAFTEN_1}, {EIGENSCHAFTEN_2} und {EIGENSCHAFTEN_3}. In meiner Freizeit {HOBBIES}. Ich rauche nicht, bin sauber und ordnungsliebend und mag gemeinsame Abendessen.\n\nMein monatliches Einkommen liegt bei ca. {EINKOMMEN} EUR (Sperrkonto/BAföG/Eltern). Eine SCHUFA-Auskunft und Sperrkonto-Nachweis kann ich Euch gerne zeigen.\n\nÜber eine kurze Rückmeldung würde ich mich sehr freuen — auch ein kurzes Video-Call wäre für mich kein Problem.\n\nVielen Dank im Voraus und viele Grüße,\n{NAME}\n{TEL}\n{EMAIL}",
                'body_tr_explanation' => "Bu şablon Almanların klassik WG başvurusu yapısına göre yazılmıştır.\n\n1. Konu satırı: İsmin, yaşın ve \"Bewerbung als neue/r Mitbewohner/in\". Onlar bunu görünce \"ciddi başvuru\" olarak filtreler.\n\n2. Selamlama: WG ilanı \"Du\" diye karşılıyorsa \"Hallo {VORNAME}\", \"Sie\" diyorsa \"Sehr geehrte Damen und Herren\". WG'ler genelde Du kullanır.\n\n3. Kendini tanıt: İsim, yaş, ülke, üniversite, bölüm. KISA TUT.\n\n4. WG anlayışın: \"Sauber, ordnungsliebend\" temiz/düzenli ve \"gemeinsame Abendessen\" ortak yemek seven biri olduğunu vurgula.\n\n5. Mali güvence: Sperrkonto, BAföG, aile destek veya yan iş — birinden destek belirt.\n\n6. Video görüşme: Almanya'da değilsen \"Video-Call wäre kein Problem\" deyince ciddi izlenim verir.\n\n7. Sigara: İçmiyorsan vurgula, içiyorsan sadece dışarıda içtiğini söyle.",
                'placeholders' => ['NAME', 'VORNAME_ANRED', 'ALTER', 'STUDIENGANG', 'SEMESTER', 'UNI', 'EINZUGSDATUM', 'EIGENSCHAFTEN_1', 'EIGENSCHAFTEN_2', 'EIGENSCHAFTEN_3', 'HOBBIES', 'EINKOMMEN', 'TEL', 'EMAIL'],
            ],
            [
                'slug' => 'wohnungsanfrage',
                'category' => 'wohnungsanfrage',
                'title_tr' => 'Özel Daire Başvurusu (Vermieter\'a Mail)',
                'title_de' => 'Wohnungsanfrage an Vermieter/Makler',
                'title_en' => 'Apartment Inquiry to Landlord',
                'description_tr' => 'ImmoScout, eBay Kleinanzeigen veya özel bir ev sahibinden bulduğun apartman/stüdyo için resmi başvuru maili.',
                'subject_de' => 'Anfrage: Wohnung in {ADRESSE} - {NAME}',
                'body_de' => "Sehr geehrte Damen und Herren,\n\nmit großem Interesse habe ich Ihre Anzeige zur Wohnung in {ADRESSE} gelesen und möchte mich um eine Besichtigung bewerben.\n\nMein Name ist {NAME}, ich bin {ALTER} Jahre alt und Student/in im {SEMESTER}. Semester {STUDIENGANG} an der {UNI}. Ich suche eine Wohnung ab dem {EINZUGSDATUM} für mindestens {MIETDAUER} Monate.\n\nMein monatliches Einkommen liegt bei {EINKOMMEN} EUR (Sperrkonto + ggf. BAföG/Eltern/Nebenjob). Eine SCHUFA-Auskunft, Mietschuldenfreiheitsbescheinigung und Sperrkonto-Bestätigung kann ich Ihnen gerne vorab zukommen lassen.\n\nIch bin Nichtraucher/in, ruhig und ordnungsliebend, ohne Haustiere. Eine längerfristige Mietbeziehung ist mir wichtig.\n\nFür einen Besichtigungstermin stehe ich {VERFÜGBARKEIT} zur Verfügung. Sollte ein persönliches Erscheinen schwierig sein, biete ich gerne ein Video-Call an.\n\nÜber eine Rückmeldung würde ich mich sehr freuen.\n\nMit freundlichen Grüßen,\n{NAME}\nTel: {TEL}\nE-Mail: {EMAIL}",
                'body_tr_explanation' => "Bu özel daire başvurusu daha resmi bir dil kullanır (Sie ile hitap).\n\n- \"Sehr geehrte Damen und Herren\": Formal mail standardı\n- Mali güvence çok önemli: Vermieter kira ödeyebilecek mi diye bakar\n- Mietschuldenfreiheitsbescheinigung yoksa \"Selbstauskunft\" yeter\n- Sessizlik/temizlik vurgula: Almanlar gürültü sevmez\n- Uzun süre kalmak istediğini söyle\n\nÖnemli: Ev sahipleri günde 50+ başvuru alır. İlk 3 cümlede ciddiyetini göster.",
                'placeholders' => ['NAME', 'ADRESSE', 'ALTER', 'STUDIENGANG', 'SEMESTER', 'UNI', 'EINZUGSDATUM', 'MIETDAUER', 'EINKOMMEN', 'VERFÜGBARKEIT', 'TEL', 'EMAIL'],
            ],
            [
                'slug' => 'studierendenwerk-nachfrage',
                'category' => 'dorm-application',
                'title_tr' => 'Studierendenwerk Yurt Başvurusu — Takip Maili',
                'title_de' => 'Nachfrage zur Wohnheimbewerbung',
                'title_en' => 'Dorm Application Follow-up',
                'description_tr' => 'Yurt başvurusu yaptın ama haber alamadın. Nazikçe durumunu sor.',
                'subject_de' => 'Nachfrage: Wohnheimbewerbung - {NAME}, Bewerbungsnummer {BEW_NR}',
                'body_de' => "Sehr geehrte Damen und Herren,\n\nam {BEWERBUNGSDATUM} habe ich mich online für ein Wohnheimplatz beworben (Bewerbungsnummer: {BEW_NR}). Bisher habe ich noch keine Rückmeldung über meinen Status erhalten.\n\nIch möchte höflich nachfragen, an welcher Stelle meine Bewerbung auf der Warteliste steht und ob es eine ungefähre Wartezeit gibt. Mein gewünschter Einzugstermin ist der {EINZUGSDATUM}.\n\nFalls noch Unterlagen fehlen, bitte ich um eine kurze Mitteilung — ich reiche sie umgehend nach.\n\nVielen Dank im Voraus für Ihre Mühe.\n\nMit freundlichen Grüßen,\n{NAME}\nMatrikelnummer: {MATRIKEL}",
                'body_tr_explanation' => 'Almanlar takibi kibarca yapmayı sever — 2-3 hafta sonra eksik dökümanın olup olmadığını sormak normal. Bewerbungsnummer (başvuru numarası) ve Matrikelnummer (öğrenci numarası) hatırlatmayı kolaylaştırır.',
                'placeholders' => ['NAME', 'BEW_NR', 'BEWERBUNGSDATUM', 'EINZUGSDATUM', 'MATRIKEL'],
            ],
            [
                'slug' => 'besichtigung-danke',
                'category' => 'besichtigung',
                'title_tr' => 'Ev Görme Sonrası Teşekkür Maili',
                'title_de' => 'Dank für die Besichtigung',
                'title_en' => 'Thank You After Viewing',
                'description_tr' => 'Ev görmeye gittin, beğendin. Aynı gün kibar bir teşekkür maili göndermek seni diğerlerinden öne çıkarır.',
                'subject_de' => 'Vielen Dank für die Besichtigung - {NAME}',
                'body_de' => "Sehr geehrte/r Frau/Herr {NACHNAME},\n\nvielen Dank, dass Sie mir heute die Wohnung in {ADRESSE} gezeigt haben. Mir hat das Apartment sehr gut gefallen und ich kann mir gut vorstellen, dort ab {EINZUGSDATUM} einzuziehen.\n\n{POSITIVES_DETAIL_1} und {POSITIVES_DETAIL_2} haben mich besonders überzeugt. Ich erfülle alle genannten Voraussetzungen (Sperrkonto, Studentenstatus, Mindestmietdauer) und bin gerne bereit, die nächsten Unterlagen zu liefern.\n\nSollten Sie sich für mich als Mieter/in entscheiden, würde ich mich sehr freuen.\n\nMit freundlichen Grüßen,\n{NAME}",
                'body_tr_explanation' => 'Almanya\'da ev görüşmesinden sonra teşekkür maili göndermek hâlâ değer verilen bir nezaket göstergesi. Ev sahibi 10 başvurudan birini seçecekse bunu yapan adayı daha çok hatırlar.',
                'placeholders' => ['NAME', 'NACHNAME', 'ADRESSE', 'EINZUGSDATUM', 'POSITIVES_DETAIL_1', 'POSITIVES_DETAIL_2'],
            ],
        ];

        foreach ($templates as $i => $t) {
            HousingTemplate::updateOrCreate(
                ['slug' => $t['slug']],
                array_merge($t, ['sort_order' => $i, 'is_active' => true])
            );
        }

        $this->command?->info('HousingTemplate seeded: ' . HousingTemplate::count() . ' rows.');
    }
}
