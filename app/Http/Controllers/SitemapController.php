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
        $llmsUrl = url('/llms.txt');
        $llmsFullUrl = url('/llms-full.txt');

        $content = view('robots', compact('sitemapUrl', 'llmsUrl', 'llmsFullUrl'))->render();

        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Generate and return the llms.txt file for Generative Engine Optimization (AI Search / LLMs).
     */
    public function llmsTxt(): Response
    {
        $categories = Category::where('status', 'active')
            ->withCount(['products' => function($q) {
                $q->where('status', 'active');
            }])
            ->get();

        $featuredProducts = Product::where('status', 'active')
            ->with(['category'])
            ->orderByDesc('id')
            ->take(150)
            ->get();

        $totalProducts = Product::where('status', 'active')->count();

        $content = view('llms', compact('featuredProducts', 'categories', 'totalProducts'))->render();

        return response($content, 200)
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }

    /**
     * Generate and return the llms-full.txt file for complete inventory ingestion by AI models.
     */
    public function llmsFullTxt(): Response
    {
        $categories = Category::where('status', 'active')
            ->with(['products' => function($q) {
                $q->where('status', 'active')->orderBy('name');
            }])
            ->get();

        $totalProducts = Product::where('status', 'active')->count();

        $content = view('llms-full', compact('categories', 'totalProducts'))->render();

        return response($content, 200)
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }
}
