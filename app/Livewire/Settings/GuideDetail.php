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
                'image' => 'guide_dashboard.png',
                'description' => 'Dashboard adalah pusat informasi visual yang memberikan ringkasan performa apotek secara real-time. Di sini Anda dapat memantau kesehatan bisnis melalui angka omset, jumlah transaksi, dan peringatan stok secara instan.',
                'screenshots' => [],
                'golden_rules' => [
                    'Pantau **Widget Pendapatan** untuk melihat tren penjualan harian.',
                    'Periksa **Stok Kritis** setiap pagi untuk merencanakan pengiriman barang.',
                    'Gunakan **Filter Tanggal** untuk membandingkan performa bulanan.'
                ],
                'sub_menus' => [
                    ['name' => 'Ringkasan Omset', 'func' => 'Menampilkan grafik pendapatan kotor harian dan total omset dalam periode tertentu.'],
                    ['name' => 'Stok Menipis', 'func' => 'Daftar produk yang sudah mencapai batas minimal stok dan perlu segera dipesan.'],
                    ['name' => 'Produk Terlaris', 'func' => 'Informasi 5 produk dengan volume penjualan tertinggi untuk optimasi stok.'],
                    ['name' => 'Aktivitas Terakhir', 'func' => 'Log audit yang mencatat transaksi atau perubahan data terbaru oleh staf.']
                ],
                'buttons' => [
                    ['label' => 'Filter Periode', 'func' => 'Tombol kalender di pojok kanan untuk memilih rentang waktu data (Hari ini, Bulan ini, atau Custom).'],
                    ['label' => 'Detail Stok', 'func' => 'Klik pada widget stok menipis untuk langsung menuju halaman inventori dengan filter aktif.'],
                    ['label' => 'Refresh', 'func' => 'Ikon putar untuk memperbarui data dashboard secara manual tanpa memuat ulang seluruh halaman.']
                ],
                'procedures' => [
                    ['title' => 'Memantau Penjualan Harian', 'desc' => 'Buka Dashboard, lihat grafik utama. Arahkan kursor ke titik grafik untuk melihat detail nominal transaksi pada jam tertentu.'],
                    ['title' => 'Mengecek Stok yang Harus Dipesan', 'desc' => 'Scroll ke widget "Stok Menipis". Produk dengan warna merah menandakan stok kritis. Klik "Lihat Semua" untuk membuat PO.']
                ],
                'form_fields' => [
                    ['name' => 'Rentang Tanggal', 'description' => 'Pilihan tanggal awal dan akhir untuk memfilter seluruh data dashboard.', 'required' => false]
                ]
            ],
            'master' => [
                'title' => 'Manajemen Produk (Master)',
                'image' => 'guide_products.png',
                'description' => 'Modul Master Data adalah jantung dari sistem ini. Di sini Anda mengelola database produk, kategori, satuan, hingga pemasok. Konsistensi data di modul ini sangat penting untuk akurasi laporan keuangan dan stok.',
                'screenshots' => [],
                'golden_rules' => [
                    'Pastikan **Barcode** unik dan sesuai dengan fisik produk.',
                    'Gunakan **Kategori** yang konsisten untuk kemudahan pelaporan.',
                    'Atur **Stok Minimal** agar sistem dapat memberi peringatan restock.'
                ],
                'sub_menus' => [
                    ['name' => 'Daftar Produk', 'func' => 'Halaman utama untuk melihat, mencari, menambah, dan mengubah data obat/barang.'],
                    ['name' => 'Kategori Produk', 'func' => 'Mengelompokkan produk berdasarkan jenis (misal: Obat Bebas, Obat Keras, Alat Kesehatan).'],
                    ['name' => 'Satuan & Konversi', 'func' => 'Mengatur unit jual (Pcs, Box, Strip) dan hubungan antar satuan tersebut.'],
                    ['name' => 'Manajemen Pemasok', 'func' => 'Daftar supplier atau PBF tempat apotek melakukan pengadaan barang.']
                ],
                'buttons' => [
                    ['label' => 'Tambah Produk', 'func' => 'Tombol biru di kanan atas untuk membuka form input produk baru.'],
                    ['label' => 'Edit (Ikon Pensil)', 'func' => 'Mengubah informasi produk yang sudah ada (Nama, Harga, Kategori, dll).'],
                    ['label' => 'Hapus (Ikon Sampah)', 'func' => 'Menghapus produk dari sistem. Catatan: Produk dengan riwayat transaksi tidak dapat dihapus.'],
                    ['label' => 'Kelola Satuan', 'func' => 'Tombol di baris produk untuk mengatur konversi (Isi 1 Box berapa Pcs).'],
                    ['label' => 'Cari (Search Bar)', 'func' => 'Mengetik nama produk atau scan barcode untuk menemukan item secara instan.'],
                    ['label' => 'Filter Kategori', 'func' => 'Dropdown untuk menampilkan produk hanya pada kategori tertentu saja.']
                ],
                'procedures' => [
                    ['title' => 'Menambah Produk Baru', 'desc' => '1. Klik "Tambah Produk".\n2. Isi Nama Obat, Kategori, dan Satuan Dasar.\n3. Masukkan Barcode (atau biarkan otomatis).\n4. Tentukan Stok Minimal dan Harga Jual.\n5. Klik Simpan.'],
                    ['title' => 'Mengatur Konversi Satuan', 'desc' => '1. Cari produk di Katalog.\n2. Klik tombol "Atur Satuan".\n3. Tambah satuan baru (Misal: Box).\n4. Masukkan jumlah isi terhadap satuan terkecil (Misal: 100).\n5. Simpan.'],
                    ['title' => 'Menambahkan Pemasok (Supplier)', 'desc' => '1. Masuk menu Pemasok.\n2. Klik "Tambah Pemasok".\n3. Isi Nama Perusahaan, No HP Sales, dan Alamat.\n4. Klik Simpan agar dapat dipilih di modul Pengadaan.']
                ],
                'form_fields' => [
                    ['name' => 'Nama Produk', 'description' => 'Nama lengkap obat beserta dosisnya (Contoh: Paracetamol 500mg).', 'required' => true],
                    ['name' => 'Barcode', 'description' => 'Kode unik produk. Bisa di-scan atau diisi manual untuk identifikasi cepat.', 'required' => true],
                    ['name' => 'Kategori', 'description' => 'Grup obat untuk pengelompokan laporan.', 'required' => true],
                    ['name' => 'Satuan Terkecil', 'description' => 'Unit paling dasar (eceran) produk, misal: Tablet atau Pcs.', 'required' => true],
                    ['name' => 'Harga Jual', 'description' => 'Harga yang dikenakan kepada pelanggan per satuan terkecil.', 'required' => true],
                    ['name' => 'Stok Minimal', 'description' => 'Angka batas bawah agar produk muncul di peringatan stok menipis.', 'required' => true]
                ]
            ],
            'pos' => [
                'title' => 'Transaksi Kasir (POS)',
                'image' => 'guide_cashier.png',
                'description' => 'Modul POS digunakan untuk melayani penjualan pelanggan dengan cepat. Antarmuka ini dirancang untuk kemudahan input menggunakan keyboard maupun barcode scanner.',
                'screenshots' => [],
                'golden_rules' => [
                    'Selalu gunakan **Barcode Scanner** untuk meminimalkan kesalahan input.',
                    'Pastikan **Metode Pembayaran** dipilih dengan benar (Tunai/Non-Tunai).',
                    'Cek kembali **Uang Kembali** sebelum menyelesaikan transaksi.'
                ],
                'sub_menus' => [
                    ['name' => 'Layar Penjualan', 'func' => 'Area utama mencari produk dan memasukkannya ke keranjang belanja.'],
                    ['name' => 'Keranjang (Cart)', 'func' => 'Daftar item yang akan dibeli, lengkap dengan pengaturan qty dan diskon per item.'],
                    ['name' => 'Pembayaran', 'func' => 'Layar final untuk input uang diterima dan cetak struk.']
                ],
                'buttons' => [
                    ['label' => 'Bayar Sekarang', 'func' => 'Membuka modal pembayaran untuk menyelesaikan transaksi.'],
                    ['label' => '+ (Tambah Qty)', 'func' => 'Menambah jumlah barang yang dibeli secara cepat.'],
                    ['label' => 'Hapus (Ikon Silang)', 'func' => 'Mengeluarkan item dari keranjang belanja.'],
                    ['label' => 'Reset Cart', 'func' => 'Membersihkan seluruh isi keranjang untuk memulai transaksi baru.'],
                    ['label' => 'Cetak Struk', 'func' => 'Mencetak bukti transaksi ke printer thermal setelah pembayaran sukses.']
                ],
                'procedures' => [
                    ['title' => 'Melayani Penjualan', 'desc' => '1. Scan barcode produk atau ketik nama di bar pencarian.\n2. Sesuaikan jumlah (Qty) jika perlu.\n3. Masukkan catatan per item (misal: "Aturan pakai 3x1") jika dibutuhkan.\n4. Klik "Bayar Sekarang".\n5. Masukkan nominal uang yang diterima.\n6. Klik "Selesai & Cetak Struk".'],
                    ['title' => 'Memberikan Diskon', 'desc' => 'Klik pada kolom diskon di baris item, masukkan persentase atau nominal potongan harga. Total akan terhitung otomatis.']
                ],
                'form_fields' => [
                    ['name' => 'Pencarian Produk', 'description' => 'Scan barcode atau ketik nama untuk memasukkan barang ke keranjang.', 'required' => false],
                    ['name' => 'Uang Diterima', 'description' => 'Nominal tunai yang diberikan pelanggan untuk menghitung kembalian.', 'required' => true],
                    ['name' => 'Catatan Item', 'description' => 'Keterangan tambahan yang akan muncul di bawah nama produk pada struk.', 'required' => false]
                ]
            ],
            'stock' => [
                'title' => 'Stok & Inventori',
                'image' => 'guide_stock.png',
                'description' => 'Kelola persediaan barang dengan sistem batch dan expired date. Modul ini memastikan Anda menjual produk dengan prinsip FEFO (First Expired First Out).',
                'screenshots' => [],
                'golden_rules' => [
                    'Jangan biarkan produk **Expired** tetap berada di rak display.',
                    'Lakukan **Stok Opname** rutin untuk mencocokkan fisik dengan sistem.',
                    'Gunakan **Log Stok** untuk melacak setiap perpindahan barang.'
                ],
                'sub_menus' => [
                    ['name' => 'Stok Per Batch', 'func' => 'Melihat detail stok setiap barang berdasarkan nomor batch dan tanggal kadaluarsa.'],
                    ['name' => 'Penyesuaian (Adjustment)', 'func' => 'Fitur untuk mengoreksi jumlah stok jika ada barang rusak atau hilang.'],
                    ['name' => 'Riwayat Stok', 'func' => 'Audit trail lengkap pergerakan stok masuk dan keluar per produk.']
                ],
                'buttons' => [
                    ['label' => 'Sesuaikan Stok', 'func' => 'Tombol untuk menambah atau mengurangi stok secara manual dengan alasan tertentu.'],
                    ['label' => 'Filter Kadaluarsa', 'func' => 'Menampilkan hanya produk yang akan expired dalam waktu dekat (misal 3-6 bulan).'],
                    ['label' => 'Cetak Kartu Stok', 'func' => 'Mengunduh laporan pergerakan stok untuk arsip fisik.']
                ],
                'procedures' => [
                    ['title' => 'Melakukan Stok Opname', 'desc' => '1. Cetak daftar stok saat ini.\n2. Hitung jumlah fisik di rak.\n3. Jika ada selisih, gunakan tombol "Sesuaikan Stok".\n4. Masukkan angka baru dan beri alasan (misal: "Selisih Opname").'],
                    ['title' => 'Mengecek Barang Expired', 'desc' => 'Gunakan filter "Kadaluarsa" pada daftar stok. Identifikasi batch yang mendekati tanggal EXP untuk segera diretur atau dipromosikan.']
                ],
                'form_fields' => [
                    ['name' => 'No. Batch', 'description' => 'Kode produksi dari pabrik untuk pelacakan satu kelompok barang.', 'required' => true],
                    ['name' => 'Expired Date', 'description' => 'Tanggal kadaluarsa produk. Sangat kritis untuk dipantau.', 'required' => true],
                    ['name' => 'Qty Penyesuaian', 'description' => 'Jumlah unit yang ingin ditambah (+) atau dikurangi (-).', 'required' => true],
                    ['name' => 'Alasan', 'description' => 'Keterangan mengapa melakukan koreksi stok (Rusak, Hilang, Salah Input).', 'required' => true]
                ]
            ],
            'procurement' => [
                'title' => 'Pengadaan (Procurement)',
                'image' => 'guide_procurement.png',
                'description' => 'Modul untuk mengatur pembelian barang ke supplier (Purchase Order) hingga penerimaan barang di gudang (Goods Receipt).',
                'screenshots' => [],
                'golden_rules' => [
                    'Sesuaikan **Harga Beli** di sistem dengan faktur fisik PBF.',
                    'Pastikan **No. Batch** yang terinput sama dengan yang tertera di box produk.',
                    'Gunakan **PO** agar pesanan Anda dapat dilacak statusnya.'
                ],
                'sub_menus' => [
                    ['name' => 'Purchase Order (PO)', 'func' => 'Membuat surat pesanan resmi kepada supplier.'],
                    ['name' => 'Penerimaan Barang', 'func' => 'Mencatat kedatangan barang dan memasukkannya ke stok aktif.'],
                    ['name' => 'Daftar Hutang', 'func' => 'Memantau faktur pembelian yang belum dibayar lunas ke supplier.']
                ],
                'buttons' => [
                    ['label' => 'Buat PO Baru', 'func' => 'Membuka form pesanan pembelian.'],
                    ['label' => 'Proses Terima', 'func' => 'Tombol pada baris PO untuk mengkonfirmasi bahwa barang telah sampai.'],
                    ['label' => 'Print PO', 'func' => 'Mencetak dokumen pesanan untuk dikirim ke sales supplier.']
                ],
                'procedures' => [
                    ['title' => 'Alur Pembelian Barang', 'desc' => '1. Masuk menu PO, klik "Buat PO Baru".\n2. Pilih Supplier dan tambah produk yang ingin dipesan.\n3. Kirim PO ke Supplier.\n4. Saat barang datang, buka menu Penerimaan Barang.\n5. Pilih PO terkait, masukkan No. Faktur dari Supplier.\n6. Cek jumlah fisik, input Batch & Expired, lalu Simpan.'],
                    ['title' => 'Mencatat Pembayaran Hutang', 'desc' => 'Cek menu "Daftar Hutang", pilih faktur yang jatuh tempo, lalu masukkan nominal pembayaran sesuai bukti transfer/cash.']
                ],
                'form_fields' => [
                    ['name' => 'Nomor Faktur', 'description' => 'Nomor referensi dari dokumen fisik yang dibawa supplier.', 'required' => true],
                    ['name' => 'Harga Beli (Neto)', 'description' => 'Harga modal per unit setelah diskon tapi sebelum PPN.', 'required' => true],
                    ['name' => 'Tenor/Termin', 'description' => 'Jangka waktu pembayaran (Cash atau Kredit 30 hari).', 'required' => true]
                ]
            ],
            'reports' => [
                'title' => 'Laporan Keuangan',
                'image' => 'guide_finance.png',
                'description' => 'Menyediakan data akurat untuk pengambilan keputusan bisnis. Mencakup laporan laba rugi, rekap penjualan, dan biaya operasional.',
                'screenshots' => [],
                'golden_rules' => [
                    'Pastikan semua **Biaya Operasional** (listrik, gaji) terinput.',
                    'Gunakan **Laporan Laba Rugi** untuk evaluasi bulanan.',
                    'Export data ke **Excel** jika butuh analisa lebih mendalam.'
                ],
                'sub_menus' => [
                    ['name' => 'Rekap Penjualan', 'func' => 'Daftar transaksi harian, per-kasir, atau per-shift.'],
                    ['name' => 'Laba Rugi', 'func' => 'Menghitung Pendapatan dikurangi HPP dan Biaya Operasional.'],
                    ['name' => 'Buku Biaya', 'func' => 'Catatan pengeluaran uang kas untuk kebutuhan operasional apotek.'],
                    ['name' => 'Laporan Pajak', 'func' => 'Rekapitulasi PPN masukan dan keluaran.']
                ],
                'buttons' => [
                    ['label' => 'Filter Tanggal', 'func' => 'Menentukan periode laporan yang ingin dibaca.'],
                    ['label' => 'Export PDF/Excel', 'func' => 'Mengunduh laporan dalam format file dokumen.'],
                    ['label' => 'Tampilkan Grafik', 'func' => 'Visualisasi tren data dalam bentuk bar atau line chart.']
                ],
                'procedures' => [
                    ['title' => 'Melihat Laba Bersih', 'desc' => '1. Buka menu Laba Rugi.\n2. Pilih periode (Misal: Bulan Desember).\n3. Sistem akan menghitung otomatis total penjualan dikurangi modal dan biaya pengeluaran.'],
                    ['title' => 'Mendownload Laporan Penjualan', 'desc' => 'Filter tanggal yang diinginkan, cari tombol "Export PDF" di bagian atas tabel. Simpan file untuk arsip.']
                ],
                'form_fields' => [
                    ['name' => 'Kategori Biaya', 'description' => 'Jenis pengeluaran (Gaji, ATK, Listrik, Kebersihan).', 'required' => true],
                    ['name' => 'Nominal', 'description' => 'Jumlah uang yang dikeluarkan atau diterima.', 'required' => true]
                ]
            ],
            'profile' => [
                'title' => 'Pengaturan Profil',
                'image' => 'guide_profile.png',
                'description' => 'Kelola informasi pribadi, update password, dan sesuaikan tampilan aplikasi agar lebih nyaman digunakan.',
                'screenshots' => [],
                'golden_rules' => [
                    'Gunakan **Password Kuat** (minimal 8 karakter dengan angka).',
                    'Jangan bagikan **Akun** Anda kepada staf lain.',
                    'Ganti **Foto Profil** untuk memudahkan identifikasi di sistem.'
                ],
                'sub_menus' => [
                    ['name' => 'Data Diri', 'func' => 'Halaman untuk merubah Nama, Email, dan unggah Foto Profil.'],
                    ['name' => 'Keamanan', 'func' => 'Menu khusus untuk mengganti password secara berkala.'],
                    ['name' => 'Tampilan', 'func' => 'Pengaturan teknis seperti Mode Gelap/Terang.']
                ],
                'buttons' => [
                    ['label' => 'Simpan', 'func' => 'Menerapkan perubahan data profil ke database.'],
                    ['label' => 'Ganti Password', 'func' => 'Tombol aksi untuk memperbarui kata sandi lama.'],
                    ['label' => 'Logout', 'func' => 'Keluar dari aplikasi dengan aman.']
                ],
                'procedures' => [
                    ['title' => 'Mengganti Password', 'desc' => '1. Masuk ke halaman Profil.\n2. Pilih tab Keamanan.\n3. Masukkan password lama, lalu ketik password baru dua kali sebagai konfirmasi.\n4. Klik Perbarui.'],
                    ['title' => 'Mengaktifkan Mode Gelap', 'desc' => 'Cari switch "Dark Mode" di halaman profil. Klik untuk mengubah tema aplikasi menjadi warna gelap yang lebih nyaman di mata.']
                ],
                'form_fields' => [
                    ['name' => 'Password Lama', 'description' => 'Sandi saat ini untuk verifikasi keamanan.', 'required' => true],
                    ['name' => 'Password Baru', 'description' => 'Minimal 8 karakter kombinasi huruf dan angka.', 'required' => true]
                ]
            ],
            'settings' => [
                'title' => 'Manajemen Sistem',
                'image' => 'guide_settings.png',
                'description' => 'Khusus Super Admin. Digunakan untuk mengatur parameter global aplikasi, hak akses user, dan info outlet.',
                'screenshots' => [],
                'golden_rules' => [
                    'Lakukan **Backup Database** secara rutin (minimal seminggu sekali).',
                    'Berikan **Hak Akses** sesuai dengan porsi kerja masing-masing staf.',
                    'Pastikan **Nama & Alamat Toko** sudah benar karena akan muncul di struk.'
                ],
                'sub_menus' => [
                    ['name' => 'Identitas Apotek', 'func' => 'Mengatur Nama, Alamat, No. Telp, dan Logo yang tercetak di struk.'],
                    ['name' => 'Manajemen User', 'func' => 'Membuat akun untuk staf (Admin, Kasir, Gudang) dan mengatur izinnya.'],
                    ['name' => 'Konfigurasi POS', 'func' => 'Setting teknis seperti PPN default dan pesan tambahan di bawah struk.'],
                    ['name' => 'Backup & Restore', 'func' => 'Fitur keamanan data untuk mengunduh salinan database.']
                ],
                'buttons' => [
                    ['label' => 'Tambah User', 'func' => 'Mendaftarkan staf baru ke dalam sistem.'],
                    ['label' => 'Atur Izin (Permissions)', 'func' => 'Memberi atau mencabut akses ke menu tertentu pada level user.'],
                    ['label' => 'Unduh Backup', 'func' => 'Menghasilkan file .sql sebagai cadangan data jika server bermasalah.'],
                    ['label' => 'Update Logo', 'func' => 'Mengganti gambar logo apotek dengan file baru dari komputer.']
                ],
                'procedures' => [
                    ['title' => 'Menambah Staf Kasir Baru', 'desc' => '1. Buka Manajemen User.\n2. Klik "Tambah User".\n3. Isi Nama, Email, dan Password.\n4. Pilih Role "Kasir".\n5. Klik Simpan. Staf sekarang bisa login dengan akun tersebut.'],
                    ['title' => 'Mengubah Info di Struk', 'desc' => '1. Masuk ke Identitas Apotek.\n2. Update Nama Toko atau Catatan Kaki (Footnote) Struk.\n3. Klik Simpan. Perubahan langsung berlaku pada transaksi berikutnya di kasir.']
                ],
                'form_fields' => [
                    ['name' => 'Role/Peran', 'description' => 'Level akses (Super Admin, Admin, Kasir, Bagian Gudang).', 'required' => true],
                    ['name' => 'PPN (%)', 'description' => 'Besaran pajak default yang berlaku di toko.', 'required' => true]
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
