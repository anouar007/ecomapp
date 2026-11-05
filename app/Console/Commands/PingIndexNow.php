<?php

namespace App\Console\Commands;

use App\Http\Controllers\SitemapController;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PingIndexNow extends Command
{
    protected $signature = 'seo:ping-index {--limit=500 : Maximum product URLs to submit}';
    protected $description = 'Purge SEO caches and submit all canonical site URLs to IndexNow (Bing, ChatGPT Search, Perplexity, Yandex)';

    public function handle(): int
    {
        $this->info('1. Purging SEO caches...');
        $cacheKeys = ['seo_sitemap_xml', 'seo_google_merchant_xml', 'seo_llms_txt', 'seo_llms_full_txt', 'seo_ai_catalog_json'];
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
            Cache::store('file')->forget($key);
        }

        $baseUrl = SitemapController::getCanonicalBaseUrl();
        $this->info("Canonical domain: {$baseUrl}");

        $indexNowKey = setting('seo_indexnow_key');
        if (!$indexNowKey) {
            $indexNowKey = substr(md5(config('app.key', 'winashop') . '_indexnow_wina'), 0, 32);
            Setting::set('seo_indexnow_key', $indexNowKey, 'string', 'seo');
        }

        $host = parse_url($baseUrl, PHP_URL_HOST) ?: 'winashop.com';
        $limit = (int) $this->option('limit');

        $urls = [
            $baseUrl . '/',
            $baseUrl . '/shop',
            $baseUrl . '/about',
            $baseUrl . '/contact',
            $baseUrl . '/sitemap.xml',
            $baseUrl . '/llms.txt',
            $baseUrl . '/ai-catalog.json',
        ];

        $products = Product::where('status', 'active')
            ->orderBy('updated_at', 'desc')
            ->take($limit)
            ->get(['id', 'slug']);

        foreach ($products as $prod) {
            $urls[] = $baseUrl . '/shop/' . ($prod->slug ?: $prod->id);
        }

        $uniqueUrls = array_values(array_unique($urls));
        $this->info("2. Prepared " . count($uniqueUrls) . " canonical URLs for submission.");

        if (in_array($host, ['localhost', '127.0.0.1'], true)) {
            $this->warn("Local environment detected. Skipping remote IndexNow HTTP POST.");
            return 0;
        }

        $this->info("3. Sending batch ping to https://api.indexnow.org/indexnow ...");
        try {
            $response = Http::timeout(10)->post('https://api.indexnow.org/indexnow', [
                'host' => $host,
                'key' => $indexNowKey,
                'keyLocation' => $baseUrl . "/indexnow-{$indexNowKey}.txt",
                'urlList' => $uniqueUrls,
            ]);

            if ($response->successful()) {
                $this->info("IndexNow accepted submission successfully! (HTTP " . $response->status() . ")");
            } else {
                $this->warn("IndexNow response code: " . $response->status() . " - " . $response->body());
            }
        } catch (\Throwable $e) {
            $this->error("Failed to connect to IndexNow API: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
