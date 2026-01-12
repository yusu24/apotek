<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking tables...\n";
echo "receivable_payments: " . (Illuminate\Support\Facades\Schema::hasTable('receivable_payments') ? 'YES' : 'NO') . "\n";
