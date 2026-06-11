<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Brief → blog: "Almanya randevu rehberi" tr/en/de (3 yazı, tek group).
 * content_html boş → blog:render-html doldurur. Kategori 8, idempotent.
 */
return new class extends Migration
{
    private string $group = 'a1b2c3d4-0005-4000-8000-000000000005';

    private array $slugs = [
        'almanya-randevu-rehberi-konsolosluk-burgeramt-termin',
        'germany-appointment-guide-consulate-burgeramt-termin',
        'termin-guide-deutschland-konsulat-buergeramt',
    ];

    public function up(): void
    {
        $now = now();
        $catId = DB::table('categories')->where('id', 8)->exists() ? 8 : null;

        $tr = <<<'MD'
# Almanya Randevu Rehberi: Konsolosluk & Bürgeramt Randevusu Nasıl Alınır?

> **30 saniyelik özet:** Süreçte iki kritik randevu var: gelmeden önce **vize randevusu** (konsolosluk / iData / VFS) ve geldikten sonra **Bürgeramt randevusu** (Anmeldung, oturum izni). İkisinde de en büyük sorun "randevu dolu". Çözüm: erken başla, iptal takibi yap, alternatif kanalları (başka ilçe/şehir, randevusuz saatler) kullan.

---

## İki tür randevu

| Randevu | Nerede | Ne için |
|---|---|---|
| **Vize randevusu** | Konsolosluk / iData / VFS Global | Gelmeden önce öğrenci vizesi |
| **Bürgeramt Termin** | Şehrin Bürgeramt'ı | Anmeldung, oturum izni (Ausländerbehörde) |

## Vize randevusu (gelmeden önce)

- **Erken başla:** Yoğun dönemde (yaz) randevular haftalar/aylar önce dolar. Koşullu kabul/başvuru varsa kanal açık olduğunda al.
- **Doğru kanal:** Ülkene göre randevuyu konsolosluk mu, iData mı, VFS mi veriyor — resmi siteden teyit et.
- **İptal takibi:** Slotlar gün içinde açılıp kapanır; düzenli kontrol et, mümkünse bildirim kur.
- **Yetki alanı:** Ülkende birden çok başkonsolosluk varsa bölgene bakan hangisiyse ona başvur.

## Bürgeramt randevusu (geldikten sonra)

- **Başka ilçe (Bezirk):** Berlin/München gibi şehirlerde her ilçenin ayrı Bürgeramt'ı var; merkez doluysa kenar ilçe boş olabilir.
- **Randevusuz saatler:** Bazı şehirlerde "offene Sprechstunde" saatleri var — erken git, sıraya gir.
- **İptal takibi:** Sabah erken ve gün ortasında yeni slot açılır; tekrar tekrar bak.
- **Oturum izni (Ausländerbehörde):** Bu randevular daha zor; vizen bitmeden başvuru talebini ve Fiktionsbescheinigung'u öğren.

## Pratik ipuçları

- Randevu portallarını tarayıcıda **yer imine** al, günde birkaç kez kontrol et.
- Sabah 07:00–08:00 ve öğlen yeni slot açılma ihtimali yüksektir.
- Bir slot bulunca **hemen** al; düşünürken kapanır.
- Belgelerini önceden hazırla; randevu kısadır, eksik belge ikinci randevu demektir.

> İlgili: Anmeldung ve vize görüşmesi rehberlerimiz, randevu sonrası adımları kapatır.
MD;

        $en = <<<'MD'
# Germany Appointment Guide: How to Get a Consulate & Bürgeramt Termin

> **30-second summary:** There are two critical appointments in the process: the **visa appointment** before you arrive (consulate / iData / VFS), and the **Bürgeramt appointment** after you arrive (Anmeldung, residence permit). In both, the biggest problem is "no slots available". The fix: start early, track cancellations, and use alternative channels (another district/city, walk-in hours).

---

## Two kinds of appointment

| Appointment | Where | What for |
|---|---|---|
| **Visa appointment** | Consulate / iData / VFS Global | Student visa before arrival |
| **Bürgeramt Termin** | Your city's Bürgeramt | Anmeldung, residence permit (Ausländerbehörde) |

## Visa appointment (before you arrive)

- **Start early:** In peak season (summer) slots fill weeks/months ahead. If you have a conditional admission/application, book as soon as the channel opens.
- **Right channel:** Depending on your country, the appointment is issued by the consulate, iData, or VFS — confirm on the official site.
- **Track cancellations:** Slots open and close during the day; check regularly and set alerts if possible.
- **Jurisdiction:** If your country has several consulates-general, apply to the one covering your region.

## Bürgeramt appointment (after you arrive)

- **Another district (Bezirk):** In cities like Berlin/Munich each district has its own Bürgeramt; if the centre is full, an outer district may be free.
- **Walk-in hours:** Some cities have "offene Sprechstunde" (no appointment) hours — arrive early and queue.
- **Track cancellations:** New slots appear early in the morning and around midday; check repeatedly.
- **Residence permit (Ausländerbehörde):** These appointments are even harder; learn about lodging the request and the Fiktionsbescheinigung before your visa expires.

## Practical tips

- **Bookmark** the appointment portals and check a few times a day.
- New slots are most likely to appear around 07:00–08:00 and at midday.
- When you find a slot, book it **immediately** — it closes while you hesitate.
- Prepare your documents in advance; the appointment is short, and a missing document means a second one.

> Related: our Anmeldung and visa-interview guides cover the steps after the appointment.
MD;

        $de = <<<'MD'
# Termin-Guide Deutschland: Konsulats- & Bürgeramt-Termin bekommen

> **Zusammenfassung in 30 Sekunden:** Im Prozess gibt es zwei kritische Termine: den **Visumtermin** vor der Anreise (Konsulat / iData / VFS) und den **Bürgeramt-Termin** nach der Ankunft (Anmeldung, Aufenthaltstitel). Bei beiden ist das größte Problem „keine Termine frei". Die Lösung: früh anfangen, Stornierungen verfolgen und alternative Kanäle (anderer Bezirk/andere Stadt, offene Sprechstunden) nutzen.

---

## Zwei Arten von Terminen

| Termin | Wo | Wofür |
|---|---|---|
| **Visumtermin** | Konsulat / iData / VFS Global | Studentenvisum vor der Anreise |
| **Bürgeramt-Termin** | Bürgeramt deiner Stadt | Anmeldung, Aufenthaltstitel (Ausländerbehörde) |

## Visumtermin (vor der Anreise)

- **Früh anfangen:** In der Hochsaison (Sommer) sind Termine Wochen/Monate im Voraus weg. Mit bedingter Zulassung/Bewerbung buchen, sobald der Kanal offen ist.
- **Richtiger Kanal:** Je nach Land vergibt das Konsulat, iData oder VFS den Termin — prüfe es auf der offiziellen Seite.
- **Stornierungen verfolgen:** Slots öffnen und schließen sich im Tagesverlauf; prüfe regelmäßig und richte wenn möglich Benachrichtigungen ein.
- **Zuständigkeit:** Hat dein Land mehrere Generalkonsulate, bewirb dich bei dem für deine Region zuständigen.

## Bürgeramt-Termin (nach der Ankunft)

- **Anderer Bezirk:** In Städten wie Berlin/München hat jeder Bezirk ein eigenes Bürgeramt; ist die Mitte voll, kann ein Außenbezirk frei sein.
- **Offene Sprechstunden:** Manche Städte haben Zeiten ohne Termin — früh kommen und anstellen.
- **Stornierungen verfolgen:** Neue Slots erscheinen früh am Morgen und um die Mittagszeit; prüfe wiederholt.
- **Aufenthaltstitel (Ausländerbehörde):** Diese Termine sind noch schwerer; informiere dich vor Ablauf deines Visums über die Antragstellung und die Fiktionsbescheinigung.

## Praktische Tipps

- **Setze Lesezeichen** auf die Terminportale und prüfe mehrmals täglich.
- Neue Slots erscheinen am ehesten gegen 07:00–08:00 und mittags.
- Wenn du einen Slot findest, buche ihn **sofort** — er schließt, während du zögerst.
- Bereite deine Dokumente im Voraus vor; der Termin ist kurz, und ein fehlendes Dokument bedeutet einen zweiten.

> Verwandt: Unsere Anmeldung- und Visumsgespräch-Leitfäden decken die Schritte nach dem Termin ab.
MD;

        $rows = [
            ['locale' => 'tr', 'slug' => $this->slugs[0],
                'title' => 'Almanya Randevu Rehberi: Konsolosluk & Bürgeramt Randevusu Nasıl Alınır?',
                'excerpt' => 'Vize randevusu (konsolosluk/iData/VFS) ve Bürgeramt Termin\'i nasıl alınır, randevu dolduğunda ne yapılır — iptal takibi ve alternatif kanallarla pratik rehber.',
                'content_md' => $tr, 'reading_minutes' => 5],
            ['locale' => 'en', 'slug' => $this->slugs[1],
                'title' => 'Germany Appointment Guide: How to Get a Consulate & Bürgeramt Termin',
                'excerpt' => 'How to get a visa appointment (consulate/iData/VFS) and a Bürgeramt Termin, and what to do when slots are full — a practical guide with cancellation-tracking and alternative channels.',
                'content_md' => $en, 'reading_minutes' => 5],
            ['locale' => 'de', 'slug' => $this->slugs[2],
                'title' => 'Termin-Guide Deutschland: Konsulats- & Bürgeramt-Termin bekommen',
                'excerpt' => 'Wie du einen Visumtermin (Konsulat/iData/VFS) und einen Bürgeramt-Termin bekommst und was du tust, wenn keine Slots frei sind — ein praktischer Guide.',
                'content_md' => $de, 'reading_minutes' => 5],
        ];

        foreach ($rows as $r) {
            if (DB::table('posts')->where('slug', $r['slug'])->where('locale', $r['locale'])->exists()) {
                continue;
            }
            DB::table('posts')->insert([
                'locale' => $r['locale'],
                'translation_group_id' => $this->group,
                'type' => 'blog',
                'category_id' => $catId,
                'title' => $r['title'],
                'slug' => $r['slug'],
                'excerpt' => $r['excerpt'],
                'content_md' => $r['content_md'],
                'content_html' => null,
                'reading_minutes' => $r['reading_minutes'],
                'meta_title' => $r['title'],
                'meta_description' => $r['excerpt'],
                'is_published' => true,
                'published_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('posts')->where('translation_group_id', $this->group)->delete();
    }
};
