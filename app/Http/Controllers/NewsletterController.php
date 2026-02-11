<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:newsletter_subscriptions,email'
        ]);

        DB::table('newsletter_subscriptions')->insert([
            'email' => $request->email,
            'subscribed_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Thanks for subscribing!']);
        }

        return back()->with('success', 'Thanks for subscribing!');
    }
}
