const fs = require("fs");
const puppeteer = require("puppeteer-extra");
const StealthPlugin = require("puppeteer-extra-plugin-stealth");

puppeteer.use(StealthPlugin());

// Use command line arguments for target URL and output file
const targetUrl = process.argv[2] || "https://www.youtube.com/@svishyderabad/videos";
const outputFile = process.argv[3] || "youtube-videos.json";

(async () => {
  const browser = await puppeteer.launch({
    headless: false, // Set to true to run in background
    defaultViewport: null,
    args: [
      "--no-sandbox",
      "--disable-setuid-sandbox",
      "--disable-blink-features=AutomationControlled",
    ],
  });

  const page = await browser.newPage();

  // Set a realistic User Agent
  await page.setUserAgent(
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36"
  );

  console.log(`Opening channel: ${targetUrl}...`);

  await page.goto(targetUrl, {
    waitUntil: "networkidle2",
    timeout: 0,
  });

  // Wait for page render
  await new Promise((resolve) => setTimeout(resolve, 5000));

  console.log("Scrolling to load videos...");

  // Auto scroll to load more videos (adjust loop count for more history)
  for (let i = 0; i < 30; i++) {
    await page.evaluate(() => {
      window.scrollBy(0, window.innerHeight);
    });
    await new Promise((resolve) => setTimeout(resolve, 1500));
  }

  // Final wait
  await new Promise((resolve) => setTimeout(resolve, 3000));

  console.log("Extracting video data...");

  const videos = await page.evaluate(() => {
    // Try multiple possible card selectors to handle YouTube layout variations
    const cards = [
      ...document.querySelectorAll("ytd-rich-item-renderer, yt-lockup-view-model, ytd-grid-video-renderer")
    ];

    const unique = [];

    cards.forEach((card) => {
      // Try multiple possible title/link selectors
      const titleElement = card.querySelector(
        "a.ytLockupMetadataViewModelTitle, a#video-title-link, a#video-title"
      );

      if (!titleElement) return;

      const href = titleElement.href || "";
      
      // Extract title
      let title = titleElement.textContent?.trim() || titleElement.getAttribute("title") || "";

      // Thumbnail
      const thumbnailElement = card.querySelector("yt-thumbnail-view-model img, img");
      const thumbnail = thumbnailElement?.src || thumbnailElement?.getAttribute("src") || "";

      // Ensure it's a watch link and unique
      if (
        href.includes("/watch?v=") &&
        title &&
        !unique.find((v) => v.videoUrl === href)
      ) {
        unique.push({
          videoUrl: href,
          videoTitle: title,
          thumbnail: thumbnail,
        });
      }
    });

    return unique;
  });

  console.log(`Successfully found ${videos.length} videos.`);

  // Save to JSON
  fs.writeFileSync(
    outputFile,
    JSON.stringify(videos, null, 2)
  );

  console.log(`Results saved to ${outputFile}`);

  await browser.close();
})();