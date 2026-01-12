<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Converting 'source' to VARCHAR...\n";
    Illuminate\Support\Facades\DB::statement("ALTER TABLE journal_entries MODIFY COLUMN source VARCHAR(50) DEFAULT 'manual'");
    
    echo "Updating 'source' column to ENUM with new values...\n";
    Illuminate\Support\Facades\DB::statement("ALTER TABLE journal_entries MODIFY COLUMN source ENUM('sale', 'purchase', 'stock_adjustment', 'manual', 'opening_balance', 'expense', 'receivable_payment', 'payable_payment') DEFAULT 'manual'");
    
    echo "Successfully updated source enum.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
