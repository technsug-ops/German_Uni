# SEO Recovery Faz 2A: `/programs` filtre (faceted) URL'leri — sonuç raporu

Tarih: 14 Ekim 2026. Durum: **yalnız yerelde uygulandı ve test edildi. Commit, push ve deploy yok; onay bekliyor.**
Ortam: `almanyauni_baseline` (canlının anonim kopyası). Dayanak: `docs/SEO_RECOVERY_GSC_URL_ANALYSIS_2026-10-14.md`. GSC "Tarandı – dizinde değil" örneklerinin %54'ü `/programs` filtre URL'siydi.

Robots.txt, noindex ve canonical politikası **değiştirilmedi**. Bunlarla ilgili seçenekler §6'da, onayına sunuluyor.

## 1. Başlangıç mimarisi

**Parametreler** (`ProgramController::cleanFilters`):

| Parametre | Geçerli değerler | Önceki davranış (geçersiz değer) |
|---|---|---|
| `q` | serbest metin | — |
| `degree` | bachelor, master, phd, staatsexamen, diplom, magister, other | sessizce yok sayılır → 200, filtresiz içerik (kopya) |
| `language` | de, en, both | aynı |
| `field` | alan slug'ı (eski Türkçe slug → 301) | bilinmeyen slug → 200, 0 sonuç |
| `state` | eyalet slug'ı | bilinmeyen slug → 200, 0 sonuç |
| `uni` | üniversite slug'ı (serbest metin kutusu) | 200, 0 sonuç |
| `has_tr`, `free_only`, `no_app_fee`, `uni_assist`, `deadline_open` | boolean | herhangi bir değer (on/1/true/0) kabul |
| `semester` | winter, summer | sessizce yok sayılır |
| `duration_range` | short, mid, long | sessizce yok sayılır |
| `admission` | zulassungsfrei, oertlich, bundesweit | sessizce yok sayılır |
| `sort` | relevance (varsayılan), name, duration, deadline | sessizce yok sayılır |
| `page` | 1… | son sayfanın ötesi → 200, boş liste (soft 404) |
| `partial` | XHR parçası (async filtre) | — |

Bulgular:
- **Çipler sayfa numarasını taşıyordu.** Hızlı filtre çipleri `request()->fullUrlWithQuery()` ile üretiliyordu. 13. sayfadayken çipe basmak `?…&page=13&degree=master` gibi URL'ler üretiyordu ve bu kombinasyonlar sonuçsuz derin sayfalara gidebiliyordu. GSC örneklerinin %97'si `page` taşıyor; 250'si 10. sayfanın, 106'sı 30. sayfanın ötesinde (en derini 352).
- **Sıra kopyası:** parametre sırası değişince aynı içerik farklı URL'de açılıyordu. Örneklemde 173 URL tutarsız sırada, 36'sı `page=1` taşıyor.
- **Sitemap:** filtreli URL sitemap'te yok; yalnız `/tr|en|de/programs` var.
- **Canonical:** tüm filtreli URL'ler canonical ile `/{locale}/programs`'a bağlı, robots `index`.
- **Robots.txt:** `/programs` için kural yok.
- **Filtre bağlantılarının kaynakları:**
  - Liste sayfasındaki 7 hızlı çip ve 3 kabul türü çipi.
  - JS onchange ile gönderilen form (GET).
  - Sayfalayıcı (`withQueryString`).
  - Başka sayfalardan iç bağlantılar: alan, şehir, keşfet, arama, karşılaştırma, ana sayfa. Bunlar `?field=…`, `?field=…&degree=…`, `?field=…&language=…`, `?language=en`, `?free_only=1`, `?uni=…&free_only=1`, `?q=…` biçiminde.

## 2. URL sınıflandırması

