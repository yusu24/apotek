<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\Batch;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Expense;
use App\Models\Account;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SimulationSeeder extends Seeder
{
    protected $accountingService;

    public function run(): void
    {
        $this->accountingService = new AccountingService();
        $user = User::first();
        
        if (!$user) {
            $this->command->error("User not found!");
            return;
        }

        // Start Date: 1 Oct 2025
        // End Date: 31 Dec 2025 (or current date if earlier)
        $startDate = Carbon::create(2025, 10, 1);
        $endDate = Carbon::create(2025, 12, 31);
        
        // Ensure we don't go into the future if running this "live"
        if ($endDate->isFuture()) {
            $endDate = now();
        }

        $this->command->info("Simulating data from {$startDate->format('d M Y')} to {$endDate->format('d M Y')}...");

        $products = Product::all();
        if ($products->isEmpty()) {
            $this->command->error("Products not found! Please seed products first.");
            return;
        }
        
        $suppliers = Supplier::all();
        if ($suppliers->isEmpty()) {
            // Create dummy supplier if none
             $suppliers = collect([Supplier::create(['name' => 'Supplier Umum', 'address' => 'Jakarta', 'phone' => '08123456789'])]);
        }

        // Cache Accounts
        $cashAccount = Account::where('code', '1-1100')->first();
        $expenseAccounts = Account::where('type', 'expense')->where('category', '!=', 'cogs')->get();
        if ($expenseAccounts->isEmpty()) {
            // Fallback if no expense accounts
             $this->command->warn("No expense accounts found. Expenses won't have journals.");
        }

        $currentDate = $startDate->copy();
        
        try {
            // Test with just first day
            \Log::info("Processing: " . $currentDate->format('Y-m-d'));
            
            // Force one purchase
            \Log::info("Creating Purchase...");
            $this->createPurchase($currentDate, $user, $products, $suppliers);
            \Log::info("Purchase Created Successfully!");
            
            $this->command->info("Test Completed Successfully!");

        } catch (\Throwable $e) {
            \Log::error("Simulation Failed: " . $e->getMessage());
            \Log::error($e->getTraceAsString());
            $this->command->error("Simulation Failed: " . $e->getMessage());
            $this->command->error($e->getTraceAsString());
        }
    }

    private function createPurchase($date, $user, $products, $suppliers)
    {
        $supplier = $suppliers->random();
        $itemsToBuy = $products->random(rand(3, 8));
        
        $goodsReceipt = GoodsReceipt::create([
            'supplier_id' => $supplier->id,
            'user_id' => $user->id,
            'delivery_note_number' => 'DO-' . $date->format('Ymd') . '-' . rand(100, 999),
            'received_date' => $date->format('Y-m-d'),
            'notes' => 'Simulated Restock',
            'status' => 'completed',
            'payment_method' => 'cash',
        ]);

        foreach ($itemsToBuy as $product) {
            $qty = rand(50, 200); // Buy in bulk
            $buyPrice = $product->sell_price * 0.7; // Approx margin

            // Create Batch
            $batch = Batch::create([
                'product_id' => $product->id,
                'batch_number' => 'BATCH-' . $date->format('ymd') . '-' . $product->id,
                'expire_date' => $date->copy()->addYear(),
                'stock_in' => $qty,
                'stock_current' => $qty,
                'buy_price' => $buyPrice,
                'supplier_id' => $supplier->id,
            ]);

            GoodsReceiptItem::create([
                'goods_receipt_id' => $goodsReceipt->id,
                'product_id' => $product->id,
                'batch_id' => $batch->id,
                'qty_received' => $qty,
                'buy_price' => $buyPrice,
                'expire_date' => $batch->expire_date,
            ]);
        }

        // Post Journal
        try {
            $this->accountingService->postPurchaseJournal($goodsReceipt->id);
            // Manually override the date to match simulation date
            $entry = \App\Models\JournalEntry::where('source', 'purchase')->where('source_id', $goodsReceipt->id)->first();
            if ($entry) {
                $entry->update(['date' => $date, 'created_at' => $date]);
            }
        } catch (\Exception $e) {
             // Ignore if fails, just simulation
        }
    }

    private function createExpense($date, $user, $expenseAccounts)
    {
        $account = $expenseAccounts->random();
        $amount = rand(50000, 500000);
        
        $expense = Expense::create([
            'user_id' => $user->id,
            'category' => 'Operasional', // Simplified
            'description' => 'Biaya ' . $account->name,
            'amount' => $amount,
            'date' => $date->format('Y-m-d'),
            'created_at' => $date,
            'updated_at' => $date,
        ]);

        // Manual Journal for Expense
        // Dr. Expense Account
        // Cr. Cash
        try {
             $cashAccount = Account::where('code', '1-1100')->first();
             if ($cashAccount) {
                 $this->accountingService->createJournalEntry([
                     'date' => $date,
                     'description' => $expense->description,
                     'lines' => [
                         [
                             'account_id' => $account->id,
                             'debit' => $amount,
                             'credit' => 0,
                             'notes' => 'Simulated Expense'
                         ],
                         [
                             'account_id' => $cashAccount->id,
                             'debit' => 0,
                             'credit' => $amount,
                             'notes' => 'Cash Payment'
                         ]
                     ],
                     'auto_post' => true
                 ]);
             }
        } catch (\Exception $e) {
            // Ignore
        }
    }

    private function createSale($date, $user, $products)
    {
        // Select products that have stock
        // We need to check stock first to avoid errors
        $availableProducts = $products->filter(function($p) {
            return $p->batches()->sum('stock_current') > 0;
        });

        if ($availableProducts->isEmpty()) return;

        $itemsToSell = $availableProducts->random(min(rand(1, 5), $availableProducts->count()));
        
        $subtotal = 0;
        $itemsData = [];

        foreach ($itemsToSell as $product) {
            // Get batch with stock
            $batch = $product->batches()->where('stock_current', '>', 0)->orderBy('expire_date')->first();
            if (!$batch) continue;

            $qty = rand(1, min(3, $batch->stock_current));
            $price = $product->sell_price;
            $lineTotal = $qty * $price;
            $subtotal += $lineTotal;

            $itemsData[] = [
                'product' => $product,
                'batch' => $batch,
                'qty' => $qty,
                'price' => $price,
                'total' => $lineTotal
            ];

            // Decrement Stock
            $batch->decrement('stock_current', $qty);
        }

        if (empty($itemsData)) return;

        $tax = $subtotal * 0.12; // 12% PPN
        $grandTotal = $subtotal + $tax;

        // Rounding to nearest 100 for Cash
        $grandTotalRounded = ceil($grandTotal / 100) * 100;

        $saleDate = $date->copy()->addHours(rand(8, 20))->addMinutes(rand(0, 59));

        $sale = Sale::create([
            'user_id' => $user->id,
            'invoice_no' => 'INV/' . $date->format('Ymd') . '/' . rand(1000, 9999),
            'date' => $saleDate,
            'total_amount' => $subtotal,
            'tax' => $tax,
            'discount' => 0,
            'grand_total' => $grandTotalRounded,
            'payment_method' => 'cash',
            'cash_amount' => $grandTotalRounded + 5000, // Pay a bit more
            'change_amount' => 5000,
            'status' => 'pending', // Pending first
            'created_at' => $saleDate,
            'updated_at' => $saleDate,
        ]);

        foreach ($itemsData as $item) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item['product']->id,
                'batch_id' => $item['batch']->id,
                'quantity' => $item['qty'],
                'sell_price' => $item['price'],
                'subtotal' => $item['total'],
            ]);
        }
        
        // Update status to completed -> Triggers Journal Post
        $sale->update(['status' => 'completed']);
        
        // Manually update the auto-generated journal date because the observer might use now() instead of sale date
        // Observer uses $sale->status == 'completed'
        // Sale model observer calls postSaleJournal($sale->id)
        // postSaleJournal uses $sale->date. 
        // So Journal Entry Date should be correct ($sale->date).
        // BUT created_at of journal entry might be now().
        // Let's force update created_at for sorting consistency
         $entry = \App\Models\JournalEntry::where('source', 'sale')->where('source_id', $sale->id)->first();
         if ($entry) {
             $entry->update(['created_at' => $saleDate]);
         }
    }
}
