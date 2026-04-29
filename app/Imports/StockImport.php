<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Batch;
use App\Models\StockMovement;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;

class StockImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    private $successCount = 0;
    private $currentRow = 1; // Starts at 1 (heading), first data row will be 2

    public function collection(Collection $rows)
    {
        // Use transaction to ensure data integrity
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                $this->currentRow++;
                $this->processRow($row);
            }
        });
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }

    private function processRow($row)
    {
        // Convert row to array for easier access
        $data = $row->toArray();
        
        // Helper function to find column value by multiple possible names
        $findColumn = function($possibleNames) use ($data) {
            foreach ($possibleNames as $name) {
                if (isset($data[$name]) && $data[$name] !== null && $data[$name] !== '') {
                    return $data[$name];
                }
            }
            return null;
        };

        // Get Product ID
        $productId = $findColumn(['id_produk_jangan_diubah', 'id_produk', 'id']);

        // Get Barcode
        $barcode = $findColumn(['kode_barcode', 'barcode', 'sku']);

        // Get Product Name
        $productName = $findColumn(['nama_produk', 'name']);

        // Get Quantity
        $qty = intval($findColumn([
            'jumlah_masuk_isi_disini',
            'jumlah_masuk',
            'total_stok',
            'stok',
            'qty',
            'quantity',
        ]) ?? 0);

        // Skip if row is empty
        if ($qty <= 0 && $productId === null && $barcode === null && $productName === null) {
            return;
        }

        // Validate Quantity
        if ($qty <= 0) {
            $this->failures[] = new Failure($this->currentRow, 'jumlah_masuk', ['Jumlah masuk harus lebih dari 0'], $data);
            return;
        }

        // 1. Find Product
        $product = null;

        // Priority 1: Try by Barcode
        if ($barcode !== null && $barcode !== '') {
            $product = Product::where('barcode', $barcode)->first();
        }

        // Priority 2: Try by ID
        if (!$product && $productId !== null && $productId !== '') {
            $product = Product::find($productId);
        }

        // Priority 3: Try by Name
        if (!$product && $productName !== null && $productName !== '') {
            $product = Product::where('name', $productName)->first();
        }

        if (!$product) {
            $identifier = $barcode ?: ($productId ?: $productName);
            $this->failures[] = new Failure($this->currentRow, 'produk', ["Produk tidak ditemukan ($identifier)"], $data);
            return;
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
                if (is_numeric($dateInput)) {
                    $expiredDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateInput);
                } else {
                    $expiredDate = Carbon::parse($dateInput);
                }
            } catch (\Exception $e) {
                $expiredDate = now()->addYear();
            }
        } else {
             $expiredDate = now()->addYear();
        }

        // Price parsing
        $currentSellPrice = $product->sell_price;
        $inputBuyPrice = $findColumn(['harga_beli_update_jika_perlu', 'harga_beli', 'buy_price']);
        $inputSellPrice = $findColumn(['harga_jual_update_jika_perlu', 'harga_jual', 'sell_price']);

        $finalBuyPrice = is_numeric($inputBuyPrice) ? floatval($inputBuyPrice) : 0;
        $finalSellPrice = is_numeric($inputSellPrice) ? floatval($inputSellPrice) : ($currentSellPrice ?? 0);

        if ($finalSellPrice != $currentSellPrice && $finalSellPrice > 0) {
            $product->update(['sell_price' => $finalSellPrice]);
        }
        
        // 2. Create Batch
        $batch = Batch::create([
            'product_id' => $product->id,
            'batch_no' => 'IMP-' . now()->format('ymd') . '-' . strtoupper(substr(uniqid(), -4)),
            'expired_date' => $expiredDate,
            'stock_in' => $qty,
            'stock_current' => $qty,
            'buy_price' => $finalBuyPrice,
        ]);

        // 3. Log Movement
        StockMovement::create([
            'product_id' => $product->id,
            'batch_id' => $batch->id,
            'user_id' => auth()->id(),
            'type' => 'opening_stock',
            'quantity' => $qty,
            'doc_ref' => 'IMP-' . now()->timestamp,
            'description' => 'Import Stock via Excel',
        ]);

        $this->successCount++;
    }

    public function rules(): array
    {
        return [
            'jumlah_masuk_isi_disini' => 'nullable|numeric|min:0',
            'jumlah_masuk' => 'nullable|numeric|min:0',
            'total_stok' => 'nullable|numeric|min:0',
            'stok' => 'nullable|numeric|min:0',
            'qty' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|numeric|min:0',
        ];
    }
}

}
