<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleManagementController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->withCount('users')->get();
        $permissions = Permission::orderBy('name')->get();
        
        return view('admin.user-management.roles.index', compact('roles', 'permissions'));
    }

    public function show(Role $role)
    {
        return response()->json([
            'success' => true,
            'role' => $role->load('permissions')
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => $validated['name'],
                'display_name' => $validated['display_name'],
                'description' => $validated['description'] ?? null,
            ]);

            if (!empty($validated['permissions'])) {
                // Remove duplicates from permissions array
                $uniquePermissions = array_unique($validated['permissions']);
                
                // Log for debugging
                \Log::info('Role creation - permissions', [
                    'role_id' => $role->id,
                    'original_permissions' => $validated['permissions'],
                    'unique_permissions' => $uniquePermissions,
                    'duplicates_removed' => count($validated['permissions']) - count($uniquePermissions)
                ]);
                
                $role->permissions()->attach($uniquePermissions);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Role berhasil dibuat',
                'role' => $role->load('permissions')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating role', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Role $role)
    {
        // Protect system roles - normalize name to slug format
        $protectedRoles = ['super_admin', 'admin', 'user'];
        $normalizedName = strtolower(str_replace(' ', '_', $role->name));
        
        if (in_array($normalizedName, $protectedRoles)) {
            return response()->json([
                'success' => false,
                'message' => 'Role sistem tidak dapat diubah'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        DB::beginTransaction();
        try {
            $role->update([
                'name' => $validated['name'],
                'display_name' => $validated['display_name'],
                'description' => $validated['description'] ?? null,
            ]);

            // Remove duplicates from permissions array
            $uniquePermissions = !empty($validated['permissions']) ? array_unique($validated['permissions']) : [];
            
            // Log for debugging
            \Log::info('Role update - permissions', [
                'role_id' => $role->id,
                'original_permissions' => $validated['permissions'] ?? [],
                'unique_permissions' => $uniquePermissions,
                'duplicates_removed' => count($validated['permissions'] ?? []) - count($uniquePermissions)
            ]);

            $role->permissions()->sync($uniquePermissions);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Role berhasil diupdate',
                'role' => $role->load('permissions')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating role', [
                'role_id' => $role->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Role $role)
    {
        // Protect system roles - normalize name to slug format
        $protectedRoles = ['super_admin', 'admin', 'user'];
        $normalizedName = strtolower(str_replace(' ', '_', $role->name));
        
        if (in_array($normalizedName, $protectedRoles)) {
            return response()->json([
                'success' => false,
                'message' => 'Role sistem tidak dapat dihapus'
            ], 403);
        }

        if ($role->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Role tidak dapat dihapus karena masih digunakan oleh ' . $role->users()->count() . ' user'
            ], 422);
        }

        try {
            $role->permissions()->detach();
            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Role berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting role: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus role: ' . $e->getMessage()
            ], 500);
        }
    }
}
