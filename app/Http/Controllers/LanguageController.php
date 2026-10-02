<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    protected array $supportedLocales = ['fr', 'ar'];

    public function switch(Request $request, string $locale)
    {
        if (!in_array($locale, $this->supportedLocales)) {
            $locale = 'fr';
        }

        session(['locale' => $locale]);

        return redirect()->back()->withHeaders([
            'Vary' => 'Accept-Language',
        ]);
    }
}
