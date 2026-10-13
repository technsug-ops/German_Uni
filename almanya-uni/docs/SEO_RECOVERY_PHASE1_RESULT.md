# SEO Recovery Faz 1: Üniversite sayfalarında program listesi (sonuç raporu)

Tarih: 13 Ekim 2026. Durum: **yalnız yerelde hazırlandı ve test edildi. Commit, push ve deploy yapılmadı; onay bekliyor.**
Ortam: yerel `almanyauni_baseline` (canlının anonimleştirilmiş kopyası), `php artisan serve`.
Dayanak: `docs/SEO_RECOVERY_AUDIT_2026-10-13.md` §6–7, Roadmap P0-2.

> **Önemli:** Bu değişiklik tek başına Google indeksleme veya trafik artışı garantisi vermez. 22 Eylül sıçramasıyla ilişki nedensellik olarak doğrulanmadı. Etki ancak canlıya çıkıştan sonra Search Console ile (Keşfedildi/Tarandı – dizinde değil, Tarama istatistikleri, üniversite sayfası gösterimleri) 4–12 hafta izlenerek ölçülebilir.

## 1. Başlangıç mimarisi

- `UniversityWebController::show` aktif programların **hepsini** tek sorguda (`->get()`) yüklüyordu. Sayfalama yoktu.
- Programlar derece başlıkları altında listeleniyordu. İnce (noindex) ve indexable programlar karışıktı; noindex programlar da bağlantılıydı.
- Liste, Alpine.js ile açılan "Programlar" sekmesindeydi (`x-show`). Bağlantılar ilk HTML'de vardı, sekme yalnız görünürlüğü değiştiriyordu.
- Filtre yoktu. "İngilizce programlar" kartı yalnız sekmeyi açıyordu.
- `?page=2` gibi URL'ler sayfanın birebir aynısını döndürüyordu. Hreflang `?page=2` içeriyor ama canonical query'siz sayfayı gösteriyordu: kopya URL ve tutarsız sinyal.
- Canonical layout'ta `url(request()->path())` olarak üretiliyordu (query'siz); hreflang ise `localized_url()` ile query'yi koruyordu.

### Program sayısı en yüksek 20 üniversite (aktif programlar, yerel baseline)

| Üniversite (slug) | Program | Lisans | Y. lisans | İngilizce | Hochschulkompass |
|---|---|---|---|---|---|
| johannes-gutenberg-universitat-mainz-q161982 | 399 | 172 | 194 | 91 | 122 |
| rheinische-friedrich-wilhelms-universitat-bonn-q152171 | 388 | 166 | 189 | 167 | 73 |
| friedrich-schiller-universitat-jena-q154561 | 355 | 165 | 129 | 68 | 92 |
| friedrich-alexander-universitat-erlangen-nurnberg-q40025 | 353 | 134 | 150 | 109 | 84 |
| freie-universitat-berlin | 347 | 145 | 155 | 109 | 93 |
| universitat-hamburg-q156725 | 328 | 147 | 156 | 83 | 99 |
| technische-universitat-munchen-partner-019ddbba | 324 | 69 | 228 | 213 | 63 |
| philipps-universitat-marburg-q155354 | 318 | 134 | 139 | 46 | 96 |
| georg-august-universitat-gottingen-q152838 | 298 | 105 | 150 | 134 | 80 |
| humboldt-universitat-zu-berlin | 296 | 140 | 138 | 56 | 71 |
| ruhr-universitat-bochum-q309948 | 282 | 98 | 161 | 117 | 59 |
| rheinland-pfalzische-technische-universitat-kaiserslautern-landau-q111020102 | 264 | 100 | 155 | 123 | 56 |
| martin-luther-universitat-halle-wittenberg-q32120 | 248 | 93 | 73 | 35 | 15 |
| ludwig-maximilians-universitat-munchen-q55044 | 244 | 61 | 99 | 112 | 112 |
| albert-ludwigs-universitat-freiburg-im-breisgau-partner-019ddbba | 239 | 77 | 131 | 93 | 16 |
| universitat-kassel-q833822 | 234 | 100 | 95 | 67 | 60 |
| universitat-duisburg-essen-q696757 | 234 | 116 | 110 | 90 | 86 |
| justus-liebig-universitat-giessen-q317053 | 234 | 95 | 112 | 102 | 63 |
| technische-universitat-berlin | 231 | 83 | 124 | 114 | 58 |
| leibniz-universitat-hannover-q678982 | 226 | 75 | 144 | 71 | 0 |

