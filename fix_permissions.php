<?php

use Spatie\Permission\Models\Permission;

$permissions = [
    'view dashboard', 'view financial overview',
    'access pos', 'create sale', 'void transaction', 'view sales history',
    'view stock', 'import stock', 'adjust stock', 'view stock movements',
    'view purchase orders', 'view goods receipts', 'manage expired products',
    'manage sales returns', 'manage purchase returns',
    'view products', 'create products', 'edit products', 'delete products',
    'manage categories', 'manage units', 'manage product units',
    'manage suppliers', 'manage customers', 'import_master_data',
    'view trial balance', 'view balance sheet', 'view profit loss',
    'view income statement', 'view general ledger', 'view ppn report', 'view ap aging report',
    'view reports', 'view sales reports', 'view product margin report',
    'view accounts', 'manage accounts', 'view journals', 'create journal',
    'edit journals', 'delete journals', 'view opening balances',
    'edit opening balances', 'lock opening balances', 'unlock opening balances',
    'view expenses', 'manage expense categories', 'manage finance',
    'manage settings', 'manage pos settings', 'manage users',
    'view activity logs', 'view audit log'
];

$missing = [];
foreach ($permissions as $perm) {
    if (!Permission::where('name', $perm)->where('guard_name', 'web')->exists()) {
        $missing[] = $perm;
    }
}

echo "Missing Permissions:\n";
print_r($missing);

if (!empty($missing)) {
    echo "\nCreating missing permissions...\n";
    foreach ($missing as $perm) {
        Permission::create(['name' => $perm, 'guard_name' => 'web']);
        echo "Created: $perm\n";
    }
}
