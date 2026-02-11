<?php

namespace App\Http\Controllers;

use App\Models\ReturnOrder;
use App\Models\Order;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $query = ReturnOrder::with(['order', 'customer', 'processedBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $returns = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'total_returns' => ReturnOrder::count(),
            'pending_returns' => ReturnOrder::pending()->count(),
            'approved_returns' => ReturnOrder::approved()->count(),
            'total_refunded' => ReturnOrder::where('status', 'completed')->sum('refund_amount'),
        ];

        return view('returns.index', compact('returns', 'stats'));
    }

    public function show(ReturnOrder $return)
    {
        $return->load(['order.items', 'customer', 'processedBy']);
        return view('returns.show', compact('return'));
    }

    public function approve(Request $request, ReturnOrder $return)
    {
        $validated = $request->validate([
            'refund_method' => 'required|in:original_payment,store_credit,exchange',
            'admin_notes' => 'nullable|string',
        ]);

        $return->approve(Auth::id(), $validated['refund_method'], $validated['admin_notes'] ?? null);
        ActivityLog::log('approved_return', $return, $validated, 'Approved return ' . $return->return_number);
        
        return back()->with('success', 'Return approved successfully!');
    }

    public function reject(Request $request, ReturnOrder $return)
    {
        $validated = $request->validate([
            'admin_notes' => 'required|string',
        ]);

        $return->reject(Auth::id(), $validated['admin_notes']);
        ActivityLog::log('rejected_return', $return, $validated, 'Rejected return ' . $return->return_number);
        
        return back()->with('success', 'Return rejected.');
    }

    public function complete(ReturnOrder $return)
    {
        $return->complete();
        ActivityLog::log('completed_return', $return, [], 'Completed return ' . $return->return_number);
        
        return back()->with('success', 'Return marked as completed!');
    }
}
