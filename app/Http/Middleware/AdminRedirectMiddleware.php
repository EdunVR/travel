<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminRedirectMiddleware
{
    /**
     * Handle an incoming request.
     * Redirect semua request ke admin area kecuali yang dikecualikan
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if admin redirect is enabled
        if (!config('admin_redirect.enabled', true)) {
            return $next($request);
        }

        // Log request untuk debugging
        if (config('admin_redirect.debug', false)) {
            Log::info('🔍 [ADMIN-REDIRECT] Request received', [
                'url' => $request->fullUrl(),
                'path' => $request->path(),
                'method' => $request->method(),
                'user_agent' => $request->userAgent(),
                'is_authenticated' => Auth::check(),
                'is_ajax' => $request->ajax(),
                'accepts_json' => $request->acceptsJson()
            ]);
        }

        // Get configuration
        $excludedPaths = config('admin_redirect.excluded_paths', []);
        $excludedExtensions = config('admin_redirect.excluded_extensions', []);
        $excludedUserAgents = config('admin_redirect.excluded_user_agents', []);

        $currentPath = $request->path();
        $pathExtension = pathinfo($currentPath, PATHINFO_EXTENSION);
        $userAgent = strtolower($request->userAgent() ?? '');

        // Skip jika user agent dikecualikan (bots, crawlers, etc)
        foreach ($excludedUserAgents as $excludedAgent) {
            if (str_contains($userAgent, strtolower($excludedAgent))) {
                if (config('admin_redirect.debug', false)) {
                    Log::debug('⏭️ [ADMIN-REDIRECT] Skipping excluded user agent', [
                        'user_agent' => $userAgent,
                        'excluded' => $excludedAgent
                    ]);
                }
                return $next($request);
            }
        }

        // Skip jika request untuk file asset
        if (in_array(strtolower($pathExtension), $excludedExtensions)) {
            if (config('admin_redirect.debug', false)) {
                Log::debug('⏭️ [ADMIN-REDIRECT] Skipping asset file', ['path' => $currentPath]);
            }
            return $next($request);
        }

        // Skip jika AJAX request atau API request
        if ($request->ajax() || $request->acceptsJson() || $request->is('api/*')) {
            if (config('admin_redirect.debug', false)) {
                Log::debug('⏭️ [ADMIN-REDIRECT] Skipping AJAX/API request', ['path' => $currentPath]);
            }
            return $next($request);
        }

        // Skip jika method bukan GET
        if (!$request->isMethod('GET')) {
            if (config('admin_redirect.debug', false)) {
                Log::debug('⏭️ [ADMIN-REDIRECT] Skipping non-GET request', [
                    'method' => $request->method(),
                    'path' => $currentPath
                ]);
            }
            return $next($request);
        }

        // Skip jika path dikecualikan
        foreach ($excludedPaths as $excludedPath) {
            if (str_starts_with($currentPath, $excludedPath)) {
                if (config('admin_redirect.debug', false)) {
                    Log::debug('⏭️ [ADMIN-REDIRECT] Skipping excluded path', [
                        'path' => $currentPath,
                        'excluded' => $excludedPath
                    ]);
                }
                return $next($request);
            }
        }

        // Skip jika user belum login
        if (!Auth::check()) {
            if (config('admin_redirect.debug', false)) {
                Log::info('⏭️ [ADMIN-REDIRECT] User not authenticated, allowing normal flow', [
                    'path' => $currentPath
                ]);
            }
            return $next($request);
        }

        // Skip jika sudah di admin area
        if (str_starts_with($currentPath, 'admin')) {
            if (config('admin_redirect.debug', false)) {
                Log::debug('⏭️ [ADMIN-REDIRECT] Already in admin area', ['path' => $currentPath]);
            }
            return $next($request);
        }

        // REDIRECT KE ADMIN
        Log::info('🔄 [ADMIN-REDIRECT] Redirecting to admin dashboard', [
            'from_path' => $currentPath,
            'from_url' => $request->fullUrl(),
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name ?? 'Unknown'
        ]);

        // Get admin route from config
        $adminRoute = config('admin_redirect.admin_route', 'admin.dashboard');

        // Redirect dengan flash message untuk user experience
        return redirect()->route($adminRoute)->with([
            'redirect_info' => [
                'message' => 'Anda telah diarahkan ke dashboard admin',
                'original_url' => $request->fullUrl(),
                'timestamp' => now()->toDateTimeString(),
                'reason' => 'browser_reload_redirect'
            ]
        ]);
    }
}