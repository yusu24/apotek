<?php

namespace App\Imports;

use App\Models\ExpenseCategory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class ExpenseCategoriesImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Skip if name is empty
        if (empty($row['nama_kategori'])) {
            return null;
        }

        // Update or create based on name (case-insensitive check could be good but exact match is fine for now)
        return ExpenseCategory::updateOrCreate(
            ['name' => $row['nama_kategori']],
            [
                'description' => $row['deskripsi'] ?? null,
            ]
        );
    }
}
