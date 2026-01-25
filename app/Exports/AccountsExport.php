<?php

namespace App\Exports;

use App\Models\Account;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AccountsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    use Exportable;

    protected $search;
    protected $typeFilter;

    public function __construct($search = '', $typeFilter = '')
    {
        $this->search = $search;
        $this->typeFilter = $typeFilter;
    }

    public function query()
    {
        $query = Account::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        return $query->orderBy('code');
    }

    public function map($account): array
    {
        return [
            $account->code,
            $account->name,
            ucfirst($account->type),
            str_replace('_', ' ', ucfirst($account->category)),
            $account->balance,
            $account->is_active ? 'Aktif' : 'Non-Aktif',
        ];
    }

    public function headings(): array
    {
        return [
            'Kode',
            'Nama Akun',
            'Tipe',
            'Kategori',
            'Saldo (Rp)',
            'Status',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
