<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Exports\ProductsExport;
use App\Exports\CustomersExport;
use App\Exports\SuppliersExport;
use App\Exports\SalesReportExport;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;

class WeeklyDataExport implements WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
        $startDate = Carbon::now()->subDays(30)->format('Y-m-d');
        $endDate = Carbon::now()->format('Y-m-d');

        return [
            new class extends ProductsExport implements WithTitle {
                public function title(): string { return 'Data Obat'; }
            },
            new class extends CustomersExport implements WithTitle {
                public function title(): string { return 'Data Pelanggan'; }
            },
            new class extends SuppliersExport implements WithTitle {
                public function title(): string { return 'Data Supplier'; }
            },
            new class($startDate, $endDate, 'all', '') extends SalesReportExport implements WithTitle {
                public function title(): string { return 'Penjualan (30 Hari)'; }
            }
        ];
    }
}
