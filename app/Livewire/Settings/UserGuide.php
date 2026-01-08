<?php

namespace App\Livewire\Settings;

use Livewire\Component;

class UserGuide extends Component
{
    public $search = '';

    public function mount()
    {
        if (!auth()->user()->can('view guide')) {
            abort(403, 'Unauthorized');
        }
    }

    public function render()
    {
        $allGuides = [
            [
                'title' => 'Dashboard & Statistik',
                'slug' => 'dashboard',
                'description' => 'Memahami ringkasan performa penjualan, stok kritis, dan grafik pendapatan harian.',
                'updated_at' => '24 Dec 2025',
                'icon' => 'chart-bar',
                'color' => 'blue',
                'category' => 'Analisis'
            ],
            [
                'title' => 'Manajemen Produk',
                'slug' => 'master',
                'description' => 'Cara menambah obat baru, mengatur kategori, serta mengelola satuan dan konversi.',
                'updated_at' => '29 Dec 2025',
                'icon' => 'beaker',
                'color' => 'indigo',
                'category' => 'Master Data'
            ],
            [
                'title' => 'Transaksi Kasir (POS)',
                'slug' => 'pos',
                'description' => 'Panduan lengkap proses penjualan, penanganan diskon, dan pencetakan struk belanja.',
                'updated_at' => '24 Dec 2025',
                'icon' => 'shopping-cart',
                'color' => 'green',
                'category' => 'Transaksi'
            ],
            [
                'title' => 'Stok & Inventori',
                'slug' => 'stock',
                'description' => 'Mengelola stok opname, mutasi barang, dan pelacakan riwayat stok per batch.',
                'updated_at' => '22 Dec 2025',
                'icon' => 'archive-box',
                'color' => 'orange',
                'category' => 'Gudang'
            ],
            [
                'title' => 'Pengadaan (Procurement)',
                'slug' => 'procurement',
                'description' => 'Proses pembuatan Purchase Order (PO) hingga penerimaan barang dari supplier.',
                'updated_at' => '23 Dec 2025',
                'icon' => 'truck',
                'color' => 'purple',
                'category' => 'Transaksi'
            ],
            [
                'title' => 'Laporan Keuangan',
                'slug' => 'reports',
                'description' => 'Analisis laba rugi, laporan pengeluaran operasional, dan rekapitulasi pajak (PPN).',
                'updated_at' => '29 Dec 2025',
                'icon' => 'document-text',
                'color' => 'rose',
                'category' => 'Laporan'
            ],
            [
                'title' => 'Pengaturan Profil',
                'slug' => 'profile',
                'description' => 'Mengelola informasi pribadi, keamanan akun, dan pembaruan password user.',
                'updated_at' => '24 Dec 2025',
                'icon' => 'user-circle',
                'color' => 'cyan',
                'category' => 'Personal'
            ],
            [
                'title' => 'Pengaturan Sistem',
                'slug' => 'settings',
                'description' => 'Konfigurasi identitas toko, manajemen hak akses user, dan pengaturan sistem lainnya.',
                'updated_at' => '24 Dec 2025',
                'icon' => 'cog-6-tooth',
                'color' => 'slate',
                'category' => 'Sistem'
            ],
            [
                'title' => 'Buku Panduan Lengkap',
                'slug' => 'user-manual',
                'description' => 'Tutorial penggunaan aplikasi apotek dari awal sampai selesai. Mencakup semua fitur dari setup awal hingga laporan.',
                'updated_at' => '08 Jan 2026',
                'icon' => 'book-open',
                'color' => 'emerald',
                'category' => 'Panduan'
            ],
        ];

        $guides = $allGuides;

        if ($this->search) {
            $guides = array_filter($allGuides, function($guide) {
                return str_contains(strtolower($guide['title']), strtolower($this->search)) || 
                       str_contains(strtolower($guide['description']), strtolower($this->search)) ||
                       str_contains(strtolower($guide['category']), strtolower($this->search));
            });
        }

        return view('livewire.settings.user-guide', [
            'guides' => $guides
        ]);
    }
}
