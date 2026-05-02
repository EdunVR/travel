<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test Sparepart Permission Flow ===\n\n";

// Step 1: Check if permissions exist
echo "Step 1: Checking permissions...\n";
$permissions = \App\Models\Permission::where('module', 'inventaris')
    ->where('menu', 'sparepart')
    ->get();

if ($permissions->isEmpty()) {
    echo "❌ No sparepart permissions found!\n";
    echo "Run: php check_sparepart_permissions.php\n";
    exit;
}

echo "✅ Found {$permissions->count()} sparepart permissions\n";
foreach ($permissions as $perm) {
    echo "   - {$perm->name} (ID: {$perm->id})\n";
}
echo "\n";

// Step 2: Create test role
echo "Step 2: Creating test role...\n";
$testRole = \App\Models\Role::where('name', 'test_sparepart_role')->first();

if ($testRole) {
    echo "⚠️  Test role already exists, deleting...\n";
    $testRole->delete();
}

$testRole = \App\Models\Role::create([
    'name' => 'test_sparepart_role',
    'display_name' => 'Test Sparepart Role',
    'description' => 'Test role for sparepart permissions'
]);

echo "✅ Created role: {$testRole->name} (ID: {$testRole->id})\n\n";

// Step 3: Attach permissions
echo "Step 3: Attaching permissions to role...\n";
$viewPerm = $permissions->where('action', 'view')->first();
$createPerm = $permissions->where('action', 'create')->first();

if (!$viewPerm || !$createPerm) {
    echo "❌ View or Create permission not found!\n";
    exit;
}

$testRole->permissions()->attach([$viewPerm->id, $createPerm->id]);
echo "✅ Attached permissions:\n";
echo "   - {$viewPerm->name}\n";
echo "   - {$createPerm->name}\n\n";

// Step 4: Verify permissions attached
echo "Step 4: Verifying permissions...\n";
$testRole->load('permissions');
echo "✅ Role has {$testRole->permissions->count()} permissions:\n";
foreach ($testRole->permissions as $perm) {
    echo "   - {$perm->name}\n";
}
echo "\n";

// Step 5: Test hasPermission
echo "Step 5: Testing hasPermission()...\n";
$hasView = $testRole->hasPermission('inventaris.sparepart.view');
$hasCreate = $testRole->hasPermission('inventaris.sparepart.create');
$hasEdit = $testRole->hasPermission('inventaris.sparepart.edit');

echo "   - inventaris.sparepart.view: " . ($hasView ? '✅ YES' : '❌ NO') . "\n";
echo "   - inventaris.sparepart.create: " . ($hasCreate ? '✅ YES' : '❌ NO') . "\n";
echo "   - inventaris.sparepart.edit: " . ($hasEdit ? '✅ YES' : '❌ NO') . "\n\n";

// Step 6: Create test user
echo "Step 6: Creating test user...\n";
$testUser = \App\Models\User::where('email', 'test_sparepart@example.com')->first();

if ($testUser) {
    echo "⚠️  Test user already exists, updating...\n";
    $testUser->role_id = $testRole->id;
    $testUser->save();
} else {
    $testUser = \App\Models\User::create([
        'name' => 'Test Sparepart User',
        'email' => 'test_sparepart@example.com',
        'password' => bcrypt('password'),
        'role_id' => $testRole->id,
    ]);
}

echo "✅ Created/Updated user: {$testUser->email} (ID: {$testUser->id})\n";
echo "   Password: password\n\n";

// Step 7: Test user permissions
echo "Step 7: Testing user permissions...\n";
$testUser->load('role.permissions');

echo "   User role: {$testUser->role->name}\n";
echo "   Role permissions: {$testUser->role->permissions->count()}\n\n";

$userHasView = $testUser->hasPermission('inventaris.sparepart.view');
$userHasCreate = $testUser->hasPermission('inventaris.sparepart.create');
$userHasEdit = $testUser->hasPermission('inventaris.sparepart.edit');

echo "   - inventaris.sparepart.view: " . ($userHasView ? '✅ YES' : '❌ NO') . "\n";
echo "   - inventaris.sparepart.create: " . ($userHasCreate ? '✅ YES' : '❌ NO') . "\n";
echo "   - inventaris.sparepart.edit: " . ($userHasEdit ? '✅ YES' : '❌ NO') . "\n\n";

// Summary
echo "=== SUMMARY ===\n";
if ($userHasView && $userHasCreate && !$userHasEdit) {
    echo "✅ ALL TESTS PASSED!\n\n";
    echo "Test User Credentials:\n";
    echo "  Email: test_sparepart@example.com\n";
    echo "  Password: password\n\n";
    echo "Expected Behavior:\n";
    echo "  ✅ Can see Sparepart menu (has view permission)\n";
    echo "  ✅ Can see 'Tambah Sparepart' button (has create permission)\n";
    echo "  ❌ Cannot edit/delete (no edit/delete permission)\n\n";
    echo "Next Steps:\n";
    echo "  1. Login with test user\n";
    echo "  2. Go to Master/Inventaris → Sparepart\n";
    echo "  3. Verify 'Tambah Sparepart' button appears\n";
} else {
    echo "❌ TESTS FAILED!\n";
    echo "   View: " . ($userHasView ? 'OK' : 'FAIL') . "\n";
    echo "   Create: " . ($userHasCreate ? 'OK' : 'FAIL') . "\n";
    echo "   Edit: " . ($userHasEdit ? 'OK (should be NO)' : 'OK') . "\n";
}

echo "\nDone!\n";
