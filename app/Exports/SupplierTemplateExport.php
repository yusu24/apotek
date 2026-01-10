<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;

class SupplierTemplateExport implements WithHeadings
{
    public function headings(): array
    {
        return [
            'nama_supplier',
            'kontak',
            'telepon',
            'alamat',
        ];
    }
}
