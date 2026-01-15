<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CHECKING DATABASE ===\n\n";

$totalProducts = App\Models\Product::count();
echo "Total Products in Database: $totalProducts\n\n";

if ($totalProducts > 0) {
    echo "First 5 Products:\n";
    echo str_repeat("-", 80) . "\n";
    printf("%-5s %-20s %-30s\n", "ID", "Barcode", "Name");
    echo str_repeat("-", 80) . "\n";
    
    App\Models\Product::limit(5)->get(['id', 'barcode', 'name'])->each(function($p) {
        printf("%-5s %-20s %-30s\n", 
            $p->id, 
            $p->barcode ?: '(no barcode)', 
            $p->name
        );
    });
    echo str_repeat("-", 80) . "\n";
} else {
    echo "⚠️  No products found in database!\n";
    echo "You need to add products first before importing stock.\n";
}
