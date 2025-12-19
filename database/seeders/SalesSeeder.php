<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class SalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        $user = User::first();
        
        if ($products->isEmpty() || !$user) {
            $this->command->warn('Pastikan sudah ada data produk dan user sebelum menjalankan seeder ini.');
            return;
        }

        // Generate sales untuk 30 hari terakhir
        $startDate = Carbon::now()->subDays(30);
        
        for ($day = 0; $day < 30; $day++) {
            $date = $startDate->copy()->addDays($day);
            
            // Random 3-8 transaksi per hari
            $transactionsPerDay = rand(3, 8);
            
            for ($i = 0; $i < $transactionsPerDay; $i++) {
                // Random waktu dalam hari tersebut
                $saleDate = $date->copy()->addHours(rand(8, 20))->addMinutes(rand(0, 59));
                
                // Pilih 1-5 produk random
                $selectedProducts = $products->random(rand(1, 5));
                
                $subtotal = 0;
                $items = [];
                
                foreach ($selectedProducts as $product) {
                    $qty = rand(1, 5);
                    $price = $product->sell_price;
                    $discount = rand(0, 1) ? rand(0, 10) * 1000 : 0; // 0 atau diskon 0-10rb
                    $itemSubtotal = ($price * $qty) - $discount;
                    
                    $items[] = [
                        'product_id' => $product->id,
                        'qty' => $qty,
                        'price' => $price,
                        'discount_amount' => $discount,
                        'subtotal' => $itemSubtotal,
                    ];
                    
                    $subtotal += $itemSubtotal;
                }
                
                // Hitung total
                $discount = rand(0, 1) ? rand(0, 5) * 1000 : 0; // Diskon global 0 atau 0-5rb
                $totalAmount = $subtotal - $discount;
                
                // Hitung PPN 12%
                $tax = $totalAmount * 0.12;
                
                // Grand total
                $grandTotal = $totalAmount + $tax;
                
                // Payment
                $paymentMethod = rand(0, 2) < 2 ? 'cash' : 'qris';
                $cashAmount = null;
                $changeAmount = null;
                
                if ($paymentMethod === 'cash') {
                    // Pembulatan ke atas ribuan terdekat
                    $cashAmount = ceil($grandTotal / 1000) * 1000;
                    $changeAmount = $cashAmount - $grandTotal;
                }
                
                // Buat transaksi
                $sale = Sale::create([
                    'invoice_no' => 'INV-' . $saleDate->format('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    'date' => $saleDate,
                    'subtotal' => $subtotal,
                    'total_amount' => $totalAmount,
                    'tax' => $tax,
                    'discount' => $discount,
                    'grand_total' => $grandTotal,
                    'rounding' => 0, // Tidak pakai rounding untuk data dummy
                    'payment_method' => $paymentMethod,
                    'cash_amount' => $cashAmount,
                    'change_amount' => $changeAmount,
                    'user_id' => $user->id,
                    'created_at' => $saleDate,
                    'updated_at' => $saleDate,
                ]);
                
                // Buat sale items
                foreach ($items as $item) {
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $item['product_id'],
                        'qty' => $item['qty'],
                        'price' => $item['price'],
                        'discount_amount' => $item['discount_amount'],
                        'subtotal' => $item['subtotal'],
                    ]);
                }
            }
        }
        
        $this->command->info('✓ Berhasil membuat ' . (30 * 5) . ' transaksi penjualan dummy untuk 30 hari terakhir');
    }
    
    private function randomCustomerName()
    {
        $names = [
            'Umum',
            'Budi Santoso',
            'Siti Nurhaliza',
            'Ahmad Wijaya',
            'Dewi Lestari',
            'Eko Prasetyo',
            'Fitri Handayani',
            'Gunawan',
            'Hana Pertiwi',
            'Irfan Hakim',
            'Joko Widodo',
            'Kartika Sari',
            'Linda Wijaya',
            'Muhammad Rizki',
            'Nur Azizah',
        ];
        
        return $names[array_rand($names)];
    }
}
