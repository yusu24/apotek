<?php

namespace App\Livewire\Settings;

use Livewire\Component;

class UserGuide extends Component
{
    public function render()
    {
        $guides = [
            [
                'title' => 'Dashboard & Statistik',
                'slug' => 'dashboard',
                'description' => 'Memahami ringkasan performa penjualan, stok kritis, dan grafik pendapatan harian.',
                'updated_at' => '24 Dec 2025',
                'icon' => 'chart-bar',
                'color' => 'blue'
            ],
            [
                'title' => 'Manajemen Produk',
                'slug' => 'master',
                'description' => 'Cara menambah obat baru, mengatur kategori, serta mengelola satuan dan konversi.',
                'updated_at' => '23 Dec 2025',
                'icon' => 'beaker',
                'color' => 'indigo'
            ],
            [
                'title' => 'Transaksi Kasir (POS)',
                'slug' => 'pos',
                'description' => 'Panduan lengkap proses penjualan, penanganan diskon, dan pencetakan struk belanja.',
                'updated_at' => '24 Dec 2025',
                'icon' => 'shopping-cart',
                'color' => 'green'
            ],
            [
                'title' => 'Stok & Inventori',
                'slug' => 'stock',
                'description' => 'Mengelola stok opname, mutasi barang, dan pelacakan riwayat stok per batch.',
                'updated_at' => '22 Dec 2025',
                'icon' => 'archive-box',
                'color' => 'orange'
            ],
            [
                'title' => 'Pengadaan (Procurement)',
                'slug' => 'procurement',
                'description' => 'Proses pembuatan Purchase Order (PO) hingga penerimaan barang dari supplier.',
                'updated_at' => '23 Dec 2025',
                'icon' => 'truck',
                'color' => 'purple'
            ],
            [
                'title' => 'Laporan Keuangan',
                'slug' => 'reports',
                'description' => 'Analisis laba rugi, laporan pengeluaran operasional, dan rekapitulasi pajak (PPN).',
                'updated_at' => '24 Dec 2025',
                'icon' => 'document-text',
                'color' => 'rose'
            ],
            [
                'title' => 'Pengaturan Profil',
                'slug' => 'profile',
                'description' => 'Mengelola informasi pribadi, keamanan akun, dan pembaruan password user.',
                'updated_at' => '24 Dec 2025',
                'icon' => 'user-circle',
                'color' => 'cyan'
            ],
            [
                'title' => 'Pengaturan Sistem',
                'slug' => 'settings',
                'description' => 'Konfigurasi identitas toko, manajemen hak akses user, dan pengaturan sistem lainnya.',
                'updated_at' => '24 Dec 2025',
                'icon' => 'cog-6-tooth',
                'color' => 'slate'
            ],
        ];

        return view('livewire.settings.user-guide', [
            'guides' => $guides
        ]);
    }
}
