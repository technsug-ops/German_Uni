# OSV Paket 2 — Canlı Chatbot Kapanış Testi

Tarih: 15 Ekim 2026.

Kapsam ve sınırlar:
- Canlı chatbot'a (`POST /{dil}/chat`, widget'ın kullandığı uç nokta) yalnızca soru gönderildi. Bu uç nokta veritabanına kayıt yazmıyor; geri bildirim ve lead uç noktaları çağrılmadı.
- Production DB'ye ve KB'ye yazma yok; commit, push ve deploy yok.
- Ham sonuçlar oturum scratchpad'inde: `chat_result_pkg2.json`.

## 1. KB senkronizasyonu

Kullanıcının paylaştığı iki çalıştırma:

| Çalıştırma | Embed edilen | Değişmeyen | kb_chunks toplam |
|---|---|---|---|
| 1 | 534 | 6.699 | 49.670 |
| 2 | 0 | 7.233 | 49.670 |

Değerlendirme: **1. çalıştırma gerçek çalıştırmaydı; KB canlı içerikle senkron.**
- `kb:embed` "kb_chunks toplam" satırını yalnız gerçek çalıştırmada basar. Dry-run çıktısında bu satır yok ("PLAN"). Kullanıcı ilk sonuçta toplam değerini bildirdi.
- 2. çalıştırmada 0 embed ve 7.233 = 534 + 6.699 değişmeyen. İlk çalıştırmada gömülenler hash olarak güncel. İlk çalıştırma dry-run olsaydı, ikincisi yine ≈534 gösterirdi.
- Toplam 49.661'den (14.10, Paket 1 sonrası) 49.670'e çıktı (+9). Yeniden yazılan uzun yazılar biraz daha fazla parçaya bölündü; beklenen bir fark.
- 534, beklenen aralıkta: Paket 2 ≈470 + Paket 1b'nin henüz gömülmemiş kısmı. Eşik (700) aşılmadı.

Kalan belirsizlik: "PLAN" ya da "TAMAM" başlık satırı ilk çıktıda görülmedi. Yukarıdaki iki bağımsız kanıt gerçek çalıştırmayı gösteriyor.

## 2. Özet

| Set | Soru | Doğru | Kısmen doğru | Yanlış |
|---|---|---|---|---|
| Önceki 14 OSV senaryosu (C01–C14, aynen) | 14 | **14** | 0 | 0 |
| Yeni 10 konu × TR/EN/DE (N01–N10) | 30 | **23** | 7 | 0 |
| **Toplam** | **44** | **37** | **7** | **0** |

- **Yasaklı ifade taraması** (44 cevabın tamamı): şu kalıpların hiçbiri bulunmadı (**0 isabet**):
  - kontrol tarihinden "itibaren / since / seit"
  - 30 €, 32,81, 45.300, 48.300, 43.759, 56.400
  - güncel kural olarak "120 gün"
  - "6 ay + çalışma yasak"
  - "iDATA üzerinden başvurulur"
  - "genellikle APS"
- **Yanlış bilgi yok.** Kısmen doğru olan 7 cevabın sebepleri:
  - Kaynak metninde resmî kaynakla doğrulanmamış tarih ifadeleri var: "01.09.2024'ten beri", "Stand September 2026", "seit 01.03.2024".
  - Eksik bilgi: 75 € vize harcı söylenmiyor.
  - Gereksiz yumuşatma: "approximately".

## 3. Önceki 14 OSV senaryosu (aynen tekrar)

