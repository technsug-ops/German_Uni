# Deploy Setup — GitHub Actions → KAS

Bu rehber bir kerelik kurulum içindir. Tamamlandıktan sonra her `main` push'u otomatik canlıya çıkar.

## 1. GitHub Secrets ekle

Repo → **Settings → Secrets and variables → Actions → New repository secret**.

Eklenecek 4 secret:

| Secret adı | Değer |
|---|---|
| `FTP_HOST` | `ftp.applytogerman.com` (KAS FTP hostname) |
| `FTP_USER` | KAS FTP kullanıcı adı (örn. `w02196cc` veya `f01XXXX`) |
| `FTP_PASS` | KAS FTP şifresi (KAS panel → FTP-Zugang) |
| `ENV_PRODUCTION` | Tüm `.env.production` içeriğinin **tamamı** (multiline) |

### `ENV_PRODUCTION` nasıl alınır

Local'de:
```powershell
Get-Content "almanya-uni\deploy\.env.production" | Set-Clipboard
```

→ Sonra GitHub UI'da New Secret → değer alanına yapıştır.

⚠️ **Önemli:** Bu secret tüm prod credentials içerir (DB password, API keys). Sadece bu repo'nun yöneticisi görebilir.

## 2. KAS Cronjob kur (cache rebuild trigger)

KAS panel → **Tools → Cronjob → Neuer Cronjob**.

| Alan | Değer |
|---|---|
| Befehl | `php /www/htdocs/w02196cc/almanya-uni/deploy/post-deploy.php` |
| Periyot | Her 1 dakika (`* * * * *`) |
| Email | Sadece hata durumunda (Fehlermeldungen) |

**Nasıl çalışır:**
- Her dakika `post-deploy.php` çalışır.
- `storage/app/.deploy-pulse` dosyası YOK ise hemen çıkar (maliyet sıfıra yakın).
- VAR ise (= yeni deploy yapıldı) `artisan view:cache + config:cache + route:cache` çalıştırır, pulse'u siler.
- Log: `storage/logs/deploy-pulse.log` (auto-trimmed, max 100KB).

## 3. İlk deploy

```powershell
# Local'de
git add .
git commit -m "Initial dual-brand + CI/CD setup"
git push -u origin main
```

→ GitHub Actions otomatik tetiklenir. **Actions** sekmesinden ilerlemeyi izle.

Workflow ~3-5 dk sürer:
1. PHP 8.3 + Composer
2. Vite build
3. `.env` secret'tan yazılır
4. KAS'a FTPS upload (incremental — sadece değişen dosyalar)
5. `.deploy-pulse` dosyası gönderilir → Cronjob rebuild eder

## 4. Doğrulama

- https://almanyauni.com → `/tr` landing, AlmanyaUni branding
- https://applytogerman.com → `/en` landing, ApplyToGerman branding
- Mail test: feedback widget'a not yaz → admin mail'i FROM doğru brand'dan
- View-source: canonical kendi host'unda

## 5. Bir sonraki deploy

Her şey kuruldu. Bundan sonra:

```powershell
git add .
git commit -m "fix: my change"
git push
```

Bu kadar. 3-5 dk içinde canlıda.

## SSH upgrade (opsiyonel, sonra)

Eğer KAS Premium plan'in varsa SSH erişimi açıktır. SSH varsa workflow'u `appleboy/ssh-action` ile değiştirip artisan komutlarını doğrudan tetikleyebiliriz (Cronjob'a gerek kalmaz, daha hızlı).

KAS panel → menüde **SSH-Zugang** var mı bak. Varsa söyle, workflow'u güncelleriz.

## Sorun giderme

**Workflow'da "FTP connection failed":**
- KAS panel → FTP-Zugang → bağlantı bilgileri doğru mu kontrol et
- Port 21 (FTPS), TLS açık olmalı

**Site açılıyor ama stil yok (CSS gelmedi):**
- public/hot dosyası kalmış olabilir → KAS WebFTP'den sil
- view:cache eski path içeriyor → cronjob 1 dk içinde rebuild eder (veya manuel komutla)

**"SECRET ENV_PRODUCTION SQLite içeriyor" hatası:**
- `ENV_PRODUCTION` secret'ında `DB_CONNECTION=mysql` olduğundan emin ol
- Postmortem #3'te bahsedilen klasik hata
