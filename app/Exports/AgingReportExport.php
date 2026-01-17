<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;

class AgingReportExport implements WithMultipleSheets
{
    use Exportable;

    protected $showPaid;

    public function __construct($showPaid)
    {
        $this->showPaid = filter_var($showPaid, FILTER_VALIDATE_BOOLEAN);
    }

    public function sheets(): array
    {
        return [
            new AgingReportSheet('ap', $this->showPaid),
            new AgingReportSheet('ar', $this->showPaid),
        ];
    }
}


