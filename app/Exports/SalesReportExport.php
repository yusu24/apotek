<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesReportExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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

    public function query()
    {
        return Sale::query()
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
    }

    public function map($sale): array
    {
        return [
            $sale->invoice_no,
            $sale->date->format('d/m/Y H:i'),
            $sale->user->name ?? '-',
            strtoupper($sale->payment_method),
            $sale->grand_total,
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
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
