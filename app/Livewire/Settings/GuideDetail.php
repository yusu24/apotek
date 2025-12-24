<?php

namespace App\Livewire\Settings;

use Livewire\Component;

class GuideDetail extends Component
{
    public $slug;
    public $guide;

    public function mount($slug)
    {
        $this->slug = $slug;
        $this->guide = $this->getGuideData($slug);
        
        if (!$this->guide) {
            return redirect()->route('guide.index');
        }
    }

    private function getGuideData($slug)
    {
        $data = [
            'dashboard' => [
                'title' => 'Dashboard & Statistik',
                'image' => 'dashboard.png',
                'description' => 'Pusat kendali visual real-time untuk memantau performa apotek. Dashboard menyajikan data agregat transaksi dan pergerakan stok secara instan.',
                'golden_rules' => [
                    'Pantau widget **Stok Kritis** setiap pagi.',
                    'Gunakan **Filter Tanggal** untuk melihat tren berkala.',
                    'Periksa **Produk Terlaris** untuk strategi stok.'
                ],
                'sub_menus' => [
                    ['name' => 'Ringkasan Omset', 'func' => 'Perbandingan pendapatan hari ini vs kemarin.'],
                    ['name' => 'Grafik Penjualan', 'func' => 'Tren visual harian, mingguan, atau bulanan.'],
                    ['name' => 'Stok Kritis', 'func' => 'Daftar item yang butuh pemesanan ulang segera.'],
                    ['name' => 'Produk Terlaris', 'func' => 'Daftar obat dengan perputaran tercepat.'],
                    ['name' => 'Log Aktivitas', 'func' => 'Rekam jejak tindakan user di sistem.']
                ],
                'buttons' => [
                    ['label' => 'Filter Tanggal', 'func' => 'Ubah cakupan waktu data statistik.'],
                    ['label' => 'Refresh Data', 'func' => 'Update widget tanpa reload halaman.'],
                    ['label' => 'Export Widget', 'func' => 'Unduh data tertentu ke format Excel.'],
                    ['label' => 'View All (Stok)', 'func' => 'Lompat ke daftar inventori lengkap.']
                ],
                'procedures' => [
                    ['title' => 'Cek Performa Pagi', 'desc' => 'Buka Dashboard, lihat widget Omset. Jika ada penurunan drastis, periksa riwayat transaksi.'],
                    ['title' => 'Order Barang Urgent', 'desc' => 'Klik "View All" pada widget Stok Kritis. Segera hubungi supplier untuk barang bertanda merah.'],
                    ['title' => 'Audit User', 'desc' => 'Scroll ke bawah ke Log Aktivitas. Pastikan tidak ada transaksi mencurigakan di jam tutup toko.']
                ],
                'form_fields' => [
                    ['name' => 'Rentang Tanggal', 'description' => 'Filter data berdasarkan periode (Contoh: 01/12/2025 - 31/12/2025).', 'required' => false],
                    ['name' => 'Kategori Dashboard', 'description' => 'Spesialisasi data pada grup produk tertentu.', 'required' => false]
                ]
            ],
            'master' => [
                'title' => 'Manajemen Produk (Master)',
                'image' => 'product.png',
                'description' => 'Modul pusat pengelola katalog produk. Definisikan identitas obat, standar harga, dan skema konversi satuan di sini.',
                'golden_rules' => [
                    'Barcode harus **unik** untuk setiap produk.',
                    'Input **Satuan Terkecil** terlebih dahulu.',
                    'Pastikan **Lokasi Rak** diisi agar mudah dicari.'
                ],
                'sub_menus' => [
                    ['name' => 'Katalog Obat', 'func' => 'Edit Nama, Barcode, Kategori, dan Rak.'],
                    ['name' => 'Aturan Harga', 'func' => 'Atur Harga Jual, Margin, dan Status PPN.'],
                    ['name' => 'Satuan & Konversi', 'func' => 'Skema Box ke Strip atau Tablet.'],
                    ['name' => 'Kategori', 'func' => 'Grup hirarkis produk untuk laporan.']
                ],
                'buttons' => [
                    ['label' => '+ Tambah Produk', 'func' => 'Daftarkan item baru ke database.'],
                    ['label' => 'Sync Barcode', 'func' => 'Pasangkan barcode fisik ke sistem.'],
                    ['label' => 'Kelola Konversi', 'func' => 'Setup relasi antar satuan produk.'],
                    ['label' => 'Import Excel', 'func' => 'Upload massal data obat via template.'],
                    ['label' => 'Cetak Label', 'func' => 'Print barcode/harga untuk rak.']
                ],
                'procedures' => [
                    ['title' => 'Input Obat Baru', 'desc' => 'Klik "+ Tambah Produk". Isi Nama, Kategori, dan Barcode. Simpan untuk lanjut ke harga.'],
                    ['title' => 'Atur Konversi', 'desc' => 'Buka tab Satuan. Masukkan Satuan Besar (Box) dan tentukan Isinya (Misal: 100 Tablet).'],
                    ['title' => 'Update Harga Beli', 'desc' => 'Jika ada kenaikan, update di Master agar HPP dan margin tetap akurat di laporan.']
                ],
                'form_fields' => [
                    ['name' => 'Nama Produk', 'description' => 'Nama merk/generik (Contoh: Amoxicillin 500mg).', 'required' => true],
                    ['name' => 'Barcode / SKU', 'description' => 'Pindai barcode fisik menggunakan scanner.', 'required' => false],
                    ['name' => 'Kategori', 'description' => 'Grup obat (Bebas, Keras, Psikotropika).', 'required' => true],
                    ['name' => 'Satuan Terkecil', 'description' => 'Dasar stok (Tablet, Pcs, atau Botol).', 'required' => true],
                    ['name' => 'Harga Jual', 'description' => 'Nilai jual akhir per satuan terkecil.', 'required' => true],
                    ['name' => 'Stok Minimal', 'description' => 'Batas bawah sebelum muncul di Stok Kritis.', 'required' => true]
                ]
            ],
            'pos' => [
                'title' => 'Transaksi Kasir (POS)',
                'image' => 'pos.png',
                'description' => 'Antarmuka penjualan cepat. Mendukung Barcode Scanner, Layar Sentuh, dan multi-metode pembayaran.',
                'golden_rules' => [
                    'Selalu gunakan **Barcode Scanner** untuk kecepatan.',
                    'Cek **Struk Terakhir** jika printer macet.',
                    'Input **Nominal Bayar** dengan teliti.'
                ],
                'sub_menus' => [
                    ['name' => 'Order Grid', 'func' => 'Katalog produk layar sentuh.'],
                    ['name' => 'Cart Panel', 'func' => 'Daftar belanja dan total otomatis.'],
                    ['name' => 'Payment Modal', 'func' => 'Pilihan Tunai/Transfer/Debit.'],
                    ['name' => 'History', 'func' => 'Reprint struk transaksi terakhir.']
                ],
                'buttons' => [
                    ['label' => 'Cari (F1)', 'func' => 'Fokus langsung ke kolom pencarian.'],
                    ['label' => 'Diskon Global', 'func' => 'Potongan harga untuk total struk.'],
                    ['label' => 'Bayar (Space)', 'func' => 'Selesaikan belanja & buka layar bayar.'],
                    ['label' => 'Hold', 'func' => 'Simpan antrian belanja sementara.'],
                    ['label' => 'Print', 'func' => 'Cetak ulang struk transaksi.']
                ],
                'procedures' => [
                    ['title' => 'Penjualan Kilat', 'desc' => 'Scan Barcode produk. Barang otomatis masuk keranjang. Tekan Space untuk bayar.'],
                    ['title' => 'Beri Diskon', 'desc' => 'Klik nominal harga di baris item untuk diskon satuan, atau tombol bawah untuk diskon total.'],
                    ['title' => 'Bayar Tunai', 'desc' => 'Masukkan uang dari pelanggan. Tekan Enter. Struk akan keluar dan laci kasir terbuka.']
                ],
                'form_fields' => [
                    ['name' => 'Cari Produk', 'description' => 'Ketik Nama atau Scan Barcode di sini.', 'required' => false],
                    ['name' => 'Qty', 'description' => 'Jumlah barang. Tekan + atau - di keyboard.', 'required' => true],
                    ['name' => 'Nominal Bayar', 'description' => 'Uang tunai yang diterima (Misal: 100.000).', 'required' => true],
                    ['name' => 'Catatan', 'description' => 'Keterangan tambahan di bawah struk.', 'required' => false]
                ]
            ],
            'stock' => [
                'title' => 'Stok & Inventori',
                'image' => 'stock.png',
                'description' => 'Kelola fisik barang dengan akurat. Mendukung sistem FEFO untuk meminimalisir obat kadaluarsa.',
                'golden_rules' => [
                    'Update **Nomor Batch** setiap barang datang.',
                    'Prioritaskan stok dengan **Expired Terdekat**.',
                    'Lakukan **Stok Opname** minimal sebulan sekali.'
                ],
                'sub_menus' => [
                    ['name' => 'Batch & Exp', 'func' => 'Monitor tgl kadaluarsa per item.'],
                    ['name' => 'Stok Opname', 'func' => 'Audit fisik vs saldo sistem.'],
                    ['name' => 'Mutasi', 'func' => 'Pindahan barang antar rak/gudang.'],
                    ['name' => 'Kartu Stok', 'func' => 'Riwayat mutasi satu produk detail.']
                ],
                'buttons' => [
                    ['label' => 'Adjustment', 'func' => 'Koreksi stok hilang/rusak.'],
                    ['label' => 'Filter Exp', 'func' => 'Tampilkan obat kadaluarsa < 6 bln.'],
                    ['label' => 'Print Log', 'func' => 'Download riwayat pergerakan stok.'],
                    ['label' => 'Fix Stock', 'func' => 'Sinkronisasi ulang saldo database.']
                ],
                'procedures' => [
                    ['title' => 'Audit Stok Opname', 'desc' => 'Pilih Rak. Hitung fisik barang. Masukkan angka ke kolom Real. Sistem setuju selisihnya.'],
                    ['title' => 'Cek Obat Expired', 'desc' => 'Gunakan Filter Soon Expired. Pindahkan barang tersebut ke etalase promo depan.'],
                    ['title' => 'Koreksi Stok Rusak', 'desc' => 'Klik Adjustment. Pilih "Rusak", kurangi jumlah stok, beri alasan di catatan.']
                ],
                'form_fields' => [
                    ['name' => 'Nomor Batch', 'description' => 'Kode unik produksi dari manufaktur.', 'required' => true],
                    ['name' => 'Tgl Expired', 'description' => 'Batas aman penggunaan obat.', 'required' => true],
                    ['name' => 'Alasan Koreksi', 'description' => 'Kenapa stok berubah (Hilang, Rusak, dll).', 'required' => true],
                    ['name' => 'Qty Real', 'description' => 'Jumlah fisik yang ditemukan saat audit.', 'required' => true]
                ]
            ],
            'procurement' => [
                'title' => 'Pengadaan (Procurement)',
                'image' => 'procurement.png',
                'description' => 'Siklus pembelian ke PBF. Pastikan modal (HPP) tercatat benar untuk laporan laba rugi.',
                'golden_rules' => [
                    'Sesuaikan **Nomor Faktur** dengan fisik kertas.',
                    'Pastikan **Diskon Supplier** terinput.',
                    'Cek **Ketelitian Batch** saat penerimaan.'
                ],
                'sub_menus' => [
                    ['name' => 'Purchase Order', 'func' => 'Pesan barang ke supplier.'],
                    ['name' => 'Penerimaan (GR)', 'func' => 'Terima barang & tambah stok.'],
                    ['name' => 'Hutang (AP)', 'func' => 'Pantau tagihan jatuh tempo.'],
                    ['name' => 'Supplier', 'func' => 'Daftar vendor dan kontak sales.']
                ],
                'buttons' => [
                    ['label' => 'Buat PO', 'func' => 'Input daftar belanja apotek.'],
                    ['label' => 'Terima GR', 'func' => 'Proses datangnya barang PO.'],
                    ['label' => 'Bayar Hutang', 'func' => 'Catat pelunasan faktur vendor.'],
                    ['label' => 'Cek Riwayat', 'func' => 'Lihat faktur beli terdahulu.']
                ],
                'procedures' => [
                    ['title' => 'Pesan Barang (PO)', 'desc' => 'Pilih Supplier. Klik barang yang stoknya kritis. Masukkan jumlah yang mau dibeli. Simpan.'],
                    ['title' => 'Terima Barang (GR)', 'desc' => 'Buka GR. Cari No PO tadi. Cocokkan jumlah fisik yang datang. Isi Batch & Expired sesuai fisik box.'],
                    ['title' => 'Bayar Tagihan', 'desc' => 'Buka menu Hutang. Pilih faktur yang mau dicicil/lunas. Pilih metode bayar. Selesai.']
                ],
                'form_fields' => [
                    ['name' => 'No Faktur', 'description' => 'Nomor dari kertas surat jalan supplier.', 'required' => true],
                    ['name' => 'Harga Beli', 'description' => 'Harga per satuan dari supplier (Neto).', 'required' => true],
                    ['name' => 'Termin', 'description' => 'Jatuh tempo (Cash, 30 Hari, dll).', 'required' => true]
                ]
            ],
            'reports' => [
                'title' => 'Laporan & Keuangan',
                'image' => 'finance.png',
                'description' => 'Dashboard audit finansial. Lihat Laba Rugi, Rekap Pajak, dan Biaya Operasional secara akurat.',
                'golden_rules' => [
                    'Lakukan **Tutup Buku** setiap shift berakhir.',
                    'Input semua **Pengeluaran** (Gaji, Listrik).',
                    'Andalkan **Laporan PPN** untuk setoran pajak.'
                ],
                'sub_menus' => [
                    ['name' => 'Penjualan', 'func' => 'Omset per-produk, kasir, atau periode.'],
                    ['name' => 'Laba Rugi', 'func' => 'Hasil bersih setelah modal & biaya.'],
                    ['name' => 'Pengeluaran', 'func' => 'Catatan biaya non-produk (OPEX).'],
                    ['name' => 'Pajak PPN', 'func' => 'Rekap PPN Masukan & Keluaran.']
                ],
                'buttons' => [
                    ['label' => 'Download PDF', 'func' => 'Cetak laporan formal bertanda tangan.'],
                    ['label' => 'Export Excel', 'func' => 'Olah data mentah secara eksternal.'],
                    ['label' => 'Print Thermal', 'func' => 'Ringkasan laporan di printer struk.'],
                    ['label' => 'Email Report', 'func' => 'Kirim laporan otomatis ke Owner.']
                ],
                'procedures' => [
                    ['title' => 'Analisa Laba Rugi', 'desc' => 'Pilih Periode Bulan. Klik Analisa. Pastikan HPP sudah terisi semua agar profit akurat.'],
                    ['title' => 'Rekonsiliasi Kasir', 'desc' => 'Buka Laporan Penjualan Shift. Hitung uang fisik di laci. Pastikan sama dengan "Total Cash" di sistem.'],
                    ['title' => 'Input Biaya Toko', 'desc' => 'Buka Buku Pengeluaran. Klik "+ Pengeluaran". Masukkan biaya Listrik/Sewa. Simpan.']
                ],
                'form_fields' => [
                    ['name' => 'Periode', 'description' => 'Tentukan Awal dan Akhir laporan.', 'required' => true],
                    ['name' => 'Kat. Biaya', 'description' => 'Jenis pengeluaran (Operasional, Marketing).', 'required' => true],
                    ['name' => 'User ID', 'description' => 'Melihat laporan hasil kerja staff tertentu.', 'required' => false]
                ]
            ],
            'profile' => [
                'title' => 'Pengaturan Profil',
                'image' => 'profile.png',
                'description' => 'Keamanan dan identitas user. Kelola password dan preferensi tampilan aplikasi.',
                'golden_rules' => [
                    'Ganti **Password** tiap 3 bulan sekali.',
                    'Pastikan **Email** aktif untuk recovery.',
                    'Gunakan **Foto Profil** asli untuk audit.'
                ],
                'sub_menus' => [
                    ['name' => 'Identitas', 'func' => 'Nama, Email, dan Foto Profil.'],
                    ['name' => 'Keamanan', 'func' => 'Ganti Password & Sesi Login.'],
                    ['name' => 'Preferences', 'func' => 'Tema Dark/Light Mode.']
                ],
                'buttons' => [
                    ['label' => 'Simpan', 'func' => 'Update data identitas ke server.'],
                    ['label' => 'Logout Sesi', 'func' => 'Keluar dari perangkat lain aktif.'],
                    ['label' => 'Ganti Pass', 'func' => 'Perbarui kunci akses akun.'],
                    ['label' => 'Upload', 'func' => 'Ganti gambar kartu identitas.']
                ],
                'procedures' => [
                    ['title' => 'Amankan Akun', 'desc' => 'Buka Keamanan. Masukkan Password Lama, lalu buat Password Baru yang rumit. Simpan.'],
                    ['title' => 'Atur Mode Gelap', 'desc' => 'Buka Preferences. Klik Switch Dark Mode. Sistem akan berubah warna lebih nyaman dimata.']
                ],
                'form_fields' => [
                    ['name' => 'Password Baru', 'description' => 'Minimal 8 karakter kombinasi.', 'required' => true],
                    ['name' => 'Email Akun', 'description' => 'Username anda masuk ke aplikasi.', 'required' => true]
                ]
            ],
            'settings' => [
                'title' => 'Manajemen Sistem',
                'image' => 'settings.png',
                'description' => 'Pengaturan Global. Kelola data outlet, hak akses karyawan, dan backup sistem.',
                'golden_rules' => [
                    'Download **Backup Data** setiap minggu.',
                    'Batasi **Hak Akses** sesuai tugas staff.',
                    'Lengkapi **Info Toko** untuk header struk.'
                ],
                'sub_menus' => [
                    ['name' => 'Info Toko', 'func' => 'Nama, Alamat, dan Logo Apotek.'],
                    ['name' => 'User & Role', 'func' => 'Manajemen staff dan login.'],
                    ['name' => 'Environment', 'func' => 'Pajak default & format invoice.'],
                    ['name' => 'Database', 'func' => 'Maintenance & Backup data.']
                ],
                'buttons' => [
                    ['label' => 'Registrasi', 'func' => 'Tambah akun karyawan baru.'],
                    ['label' => 'Atur Izin', 'func' => 'Ubah menu yang boleh diakses staff.'],
                    ['label' => 'Backup', 'func' => 'Unduh cadangan data database.'],
                    ['label' => 'Update Logo', 'func' => 'Ganti gambar header di struk.']
                ],
                'procedures' => [
                    ['title' => 'Tambah Staff Baru', 'desc' => 'Buka User & Role. Klik Tambah. Isi Nama & Password. Pilih Role (Kasir/Admin). Kirim login ke staff.'],
                    ['title' => 'Lengkapi Alamat Toko', 'desc' => 'Buka Info Toko. Masukkan Alamat Lengkap & No WhatsApp. Info ini akan muncul di Struk Penjualan.'],
                    ['title' => 'Amankan Data', 'desc' => 'Masuk menu Database. Klik "Generate Backup". Simpan file di Google Drive atau Flashdisk eksternal.']
                ],
                'form_fields' => [
                    ['name' => 'Nama Toko', 'description' => 'Muncul sebagai judul utama di struk.', 'required' => true],
                    ['name' => 'Role', 'description' => 'Level akses (Super Admin, Admin, Kasir, Gudang).', 'required' => true],
                    ['name' => 'Default PPN', 'description' => 'Angka pajak standar (Contoh: 11).', 'required' => true]
                ]
            ],
        ];

        return $data[$slug] ?? null;
    }

    public function render()
    {
        return view('livewire.settings.guide-detail');
    }
}
