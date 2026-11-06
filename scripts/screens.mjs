// Dev helper: phone (360) and desktop (1440) screenshots of key pages, plus an overflow check.
// node scripts/screens.mjs <outdir>   (dev server on 8134, seeded with php artisan migrate:fresh --seed)
import puppeteer from 'puppeteer-core';
import fs from 'node:fs';
const OUT = process.argv[2] || 'dist/screens';
const BASE = process.env.BASE || 'http://127.0.0.1:8134';
const FULL = process.env.FULL !== '0';
fs.mkdirSync(OUT, { recursive: true });
const pub = (process.env.PAGES || '/,/textures,/textures/brazilian-kinky-curly,/textures/mongolian-afro-coily,/textures/brazilian-textured-straight,/pre-order,/texture-match,/clip-ins,/queen-cam,/care,/faq,/our-story,/track,/privacy').split(',');
const adm = (process.env.ADMIN ?? '/admin,/admin/requests,/admin/prices,/admin/prices/brazilian-kinky-curly,/admin/photos,/admin/settings,/admin/team').split(',').filter(Boolean);
const widths = (process.env.WIDTHS || '360x780:phone,1440x900:desk').split(',').map((s) => { const [d, tag] = s.split(':'); const [w, h] = d.split('x').map(Number); return [w, h, tag]; });
const browser = await puppeteer.launch({ executablePath: '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome', headless: 'new' });
const problems = [];
for (const [vw, vh, tag] of widths) {
  const page = await browser.newPage();
  await page.setViewport({ width: vw, height: vh, deviceScaleFactor: 1 });
  // Skip the once-per-session splash so it never covers a screenshot.
  await page.evaluateOnNewDocument(() => sessionStorage.setItem('kq-splash', '1'));
  for (const url of [...pub, 'LOGIN', ...adm]) {
    if (url === 'LOGIN') {
      if (!adm.length) continue;
      await page.goto(BASE + '/admin/login');
      if (!page.url().endsWith('/login')) continue;
      await page.type('input[name=email]', 'owner@kinksandqueens.test');
      await page.type('input[name=password]', 'password123');
      await Promise.all([page.waitForNavigation(), page.click('main form button')]);
      continue;
    }
    await page.goto(BASE + url, { waitUntil: 'networkidle0' });
    await page.evaluate(async () => {
      document.querySelectorAll('.reveal').forEach((e) => e.classList.add('is-in'));
      document.querySelectorAll('img[loading=lazy]').forEach((i) => { i.loading = 'eager'; });
      await Promise.all([...document.images].map((i) => (i.complete ? 0 : new Promise((r) => { i.onload = i.onerror = r; }))));
      await document.fonts.ready;
    });
    await new Promise((r) => setTimeout(r, 400));
    const over = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
    if (over > 1) problems.push(`${tag} ${url}: ${over}px wider than the screen`);
    const small = await page.evaluate(() => [...document.querySelectorAll('a[href], button, input:not([type=hidden]), select')].filter((e) => {
      const r = e.getBoundingClientRect(); const s = getComputedStyle(e);
      return r.width > 0 && r.height > 0 && s.visibility !== 'hidden' && r.height < 24 && !e.closest('p, label, span.text-cocoa, dd, td, figcaption, nav[aria-label=Breadcrumb]');
    }).map((e) => (e.textContent || e.name || e.type).trim().slice(0, 30)));
    if (small.length) problems.push(`${tag} ${url}: small targets ${[...new Set(small)].slice(0, 5).join(' | ')}`);
    const name = (url === '/' ? 'home' : url.replace(/[^a-z0-9]+/gi, '-').replace(/^-|-$/g, '')) || 'home';
    await page.screenshot({ path: `${OUT}/${tag}-${name}.png`, fullPage: FULL });
  }
  await page.close();
}
await browser.close();
if (problems.length) { console.log(problems.join('\n')); process.exit(1); }
console.log('ok');
