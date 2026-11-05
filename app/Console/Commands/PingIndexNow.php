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

        $baseUrl = 'https://winashop.com';
        $this->info("Canonical domain: {$baseUrl}");

        $indexNowKey = SitemapController::ensureIndexNowStaticFiles();
        Setting::set('seo_indexnow_key', $indexNowKey, 'string', 'seo');

        $host = parse_url($baseUrl, PHP_URL_HOST) ?: 'winashop.com';
        $keyLocation = $baseUrl . "/indexnow-{$indexNowKey}.txt";
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
        $this->info("   Key file URL: {$keyLocation}");

        // Pre-verify that the remote production server is serving the key file with 200 OK
        try {
            $verifyResp = Http::timeout(8)->get($keyLocation);
            if (!$verifyResp->successful() || trim($verifyResp->body()) !== $indexNowKey) {
                $this->warn("Note: {$keyLocation} returned HTTP " . $verifyResp->status() . " on the live server.");
                $this->warn("Make sure you have pulled the latest code (git pull origin wina) on the production server winashop.com so that public/indexnow-{$indexNowKey}.txt is live before pinging IndexNow.");
            } else {
                $this->info("   Verified live key file on {$host}: HTTP 200 OK.");
            }
        } catch (\Throwable $e) {
            // Continue to IndexNow submission
        }

        $this->info("3. Sending batch ping to https://api.indexnow.org/indexnow ...");
        try {
            $response = Http::timeout(15)->post('https://api.indexnow.org/indexnow', [
                'host' => $host,
                'key' => $indexNowKey,
                'keyLocation' => $keyLocation,
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

