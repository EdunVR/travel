<?php

namespace App\Helpers;

class PermissionHelper
{
    /**
     * Check if user has any permission from a list
     *
     * @param array $permissions
     * @return bool
     */
    public static function hasAnyPermission(array $permissions): bool
    {
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
    }

    /**
     * Check if user has access to any submenu in a module
     *
     * @param string $module Module name (e.g., 'crm', 'inventory', 'finance')
     * @return bool
     */
    public static function hasModuleAccess(string $module): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $user = auth()->user();

        // Super admin has all access
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Get all permissions for this module
        $permissions = \App\Models\Permission::where('module', $module)->pluck('name')->toArray();

        return self::hasAnyPermission($permissions);
    }
}
