# Almanya Öğrenci İstatistikleri Raporu 2026 — web yayını verisi

Kaynak paket: `ApplyToGerman_Istatistik_Yayin_Paketi_TR.zip` (teslim 2026-10-11). Zip'in kendisi ulaşılamadı; önce
`ApplyToGerman_Ogrenci_Istatistikleri_2026_TR.html` (önizleme; metin, üç grafik ve PDF base64 gömülü) ve `İstatistik Rapor.pdf`
(md5 `028c3216ec94a1df0c704086ab7cb42d`, gömülü PDF ile aynı) kullanıldı. Sonradan teslim edilen esas
`ApplyToGerman_Ogrenci_Istatistikleri_2026_TR.md`, `yayin_alanlari.json` ve `OKU_BENI.txt` ile karşılaştırıldı: TR metin,
onaylı dönüşümler (H1/yazar satırı şablonda, `[^n]` dipnot, PDF sayfa bağlantısı, medya yolu, iki iç bağlantı satırı)
dışında birebir aynı; title/meta_title/meta_description JSON ile aynı; excerpt JSON'dan alındı (EN/DE aynı anlamla).
JSON'daki `proposed_slug` (almanya-ogrenci-istatistikleri-2026) kullanılmadı; repo slug kuralı esas alındı.

- `report.tr.md` — esas Türkçe metin (HTML'den aktarıldı; H1 şablondan basılır, yazar satırı şablonda; dipnotlar `[^n]`).
- `report.en.md`, `report.de.md` — aynı bölüm/tablo kapsamı, aynı sayılar (binlik/ondalık gösterimi dile göre).
- `posts.json` — slug, başlık, meta, excerpt, çeviri grubu, yazar, rehber atıfları.
- Yayın: `database/migrations/2026_10_11_000300_publish_student_statistics_report_2026.php` (yayın sahibi onayı 2026-10-11; published_at = deploy anı; rehber bağlantıları aynı transaction'da).
- Medya: `public/reports/almanya-ogrenci-istatistikleri-2026/` (PDF + 3 PNG; deploy paketiyle gider, prod medya alanına yazılmaz).

Taslak önizleme (yayın öncesi): admin/editör oturumuyla `/{tr,en,de}/blog/{slug}` (noindex, listelerde ve sitemap'te yok).
