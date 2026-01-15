<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CustomersImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new Customer([
            'name'    => $row['nama_pelanggan'],
            'phone'   => $row['telepon'] ?? null,
            'address' => $row['alamat'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_pelanggan' => 'required|string|max:255',
            'telepon'        => 'nullable|string|max:20',
            'alamat'         => 'nullable|string',
        ];
    }
}
