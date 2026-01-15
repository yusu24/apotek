<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AccountTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            [
                '1-1101',
                'Kas Utama',
                'asset',
                'cash_bank',
            ],
            [
                '4-1000',
                'Pendapatan Penjualan',
                'revenue',
                'revenue',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'kode_akun',
            'nama_akun',
            'tipe',
            'kategori',
        ];
    }
}
