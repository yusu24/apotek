<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class StockExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Get all products with their total stock calculation
        // Same logic as StockIndex query but get() instead of paginate()
        return Product::query()
            ->with(['category', 'unit'])
            ->withSum('batches as total_stock', 'stock_current')
            ->orderBy('name')
            ->get();
    }

    public function map($product): array
    {
        $status = 'Aman';
        $stock = $product->total_stock ?? 0;
        
        if ($stock <= 0) {
            $status = 'Habis';
        } elseif ($stock <= $product->min_stock) {
            $status = 'Menipis';
        }

        return [
            $product->barcode,
            $product->name,
            $product->category->name ?? '-',
            $stock,
            $product->unit->name ?? 'pcs',
            $product->min_stock,
            $status,
        ];
    }

    public function headings(): array
    {
        return [
            'Kode / Barcode',
            'Nama Produk',
            'Kategori',
            'Total Stok',
            'Satuan',
            'Stok Minimum',
            'Status Stok',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
