<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $allCategories = Category::where('status', 'active')
            ->orderBy('sort_order')
            ->get();

        $featuredProducts = Product::where('status', 'active')
            ->with(['productCategory', 'primaryImage', 'images', 'variants'])
            ->withCount(['reviews' => fn ($query) => $query->where('status', 'approved')])
            ->withAvg(['reviews' => fn ($query) => $query->where('status', 'approved')], 'rating')
            ->withSum('orderItems', 'quantity')
            ->latest()
            ->take(8)
            ->get();

        return view('storefront.home', compact('allCategories', 'featuredProducts'));
    }
}
