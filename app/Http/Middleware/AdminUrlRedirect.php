<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminUrlRedirect
{
    /**
     * Handle an incoming request.
     * Ensures all admin area requests maintain consistent URL structure
     */
    public function handle(Request $request, Closure $next)
    {
        // Only apply to authenticated users
        if (!Auth::check()) {
            return $next($request);
        }
        
        $path = $request->path();
        $url = $request->url();
        
        // Skip if already in admin area or special routes
        if (str_contains($path, 'admin') || 
            str_contains($path, 'api/') ||
            str_contains($path, 'login') ||
            str_contains($path, 'logout') ||
            str_contains($path, 'broadcasting/') ||
            $request->ajax() ||
            $request->wantsJson()) {
            return $next($request);
        }
        
        // For authenticated users accessing non-admin routes, redirect to admin
        if (Auth::check() && !str_contains($path, 'admin')) {
            // Preserve the intended route as a parameter
            $adminUrl = '/admin';
            
            // If it's a specific page, try to map it to admin equivalent
            if ($path !== '/') {
                // Try to find admin equivalent route
                $adminEquivalent = $this->mapToAdminRoute($path);
                if ($adminEquivalent) {
                    $adminUrl = $adminEquivalent;
                }
            }
            
            return redirect($adminUrl);
        }
        
        return $next($request);
    }
    
    /**
     * Map regular routes to their admin equivalents
     */
    private function mapToAdminRoute($path)
    {
        $routeMap = [
            'inventaris' => '/admin/inventaris',
            'inventaris/outlet' => '/admin/inventaris/outlet',
            'inventaris/kategori' => '/admin/inventaris/kategori',
            'inventaris/produk' => '/admin/inventaris/produk',
            'inventaris/bahan' => '/admin/inventaris/bahan',
            'keuangan' => '/admin/keuangan',
            'keuangan/jurnal' => '/admin/keuangan/jurnal',
            'keuangan/buku' => '/admin/keuangan/buku',
            'keuangan/labarugi' => '/admin/keuangan/labarugi',
            'keuangan/neraca' => '/admin/keuangan/neraca',
            'sdm' => '/admin/sdm',
            'produksi' => '/admin/produksi/produksi',
            'penjualan' => '/admin/penjualan',
            'service' => '/admin/service',
            'sistem' => '/admin/sistem',
        ];
        
        // Direct mapping
        if (isset($routeMap[$path])) {
            return $routeMap[$path];
        }
        
        // Pattern matching for dynamic routes
        foreach ($routeMap as $pattern => $adminRoute) {
            if (str_starts_with($path, $pattern)) {
                $remainder = substr($path, strlen($pattern));
                return $adminRoute . $remainder;
            }
        }
        
        return null;
    }
}