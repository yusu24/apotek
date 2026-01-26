<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Spatie\Permission\Models\Permission;

// Copied from UserForm.php
$permissionStructure = [
    'Dashboard' => [
        'items' => [
            'view dashboard' => [],
            'view financial overview' => [],
        ]
    ],
    'Kasir (POS)' => [
        'items' => [
            'access pos' => [],
            'create sale' => [],
            'void transaction' => [],
            'view sales history' => [],
        ]
    ],
    'Stok & Pengadaan' => [
        'items' => [
            'view stock' => [],
            'import stock' => [],
            'adjust stock' => [],
            'view stock movements' => [],
            'view purchase orders' => [],
            'view goods receipts' => [],
            'manage expired products' => [],
        ]
    ],
    'Retur Barang' => [
        'items' => [
            'manage sales returns' => [],
            'manage purchase returns' => [],
        ]
    ],
    'Data Master' => [
        'items' => [
            'view products' => [],
            'create products' => [],
            'edit products' => [],
            'delete products' => [],
            'manage categories' => [],
            'manage units' => [],
            'manage product units' => [],
            'manage suppliers' => [],
            'manage customers' => [],
            'import_master_data' => [],
        ]
    ],
    'Laporan Keuangan' => [
        'items' => [
            'view trial balance' => [],
            'view balance sheet' => [],
            'view profit loss' => [],
            'view income statement' => [],
            'view general ledger' => [],
            'view ppn report' => [],
            'view ap aging report' => [],
        ]
    ],
    'Laporan Operasional' => [
        'items' => [
            'view reports' => [],
            'view sales reports' => [],
            'view stock' => [], // Duplicate key logic? In PHP array keys must be unique. 'view stock' is already in 'Stok & Pengadaan'. Usage matters.
            'view product margin report' => [],
            'view stock movements' => [], // Duplicate check
        ]
    ],
    'Keuangan & Administrasi' => [
        'items' => [
            'view accounts' => [],
            'manage accounts' => [],
            'view journals' => [],
            'create journal' => [],
            'edit journals' => [],
            'delete journals' => [],
            'view opening balances' => [],
            'edit opening balances' => [],
            'lock opening balances' => [],
            'unlock opening balances' => [],
            'view expenses' => [],
            'manage expense categories' => [],
            'manage finance' => [],
        ]
    ],
    'Pengaturan Sistem' => [
        'items' => [
            'manage settings' => [],
            'manage pos settings' => [],
            'manage users' => [],
            'view activity logs' => [],
            'view audit log' => [],
        ]
    ],
];

echo "Verifying Permissions...\n";
$missing = [];
$total = 0;

foreach ($permissionStructure as $group => $data) {
    foreach ($data['items'] as $perm => $details) {
        $total++;
        if (!Permission::where('name', $perm)->where('guard_name', 'web')->exists()) {
            $missing[] = $perm;
            // Create if missing
            Permission::create(['name' => $perm, 'guard_name' => 'web']);
            echo "[FIXED] Created missing permission: $perm\n";
        }
    }
}

if (empty($missing)) {
    echo "SUCCESS: All $total permissions are present in the database.\n";
} else {
    echo "DONE: Created " . count($missing) . " missing permissions.\n";
}
