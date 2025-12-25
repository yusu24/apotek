<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Operasional', 'is_active' => true],
            ['name' => 'HPP', 'is_active' => true],
            ['name' => 'Administrasi', 'is_active' => true],
            ['name' => 'Penjualan', 'is_active' => true],
            ['name' => 'Sewa & Perawatan', 'is_active' => true],
            ['name' => 'Penyusutan', 'is_active' => true],
            ['name' => 'Non-operasional', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
