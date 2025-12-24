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
                'description' => 'Pusat kendali visual untuk memantau performa apotek secara real-time. Dashboard menyajikan data yang diagregasi dari seluruh transaksi penjualan dan pergerakan stok untuk memberikan gambaran cepat mengenai kesehatan bisnis Anda.',
                'sub_menus' => [
                    ['name' => 'Ringkasan Omset', 'func' => 'Menampilkan jumlah pendapatan kotor hari ini dibandingkan hari kemarin.'],
                    ['name' => 'Grafik Penjualan', 'func' => 'Visualisasi tren penjualan harian dalam rentang waktu yang dipilih (7 hari, 30 hari, atau 1 tahun).'],
                    ['name' => 'Stok Kritis', 'func' => 'Daftar produk yang telah mencapai atau di bawah "Stok Minimal" yang telah diatur.'],
                    ['name' => 'Produk Terlaris', 'func' => 'Peringkat produk berdasarkan kuantitas yang terjual untuk membantu strategi pengadaan.'],
                    ['name' => 'Log Aktivitas', 'func' => 'Catatan singkat aktivitas terbaru yang dilakukan oleh user sistem.']
                ],
                'buttons' => [
                    ['label' => 'Filter Tanggal', 'func' => 'Mengubah cakupan data statistik yang ditampilkan di seluruh widget dashboard.'],
                    ['label' => 'Refresh Data', 'func' => 'Memperbarui data dashboard secara manual tanpa memuat ulang halaman.'],
                    ['label' => 'Export Widget', 'func' => 'Mengunduh data dari widget tertentu (seperti stok kritis) ke format Excel.'],
                    ['label' => 'View All (Stok)', 'func' => 'Mengarahkan langsung ke halaman inventori dengan filter stok menipis aktif.']
                ],
                'procedures' => [
                    ['title' => 'Menganalisis Kinerja Harian', 'desc' => 'Periksa widget "Ringkasan" setiap pagi untuk melihat pencapaian target harian dan bandingkan dengan rata-rata penjualan.'],
                    ['title' => 'Penanganan Stok Kritis', 'desc' => 'Cek daftar stok kritis secara berkala. Segera buat Purchase Order (PO) untuk barang-barang di daftar ini guna menghindari kehilangan potensi penjualan.'],
                    ['title' => 'Evaluasi Produk Populer', 'desc' => 'Gunakan daftar produk terlaris untuk menentukan penempatan barang (merchandising) atau untuk memberikan promo pada barang yang kurang laku.'],
                    ['title' => 'Monitoring Aktivitas', 'desc' => 'Gunakan log aktivitas di bagian bawah untuk memastikan tidak ada inputan data yang mencurigakan di luar jam operasional.']
                ],
                'form_fields' => [
                    ['name' => 'Rentang Tanggal', 'description' => 'Pilih Tanggal Mulai dan Tanggal Selesai untuk memfilter ringkasan statistik.', 'required' => false],
                    ['name' => 'Kategori Dashboard', 'description' => 'Filter keseluruhan dashboard berdasarkan kategori produk tertentu (jika diaktifkan).', 'required' => false]
                ]
            ],
            'master' => [
                'title' => 'Manajemen Produk (Master)',
                'image' => 'product.png',
                'description' => 'Modul pusat untuk mengelola katalog produk. Di sini Anda mendefinisikan identitas obat, spesifikasi teknis, aturan harga, serta skema satuan yang digunakan dalam apotek.',
                'sub_menus' => [
                    ['name' => 'Katalog Obat', 'func' => 'Manajemen data utama (Nama, Barcode, Kategori, Rak).'],
                    ['name' => 'Aturan Harga', 'func' => 'Mengatur harga jual, margin keuntungan, dan status PPN.'],
                    ['name' => 'Satuan & Konversi', 'func' => 'Definisi satuan (Box, Strip, Tablet) dan relasi antar satuan tersebut.'],
                    ['name' => 'Kategori Sistem', 'func' => 'Pengelompokan hirarkis produk untuk mempermudah pencarian dan pelaporan.']
                ],
                'buttons' => [
                    ['label' => '+ Tambah Produk', 'func' => 'Membuka formulir pendaftaran produk baru.'],
                    ['label' => 'Sync Barcode', 'func' => 'Menghubungkan kode barcode fisik dengan data digital di sistem.'],
                    ['label' => 'Kelola Konversi', 'func' => 'Mengatur bagaimana 1 Box diterjemahkan menjadi 10 Strip atau 100 Tablet.'],
                    ['label' => 'Import Excel', 'func' => 'Menambah data produk dalam jumlah banyak sekaligus menggunakan template file.'],
                    ['label' => 'Cetak Label', 'func' => 'Mencetak kode produk atau harga untuk ditempel di rak pajangan.']
                ],
                'procedures' => [
                    ['title' => 'Mendaftarkan Produk Baru', 'desc' => 'Klik "+ Tambah Produk", isi identitas dasar, pilih kategori, dan tentukan Rak tempat penyimpanan agar mudah dicari.'],
                    ['title' => 'Setup Multi-Satuan', 'desc' => 'Buka tab Konversi, tambahkan satuan dari yang terbesar ke terkecil. Contoh: Isi 1 Box = 10 Strip, lalu 1 Strip = 10 Tablet. Sistem akan otomatis menghitung stok eceran.'],
                    ['title' => 'Mengaktifkan PPN', 'desc' => 'Pada form harga, centang opsi "Termasuk PPN" jika harga jual sudah mengandung pajak, atau sebaliknya agar sistem menghitung pajak secara otomatis di POS.'],
                    ['title' => 'Setting Peringatan Stok', 'desc' => 'Isi field "Stok Minimal" dengan angka aman. Pastikan angka ini cukup untuk menutupi waktu tunggu (lead time) pengiriman dari supplier.']
                ],
                'form_fields' => [
                    ['name' => 'Nama Produk', 'description' => 'Nama merk atau nama generik obat (Contoh: Paracetamol 500mg).', 'required' => true],
                    ['name' => 'Barcode / SKU', 'description' => 'Kode unik produk. Gunakan scanner untuk mengisi field ini secara otomatis.', 'required' => false],
                    ['name' => 'Kategori', 'description' => 'Jenis sediaan atau klasifikasi obat (Obat Bebas, Keras, Psikotropika).', 'required' => true],
                    ['name' => 'Satuan Terkecil', 'description' => 'Satuan dasar penyimpanan (biasanya Pcs, Tablet, atau Botol).', 'required' => true],
                    ['name' => 'Harga Jual', 'description' => 'Harga satuan yang akan muncul saat transaksi di kasir.', 'required' => true],
                    ['name' => 'Stok Minimal', 'description' => 'Ambang batas bawah jumlah stok sebelum sistem memberikan peringatan "Kritis".', 'required' => true],
                    ['name' => 'Lokasi Rak', 'description' => 'Informasi posisi fisik barang di dalam apotek (Contoh: A-01-02).', 'required' => false]
                ]
            ],
            'pos' => [
                'title' => 'Transaksi Kasir (POS)',
                'image' => 'pos.png',
                'description' => 'Antarmuka penjualan ritel yang dioptimalkan untuk kecepatan dan kemudahan penggunaan. Mendukung penggunaan barcode scanner dan layar sentuh.',
                'sub_menus' => [
                    ['name' => 'Order Grid', 'func' => 'Daftar produk dengan foto atau tombol cepat (Speed dial).'],
                    ['name' => 'Cart Panel', 'func' => 'Daftar belanja aktif beserta ringkasan kalkulasi otomatis.'],
                    ['name' => 'Payment Modal', 'func' => 'Pemilihan metode pembayaran dan input nominal uang tunai.'],
                    ['name' => 'Transaction History', 'func' => 'Melihat atau reprint struk dari transaksi yang baru saja diselesaikan.']
                ],
                'buttons' => [
                    ['label' => 'Cari Produk (F1)', 'func' => 'Fokus ke kolom pencarian produk secara instan.'],
                    ['label' => 'Diskon Global', 'func' => 'Memberikan potongan harga untuk total seluruh transaksi.'],
                    ['label' => 'Bayar (Space)', 'func' => 'Menyelesaikan belanjaan dan membuka layar pembayaran.'],
                    ['label' => 'Hold Transaction', 'func' => 'Menyimpan antrian belanjaan sementara jika pelanggan ingin menambah barang lain.'],
                    ['label' => 'Print Terakhir', 'func' => 'Mencetak ulang struk transaksi sebelumnya jika terjadi kertas macet.']
                ],
                'procedures' => [
                    ['title' => 'Proses Penjualan Standar', 'desc' => 'Scan barcode produk atau ketik nama di kolom pencarian. Tekan Enter atau klik item untuk masuk ke keranjang. Sesuaikan jumlah jika perlu.'],
                    ['title' => 'Menerapkan Diskon', 'desc' => 'Klik ikon % di samping nama item untuk diskon per-barang, atau klik "Diskon Total" di bagian bawah untuk potongan seluruh belanjaan.'],
                    ['title' => 'Penyelesaian Pembayaran', 'desc' => 'Tekan tombol "BAYAR", masukkan nominal uang yang diberikan pelanggan. Sistem akan menampilkan jumlah kembalian secara otomatis.'],
                    ['title' => 'Penanganan Retur (Opsional)', 'desc' => 'Buka riwayat transaksi, pilih nomor struk, klik "Retur" untuk mengembalikan stok dan mengeluarkan uang kembali (Sesuai hak akses).']
                ],
                'form_fields' => [
                    ['name' => 'Input Pencarian', 'description' => 'Tempat mengetik nama obat, kode, atau scan barcode.', 'required' => false],
                    ['name' => 'Jumlah (Qty)', 'description' => 'Banyaknya barang yang dibeli. Tekan tombol + atau - untuk perubahan cepat.', 'required' => true],
                    ['name' => 'Pajak (PPN)', 'description' => 'Opsi untuk mengaktifkan/menonaktifkan pajak pada transaksi ini (jika diizinkan).', 'required' => false],
                    ['name' => 'Nominal Bayar', 'description' => 'Jumlah uang fisik yang diterima dari pelanggan (Cash).', 'required' => true],
                    ['name' => 'Catatan Transaksi', 'description' => 'Keterangan tambahan yang ingin dicetak di struk (Misal: Nama Pasien).', 'required' => false]
                ]
            ],
            'stock' => [
                'title' => 'Stok & Inventori',
                'image' => 'stock.png',
                'description' => 'Modul manajemen gudang untuk memastikan ketersediaan fisik barang sesuai dengan catatan sistem. Mengelola sistem FEFO (First Expired First Out).',
                'sub_menus' => [
                    ['name' => 'Data Batch & Exp', 'func' => 'Informasi stok berdasarkan tanggal kedaluwarsa dan nomor produksi.'],
                    ['name' => 'Stok Opname', 'func' => 'Proses sinkronisasi jumlah fisik barang dengan saldo di sistem.'],
                    ['name' => 'Mutasi Stok', 'func' => 'Catatan perpindahan barang antar gudang atau antar rak.'],
                    ['name' => 'Kartu Stok', 'func' => 'Laporan kronologis keluar masuknya satu produk tertentu secara detail.']
                ],
                'buttons' => [
                    ['label' => 'Adjustment', 'func' => 'Melakukan koreksi stok jika ditemukan barang rusak, hilang, atau kelebihan.'],
                    ['label' => 'Filter Expired', 'func' => 'Menampilkan hanya barang yang akan kadaluarsa dalam 3-6 bulan ke depan.'],
                    ['label' => 'Print Barcode Batch', 'func' => 'Mencetak label spesifik untuk batch tertentu guna mempermudah tracking.'],
                    ['label' => 'Download Log', 'func' => 'Mengambil riwayat pergerakan stok ke file CSV/Excel.']
                ],
                'procedures' => [
                    ['title' => 'Melakukan Stok Opname Rutin', 'desc' => 'Pilih menu Stok Opname, cetak daftar periksa, isi jumlah fisik di lapangan, lalu input ke sistem untuk penyesuaian otomatis.'],
                    ['title' => 'Manajemen Barang Hampir Kadaluarsa', 'desc' => 'Gunakan filter "Soon Expired", pindahkan barang tersebut ke rak depan atau berikan promo khusus agar habis lebih cepat (FEFO).'],
                    ['title' => 'Trisula Audit (Kartu Stok)', 'desc' => 'Jika ada selisih, buka "Kartu Stok", bandingkan data penjualan vs data penerimaan barang untuk menemukan titik kesalahan input.'],
                    ['title' => 'Pemisahan Stok Rusak', 'desc' => 'Gunakan fitur Adjustment dengan alasan "Rusak/Expired" untuk mengeluarkan fisik barang dari stok aktif agar tidak terjual di POS.']
                ],
                'form_fields' => [
                    ['name' => 'Nomor Batch', 'description' => 'Kode unik produksi dari pabrik/supplier.', 'required' => true],
                    ['name' => 'Tanggal Expired', 'description' => 'Batas akhir penggunaan produk sesuai kemasan.', 'required' => true],
                    ['name' => 'Alasan Penyesuaian', 'description' => 'Kategori koreksi (Rusak, Hilang, Koreksi Input, Stok Opname).', 'required' => true],
                    ['name' => 'Jumlah Perubahan', 'description' => 'Angka selisih yang ingin ditambahkan (positif) atau dikurangi (negatif).', 'required' => true]
                ]
            ],
            'procurement' => [
                'title' => 'Pengadaan (Procurement)',
                'image' => 'procurement.png',
                'description' => 'Mengelola siklus pembelian barang ke PBF (Pedagang Besar Farmasi) mulai dari perencanaan pesanan hingga barang diterima di gudang.',
                'sub_menus' => [
                    ['name' => 'Purchase Order (PO)', 'func' => 'Dokumen pemesanan resmi kepada supplier.'],
                    ['name' => 'Goods Receipt (GR)', 'func' => 'Formulir penerimaan barang fisik dan update stok otomatis.'],
                    ['name' => 'Daftar Supplier', 'func' => 'Manajemen database PBF beserta kontak dan termin pembayaran.'],
                    ['name' => 'Debt Monitoring', 'func' => 'Memantau tagihan (Hutang) yang harus dibayar kepada supplier.']
                ],
                'buttons' => [
                    ['label' => 'Buat Draft PO', 'func' => 'Menyimpan rencana pesanan sebelum dikirim ke supplier.'],
                    ['label' => 'Finalize PO', 'func' => 'Mengunci PO dan menerbitkan dokumen untuk dikirim ke sales PBF.'],
                    ['label' => 'Input Penerimaan', 'func' => 'Mencatat datangnya barang berdasarkan referensi nomor PO.'],
                    ['label' => 'Verify Batch', 'func' => 'Memastikan data batch di surat jalan sesuai dengan yang diinput ke sistem.']
                ],
                'procedures' => [
                    ['title' => 'Alur Pemesanan (PO)', 'desc' => 'Pilih Supplier, tambahkan item yang dibutuhkan (Saran: lihat widget Stok Kritis), tentukan estimasi harga beli, simpan dan cetak.'],
                    ['title' => 'Proses Penerimaan (GR)', 'desc' => 'Saat barang datang, buka menu GR, cari nomor PO terkait. Masukkan jumlah barang yang benar-benar datang, nomor BATCH, dan TANGGAL EXPIRED dari fisik box.'],
                    ['title' => 'Update Harga Beli', 'desc' => 'Jika ada kenaikan harga dari supplier, update field "Harga Beli" saat GR agar sistem dapat menghitung HPP (COGS) terbaru secara akurat.'],
                    ['title' => 'Penerimaan Parsial', 'desc' => 'Jika supplier hanya mengirim sebagian barang, input jumlah yang diterima saja. Sisa PO akan tetap menggantung (Backorder) hingga pengiriman berikutnya.']
                ],
                'form_fields' => [
                    ['name' => 'Nama Supplier', 'description' => 'Pilih dari daftar PBF yang sudah terdaftar.', 'required' => true],
                    ['name' => 'Nomor Faktur / Surat Jalan', 'description' => 'Nomor unik dari dokumen fisik supplier untuk verifikasi audit.', 'required' => true],
                    ['name' => 'Harga Beli Satuan', 'description' => 'Harga neto setelah diskon dari supplier (untuk perhitungan modal).', 'required' => true],
                    ['name' => 'Termin Pembayaran', 'description' => 'Jatuh tempo pembayaran (Tunai, 7 Hari, 30 Hari, dll).', 'required' => true]
                ]
            ],
            'reports' => [
                'title' => 'Laporan & Keuangan',
                'image' => 'finance.png',
                'description' => 'Menampilkan data olahan dari aktivitas operasional menjadi informasi finansial yang siap digunakan untuk pelaporan pajak maupun evaluasi bisnis.',
                'sub_menus' => [
                    ['name' => 'Laporan Penjualan', 'func' => 'Detail transaksi per-kasir, per-shift, atau per-produk.'],
                    ['name' => 'Analisa Laba Rugi', 'func' => 'Perhitungan Gross Profit dan Net Profit dalam suatu periode.'],
                    ['name' => 'Buku Pengeluaran', 'func' => 'Pencatatan biaya operasional (Gaji, Listrik, Sewa, dll).'],
                    ['name' => 'Rekap Pajak (PPN)', 'func' => 'Laporan masukan dan keluaran PPN untuk kebutuhan administrasi pajak.']
                ],
                'buttons' => [
                    ['label' => 'Download PDF', 'func' => 'Menghasilkan dokumen laporan siap cetak dengan header apotek.'],
                    ['label' => 'Export Excel', 'func' => 'Mengambil data mentah laporan untuk diolah kembali secara mandiri.'],
                    ['label' => 'Print Mini Report', 'func' => 'Mencetak ringkasan laporan langsung ke printer thermal (seperti struk).'],
                    ['label' => 'Send to Email', 'func' => 'Mengirim salinan laporan otomatis ke alamat email pemilik.']
                ],
                'procedures' => [
                    ['title' => 'Tutup Buku Harian', 'desc' => 'Gunakan "Laporan Penjualan Harian" untuk melakukan rekonsiliasi uang di laci kasir dengan angka di sistem setiap ganti shift.'],
                    ['title' => 'Melihat Keuntungan Bersih', 'desc' => 'Buka laporan Laba Rugi, pastikan seluruh "Biaya Pengeluaran" sudah diinput agar angka Net Profit yang muncul adalah akurat.'],
                    ['title' => 'Audit Margin Produk', 'desc' => 'Periksa apakah ada produk dengan margin terlalu tipis melalui laporan "Sales by Product" untuk menyesuaikan harga jual di masa depan.'],
                    ['title' => 'Persiapan Pajak', 'desc' => 'Gunakan filter bulanan pada Laporan PPN untuk melihat total pajak yang harus disetorkan ke kas negara.']
                ],
                'form_fields' => [
                    ['name' => 'Periode Laporan', 'description' => 'Rentang waktu data yang ingin ditarik (Harian, Bulanan, Tahunan).', 'required' => true],
                    ['name' => 'Filter User/Kasir', 'description' => 'Melihat kinerja spesifik karyawan tertentu.', 'required' => false],
                    ['name' => 'Kategori Biaya', 'description' => 'Klasifikasi pengeluaran (Operasional, SDM, Marketing, dll).', 'required' => true],
                    ['name' => 'Metode Pembayaran', 'description' => 'Memisahkan laporan berdasarkan Cash, Transfer, atau Debit Card.', 'required' => false]
                ]
            ],
            'profile' => [
                'title' => 'Pengaturan Profil',
                'image' => 'profile.png',
                'description' => 'Halaman personalisasi user untuk mengelola identitas digital dan keamanan akses ke dalam sistem apotek.',
                'sub_menus' => [
                    ['name' => 'Identitas Diri', 'func' => 'Pengaturan Nama, Email, dan Foto Profil yang muncul di sistem.'],
                    ['name' => 'Keamanan Akun', 'func' => 'Fitur penggantian password dan manajemen sesi login.'],
                    ['name' => 'Preferences', 'func' => 'Pengaturan bahasa atau tema tampilan (Dark/Light Mode).']
                ],
                'buttons' => [
                    ['label' => 'Update Profil', 'func' => 'Menyimpan perubahan informasi identitas ke server.'],
                    ['label' => 'Ganti Password', 'func' => 'Memperbarui kata sandi untuk mencegah akses tidak sah.'],
                    ['label' => 'Logout Semua Sesi', 'func' => 'Keluar dari seluruh perangkat yang sedang login menggunakan akun ini.'],
                    ['label' => 'Upload Foto', 'func' => 'Memilih file gambar untuk dijadikan foto profil.']
                ],
                'procedures' => [
                    ['title' => 'Mengamankan Akun', 'desc' => 'Ganti password Anda secara berkala (misal 3 bulan sekali). Gunakan kombinasi huruf besar, kecil, angka, dan simbol.'],
                    ['title' => 'Melengkapi Identitas', 'desc' => 'Pastikan Email yang terdaftar adalah aktif untuk memudahkan proses pemulihan akun jika lupa password.'],
                    ['title' => 'Pengaturan Tampilan', 'desc' => 'Pilih mode tampilan yang paling nyaman untuk mata Anda saat bekerja dalam durasi lama (Gunakan Dark Mode untuk shift malam).']
                ],
                'form_fields' => [
                    ['name' => 'Email', 'description' => 'Alamat korespondensi resmi dan juga digunakan sebagai Username login.', 'required' => true],
                    ['name' => 'Password Lama', 'description' => 'Masukkan password saat ini sebagai verifikasi sebelum mengubah ke yang baru.', 'required' => true],
                    ['name' => 'Password Baru', 'description' => 'Kata sandi minimal 8 karakter dengan tingkat kerumitan tinggi.', 'required' => true],
                    ['name' => 'Konfirmasi Password', 'description' => 'Ulangi input password baru untuk memastikan tidak ada salah ketik.', 'required' => true]
                ]
            ],
            'settings' => [
                'title' => 'Manajemen Sistem',
                'image' => 'settings.png',
                'description' => 'Modul administratif tingkat tinggi untuk mengatur parameter global aplikasi dan manajemen personil organisasi.',
                'sub_menus' => [
                    ['name' => 'Identitas Toko', 'func' => 'Pengaturan Nama Apotek, NPWP, Alamat, dan Logo untuk dokumen resmi.'],
                    ['name' => 'User & Role', 'func' => 'Pendaftaran karyawan dan pengaturan hak akses (Permission) per-jabatan.'],
                    ['name' => 'Default Sistem', 'func' => 'Pengaturan default pajak, mata uang, dan format nomor invoice.'],
                    ['name' => 'Backup Data', 'func' => 'Melakukan pencadangan database manual untuk keamanan data jangka panjang.']
                ],
                'buttons' => [
                    ['label' => 'Simpan Konfigurasi', 'func' => 'Menerapkan pengaturan baru ke seluruh sistem secara global.'],
                    ['label' => 'Tambah User Baru', 'func' => 'Mendaftarkan akun karyawan baru ke dalam sistem.'],
                    ['label' => 'Edit Hak Akses', 'func' => 'Menyesuaikan menu apa saja yang boleh dibuka oleh Kasir, Gudang, atau Admin.'],
                    ['label' => 'Generate Backup', 'func' => 'Memulai proses pengunduhan file cadangan database.']
                ],
                'procedures' => [
                    ['title' => 'Setup Informasi Struk', 'desc' => 'Isi data di menu "Identitas Toko" dengan lengkap. Informasi ini akan muncul di bagian Header setiap struk belanja yang dicetak untuk pelanggan.'],
                    ['title' => 'Manajemen Hak Akses Karyawan', 'desc' => 'Hindari memberikan akses "Super Admin" kepada semua orang. Berikan akses minimal yang dibutuhkan untuk masing-masing pekerjaan (Principle of Least Privilege).'],
                    ['title' => 'Prosedur Backup Mandiri', 'desc' => 'Lakukan backup data minimal seminggu sekali dan simpan filenya di luar komputer kasir (Misal: Google Drive atau Flashdisk).'],
                    ['title' => 'Konfigurasi PPN Default', 'desc' => 'Atur persentase pajak yang berlaku di wilayah Anda pada menu "Default Sistem" agar kalkulasi di modul procurement dan POS selalu tepat.']
                ],
                'form_fields' => [
                    ['name' => 'Nama Apotek', 'description' => 'Nama resmi yang akan muncul di semua dokumen output.', 'required' => true],
                    ['name' => 'Role (Jabatan)', 'description' => 'Pilihan level akses (Super Admin, Admin, Kasir, Gudang).', 'required' => true],
                    ['name' => 'Persentase Pajak', 'description' => 'Angka default PPN (Contoh: 11) yang digunakan dalam perhitungan otomatis.', 'required' => true],
                    ['name' => 'Nomor WhatsApp', 'description' => 'Nomor yang muncul di struk untuk layanan pengaduan pelanggan.', 'required' => false]
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
