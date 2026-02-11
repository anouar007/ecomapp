<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    /**
     * Render a dynamic page based on its slug
     */
    public function show($slug = 'home')
    {
        $page = Page::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('frontend.page', compact('page'));
    }
}
