<?php

namespace App\Exports;

use App\Models\Sale;
use App\Models\SalesReturn;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    use Exportable;

    protected $startDate;
    protected $endDate;
    protected $paymentMethod;
    protected $search;

    public function __construct($startDate, $endDate, $paymentMethod = 'all', $search = '')
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->paymentMethod = $paymentMethod;
        $this->search = $search;
    }

    public function collection()
    {
        $query = Sale::query()
            ->with(['user'])
            ->whereDate('date', '>=', $this->startDate)
            ->whereDate('date', '<=', $this->endDate)
            ->when($this->paymentMethod !== 'all', function($q) {
                $q->where('payment_method', $this->paymentMethod);
            })
            ->when($this->search, function($q) {
                $q->where(function($query) {
                    $query->where('invoice_no', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', function($u) {
                            $u->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->latest('date');

        $sales = $query->get();

        // Calculate Totals
        $totalGrossSales = $sales->sum('grand_total');
        $totalReturns = SalesReturn::whereBetween('created_at', [
            Carbon::parse($this->startDate)->startOfDay(),
            Carbon::parse($this->endDate)->endOfDay()
        ])->sum('total_amount');
        $netSales = $sales->sum('dpp') - $totalReturns;
        
        $totalDpp = $sales->sum('dpp');
        $totalTax = $sales->sum('tax');
        $totalRounding = $sales->sum('rounding');

        // Add Spacer Row
        $sales->push((object)[
            'invoice_no' => '',
            'date' => null,
            'user' => null,
            'payment_method' => '',
            'grand_total' => '',
            'is_summary' => true,
        ]);

        // Add Summary Rows
        $sales->push((object)[
            'invoice_no' => 'TOTAL PENJUALAN KOTOR',
            'date' => null,
            'user' => null,
            'payment_method' => '',
            'grand_total' => $totalGrossSales,
            'is_summary' => true,
        ]);

        $sales->push((object)[
            'invoice_no' => '(-) TOTAL PPN (PAJAK)',
            'date' => null,
            'user' => null,
            'payment_method' => '',
            'grand_total' => $totalTax,
            'is_summary' => true,
        ]);

        $sales->push((object)[
            'invoice_no' => '(-) TOTAL PEMBULATAN',
            'date' => null,
            'user' => null,
            'payment_method' => '',
            'grand_total' => $totalRounding,
            'is_summary' => true,
        ]);

        $sales->push((object)[
            'invoice_no' => 'SUBTOTAL BERSIH (DPP)',
            'date' => null,
            'user' => null,
            'payment_method' => '',
            'grand_total' => $totalDpp,
            'is_summary' => true,
        ]);

        $sales->push((object)[
            'invoice_no' => '(-) TOTAL RETUR',
            'date' => null,
            'user' => null,
            'payment_method' => '',
            'grand_total' => $totalReturns,
            'is_summary' => true,
        ]);

        $sales->push((object)[
            'invoice_no' => 'TOTAL PENJUALAN BERSIH',
            'date' => null,
            'user' => null,
            'payment_method' => '',
            'grand_total' => $netSales,
            'is_summary' => true,
        ]);

        return $sales;
    }

    public function map($sale): array
    {
        if (isset($sale->is_summary) && $sale->is_summary) {
            return [
                $sale->invoice_no,
                '',
                '',
                '',
                $sale->grand_total !== '' ? (float)$sale->grand_total : '',
            ];
        }

        return [
            $sale->invoice_no,
            $sale->date->format('d/m/Y H:i'),
            $sale->user->name ?? '-',
            strtoupper($sale->payment_method),
            (float)$sale->grand_total,
        ];
    }

    public function headings(): array
    {
        return [
            'No. Invois',
            'Tanggal',
            'Kasir',
            'Metode Pembayaran',
            'Total (Rp)',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        
        return [
            1 => ['font' => ['bold' => true]],
            ($lastRow - 2) => ['font' => ['bold' => true]], // Total Gross
            ($lastRow - 1) => ['font' => ['bold' => true, 'color' => ['rgb' => 'FF0000']]], // Returns
            $lastRow => ['font' => ['bold' => true, 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2EFDA']]]], // Net
        ];
    }
}
