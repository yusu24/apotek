<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockTemplateExport implements WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    public function headings(): array
    {
        return [
            'barcode_produk', // Kolom A
            'nama_produk_opsional', // Kolom B (Hanya untuk bantuan visual)
            'jumlah_stok_fisik', // Kolom C (Stok Akhir yang diinginkan ATAU Stok Masuk -> Kita sepakati Stok Masuk/Opname)
            // Strategy: Kalau Opname, logicnya harus adjust. Kalau Import Masuk, logicnya add.
            // User request "Import Excel" di menu "Stok & Opname". 
            // Better naming: 'jumlah_stok_baru' or 'qty'
            'jumlah_masuk',
            'tanggal_kadaluarsa_yyyy_mm_dd',
            'harga_beli_satuan'
        ];
    }

    public function title(): string
    {
        return 'Template Import Stok';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
