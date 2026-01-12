<?php

use App\Models\Receivable;
use App\Models\Sale;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking Receivables Table...\n";
$count = Receivable::count();
echo "Total Receivables: $count\n";

if ($count > 0) {
    echo "First 5 Receivables:\n";
    foreach (Receivable::with(['customer', 'sale'])->limit(5)->get() as $r) {
        echo "ID: {$r->id} | Customer: " . ($r->customer->name ?? 'NULL') . " | Sale ID: {$r->sale_id} | Invoice: " . ($r->sale->invoice_no ?? 'NULL') . " | Amount: {$r->amount} | Status: {$r->status}\n";
    }
} else {
    echo "Table is empty.\n";
}

echo "\nChecking Sales with 'tempo' payment method...\n";
$sales = Sale::where('payment_method', 'tempo')->get();
echo "Total Tempo Sales: " . $sales->count() . "\n";
foreach ($sales as $sale) {
    echo "Sale ID: {$sale->id} | Invoice: {$sale->invoice_no} | Customer ID: {$sale->customer_id} | Has Receivable: " . ($sale->receivables ? 'YES' : 'NO') . "\n";
}
