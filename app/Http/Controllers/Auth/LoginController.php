<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        // Sanitize email to prevent injection
        $credentials['email'] = filter_var($credentials['email'], FILTER_SANITIZE_EMAIL);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            // Log successful login
            \Log::info('User logged in', [
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            $user = auth()->user();
            if ($user->hasRole('Admin') || $user->hasRole('Staff')) {
                return redirect()->intended(route('dashboard'));
            }
            
            return redirect()->intended(route('customer.dashboard'));
        }

        // Log failed login attempt
        \Log::warning('Failed login attempt', [
            'email' => $credentials['email'],
            'ip' => $request->ip()
        ]);

        throw ValidationException::withMessages([
            'email' => __('The provided credentials do not match our records.'),
        ]);
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
