<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Batch;
use App\Models\StockMovement;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockImport implements ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $rows)
    {
        // Use transaction to ensure data integrity
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                $this->processRow($row);
            }
        });
    }

    private function processRow($row)
    {
        // Convert row to array for easier access
        $data = $row->toArray();
        
        // Log all available keys for debugging
        \Log::info('Available columns: ' . implode(', ', array_keys($data)));
        \Log::info('Row data: ', $data);

        // Helper function to find column value by multiple possible names
        $findColumn = function($possibleNames) use ($data) {
            foreach ($possibleNames as $name) {
                if (isset($data[$name]) && $data[$name] !== null && $data[$name] !== '') {
                    return $data[$name];
                }
            }
            return null;
        };

        // Get Product ID - try multiple variations
        $productId = $findColumn([
            'id_produk_jangan_diubah',
            'id_produk',
            'id',
        ]);

        // Get Barcode
        $barcode = $findColumn(['barcode']);

        // Get Product Name
        $productName = $findColumn(['nama_produk', 'name']);

        // Get Quantity - THIS IS THE CRITICAL ONE
        $qty = intval($findColumn([
            'jumlah_masuk_isi_disini',
            'jumlah_masuk',
            'qty',
            'quantity',
        ]) ?? 0);

        \Log::info("Parsed - Product ID: $productId, Barcode: $barcode, Name: $productName, Qty: $qty");

        // Skip if no quantity input
        if ($qty <= 0) {
            \Log::info('Skipping row - no quantity or quantity is 0');
            return;
        }

        // 1. Find Product
        $product = null;
        $foundBy = null;

        // Priority 1: Try by Barcode (most reliable for stock operations)
        if ($barcode) {
            $product = Product::where('barcode', $barcode)->first();
            if ($product) {
                $foundBy = "Barcode";
                \Log::info("✓ Found product by Barcode: {$product->name} (ID: {$product->id})");
            }
        }

        // Priority 2: Try by ID if barcode didn't work
        if (!$product && $productId) {
            $product = Product::find($productId);
            if ($product) {
                $foundBy = "ID";
                \Log::info("✓ Found product by ID: {$product->name} (ID: {$product->id})");
            }
        }

        // Priority 3: Try by exact Name match
        if (!$product && $productName) {
            $product = Product::where('name', $productName)->first();
            if ($product) {
                $foundBy = "Name";
                \Log::info("✓ Found product by Name: {$product->name} (ID: {$product->id})");
            }
        }

        if (!$product) {
            \Log::error('❌ PRODUCT NOT FOUND!');
            \Log::error("   Searched by:");
            \Log::error("   - Barcode: " . ($barcode ?: '(empty)'));
            \Log::error("   - Product ID: " . ($productId ?: '(empty)'));
            \Log::error("   - Name: " . ($productName ?: '(empty)'));
            \Log::error("   Total products in database: " . Product::count());
            
            // Show some example products to help user
            if (Product::count() > 0) {
                \Log::error("   Example products in your database:");
                Product::limit(3)->get(['id', 'barcode', 'name'])->each(function($p) {
                    \Log::error("   - ID: {$p->id}, Barcode: {$p->barcode}, Name: {$p->name}");
                });
            }
            
            return; // Product not found, skip this row
        }

        // Date parsing
        $expiredDate = null;
        $dateInput = $findColumn([
            'tgl_kadaluarsa_yyyy_mm_dd',
            'tgl_kadaluarsa',
            'expired_date',
            'expiry_date',
        ]);
        
        if (!empty($dateInput)) {
            try {
                // Handle Excel date serial or string
                if (is_numeric($dateInput)) {
                    $expiredDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateInput);
                } else {
                    $expiredDate = Carbon::parse($dateInput);
                }
            } catch (\Exception $e) {
                $expiredDate = now()->addYear(); // Default fallback
            }
        } else {
             $expiredDate = now()->addYear(); // Default 1 year from now
        }

        // Price parsing
        $currentSellPrice = $product->sell_price;

        $inputBuyPrice = $findColumn([
            'harga_beli_update_jika_perlu',
            'harga_beli',
            'buy_price',
        ]);
        
        $inputSellPrice = $findColumn([
            'harga_jual_update_jika_perlu',
            'harga_jual',
            'sell_price',
        ]);

        // Use input if present, otherwise fallback to current
        $finalBuyPrice = is_numeric($inputBuyPrice) ? floatval($inputBuyPrice) : 0;
        $finalSellPrice = is_numeric($inputSellPrice) ? floatval($inputSellPrice) : ($currentSellPrice ?? 0);

        // Update Product Sell Price if changed
        if ($finalSellPrice != $currentSellPrice && $finalSellPrice > 0) {
            $product->update([
                'sell_price' => $finalSellPrice
            ]);
        }
        
        // Note: buy_price is stored per batch, not in products table

        // 2. Create Batch (Add Stock)
        $batch = Batch::create([
            'product_id' => $product->id,
            'batch_no' => 'IMP-' . now()->format('ymd') . '-' . strtoupper(substr(uniqid(), -4)),
            'expired_date' => $expiredDate,
            'stock_in' => $qty,
            'stock_current' => $qty,
            'buy_price' => $finalBuyPrice,
            // Note: sell_price is stored in Product table, not Batch table
        ]);

        // 3. Log Movement
        StockMovement::create([
            'product_id' => $product->id,
            'batch_id' => $batch->id,
            'user_id' => auth()->id(),
            'type' => 'opening_stock', // Or 'import'
            'quantity' => $qty,
            'doc_ref' => 'IMP-' . now()->timestamp,
            'description' => 'Import Stock via Excel',
        ]);
    }

    public function rules(): array
    {
        return [
            // We don't strictly require 'jumlah_masuk' because some rows might be left empty by user (only updating some items)
            // But if it IS present, it must be numeric.
            'jumlah_masuk_isi_disini' => 'nullable|numeric|min:0',
        ];
    }
}
