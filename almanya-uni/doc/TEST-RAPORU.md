# 🧪 AlmanyaUni — Test Raporu

**Tarih:** 7 Haziran 2026
**Çalıştırma:** `php artisan test`
**Sonuç:** ✅ **84 test / 221 assertion — HEPSİ YEŞİL** (~31 sn)
**Ortam:** Laravel 13.8 · PHP 8.3 · PHPUnit 12.5

> 🆕 Bu oturumda kapsam boşlukları kapatıldı: güvenlik-kritik 4 alan için 23 yeni test
> (HMAC captcha, i18n strict-mode, arama loglama/DoS-kırpma, login + API rate-limit).

Bu rapor iki bölümden oluşur:
1. **Otomatik test paketi** (`php artisan test` ile her seferinde koşan 61 test)
2. **Yük / stres testi rehberi** (k6 ile elle koşulan kırılma noktası testi)

---

## 1. Otomatik Test Paketi (84 test)

Testler 13 grupta toplanır. Aşağıda her grubun **ne işe yaradığı** sade dille yazılmıştır.

### 📊 Özet tablo

| Grup | Test | Ne korur? |
|---|---:|---|
| 🔎 FAQ Çıkarıcı (Unit) | 13 | Blog içinden otomatik SSS üretiminin güvenliği & doğruluğu |
| 🔌 API Yetkilendirme | 6 | Veri API'sinin izinsiz erişime kapalı olması |
| 🔐 Kimlik Doğrulama (Auth) | 18 | Giriş / kayıt / şifre / e-posta akışları |
| 📝 Blog HTML Render | 4 | Blog içeriğinin bozulmadan kaydedilmesi |
| ✉️ İletişim Formu | 4 | `/contact` formunun çalışması ve doğrulaması |
| 👤 Profil | 5 | Kullanıcı profil güncelleme & hesap silme |
| 💨 Smoke (Duman) Testi | 9 | Sitenin temel sayfaları ve kritik güvenlik kapıları |
| 🧩 Örnek (iskelet) | 2 | Laravel kurulum sağlamlık kontrolü |
| 🛡️ **HMAC Captcha** 🆕 | 9 | Captcha token sahteciliği / süre dolması / replay reddi |
| 🌐 **i18n Strict Mode** 🆕 | 7 | TR içeriğin /en /de sayfalarına sızmaması |
| 🔍 **Arama Loglama/DoS** 🆕 | 4 | 100-karakter kırpma + bot/kısa-sorgu log filtresi |
| 🚧 **Rate-Limit** 🆕 | 3 | Login 5-deneme kilidi + API 429 akışı |
| **TOPLAM** | **84** | |

---

### 🔎 FAQ Çıkarıcı — 13 test

Blog yazılarının içindeki **soru başlıklarını otomatik SSS'ye** dönüştüren kodu test eder. Burası kullanıcıya görünen içerik ürettiği için **hem doğruluk hem güvenlik** açısından kritiktir.

| Test | Ne doğrular |
|---|---|
| İki soru başlığından iki FAQ | Normal akış: 2 soru → 2 SSS |
| H3 başlıkları da çalışır | Sadece H2 değil, H3 başlıkları da yakalanır |
| Tam genişlik `？` kabul edilir | Japon/Çin klavyesi karakteri de soru sayılır |
| Soru işareti olmayan başlık atlanır | Düz başlık SSS'ye girmez |
| Çok kısa cevap atlanır | Anlamsız tek-kelime cevaplar elenir |
| Null girdi → boş dizi | Kod boş veriyle çökmez |
| Boş string → boş dizi | Aynı, boş metinle çökmez |
| Başlıksız HTML → boş | Başlık yoksa hiç SSS üretilmez |
| Çok uzun cevap 700'e kırpılır | Devasa cevaplar makul boyuta indirilir |
| Son başlık EOF'a kadar okunur | Yazının son sorusu da tam alınır |
| İç etiketler metne indirgenir | `<b>`, `<a>` gibi etiketler temizlenir |
| HTML entity'leri çözülür | `&amp;` → `&` düzgün gösterilir |
| **`<script>` cevaba sızmaz** | **🛡️ XSS koruması — zararlı kod enjekte edilemez** |

