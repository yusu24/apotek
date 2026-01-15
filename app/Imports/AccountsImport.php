<?php

namespace App\Imports;

use App\Models\Account;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class AccountsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new Account([
            'code'      => $row['kode_akun'],
            'name'      => $row['nama_akun'],
            'type'      => $row['tipe'],
            'category'  => $row['kategori'],
            'is_active' => true,
            'is_system' => false,
            'balance'   => 0,
        ]);
    }

    public function rules(): array
    {
        return [
            'kode_akun' => 'required|string|unique:accounts,code',
            'nama_akun' => 'required|string|max:255',
            'tipe'      => 'required|in:asset,liability,equity,revenue,expense',
            'kategori'  => 'required|string',
        ];
    }
}
