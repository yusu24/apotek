<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Product::with(['category', 'unit', 'batches' => function($q) {
            $q->where('stock_current', '>', 0);
        }])->get();
    }

    public function map($product): array
    {
        $currentStock = $product->batches->sum('stock_current');
        
        // Calculate average buy price from active batches
        $totalValue = $product->batches->sum(function($batch) {
            return $batch->stock_current * $batch->buy_price;
        });
        $avgBuyPrice = $currentStock > 0 ? $totalValue / $currentStock : 0;

        return [
            $product->barcode,
            $product->name,
            $product->category->name ?? '-',
            $product->unit->name ?? '-',
            $currentStock,
            $product->min_stock,
            $avgBuyPrice,
            $product->sell_price,
            $totalValue, // Inventory Value
        ];
    }

    public function headings(): array
    {
        return [
            'Kode / Barcode',
            'Nama Produk',
            'Kategori',
            'Satuan',
            'Stok Saat Ini',
            'Stok Minimum',
            'Harga Beli Rata-rata',
            'Harga Jual',
            'Nilai Persediaan (Rp)',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1    => ['font' => ['bold' => true]],
        ];
    }
}
