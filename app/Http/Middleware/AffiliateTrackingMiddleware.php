<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AffiliateTrackingService;

class AffiliateTrackingMiddleware
{
    protected $trackingService;

    public function __construct(AffiliateTrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah ada parameter ref di URL
        if ($request->has('ref')) {
            $referralCode = $request->get('ref');
            $packageId = $request->get('package_id');
            $landingPage = $request->fullUrl();

            // Track klik
            $this->trackingService->trackClick($referralCode, $packageId, $landingPage);
        }

        return $next($request);
    }
}
