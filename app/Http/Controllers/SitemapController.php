<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate and return the XML sitemap.
     */
    public function index(): Response
    {
        $products = Product::where('status', 'active')
            ->select(['id', 'name', 'updated_at'])
            ->latest('updated_at')
            ->get();

        $categories = Category::where('status', 'active')
            ->select(['id', 'slug', 'updated_at'])
            ->get();

        $content = view('sitemap', compact('products', 'categories'))->render();

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Return a proper robots.txt file.
     */
    public function robots(): Response
    {
        $sitemapUrl = config('app.url') . '/sitemap.xml';

        $content = view('robots', compact('sitemapUrl'))->render();

        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }
}
