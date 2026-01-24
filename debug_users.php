<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$users = User::all();
foreach ($users as $u) {
    echo "User: " . $u->name . " (ID: " . $u->id . ")\n";
    echo "  Roles: " . implode(', ', $u->getRoleNames()->toArray()) . "\n";
    echo "  Can View Journals: " . ($u->can('view journals') ? 'YES' : 'NO') . "\n";
    echo "  Can Edit Journals: " . ($u->can('edit journals') ? 'YES' : 'NO') . "\n";
    echo "  Can Delete Journals: " . ($u->can('delete journals') ? 'YES' : 'NO') . "\n";
    echo "-------------------\n";
}