Programı olan 448 üniversitenin 234'ünde 24'ten, 76'sında 100'den fazla program var (ortalama 55, en fazla 399).

## 2. Tespit edilen sorunlar

1. Büyük üniversite sayfaları ilk HTML'de yüzlerce program bağlantısı taşıyordu (Mainz 399). Sayfa 1,65 MB'tı.
2. Bu bağlantıların önemli kısmı ince ve noindex programlara gidiyordu (Mainz: 122 Hochschulkompass kaydı). Bu, keşif kuyruğuna düşük değerli URL eklemenin olası bir yolu.
3. Anlamlı içerikli programlar listede öne çıkmıyordu (yalnız derece + ad sırası).
4. Kullanıcı filtreleyemiyordu; 300+ kartlık bir sayfada gezinmesi gerekiyordu.
5. `?page=N` kopya sayfa üretiyordu; canonical ve hreflang tutarsızdı.

Aynı program bağlantıları başka sayfalarda da var. Program detayı 6 ilgili program, alan sayfası 24, landing'ler sayfa başına 20 bağlantı veriyor. Üniversite sayfasında ise her program yalnız bir kez listeleniyordu ve öyle kalıyor (önce/sonra benzersiz bağlantı = toplam bağlantı).

## 3. Yapılan değişiklik

- **Sayfalama:** sayfa başına **24 program**. 2 sütunlu ızgarada çift sayı ve meslek listesiyle aynı sayfa boyutu. 24, Mainz'ı 17, Bonn'u 17 sayfaya böler; ilk ekranı kısa tutar, sayfa sayısını da yönetilebilir bırakır.
- **Sıralama:** önce anlamlı metni olan programlar (açıklama ya da şart metni dolu; `Program::scopeIndexable` ile aynı sütunlar), sonra derece (lisans, yüksek lisans, diğerleri), ad, id. Sıra deterministik olduğu için sayfalar arasında kayıp ya da tekrar olmuyor. **Noindex programlar gizlenmez ve silinmez**, yalnız sona doğru kayar.
- **Filtreler:**
  - Derece filtresi (`?degree=`): yalnız o üniversitede bulunan dereceler, sayılarıyla gösterilir.
  - Öğretim dili filtresi (`?lang=en|de`): "both" iki filtrede de sayılır. Dil etiketi doğrulanmamış kilitli profillerde gösterilmez.
  - Filtreler gerçek `<a href>` bağlantılarıdır ve birbirleriyle birleşebilir.
- **Sayfa gezintisi:** gerçek `<a href>` bağlantıları. Önceki/sonraki (`rel="prev"`/`rel="next"`), ilk ve son sayfa, ±2 sayfa penceresi. JS gerekmez. `aria-label`, `aria-current="page"`, 44 px dokunma alanları ve satır kaydırma eklendi.
- **URL kuralları:**
  - Sayfa 1 = filtresiz üniversite URL'si; canonical değişmedi.
  - `?page=1` → page'siz URL'ye 301; filtreler korunur.
  - `?page=N` (N ≥ 2) → **self-canonical** (`…?page=N`). Hreflang aynı sayfanın TR/EN/DE karşılıklarına işaret eder; x-default EN. Sıralama dilden bağımsız olduğu için içerik aynıdır.
  - Filtreli URL'ler (`?degree`, `?lang`) → canonical ve hreflang **filtresiz** üniversite URL'si. Robots değiştirilmedi. Gerekçe: filtre URL'leri kullanıcı içindir, indekslenmeleri gerekmez. Bugünkü davranış da buydu (query'siz canonical).
  - Geçersiz sayfa (0, negatif, sayı olmayan, dizi, son sayfadan büyük) ve geçersiz filtre değeri → **404**.
- **Sekme davranışı:** sayfalı ya da filtreli URL'de, ya da `#programs` ile açılınca Programlar sekmesi açık başlar. "İngilizce programlar" kartı artık `?lang=en` filtresine giden bir bağlantı.
- **Sayaçlar:** toplam, İngilizce ve NC-frei sayıları sayfadaki 24 kayıttan değil, tüm aktif programlardan hesaplanır.
- **Değişmeyenler:** program ve üniversite kayıtları, migration, noindex politikası, sitemap, eski URL'ler ve yönlendirmeler, kullanıcı verileri.

