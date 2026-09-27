const puppeteer = require('puppeteer');
const fs = require('fs');

(async () => {
    const browser = await puppeteer.launch({ headless: 'new' });
    const page = await browser.newPage();
    await page.setViewport({ width: 1920, height: 1080 });

    const pagesToScreenshot = [
        { url: 'http://localhost/BMI/index.php', name: 'index_page' }
    ];

    for (const p of pagesToScreenshot) {
        console.log(`Navigating to ${p.url}...`);
        await page.goto(p.url, { waitUntil: 'networkidle2' });
        
        // Scroll down to trigger GSAP animations
        await page.evaluate(async () => {
            await new Promise((resolve) => {
                let totalHeight = 0;
                let distance = 100;
                let timer = setInterval(() => {
                    let scrollHeight = document.body.scrollHeight;
                    window.scrollBy(0, distance);
                    totalHeight += distance;

                    if(totalHeight >= scrollHeight){
                        clearInterval(timer);
                        resolve();
                    }
                }, 100);
            });
        });
        
        // Scroll back to top
        await page.evaluate(() => window.scrollTo(0, 0));
        
        // Wait a bit for animations to settle
        await new Promise(resolve => setTimeout(resolve, 2000));
        
        const path = `C:\\Users\\User\\.gemini\\antigravity-ide\\brain\\913d42a8-4fdb-4cb4-9faa-dcb195900735\\scratch\\${p.name}.png`;
        await page.screenshot({ path: path, fullPage: true });
        console.log(`Saved screenshot to ${path}`);
    }

    await browser.close();
})();
