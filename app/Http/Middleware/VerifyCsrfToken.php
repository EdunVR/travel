<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Session\TokenMismatchException;
use Closure;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Add any routes that should be excluded from CSRF verification
        'api/*',
        // Affiliate AJAX endpoints
        'admin/inventaris/affiliate/hierarchy/*',
        'admin/inventaris/affiliate/*/update-hierarchy',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Illuminate\Session\TokenMismatchException
     */
    public function handle($request, Closure $next)
    {
        try {
            return parent::handle($request, $next);
        } catch (TokenMismatchException $e) {
            // Log CSRF token mismatch for debugging
            \Log::warning('CSRF Token Mismatch', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => $request->session()->getId(),
                'csrf_token' => $request->input('_token'),
                'session_token' => $request->session()->token(),
            ]);

            // If it's a login request, redirect back with helpful message
            if ($request->is('login') && $request->isMethod('post')) {
                return redirect()->route('login')
                    ->withErrors(['csrf' => 'Sesi Anda telah berakhir. Silakan coba login kembali.'])
                    ->withInput($request->only('email'));
            }

            // For other requests, throw the original exception
            throw $e;
        }
    }

    /**
     * Determine if the session and input CSRF tokens match.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function tokensMatch($request)
    {
        $token = $this->getTokenFromRequest($request);

        return is_string($request->session()->token()) &&
               is_string($token) &&
               hash_equals($request->session()->token(), $token);
    }
}