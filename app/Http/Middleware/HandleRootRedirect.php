<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class HandleRootRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Only handle root path requests
        if ($request->getPathInfo() !== '/') {
            return $next($request);
        }

        try {
            Log::info('🏠 [ROOT REDIRECT] Processing root URL request', [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip()
            ]);

            // Only allow GET requests for root URL
            if (!$request->isMethod('GET')) {
                Log::warning('❌ [ROOT REDIRECT] Non-GET method not allowed for root URL', [
                    'method' => $request->method(),
                    'url' => $request->fullUrl()
                ]);
                
                return response()->json([
                    'error' => 'Method not allowed for root URL',
                    'allowed_methods' => ['GET'],
                    'redirect_url' => route('login')
                ], 405)->header('Allow', 'GET');
            }

            // Check if user is already authenticated
            if (Auth::check()) {
                Log::info('✅ [ROOT REDIRECT] User authenticated, redirecting to dashboard', [
                    'user_id' => Auth::id(),
                    'user_email' => Auth::user()->email ?? 'unknown'
                ]);
                
                return redirect()->route('admin.dashboard');
            }

            // Validate session health
            if (!Session::isStarted()) {
                Session::start();
            }

            // Clear any stale session data that might cause issues
            if (Session::has('_token') && !Session::token()) {
                Session::regenerateToken();
                Log::info('🔄 [ROOT REDIRECT] Regenerated session token');
            }

            Log::info('🔄 [ROOT REDIRECT] Redirecting to login page');
            return redirect()->route('login');

        } catch (\Exception $e) {
            Log::error('💥 [ROOT REDIRECT] Exception occurred', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => [
                    'method' => $request->method(),
                    'url' => $request->fullUrl(),
                    'headers' => $request->headers->all()
                ]
            ]);

            // Fallback response
            return response()->view('errors.500', [
                'message' => 'Terjadi kesalahan saat memproses permintaan. Silakan coba lagi.',
                'redirect_url' => '/login'
            ], 500);
        }
    }
}