<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;

class ProductsImport implements ToModel, WithHeadingRow, SkipsOnFailure
{
    use SkipsFailures;

    private $categories;
    private $units;
    private $currentRow = 1; // Start at 1, will increment to 2 for first data row

    public function __construct()
    {
        // Cache categories and units to avoid repeated queries
        $this->categories = Category::pluck('id', 'name')->mapWithKeys(fn($item, $key) => [strtoupper($key) => $item]);
        $this->units = Unit::pluck('id', 'name')->mapWithKeys(fn($item, $key) => [strtoupper($key) => $item]);
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $this->currentRow++;

        // Helper to find column by multiple possible names
        $findColumn = function($possibleNames) use ($row) {
            foreach ($possibleNames as $name) {
                if (isset($row[$name]) && $row[$name] !== null && $row[$name] !== '') {
                    return $row[$name];
                }
            }
            return null;
        };
        
        // Get product name
        $productName = $findColumn(['nama_produk', 'name']);
        
        // Get sell price
        $sellPrice = $findColumn(['harga_jual', 'sell_price']);
        
        // Try to get Category
        $categoryId = null;
        $categoryIdInput = $findColumn([
            'id_kategori_lihat_sheet_kategori',
            'id_kategori',
            'category_id',
            'kategori_id',
        ]);
        
        if ($categoryIdInput && is_numeric($categoryIdInput)) {
            $categoryId = intval($categoryIdInput);
            if (!Category::find($categoryId)) {
                $categoryId = null;
            }
        } else {
            $categoryName = $findColumn(['kategori', 'category']);
            if ($categoryName) {
                $categoryNameUpper = strtoupper(trim($categoryName));
                $categoryId = $this->categories[$categoryNameUpper] ?? null;
                
                if (!$categoryId) {
                    $category = Category::create([
                        'name' => ucwords(strtolower($categoryName)), 
                        'slug' => Str::slug($categoryName)
                    ]);
                    $categoryId = $category->id;
                    $this->categories[$categoryNameUpper] = $categoryId;
                }
            }
        }
        
        // Try to get Unit
        $unitId = null;
        $unitIdInput = $findColumn([
            'id_satuan_lihat_sheet_satuan',
            'id_satuan',
            'unit_id',
            'satuan_id',
        ]);
        
        if ($unitIdInput && is_numeric($unitIdInput)) {
            $unitId = intval($unitIdInput);
            if (!Unit::find($unitId)) {
                $unitId = null;
            }
        } else {
            $unitName = $findColumn(['satuan', 'unit']);
            if ($unitName) {
                $unitNameUpper = strtoupper(trim($unitName));
                $unitId = $this->units[$unitNameUpper] ?? null;
                
                if (!$unitId) {
                    $unit = Unit::create([
                        'name' => ucwords(strtolower($unitName)), 
                        'short_name' => Str::limit($unitName, 5, '')
                    ]);
                    $unitId = $unit->id;
                    $this->units[$unitNameUpper] = $unitId;
                }
            }
        }

        $barcode = $findColumn(['barcode']);
        
        // Manual Validation to populate failures
        $validationFailures = [];

        // Skip empty rows (if product name is missing, assume it's an empty/trailing row and skip it without error)
        if (!$productName) {
            return null;
        }
        
        if ($sellPrice === null || $sellPrice === '') {
            $validationFailures[] = new Failure($this->currentRow, 'harga_jual', ['Harga jual wajib diisi'], $row);
        } elseif (!is_numeric($sellPrice) || $sellPrice < 0) {
            $validationFailures[] = new Failure($this->currentRow, 'harga_jual', ['Harga jual harus berupa angka positif'], $row);
        }

        if (!$categoryId) {
             $validationFailures[] = new Failure($this->currentRow, 'kategori', ['Kategori wajib diisi atau tidak valid'], $row);
        }

        if (!$unitId) {
             $validationFailures[] = new Failure($this->currentRow, 'satuan', ['Satuan wajib diisi atau tidak valid'], $row);
        }

        if ($barcode) {
            // Check if barcode exists in DB
            if (Product::where('barcode', $barcode)->exists()) {
                $validationFailures[] = new Failure($this->currentRow, 'barcode', ['Barcode sudah digunakan oleh produk lain'], $row);
            }
        }

        if (!empty($validationFailures)) {
            foreach ($validationFailures as $failure) {
                $this->failures[] = $failure;
            }
            return null; // Skip this row
        }

        return new Product([
            'name'          => $productName,
            'slug'          => Str::slug($productName ?: 'product') . '-' . Str::random(5),
            'category_id'   => $categoryId,
            'unit_id'       => $unitId,
            'min_stock'     => $findColumn(['stok_minimal', 'min_stock']) ?? 0,
            'sell_price'    => $sellPrice ?? 0,
            'description'   => $findColumn(['keterangan', 'description']) ?? null,
            'barcode'       => $barcode,
        ]);
    }


}
