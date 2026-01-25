<?php

namespace App\Imports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;

class CategoriesImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new Category([
            'name' => $row['nama_kategori'],
            'slug' => Str::slug($row['nama_kategori']),
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_kategori' => 'required|string|max:255',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_kategori.required' => 'Nama kategori wajib diisi',
            'nama_kategori.max' => 'Nama kategori maksimal 255 karakter',
        ];
    }
}
