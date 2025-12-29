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

class NovDecSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info("🚀 Membuat data dummy untuk November-Desember 2025...");
        
        $user = User::first();
        $products = Product::all();
        $suppliers = Supplier::all();
        
        if (!$user || $products->isEmpty()) {
            $this->command->error("❌ User atau Products tidak ditemukan!");
            return;
        }
        
        if ($suppliers->isEmpty()) {
            $suppliers = collect([Supplier::create([
                'name' => 'Supplier Umum',
                'address' => 'Jakarta',
                'phone' => '08123456789'
            ])]);
        }

        // November - Desember 2025
        $startDate = Carbon::create(2025, 11, 1);
        $endDate = Carbon::create(2025, 12, 31);
        
        $currentDate = $startDate->copy();
        $dayCounter = 1;
        
        while ($currentDate->lte($endDate)) {
            $this->command->info("📅 {$currentDate->format('d M Y')} (Hari ke-{$dayCounter})");
            
            // 1. Pembelian setiap 5 hari ATAU jika stok rendah
            $totalStock = Batch::sum('stock_current');
            if ($dayCounter % 5 == 0 || $totalStock < 100) {
                $this->createPurchase($currentDate, $user, $products, $suppliers);
            }
            
            // 2. Pengeluaran - random 1-2 kali seminggu
            if (rand(1, 7) <= 2) {
                $this->createExpense($currentDate, $user);
            }
            
            // 3. Penjualan harian
            $saleCount = $currentDate->isWeekend() ? rand(8, 15) : rand(4, 10);
            for ($i = 0; $i < $saleCount; $i++) {
                $this->createSale($currentDate, $user, $products);
            }
            
            $currentDate->addDay();
            $dayCounter++;
        }
        
        $this->command->info("✅ Selesai! Data Nov-Dec berhasil dibuat!");
        $this->command->info("📊 Total Sales: " . Sale::count());
        $this->command->info("📦 Total Purchases: " . GoodsReceipt::count());
        $this->command->info("💰 Total Expenses: " . Expense::count());
    }
    
    private function createPurchase($date, $user, $products, $suppliers)
    {
        $supplier = $suppliers->random();
        $itemsToBuy = $products->random(min(rand(3, 6), $products->count()));
        
        $goodsReceipt = GoodsReceipt::create([
            'delivery_note_number' => 'DO/' . $date->format('Ymd') . '/' . rand(100, 999),
            'received_date' => $date->format('Y-m-d'),
            'user_id' => $user->id,
            'notes' => 'Pembelian rutin November-Desember',
        ]);

        foreach ($itemsToBuy as $product) {
            $qty = rand(50, 150);
            $buyPrice = $product->sell_price * 0.65; // 35% margin

            $batch = Batch::create([
                'product_id' => $product->id,
                'batch_number' => 'BATCH-' . $date->format('Ymd') . '-' . $product->id . '-' . rand(10, 99),
                'expire_date' => $date->copy()->addMonths(rand(12, 24)),
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
        
        $this->command->info("  📦 Pembelian #{$goodsReceipt->id} dibuat");
    }
    
    private function createExpense($date, $user)
    {
        $categories = [
            ['name' => 'Gaji Karyawan', 'amount' => rand(3000000, 5000000)],
            ['name' => 'Listrik & Air', 'amount' => rand(500000, 1000000)],
            ['name' => 'Sewa Toko', 'amount' => rand(2000000, 3000000)],
            ['name' => 'Perlengkapan', 'amount' => rand(200000, 500000)],
            ['name' => 'Internet & Telpon', 'amount' => rand(300000, 600000)],
        ];
        
        $expense = $categories[array_rand($categories)];
        
        Expense::create([
            'user_id' => $user->id,
            'category' => 'Operasional',
            'description' => $expense['name'],
            'amount' => $expense['amount'],
            'date' => $date->format('Y-m-d'),
            'created_at' => $date,
            'updated_at' => $date,
        ]);
        
        // Manual journal for expense
        $expenseAccount = Account::where('type', 'expense')->where('category', 'operating_expense')->first();
        $cashAccount = Account::where('code', '1-1100')->first();
        
        if ($expenseAccount && $cashAccount) {
            try {
                $accountingService = new AccountingService();
                $accountingService->createJournalEntry([
                    'date' => $date,
                    'description' => $expense['name'],
                    'lines' => [
                        [
                            'account_id' => $expenseAccount->id,
                            'debit' => $expense['amount'],
                            'credit' => 0,
                            'notes' => 'Pengeluaran ' . $expense['name']
                        ],
                        [
                            'account_id' => $cashAccount->id,
                            'debit' => 0,
                            'credit' => $expense['amount'],
                            'notes' => 'Pembayaran Cash'
                        ]
                    ],
                    'auto_post' => true
                ]);
            } catch (\Exception $e) {
                // Silent fail
            }
        }
        
        $this->command->info("  💸 Pengeluaran: {$expense['name']} (Rp " . number_format($expense['amount']) . ")");
    }
    
    private function createSale($date, $user, $products)
    {
        // Get products with stock - using direct query
        $productsWithStock = [];
        foreach ($products as $product) {
            $hasStock = Batch::where('product_id', $product->id)
                ->where('stock_current', '>', 0)
                ->exists();
            if ($hasStock) {
                $productsWithStock[] = $product;
            }
        }

        if (empty($productsWithStock)) return;

        $count = min(rand(1, 4), count($productsWithStock));
        $itemsToSell = collect($productsWithStock)->shuffle()->take($count)->all();
          
        $subtotal = 0;
        $itemsData = [];

        foreach ($itemsToSell as $product) {
            $batch = Batch::where('product_id', $product->id)
                ->where('stock_current', '>', 0)
                ->orderBy('expire_date')
                ->first();
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

            $batch->decrement('stock_current', $qty);
        }

        if (empty($itemsData)) return;

        $tax = $subtotal * 0.12;
        $grandTotal = $subtotal + $tax;
        $grandTotalRounded = ceil($grandTotal / 100) * 100;

        $saleTime = $date->copy()->addHours(rand(9, 20))->addMinutes(rand(0, 59));

        // Create sale with PENDING status first
        $sale = Sale::create([
            'user_id' => $user->id,
            'invoice_no' => 'INV/' . $date->format('Ymd') . '/' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'date' => $saleTime,
            'total_amount' => $subtotal,
            'tax' => $tax,
            'discount' => 0,
            'grand_total' => $grandTotalRounded,
            'payment_method' => 'cash',
            'cash_amount' => $grandTotalRounded + rand(0, 10000),
            'change_amount' => rand(0, 10000),
            'status' => 'pending',
            'created_at' => $saleTime,
            'updated_at' => $saleTime,
        ]);

        // Add items
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
        
        // Update to completed - triggers journal auto-post
        $sale->update(['status' => 'completed']);
        
        // Update journal created_at
        $entry = \App\Models\JournalEntry::where('source', 'sale')->where('source_id', $sale->id)->first();
        if ($entry) {
            $entry->update(['created_at' => $saleTime]);
        }
    }
}
