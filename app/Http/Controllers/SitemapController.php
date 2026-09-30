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
            ->with(['primaryImage', 'images'])
            ->latest('updated_at')
            ->get();

        $categories = Category::where('status', 'active')
            ->select(['id', 'slug', 'updated_at'])
            ->get();

        $pages = class_exists(\App\Models\Page::class) 
            ? \App\Models\Page::where('is_published', true)->select(['id', 'slug', 'updated_at'])->get()
            : collect();

        $content = view('sitemap', compact('products', 'categories', 'pages'))->render();

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Return a proper robots.txt file.
     */
    public function robots(): Response
    {
        $sitemapUrl = url('/sitemap.xml');

        $content = view('robots', compact('sitemapUrl'))->render();

        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }
}
