<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            // ========== ASET (ASSETS) ==========
            // Current Assets
            [
                'code' => '1-1100',
                'name' => 'Kas',
                'type' => 'asset',
                'category' => 'current_asset',
                'is_system' => true,
            ],
            [
                'code' => '1-1200',
                'name' => 'Bank',
                'type' => 'asset',
                'category' => 'current_asset',
                'is_system' => true,
            ],
            [
                'code' => '1-1300',
                'name' => 'Piutang Usaha',
                'type' => 'asset',
                'category' => 'current_asset',
                'is_system' => true,
            ],
            [
                'code' => '1-1400',
                'name' => 'Persediaan Obat',
                'type' => 'asset',
                'category' => 'current_asset',
                'is_system' => true,
            ],
            
            // Fixed Assets
            [
                'code' => '1-2000',
                'name' => 'Peralatan',
                'type' => 'asset',
                'category' => 'fixed_asset',
                'is_system' => false,
            ],
            [
                'code' => '1-2100',
                'name' => 'Akumulasi Penyusutan Peralatan',
                'type' => 'asset',
                'category' => 'fixed_asset',
                'is_system' => false,
            ],

            // ========== LIABILITAS (LIABILITIES) ==========
            [
                'code' => '2-1100',
                'name' => 'Utang Usaha',
                'type' => 'liability',
                'category' => 'current_liability',
                'is_system' => true,
            ],
            [
                'code' => '2-1200',
                'name' => 'Utang Jatuh Tempo',
                'type' => 'liability',
                'category' => 'current_liability',
                'is_system' => true,
            ],
            [
                'code' => '2-2000',
                'name' => 'Utang Bank Jangka Panjang',
                'type' => 'liability',
                'category' => 'long_term_liability',
                'is_system' => false,
            ],

            // ========== EKUITAS (EQUITY) ==========
            [
                'code' => '3-1000',
                'name' => 'Modal',
                'type' => 'equity',
                'category' => 'capital',
                'is_system' => true,
            ],
            [
                'code' => '3-2000',
                'name' => 'Laba Ditahan',
                'type' => 'equity',
                'category' => 'capital',
                'is_system' => true,
            ],

            // ========== PENDAPATAN (REVENUE) ==========
            [
                'code' => '4-1000',
                'name' => 'Penjualan Obat',
                'type' => 'revenue',
                'category' => 'sales',
                'is_system' => true,
            ],
            [
                'code' => '4-1100',
                'name' => 'Retur Penjualan',
                'type' => 'revenue',
                'category' => 'sales',
                'is_system' => true,
            ],
            [
                'code' => '4-2000',
                'name' => 'Pendapatan Lain-lain',
                'type' => 'revenue',
                'category' => 'other',
                'is_system' => false,
            ],

            // ========== BEBAN (EXPENSES) ==========
            [
                'code' => '5-1000',
                'name' => 'Harga Pokok Penjualan (COGS)',
                'type' => 'expense',
                'category' => 'cogs',
                'is_system' => true,
            ],
            [
                'code' => '5-1100',
                'name' => 'Retur Pembelian',
                'type' => 'expense',
                'category' => 'cogs',
                'is_system' => true,
            ],
            [
                'code' => '5-2000',
                'name' => 'Beban Gaji',
                'type' => 'expense',
                'category' => 'operating_expense',
                'is_system' => false,
            ],
            [
                'code' => '5-2100',
                'name' => 'Beban Listrik & Air',
                'type' => 'expense',
                'category' => 'operating_expense',
                'is_system' => false,
            ],
            [
                'code' => '5-2200',
                'name' => 'Beban Sewa',
                'type' => 'expense',
                'category' => 'operating_expense',
                'is_system' => false,
            ],
            [
                'code' => '5-2300',
                'name' => 'Beban Operasional Lainnya',
                'type' => 'expense',
                'category' => 'operating_expense',
                'is_system' => false,
            ],
            [
                'code' => '5-2400',
                'name' => 'Beban Penyusutan',
                'type' => 'expense',
                'category' => 'operating_expense',
                'is_system' => false,
            ],
        ];

        foreach ($accounts as $account) {
            Account::create($account);
        }
    }
}
