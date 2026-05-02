<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PreventSessionConflicts
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
        try {
            // Ensure session is properly started
            if (!Session::isStarted()) {
                Session::start();
            }

            // Check for session conflicts or corruption
            $sessionId = Session::getId();
            if (empty($sessionId)) {
                Log::warning('⚠️ [SESSION] Empty session ID detected, regenerating', [
                    'url' => $request->fullUrl(),
                    'ip' => $request->ip()
                ]);
                
                Session::regenerate(true);
            }

            // Validate session token
            if (!Session::token()) {
                Session::regenerateToken();
                Log::info('🔄 [SESSION] Regenerated missing session token');
            }

            // Clean up old session data that might cause conflicts
            $this->cleanupOldSessionData();

            return $next($request);

        } catch (\Exception $e) {
            Log::error('💥 [SESSION] Session middleware error', [
                'error' => $e->getMessage(),
                'url' => $request->fullUrl(),
                'trace' => $e->getTraceAsString()
            ]);

            // Try to recover by starting fresh session
            try {
                Session::flush();
                Session::regenerate(true);
                Log::info('🔄 [SESSION] Recovered with fresh session');
            } catch (\Exception $recoveryError) {
                Log::error('💥 [SESSION] Failed to recover session', [
                    'error' => $recoveryError->getMessage()
                ]);
            }

            return $next($request);
        }
    }

    /**
     * Clean up old or conflicting session data
     */
    private function cleanupOldSessionData()
    {
        // Remove old flash data that might cause conflicts
        $flashKeys = Session::get('_flash.old', []);
        foreach ($flashKeys as $key) {
            if (Session::has($key)) {
                Session::forget($key);
            }
        }

        // Clean up old intended URL if it's stale (older than 1 hour)
        if (Session::has('url.intended')) {
            $intendedTime = Session::get('url.intended.time', 0);
            if (time() - $intendedTime > 3600) {
                Session::forget('url.intended');
                Session::forget('url.intended.time');
            }
        }
    }
}