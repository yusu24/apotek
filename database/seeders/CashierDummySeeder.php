<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Batch;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\JournalEntry;
use App\Services\AccountingService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CashierDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accountingService = new AccountingService();

        // 1. Define cashiers and their transaction volume profiles
        $cashierProfiles = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@apotek.com',
                'min_sales' => 5,
                'max_sales' => 10,
            ],
            [
                'name' => 'Siti Rahma',
                'email' => 'siti@apotek.com',
                'min_sales' => 3,
                'max_sales' => 7,
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi@apotek.com',
                'min_sales' => 2,
                'max_sales' => 5,
            ],
            [
                'name' => 'Ahmad Wijaya',
                'email' => 'ahmad@apotek.com',
                'min_sales' => 1,
                'max_sales' => 3,
            ],
        ];

        $cashierUsers = [];

        foreach ($cashierProfiles as $profile) {
            $user = User::firstOrCreate(
                ['email' => $profile['email']],
                [
                    'name' => $profile['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            // Assign role
            $user->assignRole('kasir');
            $cashierUsers[] = [
                'user' => $user,
                'profile' => $profile,
            ];
        }

        $products = Product::with(['batches', 'unit'])->get();

        if ($products->isEmpty()) {
            $this->command->error('No products found. Please run ProductSeeder first.');
            return;
        }

        // Generate sales for the last 60 days
        $startDate = Carbon::now()->subDays(60)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $this->command->info("Seeding cashier dummy sales from {$startDate->toDateString()} to {$endDate->toDateString()}...");

        $currentDate = $startDate->copy();
        $totalSalesCreated = 0;

        DB::beginTransaction();
        try {
            while ($currentDate->lte($endDate)) {
                foreach ($cashierUsers as $cashier) {
                    $user = $cashier['user'];
                    $profile = $cashier['profile'];

                    // Number of sales for this cashier today
                    $numSales = rand($profile['min_sales'], $profile['max_sales']);

                    for ($s = 0; $s < $numSales; $s++) {
                        // Generate random sale time during store hours (8 AM to 9 PM)
                        $saleTime = $currentDate->copy()
                            ->addHours(rand(8, 20))
                            ->addMinutes(rand(0, 59))
                            ->addSeconds(rand(0, 59));

                        // Select 1 to 4 random products
                        $numProducts = rand(1, 4);
                        $selectedProducts = $products->random(min($numProducts, $products->count()));

                        $subtotal = 0;
                        $itemsData = [];

                        foreach ($selectedProducts as $product) {
                            $qty = rand(1, 3);
                            $price = $product->sell_price;
                            $itemSubtotal = $price * $qty;
                            $subtotal += $itemSubtotal;

                            // Get or create batch
                            $batch = $product->batches->first();
                            if (!$batch) {
                                $batch = Batch::create([
                                    'product_id' => $product->id,
                                    'batch_no' => 'BATCH-' . $saleTime->format('ymd') . '-' . $product->id . '-' . rand(10, 99),
                                    'expired_date' => $saleTime->copy()->addYears(2),
                                    'stock_in' => 500,
                                    'stock_current' => 500,
                                    'buy_price' => $price * 0.75,
                                ]);
                                // Refresh relationship/collection so next time we know it exists
                                $product->setRelation('batches', collect([$batch]));
                            } elseif ($batch->stock_current < $qty) {
                                // Keep stock replenished
                                $batch->increment('stock_current', 200);
                                $batch->increment('stock_in', 200);
                            }

                            $itemsData[] = [
                                'product' => $product,
                                'batch' => $batch,
                                'qty' => $qty,
                                'price' => $price,
                                'subtotal' => $itemSubtotal,
                            ];
                        }

                        // Apply a random global discount (10% chance)
                        $discount = 0;
                        if (rand(1, 10) === 1) {
                            $discount = rand(1, 5) * 1000;
                            if ($discount >= $subtotal) {
                                $discount = 0;
                            }
                        }

                        $totalAfterDiscount = $subtotal - $discount;
                        $tax = $totalAfterDiscount * 0.12; // 12% PPN
                        $grandTotal = $totalAfterDiscount + $tax;
                        $grandTotalRounded = ceil($grandTotal / 100) * 100;

                        $paymentMethod = ['cash', 'qris', 'transfer'][rand(0, 2)];
                        $cashAmount = 0;
                        $changeAmount = 0;

                        if ($paymentMethod === 'cash') {
                            $cashAmount = ceil($grandTotalRounded / 1000) * 1000;
                            if ($cashAmount < $grandTotalRounded) {
                                $cashAmount += 1000;
                            }
                            $changeAmount = $cashAmount - $grandTotalRounded;
                        } else {
                            $cashAmount = $grandTotalRounded;
                            $changeAmount = 0;
                        }

                        $invoiceNo = 'INV/' . $saleTime->format('Ymd') . '/' . strtoupper(Str::random(6));

                        // Create Sale
                        $sale = Sale::create([
                            'user_id' => $user->id,
                            'invoice_no' => $invoiceNo,
                            'date' => $saleTime,
                            'total_amount' => $subtotal,
                            'discount' => $discount,
                            'tax' => $tax,
                            'grand_total' => $grandTotalRounded,
                            'payment_method' => $paymentMethod,
                            'cash_amount' => $cashAmount,
                            'change_amount' => $changeAmount,
                            'order_mode' => 'In',
                            'ppn_mode' => 'off',
                            'status' => 'completed',
                            'created_at' => $saleTime,
                            'updated_at' => $saleTime,
                        ]);

                        // Create SaleItems and StockMovements
                        foreach ($itemsData as $item) {
                            $product = $item['product'];
                            $batch = $item['batch'];
                            $qty = $item['qty'];

                            SaleItem::create([
                                'sale_id' => $sale->id,
                                'product_id' => $product->id,
                                'unit_id' => $product->unit_id,
                                'batch_id' => $batch->id,
                                'quantity' => $qty,
                                'sell_price' => $item['price'],
                                'discount_amount' => 0,
                                'subtotal' => $item['subtotal'],
                                'created_at' => $saleTime,
                                'updated_at' => $saleTime,
                            ]);

                            $batch->decrement('stock_current', $qty);

                            StockMovement::create([
                                'product_id' => $product->id,
                                'batch_id' => $batch->id,
                                'user_id' => $user->id,
                                'type' => 'sale',
                                'quantity' => -$qty,
                                'doc_ref' => $invoiceNo,
                                'description' => 'Penjualan Kasir (' . $qty . ' ' . ($product->unit->name ?? 'pcs') . ')',
                                'created_at' => $saleTime,
                                'updated_at' => $saleTime,
                            ]);
                        }

                        // Post Journal
                        try {
                            $accountingService->postSaleJournal($sale->id);

                            // Override created_at/updated_at and date of journal entry and lines to match sale time
                            $entry = JournalEntry::where('source', 'sale')
                                ->where('source_id', $sale->id)
                                ->first();

                            if ($entry) {
                                $entry->update([
                                    'date' => $saleTime,
                                    'created_at' => $saleTime,
                                    'updated_at' => $saleTime,
                                ]);

                                DB::table('journal_entry_lines')
                                    ->where('journal_entry_id', $entry->id)
                                    ->update([
                                        'created_at' => $saleTime,
                                        'updated_at' => $saleTime,
                                    ]);
                            }
                        } catch (\Exception $e) {
                            // Log and ignore failure in journal posting to continue seeding
                            \Log::warning("Failed to post sale journal for {$invoiceNo}: " . $e->getMessage());
                        }

                        $totalSalesCreated++;
                    }
                }

                $currentDate->addDay();
            }

            DB::commit();
            $this->command->info("✓ Successfully created {$totalSalesCreated} transactions across 4 cashiers for the last 60 days.");
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error("Seeding failed: " . $e->getMessage());
            \Log::error($e);
        }
    }
}
