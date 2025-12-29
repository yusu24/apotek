<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Expense;
use App\Models\Batch;
use Carbon\Carbon;

class QuickNovDecSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info("🚀 Generating Nov-Dec 2025 dummy data...");
        
        $products = Product::all();
        $user = User::first();
        
        if ($products->isEmpty() || !$user) {
            $this->command->error('❌ Products or User not found!');
            return;
        }

        // Ensure we have batches with stock
        $this->ensureBatches($products);

        // November - December 2025
        $startDate = Carbon::create(2025, 11, 1);
        $endDate = Carbon::create(2025, 12, 31);
        
        $currentDate = $startDate->copy();
        $salesCount = 0;
        $expensesCount = 0;
        
        while ($currentDate->lte($endDate)) {
            // Daily sales: 5-12 per day
            $dailySales = rand(5, 12);
            for ($i = 0; $i < $dailySales; $i++) {
                if ($this->createSale($currentDate, $user, $products)) {
                    $salesCount++;
                }
            }
            
            // Expenses: 1-2 times per week
            if (rand(1, 7) <= 2) {
                $this->createExpense($currentDate, $user);
                $expensesCount++;
            }
            
            $currentDate->addDay();
        }
        
        $this->command->info("✅ Done!");
        $this->command->info("📊 Sales created: $salesCount");
        $this->command->info("💸 Expenses created: $expensesCount");
    }
    
    private function ensureBatches($products)
    {
        // Quick check - create batches if none exist
        if (Batch::count() < 10) {
            $this->command->info("Creating initial batches...");
            foreach ($products->take(10) as $product) {
                Batch::create([
                    'product_id' => $product->id,
                    'batch_number' => 'BATCH-INIT-' . $product->id,
                    'expire_date' => now()->addYear(),
                    'stock_in' => 500,
                    'stock_current' => 500,
                    'buy_price' => $product->sell_price * 0.65,
                    'supplier_id' => 1,
                ]);
            }
        }
    }
    
    private function createSale($date, $user, $products)
    {
        // Get random products (1-3 items)
        $selectedProducts = $products->random(rand(1, 3));
        if (!is_array($selectedProducts) && !($selectedProducts instanceof \Illuminate\Support\Collection)) {
            $selectedProducts = [$selectedProducts];
        }
        
        $subtotal = 0;
        $items = [];
        
        foreach ($selectedProducts as $product) {
            // Find a batch with stock for this product
            $batch = Batch::where('product_id', $product->id)
                ->where('stock_current', '>', 0)
                ->first();
                
            if (!$batch) continue;
            
            $qty = rand(1, min(3, $batch->stock_current));
            $price = $product->sell_price;
            $itemSubtotal = $price * $qty;
            
            $items[] = [
                'product_id' => $product->id,
                'batch_id' => $batch->id,
                'qty' => $qty,
                'price' => $price,
                'subtotal' => $itemSubtotal,
            ];
            
            $subtotal += $itemSubtotal;
            
            // Decrement stock
            $batch->decrement('stock_current', $qty);
        }
        
        if (empty($items)) return false;
        
        // Calculate totals
        $tax = $subtotal * 0.12;
        $grandTotal = $subtotal + $tax;
        
        // Random time during business hours
        $saleTime = $date->copy()->addHours(rand(9, 20))->addMinutes(rand(0, 59));
        
        // Create sale (PENDING first)
        $sale = Sale::create([
            'user_id' => $user->id,
            'invoice_no' => 'INV-' . $date->format('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'date' => $saleTime,
            'total_amount' => $subtotal,
            'tax' => $tax,
            'discount' => 0,
            'grand_total' => $grandTotal,
            'payment_method' => rand(0, 1) ? 'cash' : 'qris',
            'cash_amount' => $grandTotal,
            'change_amount' => 0,
            'status' => 'pending',
            'created_at' => $saleTime,
            'updated_at' => $saleTime,
        ]);
        
        // Create sale items
        foreach ($items as $item) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item['product_id'],
                'batch_id' => $item['batch_id'],
                'quantity' => $item['qty'],
                'sell_price' => $item['price'],
                'subtotal' => $item['subtotal'],
            ]);
        }
        
        // Complete the sale (triggers journal auto-post via observer)
        $sale->update(['status' => 'completed']);
        
        return true;
    }
    
    private function createExpense($date, $user)
    {
        $expenses = [
            ['desc' => 'Gaji Karyawan', 'min' => 3000000, 'max' => 5000000],
            ['desc' => 'Listrik & Air', 'min' => 500000, 'max' => 1000000],
            ['desc' => 'Sewa Toko', 'min' => 2000000, 'max' => 3000000],
            ['desc' => 'Internet & Telepon', 'min' => 300000, 'max' => 600000],
            ['desc' => 'Perlengkapan Toko', 'min' => 200000, 'max' => 500000],
        ];
        
        $expense = $expenses[array_rand($expenses)];
        $amount = rand($expense['min'], $expense['max']);
        
        Expense::create([
            'user_id' => $user->id,
            'category' => 'Operasional',
            'description' => $expense['desc'],
            'amount' => $amount,
            'date' => $date->format('Y-m-d'),
            'created_at' => $date,
            'updated_at' => $date,
        ]);
    }
}
