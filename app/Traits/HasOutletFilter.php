<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

trait HasOutletFilter
{
    /**
     * Apply outlet filter to query based on user access
     *
     * @param Builder $query
     * @param string $outletColumn Column name for outlet_id (default: 'outlet_id')
     * @return Builder
     */
    protected function applyOutletFilter(Builder $query, string $outletColumn = 'outlet_id'): Builder
    {
        $user = auth()->user();

        // Super admin can see all outlets
        if ($user->hasRole('super_admin')) {
            return $query;
        }

        // Filter by user's accessible outlets
        $outletIds = $user->outlets->pluck('id_outlet')->toArray();
        
        if (empty($outletIds)) {
            // If user has no outlet access, return empty result
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn($outletColumn, $outletIds);
    }

    /**
     * Get user's accessible outlet IDs
     *
     * @return array
     */
    protected function getUserOutletIds(): array
    {
        $user = auth()->user();

        // Super admin has access to all outlets
        if ($user->hasRole('super_admin')) {
            return \App\Models\Outlet::pluck('id_outlet')->toArray();
        }

        // Use akses_outlet property if available (legacy system)
        if ($user->akses_outlet && is_array($user->akses_outlet)) {
            return $user->akses_outlet;
        }

        // Fallback to outlets relation (new system)
        return $user->outlets->pluck('id_outlet')->toArray();
    }

    /**
     * Get user's accessible outlets
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getUserOutlets()
    {
        $user = auth()->user();

        // Super admin has access to all outlets
        if ($user->hasRole('super_admin')) {
            return \App\Models\Outlet::all();
        }

        // Use akses_outlet property if available (legacy system)
        if ($user->akses_outlet && is_array($user->akses_outlet)) {
            return \App\Models\Outlet::whereIn('id_outlet', $user->akses_outlet)->get();
        }

        // Fallback to outlets relation (new system)
        return $user->outlets;
    }

    /**
     * Check if user has access to specific outlet
     *
     * @param int $outletId
     * @return bool
     */
    protected function hasOutletAccess(int $outletId): bool
    {
        $user = auth()->user();

        // Super admin has access to all outlets
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->hasAccessToOutlet($outletId);
    }

    /**
     * Get selected outlet ID from request or session
     *
     * @param \Illuminate\Http\Request|null $request
     * @return int|string|null Returns outlet ID, 'ALL' for all outlets, or null
     */
    protected function getSelectedOutlet($request = null)
    {
        $request = $request ?? request();
        
        Log::info('🏢 [OUTLET FILTER] Getting selected outlet', [
            'request_outlet_id' => $request->get('outlet_id'),
            'session_outlet_id' => session('selected_outlet_id'),
            'user_id' => auth()->id(),
            'request_url' => $request->fullUrl()
        ]);
        
        // Get from request parameter first
        if ($request->has('outlet_id')) {
            $outletId = $request->outlet_id;
            
            // Handle "ALL" or empty string for all outlets
            if ($outletId === 'ALL' || $outletId === '' || $outletId === null) {
                session(['selected_outlet_id' => 'ALL']);
                Log::info('✅ [OUTLET FILTER] All outlets selected', [
                    'outlet_id' => 'ALL',
                    'saved_to_session' => true
                ]);
                return 'ALL';
            }
            
            session(['selected_outlet_id' => $outletId]);
            Log::info('✅ [OUTLET FILTER] Outlet from request', [
                'outlet_id' => $outletId,
                'saved_to_session' => true
            ]);
            return $outletId;
        }

        // Get from session
        if (session()->has('selected_outlet_id')) {
            $outletId = session('selected_outlet_id');
            Log::info('✅ [OUTLET FILTER] Outlet from session', [
                'outlet_id' => $outletId
            ]);
            return $outletId;
        }

        // Default to ALL outlets for super admin, first outlet for others
        if ($this->isSuperAdmin()) {
            session(['selected_outlet_id' => 'ALL']);
            Log::info('✅ [OUTLET FILTER] Super admin - defaulting to ALL outlets', [
                'outlet_id' => 'ALL',
                'saved_to_session' => true
            ]);
            return 'ALL';
        }
        
        // Get first accessible outlet for non-super admin
        $outlets = $this->getAccessibleOutlets();
        Log::info('🔍 [OUTLET FILTER] Getting accessible outlets', [
            'total_outlets' => $outlets->count(),
            'outlet_ids' => $outlets->pluck('id_outlet')->toArray()
        ]);
        
        if ($outlets->isNotEmpty()) {
            $outletId = $outlets->first()->id_outlet;
            session(['selected_outlet_id' => $outletId]);
            Log::info('✅ [OUTLET FILTER] Using first accessible outlet', [
                'outlet_id' => $outletId,
                'outlet_name' => $outlets->first()->nama_outlet,
                'saved_to_session' => true
            ]);
            return $outletId;
        }

        Log::warning('⚠️ [OUTLET FILTER] No outlet found', [
            'user_id' => auth()->id(),
            'accessible_outlets_count' => $outlets->count()
        ]);
        return null;
    }

    /**
     * Get user's accessible outlets collection
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getAccessibleOutlets()
    {
        return $this->getUserOutlets();
    }

    /**
     * Get user's accessible outlet IDs
     *
     * @return array
     */
    protected function getAccessibleOutletIds(): array
    {
        return $this->getUserOutletIds();
    }

    /**
     * Check if current user is super admin
     *
     * @return bool
     */
    protected function isSuperAdmin(): bool
    {
        $user = auth()->user();
        return $user && $user->hasRole('super_admin');
    }

    /**
     * Validate outlet access and throw 403 if unauthorized
     *
     * @param int $outletId
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function validateOutletAccess(int $outletId): void
    {
        if (!$this->isSuperAdmin()) {
            $accessibleIds = $this->getAccessibleOutletIds();
            if (!in_array($outletId, $accessibleIds)) {
                abort(403, 'Anda tidak memiliki akses ke outlet ini.');
            }
        }
    }
}
