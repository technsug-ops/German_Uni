<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * TR referans: Almanya konsoloslukları (Ankara, İstanbul, İzmir) iletişim bilgileri.
 * Resmi tuerkei.diplo.de ile doğrulandı; bozuk değerler temizlendi. FK-safe, İngilizce slug.
 */
return new class extends Migration
{
    public function up(): void
    {
        $slug = 'germany-consulates-turkey-contact-ankara-istanbul-izmir';

        $body = <<<'MD'
Almanya vize başvurun hangi şehirde yapılacaksa, ilgili **Almanya temsilciliğinin** (büyükelçilik/başkonsolosluk) iletişim bilgileri işine yarar: belge eksiği, randevu sorunu, erteleme veya durum sorgusu için. Aşağıda **Ankara, İstanbul ve İzmir** temsilciliklerinin güncel iletişim bilgileri var.

> ⚠️ **Önce bunu oku:** Türkiye'de vize başvuruları **çoğunlukla [iDATA](https://www.idata.com.tr) vize merkezi** üzerinden yapılır (randevu, belge teslimi, biyometri). Konsolosluk başvuruyu **değerlendirip karar verir**. Ayrıca **telefonla vize/pasaport bilgisi verilmez** — soruları e-posta ile iletmen gerekir. Bilgiler değişebilir; güncel adres, çalışma saati ve randevu için her zaman resmi site **[tuerkei.diplo.de](https://tuerkei.diplo.de/tr-tr/vertretungen)**.

## 🏛️ Ankara — Almanya Büyükelçiliği

| | |
|---|---|
| **Telefon (santral)** | +90 312 455 51 00 |
| **Faks** | +90 312 455 53 37 |
| **Vize e-posta** | visa@anka.auswaertiges-amt.de |
| **Adres** | Remzi Oğuz Arık Mah., Paris Cad. No: 29, 06540 Çankaya/Ankara |
| **Resmi sayfa** | [tuerkei.diplo.de — Ankara](https://tuerkei.diplo.de/tr-tr/vertretungen/botschaft) |

## 🏛️ İstanbul — Almanya Başkonsolosluğu

| | |
|---|---|
| **Telefon** | +90 212 334 61 00 · (vize) +90 212 334 61 67 |
| **Faks** | +90 212 249 99 20 |
| **Vize e-posta** | visa@ista.auswaertiges-amt.de · (genel) info@ista.auswaertiges-amt.de |
| **Adres** | İnönü Cad. No: 10, 34437 Gümüşsuyu, Beyoğlu/İstanbul (PK 6, 34431) |
| **Resmi sayfa** | [tuerkei.diplo.de — İstanbul](https://tuerkei.diplo.de/tr-tr/vertretungen/generalkonsulat-istanbul) |

## 🏛️ İzmir — Almanya Başkonsolosluğu

| | |
|---|---|
| **Telefon** | +90 232 488 88 88 |
| **Faks** | (vize) +90 232 463 79 90 · (pasaport) +90 232 465 03 31 |
| **Vize e-posta** | visa@izmi.auswaertiges-amt.de · visa@izmi.diplo.de · info@izmi.diplo.de |
| **Adres** | Korutürk Mah., Havuzbaşı Sok., 35330 Balçova/İzmir |
| **Resmi sayfa** | [tuerkei.diplo.de — İzmir](https://tuerkei.diplo.de/tr-tr/vertretungen/generalkonsulat-izmir) |

## Hangi konsolosluğa başvurmalıyım?

Genel kural: **ikamet ettiğin ile göre** yetkili temsilcilik belirlenir. Yaşadığın ilin hangi konsolosluğa bağlı olduğunu iDATA randevu sistemi veya resmi site üzerinden kontrol et. İkametini değiştirip başka konsolosluğa geçmek bazen bekleme/farklı kurallar getirebilir — randevu almadan önce teyit et.

## Sırada ne var?

Randevu öncesi hazırlık, biyometri, görüşme ve süreç akışı için: **Konsolosluğa Gitmeden Önce — Almanya Vize Süreci Nasıl İşler** rehberimize bak. Finansman tarafı için [Sperrkonto](/tr/blog/sperrkonto-for-a-german-visa-what-is-it-how-much-and) veya [garantör belgesi](/tr/blog/germany-guarantor-declaration-verpflichtungserklarung-guide); dil belgen hazır değilse [şartlı kabul](/tr/blog/germany-conditional-admission-bedingte-zulassung-guide).

---

**Kaynak:** Almanya Dışişleri Bakanlığı Türkiye temsilcilikleri — [tuerkei.diplo.de](https://tuerkei.diplo.de/tr-tr/vertretungen). Bilgiler bilgilendirme amaçlıdır; resmi siteden teyit et.
MD;

        $excerpt = 'Almanya Ankara, İstanbul ve İzmir konsolosluklarının güncel iletişim bilgileri: telefon, faks, vize e-posta, adres ve resmi sayfa linkleri (2026, resmi kaynakla doğrulandı).';

        $html = Str::markdown($body, ['html_input' => 'allow', 'allow_unsafe_links' => false]);

        $userId = DB::table('users')->where('id', 6)->exists() ? 6 : DB::table('users')->orderBy('id')->value('id');
        $categoryId = DB::table('categories')->where('id', 6)->exists() ? 6 : DB::table('categories')->where('slug', 'vize')->value('id');

        $payload = [
            'locale'           => 'tr',
            'user_id'          => $userId,
            'category_id'      => $categoryId,
            'title'            => 'Almanya Konsoloslukları İletişim Bilgileri: Ankara, İstanbul, İzmir (2026)',
            'excerpt'          => Str::limit($excerpt, 250, '…'),
            'content_md'       => $body,
            'content_html'     => $html,
            'meta_title'       => 'Almanya Konsoloslukları İletişim (Ankara/İstanbul/İzmir) 2026',
            'meta_description' => Str::limit($excerpt, 158, '…'),
            'reading_minutes'  => max(1, (int) round(str_word_count(strip_tags($html)) / 200)),
            'is_published'     => true,
            'published_at'     => now(),
        ];

        $existing = Post::where('slug', $slug)->first();
        if ($existing) {
            $existing->update($payload);
        } else {
            Post::create($payload + ['slug' => $slug, 'translation_group_id' => (string) Str::uuid()]);
        }
    }

    public function down(): void
    {
        Post::where('slug', 'germany-consulates-turkey-contact-ankara-istanbul-izmir')->delete();
    }
};
