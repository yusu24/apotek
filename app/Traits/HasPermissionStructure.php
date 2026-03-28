<?php

namespace App\Traits;

trait HasPermissionStructure
{
    public function getPermissionStructureProperty()
    {
        return [
            'Dashboard' => [
                'icon' => 'home',
                'color' => 'blue',
                'items' => [
                    'view dashboard' => ['label' => 'Halaman Dashboard (Utama)', 'type' => 'view'],
                    'view dashboard receivables' => ['label' => 'Ringkasan Piutang di Dashboard', 'type' => 'view'],
                    'view dashboard payables' => ['label' => 'Ringkasan Hutang di Dashboard', 'type' => 'view'],
                ]
            ],
            'Kasir (POS)' => [
                'icon' => 'shopping-cart',
                'color' => 'purple',
                'items' => [
                    'access pos' => ['label' => 'Akses Mesin Kasir', 'type' => 'view'],
                    'view sales history' => ['label' => 'Riwayat Penjualan (Kasir)', 'type' => 'view'],
                ]
            ],
            'Stok & Pengadaan' => [
                'icon' => 'archive',
                'color' => 'orange',
                'items' => [
                    'view stock' => ['label' => 'Lihat Stok & Opname', 'type' => 'view'],
                    'import stock' => ['label' => 'Import Stok via Excel', 'type' => 'action'],
                    'adjust stock' => ['label' => 'Penyesuaian Stok', 'type' => 'action'],
                    'view stock movements' => ['label' => 'Riwayat Mutasi Stok', 'type' => 'view'],
                    'view purchase orders' => ['label' => 'Pesanan Pembelian (PO)', 'type' => 'view'],
                    'view goods receipts' => ['label' => 'Penerimaan Pesanan', 'type' => 'view'],
                ]
            ],
            'Retur Barang' => [
                'icon' => 'refresh',
                'color' => 'red',
                'items' => [
                    'manage sales returns' => ['label' => 'Retur Penjualan', 'type' => 'view'],
                    'manage purchase returns' => ['label' => 'Retur Pembelian', 'type' => 'view'],
                ]
            ],
            'Data Master' => [
                'icon' => 'database',
                'color' => 'green',
                'items' => [
                    'view products' => ['label' => 'Lihat Produk', 'type' => 'view'],
                    'create products' => ['label' => 'Tambah Produk', 'type' => 'action'],
                    'edit products' => ['label' => 'Edit Produk', 'type' => 'action'],
                    'delete products' => ['label' => 'Hapus Produk', 'type' => 'action'],
                    'manage categories' => ['label' => 'Kategori Produk', 'type' => 'view'],
                    'manage units' => ['label' => 'Master Satuan', 'type' => 'view'],
                    'manage product units' => ['label' => 'Konversi Satuan', 'type' => 'view'],
                    'manage suppliers' => ['label' => 'Supplier', 'type' => 'view'],
                    'manage customers' => ['label' => 'Pelanggan', 'type' => 'view'],
                ]
            ],
            'Laporan Keuangan' => [
                'icon' => 'chart-pie',
                'color' => 'indigo',
                'items' => [
                    'view trial balance' => ['label' => 'Neraca Saldo Awal', 'type' => 'view'],
                    'view balance sheet' => ['label' => 'Neraca Saldo Akhir', 'type' => 'view'],
                    'view profit loss' => ['label' => 'Laporan Laba Rugi', 'type' => 'view'],
                    'view income statement' => ['label' => 'Laporan Arus Kas', 'type' => 'view'],
                    'view general ledger' => ['label' => 'Buku Besar', 'type' => 'view'],
                    'view ppn report' => ['label' => 'Laporan PPN', 'type' => 'view'],
                    'view ap aging report' => ['label' => 'Laporan Umur Hutang & Piutang', 'type' => 'view'],
                ]
            ],
            'Laporan Operasional' => [
                'icon' => 'clipboard-list',
                'color' => 'teal',
                'items' => [
                    'view sales reports' => ['label' => 'Laporan Penjualan Detail', 'type' => 'view'],
                    'view stock' => ['label' => 'Laporan Stok', 'type' => 'view'],
                    'view product margin report' => ['label' => 'Laporan Margin Produk', 'type' => 'view'],
                    'view stock movements' => ['label' => 'Riwayat Transaksi Produk', 'type' => 'view'],
                ]
            ],
            'Keuangan & Administrasi' => [
                'icon' => 'calculator',
                'color' => 'cyan',
                'items' => [
                    'view accounts' => ['label' => 'Daftar Akun (COA)', 'type' => 'view'],
                    'view journals' => ['label' => 'Lihat Jurnal Umum', 'type' => 'view'],
                    'create journal' => ['label' => 'Input Jurnal Manual', 'type' => 'action'],
                    'edit journals' => ['label' => 'Edit Jurnal Draft', 'type' => 'action'],
                    'delete journals' => ['label' => 'Hapus/Reversal Jurnal', 'type' => 'action'],
                    'view opening balances' => ['label' => 'Lihat Neraca Awal', 'type' => 'view'],
                    'edit opening balances' => ['label' => 'Input/Edit Neraca Awal', 'type' => 'action'],
                    'lock opening balances' => ['label' => 'Kunci Neraca Awal', 'type' => 'action'],
                    'unlock opening balances' => ['label' => 'Buka Kunci Neraca Awal', 'type' => 'action'],
                    'view expenses' => ['label' => 'Daftar Pengeluaran', 'type' => 'view'],
                    'manage expense categories' => ['label' => 'Kategori Pengeluaran', 'type' => 'view'],
                ]
            ],
            'Pengaturan Sistem' => [
                'icon' => 'cog',
                'color' => 'gray',
                'items' => [
                    'manage settings' => ['label' => 'Identitas Toko', 'type' => 'view'],
                    'manage pos settings' => ['label' => 'Konfigurasi Kasir', 'type' => 'view'],
                    'manage users' => ['label' => 'Kelola User', 'type' => 'view'],
                    'view activity logs' => ['label' => 'Log Aktivitas', 'type' => 'view'],
                    'view audit log' => ['label' => 'Audit Log', 'type' => 'view'],
                ]
            ],
        ];
    }
}
