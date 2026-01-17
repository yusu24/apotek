<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExpenseCategoryTemplateExport implements WithHeadings, WithTitle, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'Nama Kategori',
            'Deskripsi'
        ];
    }

    public function title(): string
    {
        return 'Template Kategori Pengeluaran';
    }
}
