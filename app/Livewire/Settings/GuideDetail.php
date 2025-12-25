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
                'screenshots' => [
                    ['src' => 'guide_dashboard_ui_1766530015722.png', 'caption' => 'Tampilan Utama Dashboard dengan Grafik Penjualan'],
                    ['src' => 'report.png', 'caption' => 'Widget Laporan Ringkas Harian']
                ],
                'golden_rules' => [
                    'Pantau widget **Stok Kritis** setiap pagi.',
                    'Gunakan **Filter Tanggal** untuk melihat tren berkala.',
                    'Periksa **Produk Terlaris** untuk strategi stok.'
                ],
                'sub_menus' => [
                    ['name' => 'Ringkasan Omset', 'func' => 'Menampilkan total pendapatan kotor hari ini dibandingkan dengan kemarin (persentase kenaikan/penurunan).'],
                    ['name' => 'Grafik Penjualan', 'func' => 'Visualisasi tren penjualan dalam format grafik garis untuk melihat pola jam/hari sibuk.'],
                    ['name' => 'Stok Kritis', 'func' => 'Tabel peringatan dini untuk barang yang jumlahnya di bawah batas minimum.'],
                    ['name' => 'Produk Terlaris', 'func' => 'Daftar top 5 produk yang paling banyak terjual dalam periode terpilih.'],
                    ['name' => 'Log Aktivitas', 'func' => 'Rekam jejak audit trail (siapa melakukan apa) untuk keamanan sistem.']
                ],
                'buttons' => [
                    ['label' => 'Filter Tanggal', 'func' => 'Tombol di pojok kanan atas untuk mengubah rentang waktu data dashboard (Hari Ini, 7 Hari, 30 Hari, atau Custom).'],
                    ['label' => 'Refresh Data', 'func' => 'Memuat ulang angka-angka di widget tanpa perlu refresh satu halaman penuh.'],
                    ['label' => 'Export Widget', 'func' => 'Mengunduh data tabel spesifik (misal: Stok Kritis) ke dalam format Excel/PDF.'],
                    ['label' => 'View All (Stok)', 'func' => 'Shortcut cepat untuk melompat ke halaman Inventori lengkap dengan filter stok menipis otomatis aktif.']
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
                'screenshots' => [
                    ['src' => 'guide_product_ui_1766530091924.png', 'caption' => 'Form Input Produk Baru'],
                    ['src' => 'stock.png', 'caption' => 'Ilustrasi Manajemen Stok Produk']
                ],
                'golden_rules' => [
                    'Barcode harus **unik** untuk setiap produk.',
                    'Input **Satuan Terkecil** terlebih dahulu.',
                    'Pastikan **Lokasi Rak** diisi agar mudah dicari.'
                ],
                'sub_menus' => [
                    ['name' => 'Katalog Obat', 'func' => 'Tabel utama berisi seluruh database produk aktif dan non-aktif.'],
                    ['name' => 'Aturan Harga', 'func' => 'Setting harga beli (HPP), margin keuntungan, dan harga jual final.'],
                    ['name' => 'Satuan & Konversi', 'func' => 'Konfigurasi multi-satuan (1 Box = 10 Strip = 100 Tablet).'],
                    ['name' => 'Kategori', 'func' => 'Pengelompokan produk (Obat Bebas, Keras, Alkes) untuk kemudahan laporan.']
                ],
                'buttons' => [
                    ['label' => '+ Tambah Produk', 'func' => 'Membuka formulir kosong untuk mendaftarkan SKU/Item baru ke database.'],
                    ['label' => 'Sync Barcode', 'func' => 'Fitur untuk menghubungkan Scanner Barcode agar siap digunakan saat input.'],
                    ['label' => 'Kelola Konversi', 'func' => 'Tombol aksi di baris produk untuk mengatur pecahan satuan (Box ke Pcs).'],
                    ['label' => 'Import Excel', 'func' => 'Fitur upload massal untuk memasukkan ratusan data obat sekaligus dari file .xlsx.'],
                    ['label' => 'Cetak Label', 'func' => 'Membuat PDF berisi Barcode dan Harga untuk ditempel di rak display toko.']
                ],
                'procedures' => [
                    ['title' => 'Input Obat Baru', 'desc' => 'Klik "+ Tambah Produk". Isi Nama, Kategori, dan Barcode. Simpan untuk lanjut ke harga.'],
                    ['title' => 'Atur Konversi', 'desc' => 'Buka tab Satuan. Masukkan Satuan Besar (Box) dan tentukan Isinya (Misal: 100 Tablet).'],
                    ['title' => 'Update Harga Beli', 'desc' => 'Jika ada kenaikan, update di Master agar HPP dan margin tetap akurat di laporan.']
                ],
                'form_fields' => [
                    ['name' => 'Nama Produk', 'description' => 'Nama merk/generik lengkap (Contoh: Amoxicillin 500mg).', 'required' => true],
                    ['name' => 'Barcode / SKU', 'description' => 'Kode unik identifikasi produk. Scan barcode fisik di sini.', 'required' => false],
                    ['name' => 'Kategori', 'description' => 'Grup obat (Bebas, Keras, Psikotropika).', 'required' => true],
                    ['name' => 'Satuan Terkecil', 'description' => 'Unit dasar stok yang dijual eceran (Tablet, Pcs).', 'required' => true],
                    ['name' => 'Harga Jual', 'description' => 'Nilai jual akhir per satuan terkecil ke pelanggan.', 'required' => true],
                    ['name' => 'Stok Minimal', 'description' => 'Batas jumlah stok sebelum sistem memberikan peringatan restock.', 'required' => true]
                ]
            ],
            'pos' => [
                'title' => 'Transaksi Kasir (POS)',
                'image' => 'pos.png',
                'description' => 'Antarmuka penjualan cepat. Mendukung Barcode Scanner, Layar Sentuh, dan multi-metode pembayaran.',
                'screenshots' => [
                    ['src' => 'guide_pos_ui_1766530034081.png', 'caption' => 'Halaman Kasir (Point of Sale)'],
                    ['src' => 'finance.png', 'caption' => 'Ilustrasi Pembayaran & Keuangan']
                ],
                'golden_rules' => [
                    'Selalu gunakan **Barcode Scanner** untuk kecepatan.',
                    'Cek **Struk Terakhir** jika printer macet.',
                    'Input **Nominal Bayar** dengan teliti.'
                ],
                'sub_menus' => [
                    ['name' => 'Order Grid', 'func' => 'Area pemilihan produk (kiri) menampilkan katalog dengan gambar/icon.'],
                    ['name' => 'Cart Panel', 'func' => 'Area keranjang belanja (kanan) menampilkan item terpilih dan subtotal.'],
                    ['name' => 'Payment Modal', 'func' => 'Jendela popup akhir untuk memilih metode bayar dan input uang tunai.'],
                    ['name' => 'History', 'func' => 'Daftar riwayat transaksi hari ini untuk keperluan reprint atau pembatalan.']
                ],
                'buttons' => [
                    ['label' => 'Cari (F1)', 'func' => 'Shortcut keyboard F1 untuk langsung mengetik nama obat di kolom pencarian.'],
                    ['label' => 'Diskon Global', 'func' => 'Memberikan potongan harga (%) atau nominal (Rp) untuk TOTAL belanjaan.'],
                    ['label' => 'Bayar (Space)', 'func' => 'Tombol besar hijau untuk menyelesaikan belanja & membuka layar pembayaran.'],
                    ['label' => 'Hold / Simpan', 'func' => 'Menyimpan antrian belanja sementara jika pelanggan ingin menambah barang lagi.'],
                    ['label' => 'Print Last', 'func' => 'Mencetak ulang struk transaksi terakhir tanpa perlu masuk history.'],
                    ['label' => 'Hapus Item (x)', 'func' => 'Menghapus satu baris produk dari keranjang belanja.']
                ],
                'procedures' => [
                    ['title' => 'Penjualan Kilat', 'desc' => 'Scan Barcode produk. Barang otomatis masuk keranjang. Tekan Space untuk bayar.'],
                    ['title' => 'Beri Diskon', 'desc' => 'Klik nominal harga di baris item untuk diskon satuan, atau tombol bawah untuk diskon total.'],
                    ['title' => 'Bayar Tunai', 'desc' => 'Masukkan uang dari pelanggan. Tekan Enter. Struk akan keluar dan laci kasir terbuka.']
                ],
                'form_fields' => [
                    ['name' => 'Cari Produk', 'description' => 'Ketik Nama atau Scan Barcode di sini untuk menambah item.', 'required' => false],
                    ['name' => 'Qty', 'description' => 'Jumlah barang. Tekan + atau - di keyboard/layar.', 'required' => true],
                    ['name' => 'Nominal Bayar', 'description' => 'Uang tunai yang diterima (Misal: 100.000).', 'required' => true],
                    ['name' => 'Catatan', 'description' => 'Keterangan tambahan (misal: "Resep Dr. Budi") di struk.', 'required' => false]
                ]
            ],
            'stock' => [
                'title' => 'Stok & Inventori',
                'image' => 'stock.png',
                'description' => 'Kelola fisik barang dengan akurat. Mendukung sistem FEFO untuk meminimalisir obat kadaluarsa.',
                'screenshots' => [
                    ['src' => 'stock.png', 'caption' => 'Manajemen Stok & Opname'],
                    ['src' => 'archive-box.png', 'caption' => 'Ilustrasi Gudang']
                ],
                'golden_rules' => [
                    'Update **Nomor Batch** setiap barang datang.',
                    'Prioritaskan stok dengan **Expired Terdekat**.',
                    'Lakukan **Stok Opname** minimal sebulan sekali.'
                ],
                'sub_menus' => [
                    ['name' => 'Batch & Exp', 'func' => 'Monitoring detail setiap batch produksi dan tanggal kadaluarsanya.'],
                    ['name' => 'Stok Opname', 'func' => 'Halaman audit untuk mencocokkan stok fisik di rak dengan stok di sistem komputer.'],
                    ['name' => 'Mutasi Stok', 'func' => 'Pencatatan perpindahan barang (Masuk/Keluar) di luar penjualan, misal: Barang Rusak/Hilang.'],
                    ['name' => 'Kartu Stok', 'func' => 'Laporan detail pergerakan satu item (History in/out) untuk investigasi selisih.']
                ],
                'buttons' => [
                    ['label' => '+ Adjustment', 'func' => 'Membuat penyesuaian stok manual (menambah/mengurangi) karena alasan khusus.'],
                    ['label' => 'Filter Expired', 'func' => 'Menyaring daftar obat yang akan kadaluarsa dalam < 3 bulan atau < 6 bulan.'],
                    ['label' => 'Print Log', 'func' => 'Mencetak riwayat pergerakan stok ke PDF untuk arsip gudang.'],
                    ['label' => 'Fix Stock', 'func' => 'Tombol utility untuk sinkronisasi ulang total stok jika ada ketidakcocokan saldo database.']
                ],
                'procedures' => [
                    ['title' => 'Audit Stok Opname', 'desc' => 'Pilih Rak. Hitung fisik barang. Masukkan angka ke kolom Real. Sistem setuju selisihnya.'],
                    ['title' => 'Cek Obat Expired', 'desc' => 'Gunakan Filter Soon Expired. Pindahkan barang tersebut ke etalase promo depan.'],
                    ['title' => 'Koreksi Stok Rusak', 'desc' => 'Klik Adjustment. Pilih "Rusak", kurangi jumlah stok, beri alasan di catatan.']
                ],
                'form_fields' => [
                    ['name' => 'Nomor Batch', 'description' => 'Kode unik produksi dari manufaktur untuk tracking.', 'required' => true],
                    ['name' => 'Tgl Expired', 'description' => 'Tanggal batas aman penggunaan obat.', 'required' => true],
                    ['name' => 'Alasan Koreksi', 'description' => 'Keterangan kenapa stok diubah (Hilang, Pecah, Rusak, dll).', 'required' => true],
                    ['name' => 'Qty Real', 'description' => 'Jumlah fisik yang ditemukan saat melakukan audit.', 'required' => true]
                ]
            ],
            'procurement' => [
                'title' => 'Pengadaan (Procurement)',
                'image' => 'procurement.png',
                'description' => 'Siklus pembelian ke PBF. Pastikan modal (HPP) tercatat benar untuk laporan laba rugi.',
                'screenshots' => [
                    ['src' => 'procurement.png', 'caption' => 'Alur Pengadaan Barang'],
                    ['src' => 'po.png', 'caption' => 'Ilustrasi Purchase Order']
                ],
                'golden_rules' => [
                    'Sesuaikan **Nomor Faktur** dengan fisik kertas.',
                    'Pastikan **Diskon Supplier** terinput.',
                    'Cek **Ketelitian Batch** saat penerimaan.'
                ],
                'sub_menus' => [
                    ['name' => 'Purchase Order (PO)', 'func' => 'Surat pesanan pembelian resmi kepada supplier untuk request stok.'],
                    ['name' => 'Penerimaan (GR)', 'func' => 'Goods Receipt. Halaman input barang datang berdasarkan PO yang sudah dibuat.'],
                    ['name' => 'Hutang (AP)', 'func' => 'Accounts Payable. Daftar tagihan supplier yang belum lunas jatuh tempo.'],
                    ['name' => 'Supplier', 'func' => 'Database vendor/PBF lengkap dengan kontak sales dan alamat.']
                ],
                'buttons' => [
                    ['label' => '+ Buat PO', 'func' => 'Memulai form pesanan pembelian baru.'],
                    ['label' => 'Terima Barang', 'func' => 'Tombol aksi di daftar PO untuk memproses kedatangan barang (mengubah status jadi Received).'],
                    ['label' => 'Bayar Hutang', 'func' => 'Mencatat pembayaran keluar ke supplier untuk melunasi faktur.'],
                    ['label' => 'Cek Riwayat', 'func' => 'Melihat arsip pembelian yang sudah selesai (Lunas & Diterima).']
                ],
                'procedures' => [
                    ['title' => 'Pesan Barang (PO)', 'desc' => 'Pilih Supplier. Klik barang yang stoknya kritis. Masukkan jumlah yang mau dibeli. Simpan.'],
                    ['title' => 'Terima Barang (GR)', 'desc' => 'Buka GR. Cari No PO tadi. Cocokkan jumlah fisik yang datang. Isi Batch & Expired sesuai fisik box.'],
                    ['title' => 'Bayar Tagihan', 'desc' => 'Buka menu Hutang. Pilih faktur yang mau dicicil/lunas. Pilih metode bayar. Selesai.']
                ],
                'form_fields' => [
                    ['name' => 'No Faktur', 'description' => 'Nomor referensi dari surat jalan/faktur supplier.', 'required' => true],
                    ['name' => 'Harga Beli', 'description' => 'Harga per satuan dari supplier (Neto setelah pajak).', 'required' => true],
                    ['name' => 'Termin Pembayaran', 'description' => 'Jangka waktu pembayaran (Cash, Tempo 7 Hari, 30 Hari).', 'required' => true]
                ]
            ],
            'reports' => [
                'title' => 'Laporan & Keuangan',
                'image' => 'report.png',
                'description' => 'Dashboard audit finansial. Lihat Laba Rugi, Rekap Pajak, dan Biaya Operasional secara akurat.',
                'screenshots' => [
                    ['src' => 'report.png', 'caption' => 'Laporan Keuangan'],
                    ['src' => 'finance.png', 'caption' => 'Analisa Profit & Loss']
                ],
                'golden_rules' => [
                    'Lakukan **Tutup Buku** setiap shift berakhir.',
                    'Input semua **Pengeluaran** (Gaji, Listrik).',
                    'Andalkan **Laporan PPN** untuk setoran pajak.'
                ],
                'sub_menus' => [
                    ['name' => 'Laporan Penjualan', 'func' => 'Detail omset per-transaksi, per-kasir, atau per-produk.'],
                    ['name' => 'Laba Rugi (P&L)', 'func' => 'Laporan profit bersih (Net Income) = Pendapatan - (HPP + Biaya Operasional).'],
                    ['name' => 'Pengeluaran (Biaya)', 'func' => 'Buku kas keluar untuk biaya operasional toko (Listrik, Air, Gaji, ATK).'],
                    ['name' => 'Pajak PPN', 'func' => 'Rekapitulasi PPN Masukan (Beli) dan PPN Keluaran (Jual) untuk pelaporan pajak.'],
                    ['name' => 'Kelola Kategori', 'func' => 'Manajemen master data kategori biaya untuk pengelompokan laporan.']
                ],
                'buttons' => [
                    ['label' => 'Download PDF', 'func' => 'Mengunduh laporan dalam format dokumen resmi siap cetak.'],
                    ['label' => 'Export Excel', 'func' => 'Mengunduh data mentah ke .xlsx untuk diolah lebih lanjut.'],
                    ['label' => 'Print Thermal', 'func' => 'Mencetak ringkasan laporan singkat di printer struk kasir.'],
                    ['label' => '+ Pengeluaran', 'func' => 'Tombol untuk mencatat biaya operasional baru hari ini.'],
                    ['label' => 'Kelola Kategori', 'func' => 'Tombol khusus admin untuk menambah/edit kategori pengeluaran.']
                ],
                'procedures' => [
                    ['title' => 'Analisa Laba Rugi', 'desc' => 'Pilih Periode Bulan. Klik Analisa. Pastikan HPP sudah terisi semua agar profit akurat.'],
                    ['title' => 'Input Biaya Toko', 'desc' => 'Buka Buku Pengeluaran. Klik "+ Pengeluaran". Pilih Kategori, input Jumlah, dan Simpan.'],
                    ['title' => 'Tambah Kategori Biaya', 'desc' => 'Klik tombol "Kelola Kategori". Tambahkan nama kategori baru (misal: "Keamanan") lalu Simpan.']
                ],
                'form_fields' => [
                    ['name' => 'Periode', 'description' => 'Rentang tanggal Awal dan Akhir laporan yang ingin dilihat.', 'required' => true],
                    ['name' => 'Kategori Biaya', 'description' => 'Jenis pengeluaran (Operasional, Gaji, Marketing, Maintenance).', 'required' => true],
                    ['name' => 'User ID', 'description' => 'Filter laporan berdasarkan kinerja karyawan tertentu.', 'required' => false]
                ]
            ],
            'profile' => [
                'title' => 'Pengaturan Profil',
                'image' => 'profile.png',
                'description' => 'Keamanan dan identitas user. Kelola password dan preferensi tampilan aplikasi.',
                'screenshots' => [
                    ['src' => 'profile.png', 'caption' => 'Halaman Profil User']
                ],
                'golden_rules' => [
                    'Ganti **Password** tiap 3 bulan sekali.',
                    'Pastikan **Email** aktif untuk recovery.',
                    'Gunakan **Foto Profil** asli untuk audit.'
                ],
                'sub_menus' => [
                    ['name' => 'Identitas', 'func' => 'Halaman edit Nama Lengkap, Email, dan Foto Profil.'],
                    ['name' => 'Keamanan', 'func' => 'Formulir ganti password dan manajemen sesi login aktif.'],
                    ['name' => 'Preferences', 'func' => 'Pengaturan personalisasi tampilan (Tema Gelap/Terang).']
                ],
                'buttons' => [
                    ['label' => 'Simpan Perubahan', 'func' => 'Menyimpan update data diri ke database.'],
                    ['label' => 'Logout Sesi Lain', 'func' => 'Memaksa keluar akun anda di perangkat lain yang tertinggal.'],
                    ['label' => 'Update Password', 'func' => 'Mengganti kata sandi lama dengan yang baru.'],
                    ['label' => 'Upload Foto', 'func' => 'Mengunggah file gambar untuk foto profil.']
                ],
                'procedures' => [
                    ['title' => 'Amankan Akun', 'desc' => 'Buka Keamanan. Masukkan Password Lama, lalu buat Password Baru yang rumit. Simpan.'],
                    ['title' => 'Atur Mode Gelap', 'desc' => 'Buka Preferences. Klik Switch Dark Mode. Sistem akan berubah warna lebih nyaman dimata.']
                ],
                'form_fields' => [
                    ['name' => 'Password Baru', 'description' => 'Kata sandi baru minimal 8 karakter kombinasi huruf & angka.', 'required' => true],
                    ['name' => 'Email Akun', 'description' => 'Alamat email yang digunakan untuk login masuk.', 'required' => true]
                ]
            ],
            'settings' => [
                'title' => 'Manajemen Sistem',
                'image' => 'settings.png',
                'description' => 'Pengaturan Global. Kelola data outlet, hak akses karyawan, dan backup sistem.',
                'screenshots' => [
                    ['src' => 'settings.png', 'caption' => 'Pengaturan Toko & Sistem']
                ],
                'golden_rules' => [
                    'Download **Backup Data** setiap minggu.',
                    'Batasi **Hak Akses** sesuai tugas staff.',
                    'Lengkapi **Info Toko** untuk header struk.'
                ],
                'sub_menus' => [
                    ['name' => 'Info Toko', 'func' => 'Setting identitas outlet (Nama, Alamat, Telepon, Logo).'],
                    ['name' => 'User & Role', 'func' => 'Manajemen akun karyawan, pembuatan user baru, dan assignment role.'],
                    ['name' => 'Environment', 'func' => 'Konfigurasi teknis (Pajak Default, Timezone, Format Invoice).'],
                    ['name' => 'Database', 'func' => 'Maintenance data, optimasi, dan fitur backup/restore.']
                ],
                'buttons' => [
                    ['label' => '+ Tambah User', 'func' => 'Mendaftarkan akun baru untuk karyawan.'],
                    ['label' => 'Atur Permission', 'func' => 'Menentukan menu apa saja yang boleh dibuka oleh role tertentu.'],
                    ['label' => 'Generate Backup', 'func' => 'Membuat file cadangan database .sql untuk diunduh.'],
                    ['label' => 'Update Logo', 'func' => 'Mengganti gambar logo yang muncul di header struk kasir.']
                ],
                'procedures' => [
                    ['title' => 'Tambah Staff Baru', 'desc' => 'Buka User & Role. Klik Tambah. Isi Nama & Password. Pilih Role (Kasir/Admin). Kirim login ke staff.'],
                    ['title' => 'Lengkapi Alamat Toko', 'desc' => 'Buka Info Toko. Masukkan Alamat Lengkap & No WhatsApp. Info ini akan muncul di Struk Penjualan.'],
                    ['title' => 'Amankan Data', 'desc' => 'Masuk menu Database. Klik "Generate Backup". Simpan file di Google Drive atau Flashdisk eksternal.']
                ],
                'form_fields' => [
                    ['name' => 'Nama Toko', 'description' => 'Teks judul utama yang tercetak di struk belanja.', 'required' => true],
                    ['name' => 'Role', 'description' => 'Level otoritas user (Super Admin, Admin, Kasir, Gudang).', 'required' => true],
                    ['name' => 'Default PPN', 'description' => 'Persentase pajak pertambahan nilai standar (misal: 11%).', 'required' => true]
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
