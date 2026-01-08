<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductMarginExport implements FromView, WithTitle, ShouldAutoSize, WithStyles
{
    protected $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function view(): View
    {
        return view('exports.product-margin', [
            'products' => $this->products
        ]);
    }

    public function title(): string
    {
        return 'Laporan Margin';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['bold' => true]],
        ];
    }
}
