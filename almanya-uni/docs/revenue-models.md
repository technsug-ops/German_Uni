# Gelir Modelleri — AlmanyaUni / ApplyToGerman

_Yaşayan belge · İlke: ücretsiz çekirdek korunur; para katmanları değeri BÖLMEZ, EKLER._

Durum kodları: 🟢 canlı · 🟡 altyapı var, gelir yok · 🔴 planlı

---

## 1. 🟡 Affiliate / Lead-Gen — **ana ilk gelir**
**Ne:** Öğrencinin zaten ihtiyaç duyduğu hizmetlere küratörlü dizinler + yönlendirme.
**Nasıl:** Partner dizinleri (admin'den yönetilir) → "Web sitesi" tık takibi (`click_count`/`/go`)
+ "İlgileniyorum" formu (Lead). Gelir: **per-click**, **per-lead**, veya **% komisyon**.
**Dikeyler:**
- Dil kursları (🟢 8 kayıt) · Yeminli tercüme (🟢 4 kayıt) — _bugün kuruldu_
- Bloke hesap (Sperrkonto) · sağlık sigortası (Krankenkasse) · banka (N26/DKB)
- Konut/yurt (housing providers var) · SIM/telefon · uçak · kitap · APS/uni-assist ücret servisleri
**Sıradaki:** gerçek affiliate anlaşmaları + `affiliate_url` doldur + lead → partner aktarımı.

## 2. 🟡 Premium Abonelik (€14/ay vaadi)
**Ne:** Reklamsız + mentor seansı + portal bildirimleri (deadline/burs) + kişisel öneri algoritması
+ öncelikli destek (24s SLA) + Pro tier. (Bkz. premium operations playbook — memory.)
**Nasıl:** Stripe/Paddle + Laravel Notifications + Cal.com/Jitsi mentor.
**Sıradaki:** Premium MVP — önce 2-3 net değer (mentor + bildirim + reklamsız), sonra genişlet.

## 3. 🔴 B2B (Üniversite / Acente / Kurum)
**Ne:** Öne çıkan listeleme, "doğrulanmış profil", recruitment lead'i, anonim trend verisi raporu.
**Nasıl:** Featured flag (var) + B2B paneli + sözleşme. Yüksek bilet, düşük hacim.
**Sıradaki:** trafik otoritesi oluşunca (B2B trafik ister).

## 4. 🟡 Veri / API
**Ne:** Public REST API (var) + FlatReklam SEO API (var). Ücretli tier'lar (rate-limit + premium uçlar).
**Nasıl:** API client + usage log (mevcut). 
**Sıradaki:** ücretli plan tanımı; niş ama yüksek-marj.

## 5. 🟡 Reklam / Sponsorluk
**Ne:** Blog/araç sayfalarındaki "Reklam alanı (yakında)" banner'ları; sponsorlu içerik (etiketli).
**Nasıl:** Doğrudan sponsorluk > programatik (kalite + UX için). Consent Mode v2 hazır.
**Sıradaki:** trafik eşiği sonrası; UX'i bozmadan, az ve alakalı.

## 6. 🟡 Mentor Marketplace Komisyonu
**Ne:** Mentor seansı ücretinden komisyon (mentor sistemi + sessions var).
**Nasıl:** Rezervasyon + ödeme + komisyon kesintisi. Premium ile bağlanabilir.

## 7. 🔴 Kendi Dijital Ürünler
**Ne:** Ücretli derin rehberler (APS/DSH/vize paketi), webinar, şablon paketleri, e-kitap.
**Nasıl:** Content Factory zaten üretebiliyor → premium gated ürünler.

## 8. 🟡 İş İlanları (Academic Job Board)
**Ne:** Akademik/Werkstudent ilanları (JobPosting var) — ücretli ilan + öne çıkarma.

---

## Önceliklendirme & Sıralama
| Sıra | Akış | Neden önce | Ufuk |
|---|---|---|---|
| 1 | Affiliate/lead-gen | altyapı hazır, CAC~0, hızlı ilk € | 30 gün–3 ay |
| 2 | Premium MVP | retention + tahmin edilebilir MRR | 3–6 ay |
| 3 | Mentor komisyonu | premium ile sinerjik | 6 ay |
| 4 | Reklam/sponsorluk | trafik eşiği gerektirir | 6–12 ay |
| 5 | B2B + API + ürünler | otorite/ölçek gerektirir | 12 ay+ |

**Hedef gelir karışımı (3 yıl):** ~%40 affiliate, %35 premium+mentor, %15 B2B/sponsor, %10 API/ürün.

> Kural: Her gelir hamlesi "öğrenciye zarar vermez, güveni artırır" testinden geçmeli.
> Affiliate'te `rel=sponsored`, sponsorlu içerikte etiket, premium'da ücretsiz çekirdek korunur.
