<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class BladeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // @hasPermission directive
        Blade::if('hasPermission', function ($permission) {
            return auth()->check() && auth()->user()->hasPermission($permission);
        });
        
        // @hasRole directive
        Blade::if('hasRole', function ($role) {
            return auth()->check() && auth()->user()->hasRole($role);
        });
        
        // @hasAnyRole directive
        Blade::if('hasAnyRole', function (...$roles) {
            return auth()->check() && auth()->user()->hasAnyRole($roles);
        });
        
        // @hasOutletAccess directive
        Blade::if('hasOutletAccess', function ($outletId) {
            return auth()->check() && auth()->user()->hasOutletAccess($outletId);
        });
        
        // @hasModuleAccess directive - check if user has access to any permission in module
        Blade::if('hasModuleAccess', function ($module) {
            if (!auth()->check()) {
                return false;
            }
            
            $user = auth()->user();
            
            // Super admin has all access
            if ($user->hasRole('super_admin')) {
                return true;
            }
            
            // Check if user has any permission in this module
            $permissions = \App\Models\Permission::where('module', $module)->pluck('name')->toArray();
            
            foreach ($permissions as $permission) {
                if ($user->hasPermission($permission)) {
                    return true;
                }
            }
            
            return false;
        });
        
        // @hasAnyPermission directive - check if user has any of the given permissions
        Blade::if('hasAnyPermission', function (...$permissions) {
            if (!auth()->check()) {
                return false;
            }
            
            $user = auth()->user();
            
            // Super admin has all permissions
            if ($user->hasRole('super_admin')) {
                return true;
            }
            
            foreach ($permissions as $permission) {
                if ($user->hasPermission($permission)) {
                    return true;
                }
            }
            
            return false;
        });
    }
}
