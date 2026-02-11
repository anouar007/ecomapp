<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CustomerDashboardController extends Controller
{
    /**
     * Show the dashboard index.
     */
    public function index()
    {
        $user = Auth::user();
        $recentOrders = Order::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('frontend.dashboard.index', compact('user', 'recentOrders'));
    }

    /**
     * Show user orders.
     */
    public function orders()
    {
        $orders = Order::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('frontend.dashboard.orders', compact('orders'));
    }

    /**
     * Show order details.
     */
    public function orderShow(Order $order)
    {
        // AUTHORIZATION CHECK
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('frontend.dashboard.order-show', compact('order'));
    }

    /**
     * Show profile edit form.
     */
    public function profile()
    {
        $user = Auth::user();
        return view('frontend.dashboard.profile', compact('user'));
    }

    /**
     * Update profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }
}
