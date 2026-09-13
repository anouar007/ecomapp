import { copyFile, mkdir } from 'node:fs/promises';

const destination = new URL('../public/vendor/swiper/', import.meta.url);
await mkdir(destination, { recursive: true });
for (const file of ['swiper-bundle.min.js', 'swiper-bundle.min.css', 'LICENSE']) {
    await copyFile(new URL(`../node_modules/swiper/${file}`, import.meta.url), new URL(file, destination));
}
