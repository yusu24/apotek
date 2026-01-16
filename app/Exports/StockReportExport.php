<?php

namespace App\Exports;

use App\Models\Batch;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockReportExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    use Exportable;

    protected $search;
    protected $startExpiry;
    protected $endExpiry;

    public function __construct($search = '', $startExpiry = null, $endExpiry = null)
    {
        $this->search = $search;
        $this->startExpiry = $startExpiry;
        $this->endExpiry = $endExpiry;
    }

    public function query()
    {
        $query = Batch::query()
            ->with(['product.category', 'product.unit'])
            ->where('stock_current', '>', 0)
            ->whereHas('product', function($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('barcode', 'like', '%'.$this->search.'%');
            });

        if ($this->startExpiry) {
            $query->whereDate('expired_date', '>=', $this->startExpiry);
        }

        if ($this->endExpiry) {
            $query->whereDate('expired_date', '<=', $this->endExpiry);
        }

        return $query->orderBy('expired_date');
    }

    public function map($batch): array
    {
        return [
            $batch->product->barcode,
            $batch->product->name,
            $batch->batch_no,
            $batch->expired_date ? $batch->expired_date->format('d/m/Y') : '-',
            $batch->product->unit->name ?? 'pcs',
            $batch->stock_current,
            $batch->buy_price,
            $batch->stock_current * $batch->buy_price,
        ];
    }

    public function headings(): array
    {
        return [
            'Kode Barang',
            'Nama Barang',
            'No. Batch',
            'Kadaluwarsa',
            'Satuan',
            'Stok',
            'Harga Beli',
            'Total Nilai',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
