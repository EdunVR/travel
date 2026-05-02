<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule) {
        // Kirim reminder keberangkatan setiap Senin pagi jam 08:00
        $schedule->command('travel:send-reminders')->weeklyOn(1, '08:00');
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\AdminRedirectMiddleware::class, // Add global admin redirect
            \App\Http\Middleware\AffiliateReferralMiddleware::class, // Track affiliate referrals
        ]);

        // Register custom middleware aliases
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'outlet.access' => \App\Http\Middleware\CheckOutletAccess::class,
            'cors' => \App\Http\Middleware\CorsMiddleware::class,
            'admin.redirect' => \App\Http\Middleware\AdminRedirectMiddleware::class,
            'affiliate.tracking' => \App\Http\Middleware\AffiliateTrackingMiddleware::class,
        ]);

        // Add CORS to API routes
        $middleware->api(prepend: [
            \App\Http\Middleware\CorsMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle CSRF token mismatch (419 Page Expired)
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'CSRF token mismatch. Please refresh and try again.',
                    'error' => 'token_mismatch'
                ], 419);
            }
            
            return redirect()->back()
                ->withInput($request->except('password', '_token'))
                ->withErrors(['csrf' => 'Halaman sudah expired. Silakan coba lagi.']);
        });
        
        // Handle authentication exceptions
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->guest(route('login'));
        });
        
        // Handle other exceptions gracefully
        $exceptions->render(function (\Throwable $e, $request) {
            // If error contains "Call to a member function" and user is not authenticated
            if (str_contains($e->getMessage(), 'Call to a member function') && !auth()->check()) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Session expired. Please login again.'], 401);
                }
                return redirect()->route('login')->with('error', 'Session Anda telah berakhir. Silakan login kembali.');
            }
            
            // Let other exceptions be handled normally
            return null;
        });
    })->create();
