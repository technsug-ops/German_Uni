# Handbook — Giriş & Ortak Değerler

Bu el kitabı; bu yapıda yer alan **herkes** içindir. Önce bu sayfayı oku, sonra kendi rolünü.

## Roller
| Rol | Kim | Handbook |
|---|---|---|
| 👤 Kullanıcı | Öğrenci/aday (son kullanıcı) | [user.md](user.md) |
| 🛠️ Admin & Ops | Sistemi işleten | [admin-ops.md](admin-ops.md) |
| ✍️ Editör | İçerik üreten/onaylayan | [editor.md](editor.md) |
| 💻 Geliştirici | Kod yazan | [developer.md](developer.md) |
| 📣 Pazarlama | Büyüme/dağıtım | [marketing.md](marketing.md) |
| 🎓 Mentor | Öğrenciye birebir | [mentor.md](mentor.md) |

## 🎯 Ortak Değerler (herkes uyar)
Bunlar pazarlık konusu değil — markanın güveni bunlara bağlı.

1. **Gerçek > parlak.** Sosyal kanıt/metrik **doğrulanabilir** olmalı. Boş "0 metrik" puflama YASAK.
2. **Tahmin değil kesin bilgi.** Emin değilsen doğrula (resmî kaynak/kod/veri). YMYL (vize/yasa) = resmî kaynak + insan onayı + "son güncelleme".
3. **Sıkma — küratörlük.** Bilgi denizi ≠ boğulma. Sayfa başına link az (~3), her özellik bir işe yaramalı. Aşırılık yok.
4. **TR-first ama native her dil.** Birincil /tr; EN/DE birebir çeviri değil **native, SEO-destekli** uyarlama. Hardcoded TR yazma → `__()` + `lang/{tr,de}.json`. DE register = **du**.
5. **Topluluk havuzu zorunlu.** İçerik üretirken Forum + Telegram gerçek sorularını kullan.
6. **Kullanıcı memnuniyeti = büyümenin temeli.** Her karar "öğrenciye faydası ne?" testinden geçer.
7. **Şeffaflık.** Affiliate `rel=sponsored`, sponsorlu içerik etiketli, ücretsiz çekirdek korunur.

## 🧭 Nasıl çalışırız
- **Küçük + sık + güvenli.** Büyük riskli hamle yerine küçük doğrulanmış adımlar.
- **Önce ara, sonra kur.** Yeni özellikten önce var mı diye bak (routes/controllers/migrations).
- **Belge yaşar.** Değişiklik → aynı PR'da ilgili docs güncellenir.
- **Marka:** applytogerman.com = ApplyToGerman; (AlmanyaUni) parantezde geçebilir. Şablonda `brand()`.

## Karar çerçevesi
Bir şey yapmadan önce sor: **(1)** Öğrenciye faydası? **(2)** Kuzey yıldızına (MAU×retention) katkısı?
**(3)** Güveni artırır mı? **(4)** Sürdürülebilir mi (bootstrap)? Dördü de zayıfsa yapma.
