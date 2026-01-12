<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Customer;
use App\Models\Sale;
use App\Models\Receivable;
use App\Models\User;

// 1. Create Customer
$customer = Customer::create([
    'name' => 'Test Customer',
    'phone' => '08123456789',
    'address' => 'Test Address'
]);
echo "Customer Created: " . $customer->name . "\n";

// 2. Create Sale (Tempo)
$sale = Sale::create([
    'user_id' => User::first()->id ?? 1,
    'customer_id' => $customer->id,
    'invoice_no' => 'INV-TEST-001',
    'date' => now(),
    'total_amount' => 100000,
    'grand_total' => 100000,
    'payment_method' => 'tempo',
    'cash_amount' => 20000, // DP
    'change_amount' => 0,
    'status' => 'completed'
]);
echo "Sale Created: " . $sale->invoice_no . "\n";

// 3. Create Receivable
$receivable = Receivable::create([
    'sale_id' => $sale->id,
    'customer_id' => $customer->id,
    'amount' => 100000,
    'paid_amount' => 20000,
    'remaining_balance' => 80000,
    'status' => 'partial',
    'due_date' => now()->addDays(30),
    'notes' => 'Test Tempo'
]);
echo "Receivable Created. Remaining: " . $receivable->remaining_balance . "\n";

// 4. Verify Relationships
$fetchedSale = Sale::find($sale->id);
if ($fetchedSale->customer->id === $customer->id) {
    echo "Sale -> Customer Relation: OK\n";
} else {
    echo "Sale -> Customer Relation: FAIL\n";
}

$fetchedCustomer = Customer::find($customer->id);
if ($fetchedCustomer->receivables->first()->id === $receivable->id) {
    echo "Customer -> Receivable Relation: OK\n";
} else {
    echo "Customer -> Receivable Relation: FAIL\n";
}

// Cleanup
$receivable->delete();
$sale->delete();
$customer->delete();
echo "Cleanup Done.\n";
