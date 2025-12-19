
import puppeteer from 'puppeteer';
import fs from 'fs';
import path from 'path';

(async () => {
    const browser = await puppeteer.launch({ headless: true }); // Ensure headless is true for background execution
    const page = await browser.newPage();
    await page.setViewport({ width: 1366, height: 768 });

    const baseUrl = 'http://127.0.0.1:8000';
    const guideDir = 'public/images/guide';

    if (!fs.existsSync(guideDir)) {
        fs.mkdirSync(guideDir, { recursive: true });
    }

    try {
        console.log('Navigating to Login...');
        await page.goto(baseUrl, { waitUntil: 'networkidle2' });

        // Screenshot Login
        await page.screenshot({ path: path.join(guideDir, 'login.png') });
        console.log('Saved login.png');

        // Perform Login
        console.log('Logging in...');
        await page.type('#email', 'admin@apotek.com');
        await page.type('#password', 'password');

        // Find login button - adjust selector if needed. Usually type="submit" in Breeze
        const submitButton = await page.$('button[type="submit"]');
        if (submitButton) {
            await Promise.all([
                page.waitForNavigation({ waitUntil: 'networkidle2' }),
                submitButton.click(),
            ]);
        } else {
            console.error('Login button not found');
            await browser.close();
            return;
        }

        // Screenshot Dashboard
        console.log('Capturing Dashboard...');
        await page.screenshot({ path: path.join(guideDir, 'dashboard.png') });

        // Screenshot Products
        console.log('Capturing Products...');
        await page.goto(`${baseUrl}/products`, { waitUntil: 'networkidle2' });
        await page.screenshot({ path: path.join(guideDir, 'product.png') });

        // Screenshot POS
        console.log('Capturing POS...');
        await page.goto(`${baseUrl}/cashier`, { waitUntil: 'networkidle2' });
        // Wait a bit for products to load if async
        await new Promise(r => setTimeout(r, 2000));
        await page.screenshot({ path: path.join(guideDir, 'pos.png') });

        // Screenshot Reports
        console.log('Capturing Reports...');
        await page.goto(`${baseUrl}/reports/sales`, { waitUntil: 'networkidle2' });
        await page.screenshot({ path: path.join(guideDir, 'report.png') });

        // Screenshot Procurement (PO)
        console.log('Capturing PO...');
        await page.goto(`${baseUrl}/procurement/purchase-orders`, { waitUntil: 'networkidle2' });
        await page.screenshot({ path: path.join(guideDir, 'po.png') });

        console.log('All screenshots captured successfully.');

    } catch (e) {
        console.error('Error capturing screenshots:', e);
    } finally {
        await browser.close();
    }
})();