| # | Dil | Konu | Sonuç | Not |
|---|---|---|---|---|
| C01–C03 | TR/EN/DE | Türkiye diploması → APS | Doğru | "APS gerekmez; belge ülkesi belirleyici" (14.10'daki "genellikle APS gerekir" çelişkisi yok) |
| C04 | TR | Hindistan lisansı → APS | Doğru | |
| C05–C07 | TR/EN/DE | Remonstrasyon | Doğru | 01.07.2025'te kaldırıldı; yeni başvuru veya VG Berlin'de dava |
| C08–C10 | TR/EN/DE | Öğrenim vizesi kanalı | Doğru | Yalnız Auslandsportal (`digital.diplo.de/studium`), "kontrol tarihi: 14.10.2026" |
| C11–C13 | TR/EN/DE | Harç ≠ iDATA bedeli | Doğru | 75 € ayrı; 39,92 € ve/veya 12 € (TL); öğrenim vizesinde iDATA bedeli yok |
| C14 | TR | Öğrenim yeri arama vizesi kanalı | Doğru | iDATA değil, portal |

## 4. Yeni 10 konu × 3 dil

| # | Konu | TR | EN | DE | Kullanılan kaynaklar (örnek) |
|---|---|---|---|---|---|
| N01 | Türkiye diploması + APS | Doğru | Doğru | Doğru | `/…/blog/aps-certificate-turkey-2026-germany-university-guide`, `/…/faq/uni-assist/what-is-the-relationship-and-order-between-aps-and-uni-assist` |
| N02 | Hindistan diploması + APS | Doğru | Doğru | Doğru | Aynı. DE cevabı APS India istisnalarını da veriyor |
| N03 | Remonstrasyon | Doğru | Doğru | Doğru | `/…/faq/visa/what-can-i-do-if-my-german-visa-application-is-refused`, `/…/blog/what-to-do-after-german-student-visa-refusal-remonstration-appeal-guide` |
| N04 | Öğrenim vizesi kanalı | Doğru | **Kısmen** | **Kısmen** | `/…/faq/visa/how-do-i-get-a-german-student-visa`, `/…/blog/germany-student-visa-consulate-process-before-you-go` |
| N05 | Vize harcı ve iDATA ücretleri | Doğru | **Kısmen** | **Kısmen** | `/…/faq/appointments/how-do-you-apply-for-a-visa-through-idata`, `/…/faq/visa/whats-the-difference-between-idata-and-the-consulate-for-german-visa` |
| N06 | Kontrol tarihi ↔ yürürlük tarihi | Doğru | Doğru | Doğru | `/…/blog/germany-student-visa-consulate-process-before-you-go`, Ankara SSS'si |
| N07 | Blue Card 2026 eşikleri | Doğru | **Kısmen** | Doğru | `/…/blog/germany-blue-card-2026-comprehensive-guide-for-graduates`, meslek ve maaş yazıları |
| N08 | 140 gün çalışma hakkı | Doğru | **Kısmen** | **Kısmen** | `/…/faq/work/can-i-use-my-140-working-days-per-year-…`, `/…/faq/work/how-many-hours-a-week-can-i-work-as-a-student-in-germany` |
| N09 | Chancenkarte ve haftalık çalışma | Doğru | Doğru | Doğru | `/…/blog/germany-job-seeker-visa-2026-complete-guide-for-graduates` (Paket 2 ile yeniden yazıldı), `/…/faq/visa/how-do-i-apply-for-germanys-opportunity-card-chancenkarte` |
| N10 | Dil kursu tutarı ve alternatifler | Doğru | **Kısmen** | Doğru | `/…/faq/visa/how-much-money-do-you-need-in-a-blocked-account-for-a-language-course-visa`, Sperrkonto yazıları |

Doğru cevaplardan örnekler:
- **N06:** Üç dilde "kuralın hangi tarihten beri uygulandığı resmî sayfada belirtilmiyor; 14.10.2026 kontrol tarihi". Uydurma yürürlük tarihi yok.
- **N07 TR/DE:** 50.700 € / 45.934,20 €; indirimli eşik darboğaz meslekler ve son 3 yılın mezunları için, BA onayıyla.
- **N09:** Ortalama ≤20 saat/hafta, en fazla 2 haftalık deneme çalışması, ilk kart 1 yıla kadar, Folge-Chancenkarte ile 2 yıla kadar.
- **N10 TR/DE:** Dil kursu (§ 16f) en az 1.091 €/ay (Merkblatt 44F); Sperrkonto zorunlu değil, Verpflichtungserklärung eşit seçenek. TR cevap § 16b hazırlık kursu (992 €) ayrımını da yapıyor.

## 5. Kısmen doğru cevaplar ve kaynak chunk'ları

Kaynak chunk = chatbot'un `[n]` ile atıf yaptığı canlı sayfa metni. Hepsi canlıda GET ile teyit edildi.

| # | Cevaptaki sorunlu ifade | Kaynak chunk (canlı metin) | Değerlendirme | Önerilen çözüm (uygulanmadı) |
|---|---|---|---|---|
| N04 EN | "German missions in Türkiye **currently (September 2026)** handle study … visas exclusively via the Auslandsportal"; "As of September 1, 2024, the reference amount is €992" | `/en/faq/visa/how-do-i-get-a-german-student-visa-en`: "currently (September 2026)". "1 September 2024" ifadesi bu EN sayfada yok; muhtemelen DE kardeşin chunk'ından | "September 2026" kaynaksız bir durum tarihi; kontrol tarihi kuralıyla (14.10.2026) çelişiyor. 992 € tutarı doğru ama "01.09.2024'ten beri" resmî kaynakla doğrulanmadı (OSV-1 C02: UNVERIFIABLE) | OSV mini paketi (TR/EN/DE `how-do-i-get-a-german-student-visa`): "(Stand / currently September 2026)" → "(kontrol tarihi / checked / geprüft am 14.10.2026, [kaynak])"; "01.09.2024'ten beri 992 €" → "2026 için 992 €/ay (BMI ilanı BAnz AT 29.08.2025 B1)" |
| N04 DE | "Seit dem **01.09.2024** gilt ein Richtwert von 992 €" | `/de/faq/visa/how-do-i-get-a-german-student-visa-de`: "Seit dem 01.09.2024 gilt als Richtwert 992 € … (Stand September 2026)" | Aynı | Aynı paket |
| N05 EN | "The sources do not specify the exact amount for the German national visa fee itself" (75 € eksik) | Getirilen 6 chunk'ın hiçbirinde 75 € yok. `/en/faq/appointments/how-do-you-apply-for-a-visa-through-idata-en` 75'i içermiyor. 75'i anlatan `/en/faq/money/is-the-75eur-fee-sufficient-…-en` bu soru için getirilmedi | Yanlış değil ama eksik; "kaynakta yok" demesi dürüst | iDATA SSS'sinin EN/DE gövdesine "Ulusal vize harcı 75 € (§ 46 AufenthV), iDATA bedelinden ayrı ödenir" cümlesi; eval setine bu soru |
| N05 DE | Yalnız iDATA bedeli; 75 € vize harcı yok | Aynı (DE iDATA SSS'si) | Eksik | Aynı |
| N07 EN | "**Approximately** €50,700 … these figures are approximate" | Kaynakta eşik kesin (50.700 / 45.934,20). "approximate" ifadesi modelin kendi yumuşatması (sistem promptundaki "değişebilen bilgiyi kesinmiş gibi sunma" kuralının aşırı uygulaması) | Değer doğru, çerçeve yanlış: eşik yıllık sabit bir değer | Sistem promptuna not: "resmî ilanla sabitlenmiş yıllık eşikleri yaklaşık diye verme; yılı belirt". Ayrı onayla |
| N08 EN | "**Since March 1, 2024**, a day with up to 4 hours counts as half a day" | EN çalışma SSS'leri ve yazıları; tarih DE SSS'sinde "seit 01.03.2024" | Yarım gün kuralı 2024'ten önce de vardı (eski 120 tam / 240 yarım gün). Yanlış bağlam. 01.03.2024 yürürlük tarihi denetimde doğrulanmadı (C11 NEEDS_REVIEW) | Kaynak doğrulandıktan sonra (BGBl. / AufenthG geçiş hükmü) ya tarih kaynakla belirtilir ya kaldırılır; EN metinde yarım gün kuralının tarihsizleştirilmesi |
| N08 DE | "Die alte 120/240-Tage-Regel endete am **29.02.2024**; seit dem **01.03.2024** gilt die 140-Tage-Regel" | `/de/faq/work/how-many-hours-a-week-can-i-work-as-a-student-in-germany-de`: aynı ifadeler | Kural doğru; tarih resmî kaynakla doğrulanmadı | Aynı |
| N10 EN | "This amount is calculated as the BAföG maximum rate plus 10%" | `/en/faq/money/how-much-money-must-be-in-the-blocked-account-for-a-language-course-visa-en`: aynı cümle | 992 × 1,1 = 1.091,2, yani tutarla tutarlı. Ancak hesap yönteminin Merkblatt 44F'de böyle yazdığı bu turda teyit edilmedi | Merkblatt 44F'de "BAföG-Höchstsatz zzgl. 10 %" ifadesini doğrula; yoksa cümleyi kaldır |

**Resmî kaynağı doğrulanamayan bilgiler kesin doğru kabul edilmedi.** "01.09.2024", "01.03.2024", "September 2026" ve "+%10" bu nedenle "kısmen" sınıfında.

## 6. Önerilen sonraki adımlar (onay bekliyor)

1. **OSV mini paket (Paket 2b):** Yukarıdaki 4 kaynak metin ailesi.
   - `how-do-i-get-a-german-student-visa` TR/EN/DE: tarih ifadeleri.
   - iDATA SSS EN/DE: 75 € cümlesi.
   - Çalışma SSS'leri: 01.03.2024 tarihi, kaynakla doğrulanırsa.
   - Dil kursu EN SSS: "+%10", Merkblatt 44F ile doğrulanırsa.
   - KB etkisi: ≈10–15 kayıt, ≈20–40 chunk.
2. **Eval seti:** `resources/data/chat_eval.json` içine N05 (harç + iDATA), N07 (yaklaşık ifadesi yasak) ve N08 (yarım gün kuralının tarihi) için `must_contain` / `must_not_contain`.
3. **Chatbot promptu:** resmî ilanla sabitlenen yıllık eşikleri "yaklaşık" diye vermeme notu. Küçük ve düşük riskli bir değişiklik.
