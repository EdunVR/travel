<?php

/**
 * Fix Script: Role Permission Unique Constraint
 * 
 * This script adds unique constraint to prevent duplicate role-permission assignments
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

echo "=== FIX ROLE PERMISSION CONSTRAINT ===\n\n";

echo "1. Checking current constraints...\n";
$hasUniqueConstraint = checkUniqueConstraint();

if (!$hasUniqueConstraint) {
    echo "\n2. Cleaning up existing duplicates...\n";
    cleanupDuplicates();
    
    echo "\n3. Adding unique constraint...\n";
    addUniqueConstraint();
} else {
    echo "  ✅ Unique constraint already exists\n";
}

echo "\n4. Verifying constraint...\n";
verifyConstraint();

echo "\n=== FIX COMPLETED ===\n";

function checkUniqueConstraint() {
    try {
        $constraints = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'role_permissions'
            AND CONSTRAINT_TYPE = 'UNIQUE'
            AND CONSTRAINT_NAME LIKE '%role%permission%'
        ");
        
        if (count($constraints) > 0) {
            echo "  ✅ Found unique constraint: " . $constraints[0]->CONSTRAINT_NAME . "\n";
            return true;
        } else {
            echo "  ❌ No unique constraint found\n";
            return false;
        }
    } catch (\Exception $e) {
        echo "  ❌ Error checking constraints: " . $e->getMessage() . "\n";
        return false;
    }
}

function cleanupDuplicates() {
    try {
        // Find duplicates
        $duplicates = DB::select("
            SELECT role_id, permission_id, COUNT(*) as count, MIN(id) as keep_id
            FROM role_permissions 
            GROUP BY role_id, permission_id 
            HAVING COUNT(*) > 1
        ");
        
        if (count($duplicates) > 0) {
            echo "  ⚠️  Found " . count($duplicates) . " duplicate combinations\n";
            
            foreach ($duplicates as $dup) {
                echo "    Cleaning role_id: {$dup->role_id}, permission_id: {$dup->permission_id}\n";
                
                // Delete all except the first one (with MIN id)
                $deleted = DB::delete("
                    DELETE FROM role_permissions 
                    WHERE role_id = ? AND permission_id = ? AND id != ?
                ", [$dup->role_id, $dup->permission_id, $dup->keep_id]);
                
                echo "      Deleted {$deleted} duplicate records\n";
            }
            
            echo "  ✅ Cleanup completed\n";
        } else {
            echo "  ✅ No duplicates found to clean up\n";
        }
    } catch (\Exception $e) {
        echo "  ❌ Error cleaning duplicates: " . $e->getMessage() . "\n";
    }
}

function addUniqueConstraint() {
    try {
        DB::statement("
            ALTER TABLE role_permissions 
            ADD CONSTRAINT role_permissions_role_id_permission_id_unique 
            UNIQUE (role_id, permission_id)
        ");
        
        echo "  ✅ Unique constraint added successfully\n";
    } catch (\Exception $e) {
        echo "  ❌ Error adding constraint: " . $e->getMessage() . "\n";
        
        // Check if constraint already exists with different name
        if (str_contains($e->getMessage(), 'Duplicate key name')) {
            echo "  ℹ️  Constraint might already exist with different name\n";
        }
    }
}

function verifyConstraint() {
    try {
        // Try to insert a duplicate (should fail)
        $testRoleId = 1;
        $testPermissionId = 1;
        
        // First, ensure we have a record to test with
        DB::table('role_permissions')->updateOrInsert(
            ['role_id' => $testRoleId, 'permission_id' => $testPermissionId],
            ['created_at' => now(), 'updated_at' => now()]
        );
        
        // Now try to insert duplicate (should fail)
        try {
            DB::table('role_permissions')->insert([
                'role_id' => $testRoleId,
                'permission_id' => $testPermissionId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            echo "  ❌ Constraint not working - duplicate was inserted\n";
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                echo "  ✅ Constraint working - duplicate insertion prevented\n";
            } else {
                echo "  ⚠️  Unexpected error: " . $e->getMessage() . "\n";
            }
        }
        
        // Show final constraint status
        $constraints = DB::select("
            SELECT CONSTRAINT_NAME, CONSTRAINT_TYPE 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'role_permissions'
            AND CONSTRAINT_TYPE = 'UNIQUE'
        ");
        
        echo "  📊 Final unique constraints:\n";
        foreach ($constraints as $constraint) {
            echo "    - {$constraint->CONSTRAINT_NAME}\n";
        }
        
    } catch (\Exception $e) {
        echo "  ❌ Error verifying constraint: " . $e->getMessage() . "\n";
    }
}

echo "\n📋 WHAT THIS SCRIPT DOES:\n";
echo "1. Checks if unique constraint exists on role_permissions table\n";
echo "2. Cleans up any existing duplicate records\n";
echo "3. Adds unique constraint on (role_id, permission_id)\n";
echo "4. Verifies the constraint is working\n";

echo "\n🎯 EXPECTED RESULT:\n";
echo "After running this script, duplicate role-permission assignments will be prevented at database level\n";

?>