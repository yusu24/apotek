<?php
/**
 * Script to create missing permissions in the database
 * Run this on the server to fix empty permission checkboxes
 * 
 * Usage: php create_missing_permissions.php
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Spatie\Permission\Models\Permission;

echo "=== Creating Missing Permissions ===\n\n";

$permissions = [
    'view dashboard', 'view reports', 'view sales reports', 'view profit loss',
    'view balance sheet', 'view income statement', 'view ppn report',
    'view product margin report', 'view ap aging report', 'view journals',
    'edit journals', 'delete journals', 'export reports', 'manage master data',
    'view products', 'create products', 'edit products', 'delete products',
    'view categories', 'manage categories', 'view units', 'manage units',
    'view suppliers', 'manage suppliers', 'manage product units',
    'upload product images', 'view customers', 'create customers',
    'edit customers', 'delete customers', 'import customers', 'manage customers',
    'manage inventory', 'view stock', 'adjust stock', 'view stock movements',
    'manage expired products', 'import stock', 'manage purchasing',
    'view purchase orders', 'view goods receipts', 'create purchase',
    'edit goods receipts', 'approve purchase', 'receive stock', 'access pos',
    'create sale', 'void transaction', 'view sales history', 'manage returns',
    'create return', 'approve return', 'manage users', 'create users',
    'edit users', 'delete users', 'assign roles', 'manage finance',
    'view finance', 'view expenses', 'manage expense categories',
    'view opening balances', 'edit opening balances', 'lock opening balances',
    'unlock opening balances', 'view accounts', 'manage accounts',
    'create journal', 'view general ledger', 'export general ledger',
    'manage settings', 'view settings', 'edit store profile',
    'manage pos settings', 'view guide', 'view audit log',
    'view activity logs', 'manage security', 'system maintenance',
    'manage sales returns', 'manage purchase returns', 'view trial balance',
    'export aging report', 'import_master_data',
];

$created = 0;
$existing = 0;

foreach ($permissions as $permission) {
    if (Permission::where('name', $permission)->where('guard_name', 'web')->exists()) {
        $existing++;
        echo "  [SKIP] {$permission}\n";
    } else {
        Permission::create(['name' => $permission, 'guard_name' => 'web']);
        $created++;
        echo "  [✓] Created: {$permission}\n";
    }
}

echo "\n=== Summary ===\n";
echo "  Created: {$created} permissions\n";
echo "  Already existed: {$existing} permissions\n";
echo "  Total: " . ($created + $existing) . " permissions\n";
echo "\n✓ Done! You can now edit users and see their permissions correctly.\n";
