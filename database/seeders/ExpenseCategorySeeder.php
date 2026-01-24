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
            // Beban Pokok Penjualan
            ['name' => 'Beban Pokok Penjualan (HPP)', 'is_active' => true],
            
            // Beban Operasional
            ['name' => 'Beban Gaji & Upah', 'is_active' => true],
            ['name' => 'Beban Listrik & Air', 'is_active' => true],
            ['name' => 'Beban Telepon & Internet', 'is_active' => true],
            ['name' => 'Beban Sewa', 'is_active' => true],
            ['name' => 'Beban Pemeliharaan & Perbaikan', 'is_active' => true],
            ['name' => 'Beban Transportasi', 'is_active' => true],
            ['name' => 'Beban Supplies & ATK', 'is_active' => true],
            
            // Beban Administrasi & Umum
            ['name' => 'Beban Administrasi', 'is_active' => true],
            ['name' => 'Beban Bank & Administrasi', 'is_active' => true],
            ['name' => 'Beban Asuransi', 'is_active' => true],
            
            // Beban Penjualan & Pemasaran
            ['name' => 'Beban Iklan & Promosi', 'is_active' => true],
            ['name' => 'Beban Pengiriman', 'is_active' => true],
            
            // Beban Penyusutan
            ['name' => 'Beban Penyusutan Aset', 'is_active' => true],
            
            // Beban Pajak
            ['name' => 'Beban Pajak Penghasilan', 'is_active' => true],
            ['name' => 'Beban Pajak Lainnya', 'is_active' => true],
            
            // Beban Lain-lain
            ['name' => 'Beban Lain-lain', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
