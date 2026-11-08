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
            $user = auth()->user();

            // Check if user account is inactive or suspended
            if ($user->status && $user->status !== 'active') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $statusMsg = $user->status === 'suspended'
                    ? 'Votre compte a été suspendu pour des raisons de sécurité. Veuillez contacter l\'administrateur.'
                    : 'Votre compte est inactif. Veuillez contacter votre administrateur pour réactiver votre accès.';

                throw ValidationException::withMessages([
                    'email' => __($statusMsg),
                ]);
            }

            $request->session()->regenerate();

            // Track last login timestamp and IP
            try {
                $user->update([
                    'last_login_at' => now(),
                    'last_login_ip' => $request->ip(),
                ]);
            } catch (\Throwable $e) {
                \Log::warning('Could not record last_login_at: ' . $e->getMessage());
            }
            
            // Log successful login
            \Log::info('User logged in', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            if ($user->hasRole('Admin') || $user->hasRole('Manager') || $user->hasRole('Staff') || $user->can('view_dashboard')) {
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
