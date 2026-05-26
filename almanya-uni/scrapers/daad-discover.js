/**
 * DAAD International Programmes — network sniff to find the real API endpoint.
 * Loads the result page in Chromium and logs all XHR/fetch requests.
 */
const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: true });
  const ctx = await browser.newContext({
    userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148 Safari/537.36',
    locale: 'en-US',
  });
  const page = await ctx.newPage();

  const apiCalls = [];
  page.on('request', (req) => {
    const url = req.url();
    const type = req.resourceType();
    if (type === 'xhr' || type === 'fetch' || url.includes('.json') || url.includes('/api/') || url.includes('solr')) {
      apiCalls.push({ method: req.method(), url, type });
    }
  });

  page.on('response', async (res) => {
    const url = res.url();
    if (url.includes('solr') || url.includes('/api/') || (url.includes('.json') && !url.includes('.css') && !url.includes('manifest'))) {
      try {
        const ct = res.headers()['content-type'] || '';
        if (ct.includes('json')) {
          const body = await res.text();
          console.log(`\n[JSON RESPONSE] ${res.status()} ${url}`);
          console.log(`Content-Type: ${ct}`);
          console.log(`First 400 chars: ${body.substring(0, 400)}`);
        }
      } catch (e) {}
    }
  });

  console.log('Loading DAAD result page...');
  await page.goto(
    'https://www2.daad.de/deutschland/studienangebote/international-programmes/en/result/?q=&degree[0]=2&limit=10&offset=0',
    { waitUntil: 'networkidle', timeout: 60000 }
  );

  console.log('\nWaiting for any deferred JS calls...');
  await page.waitForTimeout(5000);

  console.log('\n=== ALL XHR/FETCH/JSON REQUESTS ===');
  for (const c of apiCalls) {
    console.log(`  ${c.method} [${c.type}] ${c.url}`);
  }

  console.log(`\nTotal API calls: ${apiCalls.length}`);

  // Try to extract result count from the page
  const resultCount = await page.evaluate(() => {
    const el = document.querySelector('.js-num-total-programmes, [data-max-count]');
    return el ? (el.textContent || el.getAttribute('data-max-count')) : null;
  });
  console.log(`Result count from page: ${resultCount}`);

  await browser.close();
})();
