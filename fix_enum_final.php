<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // 1. Ensure it is VARCHAR first to avoid data loss
    Illuminate\Support\Facades\DB::statement("ALTER TABLE journal_entries MODIFY COLUMN source VARCHAR(50) DEFAULT 'manual'");
    
    // 2. Convert to ENUM including ALL existing values found + new ones
    // Found: sale, purchase, expense, supplier_payment
    // New: receivable_payment, payable_payment
    Illuminate\Support\Facades\DB::statement("ALTER TABLE journal_entries MODIFY COLUMN source ENUM('sale', 'purchase', 'stock_adjustment', 'manual', 'opening_balance', 'expense', 'supplier_payment', 'receivable_payment', 'payable_payment') DEFAULT 'manual'");
    
    echo "Successfully updated source enum.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
