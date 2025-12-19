<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles & Permissions
        $this->call([
            RoleSeeder::class,
            SettingSeeder::class,
        ]);

        // 2. Create Super Admin User
        $user = \App\Models\User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@apotek.com',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('super-admin');

        // 3. Create Additional Users
        $admin = \App\Models\User::factory()->create([
            'name' => 'Admin Apotek',
            'email' => 'admin2@apotek.com',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('admin');

        $kasir = \App\Models\User::factory()->create([
            'name' => 'Kasir 1',
            'email' => 'kasir@apotek.com',
            'password' => bcrypt('password'),
        ]);
        $kasir->assignRole('kasir');

        $gudang = \App\Models\User::factory()->create([
            'name' => 'Staff Gudang',
            'email' => 'gudang@apotek.com',
            'password' => bcrypt('password'),
        ]);
        $gudang->assignRole('gudang');

        // 4. Seed Master Data
        $this->call([
            CategorySeeder::class,
            UnitSeeder::class,
        ]);

        // 5. Suppliers
        $this->call([
            SupplierSeeder::class,
        ]);

        // 6. Products & Batches
        $this->call([
            ProductSeeder::class,
        ]);

        // 7. Expenses
        $this->call([
            ExpenseSeeder::class,
        ]);

        // 7. Sample Transactions (Optional - untuk testing)
        $this->createSampleSales($user);
    }

    private function createSampleSales($user)
    {
        // Create 5 sample sales transactions
        $products = \App\Models\Product::with('batches')->limit(10)->get();
        
        for ($i = 0; $i < 5; $i++) {
            $sale = \App\Models\Sale::create([
                'user_id' => $user->id,
                'invoice_no' => 'INV/' . date('Ymd') . '/' . (1000 + $i),
                'date' => now()->subDays(rand(0, 30)),
                'total_amount' => 0,
                'tax' => 0,
                'discount' => 0,
                'grand_total' => 0,
                'payment_method' => ['cash', 'qris', 'transfer'][rand(0, 2)],
                'cash_amount' => 0,
                'change_amount' => 0,
            ]);

            $total = 0;
            $itemCount = rand(2, 5);
            
            for ($j = 0; $j < $itemCount; $j++) {
                $product = $products->random();
                $batch = $product->batches->where('stock_current', '>', 0)->first();
                
                if ($batch) {
                    $qty = rand(1, 3);
                    $subtotal = $product->sell_price * $qty;
                    $total += $subtotal;

                    \App\Models\SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'batch_id' => $batch->id,
                        'quantity' => $qty,
                        'sell_price' => $product->sell_price,
                        'subtotal' => $subtotal,
                    ]);

                    // Update batch stock
                    $batch->decrement('stock_current', $qty);

                    // Record stock movement
                    \App\Models\StockMovement::create([
                        'product_id' => $product->id,
                        'batch_id' => $batch->id,
                        'user_id' => $user->id,
                        'type' => 'sale',
                        'quantity' => -$qty,
                        'doc_ref' => $sale->invoice_no,
                        'description' => 'Penjualan Sample',
                    ]);
                }
            }

            // Update sale totals
            $sale->update([
                'total_amount' => $total,
                'grand_total' => $total,
                'cash_amount' => $sale->payment_method == 'cash' ? ceil($total / 1000) * 1000 : $total,
                'change_amount' => $sale->payment_method == 'cash' ? (ceil($total / 1000) * 1000) - $total : 0,
            ]);
        }

        $this->command->info('✓ Created 5 sample sales transactions');
    }
}
