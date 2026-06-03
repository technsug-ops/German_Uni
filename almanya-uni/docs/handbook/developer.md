# 💻 Geliştirici Handbook

## Stack
- **Laravel 13.8 · PHP 8.3 · Filament 4 (v4.11.x) · MySQL** · Tailwind (Vite build) · Alpine
- Çok-dilli: `/tr /en /de /fr` (default en; root→/tr). `__()` + `lang/{tr,en,de}.json`.
- Dual-brand: `brand()` host-aware helper (almanyauni.com / applytogerman.com).

## 🔴 KAS Hosting Gerçeği (her kararı etkiler)
- **SSH yok · Cronjob yok · CLI PHP 7.4** (Web 8.3). `php artisan` CLI prod'da ÇALIŞMAZ.
- **Gateway timeout sabit (~40-120s)** → uzun senkron iş 504. Çözüm: `--max-seconds` zaman bütçesi + parçalı.
- **FTPS upload tutarsız** → deploy bazen 5/5 fail eder (kod sağlamken). Çözüm: **boş retry commit** + tekrar.
- Migration prod'da: **`/admin/ops/migrate?run=1`** veya 🛠️ Operasyonlar → Migration Çalıştır (CLI değil).

## Deploy akışı
1. `git push origin main` (main'e direkt; kullanıcı onayı şart — auto-mode classifier).
2. GitHub Actions: testler + i18n gate + blade lint + Vite build → FTPS upload → `_deploy.php` (extract + cache temizle).
3. Deploy yeşil → migration gerekiyorsa `/admin/ops/migrate?run=1`.
4. FTPS fail → `git commit --allow-empty -m "ci: redeploy retry" && push`.

## ⚠️ Filament v4 Tuzakları (BUGÜN yaşandı — tekrar etme)
- **Section namespace:** `Filament\Schemas\Components\Section` (v3'teki `Forms\Components\Section` DEĞİL) → "Class not found" + sayfa 500.
- **Tablo gruplama:** `getTitleFromRecordUsing(fn ($r) => ...)` `$r` null gelebilir → null-guard şart.
- **`modifyQueryUsing(...->with())`:** v4'te tablo özet/filtre sorgusunu bozup `newQueryWithoutRelationships() on null` → **kullanma**; ilişki kolonlarını Filament zaten eager-load eder. (Filament #17275)
- **`getNavigationBadge()`:** tablo daha migrate edilmemişse sorgu admin-geneli 500 yapar → **`Schema::hasTable()` guard** koy.
- **Render testi şart:** lint yetmez. Local DB yoksa en azından `route:list` ile resource yüklemesini doğrula; mümkünse render et.

## ⚠️ Frontend/CSS Tuzakları
- **Tailwind interpolasyonlu class YASAK:** `bg-{{ $c }}-500` derlenmez (JIT literal tarar). Literal kullan veya match-map.
- **Admin custom sayfa CSS'i:** vendor Filament CSS sınırlı. Custom blade'de exotic utility → derlenmez. `viteTheme` denedik, **admin-geneli 500'e yol açtı, geri alındı.** Güvenli yol: sayfa içinde **`<style>` bloğu / inline stil** (manifest bağımsız).

## ⚠️ i18n Disiplin Gate (CI build'i DURDURUR)
- Her `__()` anahtarının `lang/tr.json` + `lang/de.json` karşılığı OLMALI. Eksikse **build fail**.
- Doğrula: `php artisan i18n:audit --leaky` → "Missing in tr/de: 0" olmalı.
- DB taxonomy için `name_tr` accessor; DE register = **du**.

## ⚠️ Güvenli dosya işlemleri
- `preg_replace` null dönerse `file_put_contents` dosyayı SİLER. `str_replace` tercih et + size sanity check.
- Bash'te PowerShell here-string (`@'...'@`) çalışmaz → commit mesajını dosyaya yaz, `git commit -F`.

## Konvansiyonlar
- Yeni route İngilizce-canonical (özel isim hariç); Türkçe path'ler sadece 301.
- Ops işleri: `/admin/ops/*` (admin-gated) + 🛠️ Operasyonlar dashboard kartı.
- Çevre içeriği (provider/dizin): Housing Providers desenini mirror et.
- "Önce ara": yeni özellikten önce `routes/web.php` + controllers + migrations grep.

## "Ship etmeden önce" checklist
- [ ] `php -l` tüm değişen dosyalar · `route:list` resource/sayfa yükleniyor
- [ ] `__()` anahtarları tr+de'de var (`i18n:audit --leaky`)
- [ ] Tailwind class'lar literal · custom admin CSS inline
- [ ] Migration idempotent (`Schema::hasTable` / `updateOrInsert`)
- [ ] Uzun iş varsa `--max-seconds` + set_time_limit
- [ ] Commit mesajı dosyadan (`-F`) · main push için kullanıcı onayı

## Hata ayıklama (prod 500)
1. `/admin/ops/migrate?run=1` (eksik migration mı?) — çoğu admin 500'ün sebebi.
2. Log: gerçek stack trace için `laravel-YYYY-MM-DD.log` (günlük rotasyon; `laravel.log` bayat olabilir).
3. Tahmin etme — stack trace'in **app/** frame'ini bul, oradan git.
