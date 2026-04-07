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
                'code'         => '1-1100',
                'name'         => 'Kas',
                'type'         => 'asset',
                'category'     => 'current_asset',
                'sub_category' => 'cash',
                'is_system'    => true,
            ],
            [
                'code'         => '1-1200',
                'name'         => 'Bank',
                'type'         => 'asset',
                'category'     => 'current_asset',
                'sub_category' => 'cash',
                'is_system'    => true,
            ],
            [
                'code'         => '1-1300',
                'name'         => 'Piutang Usaha',
                'type'         => 'asset',
                'category'     => 'current_asset',
                'sub_category' => 'receivable',
                'is_system'    => true,
            ],
            [
                'code'         => '1-1400',
                'name'         => 'Persediaan Obat',
                'type'         => 'asset',
                'category'     => 'current_asset',
                'sub_category' => 'inventory',
                'is_system'    => true,
            ],

            // Fixed Assets
            [
                'code'         => '1-3100',
                'name'         => 'Peralatan',
                'type'         => 'asset',
                'category'     => 'fixed_asset',
                'sub_category' => 'equipment',
                'is_system'    => false,
            ],
            [
                'code'         => '1-3110',
                'name'         => 'Akumulasi Penyusutan Peralatan',
                'type'         => 'asset',
                'category'     => 'fixed_asset',
                'sub_category' => 'equipment',
                'is_system'    => false,
            ],
            [
                'code'         => '1-3200',
                'name'         => 'Kendaraan',
                'type'         => 'asset',
                'category'     => 'fixed_asset',
                'sub_category' => 'vehicle',
                'is_system'    => false,
            ],
            [
                'code'         => '1-3210',
                'name'         => 'Akumulasi Penyusutan Kendaraan',
                'type'         => 'asset',
                'category'     => 'fixed_asset',
                'sub_category' => 'vehicle',
                'is_system'    => false,
            ],
            [
                'code'         => '1-3300',
                'name'         => 'Tanah',
                'type'         => 'asset',
                'category'     => 'fixed_asset',
                'sub_category' => 'land',
                'is_system'    => false,
            ],
            [
                'code'         => '1-3400',
                'name'         => 'Bangunan Usaha',
                'type'         => 'asset',
                'category'     => 'fixed_asset',
                'sub_category' => 'building',
                'is_system'    => false,
            ],
            [
                'code'         => '1-3410',
                'name'         => 'Akumulasi Penyusutan Bangunan',
                'type'         => 'asset',
                'category'     => 'fixed_asset',
                'sub_category' => 'building',
                'is_system'    => false,
            ],

            // ========== LIABILITAS LANCAR (CURRENT LIABILITIES) ==========
            // 1. Hutang Dagang
            [
                'code'         => '2-1100',
                'name'         => 'Hutang Dagang',
                'type'         => 'liability',
                'category'     => 'current_liability',
                'sub_category' => 'trade_payable',
                'is_system'    => true,
            ],
            [
                'code'         => '2-1200',
                'name'         => 'Hutang Jatuh Tempo',
                'type'         => 'liability',
                'category'     => 'current_liability',
                'sub_category' => 'trade_payable',
                'is_system'    => true,
            ],

            // 2. Hutang Gaji
            [
                'code'         => '2-1300',
                'name'         => 'Hutang Gaji',
                'type'         => 'liability',
                'category'     => 'current_liability',
                'sub_category' => 'salary_payable',
                'is_system'    => false,
            ],

            // 3. Hutang Pajak
            [
                'code'         => '2-1400',
                'name'         => 'PPN Keluaran',
                'type'         => 'liability',
                'category'     => 'current_liability',
                'sub_category' => 'tax_payable',
                'is_system'    => true,
            ],
            [
                'code'         => '2-1410',
                'name'         => 'PPh 21 Terutang',
                'type'         => 'liability',
                'category'     => 'current_liability',
                'sub_category' => 'tax_payable',
                'is_system'    => false,
            ],

            // 4. Hutang Bank/Lembaga Keuangan (Jangka Pendek)
            [
                'code'         => '2-1500',
                'name'         => 'Hutang Bank Jangka Pendek',
                'type'         => 'liability',
                'category'     => 'current_liability',
                'sub_category' => 'bank_payable_current',
                'is_system'    => false,
            ],
            [
                'code'         => '2-1510',
                'name'         => 'Hutang Lembaga Keuangan Jangka Pendek',
                'type'         => 'liability',
                'category'     => 'current_liability',
                'sub_category' => 'bank_payable_current',
                'is_system'    => false,
            ],

            // 5. Hutang Non-Lembaga Keuangan (Jangka Pendek)
            [
                'code'         => '2-1600',
                'name'         => 'Hutang Non-Lembaga Keuangan Jangka Pendek',
                'type'         => 'liability',
                'category'     => 'current_liability',
                'sub_category' => 'non_bank_payable_current',
                'is_system'    => false,
            ],

            // ========== LIABILITAS JANGKA PANJANG (LONG-TERM LIABILITIES) ==========
            // 1. Hutang Bank/Lembaga Keuangan (Jangka Panjang)
            [
                'code'         => '2-2000',
                'name'         => 'Hutang Bank Jangka Panjang',
                'type'         => 'liability',
                'category'     => 'long_term_liability',
                'sub_category' => 'bank_payable_longterm',
                'is_system'    => false,
            ],
            [
                'code'         => '2-2100',
                'name'         => 'Hutang Lembaga Keuangan Jangka Panjang',
                'type'         => 'liability',
                'category'     => 'long_term_liability',
                'sub_category' => 'bank_payable_longterm',
                'is_system'    => false,
            ],

            // 2. Hutang Non-Lembaga Keuangan (Jangka Panjang)
            [
                'code'         => '2-2200',
                'name'         => 'Hutang Non-Lembaga Keuangan Jangka Panjang',
                'type'         => 'liability',
                'category'     => 'long_term_liability',
                'sub_category' => 'non_bank_payable_longterm',
                'is_system'    => false,
            ],

            // ========== EKUITAS / MODAL (EQUITY) ==========
            // 1. Modal Sendiri
            [
                'code'         => '3-1000',
                'name'         => 'Modal Sendiri',
                'type'         => 'equity',
                'category'     => 'capital',
                'sub_category' => 'paid_in_capital',
                'is_system'    => true,
            ],

            // 2. Laba Ditahan
            [
                'code'         => '3-2000',
                'name'         => 'Laba Ditahan',
                'type'         => 'equity',
                'category'     => 'capital',
                'sub_category' => 'retained_earnings',
                'is_system'    => true,
            ],
            // Note: Laba Tahun Berjalan dihitung otomatis (Revenue - Expense), tidak perlu akun GL terpisah.

            // ========== PENDAPATAN (REVENUE) ==========
            [
                'code'      => '4-1000',
                'name'      => 'Penjualan Obat',
                'type'      => 'revenue',
                'category'  => 'sales',
                'is_system' => true,
            ],
            [
                'code'      => '4-1100',
                'name'      => 'Retur Penjualan',
                'type'      => 'revenue',
                'category'  => 'sales',
                'is_system' => true,
            ],
            [
                'code'      => '4-2000',
                'name'      => 'Pendapatan Lain-lain',
                'type'      => 'revenue',
                'category'  => 'other',
                'is_system' => false,
            ],

            // ========== BEBAN (EXPENSES) ==========
            [
                'code'      => '5-1000',
                'name'      => 'Harga Pokok Penjualan (COGS)',
                'type'      => 'expense',
                'category'  => 'cogs',
                'is_system' => true,
            ],
            [
                'code'      => '5-1100',
                'name'      => 'Retur Pembelian',
                'type'      => 'expense',
                'category'  => 'cogs',
                'is_system' => true,
            ],
            [
                'code'      => '5-2000',
                'name'      => 'Beban Gaji',
                'type'      => 'expense',
                'category'  => 'operating_expense',
                'is_system' => false,
            ],
            [
                'code'      => '5-2100',
                'name'      => 'Beban Listrik & Air',
                'type'      => 'expense',
                'category'  => 'operating_expense',
                'is_system' => false,
            ],
            [
                'code'      => '5-2200',
                'name'      => 'Beban Sewa',
                'type'      => 'expense',
                'category'  => 'operating_expense',
                'is_system' => false,
            ],
            [
                'code'      => '5-2300',
                'name'      => 'Beban Operasional Lainnya',
                'type'      => 'expense',
                'category'  => 'operating_expense',
                'is_system' => false,
            ],
            [
                'code'      => '5-2400',
                'name'      => 'Beban Penyusutan',
                'type'      => 'expense',
                'category'  => 'operating_expense',
                'is_system' => false,
            ],
        ];

        foreach ($accounts as $account) {
            Account::updateOrCreate(
                ['code' => $account['code']],
                $account
            );
        }
    }
}
