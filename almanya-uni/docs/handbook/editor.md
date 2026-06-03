# ✍️ Editör / İçerik Handbook

Amaç: **az ama kritik, küratörlü, doğru, çok-dilli** içerik. Haftada 2-4 nitelikli yayın > günlük firehose.

## İçerik akışı (uçtan uca)
1. **Fikir → Brief:** `/admin/content-briefs` (Content Factory). Hedef kitle, topic, pain point, anahtar kelimeler. (🪄 Brief Önerileri AI yardımcı.)
2. **AI Asset Üret:** brief içinde → blog (+ sosyal/diğer platformlar). Gemini ile özgün taslak.
3. **Kontrol et:** doğruluk + akıcılık + native dil. AI çıktısını **düzelt** (ör. tuhaf Türkçe, &quot; gibi artıklar).
4. **Yayınla:** asset → **📤 Blog'a Aktar** (Yayın Merkezi veya brief) → yazar seç → **TR + EN + DE tek tık**.
5. **Dağıt:** sosyal asset'leri Yayın Kokpiti'nden paylaş.

## Kalite barı (red çizgiler)
- **Gerçek > parlak.** Doğrulanamayan sayı/iddia yok. Vize/yasa = resmî kaynak + "son güncelleme".
- **Topluluk havuzu zorunlu:** Forum + Telegram gerçek sorularını içeriğe kat.
- **Telif:** birebir kopyalama YASAK. Kaynaktan **ilham + özgün** + atıf + deep-link.
- **Aşırılık yok:** sayfa başına link az (~3). Sıkma.
- **İç link:** sadece var olan yazıya bağla; resolver hedefsizi düz metne indirir (404 üretme).

## Çok-dillilik (i18n)
- Birincil **/tr**. EN/DE **birebir çeviri değil**, native + SEO-destekli uyarlama. DE register = **du**.
- Blade/controller'da hardcoded TR yazma → `__()` + `lang/{tr,de}.json` (3 dilin de değeri). Eksikse CI build durur.
- Kaynak kendi dilinde işlenir; diğer dillere native uyarlanır; ham başka-dil sızmaz.

## Marka dili
- applytogerman.com'da birincil **ApplyToGerman**; (AlmanyaUni) parantezde geçebilir. İkisi de serbest.

## Kategoriler
- Blog: Başvuru · Almanya'da Eğitim · Dil & Sınavlar · Vize · **Almanya'da Yaşam & Kültür** · Finans · Öğrenci Hayatı
- Haber: Vize&Oturum · Yasa · Üniversite · Entegrasyon · Burs · Pratik · Öğrenci Yaşamı

## Ritim
Haftada 2-4 yayın (3 dil) + haber akışını işle. Pzt plan, hafta içi üret/yayınla, Cuma gözden geçir.
İçerik = SEO + retention + kendi-kendini-büyüten çarkın yakıtı.
