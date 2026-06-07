# AlmanyaUni — Stres / Yük Testi Rehberi

Hedef: günlük binlerce ziyaretçiye dayanacak altyapının **kırılma noktasını
(breakpoint)** bulmak ve dar boğazları tespit etmek.
Stack: **Laravel (PHP 8.3) + MySQL + Nginx (KAS All-Inkl, paylaşımlı hosting)**.

## 1. Çalıştırma

```bash
# Kurulum: https://k6.io/docs/get-started/installation/  (tek binary)

# Önce DÜŞÜK yükle duman testi:
k6 run -e BASE_URL=https://applytogerman.com -e SCENARIO=smoke tests/load/k6-stress.js

# Kademeli artış (breakpoint avı: 0→50→100→300→500 VU):
k6 run -e BASE_URL=https://applytogerman.com tests/load/k6-stress.js

# Belirli bir eşzamanlılıkta sabit yük:
k6 run -e BASE_URL=https://applytogerman.com -e SCENARIO=steady -e VUS=200 tests/load/k6-stress.js
```

> ⚠️ **Sadece kendi sunucuna** karşı çalıştır. KAS paylaşımlı hosting → AUP'a
> takılmamak için düşükten başla, kademeli artır, gece/az-trafik saatinde test et.
> İdeali: önce bir **staging** kopyasında.

## 2. Test senaryosu (script içinde)

3 kritik kullanıcı akışı, gerçekçi "düşünme süreleri" (sleep) ile:
1. **Ana sayfa** (`/tr`)
2. **Arama** (`/tr/search?q=...`) — en pahalı yol (~19 FULLTEXT/LIKE sorgusu)
3. **Liste** (`/tr/universities` · `/tr/programs` · `/tr/blog`)

## 3. Kırılma noktası mantığı (otomatik durdurma)

Script şu eşikler aşılınca testi **otomatik durdurur** (`abortOnFail`):

| Metrik | Eşik | Anlamı |
|---|---|---|
| `http_req_duration` p95 | **> 3000 ms** | Kabul edilemez gecikme |
| `errors` (5xx + bağlantı) | **> %5** | Sistem kırılma noktasında |

Breakpoint = bu eşiklerin ilk aşıldığı VU (eşzamanlı kullanıcı) seviyesidir.
k6 çıktısındaki o anki `vus` değerini not et.

## 4. İzleme kontrol listesi (test SIRASINDA)

Test koşarken sunucuda paralel izle (KAS panel + SSH varsa):

**CPU**
- [ ] CPU sürekli **%85+**'te mi takılıyor? → PHP-FPM darboğazı
- [ ] `top` / `htop` → hangi süreç (php-fpm, mysqld) yiyor?

**RAM**
- [ ] Bellek dolup **swap**'a mı giriyor? (swap = ani yavaşlama)
- [ ] PHP-FPM `pm.max_children` × ortalama süreç MB > RAM mı?

**MySQL — en olası darboğaz**
- [ ] `SHOW STATUS LIKE 'Threads_connected';` → `max_connections`'a yaklaşıyor mu?
- [ ] `SHOW FULL PROCESSLIST;` → "Sending data" / "Copying to tmp table" takılan sorgu?
- [ ] `SHOW STATUS LIKE 'Slow_queries';` artıyor mu?
- [ ] Arama (FULLTEXT) sorguları yavaş mı? → `slow_query_log` aç

**PHP-FPM / Nginx**
- [ ] `502/504` görülüyor mu? → FPM havuzu tükendi (`pm.max_children` az)
- [ ] Nginx `worker_connections` limiti?

**Uygulama-içi (bu projeye özel)**
- [ ] **Rate-limit cache file driver** (`CACHE_STORE`) — yüksek eşzamanlılıkta
      atomik değil → yarış. Prod'da **Redis** öner. (Keşif QA bulgusu #3)
- [ ] Queue (`QUEUE_CONNECTION=database`) — webhook/mail işleri DB'yi mi kilitliyor?

## 5. Çökerse — loglarda ne aranır?

| Belirti | Log | Aranacak |
|---|---|---|
| 500 hataları | `storage/logs/laravel.log` | `SQLSTATE[HY000] [1040] Too many connections`, `max_user_connections`, OOM |
| 502/504 | Nginx error log | `upstream timed out`, `connect() failed`, `worker_connections are not enough` |
| PHP çöküşü | PHP-FPM log | `server reached pm.max_children`, `memory_size ... exhausted` |
| MySQL | MySQL error log | `Too many connections`, `Lock wait timeout`, `tmp table` |
| Yavaşlık | `slow_query_log` | En çok tekrarlanan / en uzun süren sorgu (genelde arama/sıralama) |

## 6. İlk müdahale öncelikleri (tipik)

1. **MySQL `max_connections`** yükselt + sorgu indeksleri (arama FULLTEXT zaten var).
2. **PHP-FPM `pm.max_children`** RAM'e göre ayarla.
3. **Sayfa cache** — public sayfalar büyük ölçüde statik; full-page/HTTP cache (CDN/Cloudflare) en büyük kazanç.
4. **Redis** → cache + rate-limit + session (file driver yarışını çözer).
5. **Arama** ağırsa: sonuçları kısa süreli cache'le (suggest zaten 300 sn cache'li).

## Alternatif: Locust (Python)

k6 yerine Python tercih edilirse aynı 3 akış `locust -f locustfile.py --host=...`
ile kurulabilir; ramp-up `--users 500 --spawn-rate 10`, eşik mantığı
`response.elapsed` + `environment.runner.quit()` ile elle kurulur. k6 daha
basit (tek binary, eşik/abort yerleşik) olduğu için varsayılan budur.
