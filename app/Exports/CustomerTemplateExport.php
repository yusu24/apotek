<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            [
                'CONTOH PELANGGAN A',
                '08987654321',
                'Jl. Melati No. 5',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'nama_pelanggan',
            'telepon',
            'alamat',
        ];
    }
}