## 4. Değişen dosyalar

| Dosya | Değişiklik |
|---|---|
| `app/Http/Controllers/Web/UniversityWebController.php` | `programListing()`: sayaçlar, filtre doğrulama, sayfalama, 301/404, canonical/hreflang paylaşımı; `show()` artık `Request` alıyor |
| `resources/views/universities/show.blade.php` | Sayaçlar, filtre çubuğu, "gösterilen aralık" satırı, sayfa gezintisi, sekme başlangıcı, `id="programs"` |
| `resources/views/layouts/app.blade.php` | Canonical: controller `canonicalUrl` paylaşırsa onu kullanır; aksi halde eskisi gibi |
| `lang/tr.json`, `lang/de.json`, `lang/en.json` | 5 yeni arayüz metni |
| `tests/Feature/UniversityProgramListingTest.php` | Yeni: 8 test |

## 5. Önce / sonra ölçümü

Yerel `php artisan serve` üzerinde, sayfa başına 3 isteğin medyanı alındı. Yerel geliştirme sunucusu tek iş parçacıklı ve gürültülü olduğu için **süreler yalnız göreli karşılaştırma içindir**; canlı süreyi temsil etmez.

| Sayfa | Program bağlantısı (önce → sonra) | HTML boyutu (önce → sonra) | Medyan süre (önce → sonra) |
|---|---|---|---|
| Mainz `/tr/…` | 399 → **24** | 1.649.938 → **438.802** bayt (−73%) | 7,1 sn → 2,7 sn |
| Bonn `/en/…` | 388 → **24** | 1.628.159 → **429.549** bayt (−74%) | 7,9 sn → 2,0 sn |
| Burg Giebichenstein `/de/…` (43 program) | 43 → **24** | 491.508 → **437.729** bayt | 2,9 sn → 1,6 sn |
| Akademie Nürnberg `/tr/…` (6 program) | 6 → 6 | 366.226 → 370.037 bayt | 1,9 sn → 1,4 sn |
| Mainz `?page=2` | 399 (kopya sayfa) → 24 (farklı liste) | 1.650.008 → 441.493 bayt | — |
| Mainz `?page=17` (son) | — → 15 | — → 394.391 bayt | — |

## 6. Programların erişilebilirliği (bütünlük)

`rel="next"` zinciri izlenerek tüm sayfalar gezildi. Bağlantıların birleşimi veritabanındaki aktif program kümesiyle karşılaştırıldı:

| Üniversite / filtre | Sayfa | Bağlantı | Beklenen | Tekrar | Kayıp | Fazla | rel zinciri |
|---|---|---|---|---|---|---|---|
| Mainz (tümü) | 17 | 399 | 399 | 0 | 0 | 0 | sağlam |
| Mainz lisans | 8 | 172 | 172 | 0 | 0 | 0 | sağlam |
| Mainz yüksek lisans + İngilizce | 3 | 67 | 67 | 0 | 0 | 0 | sağlam |
| Bonn (tümü) | 17 | 388 | 388 | 0 | 0 | 0 | sağlam |
| Bonn lisans | 7 | 166 | 166 | 0 | 0 | 0 | sağlam |
| Bonn yüksek lisans + İngilizce | 5 | 116 | 116 | 0 | 0 | 0 | sağlam |

