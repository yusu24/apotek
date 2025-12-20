<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center max-w-screen-2xl mx-auto">
            <h2 class="text-xl font-semibold text-slate-900 leading-tight">
                {{ __('Panduan Aplikasi (User Guide)') }}
            </h2>
            <button onclick="window.print()" class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-bold no-print">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print PDF
            </button>
        </div>
    </x-slot>

    <div class="py-12 max-w-5xl mx-auto guide-book">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8 md:p-12">
            
            <!-- Cover Page -->
            <div class="text-center border-b-2 border-gray-100 pb-12 mb-12 page-break">
                <h1 class="text-4xl font-extrabold text-blue-900 mb-4">BUKU PANDUAN PENGGUNA</h1>
                <h2 class="text-2xl font-bold text-gray-700 mb-8">Sistem Informasi Apotek (POS)</h2>
                <div class="text-sm text-gray-500">
                    <p>Versi 1.0</p>
                    <p>{{ date('Y') }}</p>
                </div>
            </div>

            <!-- Table of Contents -->
            <div class="mb-12 page-break">
                <h3 class="text-xl font-bold text-gray-900 mb-4 uppercase tracking-wider border-b border-gray-200 pb-2">Daftar Isi</h3>
                <ul class="space-y-2 text-gray-700">
                    <li><a href="#login" class="hover:text-blue-600">1. Login & Akses Sistem</a></li>
                    <li><a href="#dashboard" class="hover:text-blue-600">2. Dashboard</a></li>
                    <li><a href="#master" class="hover:text-blue-600">3. Master Data (Produk)</a></li>
                    <li><a href="#stock" class="hover:text-blue-600">4. Manajemen Stok</a></li>
                    <li><a href="#pos" class="hover:text-blue-600">5. Kasir (Point of Sale)</a></li>
                    <li><a href="#procurement" class="hover:text-blue-600">6. Pengadaan (Purchase Order)</a></li>
                    <li><a href="#reports" class="hover:text-blue-600">7. Laporan & Keuangan</a></li>
                    <li><a href="#settings" class="hover:text-blue-600">8. Pengaturan & User</a></li>
                </ul>
            </div>

            <!-- Section 1: Login -->
            <section id="login" class="mb-16">
                <div class="flex items-center gap-3 mb-6">
                    <span class="bg-blue-600 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold">1</span>
                    <h3 class="text-2xl font-bold text-gray-900">Login & Akses Sistem</h3>
                </div>
                <div class="prose max-w-none text-gray-600 space-y-4">
                    <p>Untuk mengakses sistem, pengguna harus masuk menggunakan email dan password yang telah didaftarkan.</p>
                    <ol class="list-decimal pl-5 space-y-2">
                        <li>Buka halaman login sistem.</li>
                        <li>Masukkan <strong>Email</strong> user (contoh: admin@apotek.com).</li>
                        <li>Masukkan <strong>Password</strong>.</li>
                        <li>Klik tombol <strong>Log in</strong>.</li>
                    </ol>
                    <div class="my-6 border rounded-lg overflow-hidden shadow-lg">
                        <img src="{{ asset('images/guide/login.png') }}" alt="Screenshot Login" class="w-full">
                    </div>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <p class="text-sm text-yellow-700"><strong>Catatan:</strong> Jika lupa password, hubungi Super Admin untuk reset password.</p>
                    </div>
                </div>
            </section>

            <hr class="my-10 border-gray-200">

            <!-- Section 2: Dashboard -->
            <section id="dashboard" class="mb-16">
                <div class="flex items-center gap-3 mb-6">
                    <span class="bg-blue-600 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold">2</span>
                    <h3 class="text-2xl font-bold text-gray-900">Dashboard</h3>
                </div>
                <div class="prose max-w-none text-gray-600 space-y-4">
                    <p>Halaman utama yang menampilkan ringkasan performa apotek.</p>
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Ringkasan Penjualan:</strong> Total penjualan hari ini, bulan ini, dll.</li>
                        <li><strong>Stok Kritis:</strong> Daftar obat yang stoknya menipis.</li>
                        <li><strong>Grafik Tren:</strong> Visualisasi pendapatan.</li>
                    </ul>
                    <div class="my-6 border rounded-lg overflow-hidden shadow-lg">
                        <img src="{{ asset('images/guide/dashboard.png') }}" alt="Screenshot Dashboard" class="w-full">
                    </div>
                </div>
            </section>

            <hr class="my-10 border-gray-200">

            <!-- Section 3: Master Data -->
            <section id="master" class="mb-16">
                <div class="flex items-center gap-3 mb-6">
                    <span class="bg-blue-600 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold">3</span>
                    <h3 class="text-2xl font-bold text-gray-900">Master Data (Produk)</h3>
                </div>
                <div class="prose max-w-none text-gray-600 space-y-4">
                    <p>Menu untuk mengelola data obat dan produk.</p>
                    <h4 class="font-bold text-gray-800">Menambah Produk Baru:</h4>
                    <ol class="list-decimal pl-5 space-y-2">
                        <li>Masuk ke menu <strong>Obat / Produk</strong>.</li>
                        <li>Klik tombok <strong>+ Tambah Obat</strong>.</li>
                        <li>Isi formulir (Nama, Kategori, Harga Beli, Harga Jual, Satuan, Min Stok).</li>
                        <li>Klik <strong>Simpan</strong>.</li>
                    </ol>
                    <div class="my-6 border rounded-lg overflow-hidden shadow-lg">
                        <img src="{{ asset('images/guide/product.png') }}" alt="Screenshot Produk" class="w-full">
                    </div>
                </div>
            </section>

            <hr class="my-10 border-gray-200">

            <!-- Section 5: POS -->
            <section id="pos" class="mb-16">
                <div class="flex items-center gap-3 mb-6">
                    <span class="bg-blue-600 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold">5</span>
                    <h3 class="text-2xl font-bold text-gray-900">Kasir (Point of Sale)</h3>
                </div>
                <div class="prose max-w-none text-gray-600 space-y-4">
                    <p>Halaman utama untuk memproses transaksi penjualan ke pelanggan.</p>
                    
                    <h4 class="font-bold text-gray-800">Cara Melakukan Transaksi:</h4>
                    <ol class="list-decimal pl-5 space-y-2">
                        <li>Buka menu <strong>Kasir (POS)</strong>.</li>
                        <li>Cari produk menggunakan kolom pencarian (scan barcode atau ketik nama).</li>
                        <li>Klik produk untuk menambahkannya ke keranjang.</li>
                        <li>Atur jumlah (Qty) jika pembeli membeli lebih dari 1.</li>
                        <li>Jika sudah selesai, klik tombol <strong>Bayar</strong>.</li>
                        <li>Masukkan jumlah uang yang diterima, pilih metode pembayaran.</li>
                        <li>Klik <strong>Proses & Cetak</strong>.</li>
                    </ol>
                    
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 my-4">
                        <p class="text-sm text-blue-700"><strong>Info Stok:</strong> Sistem akan menolak pembayaran jika stok produk yang ada di keranjang melebihi stok yang tersedia di gudang.</p>
                    </div>

                    <div class="my-6 border rounded-lg overflow-hidden shadow-lg">
                        <img src="{{ asset('images/guide/pos.png') }}" alt="Screenshot POS" class="w-full">
                    </div>
                </div>
            </section>
            
            <hr class="my-10 border-gray-200">

            <!-- Section 6: Procurement -->
            <section id="procurement" class="mb-16">
                <div class="flex items-center gap-3 mb-6">
                    <span class="bg-blue-600 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold">6</span>
                    <h3 class="text-2xl font-bold text-gray-900">Pengadaan (Procurement)</h3>
                </div>
                <div class="prose max-w-none text-gray-600 space-y-4">
                    <p>Modul untuk membeli stok barang dari Supplier.</p>
                    
                    <h4 class="font-bold text-gray-800">1. Pesanan Pembelian (PO)</h4>
                    <p>Digunakan untuk memesan barang ke supplier.</p>
                    <ol class="list-decimal pl-5 space-y-2">
                        <li>Ke menu <strong>Pengadaan -> Pesanan (PO)</strong>.</li>
                        <li>Klik <strong>Buat PO</strong>.</li>
                        <li>Pilih Supplier dan tambahkan item barang yang ingin dibeli.</li>
                        <li>Cetak PO untuk dikirim ke Supplier.</li>
                    </ol>
                    
                    <h4 class="font-bold text-gray-800 mt-4">2. Penerimaan Barang</h4>
                    <p>Digunakan saat barang fisik datang dari supplier.</p>
                    <ol class="list-decimal pl-5 space-y-2">
                        <li>Ke menu <strong>Pengadaan -> Penerimaan</strong>.</li>
                        <li>Klik <strong>Terima Barang</strong>.</li>
                        <li>Pilih Nomor PO yang sesuai.</li>
                        <li>Cek kesesuaian jumlah barang fisik dengan dokumen.</li>
                        <li>Simpan penerimaan. <strong>Stok akan otomatis bertambah</strong>.</li>
                    </ol>
                </div>
            </section>

             <hr class="my-10 border-gray-200">

            <!-- Section 7: Reports -->
            <section id="reports" class="mb-16">
                <div class="flex items-center gap-3 mb-6">
                    <span class="bg-blue-600 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold">7</span>
                    <h3 class="text-2xl font-bold text-gray-900">Laporan & Analitik</h3>
                </div>
                <div class="prose max-w-none text-gray-600 space-y-4">
                    <p>Melihat performa bisnis melalui grafik dan data tabel.</p>
                    <ul class="list-disc pl-5 space-y-2">
                        <li>Buka menu <strong>Analitik -> Laporan Penjualan</strong>.</li>
                        <li>Gunakan filter <strong>Periode</strong> untuk melihat data Harian, Mingguan, Bulanan, atau Custom Tanggal.</li>
                        <li>Grafik akan menyesuaikan secara otomatis.</li>
                        <li>Arahkan mouse ke grafik untuk melihat detail nilai uang.</li>
                    <div class="my-6 border rounded-lg overflow-hidden shadow-lg">
                        <img src="{{ asset('images/guide/report.png') }}" alt="Screenshot Laporan" class="w-full">
                    </div>
                </div>
            </section>

        </div>
        
        <!-- Print Footer -->
        <div class="text-center text-gray-400 text-sm mt-8 pb-8 no-print">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>

    <!-- Print Styles -->
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .guide-book, .guide-book * {
                visibility: visible;
            }
            .guide-book {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
            }
            .no-print, nav, header {
                display: none !important;
            }
            .page-break {
                page-break-after: always;
            }
            .bg-gray-100 {
                border: 1px solid #ddd; /* Ensure placeholders visible in print */
            }
        }
        
        /* Smooth scrolling for anchor links */
        html {
            scroll-behavior: smooth;
        }
    </style>
</x-app-layout>
