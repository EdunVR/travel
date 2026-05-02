<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserActivityLog;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        
        // Ensure fresh session for login page
        if (!session()->isStarted()) {
            session()->start();
        }
        
        // Add cache control headers to prevent caching of login page
        return response()
            ->view('auth.login')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function login(Request $request)
    {
        // Log login attempt
        \Log::info('Login attempt', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => session()->getId(),
            'csrf_token_present' => $request->has('_token'),
            'csrf_token_valid' => $request->session()->token() === $request->input('_token')
        ]);

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter'
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        // Check if user exists and is active
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            \Log::warning('Login failed: User not found', ['email' => $request->email]);
            return back()->withErrors([
                'email' => 'Email tidak terdaftar'
            ])->withInput($request->only('email'));
        }

        if (!$user->is_active) {
            \Log::warning('Login failed: User not active', ['email' => $request->email, 'user_id' => $user->id]);
            return back()->withErrors([
                'email' => 'Akun Anda tidak aktif. Hubungi administrator.'
            ])->withInput($request->only('email'));
        }

        // Attempt login
        if (Auth::attempt($credentials, $remember)) {
            \Log::info('Login successful', [
                'user_id' => Auth::id(),
                'email' => Auth::user()->email,
                'session_id' => session()->getId()
            ]);

            // Regenerate session to prevent session fixation
            $request->session()->regenerate();
            
            // Regenerate CSRF token
            $request->session()->regenerateToken();

            // Update last login
            try {
                $user->updateLastLogin();
            } catch (\Exception $e) {
                \Log::error('Failed to update last login', ['error' => $e->getMessage()]);
            }

            // Log activity
            try {
                UserActivityLog::log('login', 'User logged in', 'auth');
            } catch (\Exception $e) {
                \Log::error('Failed to log activity', ['error' => $e->getMessage()]);
            }

            \Log::info('Redirecting to dashboard', ['intended_url' => route('admin.dashboard')]);
            return redirect()->intended(route('admin.dashboard'));
        }

        \Log::warning('Login failed: Invalid credentials', ['email' => $request->email]);
        return back()->withErrors([
            'email' => 'Email atau password salah'
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        // Log activity before logout
        UserActivityLog::log('logout', 'User logged out', 'auth');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
