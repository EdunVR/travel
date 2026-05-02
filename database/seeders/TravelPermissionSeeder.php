<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class TravelPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        
        try {
            // Define travel management modules and menus
            $travelModules = [
                'travel' => [
                    // Master data
                    'flight',
                    'hotel',
                    
                    // Package management
                    'package',
                    'hpp',
                    
                    // Jamaah and booking
                    'jamaah',
                    'booking',
                    'keberangkatan',
                    
                    // Workflow
                    'workflow',
                    'task',
                    
                    // Operations
                    'flight-booking',
                    'hotel-booking',
                    'design-material',
                    'document',
                    'logistics',
                    
                    // Financial
                    'payment',
                    'invoice',
                    
                    // Communication and catalog
                    'communication',
                    'catalog',
                    
                    // Reporting
                    'report',
                    'dashboard',
                    
                    // System
                    'audit',
                    'notification',
                    'search'
                ]
            ];

            // Standard CRUD actions
            $actions = ['view', 'create', 'update', 'delete'];
            
            // Special actions for specific menus
            $specialActions = [
                'workflow' => ['view', 'transition', 'history'],
                'task' => ['view', 'assign', 'complete', 'reassign'],
                'payment' => ['view', 'create', 'approve', 'receipt'],
                'report' => ['view', 'export', 'dashboard'],
                'dashboard' => ['view'],
                'audit' => ['view', 'export'],
                'notification' => ['view', 'send'],
                'search' => ['view']
            ];

            $permissionCount = 0;

            foreach ($travelModules as $module => $menus) {
                foreach ($menus as $menu) {
                    // Use special actions if defined, otherwise use standard CRUD
                    $menuActions = $specialActions[$menu] ?? $actions;
                    
                    foreach ($menuActions as $action) {
                        Permission::create([
                            'name' => "{$module}.{$menu}.{$action}",
                            'display_name' => ucfirst($action) . ' ' . ucfirst(str_replace('-', ' ', $menu)),
                            'module' => $module,
                            'menu' => $menu,
                            'action' => $action
                        ]);
                        $permissionCount++;
                    }
                }
            }

            // Assign all travel permissions to super admin
            $superAdmin = Role::where('name', 'super_admin')->first();
            if ($superAdmin) {
                $travelPermissions = Permission::where('module', 'travel')->pluck('id');
                $superAdmin->permissions()->syncWithoutDetaching($travelPermissions);
            }

            // Assign view permissions to manager role
            $manager = Role::where('name', 'manager')->first();
            if ($manager) {
                $viewPermissions = Permission::where('module', 'travel')
                    ->where('action', 'view')
                    ->pluck('id');
                $manager->permissions()->syncWithoutDetaching($viewPermissions);
            }

            // Assign operational permissions to admin role
            $admin = Role::where('name', 'admin')->first();
            if ($admin) {
                $adminPermissions = Permission::where('module', 'travel')
                    ->whereIn('action', ['view', 'create', 'update'])
                    ->pluck('id');
                $admin->permissions()->syncWithoutDetaching($adminPermissions);
            }

            DB::commit();
            
            $this->command->info('✅ Travel Management Permissions created successfully!');
            $this->command->info("   - {$permissionCount} permissions created");
            $this->command->info("   - Permissions assigned to roles");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
