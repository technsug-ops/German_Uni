/**
 * QS World University Rankings — Germany scraper (stealth)
 * Run: node scripts/scrape-qs-rankings.js
 * Output: storage/app/rankings/qs-germany-2026.json
 */

const { chromium } = require('playwright-extra');
const stealth = require('puppeteer-extra-plugin-stealth')();
const fs = require('fs');
const path = require('path');

chromium.use(stealth);

const URL = 'https://www.topuniversities.com/world-university-rankings?countries=de';
const OUTPUT = path.resolve(__dirname, '../storage/app/rankings/qs-germany-2026.json');

(async () => {
  console.log('🚀 Launching Chromium (stealth mode)...');
  const browser = await chromium.launch({
    headless: false,
    args: ['--disable-blink-features=AutomationControlled', '--no-sandbox'],
  });

  const context = await browser.newContext({
    userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36',
    viewport: { width: 1440, height: 900 },
    locale: 'en-US',
    timezoneId: 'Europe/Berlin',
  });

  const page = await context.newPage();
  console.log(`📡 Navigating to ${URL}`);
  await page.goto(URL, { waitUntil: 'domcontentloaded', timeout: 60000 });

  console.log('⏳ Cloudflare wait (15s)...');
  await page.waitForTimeout(15000);

  try {
    await page.click('button:has-text("Accept")', { timeout: 3000 });
    console.log('✓ Cookies accepted');
  } catch { console.log('(no cookie banner)'); }

  console.log('⏳ Waiting for QS data rows...');
  await page.waitForSelector('._qs-ranking-data-row', { timeout: 30000 }).catch(() => {
    console.warn('⚠️  No _qs-ranking-data-row');
  });

  // Lazy load — uzun scroll
  console.log('📜 Scrolling to load all rows...');
  for (let i = 0; i < 30; i++) {
    await page.evaluate(() => window.scrollBy(0, 600));
    await page.waitForTimeout(400);
  }
  await page.evaluate(() => window.scrollTo(0, 0));
  await page.waitForTimeout(2000);

  fs.mkdirSync(path.dirname(OUTPUT), { recursive: true });
  fs.writeFileSync(OUTPUT.replace('.json', '.html'), await page.content());
  console.log(`💾 Raw HTML saved`);

  const data = await page.evaluate(() => {
    const rows = [];
    document.querySelectorAll('._qs-ranking-data-row').forEach(row => {
      const rankText = row.querySelector('._univ-rank')?.textContent?.trim();
      const nameText = row.querySelector('._qs-ranking-title')?.textContent?.trim();
      if (!rankText || !nameText) return;
      // Tüm sayısal hücreleri çek
      const numbers = Array.from(row.querySelectorAll('*'))
        .map(el => el.textContent?.trim())
        .filter(t => t && /^\d{1,3}(\.\d{1,2})?$/.test(t))
        .map(t => parseFloat(t));
      rows.push({
        rank: parseInt(rankText.replace(/[^0-9]/g, ''), 10) || null,
        rank_raw: rankText,
        name: nameText,
        scores: numbers.slice(0, 12),
      });
    });
    return { url: window.location.href, title: document.title, rowCount: rows.length, sample: rows.slice(0, 3), rows };
  });

  fs.writeFileSync(OUTPUT, JSON.stringify(data, null, 2));
  console.log(`✓ Wrote ${data.rowCount} rows → ${OUTPUT}`);
  console.log('Sample:', JSON.stringify(data.sample, null, 2));

  console.log('\n🔍 Browser stays open 30s for manual inspection');
  await page.waitForTimeout(30000);
  await browser.close();
})().catch(e => { console.error('❌', e.message); process.exit(1); });
