<?php

namespace App\Http\Controllers;

use App\Models\ProductReview;
use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    public function store(Request $request)
    {
        // Validate based on authentication status
        $rules = [
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'comment' => 'required|string',
        ];

        // Add name/email validation for guest users
        if (!Auth::check()) {
            $rules['customer_name'] = 'required|string|max:255';
            $rules['customer_email'] = 'required|email|max:255';
        }

        $request->validate($rules);

        // Use authenticated user data if logged in, otherwise use submitted form data
        $customerName = Auth::check() ? Auth::user()->name : $request->customer_name;
        $customerEmail = Auth::check() ? Auth::user()->email : $request->customer_email;

        ProductReview::create([
            'product_id' => $request->product_id,
            'customer_name' => $customerName,
            'customer_email' => $customerEmail,
            'rating' => $request->rating,
            'title' => $request->title,
            'comment' => $request->comment,
            'status' => 'pending', // Requires admin approval
            'verified_purchase' => false, // Could check orders here
        ]);

        return back()->with('success', 'Review submitted! It will appear after approval.');
    }

    public function index(Request $request)
    {
        $query = ProductReview::with(['product']);

        // Search filter
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('customer_name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('customer_email', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('title', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('comment', 'LIKE', '%' . $request->search . '%')
                  ->orWhereHas('product', function($q2) use ($request) {
                      $q2->where('name', 'LIKE', '%' . $request->search . '%');
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Rating filter
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Product filter
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get products for filter dropdown
        $products = \App\Models\Product::orderBy('name')->get();

        // Calculate statistics
        $stats = [
            'total_reviews' => ProductReview::count(),
            'pending_reviews' => ProductReview::pending()->count(),
            'approved_reviews' => ProductReview::approved()->count(),
            'average_rating' => round(ProductReview::approved()->avg('rating') ?? 0, 1),
        ];

        return view('reviews.index', compact('reviews', 'stats', 'products'));
    }

    public function approve(ProductReview $review)
    {
        $review->approve(Auth::id());
        ActivityLog::log('approved_review', $review, [], 'Approved product review #' . $review->id);
        
        if (request()->ajax()) {
            $stats = [
                'total_reviews' => ProductReview::count(),
                'pending_reviews' => ProductReview::pending()->count(),
                'approved_reviews' => ProductReview::approved()->count(),
                'average_rating' => round(ProductReview::approved()->avg('rating') ?? 0, 1),
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Review approved successfully!',
                'stats' => $stats,
                'review' => $review->load('product')
            ]);
        }
        
        return back()->with('success', 'Review approved successfully!');
    }

    public function reject(ProductReview $review)
    {
        $review->reject();
        ActivityLog::log('rejected_review', $review, [], 'Rejected product review #' . $review->id);
        
        if (request()->ajax()) {
            $stats = [
                'total_reviews' => ProductReview::count(),
                'pending_reviews' => ProductReview::pending()->count(),
                'approved_reviews' => ProductReview::approved()->count(),
                'average_rating' => round(ProductReview::approved()->avg('rating') ?? 0, 1),
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Review rejected successfully!',
                'stats' => $stats,
                'review' => $review->load('product')
            ]);
        }
        
        return back()->with('success', 'Review rejected successfully!');
    }

    public function destroy(ProductReview $review)
    {
        $reviewId = $review->id;
        $review->delete();
        ActivityLog::log('deleted_review', null, ['review_id' => $reviewId], 'Deleted product review');
        
        if (request()->ajax()) {
            $stats = [
                'total_reviews' => ProductReview::count(),
                'pending_reviews' => ProductReview::pending()->count(),
                'approved_reviews' => ProductReview::approved()->count(),
                'average_rating' => round(ProductReview::approved()->avg('rating') ?? 0, 1),
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Review deleted successfully!',
                'stats' => $stats
            ]);
        }
        
        return back()->with('success', 'Review deleted successfully!');
    }
}
