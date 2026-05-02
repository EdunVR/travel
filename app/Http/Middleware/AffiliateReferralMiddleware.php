<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AffiliateTrackingService;

class AffiliateReferralMiddleware
{
    protected $trackingService;

    public function __construct(AffiliateTrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    public function handle(Request $request, Closure $next)
    {
        // Cek apakah ada parameter ref di URL
        if ($request->has('ref')) {
            $referralCode = $request->get('ref');
            
            // Track klik
            $this->trackingService->trackClick(
                $referralCode,
                $request->get('package_id'),
                $request->fullUrl()
            );
        }

        // Get affiliator dari cookie untuk customize konten
        $affiliator = $this->trackingService->getAffiliatorFromCookie();
        
        if ($affiliator) {
            // Share affiliator data ke semua views
            view()->share('affiliator', $affiliator);
            view()->share('isAffiliateReferral', true);
        } else {
            view()->share('isAffiliateReferral', false);
        }

        return $next($request);
    }
}