| Sınıf | Örnekler | Karar |
|---|---|---|
| **A. İndekslenmesi gereken SEO landing'leri** | `/programs/field/{f}/degree/{d}`, `/programs/field/{f}/language/{l}`, `/programs/city/{c}/field/{f}`, `/english-taught`, `/tuition-free`, `/nc-free`, alan/şehir/üniversite sayfaları, program detayları | **Dokunulmadı.** Kendi URL'leri, self-canonical, sitemap'te |
| **B. Kullanıcı için faydalı, bağımsız indekslenmesi gerekmeyen filtre sonuçları** | `/programs?field=…&degree=…`, `?language=en`, `?q=…`, çip kombinasyonları ve sayfaları | Çalışmaya devam ediyor. Canonical `/programs` (değişmedi). Artık kanonik sırada ve sayfasız çip bağlantılarıyla |
| **C. Kopya, geçersiz, boş, gereksiz** | sıra kopyaları, `page=1`, boş değerler, `sort=relevance`, `has_tr=0`, geçersiz enum, bilinmeyen slug, dizi parametre, sayısal olmayan veya aralık dışı `page` | **Normalizasyon 301 ya da 404** (§3) |

## 3. Yöntem karşılaştırması ve seçim

| Yöntem | Etki | Risk | Karar |
|---|---|---|---|
| Canonical konsolidasyonu | Zaten var (`/programs`). Google bunu ipucu olarak alıyor ve URL'leri yine tarıyor (GSC'de "Tarandı") | Tek başına tarama azaltmaz | Korundu, değiştirilmedi |
| **Filtre bağlantılarının üretimini sınırlama** | Çipler artık sayfasız ve kanonik sırada. Derin sayfa × filtre kombinasyonları bağlantı almıyor | Düşük: kullanıcı filtreyi değiştirince 1. sayfadan başlar (beklenen davranış) | **Uygulandı** |
| **Parametre normalizasyonu** | Boş, varsayılan, karışık sıra ve eski slug → tek 301 ile kanonik URL. İzleme parametreleri (`utm_*`) korunur. XHR yönlendirilmez | Düşük: iç bağlantılar zaten kanonik sırada (sıra buna göre seçildi); kodlama farkı (+/%20) yönlendirme sebebi değil | **Uygulandı** |
| **Boş/geçersiz filtrelerde 404** | Geçersiz enum, bilinmeyen field/state, dizi parametre, geçersiz veya aralık dışı sayfa → 404 | Düşük: geçerli değerlerin sonuçsuz kombinasyonu 200 kalıyor (kullanıcı araması); `q` ve `uni` serbest metin, doğrulanmıyor | **Uygulandı** |
| Seçici robots.txt disallow | Taramayı gerçekten durdurur. Ancak engellenen sayfada canonical ve noindex okunamaz; dışarıdan bağlantı alan URL'ler "Dizine eklendi ama robots.txt engelledi" olabilir | Orta | **Uygulanmadı** — §6, onay gerekli |
| noindex,follow (filtreli sayfalar) | Dizin dışı sinyali; tarama bir süre devam eder, zamanla azalır | Orta: politika değişikliği | **Uygulanmadı** — §6, onay gerekli |
| nofollow (filtre bağlantıları) | Tarama ipucu; Faz 1'deki üniversite filtrelerinde kullanıldı | Düşük, ama yalnız ipucu | **Varsayılan seçilmedi** — §6'da seçenek |

## 4. Kod değişiklikleri

| Dosya | Değişiklik |
|---|---|
| `app/Http/Controllers/Web/ProgramController.php` | `FACET_ORDER` kanonik sıra (sitenin iç bağlantılarıyla aynı: `q, field, degree, language, …`); `facetQuery()`, `facetUrl()`, `unknownParams()`, `abortOnInvalidFacets()`; tek 301 normalizasyon (eski field slug dahil, zincir yok); aralık dışı sayfa 404; sayfalayıcı kanonik filtreleri taşır (`appends`) |
| `resources/views/programs/index.blade.php` | 7 hızlı çip ve 4 kabul türü bağlantısı `request()->fullUrlWithQuery()` yerine `$facetUrl()`: sayfa sıfırlanır, kanonik sıra |
| `app/Support/CanonicalPageOnePaginator.php` | Yeni: 1. sayfa bağlantısını `?page=1` olmadan üretir. Laravel'in standart sayfalayıcısı `?page=1` verir; bu adres artık 301 aldığından 2. sayfadaki "önceki" ve "1" bağlantıları fazladan yönlendirmeden geçerdi (son kontrolde bulundu, §7) |
| `resources/views/programs/_grid.blade.php` | Sayfalama görünümü açıkça `pagination::tailwind` (§7 kök neden) |
| `tests/Feature/ProgramListingFacetTest.php` | Yeni: 8 test |
| `tests/Feature/SeoGrowthBatch3Test.php` | Bir istek URL'si kanonik sıraya çevrildi (§7) |

