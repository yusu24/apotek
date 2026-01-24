<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

echo "All Permissions:\n";
foreach (Permission::all() as $p) {
    echo "- " . $p->name . "\n";
}

echo "\nSuper Admin Permissions:\n";
$role = Role::where('name', 'super-admin')->first();
if ($role) {
    foreach ($role->permissions as $p) {
        echo "- " . $p->name . "\n";
    }
} else {
    echo "Super Admin role not found.\n";
}
