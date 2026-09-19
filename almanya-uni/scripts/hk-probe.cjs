/**
 * PROBE v4 — orijinal /detail URL'inde section.result-box satırlarını döker.
 */
const { chromium } = require('playwright-extra');
const stealth = require('puppeteer-extra-plugin-stealth')();
const fs = require('fs');
chromium.use(stealth);

const U = (pn) =>
  `https://www.hochschulkompass.de/studium/studiengangsuche/erweiterte-studiengangsuche/detail/all/search/1/studtyp/1${pn ? `/pn/${pn}` : ''}.html?tx_szhrksearch_pi1%5Bzubesch%5D%5B%5D=O`;
const sleep = (ms) => new Promise((r) => setTimeout(r, ms));
async function passEnodia(page) {
  for (let i = 0; i < 30; i++) {
    if (!/enodia/i.test(await page.title().catch(() => ''))) return true;
    const b = page.locator('button', { hasText: /continue|fortfahren/i });
    if (await b.count().catch(() => 0)) await b.first().click().catch(() => {});
    await sleep(1200);
  }
  return false;
}

(async () => {
  const browser = await chromium.launch({ headless: false, args: ['--no-sandbox'] });
  const ctx = await browser.newContext({
    userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36',
    locale: 'de-DE', timezoneId: 'Europe/Berlin', viewport: { width: 1440, height: 900 },
  });
  const page = await ctx.newPage();

  for (const pn of [0, 2]) {
    await page.goto(U(pn), { waitUntil: 'domcontentloaded', timeout: 60000 });
    await passEnodia(page);
    await sleep(900);
    const data = await page.evaluate(() => {
      const boxes = [...document.querySelectorAll('section.result-box')];
      const rows = boxes.map((b) => {
        const txt = b.innerText.replace(/\n+/g, ' | ').replace(/\s+/g, ' ').trim();
        const a = b.querySelector('a[href*="detail"]');
        // dt/dd veya label yapısı
        const fields = {};
        b.querySelectorAll('dt, .label, strong, th').forEach((lbl) => {
          const k = lbl.innerText.trim().replace(':', '');
          const v = (lbl.nextElementSibling?.innerText || '').trim();
          if (k && v) fields[k] = v;
        });
        return { txt: txt.slice(0, 220), href: a?.getAttribute('href')?.split('?')[0] || null, fields };
      });
      const m = (document.body.innerText || '').match(/([0-9.]+)\s*Treffer/i);
      return { treffer: m ? m[0] : null, count: boxes.length, rows: rows.slice(0, 4) };
    });
    console.log(`\n=== pn=${pn} | TREFFER=${data.treffer} | result-box=${data.count} ===`);
    console.log(JSON.stringify(data.rows, null, 1));
    if (pn === 0) fs.writeFileSync('storage/app/hk-results.html', await page.content());
  }
  await sleep(1000);
  await browser.close();
})().catch((e) => { console.error('HATA:', e.message); process.exit(1); });
