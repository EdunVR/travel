<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOutletAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Super Admin has access to all outlets
        if ($user->hasRole('Super Admin')) {
            return $next($request);
        }

        // Check if outlet_id is in request
        $outletId = $request->input('outlet_id') ?? $request->route('outlet_id');
        
        if ($outletId && !$user->hasOutletAccess($outletId)) {
            abort(403, 'Anda tidak memiliki akses ke outlet ini.');
        }

        return $next($request);
    }
}
