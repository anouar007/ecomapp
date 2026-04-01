<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class FeedController extends Controller
{
    /**
     * Generate product feed for Google Merchant Center / Facebook Catalog.
     */
    public function products()
    {
        $products = Product::where('status', 'active')->with(['productCategory'])->get();
        
        $xml = view('frontend.feed.products', compact('products'))->render();
        
        return Response::make($xml, 200, [
            'Content-Type' => 'application/xml',
            'Charset' => 'utf-8',
        ]);
    }
}
