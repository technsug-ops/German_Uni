/**
 * Hochschulkompass — zulassungsfrei (NC-frei) program kazıyıcı
 *
 * Enodia anti-bot duvarını stealth + buton-tıklama ile bir kez aşar, cookie'yi
 * tutar; zubesch[0]=O (ohne Zulassungsbeschränkung) filtreli sonuç listesini
 * sayfa sayfa (section.result-box) gezerek tüm programları çıkarır.
 *
 * Sayfa URL şeması (cHash'siz, temiz):
 *   sayfa 1     : .../erweiterte-studiengangsuche/search/1/studtyp/{st}.html?...[zubesch][0]=O
 *   sayfa N>=2  : .../erweiterte-studiengangsuche/search/1/studtyp/{st}/pn/{N-1}.html?...[zubesch][0]=O
 *
 * Çıktı: storage/app/hk-zulassungsfrei.json
 * Kullanım:
 *   node scripts/scrape-hk-zulassungsfrei.cjs --max=3   # test: her studtyp ilk 3 sayfa
 *   node scripts/scrape-hk-zulassungsfrei.cjs           # tam koşu
 */
const { chromium } = require('playwright-extra');
const stealth = require('puppeteer-extra-plugin-stealth')();
const fs = require('fs');
const path = require('path');
chromium.use(stealth);

const argMax = (() => {
  const a = process.argv.find((x) => x.startsWith('--max='));
  return a ? parseInt(a.split('=')[1], 10) : 0;
})();

const OUT = path.resolve(__dirname, '../storage/app/hk-zulassungsfrei.json');
const ROOT = 'https://www.hochschulkompass.de/studium/studiengangsuche/erweiterte-studiengangsuche';
const ZUBESCH = 'tx_szhrksearch_pi1%5Bzubesch%5D%5B0%5D=O';
const STUDTYPEN = [{ id: 1, label: 'grundständig' }, { id: 3, label: 'weiterführend' }];

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));
// page: 1-based
const urlFor = (st, page) =>
  `${ROOT}/search/1/studtyp/${st}${page > 1 ? `/pn/${page - 1}` : ''}.html?${ZUBESCH}`;

async function passEnodia(page) {
  for (let i = 0; i < 30; i++) {
    if (!/enodia/i.test(await page.title().catch(() => ''))) return true;
    const b = page.locator('button', { hasText: /continue|fortfahren/i });
    if (await b.count().catch(() => 0)) await b.first().click().catch(() => {});
    await sleep(1200);
  }
  return false;
}

async function extractPage(page) {
  return page.evaluate(() => {
    const LABELS = ['Hochschule', 'Studienort', 'Abschluss', 'Studientyp', 'Studienform', 'SIT-Passung'];
    const rows = [];
    document.querySelectorAll('section.result-box').forEach((b) => {
      const toks = b.innerText.split('\n').map((s) => s.trim()).filter(Boolean);
      if (!toks.length) return;
      const fach = toks[0];
      const get = (label) => {
        const i = toks.findIndex((t) => t.replace(':', '') === label);
        return i >= 0 && i + 1 < toks.length ? toks[i + 1] : '';
      };
      // değer LABELS'ten biri olmamalı (boş alan kontrolü)
      const val = (label) => { const v = get(label); return LABELS.includes(v) ? '' : v; };
      const hoch = val('Hochschule');
      if (!fach || !hoch || LABELS.includes(fach)) return;
      rows.push({
        fach, hochschule: hoch, ort: val('Studienort'),
        abschluss: val('Abschluss'), typ: val('Studientyp'), form: val('Studienform'),
      });
    });
    const m = (document.body.innerText || '').match(/([0-9.]+)\s*Treffer/i);
    return { rows, treffer: m ? parseInt(m[1].replace(/\./g, ''), 10) : null };
  });
}

(async () => {
  const browser = await chromium.launch({ headless: false, args: ['--no-sandbox'] });
  const ctx = await browser.newContext({
    userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36',
    locale: 'de-DE', timezoneId: 'Europe/Berlin', viewport: { width: 1440, height: 900 },
  });
  const page = await ctx.newPage();

  const all = [];
  const seen = new Set();
  let emptyStreak = 0;

  for (const st of STUDTYPEN) {
    console.log(`\n=== studtyp ${st.id} (${st.label}) ===`);
    await page.goto(urlFor(st.id, 1), { waitUntil: 'domcontentloaded', timeout: 60000 });
    if (!(await passEnodia(page))) { console.error('  Enodia geçilemedi'); continue; }
    await sleep(700);

    let { rows, treffer } = await extractPage(page);
    const perPage = rows.length || 10;
    const totalPages = treffer ? Math.ceil(treffer / perPage) : 1;
    const maxPages = argMax > 0 ? Math.min(argMax, totalPages) : totalPages;
    console.log(`  Treffer=${treffer} | /sayfa=${perPage} | toplam=${totalPages} | çekilecek=${maxPages}`);

    for (let p = 1; p <= maxPages; p++) {
      if (p > 1) {
        await page.goto(urlFor(st.id, p), { waitUntil: 'domcontentloaded', timeout: 60000 });
        if (!(await passEnodia(page))) { console.error(`  sayfa ${p} enodia takıldı`); break; }
        await sleep(120);
        ({ rows } = await extractPage(page));
      }
      if (!rows.length) {
        emptyStreak++;
        if (emptyStreak >= 3) { console.log(`  3 boş sayfa peş peşe → ${st.label} bitti @ sayfa ${p}`); break; }
      } else emptyStreak = 0;

      let added = 0;
      for (const r of rows) {
        const key = `${r.fach}|${r.hochschule}|${r.abschluss}`;
        if (seen.has(key)) continue;
        seen.add(key);
        all.push({ ...r, studtyp: st.label, zulassung: 'zulassungsfrei' });
        added++;
      }
      if (p % 25 === 0 || p === maxPages) {
        console.log(`  sayfa ${p}/${maxPages} → toplam ${all.length}`);
        fs.writeFileSync(OUT, JSON.stringify(all, null, 1));
      }
      await sleep(300 + (p % 4) * 120);
    }
  }

  fs.writeFileSync(OUT, JSON.stringify(all, null, 1));
  console.log(`\n✅ Bitti. ${all.length} zulassungsfrei program → ${OUT}`);
  await browser.close();
})().catch((e) => { console.error('HATA:', e.message); process.exit(1); });
