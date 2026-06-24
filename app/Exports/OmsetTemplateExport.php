<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OmsetTemplateExport implements WithHeadings, WithStyles
{
    public function headings(): array
    {
        return [
            'tanggal',
            'tahun',
            'omset',
            'hpp',
            'laba',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
