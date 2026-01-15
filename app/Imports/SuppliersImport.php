<?php

namespace App\Imports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SuppliersImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new Supplier([
            'name'           => $row['nama_supplier'],
            'contact_person' => $row['kontak'] ?? null,
            'phone'          => $row['telepon'] ?? null,
            'address'        => $row['alamat'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_supplier' => 'required|string|max:255',
            'telepon'       => 'nullable|string|max:20',
        ];
    }
}