Hata durumları (Mainz):
- `?page=1` → 301 (page'siz URL).
- `?page=1&degree=master` → 301 `?degree=master`.
- `?page=0`, `?page=18`, `?page=abc`, `?page=2x`, `?page=99999`, `?degree=xyz` ve `?lang=fr` → 404.
- `?degree=master&page=9` (son sayfa) → 200.

## 7. Canonical / hreflang sonuçları

| URL | Canonical | Hreflang |
|---|---|---|
| `/tr/universities/{slug}` | Kendisi (değişmedi) | TR/EN/DE + x-default, query'siz (değişmedi) |
| `/tr/universities/{slug}?page=2` | `…?page=2` | TR/EN/DE `…?page=2`, x-default EN `…?page=2` |
| `/de/universities/{slug}?page=3` (son) | `…?page=3` | Karşılıklı `?page=3` |
| `…?degree=master&lang=en` | Filtresiz üniversite URL'si | Filtresiz üniversite URL'leri |

Robots meta değişmedi: tüm üniversite sayfaları `index, follow`.

## 8. Testler

| Test | Sonuç |
|---|---|
| Yeni `UniversityProgramListingTest` (10 test; boş kombinasyon ve nofollow testleri dahil) | Geçti |
| İlgili mevcut testler: hreflang, canonical, sitemap, üniversite, redirect/legacy, SEO, consent, layout (13 dosya) | 119/119 geçti |
| i18n sızıntı denetimi | TR 0, DE 0 |
| Tam test paketi | **555/555 geçti** (38.940 assertion; önceki 547 + 8 yeni) |

`UniversityProgramListingTest` kapsamı:
1. Az programlı üniversitede sayfalama yok.
2. Çok programlı üniversitenin ilk sayfası 24 bağlantıyla sınırlı; sayfa 2 ve 3'e gerçek bağlantı var; `?page=1` bağlantısı üretilmiyor; anlamlı içerikli program önde.
3. Sayfa 2 ve son sayfa self-canonical, hreflang karşılıklı, sekme açık başlıyor.
4. Tüm sayfaların birleşimi = 60 aktif program: kayıp ve tekrar yok, pasif programlar yok.
5. Lisans, yüksek lisans ve dil filtreleri ile filtre + sayfalama.
6. Geçersiz sayfa ve filtre: 301 ve 404'ler.
7. Mobil erişilebilirlik: aria etiketleri, 44 px dokunma alanı, satır kaydırma.
8. Program detay bağlantıları çalışıyor.

## 8b. Yayın öncesi kontrol (canlıya alma görevi) ve düzeltmeler

İlk sürümde iki sorun bulundu ve hedefli olarak düzeltildi:
1. **Boş kombinasyonlara bağlantı:** derece × dil çipleri, sonuç vermeyen 308 kombinasyona bağlantı veriyordu (ör. `?degree=bachelor&lang=en`, 0 program). Bu sayfalar 200 dönüp "program yok" gösteriyordu: soft 404 riski.
   - Düzeltme: sonuç vermeyen çip gösterilmez. Çip sayıları diğer filtre seçimine göre hesaplanır. Geçerli değerlerin boş kombinasyonu doğrudan açılırsa 404 döner.
2. **Filtre URL hacmi:** filtre çipleri ve filtreli sayfalama, dil başına yaklaşık 5.500 yeni taranabilir URL üretiyordu. Hepsinin canonical'ı filtresiz sayfaya gidiyordu; bu, azaltmak istediğimiz düşük değerli keşfi artırırdı.
   - Düzeltme: filtre çiplerine, "İngilizce programlar" kartına ve **yalnız filtreli** sayfalardaki sayfalama bağlantılarına `rel="nofollow"` eklendi.
   - Kullanıcı için her şey aynı çalışıyor. Filtresiz sayfalama takip edilebilir kaldığı için Google tüm programlara ulaşabiliyor.
   - `nofollow` bir ipucudur; Google bu URL'leri yine de tarayabilir. Tararsa canonical filtresiz sayfaya işaret ediyor.

Canonical / robots / hreflang tutarlılığı (Mainz, yerel):

| URL | Durum | Canonical | Robots | Hreflang (tr/en/de/x-default) |
|---|---|---|---|---|
| filtresiz sayfa 1 | 200 | kendisi | index | filtresiz sayfa 1 |
| `?page=2` | 200 | `?page=2` | index | `?page=2` |
| `?page=17` (son) | 200 | `?page=17` | index | `?page=17` |
| `?degree=master&page=2` | 200 | filtresiz sayfa 1 | index | filtresiz sayfa 1 |
| `?lang=en&page=3` | 200 | filtresiz sayfa 1 | index | filtresiz sayfa 1 |
| `?degree=master&page=9` (son) | 200 | filtresiz sayfa 1 | index | filtresiz sayfa 1 |
| `?page=18`, `?degree=master&page=10` | 404 | — | — | — |
| `?degree=sprachkurs` (Mainz'da yok) | 404 | — | — | — |

Çelişki yok: hiçbir sayfada noindex ile canonical birlikte kullanılmıyor. Hreflang hedefleri her zaman kendine canonical veren sayfalar.

### Yeni URL sayısı (yerel baseline, canlıyla aynı veri)

| Kalem | Dil başına | 3 dil |
|---|---|---|
| Programı olan aktif üniversite | 437 | — |
| Sayfalanan üniversite (>24 program) | 234 | — |
| Filtresiz sayfa toplamı (sayfa 1 dahil) | 1.279 | 3.837 |
| **Yeni, takip edilebilir sayfalama URL'si (`?page=N`, self-canonical)** | **842** | **2.526** |
| Filtre sayfa 1 URL'si (bağlantılı, nofollow, canonical → filtresiz) | 3.745 | 11.235 |
| Filtreli ek sayfa (nofollow, canonical → filtresiz) | 1.778 | 5.334 |
| Sonuç vermeyen kombinasyon (artık bağlantısız, 404) | 308 → 0 | — |

Sonsuzluk kontrolü:
- Sayfa numarası son sayfayla sınırlı; ötesi 404.
- Filtre değerleri doğrulanıyor; geçersiz değer 404.
- Yalnız 2 filtre boyutu var.
- Parametre sırası farkları (`?lang=en&degree=master`) ve bilinmeyen parametreler sayfa tarafından üretilmiyor. Elle yazılırsa 200 döner ve canonical filtresiz sayfaya gider; bu, sitedeki diğer sayfaların bugünkü davranışıyla aynı.

## 9. Mobil erişilebilirlik

- Filtre çipleri ve sayfa düğmeleri en az 40–44 px dokunma alanına sahip ve satır kaydırıyor (`flex-wrap`).
- Gezinti `nav` öğesi `aria-label` taşıyor; geçerli sayfa `aria-current="page"` ile işaretli.
- Kart ızgarası mobilde tek sütun (değişmedi).
- Gerçek cihaz veya Lighthouse testi yapılmadı. Canlı öncesi kontrol listesinde yer alıyor.

## 10. Riskler

- **Hız:** sayfa 2 ve sonrasının taranması zaman alacak. Google sayfalı URL'leri tarayana kadar ince programlara giden bağlantı sayısı azalır ama sıfırlanmaz; bu kasıtlı, çünkü kullanıcı erişimi korunuyor. İnce programları sayfalardan tamamen ayırmak ayrı bir karardır (Roadmap P1-3).
- **Yeni URL'ler:** yaklaşık 3 dil × (toplam sayfa − üniversite sayısı) kadar yeni `?page=N` URL'si taranabilir hale gelir; tahmini birkaç bin. Self-canonical ve gerçek içerik taşıdıkları için kopya değiller. Sitemap'e eklenmediler.
- **Filtre URL'leri:** canonical filtresiz sayfaya gidiyor. Google bunu zaman zaman yok sayıp "Canonical alternatifi" olarak raporlayabilir; bu beklenen bir durum. Gerekirse ileride `noindex,follow` değerlendirilir.
- **Sıralama değişikliği:** kullanıcılar eskiden derece sırasına alışkındı; şimdi anlamlı içerikli programlar önde. Derece filtresi bunu dengeliyor.
- **Sayaç sorguları:** üniversite başına 4 ek `COUNT` sorgusu var; toplam yük, 399 kaydı yüklemekten çok daha düşük.
- **Önbellek:** canlıda view ve config önbelleği deploy sırasında yenileniyor (post-deploy). Ek işlem gerekmiyor.

## 11. Canlıya alma öncesi kontrol listesi

- [ ] Bu rapor ve kod incelendi, onay verildi.
- [ ] Tam test paketi yeşil (pre-push kapısı).
- [ ] Commit yalnız bu dosyaları içeriyor (forum önbelleği, yerel dosyalar ve scratchpad hariç).
- [ ] Canlıda 3 büyük (Mainz, Bonn, TUM) ve 1 küçük üniversite kontrol edildi: 200, ilk HTML ≤ 24 program bağlantısı, sayfa gezintisi gerçek `<a href>`.
- [ ] Canlıda `?page=2`: self-canonical, karşılıklı hreflang; `?page=1` → 301; `?page=999` → 404; filtreli URL'nin canonical'ı filtresiz URL.
- [ ] Canlıda bir büyük üniversitenin tüm sayfaları gezildi; bağlantı birleşimi = program sayısı.
- [ ] Mobil görünüm (gerçek cihaz veya DevTools) ve Lighthouse erişilebilirlik puanı kontrol edildi.
- [ ] GSC başlangıç değerleri kaydedildi: Keşfedildi/Tarandı – dizinde değil, tarama istatistikleri, üniversite sayfaları gösterimi. 4, 8 ve 12. haftalarda tekrar ölçülecek.
- [ ] Geri alma planı: tek commit revert'ü. Veri değişikliği olmadığı için migration geri alması gerekmez.
