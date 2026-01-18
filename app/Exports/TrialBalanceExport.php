<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TrialBalanceExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $reportData;

    public function __construct($reportData)
    {
        $this->reportData = $reportData;
    }

    public function view(): View
    {
        return view('exports.trial-balance', [
            'data' => $this->reportData
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
        ];
    }
}
