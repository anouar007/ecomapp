<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $wishlistItems = Wishlist::with('product.images')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(12);

        return view('frontend.wishlist.index', compact('wishlistItems'));
    }

    /**
     * Toggle item in wishlist.
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $user = Auth::user();
        $productId = $request->product_id;

        $wishlist = Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            $status = 'removed';
            $message = 'Product removed from wishlist.';
        } else {
            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $productId,
            ]);
            $status = 'added';
            $message = 'Product added to wishlist.';
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => $status,
                'message' => $message,
                'count' => $user->wishlist()->count() // Assuming relationship exists on User
            ]);
        }

        return back()->with('success', $message);
    }
}