Migration, veri, sitemap, robots.txt, noindex ve canonical değişikliği yok.

## 5. Başlangıç / sonuç envanteri (GSC "Tarandı" örneklemindeki 540 filtre URL'si, yerel)

Ölçüm `scripts/seo-audit/gsc_url_local_check.php` ve yönlendirme izleyen sürümüyle yapıldı. Her istek transaction içinde geri alındı; izlenen tablolarda yazma yok.

| Durum | Önce | Sonra (ilk yanıt) | Sonra (son durak) |
|---|---|---|---|
| 200 | 255 (17'si boş liste) | 40 | 462 (**0 boş liste**) |
| 301 | 284 (eski Türkçe field slug) | 499 | — |
| 302 | 1 (dil öneksiz) | 1 | — |
| 404 | 0 | 0 | **78** (son sayfanın ötesi) |

Sonra'daki 301 nedenleri: 284 eski field slug (artık sıra düzeltmesiyle **aynı** adımda), 200 parametre sırası, 15 `page=1`.

Yönlendirme zinciri:
- 421 URL: tek 301 → 200.
- 78 URL: tek 301 → 404.
- 40 URL: doğrudan 200.
- 1 URL: 302 → 301 → 200. Bu dil öneksiz URL; dil normalizasyonu ayrı bir konu (Roadmap P0-3).

Son durakta 460 farklı 200 URL'si kalıyor; hepsi canonical `/programs`, hiçbiri boş değil.

404'e düşen 78 URL'nin sınıflandırması (son kontrol, yerel baseline, istekler geri alındı):

| Sınıf | Adet |
|---|---|
| Son sayfanın ötesi: sayfasız sürüm 200 ve program içeriyor, son geçerli sayfa 200 | **78** |
| Geçersiz filtre değeri | 0 |
| Sonuçsuz filtre kombinasyonu | 0 |
| İncelenmesi gereken (geçerli sonuçlu URL'nin 404 olması) | **0** |

- Hiçbir geçerli program sonucu 404'e düşmedi; 404 yalnız sonuç sayısını aşan `page` değerinde.
- 78 URL'nin 61'i eski field slug'ı taşıyordu (`matematik-doga`, `bilisim`). Slug önce doğru İngilizce karşılığına çevriliyor, sonra sayfa aralığı kontrol ediliyor.
- 540 URL'deki tüm eski field slug'ları doğru hedefe gidiyor: `bilisim` → `computer-science`, `matematik-doga` → `mathematics-natural-sciences`, `muhendislik` → `engineering`, `tarim-ormancilik` → `agriculture-forestry-nutrition`.
- İstenen sayfa son sayfanın çoğunlukla 1–3 katı. Bu URL'ler Google'ın çip × sayfa bağlantılarından keşfettiği derin kombinasyonlar; bugün canlıda boş liste (soft-404) gösteriyorlar.

Bağlantı üretimi:
- **Önce:** çip × sayfa kombinasyonu sınırsız sayıda URL'ye bağlantı veriyordu (her sayfadaki her çip, o sayfanın numarasını taşıyordu).
- **Sonra:** çipler yalnız sayfa 1 kombinasyonlarına bağlanıyor. Teorik üst sınır, dil başına hızlı çip ve kabul türü kombinasyonları için birkaç yüz sayfa-1 URL'si.
- Bu kombinasyonların ötesindeki sayfalar yalnız sayfalayıcıdan erişilebilir ve sonuç sayısıyla sınırlı.

Korunan URL'ler ve davranışlar:
- Tüm A sınıfı landing'ler.
- Program detayları.
- Sitenin iç bağlantılarındaki filtre URL'leri (yönlendirmesiz 200, test edildi): `?field=…`, `?field=…&degree=…`, `?field=…&language=…`, `?language=en`, `?free_only=1`, `?uni=…&free_only=1`, `?q=…`.
- Async filtre (XHR) yönlendirilmiyor.

Kullanıcıya açık filtreler: hepsi aynen çalışıyor. Değişen tek davranış: filtre değişince liste 1. sayfaya dönüyor.

## 6. Onayına sunulan ek seçenekler (uygulanmadı)

Normalizasyondan sonra kalan risk şu: geçerli filtre kombinasyonlarının 2. ve sonraki sayfaları (sayfalayıcı üzerinden) taranabilir kalıyor ve canonical ile `/programs`'a bağlanıyor. Bunu daha da azaltmak için üç seçenek var; birini seçmeni öneririm:

1. **Filtreli sayfalama ve çip bağlantılarına `rel="nofollow"`.** Faz 1'deki üniversite filtreleriyle aynı yaklaşım. En düşük risk, ama yalnız ipucu.
2. **Filtreli URL'lere `noindex,follow`, canonical kaldırılarak.** Google noindex'i görmek için taramaya devam eder; dizin dışı kalma netleşir, tarama zamanla azalır. Politika değişikliği olduğu için onayın gerekli.
3. **Seçici robots.txt** (ör. iki ve daha fazla filtre + `page` içeren `/*/programs?` desenleri). Taramayı en hızlı keser. Ancak canonical okunamaz, dış bağlantı alan URL'ler dizinde "engellendi" olarak kalabilir ve desen hatası değerli URL'leri kapatabilir. Önerilmez; en son seçenek.

Önerim: önce bu paketi (normalizasyon + 404 + çip düzeltmesi) yayınlayıp GSC'de 4 hafta izlemek. "Tarandı" örneklerinde filtre payı düşmezse 1. seçeneği eklemek.

## 7. Test sonuçları

| Test | Sonuç |
|---|---|
| Yeni `ProgramListingFacetTest` (8 test) | 8/8 |
| Tam test paketi | **565/565 geçti** (39.029 assertion) |
| i18n sızıntı denetimi | TR 0, DE 0 |

Mevcut testte değişiklik: `SeoGrowthBatch3Test` liste isteğini `?language=en&degree=master&q=B3` sırasıyla atıyordu. Site bu sırayı hiçbir yerde üretmiyor; yeni davranışta bu istek tek 301 ile kanonik sıraya gidiyor (kasıtlı). Testin URL'si kanonik sıraya (`?q=B3&degree=master&language=en`) çevrildi; içerik kontrolleri aynen kaldı.

Sayfalama render sorunu, kök neden (son kontrol):
- **Belirti:** tam pakette, admin testlerinden sonra liste sayfası `<a href>` sayfa bağlantısı içermiyordu.
- **Kök neden:** Livewire, sayfalamalı bir bileşeni (Filament tablosu) başlatırken statik `Paginator::$defaultView` değerini kendi `livewire::tailwind` görünümüne çeviriyor. Bu görünüm `href` yerine `wire:click` butonları üretir. Geri alma yalnız bileşen yok edilince çalışıyor. PHPUnit tüm testleri tek süreçte koştuğu için değer sonraki testlere sızıyordu.
- **Yeniden üretme:** `DuplicateContentCleanupTest`, `FaqQualityAtlasDashboardTest`, `FieldRankingScoreTest`, `ProgramVerificationLayerTest` ve ardından `ProgramListingFacetTest` birlikte koşulunca hata çıktı. Hata HTML'inde `wire:click="gotoPage(2, 'page')"` görüldü.
- **Üretim etkisi:** her istek ayrı PHP süreci olduğu için bugün beklenmez. Yine de aynı istekte bir Livewire bileşeni sayfalama görünümünü değiştirirse liste etkilenirdi.
- **Düzeltme:** liste şablonu görünümü açıkça veriyor (`links('pagination::tailwind')`). Bu hem testi hem üretimi statik duruma bağımsız kılar. Fixture sorunu değil.
- **Test:** zayıflatılmadı, güçlendirildi. Test sızıntıyı bilerek taklit ediyor (`Paginator::defaultView('livewire::tailwind')`), sonra gerçek HTML'de şunları doğruluyor: filtreli 1. sayfada `?degree=bachelor&page=2` bağlantısı var, `wire:click` yok, 2. sayfanın "1/önceki" bağlantısı `?page=1` taşımıyor. Filtresiz listede yalnız HTML'deki bağlantılar izlenerek 45 programın hepsine ulaşılıyor. `tearDown` varsayılan görünümü geri yüklüyor.
- **Mutasyon kontrolü:** şablondaki düzeltme geçici olarak geri alınınca test düştü; geri konunca geçti.
- **Yan bulgu:** bu güçlendirilmiş test, 1. sayfa bağlantısının `?page=1` ile üretildiğini ve 301'e düştüğünü ortaya çıkardı. `CanonicalPageOnePaginator` ile düzeltildi (§4).

`ProgramListingFacetTest` kapsamı:
1. Normal liste ve geçerli filtreler: canonical `/programs`, robots index; TR/EN/DE.
2. Normalizasyon: sıra, `page=1`, `sort=relevance`, boş değerler, `has_tr=0`, `on` → `1`, `utm_*` korunur, zaten kanonik URL yönlendirilmez.
3. Eski field slug + karışık sıra → tek 301, hedef doğrudan 200.
4. XHR (`partial=1` ve `X-Requested-With`) yönlendirilmez.
5. Geçersiz değerler: enum dışı, bilinmeyen field/state, `degree[]`, `page=0/abc`, aralık dışı sayfa (filtreli ve filtresiz) → 404.
6. Çipler sayfa taşımaz ve kanonik sırada.
7. Sayfalama gerçek HTML'de `<a href>` olarak render edilir (Livewire görünüm sızıntısı taklit edilse de), 1. sayfa bağlantısı `?page=1` taşımaz, HTML bağlantıları izlenerek tüm programlara erişilir.
8. Program detayı ve SEO landing'leri etkilenmez.

## 8. Riskler

- **Yeni 404'ler:** derin sayfa ve geçersiz parametre URL'leri artık 404 dönüyor (örneklemde 78). Bunlar zaten boş ya da kopya içerikti. GSC'de "Bulunamadı (404)" sayısı geçici olarak artabilir; bu beklenen ve zararsız bir durum.
- **Yeni 301'ler:** sıra ve eski slug normalizasyonu. GSC'de "Yönlendirmeli sayfa" geçici olarak artabilir.
- **Dış bağlantılar:** geçersiz değerli eski URL'ler artık 404 dönüyor. Geçerli olanlar tek 301 ile korunuyor.
- **Serbest metin:** `q` ve `uni` serbest metin olduğu için sonuçsuz arama 200 kalıyor. Kullanıcı deneyimi gereği soft-404 riski bu iki alanda sürüyor; canonical `/programs`.
- **Etki süresi:** tarama davranışının değişmesi haftalar sürer. Bu değişiklik tek başına indeksleme artışı garantisi vermez.

## 9. Canlıya alma öncesi kontrol listesi

- [x] Rapor ve kod onaylandı (14.10.2026); §6 ertelendi (4 hafta GSC izleme).
- [x] Son kontrol: sayfalama kök nedeni bulundu ve düzeltildi; 78 404'ün hepsi son sayfanın ötesi.
- [ ] Tam test paketi yeşil (pre-push).
- [ ] Canlıda kontrol:
  - Sitenin iç bağlantı biçimleri 200 dönüyor (§5 listesi).
  - Karışık sıra → tek 301.
  - `page=1` → 301.
  - Aralık dışı sayfa ve geçersiz değer → 404.
  - Async filtre çalışıyor; tarayıcıda form ve çip tıklamaları kontrol edildi.
  - Liste sayfasında sayfalama bağlantıları render ediliyor ve kanonik (ör. `?degree=bachelor&page=2`).
- [ ] GSC başlangıç değerleri kaydedildi: "Tarandı – dizinde değil" (6.805), "Canonical alternatifi" (9.134), "Bulunamadı" (64), "Yönlendirmeli sayfa" (163).
- [ ] Geri alma: tek commit revert'ü; veri değişikliği yok.
