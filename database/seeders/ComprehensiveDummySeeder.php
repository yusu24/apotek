<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ComprehensiveDummySeeder extends Seeder
{
    /**
     * Run the comprehensive dummy seeders.
     */
    public function run(): void
    {
        $this->command->info('=== Memulai Seeding Data Dummy Apotek Komprehensif ===');

        // 1. Roles & Permissions
        $this->command->info('1. Memastikan Role & Permission...');
        $this->call(RoleSeeder::class);

        // 2. Chart of Accounts (COA)
        $this->command->info('2. Memastikan Chart of Accounts (Akun Akuntansi)...');
        $this->call(AccountSeeder::class);

        // 3. Kategori Pengeluaran
        $this->command->info('3. Memastikan Kategori Pengeluaran...');
        $this->call(ExpenseCategorySeeder::class);

        // 4. Data Supplier
        $this->command->info('4. Seeding Data Supplier Distributor...');
        $this->call(SupplierSeeder::class);

        // 5. Data Pelanggan / Pasien
        $this->command->info('5. Seeding Data Pelanggan & Pasien...');
        $this->call(CustomerSeeder::class);

        // 6. Produk & Batch (Katalog Obat Komprehensif)
        $this->command->info('6. Seeding Katalog Produk Obat & Batch Inventaris...');
        $this->call(ProductSeeder::class);

        // 7. Pengadaan, Hutang (Goods Receipts), dan Piutang (Receivables)
        $this->command->info('7. Seeding Pengadaan & Hutang Piutang (Financial Overview)...');
        $this->call(ProcurementFinancialSeeder::class);

        // 8. Transaksi Kasir (60 Hari Termasuk Hari Ini)
        $this->command->info('8. Seeding Transaksi Penjualan Kasir (60 Hari + Hari Ini)...');
        $this->call(CashierDummySeeder::class);

        // 9. Beban & Biaya Operasional Apotek
        $this->command->info('9. Seeding Beban & Pengeluaran Operasional...');
        $this->call(ExpenseSeeder::class);

        $this->command->info('=== Selesai Seeding Data Dummy Apotek Komprehensif! ===');
    }
}
