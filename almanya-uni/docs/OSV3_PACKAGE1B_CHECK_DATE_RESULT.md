# OSV-3 Paket 1b: Kontrol tarihi ile yürürlük tarihinin ayrılması

Tarih: 15 Ekim 2026. Durum: **yalnız yerelde uygulandı ve test edildi.** Commit, push, deploy ve KB güncellemesi yok; onay bekliyor.

## Sorun

Chatbot öğrenim vizesi kanalını doğru anlatıyor, ancak "**14.10.2026 tarihinden itibaren** yalnızca Konsolosluk Hizmet Portalı" diyebiliyordu (C08, 14.10.2026 canlı test).

14.10.2026 bizim kontrol tarihimizdir; kuralın başlangıç tarihi değildir. Yanlış okumanın kaynağı OSV-3 Paket 1'in 30 kayda (TR/EN/DE × 10) eklediği nottaki iki ifade:

- Başlık: `**Güncelleme (14.10.2026):**` / `**Update (14.10.2026):**` / `**Aktualisierung (14.10.2026):**`
- Zaman zarfı: "**artık** yalnızca" / "are **now** handled only" / "**inzwischen** ausschließlich"

İkisi birlikte, 14.10.2026'da başlayan yeni bir kural izlenimi veriyor.

## Resmî kaynak kontrolü

`https://tuerkei.diplo.de/tr-tr/service/05-visaeinreise/2728622-2728622` sayfasının TR ve DE sürümleri 15.10.2026'da yeniden okundu. Sayfa kuralın başlangıç tarihini **vermiyor**. Bu yüzden yürürlük tarihi uydurulmadı; metin, tarihin bilinmediğini açıkça söylüyor.

## Değişiklik (en küçük kapsam)

Yalnızca notun ilk iki cümlesi değişti. Bağlantılar ve geri kalan metin aynı kaldı.

| Dil | Önce | Sonra |
|---|---|---|
| TR | **Güncelleme (14.10.2026):** … başvuruları **artık** yalnızca Konsolosluk Hizmet Portalı … üzerinden yürütülüyor: … ([kaynak](…)). | **Güncel durum (kontrol tarihi: 14.10.2026):** … başvuruları yalnızca Konsolosluk Hizmet Portalı … üzerinden yürütülüyor: … ([kaynak](…)). **Bu kuralın hangi tarihten beri uygulandığı resmî sayfada belirtilmiyor; 14.10.2026 yalnızca bizim kontrol tarihimiz.** |
| EN | **Update (14.10.2026):** … applications are **now** handled only through … | **Current status (checked 14.10.2026):** … applications are handled only through … **The official page does not say since when this rule applies; 14.10.2026 is only the date we checked it.** |
| DE | **Aktualisierung (14.10.2026):** … laufen Visumanträge … **inzwischen** ausschließlich … | **Aktueller Stand (geprüft am 14.10.2026):** … laufen Visumanträge … ausschließlich … **Seit wann diese Regel gilt, nennt die offizielle Seite nicht; der 14.10.2026 ist nur unser Prüfdatum.** |

**Etkilenen kayıtlar (30):**
- **SSS (21):** 7 grup × TR/EN/DE.
  - `how-do-you-apply-for-a-visa-through-idata`
  - `how-many-weeks-on-average-does-it-take-to-get-a-visa-appointment-from-the`
  - `how-many-weeks-does-it-take-to-get-a-visa-appointment-at-the-german-consulate`
  - `what-is-the-student-visa-application-appointment-process-at-the-ankara-consulate`
  - `the-earliest-appointment-is-in-early-october-what-should-you-do`
  - `what-are-the-alternative-ways-to-get-a-visa-appointment-if-you-cant-find-one`
  - `how-long-does-a-national-visa-application-take-through-idata-izmir`
- **Yazı (9):** 3 grup × TR/EN/DE.
  - iDATA rehberi
  - Konsolosluk randevu stratejisi
  - Öğrenci vizesi süreç rehberi (`germany-student-visa-consulate-process-before-you-go` / `german-student-visa-process-guide-2026` / `deutscher-studentenvisum-prozess-leitfaden-2026`)

**Değişmeyenler:** ID, slug, URL, çeviri grubu, canonical ve hreflang. "kontrol: 14.10.2026" ve "checked 14.10.2026" biçimindeki kaynak atıfları zaten kontrol tarihi olarak doğru yazılmıştı; onlara dokunulmadı.

**Araç sayfası:** `resources/views/tools/visa-appointment.blade.php` hero metnindeki "**artık** yalnızca" ifadesinden "artık" kaldırıldı. Sayfanın altı zaten "14.10.2026'da kontrol edilen resmî kaynaklar" diyor.

