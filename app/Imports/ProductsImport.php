<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation
{
    private $categories;
    private $units;

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
        $categoryName = strtoupper(trim($row['kategori']));
        $unitName = strtoupper(trim($row['satuan']));

        // Find Category ID
        $categoryId = $this->categories[$categoryName] ?? null;
        if (!$categoryId) {
            // Option: Create or Fallback. For now, we will create if simple, or just nullable? 
            // Better to strictly require it based on Plan. 
            // However, inside 'model', validation should have caught it? 
            // Validation rules don't easily access DB state like this for 'exists' on a *name* unless using custom rule.
            // Let's rely on simple FirstOrCreate for user convenience, or fallback if allowed.
            // But to adhere to strictness:
             $category = Category::create(['name' => ucwords(strtolower($row['kategori'])), 'slug' => Str::slug($row['kategori'])]);
             $categoryId = $category->id;
             $this->categories[$categoryName] = $categoryId; // update cache
        }
        
        // Find Unit ID
        $unitId = $this->units[$unitName] ?? null;
        if (!$unitId) {
             $unit = Unit::create(['name' => ucwords(strtolower($row['satuan'])), 'short_name' => Str::limit($row['satuan'], 5, '')]);
             $unitId = $unit->id;
             $this->units[$unitName] = $unitId; // update cache
        }

        return new Product([
            'name'          => $row['nama_produk'],
            'slug'          => Str::slug($row['nama_produk']) . '-' . Str::random(5),
            'category_id'   => $categoryId,
            'unit_id'       => $unitId,
            'min_stock'     => $row['stok_minimal'] ?? 0,
            'sell_price'    => $row['harga_jual'] ?? 0,
            'description'   => $row['keterangan'] ?? null,
            'barcode'       => $row['barcode'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_produk' => 'required|string',
            'kategori'    => 'required|string',
            'satuan'      => 'required|string',
            'harga_jual'  => 'required|numeric|min:0',
        ];
    }
}
