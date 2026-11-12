<?php

namespace App\Exports;

use App\Models\StockMovement;
use App\Livewire\Reports\StockOpnameReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockOpnameReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    use Exportable;

    protected $startDate;
    protected $endDate;
    protected $categoryFilter;
    protected $varianceType;
    protected $search;

    public function __construct($startDate = null, $endDate = null, $categoryFilter = '', $varianceType = 'all', $search = '')
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->categoryFilter = $categoryFilter;
        $this->varianceType = $varianceType;
        $this->search = $search;
    }

    public function collection()
    {
        $baseQuery = StockMovement::query()
            ->where('stock_movements.type', 'adjustment')
            ->join('products', 'products.id', '=', 'stock_movements.product_id')
            ->leftJoin('batches', 'batches.id', '=', 'stock_movements.batch_id');

        if ($this->startDate) {
            $baseQuery->whereDate('stock_movements.created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $baseQuery->whereDate('stock_movements.created_at', '<=', $this->endDate);
        }

        if ($this->categoryFilter) {
            $baseQuery->where('products.category_id', $this->categoryFilter);
        }

        if ($this->search) {
            $search = '%' . $this->search . '%';
            $baseQuery->where(function ($q) use ($search) {
                $q->where('products.name', 'like', $search)
                  ->orWhere('products.barcode', 'like', $search)
                  ->orWhere('stock_movements.doc_ref', 'like', $search)
                  ->orWhere('stock_movements.description', 'like', $search)
                  ->orWhere('batches.batch_no', 'like', $search);
            });
        }

        $stats = StockOpnameReport::getSummaryStats($baseQuery);

        $query = (clone $baseQuery)
            ->with(['product.category', 'product.unit', 'batch', 'user'])
            ->select('stock_movements.*')
            ->selectRaw('CASE WHEN COALESCE(batches.buy_price, 0) > 0 THEN batches.buy_price ELSE COALESCE(products.purchase_price, 0) END as unit_hpp');

        if ($this->varianceType === 'minus') {
            $query->where('stock_movements.quantity', '<', 0);
        } elseif ($this->varianceType === 'plus') {
            $query->where('stock_movements.quantity', '>', 0);
        }

        $movements = $query->orderBy('stock_movements.created_at', 'desc')
            ->orderBy('stock_movements.id', 'desc')
            ->get();

        // Add Spacer & Summary Rows
        $movements->push((object)[
            'is_summary' => true,
            'label' => '',
            'qty' => '',
            'value' => '',
        ]);

        $movements->push((object)[
            'is_summary' => true,
            'label' => '1. STOK MINUS (Fisik < Sistem) [' . $stats['minus_items'] . ' Produk]',
            'qty' => -$stats['minus_qty'],
            'value' => -$stats['minus_value'],
        ]);

        $movements->push((object)[
            'is_summary' => true,
            'label' => '2. STOK PLUS (Fisik > Sistem) [' . $stats['plus_items'] . ' Produk]',
            'qty' => $stats['plus_qty'],
            'value' => $stats['plus_value'],
        ]);

        $movements->push((object)[
            'is_summary' => true,
            'label' => '3A. TOTAL SELISIH BERSIH (Stok Plus - Stok Minus)',
            'qty' => $stats['net_qty'],
            'value' => $stats['net_value'],
        ]);

        $movements->push((object)[
            'is_summary' => true,
            'label' => '3B. TOTAL AKUMULASI SELISIH (Stok Plus + Stok Minus)',
            'qty' => $stats['abs_qty'],
            'value' => $stats['abs_value'],
        ]);

        return $movements;
    }

    public function map($item): array
    {
        if (isset($item->is_summary) && $item->is_summary) {
            return [
                $item->label,
                '',
                '',
                '',
                '',
                '',
                '',
                $item->qty !== '' ? (int) $item->qty : '',
                '',
                '',
                $item->value !== '' ? (float) $item->value : '',
                '',
                '',
            ];
        }

        $qty = (int) $item->quantity;
        $unitHpp = (float) ($item->unit_hpp ?? 0);
        $varianceValue = $qty * $unitHpp;
        $status = $qty < 0 ? 'Stok Minus' : 'Stok Plus';

        return [
            $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-',
            $item->doc_ref ?: '-',
            $item->product->barcode ?? '-',
            $item->product->name ?? '-',
            $item->product->category->name ?? '-',
            $item->batch->batch_no ?? '-',
            $status,
            $qty,
            $item->product->unit->name ?? 'pcs',
            $unitHpp,
            $varianceValue,
            $item->description ?: '-',
            $item->user->name ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'No. Referensi',
            'Kode / Barcode',
            'Nama Produk',
            'Kategori',
            'No. Batch',
            'Jenis Selisih',
            'Qty Selisih',
            'Satuan',
            'HPP / Satuan (Rp)',
            'Nilai Selisih (Rp)',
            'Keterangan / Alasan',
            'Petugas',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        return [
            1 => ['font' => ['bold' => true]],
            ($lastRow - 3) => ['font' => ['bold' => true, 'color' => ['rgb' => 'C62828']]], // Stok Minus
            ($lastRow - 2) => ['font' => ['bold' => true, 'color' => ['rgb' => '2E7D32']]], // Stok Plus
            ($lastRow - 1) => ['font' => ['bold' => true]], // Total Selisih Bersih
            $lastRow => ['font' => ['bold' => true]], // Total Akumulasi Selisih
        ];
    }
}
