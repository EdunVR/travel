<?php

/**
 * Debug Script: Role Permission Duplicate Issue
 * 
 * This script helps debug the duplicate permission issue when creating roles
 */

require_once 'vendor/autoload.php';

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

echo "=== DEBUG ROLE PERMISSION DUPLICATE ISSUE ===\n\n";

echo "1. Checking database constraints...\n";
checkDatabaseConstraints();

echo "\n2. Analyzing existing role permissions...\n";
analyzeExistingRolePermissions();

echo "\n3. Checking for duplicate permissions in database...\n";
checkDuplicatePermissions();

echo "\n4. Testing role creation with sample data...\n";
testRoleCreation();

echo "\n=== DEBUG COMPLETED ===\n";

function checkDatabaseConstraints() {
    try {
        // Check if unique constraint exists
        $constraints = DB::select("
            SELECT CONSTRAINT_NAME, CONSTRAINT_TYPE 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'role_permissions'
        ");
        
        echo "  📊 Database constraints on role_permissions table:\n";
        foreach ($constraints as $constraint) {
            echo "    - {$constraint->CONSTRAINT_NAME}: {$constraint->CONSTRAINT_TYPE}\n";
        }
        
        // Check indexes
        $indexes = DB::select("
            SHOW INDEX FROM role_permissions
        ");
        
        echo "  📊 Indexes on role_permissions table:\n";
        foreach ($indexes as $index) {
            echo "    - {$index->Key_name}: {$index->Column_name} (Unique: " . ($index->Non_unique ? 'No' : 'Yes') . ")\n";
        }
        
    } catch (\Exception $e) {
        echo "  ❌ Error checking constraints: " . $e->getMessage() . "\n";
    }
}

function analyzeExistingRolePermissions() {
    try {
        $totalRolePermissions = DB::table('role_permissions')->count();
        echo "  📊 Total role-permission assignments: $totalRolePermissions\n";
        
        // Check for duplicates in existing data
        $duplicates = DB::select("
            SELECT role_id, permission_id, COUNT(*) as count 
            FROM role_permissions 
            GROUP BY role_id, permission_id 
            HAVING COUNT(*) > 1
        ");
        
        if (count($duplicates) > 0) {
            echo "  ⚠️  Found " . count($duplicates) . " duplicate role-permission combinations:\n";
            foreach ($duplicates as $dup) {
                echo "    - Role ID {$dup->role_id}, Permission ID {$dup->permission_id}: {$dup->count} times\n";
            }
        } else {
            echo "  ✅ No duplicates found in existing data\n";
        }
        
        // Show role permission counts
        $roleCounts = DB::select("
            SELECT r.name, r.display_name, COUNT(rp.permission_id) as permission_count
            FROM roles r
            LEFT JOIN role_permissions rp ON r.id = rp.role_id
            GROUP BY r.id, r.name, r.display_name
            ORDER BY permission_count DESC
        ");
        
        echo "  📊 Permission counts by role:\n";
        foreach ($roleCounts as $role) {
            echo "    - {$role->display_name} ({$role->name}): {$role->permission_count} permissions\n";
        }
        
    } catch (\Exception $e) {
        echo "  ❌ Error analyzing role permissions: " . $e->getMessage() . "\n";
    }
}

function checkDuplicatePermissions() {
    try {
        // Check for duplicate permission names
        $duplicateNames = DB::select("
            SELECT name, COUNT(*) as count 
            FROM permissions 
            GROUP BY name 
            HAVING COUNT(*) > 1
        ");
        
        if (count($duplicateNames) > 0) {
            echo "  ⚠️  Found " . count($duplicateNames) . " duplicate permission names:\n";
            foreach ($duplicateNames as $dup) {
                echo "    - {$dup->name}: {$dup->count} times\n";
            }
        } else {
            echo "  ✅ No duplicate permission names found\n";
        }
        
        // Check permission distribution by module
        $moduleStats = DB::select("
            SELECT module, COUNT(*) as count 
            FROM permissions 
            GROUP BY module 
            ORDER BY count DESC
        ");
        
        echo "  📊 Permissions by module:\n";
        foreach ($moduleStats as $stat) {
            echo "    - {$stat->module}: {$stat->count} permissions\n";
        }
        
    } catch (\Exception $e) {
        echo "  ❌ Error checking duplicate permissions: " . $e->getMessage() . "\n";
    }
}

function testRoleCreation() {
    try {
        // Get some sample permissions
        $samplePermissions = Permission::take(5)->pluck('id')->toArray();
        
        echo "  🧪 Testing with sample permissions: " . implode(', ', $samplePermissions) . "\n";
        
        // Test array_unique functionality
        $duplicatedArray = array_merge($samplePermissions, $samplePermissions); // Create duplicates
        $uniqueArray = array_unique($duplicatedArray);
        
        echo "  📊 Original array: " . implode(', ', $duplicatedArray) . "\n";
        echo "  📊 After array_unique: " . implode(', ', $uniqueArray) . "\n";
        echo "  📊 Duplicates removed: " . (count($duplicatedArray) - count($uniqueArray)) . "\n";
        
        // Test if we can create a test role (without actually creating it)
        echo "  🧪 Testing role creation logic...\n";
        
        $testRoleName = 'test_role_' . time();
        echo "  📝 Would create role: $testRoleName\n";
        echo "  📝 Would assign permissions: " . implode(', ', $uniqueArray) . "\n";
        
        // Check if any of these permissions are already assigned to any role
        foreach ($uniqueArray as $permId) {
            $existingAssignments = DB::table('role_permissions')
                ->where('permission_id', $permId)
                ->count();
            echo "    - Permission $permId: assigned to $existingAssignments roles\n";
        }
        
    } catch (\Exception $e) {
        echo "  ❌ Error testing role creation: " . $e->getMessage() . "\n";
    }
}

echo "\n📋 DEBUGGING CHECKLIST:\n";
echo "1. ✅ Check database constraints and indexes\n";
echo "2. ✅ Analyze existing role-permission data\n";
echo "3. ✅ Look for duplicate permissions\n";
echo "4. ✅ Test array_unique functionality\n";
echo "5. ✅ Check Laravel logs for detailed error info\n";

echo "\n🔧 POTENTIAL SOLUTIONS:\n";
echo "1. Add unique constraint: ALTER TABLE role_permissions ADD UNIQUE KEY unique_role_permission (role_id, permission_id)\n";
echo "2. Use array_unique() before attach/sync operations\n";
echo "3. Use updateOrInsert() instead of insert() for manual operations\n";
echo "4. Check frontend for duplicate permission selections\n";
echo "5. Add validation to prevent duplicate submissions\n";

echo "\n🚨 COMMON CAUSES:\n";
echo "- Frontend sending duplicate permission IDs\n";
echo "- Complex permission grouping logic creating duplicates\n";
echo "- Race conditions in concurrent requests\n";
echo "- Missing unique constraint on role_id + permission_id\n";
echo "- JavaScript form serialization issues\n";

?>