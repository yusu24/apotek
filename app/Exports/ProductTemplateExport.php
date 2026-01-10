<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductTemplateExport implements WithHeadings
{
    public function headings(): array
    {
        return [
            'nama_produk',
            'kategori',
            'satuan',
            'harga_jual',
            'stok_minimal',
            'barcode',
            'keterangan',
        ];
    }
}
