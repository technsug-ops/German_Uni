// ────────────────────────────────────────────────────────────────────────────
// AlmanyaUni — k6 Stres / Yük Testi
// ────────────────────────────────────────────────────────────────────────────
// AMAÇ: Sitenin "kırılma noktasını" (breakpoint) bulmak ve dar boğazları
//       tespit etmek. Stack: Laravel (PHP 8.3) + MySQL + Nginx (KAS All-Inkl).
//
// KURULUM:  https://k6.io/docs/get-started/installation/  (tek binary)
//
// ÇALIŞTIRMA:
//   # Hızlı duman testi (1 dk, düşük yük):
//   k6 run -e BASE_URL=https://applytogerman.com -e SCENARIO=smoke tests/load/k6-stress.js
//
//   # Kademeli yük (ramp-up, varsayılan — kırılma noktasını arar):
//   k6 run -e BASE_URL=https://applytogerman.com tests/load/k6-stress.js
//
//   # Tek bir hedef seviyede sabit yük (örn. 200 eşzamanlı):
//   k6 run -e BASE_URL=https://applytogerman.com -e SCENARIO=steady -e VUS=200 tests/load/k6-stress.js
//
// ⚠️  SADECE KENDİ sunucuna karşı çalıştır. Paylaşımlı hosting'de (KAS) önce
//     düşük yükle başla; sağlayıcının AUP'una takılmamak için aşırı yükten kaçın.
// ────────────────────────────────────────────────────────────────────────────

import http from 'k6/http';
import { check, group, sleep } from 'k6';
import { Rate, Trend } from 'k6/metrics';

const BASE = (__ENV.BASE_URL || 'http://localhost:8000').replace(/\/$/, '');
const SCENARIO = __ENV.SCENARIO || 'rampup';
const STEADY_VUS = parseInt(__ENV.VUS || '200', 10);

// ── Özel metrikler ──
const errorRate = new Rate('errors');           // 5xx + bağlantı hataları oranı
const flowHome = new Trend('flow_home', true);
const flowSearch = new Trend('flow_search', true);
const flowList = new Trend('flow_list', true);

// ── Senaryo profilleri ──
const SCENARIOS = {
  // 1 dk, küçük yük — pipeline duman testi (CI'da bile koşabilir)
  smoke: {
    executor: 'constant-vus', vus: 5, duration: '1m',
  },
  // Sabit hedef yük — belirli bir eşzamanlılıkta davranışı gözle
  steady: {
    executor: 'constant-vus', vus: STEADY_VUS, duration: '5m',
  },
  // KADEMELİ ARTIŞ (breakpoint avı): 0 → 100 → 300 → 500 VU
  rampup: {
    executor: 'ramping-vus',
    startVUs: 0,
    stages: [
      { duration: '1m', target: 50 },   // ısınma
      { duration: '2m', target: 100 },  // hedef alt sınır
      { duration: '2m', target: 300 },  // baskı
      { duration: '2m', target: 500 },  // hedef üst sınır
      { duration: '1m', target: 0 },    // soğuma
    ],
    gracefulRampDown: '30s',
  },
};

export const options = {
  scenarios: { main: SCENARIOS[SCENARIO] || SCENARIOS.rampup },

  // KIRILMA EŞİĞİ — bu eşikler aşılırsa test FAIL döner (abortOnFail ile durur):
  //   • p95 yanıt süresi > 3 sn  → kabul edilemez gecikme
  //   • hata oranı       > %5    → sistem kırılma noktasında
  thresholds: {
    http_req_duration: [{ threshold: 'p(95)<3000', abortOnFail: true, delayAbortEval: '30s' }],
    errors:            [{ threshold: 'rate<0.05',   abortOnFail: true, delayAbortEval: '30s' }],
    http_req_failed:   ['rate<0.05'],
  },

  // Paylaşımlı hosting'i hızlı 5xx ile öldürmemek için makul varsayılanlar
  noConnectionReuse: false,
  userAgent: 'k6-loadtest/AlmanyaUni',
};

// HTTP cevabını değerlendirip metrikleri günceller
function track(res, trend) {
  trend.add(res.timings.duration);
  const ok = check(res, {
    'status 2xx/3xx': (r) => r.status >= 200 && r.status < 400,
  });
  // 5xx VEYA bağlantı hatası (status 0) = gerçek hata
  errorRate.add(res.status === 0 || res.status >= 500);
  return ok;
}

export default function () {
  // ── AKIŞ 1: Ana sayfa ziyareti ──
  group('1_home', () => {
    const res = http.get(`${BASE}/tr`, { tags: { flow: 'home' } });
    track(res, flowHome);
  });
  sleep(1 + Math.random());  // gerçekçi düşünme süresi

  // ── AKIŞ 2: Arama (en pahalı sorgu yolu — ~19 FULLTEXT/LIKE) ──
  group('2_search', () => {
    const terms = ['informatik', 'münih', 'maschinenbau', 'berlin', 'bwl'];
    const q = terms[Math.floor(Math.random() * terms.length)];
    const res = http.get(`${BASE}/tr/search?q=${q}`, { tags: { flow: 'search' } });
    track(res, flowSearch);
  });
  sleep(1 + Math.random());

  // ── AKIŞ 3: Liste/detay sayfası (üniversite veya program listesi) ──
  group('3_list', () => {
    const paths = ['/tr/universities', '/tr/programs', '/tr/blog'];
    const p = paths[Math.floor(Math.random() * paths.length)];
    const res = http.get(`${BASE}${p}`, { tags: { flow: 'list' } });
    track(res, flowList);
  });
  sleep(2 + Math.random() * 2);
}

// Test bitince özet — hangi akışın yavaşladığını netleştirir
export function handleSummary(data) {
  const m = data.metrics;
  const p95 = (k) => (m[k] && m[k].values ? Math.round(m[k].values['p(95)'] || 0) : 0);
  const line = (label, key) => `  ${label.padEnd(14)} p95=${p95(key)}ms`;
  const txt = [
    '',
    '═══ AlmanyaUni Stres Testi Özeti ═══',
    `  Senaryo:       ${SCENARIO}`,
    `  Toplam istek:  ${m.http_reqs ? m.http_reqs.values.count : 0}`,
    `  Hata oranı:    ${m.errors ? (m.errors.values.rate * 100).toFixed(2) : 0}%`,
    line('Ana sayfa', 'flow_home'),
    line('Arama', 'flow_search'),
    line('Liste', 'flow_list'),
    `  http p95:      ${p95('http_req_duration')}ms  (eşik: 3000ms)`,
    '═══════════════════════════════════',
    '',
  ].join('\n');
  return { stdout: txt };
}