---

### 🔌 API Yetkilendirme — 6 test

Dış dünyaya açık **veri API'sinin** yalnızca geçerli token'la erişilebildiğini garanti eder. (Self-registration kapatıldığından erişim sadece verilen token'larla mümkün.)

| Test | Ne doğrular |
|---|---|
| Token olmadan → 401 | Token'sız istek reddedilir |
| Geçersiz token → 401 | Uydurma token reddedilir |
| Free client referans verisini okur | Ücretsiz token salt-okunur veriye erişir |
| Free client webhook yönetemez → 403 | Ücretsiz token yazma/webhook'a giremez |
| Partner client webhook'a erişir | Partner token'ı webhook yönetebilir |
| User token tüm uçlara erişir | `*` yetkili token her şeye erişir |

---

### 🔐 Kimlik Doğrulama (Auth) — 18 test

Laravel Breeze tabanlı **giriş, kayıt, şifre ve e-posta** akışlarının tamamı.

- **Giriş (4):** giriş ekranı açılır · doğru şifreyle giriş · yanlış şifre reddedilir · çıkış yapılır
- **E-posta Doğrulama (3):** doğrulama ekranı açılır · e-posta doğrulanır · geçersiz hash reddedilir
- **Şifre Onayı (3):** onay ekranı açılır · şifre onaylanır · yanlış şifre reddedilir
- **Şifre Sıfırlama (4):** link ekranı açılır · sıfırlama linki istenir · sıfırlama ekranı açılır · geçerli token'la şifre sıfırlanır
- **Şifre Güncelleme (2):** şifre güncellenir · güncellerken mevcut şifre doğru olmalı
- **Kayıt (2):** kayıt ekranı açılır · yeni kullanıcı kaydolur

---

### 📝 Blog HTML Render — 4 test

Blog içeriğinin Markdown→HTML dönüşümünde **bozulmadan kaydedildiğini** doğrular.

| Test | Ne doğrular |
|---|---|
| Boş `content_html` backfill edilir | Eksik HTML otomatik üretilir |
| Dolu `content_html` korunur | Var olan HTML üzerine yazılmaz |
| `--dry-run` değişiklik yapmaz | Prova modu veriyi ellemez |
| `--id` filtresi sadece hedef postu işler | Tek yazı işlenirken diğerleri izole kalır |

---

### ✉️ İletişim Formu — 4 test

`/contact` formunun çalıştığını ve **doğrulama kurallarını** test eder.

| Test | Ne doğrular |
|---|---|
| Form render olur | Sayfa açılır |
| Gönderim feedback kaydı oluşturur | Mesaj DB'ye düşer |
| İsim opsiyonel | İsim boş bırakılsa da çalışır |
| E-posta + mesaj zorunlu | Eksik alan reddedilir |

---

### 👤 Profil — 5 test

- Profil sayfası gösterilir
- Profil bilgileri güncellenir
- E-posta değişmediyse doğrulama durumu korunur
- Kullanıcı kendi hesabını silebilir
- Hesap silmek için doğru şifre gerekir

---

### 💨 Smoke (Duman) Testi — 9 test

Deploy öncesi **"site ayakta mı, kritik kapılar kapalı mı"** kontrolü. Render-500 hatalarını yakalayan ilk savunma hattı.

| Test | Ne doğrular |
|---|---|
| Public sayfalar render olur | Ana sayfa vb. 200 döner |
| Veri API'si auth ister | Token'sız erişim kapalı |
| **API self-registration kapalı** | 🛡️ Kendi kendine token üretilemez |
| Geçerli token'la API yanıt verir | Doğru token çalışır |
| **Aşırı uzun arama sorgusu** | 🛡️ DoS savunması — 100 karakter sınırı |
| Kavram/eş anlamlı arama aracı bulur | Akıllı arama çalışıyor |
| Flatreklam token ister | Reklam ucu korumalı |
| Haber modülü aç/kapatılabilir | Menü toggle çalışıyor |
| Admin sayfaları admin'e render olur | Panel erişimi sağlam |

---

### 🧩 Örnek / İskelet — 2 test

Laravel kurulumunun sağlam olduğunu doğrulayan standart iskelet testleri (`true is true`, ana sayfa 200 döner).

---

### 🛡️ HMAC Captcha — 9 test 🆕

[app/Support/MathCaptcha.php](../app/Support/MathCaptcha.php) — stateless (session'sız, HMAC-imzalı) captcha'nın çekirdeği. Login / kayıt / şifre-sıfırlama formlarını bota karşı korur. Daha önce **sıfır birebir testi** vardı.

| Test | Ne doğrular |
|---|---|
| Doğru cevapla doğrulanır | Normal akış geçer |
| Yanlış cevap reddedilir | Bilinçsiz/yanlış cevap geçmez |
| Null ve boş anahtar reddedilir | Bozuk istek çökmeden reddedilir |
| Base64 olmayan çöp reddedilir | Geçersiz token formatı elenir |
| Eksik parçalı payload reddedilir | `answer\|expires\|sig` yapısı zorunlu |
| Rakam olmayan cevap reddedilir | Tip güvenliği |
| **Kurcalanmış imza reddedilir** | 🔐 HMAC sahteciliği constant-time `hash_equals` ile durdurulur |
| **Süresi geçmiş token reddedilir** | 🔐 Doğru cevap olsa bile TTL geçtiyse replay kapanır |
| Başka token üzerinden cevap aktarımı olmaz | İmza payload'a bağlı — çapraz kullanılamaz |

---

### 🌐 i18n Strict Mode — 7 test 🆕

[app/Models/Concerns/LocalizableContent.php](../app/Models/Concerns/LocalizableContent.php) — çok dilli içeriğin fallback zinciri. **Kritik kural:** /en ve /de sayfalarında Türkçe içerik sızmamalı (serbest-metin alanlarda `strict` mod).

| Test | Ne doğrular |
|---|---|
| Aktif dil kolonunu döner | de locale → Almanca kolon |
| **Strict modunda TR yabancı dile sızmaz** | 🌐 EN sayfada sadece TR varsa → null (gizlenir) |
| DE strict TR yerine EN'e düşer | TR atlanır, anlamlı dile (EN) düşülür |
| Strict olmayan ad TR'ye düşebilir | İsim/kimlik alanı boş kalmasın diye TR'ye düşer |
| Strict aktif dil doluysa onu kullanır | EN doluysa EN gösterilir |
| Localized kolon dilinde TR kalır | /tr sayfasında TR doğal → korunur |
| Localized kolon yoksa ham `name` döner | Polimorfik kullanım güvenli |

---

### 🔍 Arama Loglama & DoS Kırpma — 4 test 🆕

[SearchController.php](../app/Http/Controllers/Web/SearchController.php) — önceden sadece "200 döndü" smoke testi vardı; artık davranış semantik olarak doğrulanıyor.

| Test | Ne doğrular |
|---|---|
| **Uzun sorgu logda 100 karaktere kırpılır** | 🛡️ DoS kırpması gerçekten uygulanıyor (loglanan değerden) |
| Bot user-agent loglanmaz | Googlebot vb. arama logunu kirletmez |
| Çok kısa sorgu loglanmaz | < 2 karakter loglanmaz |
| Normal sorgu breakdown ile loglanır | Tür kırılımı + sonuç sayısı doğru kaydedilir |

---

### 🚧 Rate-Limit — 3 test 🆕

Brute-force / suistimal savunması — önceden sadece yetki (ability) test ediliyordu, gerçek limit aşımı değil.

| Test | Ne doğrular |
|---|---|
| **5 başarısız giriş → doğru şifre bile kilitli** | 🔐 [LoginRequest](../app/Http/Requests/Auth/LoginRequest.php) per-email throttle çalışıyor |
| Farklı e-posta kilitten etkilenmez | Throttle anahtarı `email\|ip` — global değil |
| **API dakikalık limit aşılınca 429** | 🚧 [ApiThrottleAndLog](../app/Http/Middleware/ApiThrottleAndLog.php) 429 + `Retry-After` header döner |

---

## 2. Yük / Stres Testi Rehberi (k6)

Otomatik paketten **ayrı**, elle koşulan bir testtir. Amaç: günlük binlerce ziyaretçide altyapının **kırılma noktasını (breakpoint)** bulmak.
Script: [`tests/load/k6-stress.js`](../tests/load/k6-stress.js) · Rehber: [`tests/load/README.md`](../tests/load/README.md)

### Nasıl çalıştırılır

```bash
# 1) Önce düşük yükle duman testi
k6 run -e BASE_URL=https://applytogerman.com -e SCENARIO=smoke tests/load/k6-stress.js

# 2) Kademeli artış (0→50→100→300→500 eşzamanlı kullanıcı)
k6 run -e BASE_URL=https://applytogerman.com tests/load/k6-stress.js
```

> ⚠️ **Sadece kendi sunucuna** çalıştır. KAS paylaşımlı hosting → düşükten başla, kademeli artır, az-trafik saatinde test et. İdeali staging kopyası.

### Test edilen 3 kritik akış
1. **Ana sayfa** (`/tr`)
2. **Arama** (`/tr/search?q=...`) — en pahalı yol (~19 FULLTEXT/LIKE sorgusu)
3. **Liste** sayfaları (üniversiteler · programlar · blog)

### Otomatik durma (breakpoint) eşikleri

| Metrik | Eşik | Anlamı |
|---|---|---|
| `http_req_duration` p95 | **> 3000 ms** | Kabul edilemez gecikme |
| Hata oranı (5xx + bağlantı) | **> %5** | Sistem kırılma noktasında |

Bu eşiklerin **ilk aşıldığı eşzamanlı kullanıcı sayısı = kırılma noktasıdır.**

### Test sırasında izlenecekler
- **MySQL** (en olası darboğaz): `Threads_connected` → `max_connections`'a yaklaşıyor mu? Yavaş sorgu var mı?
- **CPU:** sürekli %85+ → PHP-FPM darboğazı
- **RAM:** swap'a giriyor mu?
- **PHP-FPM:** 502/504 → `pm.max_children` tükendi
- **Bu projeye özel:** rate-limit `file` cache driver yüksek eşzamanlılıkta atomik değil → prod'da **Redis** öner.

### Çökerse — ilk müdahale öncelikleri
1. MySQL `max_connections` yükselt
2. PHP-FPM `pm.max_children` RAM'e göre ayarla
3. **Sayfa cache** (CDN/Cloudflare full-page) — en büyük kazanç
4. **Redis** → cache + rate-limit + session
5. Arama ağırsa sonuçları kısa süreli cache'le

---

## ✅ Sonuç

- **Otomatik paket:** 84/84 yeşil, 221 assertion. Her `git push` öncesi koşuluyor (CI gate aktif).
- **Güvenlik kapıları test altında:** XSS (`<script>` sızıntısı), API yetkilendirme, self-registration kapalı, arama DoS sınırı, **HMAC captcha sahteciliği, i18n TR-sızıntısı, login + API rate-limit**.
- **Yük testi:** script + rehber hazır, kullanıcı kendi sunucusunda kademeli koşacak.

> Bu raporu yenilemek için: `php artisan test` çalıştır, çıkan sayıyı bu dosyanın başındaki özetle güncelle.