## Uygulama mekanizması

- **Paket:** `database/data/osv/2026-10-14-osv3-package1b-check-date.json`, 30 `replace` düzenlemesi. OSV-3 ile aynı güvenli motor kullanılıyor (`App\Support\OfficialInfoRepair`):
  - Kayıt slug + locale ile bulunur.
  - Beklenen metin yoksa `mismatch` olur ve kayıt atlanır (ezilmez).
  - İkinci çalıştırma no-op'tur.
- **Migration:** `2026_10_14_000500_osv3_package1b_check_date_vs_effective_date`. Önceki migration dosyalarına dokunulmadı.
- **Paket 1 verisinde iki küçük güncelleme.** Migration dosyası değişmedi; yalnızca okuduğu JSON değişti:
  1. **Marker'lar (30 satır).** Paket 1'in not ekleyen `prepend` / `insert_after` düzenlemeleri, notun zaten var olduğunu `Güncelleme (14.10.2026):` marker'ıyla anlıyordu. 1b bu başlığı değiştirdiği için paket 1 bir gün tekrar çalıştırılsa notu **ikinci kez eklerdi**. Marker'lar, iki sürümde de geçen sabit bir parçayla değiştirildi (ör. `Konsolosluk Hizmet Portalı (Auslandsportal) üzerinden yürütülüyor: [digital.diplo.de/studium]`). Test bunu doğruluyor.
  2. **İki `replace` metni** (`F-166-tr-check`, `F-582-de-check`). 14.10'daki emoji temizliği (`2026_10_14_000300`) bu iki metindeki ✅ işaretini kaldırmıştı; bu yüzden `/admin/ops/osv-repair` önizlemesi 2 `mismatch` gösteriyordu. Paketteki metin emojisiz hâliyle güncellendi.
  Sonuç: önizleme yerelde paket 1 için `already: 146`, paket 1b için `already: 30`.

## Yerel sonuç

| Kontrol | Sonuç |
|---|---|
| 1b önizleme (uygulama öncesi) | `apply: 30` |
| Migration | 3 sn, DONE |
| 1b önizleme (sonra) | `already: 30` |
| Paket 1 önizleme (sonra) | `already: 146` (önceden `already: 144, mismatch: 2`) |
| Testler | `OsvPackage1bCheckDateTest` 3/3, `OsvPackage1RepairTest` 9/9 |

`OsvPackage1bCheckDateTest` şunları doğrular:
- Her yeni metin kontrol tarihi ifadesi içerir.
- "artık / now handled / inzwischen" geçmez.
- Metinde 14.10.2026 dışında tarih yoktur (yürürlük tarihi uydurulmamış).
- Bir kez uygulanır; ikinci çalıştırma no-op'tur.
- Beklenmeyen metin ezilmez.
- Paket 1'in 30 marker'ı yeni metinde de geçer.

## KB planı (uygulanmadı)

KB'nin tamamı yeniden gömülmez. `kb:embed` artımlı çalışır: yalnızca içerik hash'i değişen kayıtları siler ve yeniden gömer.

- **Etkilenen:** 30 kayıt → **≈102 chunk** (SSS 21: kayıt başına 1; yazı 81: `chunkProse` ile hesaplandı).
- **Deploy sonrası, admin oturumuyla:**
  1. `/admin/ops/kb-embed?source=faq,post&dry=1` → beklenen: embed edilecek ≈ 102, gerisi "değişmeyen". **200'ü aşarsa durdurun:** başka içerik de değişmiş demektir.
  2. `/admin/ops/kb-embed?source=faq,post` → yalnızca bu kayıtlar yeniden gömülür (≈102 Gemini embedding çağrısı).
  3. Doğrulama: C08–C10 ve C14 tekrar sorulur. Beklenen: "14.10.2026 tarihinden itibaren" ifadesi yok; yanıt "kontrol tarihi" ya da tarihsiz biçimde.
- **Canlı önizleme:** `/admin/ops/osv-repair?file=2026-10-14-osv3-package1b-check-date` → deploy öncesi `apply: 30`, sonrası `already: 30`.

## Riskler

- Canlıda bu 30 kaydın notu 14.10'dan sonra panelden elle değiştirilmişse ilgili satır `mismatch` olarak atlanır (ezilmez). Önizlemeden görülür.
- Chatbot tarihi yine de yorumlayabilir. Metin artık açıkça "başlangıç tarihi belirtilmiyor" diyor; KB yenilendikten sonra C08 tekrar test edilmeli.
