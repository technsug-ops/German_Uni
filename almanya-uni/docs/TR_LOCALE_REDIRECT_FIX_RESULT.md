# /tr/ dil sızıntısı düzeltmesi — sonuç

Tarih: 15 Ekim 2026. Durum: **yalnız yerelde uygulandı ve test edildi.** Commit, push ve deploy yok.
Dayanak: `docs/LEGACY_DOMAIN_REDIRECT_AUDIT.md` §1.5.

## Sorun

`routes/web.php` içindeki dil grubunda (`Route::prefix('{locale}')`) 29 eski Türkçe takma ad, öneksiz hedeflere yönlendiriyordu (örnek: `Route::redirect('/ekip', '/team')`). Öneksiz hedef `Route::fallback`'e düşüyor, oradan da marka varsayılanına (applytogerman.com için EN) 302 ile gidiyordu.

Canlı örnek (15.10, değişiklik öncesi):

```
/tr/ekip → 301 /team → 302 /en/team → 200
```

Etkisi:
- Türk ziyaretçi İngilizce sayfaya iniyordu.
- Sinyal yanlış dile akıyordu.
- EN ve DE sayfalarda da ara adım fazladan bir hop ekliyordu.

## Değişiklik

- `routes/web.php`: dil grubundaki 29 `Route::redirect` hedefi `'/{locale}/…'` biçimine çevrildi.
  - Laravel `RedirectController`, hedefteki `{locale}`'ü (ve `{slug}`'ı) mevcut rota parametresinden doldurur.
  - Sonuç: `/tr/ekip` → tek 301 → `/tr/team`.
  - Açıklayıcı yorum eklendi.
- Yeni test: `tests/Feature/TrLocaleRedirectTest.php`.

**Değişmeyenler:**
- Hedeflerin hiçbiri değişmedi; yalnız dil öneki eklendi. Hedefler her dilde kayıtlı birer rota (fallback değil); testle doğrulandı.
- Kök `/` → 302 `/en`; applytogerman.com'un varsayılan dili EN.
- Öneksiz eski adlar (`/ekip` → `/en/team`, tek 301): mevcut davranış.
- `public/.htaccess` ve almanyauni.com yönlendirmeleri: dokunulmadı.
- `LegacyPaths` ve FAQ ile blog slug çözümleri: dokunulmadı.

## Yönlendirme matrisi

Yerel HTTP kernel, host `applytogerman.com`; 29 takma ad × (tr / en / de / öneksiz) = 116 URL. Ham veri: `scratchpad/trfix/before.csv`, `after.csv`.

| Başlangıç | Önce | Sonra |
|---|---|---|
| `/tr/<takma ad>` | 24× 2 hop → **EN** 200; 2× 3 hop → EN; 2× → EN 404; 1× giriş | **28× tek 301 → TR 200**; 1× (`/tr/deneyim-paylas`) 301 → `/tr/contribute` → giriş (sayfa oturum istiyor) |
| `/en/<takma ad>` | 24× 2 hop → EN 200; 2× 3 hop; 2× 404 | 25× tek 301 → EN; 2× 2 hop (aşağıda); 1× giriş; 1× 404 (aşağıda) |
| `/de/<takma ad>` | Hepsi 2–3 hop ve **EN**'e | 25× tek 301 → **DE**; 2× 2 hop (aşağıda); 1× giriş; 1× 404 (aşağıda) |
| `/<takma ad>` (öneksiz) | 1 hop → EN | Aynı (değişmedi) |

Örnekler (sonra):

| URL | Zincir |
|---|---|
| `/tr/ekip` | 301 → `/tr/team` 200 |
| `/tr/gizlilik` | 301 → `/tr/privacy` |
| `/tr/haberler` | 301 → `/tr/news` |
| `/tr/araclar/sperrkonto` | 301 → `/tr/tools/sperrkonto` |
| `/de/gizlilik` | 301 → `/de/privacy` 200 |
| `/ekip` | 301 → `/en/team` (değişmedi) |

Kalan iki hop'lu durumlar beklenen davranış:
- **`/en|de/tools/vize-randevu`, `/en|de/araclar/vize-randevu`:** 301 `/en|de/tools/visa-appointment`, ardından 302 `/en|de/tools`. Araç yalnız TR; dil korunuyor, sızıntı yok.
- **`/en|de/haberler/{tr-haber-slug}`:** 301 `/en|de/news/{tr-slug}` → 404. TR slug'ı EN/DE'de yok (haber slug'ları dil başına farklı). Bu, düzeltmeden önce de 404'tü (araya EN 302'si ekleniyordu). "Var olmayan hedefe yönlendirme" sayılabilir ama eski `/de/haberler/<tr-slug>` adresinin gerçek bir kullanımı bilinmiyor. İyileştirme önerisi (uygulanmadı): `haberler/{slug}` için çeviri grubundan kardeş çözümü ya da doğrudan 404.

## Testler

| Test | Sonuç |
|---|---|
| `TrLocaleRedirectTest`: 29 takma ad × tr/en/de → tek 301, aynı dil; tüm hedefler kayıtlı rota; statik hedefler 200; `/tr/ekip` EN'e gitmiyor; kök 302 `/en`; öneksiz `/ekip` → `/en/team` | 35 test (veri sağlayıcı dahil) + `LegacyRedirectChainTest`: **35/35** |
| Tam paket | Aşama 4 sonunda toplu koşuldu (bkz. `OSV_PHASE0_GUARDRAILS_RESULT.md`) |

## Yayın notu (onay bekliyor)

- Kod değişikliği `routes/web.php` (29 satır + yorum) ve tek test dosyası. Migration yok.
- Canlı doğrulama: `scratchpad/legacy/probe.py` ile `/tr/…` takma adları (21 URL, LEGACY §1.5) yeniden ölçülür. Beklenen: tek 301, TR 200.
- Risk düşük: yalnız yönlendirme hedeflerinin dili değişiyor; 301'ler tarayıcı önbelleğinde kalır, ama yeni hedef doğru.
- Eski domain → TR (P0-E) için ön koşullardan biri kapanmış olur.
