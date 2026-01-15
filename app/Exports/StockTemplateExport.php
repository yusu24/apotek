<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockTemplateExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return Product::with(['category', 'unit'])
            ->withSum('batches as total_stock', 'stock_current')
            ->orderBy('name')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID Produk (JANGAN DIUBAH)',
            'Barcode',
            'Nama Produk',
            'Kategori',
            'Satuan',
            'Stok Saat Ini',
            'Jumlah Masuk (Isi Disini)',
            'Tgl Kadaluarsa (YYYY-MM-DD)',
            'Harga Beli (Update jika perlu)',
            'Harga Jual (Update jika perlu)',
        ];
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->barcode,
            $product->name,
            optional($product->category)->name,
            optional($product->unit)->name,
            $product->total_stock ?? 0,
            '', // Jumlah Masuk - User fills this
            '', // Expired - User fills this
            $product->buy_price, // Pre-fill with current buy price
            $product->sell_price, // Pre-fill with current sell price
        ];
    }

    public function title(): string
    {
        return 'Input Stok';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
