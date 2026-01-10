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
        // 1. Find Product by Barcode or Name (fuzzy match fallback?)
        // Priority: Barcode.
        $product = null;
        
        if (!empty($row['barcode_produk'])) {
            $product = Product::where('barcode', $row['barcode_produk'])->first();
        }

        // If no barcode match, try name if provided (risky but helpful)
        if (!$product && !empty($row['nama_produk_opsional'])) {
            $product = Product::where('name', $row['nama_produk_opsional'])->first();
        }

        if (!$product) {
            // Skip or log error? Validation should catch this 'exist' rule ideally, 
            // but if we support name fallback, we do manual check.
            return; 
        }

        $qty = intval($row['jumlah_masuk']);
        
        if ($qty <= 0) return; // Skip invalid qty

        // Date parsing
        $expiredDate = null;
        if (!empty($row['tanggal_kadaluarsa_yyyy_mm_dd'])) {
            try {
                // Handle Excel date serial or string
                $expiredDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_kadaluarsa_yyyy_mm_dd']);
            } catch (\Exception $e) {
                // Try parse string
                try {
                    $expiredDate = Carbon::parse($row['tanggal_kadaluarsa_yyyy_mm_dd']);
                } catch (\Exception $ex) {
                    $expiredDate = now()->addYear(); // Default fallback
                }
            }
        } else {
             $expiredDate = now()->addYear(); // Default 1 year from now
        }

        // Price parsing
        $buyPrice = !empty($row['harga_beli_satuan']) ? floatval($row['harga_beli_satuan']) : ($product->buy_price ?? 0);

        // 2. Create Batch (Add Stock)
        // We do NOT use 'update existing'. Migration/Opname Import usually imply dumping stock in.
        $batch = Batch::create([
            'product_id' => $product->id,
            'batch_no' => 'IMP-' . now()->format('ymd') . '-' . strtoupper(substr(uniqid(), -4)),
            'expired_date' => $expiredDate,
            'stock_in' => $qty,
            'stock_current' => $qty,
            'buy_price' => $buyPrice,
            'sell_price' => $product->sell_price, // Inherit current sell price
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
            'jumlah_masuk' => 'required|numeric|min:1',
            // 'barcode_produk' => 'required_without:nama_produk_opsional', // Custom logic handled in processRow
        ];
    }
}
