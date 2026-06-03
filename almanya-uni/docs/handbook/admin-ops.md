# 🛠️ Admin & Operasyon Handbook

Sistemi günlük işleten kişi için. Çoğu işlem artık **🛠️ Operasyonlar** dashboard'ında (URL yazmadan).

## Giriş & Yapı
- `/admin` (Filament). Sidebar grupları: İçerik · Akademik Veri · Topluluk · Pazarlama · Kaynaklar · **Sistem** (en sonda).
- **Sistem → 🛠️ Operasyonlar:** tek-tık bakım/içerik işlemleri.
- **Sistem → 📊 Analitik:** trafik (self-hosted, KVKK uyumlu).

## 🛠️ Operasyonlar dashboard (sık işler)
- **📰 Haber Çek** — RSS kaynaklardan aday (gelen kutusu)
- **🔗 İç Linkleri Çöz** — yazılardaki iç linkleri gerçek yazıya bağla / 404'leri düz metne indir
- **🌐 Eksik Çevirileri Tamamla** — TR yazıları EN+DE
- **🎨 Kültür Brief Seed** · **🧹 Cache Temizle** · **🖼️ OG Cache Temizle** · **🗃️ Migration Çalıştır**

## Deploy sonrası rutin
1. Deploy yeşil mi? (GitHub Actions)
2. Migration eklendiyse → **🗃️ Migration Çalıştır** (veya `/admin/ops/migrate?run=1`)
3. Menü/çeviri değiştiyse → **🧹 Cache Temizle**
4. Hızlı duman testi: anasayfa + 1 araç + /admin açılıyor mu

## İçerik yayını (Yayın Merkezi)
- **İçerik → 📡 Yayın Merkezi:** blog + sosyal asset'ler tek tabloda.
  - Blog satırı → **Blog'a Aktar** (yazar + tek tık TR+EN+DE yayın)
  - Sosyal → Paylaş (kopyala+aç) / Otomatik Paylaş (Ayrshare key'liyse) / ✓ Paylaşıldı
  - **Toplu:** çoklu seç → Blog Toplu Yayınla / Sosyal Toplu işaretle
- **Haber:** İçerik → Haber Akışı → İçeriği Çek → AI Taslak → Paylaş. Kaynaklar: Haber Kaynakları.

## Lead yönetimi (gelir!)
- **Topluluk → 📥 Lead'ler** (yeni-lead sayaç rozeti). Form + tık kaynaklı.
- Rutin: yeni lead'leri **contacted/converted** olarak güncelle; haftalık raporla; partnere aktar.

## Partner dizinleri (affiliate)
- **Kaynaklar → 🗣️ Dil Kursları / 📜 Yeminli Tercüme:** ekle/çıkar, logo (URL veya upload), `affiliate_url` (anlaşma olunca), açıklama.
- Tık (click_count) tabloda görünür → hangi partner ilgi çekiyor.

## Menü yönetimi
- **Sistem → Menü Sayfaları:** 28+ öğeyi aç/kapat, grup (Keşfet/Araçlar/Fırsatlar...), sırala. Kaydetince cache otomatik temizlenir.

## Pazarlama entegrasyonları
- **Sistem → Entegrasyonlar:** GA4 ✓ + Search Console ✓ · Ads/GTM/Meta/TikTok (boşsa basılmaz). Consent Mode v2 + çerez onayı.

## Bir şey patlarsa
- `/admin` 500 → genelde **eksik migration**. Önce `/admin/ops/migrate?run=1`.
- Devam ederse → geliştiriciye: log (`laravel-YYYY-MM-DD.log`) + hangi sayfa.
- Deploy FTPS fail → geliştirici boş-retry-commit atar.
