<?php

namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExpensesExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithMapping
{
    protected $search;
    protected $dateFrom;
    protected $dateTo;

    public function __construct($search = '', $dateFrom = null, $dateTo = null)
    {
        $this->search = $search;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function collection()
    {
        return Expense::with(['user', 'account'])
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                      ->orWhere('category', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->dateFrom && $this->dateTo, function($query) {
                $query->whereBetween('date', [$this->dateFrom, $this->dateTo]);
            })
            ->when($this->dateFrom && !$this->dateTo, function($query) {
                $query->where('date', '>=', $this->dateFrom);
            })
            ->when(!$this->dateFrom && $this->dateTo, function($query) {
                $query->where('date', '<=', $this->dateTo);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Kategori',
            'Deskripsi',
            'Jumlah (Rp)',
            'Metode Pembayaran (Akun)',
            'Dicatat Oleh',
        ];
    }

    public function map($expense): array
    {
        return [
            $expense->date ? $expense->date->format('d/m/Y') : '-',
            $expense->category,
            $expense->description,
            $expense->amount,
            $expense->account ? ($expense->account->code . ' - ' . $expense->account->name) : 'Tanpa Akun (Non-Jurnal)',
            $expense->user ? $expense->user->name : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
