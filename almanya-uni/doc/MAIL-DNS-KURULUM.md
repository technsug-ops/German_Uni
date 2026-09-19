# Mail Teslimat Kurulumu (SPF · DKIM · DMARC)

**Sorun (2026-09-20):** Gmail'e giden tüm mailler geri dönüyordu:

```
550-5.7.26 Your email has been blocked because the sender is unauthenticated.
Gmail requires all senders to authenticate with either SPF or DKIM.
DKIM = did not pass · SPF [applytogerman.com] with ip: [85.13.128.135] = did not pass
```

Sunucu maili gönderiyor, **alıcı** reddediyor. Sebep: alan adının DNS'inde gönderim
yetkilendirmesi yok. Bu yalnızca lead onay maillerini değil, bülten ve panelden
atılan her maili etkiler.

**Durum tespiti:**

| Kayıt | Durum (öncesi) |
|---|---|
| SPF | **yok** |
| DKIM | **yok** (default/kas/dkim/mail/k1 seçicilerinin hiçbirinde kayıt yok) |
| DMARC | var ama `v=DMARC1; p=none;` (yalnızca izleme) |

**Önemli:** alan adının DNS'i **Cloudflare**'de (`kurt.ns.cloudflare.com`), mail sunucusu
ise **All-Inkl** (`dd53930.kasserver.com` = `w02196cc.kasserver.com`, IP `85.13.128.135`).
Kayıtlar Cloudflare'e **elle** eklenir; All-Inkl otomatik ekleyemez.

İki markanın durumu farklı — karıştırma:

| Alan adı | DNS nerede | SPF | Sonuç |
|---|---|---|---|
| **applytogerman.com** | Cloudflare | **yoktu** → elle eklenecek | Gmail reddediyordu |
| almanyauni.com | All-Inkl (ns5/ns6.kasserver.com) | var (`a` mekanizmasıyla geçiyor) | sorunsuz |

KAS hesabı: `w02196cc`, kök dizin `/www/htdocs/w02196cc/` (prod `.env` orada).

---

## 1. SPF (Cloudflare)

1. dash.cloudflare.com → `applytogerman.com` → **DNS → Records → Add record**
2. Type: **TXT** · Name: **@** · TTL: Auto
3. Content:
   ```
   v=spf1 a mx include:kasserver.com ~all
   ```
4. Save.

**Neden tam olarak bu satır** (üç mekanizma da doğrulandı):

| Mekanizma | Neyi kapsıyor |
|---|---|
| `a` | `applytogerman.com` A kaydı → 85.13.128.135 (Cloudflare proxy'si kapalı, IP doğrudan görünüyor) |
| `mx` | `mail.applytogerman.com` → 85.13.128.135 |
| `include:kasserver.com` | `v=spf1 a mx ip4:85.13.128.0/18 ip4:185.3.40.0/22 ip6:2a02:2d40::/32 ~all` — gönderen IP bu aralıkta; sağlayıcı IP değiştirirse kayıt kendiliğinden geçerli kalır |

> **TUZAK:** All-Inkl'in `almanyauni.com` için otomatik yazdığı satır
> `v=spf1 a mx include:spf.kasserver.com ~all` biçiminde. Ama `spf.kasserver.com`
> yalnızca `ip4:85.13.159.116` içeriyor — **bizim gönderen IP'miz orada yok.**
> O alan adında mail yine de geçiyor, çünkü `a` mekanizması işi görüyor. Bu satırı
> applytogerman.com'a olduğu gibi kopyalamak yanıltıcı olur; yukarıdaki sürümü kullan.

> Alan adı başına **tek** SPF kaydı olur. İleride başka bir gönderici eklenirse (ör. bülten
> servisi) yeni kayıt açma, mevcut satıra `include:` ekle.

---

## 2. DKIM (önce All-Inkl, sonra Cloudflare)

1. kas.all-inkl.com → **E-Mail** bölümü → `applytogerman.com` → **DKIM / Mail-Authentifizierung**
   (menüde göremezsen Tools/Einstellungen altına bak).
2. DKIM'i etkinleştir. KAS bir **seçici (selector)** ve uzun bir **public key** üretir.
   DNS All-Inkl'de olmadığı için "kaydı kendiniz ekleyin" uyarısı çıkar — normal.
3. Cloudflare → **DNS → Add record**
   - Type: **TXT**
   - Name: `<seçici>._domainkey` (Cloudflare alan adını kendisi ekler)
   - Content: KAS'ın verdiği `v=DKIM1; k=rsa; p=...` değerinin tamamı
4. Save.

---

## 3. Doğrulama

Yayılma 5–30 dakika. Sonra:

```bash
nslookup -type=TXT applytogerman.com 8.8.8.8            # v=spf1 ... görünmeli
nslookup -type=TXT <seçici>._domainkey.applytogerman.com 8.8.8.8   # v=DKIM1 ... görünmeli
```

Gerçek test: bir Gmail adresine mail at, Gmail'de **"Orijinali göster"** de.
`SPF: PASS` ve `DKIM: PASS` yazmalı.

---

## 4. Sonra: DMARC'ı sıkılaştır

SPF ve DKIM geçtiği doğrulandıktan **sonra** `_dmarc` kaydını güncelle:

```
v=DMARC1; p=quarantine; rua=mailto:admin@applytogerman.com
```

Önce `p=none` ile birkaç gün rapor toplamak, sonra `quarantine`e geçmek en güvenlisi.
`p=reject`'e ancak raporlar temizken geçilir.

---

İlgili: `doc/…` mail kutusu ayarları için memory `smtp-allinkl-setup`
(host `dd53930.kasserver.com`, kutu kimlikleri, 554 hatasının anlamı).
