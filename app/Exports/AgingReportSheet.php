<?php

namespace App\Exports;

use App\Services\AccountingService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AgingReportSheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    protected $type;
    protected $showPaid;

    public function __construct($type, $showPaid)
    {
        $this->type = $type;
        $this->showPaid = filter_var($showPaid, FILTER_VALIDATE_BOOLEAN);
    }

    public function collection()
    {
        $accountingService = new AccountingService();
        $data = null;

        if ($this->type === 'ar') {
            $data = $accountingService->getArAgingReport($this->showPaid);
        } else {
            $data = $accountingService->getApAgingReport($this->showPaid);
        }

        // Flatten the grouped data
        $flattened = [];
        $buckets = ['0-7', '8-15', '16-30', '31-45', '45+'];
        
        foreach ($buckets as $bucket) {
            if (isset($data[$bucket]) && is_array($data[$bucket])) {
                foreach ($data[$bucket] as $item) {
                     // Convert array to object if necessary or keep as array. 
                     // The Service returns arrays of items.
                     // We'll wrap it in a collection or just array merge.
                     $flattened[] = (object) $item;
                }
            }
        }
        
        // Sort by date or id if needed, but they are already sorted by age in buckets.
        // Let's sort by Date Descending for better readability in one list?
        // Or keep Age Descending (Risk based). Let's keep existing order (Age).

        return collect($flattened);
    }

    public function map($item): array
    {
        // $item is an object cast from array
        // Expected keys from Service: supplier/customer, invoice_number, date, due_date, total_amount, outstanding ...

        $status = 'Belum Lunas';
        if ($item->outstanding <= 0) {
            $status = 'Lunas';
        } else {
             // Re-calculate or use age passing from service if available
             // Service returns 'age' directly
             $age = $item->age ?? 0;
             if ($age > 0) {
                $status = 'Jatuh Tempo ' . $age . ' Hari';
             } else {
                $status = 'Belum Jatuh Tempo';
             }
        }
        
        $entityName = $this->type === 'ar' ? ($item->customer ?? 'Umum') : ($item->supplier ?? 'Unknown');

        return [
            $entityName,
            $item->invoice_number ?? '-',
            \Carbon\Carbon::parse($item->date)->format('d/m/Y'),
            ($item->due_date && $item->due_date !== '-') ? \Carbon\Carbon::parse($item->due_date)->format('d/m/Y') : '-',
            $item->total_amount,
            ($item->total_amount - $item->outstanding), // Paid Amount logic
            $item->outstanding,
            $item->age ?? 0,
            $status
        ];
    }

    public function headings(): array
    {
        $typeName = $this->type === 'ar' ? 'Piutang (AR)' : 'Hutang (AP)';
        
        return [
            ['Laporan Umur ' . $typeName],
            ['Tanggal Export: ' . now()->format('d/m/Y H:i')],
            [''],
            [
                $this->type === 'ar' ? 'Pelanggan' : 'Supplier',
                'No. Transaksi',
                'Tanggal',
                'Jatuh Tempo',
                'Total (Rp)',
                'Dibayar (Rp)',
                'Sisa (Rp)',
                'Hari Terlambat',
                'Status'
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['italic' => true]],
            4 => ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE0E0E0']]],
        ];
    }

    public function title(): string
    {
        return $this->type === 'ar' ? 'Piutang (AR)' : 'Hutang (AP)';
    }
}
