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
            'initial-setup' => [
                'title' => 'Setup Awal Aplikasi',
                'image' => 'guide_setup.png',
                'description' => 'Panduan lengkap konfigurasi sistem apotek dari nol, langkah demi langkah, agar siap digunakan untuk operasional kasir, inventori, dan akuntansi keuangan tanpa resiko saldo minus atau data yang salah referensi.',
                'screenshots' => [],
                'golden_rules' => [
                    'Isi **Identitas Toko** terlebih dahulu karena data ini dicetak pada struk penjualan dan kop laporan.',
                    'Setup **Daftar Akun (COA) / Rekening** sebelum mencatat transaksi keuangan.',
                    'Wajib menginput **Saldo Awal** (Kas, Bank, dan Persediaan) agar laporan neraca awal seimbang dan saldo tidak minus.',
                    'Lakukan **Import Data Master** (Kategori, Satuan, Supplier, Pelanggan, Produk) secara berurutan agar referensi tidak error.',
                    'Lakukan **transaksi percobaan** di kasir sebelum benar-benar melayani pelanggan, untuk memastikan semua pengaturan sudah benar.'
                ],
                'sub_menus' => [
                    ['name' => '1. Pengaturan Toko', 'func' => 'Mengatur nama, alamat, nomor telepon, logo, dan tarif PPN toko.'],
                    ['name' => '2. Manajemen Pengguna', 'func' => 'Menambahkan akun staf kasir, apoteker, dan gudang beserta hak aksesnya.'],
                    ['name' => '3. Setup Rekening Bank', 'func' => 'Menyusun Chart of Accounts (COA) / rekening bank operasional.'],
                    ['name' => '4. Saldo Awal (Opening Balance)', 'func' => 'Mengisi kas laci awal, rekening bank, dan taksiran total nilai persediaan obat awal.'],
                    ['name' => '5. Import Data Master', 'func' => 'Mengimpor database produk obat, supplier, dan pelanggan secara massal.'],
                    ['name' => '6. Import Stok Awal', 'func' => 'Memasukkan jumlah stok, batch, dan tanggal kadaluarsa obat yang sudah dimiliki.'],
                    ['name' => '7. Import Omset Historis', 'func' => 'Mengunggah omset tahun lalu jika ingin melihat komparasi laporan Laba Rugi historis.'],
                    ['name' => '8. Uji Coba Transaksi', 'func' => 'Mencoba transaksi kasir sebelum sistem benar-benar dipakai melayani pelanggan.']
                ],
                'buttons' => [
                    ['label' => 'Buka Pengaturan Toko', 'func' => 'Mengarah ke halaman /settings/store'],
                    ['label' => 'Buka Manajemen User', 'func' => 'Mengarah ke halaman /admin/users'],
                    ['label' => 'Buka Saldo Awal', 'func' => 'Mengarah ke halaman /finance/opening-balance'],
                    ['label' => 'Buka Daftar Akun (COA)', 'func' => 'Mengarah ke halaman /accounting/accounts'],
                    ['label' => 'Buka Laporan Penjualan', 'func' => 'Mengarah ke halaman /reports/sales']
                ],
                'procedures' => [
                    ['title' => 'Langkah 1: Login & Mengisi Pengaturan Toko', 'desc' => "1. Login menggunakan akun Super Admin/Owner yang diberikan saat instalasi.\n2. Buka halaman <a href=\"/settings/store\" class=\"text-blue-600 hover:underline font-bold\">Pengaturan Toko</a>.\n3. Isi **Nama Apotek**, **Alamat Lengkap**, dan **Nomor Telepon** — data ini akan tercetak di struk kasir dan kop semua laporan.\n4. Isi **Tarif PPN Default** (misal 11%) jika toko Anda dikenakan pajak, atau isi 0 jika tidak.\n5. Upload **Logo Apotek** (opsional, format PNG/JPG).\n6. Klik **Simpan**."],
                    ['title' => 'Langkah 2: Menambahkan Pengguna & Staf', 'desc' => "1. Buka halaman <a href=\"/admin/users\" class=\"text-blue-600 hover:underline font-bold\">Manajemen User</a>.\n2. Klik tombol **Tambah User**.\n3. Isi Nama Lengkap, Email, dan Password staf.\n4. Pilih **Role/Peran** — sistem punya 4 role bawaan (super-admin, admin, kasir, gudang); kalau butuh peran lain seperti Apoteker, buat dulu role custom-nya di Manajemen Role.\n5. Klik **Simpan** — staf langsung bisa login dengan akun tersebut.\n6. Ulangi untuk setiap staf yang akan menggunakan sistem."],
                    ['title' => 'Langkah 3: Menyiapkan Daftar Akun (COA) & Rekening Bank', 'desc' => "1. Buka halaman <a href=\"/accounting/accounts\" class=\"text-blue-600 hover:underline font-bold\">Daftar Akun (COA)</a>.\n2. Chart of Accounts standar (Kas, Bank, Piutang, Beban, dll) sudah tersedia otomatis — Anda **tidak perlu** membuatnya dari nol.\n3. Jika memiliki lebih dari satu rekening bank operasional, klik tombol **Tambah Akun Bank**.\n4. Isi Nama Bank terkait (misal: BCA - 1234567890), kode akun baru dibuat otomatis (format 1-12XX).\n5. Klik **Simpan**."],
                    ['title' => 'Langkah 4: Memasukkan Saldo Awal (Opening Balance) — PENTING', 'desc' => "1. Buka halaman <a href=\"/finance/opening-balance\" class=\"text-blue-600 hover:underline font-bold\">Saldo Awal</a>.\n2. Isi nominal uang tunai fisik di laci kasir pada kolom **Kas**.\n3. Isi saldo setiap rekening **Bank** sesuai buku tabungan/mutasi terakhir.\n4. Isi **Taksiran Nilai Persediaan** — total modal (harga beli) seluruh stok obat yang Anda miliki saat ini.\n5. Di bagian Ekuitas, isi **Modal Awal** dengan angka yang **SAMA** dengan total penjumlahan Kas + Bank + Persediaan, agar Neraca seimbang.\n6. Perhatikan indikator status di layar — kalau masih ada selisih, periksa kembali angka yang diinput.\n7. Klik **Konfirmasi & Kunci Saldo Awal**. Setelah dikunci, saldo awal tidak bisa diubah sendiri lagi."],
                    ['title' => 'Langkah 5: Mengimpor Data Master (Kategori, Satuan, Supplier, Pelanggan, Produk)', 'desc' => "1. Import **berurutan**: Kategori & Satuan → Supplier → Pelanggan → baru Produk (Produk butuh referensi Kategori/Satuan/Supplier yang sudah ada, kalau dibalik akan gagal).\n2. Buka masing-masing halaman Master, klik **Import Excel** lalu **Download Template**.\n3. Isi template Excel sesuai kolom yang tersedia — jangan mengubah nama header di baris pertama.\n4. Simpan file, kembali ke aplikasi, klik **Import Excel** lagi, pilih file yang sudah diisi.\n5. Klik **Import** dan tunggu notifikasi sukses. Jika ada baris error, sistem menunjukkan baris mana yang bermasalah untuk diperbaiki lalu diimpor ulang."],
                    ['title' => 'Langkah 6: Mengimpor Stok Awal Obat', 'desc' => "1. Setelah data Produk masuk, buka menu import Stok Awal.\n2. Isi jumlah stok per produk beserta **Nomor Batch** dan **Tanggal Kadaluarsa** — data ini wajib untuk sistem FEFO (First Expired First Out).\n3. Isi juga **Harga Beli** per unit agar perhitungan HPP (Harga Pokok Penjualan) akurat sejak transaksi pertama.\n4. Import, lalu cek kembali di halaman Stok & Inventori bahwa jumlahnya sudah sesuai dengan fisik di apotek."],
                    ['title' => 'Langkah 7: Mengimpor Omset Historis (Opsional)', 'desc' => "1. Kalau ingin laporan Laba Rugi bisa dibandingkan dengan bulan/tahun sebelumnya, buka <a href=\"/reports/sales\" class=\"text-blue-600 hover:underline font-bold\">Laporan Penjualan</a>.\n2. Klik **Import Omset**, unduh template, isi kolom Tanggal/Periode, Omset, HPP, dan Laba dari catatan lama Anda.\n3. Upload kembali — sistem otomatis membuat jurnal historis tanpa memengaruhi stok maupun kas saat ini."],
                    ['title' => 'Langkah 8: Uji Coba Transaksi Sebelum Go-Live', 'desc' => "1. Lakukan 1-2 transaksi percobaan di menu **Kasir (POS)** memakai produk sungguhan dengan qty kecil.\n2. Cek struk tercetak dengan benar (nama toko, harga, kembalian).\n3. Cek **Laporan Penjualan** dan **Stok** — pastikan angkanya berkurang/bertambah sesuai transaksi percobaan tadi.\n4. Kalau ada yang salah (misal saldo minus atau harga keliru), perbaiki dulu Saldo Awal/Data Master sebelum melayani pelanggan sungguhan.\n5. Setelah semua benar, sistem siap dipakai untuk operasional harian."]
                ],
                'form_fields' => [
                    ['name' => 'Nama Apotek', 'description' => 'Nama resmi toko yang tercetak di kop laporan dan struk penjualan.', 'required' => true],
                    ['name' => 'PPN Default (%)', 'description' => 'Persentase pajak yang otomatis diterapkan pada transaksi kasir (isi 0 jika toko tidak dikenakan PPN).', 'required' => true],
                    ['name' => 'Kas Awal', 'description' => 'Nominal uang tunai fisik yang ada di laci kasir saat sistem mulai digunakan.', 'required' => true],
                    ['name' => 'Saldo Bank Awal', 'description' => 'Saldo setiap rekening bank operasional sesuai mutasi/buku tabungan terakhir.', 'required' => true],
                    ['name' => 'Nilai Persediaan Awal', 'description' => 'Total taksiran modal (harga beli) seluruh stok obat yang sudah dimiliki sebelum sistem digunakan.', 'required' => true],
                    ['name' => 'Modal Awal (Ekuitas)', 'description' => 'Harus sama dengan total Kas + Bank + Persediaan Awal, agar Neraca Saldo Awal seimbang (selisih menunjukkan 0).', 'required' => true],
                ]
            ],
            'dashboard' => [
                'title' => 'Dashboard & Statistik',
                'image' => 'guide_dashboard.png',
                'description' => 'Dashboard adalah pusat informasi visual yang memberikan ringkasan performa apotek secara real-time — omset & transaksi hari ini, tren penjualan, klasemen kasir, produk terlaris/paling lambat, hingga piutang dan hutang yang mendekati jatuh tempo. Setiap widget hanya tampil kalau akun Anda punya izin (permission) untuk melihatnya, jadi tampilan bisa berbeda antar role (Kasir, Admin, Super Admin).',
                'screenshots' => [],
                'golden_rules' => [
                    'Pantau **Omset & Transaksi Hari Ini** setiap pagi dan sore untuk memantau performa harian.',
                    'Gunakan **Tren Omset Harian** untuk melihat pola penjualan mingguan/bulanan, bukan cuma angka satu hari.',
                    'Cek **Piutang Jatuh Tempo** secara rutin agar tidak ada tagihan pelanggan yang terlewat ditagih.',
                    'Cek **Hutang Pembelian** agar pembayaran ke supplier tidak telat dan hubungan bisnis tetap baik.',
                    'Gunakan grafik **Produk Paling Lambat** untuk menentukan barang mana yang perlu dipromosikan sebelum kadaluarsa.'
                ],
                'sub_menus' => [
                    ['name' => 'Omset & Transaksi Hari Ini', 'func' => 'Dua kartu ringkas menampilkan total omset dan jumlah transaksi hari ini secara real-time.'],
                    ['name' => 'Tren Omset Harian', 'func' => 'Grafik garis omset dengan filter periode Harian, Mingguan, atau Bulanan.'],
                    ['name' => 'Klasemen Penjualan Kasir', 'func' => 'Peringkat kasir dengan omset penjualan tertinggi pada bulan berjalan, lengkap dengan modal "Lihat Semua" untuk daftar lengkap.'],
                    ['name' => 'Produk Paling Laku', 'func' => 'Grafik batang produk dengan volume penjualan tertinggi, bisa difilter Harian/Mingguan/Bulanan/Tahunan.'],
                    ['name' => 'Produk Paling Lambat', 'func' => 'Grafik batang produk dengan volume penjualan terendah — kandidat untuk dipromosikan atau dicek tanggal kadaluarsanya.'],
                    ['name' => 'Piutang Jatuh Tempo', 'func' => 'Tabel tagihan pelanggan yang mendekati atau sudah melewati jatuh tempo, beserta total piutang usaha.'],
                    ['name' => 'Hutang Pembelian', 'func' => 'Tabel tagihan ke supplier yang mendekati atau sudah melewati jatuh tempo, beserta total hutang usaha.']
                ],
                'buttons' => [
                    ['label' => 'Dropdown Periode (per widget)', 'func' => 'Setiap grafik (Tren Omset, Produk Terlaris) punya filter periode sendiri-sendiri di pojok kanan atasnya — bukan satu filter tanggal global untuk seluruh dashboard.'],
                    ['label' => 'Lihat Semua (Klasemen Kasir)', 'func' => 'Membuka modal berisi daftar lengkap peringkat kasir, tidak hanya 3 teratas.'],
                    ['label' => 'Lihat Semua (Piutang/Hutang)', 'func' => 'Mengarah ke halaman /finance/aging-report untuk melihat seluruh daftar piutang dan hutang, tidak hanya yang mendekati jatuh tempo.']
                ],
                'procedures' => [
                    ['title' => 'Memantau Omset & Transaksi Hari Ini', 'desc' => "1. Buka Dashboard, lihat dua kartu di kiri atas: **Omset Hari Ini** dan **Transaksi Hari Ini**.\n2. Badge hijau \"Aktif\" muncul kalau sudah ada transaksi hari itu; badge abu-abu \"Belum Ada Transaksi\" muncul kalau belum ada sama sekali.\n3. Angka ini otomatis terhitung dari transaksi Kasir (POS) dan diperbarui setiap kali halaman dimuat ulang."],
                    ['title' => 'Melihat Tren Omset', 'desc' => "1. Scroll ke kartu **Tren Omset Harian**.\n2. Gunakan dropdown di kanan atas untuk ganti tampilan: Harian, Mingguan, atau Bulanan.\n3. Arahkan kursor ke titik pada grafik untuk melihat nominal omset pada tanggal/periode tersebut."],
                    ['title' => 'Memeriksa Klasemen Penjualan Kasir', 'desc' => "1. Lihat kartu **Klasemen Penjualan Kasir** di sisi kanan — peringkat dihitung dari total omset penjualan tiap kasir pada bulan berjalan.\n2. Klik **Lihat Semua** untuk membuka daftar lengkap seluruh kasir, tidak dibatasi 3 teratas saja."],
                    ['title' => 'Menganalisis Produk Terlaris & Paling Lambat', 'desc' => "1. Scroll ke bagian **Produk Paling Laku** dan **Produk Paling Lambat**.\n2. Ganti periode di dropdown masing-masing grafik (Harian/Mingguan/Bulanan/Tahunan) sesuai kebutuhan analisa.\n3. Gunakan info Produk Paling Lambat untuk memutuskan barang mana yang perlu didiskon atau dipromosikan sebelum kadaluarsa."],
                    ['title' => 'Memantau Piutang & Hutang Jatuh Tempo', 'desc' => "1. Scroll ke tabel **Piutang Jatuh Tempo** (kiri) dan **Hutang Pembelian** (kanan) di bagian bawah Dashboard.\n2. Baris berwarna merah dengan label **Overdue** artinya sudah melewati tanggal jatuh tempo dan perlu segera ditindaklanjuti.\n3. Klik **Lihat Semua** pada masing-masing tabel untuk membuka halaman lengkap Aging Report (/finance/aging-report)."]
                ],
                'form_fields' => [
                    ['name' => 'Filter Periode Grafik', 'description' => 'Dropdown per-widget (Harian/Mingguan/Bulanan/Tahunan) yang menentukan rentang data yang ditampilkan pada grafik tersebut saja.', 'required' => false]
                ]
            ],
            'master' => [
                'title' => 'Manajemen Produk (Master)',
                'image' => 'guide_products.png',
                'description' => 'Modul Master Data adalah jantung dari sistem ini: database produk, kategori, satuan & konversi, supplier, dan pelanggan. Konsistensi data di modul ini sangat penting karena dipakai langsung oleh Kasir (POS), Pengadaan, dan seluruh Laporan.',
                'screenshots' => [],
                'golden_rules' => [
                    'Kode **Barcode** dibuat otomatis saat Anda mengetik nama produk, tapi tetap bisa diedit manual — pastikan tetap unik.',
                    'Harga Jual, Harga Beli, dan Stok Minimum hanya bisa diubah oleh user dengan izin **"edit product price"** — kalau field-nya terlihat terkunci, itu memang dibatasi sesuai peran (role) akun Anda.',
                    '**Satuan & Konversi** (mis. 1 Box = 10 Strip) diatur di halaman terpisah (/master/product-units), bukan di form Tambah Produk.',
                    'Import berurutan: **Kategori & Satuan → Supplier → Pelanggan → baru Produk**, karena Produk butuh referensi yang sudah ada.'
                ],
                'sub_menus' => [
                    ['name' => 'Daftar Produk', 'func' => 'Halaman utama (/products) untuk melihat, mencari, menambah, dan mengubah data obat/barang.'],
                    ['name' => 'Kategori Produk', 'func' => 'Mengelompokkan produk berdasarkan jenis (/master/categories).'],
                    ['name' => 'Master Satuan', 'func' => 'Daftar nama satuan global seperti Tablet, Strip, Box, Karton (/master/units).'],
                    ['name' => 'Satuan & Konversi', 'func' => 'Mengatur hubungan/konversi antar satuan per produk, bisa berjenjang sampai 5 level (/master/product-units).'],
                    ['name' => 'Manajemen Supplier', 'func' => 'Daftar supplier/PBF tempat apotek melakukan pengadaan barang (/master/suppliers).'],
                    ['name' => 'Manajemen Pelanggan', 'func' => 'Database pelanggan tetap untuk dipakai di transaksi Kasir dan penjualan Tempo (/master/customers).']
                ],
                'buttons' => [
                    ['label' => 'Tambah', 'func' => 'Membuka form input baru — tersedia di semua halaman Master (Produk, Kategori, Satuan, Supplier, Pelanggan).'],
                    ['label' => 'Riwayat Harga (Ikon Mata)', 'func' => 'Pada Daftar Produk, menampilkan riwayat Harga Jual dan riwayat Harga Beli (per Penerimaan Barang/supplier) secara terpisah.'],
                    ['label' => 'Atur Satuan', 'func' => 'Di halaman Satuan & Konversi, membuka modal untuk menambah/mengubah tingkatan satuan sebuah produk.'],
                    ['label' => 'Import Excel', 'func' => 'Mengunggah data massal — selalu unduh Template dulu sebelum mengisi.'],
                    ['label' => 'Export Excel', 'func' => 'Mengunduh data yang sedang tampil ke file Excel.'],
                    ['label' => 'Edit / Hapus', 'func' => 'Mengubah atau menghapus data. Produk yang sudah punya riwayat transaksi (penjualan/stok) tidak bisa dihapus.']
                ],
                'procedures' => [
                    ['title' => 'Menambah Produk Baru', 'desc' => "1. Buka halaman <a href=\"/products\" class=\"text-blue-600 hover:underline font-bold\">Daftar Produk</a>, klik **Tambah**.\n2. Isi Nama Produk (minimal 3 karakter) — Kode Barcode akan otomatis terbentuk saat Anda mengetik.\n3. Pilih Kategori dan Satuan Dasar (satuan terkecil/eceran, misal Tablet).\n4. Cek/edit Barcode kalau perlu (harus unik).\n5. Isi Stok Minimum dan Harga Jual (field ini terkunci kalau akun Anda tidak punya izin ubah harga).\n6. Harga Beli dan Gambar Produk bersifat opsional.\n7. Klik **Simpan**."],
                    ['title' => 'Mengatur Satuan & Konversi', 'desc' => "1. Buka halaman <a href=\"/master/product-units\" class=\"text-blue-600 hover:underline font-bold\">Satuan & Konversi</a>.\n2. Cari produk, klik ikon pensil **Atur Satuan**.\n3. Pastikan Satuan Dasar (Terkecil) sudah benar.\n4. Klik **+ Tambah Satuan** untuk menambah satuan yang lebih besar, misal Box, lalu isi jumlah isinya (misal 1 Box = 10 Strip).\n5. Bisa ditambah lagi berjenjang (misal 1 Karton = 12 Box) — sistem otomatis menghitung total ke satuan terkecil.\n6. Klik Simpan."],
                    ['title' => 'Melihat Riwayat Harga Produk', 'desc' => "1. Di Daftar Produk, klik ikon mata (Riwayat Harga) pada baris produk.\n2. Tab **Riwayat Harga Jual** menampilkan perubahan harga jual dari waktu ke waktu.\n3. Tab **Riwayat Harga Beli** menampilkan histori harga modal per Penerimaan Barang/supplier — berguna untuk membandingkan harga antar PBF."],
                    ['title' => 'Menambahkan Supplier (Pemasok)', 'desc' => "1. Buka halaman <a href=\"/master/suppliers\" class=\"text-blue-600 hover:underline font-bold\">Manajemen Supplier</a>, klik **Tambah**.\n2. Isi Nama, Nama PIC (kontak sales), No. HP, dan Alamat — semuanya wajib diisi.\n3. Klik Simpan agar supplier ini bisa dipilih saat membuat Purchase Order."],
                    ['title' => 'Menambahkan Pelanggan', 'desc' => "1. Buka halaman <a href=\"/master/customers\" class=\"text-blue-600 hover:underline font-bold\">Manajemen Pelanggan</a>, klik **Tambah**.\n2. Isi Nama (wajib); No. HP dan Alamat bersifat opsional.\n3. Data ini akan muncul sebagai pilihan pelanggan saat transaksi Kasir memakai metode pembayaran Tempo."]
                ],
                'form_fields' => [
                    ['name' => 'Nama Produk', 'description' => 'Nama lengkap obat beserta dosisnya (Contoh: Paracetamol 500mg). Minimal 3 karakter.', 'required' => true],
                    ['name' => 'Barcode', 'description' => 'Kode unik produk, dibuat otomatis dari Kategori+Satuan+Nama+nomor urut saat mengetik nama, tapi tetap bisa diedit manual.', 'required' => true],
                    ['name' => 'Kategori', 'description' => 'Grup obat untuk pengelompokan dan pelaporan.', 'required' => true],
                    ['name' => 'Satuan Dasar', 'description' => 'Unit paling kecil/eceran produk, misal Tablet atau Pcs — jadi acuan seluruh konversi satuan.', 'required' => true],
                    ['name' => 'Stok Minimum', 'description' => 'Angka batas bawah (minimal 1) agar produk muncul di status "Menipis" pada halaman Stok.', 'required' => true],
                    ['name' => 'Harga Jual', 'description' => 'Harga ke pelanggan per satuan dasar (minimal 1). Hanya bisa diubah dengan izin edit harga.', 'required' => true],
                    ['name' => 'Harga Beli', 'description' => 'Harga modal per satuan dasar. Boleh dikosongkan saat membuat produk baru; nilainya akan ikut ter-update otomatis setiap ada Penerimaan Barang.', 'required' => false]
                ]
            ],
            'pos' => [
                'title' => 'Transaksi Kasir (POS)',
                'image' => 'guide_cashier.png',
                'description' => 'Modul Kasir (/cashier) melayani penjualan dengan cepat lewat keyboard maupun barcode scanner. Mendukung diskon per item, transaksi ditunda (draft), tiga metode pembayaran (Tunai/QRIS/Tempo), dan otomatis mengambil stok dari batch yang paling dekat kadaluarsa (FEFO).',
                'screenshots' => [],
                'golden_rules' => [
                    'Tekan **F2** untuk langsung fokus ke kolom pencarian/scan barcode, dan **F9** untuk langsung membuka modal pembayaran.',
                    'Metode pembayaran yang tersedia hanya **Tunai, QRIS, dan Tempo** — pastikan QR Code QRIS toko sudah diupload di halaman Pengaturan sebelum dipakai, kalau belum tombol konfirmasi QRIS akan terkunci.',
                    'Kalau pelanggan belum siap bayar, gunakan **Simpan** (jadi transaksi Draft/Pending) — stok tidak berkurang sampai transaksi benar-benar dibayar.',
                    'Sistem otomatis memotong stok dari batch dengan tanggal kadaluarsa paling dekat (FEFO) — kasir tidak perlu dan tidak bisa memilih batch manual.',
                    'Diskon per item hanya berbentuk **persentase (%)**, bukan nominal rupiah langsung.'
                ],
                'sub_menus' => [
                    ['name' => 'Pencarian & Grid Produk', 'func' => 'Cari produk lewat nama atau scan barcode (F2), tampil sebagai grid dengan filter kategori.'],
                    ['name' => 'Keranjang (Cart)', 'func' => 'Daftar item yang dibeli — qty, satuan (kalau produk punya konversi), diskon %, dan catatan per item.'],
                    ['name' => 'Daftar Transaksi Pending', 'func' => 'Melihat dan memulihkan transaksi yang sebelumnya disimpan sebagai draft.'],
                    ['name' => 'Modal Pembayaran', 'func' => 'Layar final memilih metode bayar (Tunai/QRIS/Tempo), input data pasien (opsional), dan cetak struk.']
                ],
                'buttons' => [
                    ['label' => 'Simpan', 'func' => 'Menyimpan keranjang sebagai transaksi Draft/Pending (stok aman, belum terpotong), keranjang lalu dikosongkan.'],
                    ['label' => 'Bayar Sekarang (F9)', 'func' => 'Membuka modal pembayaran untuk menyelesaikan transaksi — nonaktif kalau keranjang masih kosong.'],
                    ['label' => 'Diskon (%) per item', 'func' => 'Ikon pada tiap baris keranjang untuk memberi potongan harga dalam bentuk persentase (0-100%).'],
                    ['label' => 'Catatan per item', 'func' => 'Menambahkan keterangan (misal aturan pakai obat) yang akan ikut tercetak di struk di bawah nama produk.'],
                    ['label' => 'Hapus (Ikon Sampah)', 'func' => 'Mengeluarkan satu item dari keranjang.'],
                    ['label' => 'Cetak Struk Transaksi', 'func' => 'Tombol submit di modal pembayaran; setelah sukses membuka struk yang bisa langsung dicetak.']
                ],
                'procedures' => [
                    ['title' => 'Melayani Penjualan', 'desc' => "1. Tekan **F2** atau klik kolom pencarian, scan barcode atau ketik nama produk.\n2. Klik produk (atau navigasi pakai tombol panah + Enter) untuk memasukkannya ke keranjang.\n3. Sesuaikan Qty langsung di kolom keranjang bila perlu.\n4. Kalau produk punya beberapa satuan (misal Strip/Box), pilih satuan yang sesuai — harga otomatis menyesuaikan.\n5. Tambahkan Diskon (%) atau Catatan per item lewat ikon di baris tersebut kalau dibutuhkan.\n6. Klik **Bayar Sekarang** (atau tekan F9)."],
                    ['title' => 'Menyimpan Transaksi Sementara (Draft)', 'desc' => "1. Kalau pelanggan belum siap membayar, klik **Simpan** di bagian bawah keranjang.\n2. Transaksi masuk ke **Daftar Transaksi Pending** dan keranjang dikosongkan — stok TIDAK berkurang.\n3. Untuk melanjutkan, buka **Daftar Transaksi Pending**, klik **Pulihkan** pada transaksi terkait, keranjang akan terisi kembali seperti semula."],
                    ['title' => 'Menyelesaikan Pembayaran Tunai', 'desc' => "1. Di modal pembayaran, pilih metode **Tunai**.\n2. Masukkan nominal uang diterima (tersedia tombol saran cepat: uang pas, dibulatkan ke atas Rp50rb/Rp100rb).\n3. Sistem otomatis menghitung kembalian.\n4. Klik **Cetak Struk Transaksi**."],
                    ['title' => 'Menyelesaikan Pembayaran QRIS', 'desc' => "1. Pilih metode **QRIS** — kode QR toko akan tampil (harus sudah diupload lebih dulu di Pengaturan Toko).\n2. Opsional: unggah bukti pembayaran.\n3. Klik **Konfirmasi Pembayaran QRIS**."],
                    ['title' => 'Menjual dengan Tempo (Kredit/Piutang)', 'desc' => "1. Pilih metode **Tempo**.\n2. Pilih Pelanggan yang sudah terdaftar (atau tambah baru langsung dari sini).\n3. Isi Jangka Waktu Tempo (default 30 hari) — sisa tagihan akan tercatat sebagai Piutang dan muncul di Aging Report.\n4. Boleh diisi uang muka (DP) sebagian lewat kolom Tunai; sisanya otomatis jadi piutang.\n5. Klik **Cetak Struk Transaksi**."]
                ],
                'form_fields' => [
                    ['name' => 'Pencarian Produk', 'description' => 'Scan barcode atau ketik nama untuk memasukkan barang ke keranjang (tekan F2 untuk fokus cepat).', 'required' => false],
                    ['name' => 'Diskon Item (%)', 'description' => 'Potongan harga per baris item dalam bentuk persentase (0-100), bukan nominal langsung.', 'required' => false],
                    ['name' => 'Catatan Item', 'description' => 'Keterangan tambahan yang tercetak di struk di bawah nama produk (misal aturan pakai).', 'required' => false],
                    ['name' => 'Uang Diterima (Tunai)', 'description' => 'Nominal tunai yang diberikan pelanggan, dipakai untuk menghitung kembalian otomatis.', 'required' => true],
                    ['name' => 'Jangka Waktu Tempo', 'description' => 'Jumlah hari sebelum piutang penjualan Tempo jatuh tempo (default 30 hari).', 'required' => true]
                ]
            ],
            'stock' => [
                'title' => 'Stok & Inventori',
                'image' => 'guide_stock.png',
                'description' => 'Kelola persediaan barang berbasis batch dan tanggal kadaluarsa. Penjualan di Kasir otomatis mengikuti prinsip FEFO (First Expired First Out). Modul ini juga menangani koreksi stok manual serta retur penjualan/pembelian.',
                'screenshots' => [],
                'golden_rules' => [
                    'Sistem **tidak** punya fitur hitung stok fisik otomatis (stok-opname per batch) — kalau ketemu selisih setelah hitung manual di rak, koreksi lewat **Penyesuaian Stok**.',
                    'Penyesuaian Stok berlaku untuk **total stok produk**, bukan pilih batch tertentu — pengurangan otomatis memakai urutan FIFO (batch kadaluarsa terdekat dulu).',
                    'Tidak ada filter "Kadaluarsa" khusus di daftar stok — cek tanggal kadaluarsa tiap batch lewat halaman **Riwayat/Kartu Stok** per produk.',
                    'Retur Penjualan otomatis mengembalikan stok ke batch asal transaksi; Retur Pembelian mengurangi stok dari batch yang Anda pilih sendiri.'
                ],
                'sub_menus' => [
                    ['name' => 'Daftar Stok', 'func' => 'Halaman utama (/stock) menampilkan status tiap produk: Habis, Menipis, atau Aman.'],
                    ['name' => 'Riwayat / Kartu Stok', 'func' => 'Per produk (/stock/{id}/history) — daftar batch beserta tanggal kadaluarsa, dan log lengkap pergerakan stok masuk/keluar.'],
                    ['name' => 'Penyesuaian Stok', 'func' => 'Koreksi total stok produk (Tambah/Kurangi) dengan alasan (/stock/adjust/{id}).'],
                    ['name' => 'Retur Penjualan', 'func' => 'Mencatat barang yang dikembalikan pelanggan berdasarkan nomor invoice (/inventory/returns/sales).'],
                    ['name' => 'Retur Pembelian', 'func' => 'Mencatat barang yang dikembalikan ke supplier berdasarkan Penerimaan Barang (/inventory/returns/purchase).']
                ],
                'buttons' => [
                    ['label' => 'Semua / Menipis', 'func' => 'Tombol filter di Daftar Stok untuk menampilkan seluruh produk atau hanya yang stoknya di bawah Stok Minimum.'],
                    ['label' => 'Detail (Ikon Mata)', 'func' => 'Membuka halaman Riwayat/Kartu Stok produk tersebut — termasuk daftar batch & tanggal kadaluarsa.'],
                    ['label' => 'Penyesuaian Stok (Ikon Pensil Hijau)', 'func' => 'Membuka form koreksi stok untuk produk tersebut.'],
                    ['label' => 'Export Excel', 'func' => 'Mengunduh data stok saat ini (file "Stok-Opname-...xlsx") untuk arsip atau hitung fisik manual.'],
                    ['label' => 'Import Excel', 'func' => 'Mengunggah data stok awal secara massal (dipakai umumnya saat setup awal).']
                ],
                'procedures' => [
                    ['title' => 'Memeriksa Stok Menipis/Habis', 'desc' => "1. Buka halaman <a href=\"/stock\" class=\"text-blue-600 hover:underline font-bold\">Stok</a>.\n2. Klik filter **Menipis** untuk menampilkan hanya produk yang stoknya sudah di bawah Stok Minimum.\n3. Perhatikan badge status: merah (Habis), kuning (Menipis), hijau (Aman)."],
                    ['title' => 'Melakukan Penyesuaian Stok (Koreksi Manual)', 'desc' => "1. Hitung fisik stok di rak secara manual, lalu bandingkan dengan angka di sistem (bisa Export Excel dulu sebagai acuan).\n2. Kalau ada selisih, klik ikon **Penyesuaian Stok** (pensil hijau) pada produk terkait.\n3. Pilih Tipe: **Tambah** (kalau stok fisik lebih banyak) atau **Kurangi** (kalau lebih sedikit/rusak/hilang).\n4. Isi Jumlah Unit selisihnya dan Alasan (opsional, misal \"Selisih hitung fisik\", \"Rusak\").\n5. Klik Simpan — sistem otomatis pakai logika FIFO untuk menentukan batch mana yang dikurangi."],
                    ['title' => 'Mengecek Batch & Tanggal Kadaluarsa', 'desc' => "1. Buka Riwayat/Kartu Stok pada produk yang ingin dicek (ikon mata di Daftar Stok).\n2. Lihat daftar batch yang tersedia beserta tanggal kadaluarsanya (diurutkan dari yang paling dekat expired).\n3. Segera retur ke supplier atau promosikan produk yang mendekati tanggal kadaluarsa."],
                    ['title' => 'Memproses Retur Penjualan', 'desc' => "1. Buka <a href=\"/inventory/returns/sales\" class=\"text-blue-600 hover:underline font-bold\">Retur Penjualan</a>.\n2. Cari transaksi berdasarkan Nomor Invoice (minimal 3 karakter).\n3. Pilih invoice, lalu isi jumlah retur per item (dibatasi sisa qty yang belum pernah diretur sebelumnya).\n4. Simpan — stok otomatis kembali ke batch asal penjualan tadi."],
                    ['title' => 'Memproses Retur Pembelian', 'desc' => "1. Buka <a href=\"/inventory/returns/purchase\" class=\"text-blue-600 hover:underline font-bold\">Retur Pembelian</a>.\n2. Pilih Supplier, lalu pilih Penerimaan Barang (Goods Receipt) terkait.\n3. Centang item & batch yang mau diretur, isi jumlahnya (dibatasi stok batch tersebut) dan catatan.\n4. Simpan — stok berkurang dari batch yang dipilih tadi."]
                ],
                'form_fields' => [
                    ['name' => 'Tipe Penyesuaian', 'description' => 'Tambah (menaikkan stok) atau Kurangi (menurunkan stok) — bukan input angka akhir, tapi selisihnya.', 'required' => true],
                    ['name' => 'Jumlah Unit', 'description' => 'Banyaknya unit yang ditambah/dikurangi (minimal 1).', 'required' => true],
                    ['name' => 'Alasan', 'description' => 'Keterangan kenapa dilakukan koreksi stok (misal: Rusak, Hilang, Selisih Opname).', 'required' => false]
                ]
            ],
            'procurement' => [
                'title' => 'Pengadaan (Procurement)',
                'image' => 'guide_procurement.png',
                'description' => 'Modul untuk memesan barang ke supplier lewat Purchase Order (PO), lalu mencatat kedatangan fisiknya lewat Penerimaan Barang (Goods Receipt) — termasuk pencatatan hutang dan pembayarannya ke supplier.',
                'screenshots' => [],
                'golden_rules' => [
                    'PO hanya mencatat **apa dan berapa banyak** yang dipesan — harga beli baru diinput saat **Penerimaan Barang**, bukan saat membuat PO.',
                    'Status PO (Draf/Dipesan/Sebagian/Diterima/Dibatalkan) diubah manual lewat dropdown pada form PO — tidak ada tombol "kirim ke supplier" otomatis.',
                    'No. Batch dan Tanggal Kadaluarsa **wajib** diisi setiap Penerimaan Barang karena menentukan urutan FEFO saat produk dijual nanti.',
                    'Pembayaran hutang ke supplier dicatat langsung dari halaman **Daftar Penerimaan Barang** (tombol Bayar) — tidak ada halaman "Daftar Hutang" terpisah.'
                ],
                'sub_menus' => [
                    ['name' => 'Purchase Order (PO)', 'func' => 'Membuat dan memantau pesanan ke supplier (/procurement/purchase-orders).'],
                    ['name' => 'Penerimaan Barang', 'func' => 'Mencatat kedatangan barang fisik, harga beli, batch, dan status pembayaran ke supplier (/procurement/goods-receipts).']
                ],
                'buttons' => [
                    ['label' => 'Tambah Pesanan', 'func' => 'Membuka form pembuatan PO baru.'],
                    ['label' => 'Cetak PO', 'func' => 'Mencetak dokumen PO untuk dikirim ke sales supplier.'],
                    ['label' => 'Proses Penerimaan', 'func' => 'Ada di halaman detail PO (status Dipesan/Sebagian) — mengarah ke form Penerimaan Barang yang sudah terisi otomatis dari PO tersebut.'],
                    ['label' => 'Selesaikan Pesanan', 'func' => 'Ada di halaman detail PO berstatus Sebagian — memaksa PO ditandai selesai (Diterima) walau belum semua barang datang.'],
                    ['label' => 'Bayar', 'func' => 'Di Daftar Penerimaan Barang, muncul kalau status bayar belum Lunas — mencatat pembayaran hutang ke supplier.'],
                    ['label' => 'Cetak Surat Jalan', 'func' => 'Mencetak dokumen bukti terima barang dari Penerimaan Barang terkait.']
                ],
                'procedures' => [
                    ['title' => 'Membuat Purchase Order (PO)', 'desc' => "1. Buka <a href=\"/procurement/purchase-orders\" class=\"text-blue-600 hover:underline font-bold\">Purchase Order</a>, klik **Tambah Pesanan**.\n2. Nomor PO terisi otomatis; pilih Supplier dan Tanggal.\n3. Cari produk lewat kolom pencarian, lalu di modal yang muncul isi Qty dan Satuan pesanan.\n4. Ulangi untuk semua produk yang ingin dipesan, isi Catatan bila perlu.\n5. Klik Simpan, lalu **Cetak PO** untuk dikirim ke sales supplier."],
                    ['title' => 'Memproses Penerimaan Barang dari PO', 'desc' => "1. Saat barang datang, buka detail PO terkait (status Dipesan/Sebagian), klik **Proses Penerimaan**.\n2. Form Penerimaan Barang akan terisi otomatis dari PO — cek \"Sisa Order\" per item.\n3. Isi Nomor Surat Jalan, Tanggal Terima, dan Metode Pembayaran (Cash/Transfer/Tempo).\n4. Untuk tiap item: isi Qty Terima, No. Batch (ada saran otomatis), Tanggal Kadaluarsa, dan **Harga Beli** (di sinilah harga modal sebenarnya diinput).\n5. Klik Simpan — stok bertambah, PO otomatis berubah status jadi Sebagian atau Diterima tergantung kelengkapan qty."],
                    ['title' => 'Mencatat Penerimaan Barang Tanpa PO', 'desc' => "1. Buka <a href=\"/procurement/goods-receipts\" class=\"text-blue-600 hover:underline font-bold\">Penerimaan Barang</a>, klik **Tambah Penerimaan**.\n2. Isi form yang sama seperti di atas tanpa perlu memilih PO — cocok untuk pembelian mendadak/tanpa pemesanan formal."],
                    ['title' => 'Membayar Hutang ke Supplier', 'desc' => "1. Buka Daftar Penerimaan Barang, cari baris dengan status bayar **Hutang** atau **Setengah** (bukan Lunas).\n2. Klik tombol **Bayar** — muncul modal \"Catat Pembayaran Hutang\" dengan info Sisa Hutang.\n3. Isi Jumlah Bayar, Tanggal, dan Metode (Cash atau Transfer + pilih rekening bank).\n4. Klik Simpan — status bayar otomatis update (Setengah jika masih ada sisa, Lunas jika pas/lebih)."]
                ],
                'form_fields' => [
                    ['name' => 'Nomor Surat Jalan', 'description' => 'Nomor referensi dari dokumen fisik yang dibawa kurir/sales supplier.', 'required' => true],
                    ['name' => 'Metode Pembayaran', 'description' => 'Cash, Transfer (wajib pilih rekening bank), atau Tempo (wajib isi jangka waktu minggu).', 'required' => true],
                    ['name' => 'No. Batch', 'description' => 'Kode batch produksi dari supplier, dipakai untuk pelacakan FEFO. Sistem memberi saran otomatis, tetap bisa diedit.', 'required' => true],
                    ['name' => 'Tanggal Kadaluarsa', 'description' => 'Tanggal expired produk pada batch tersebut — sangat kritis, menentukan urutan penjualan FEFO.', 'required' => true],
                    ['name' => 'Harga Beli', 'description' => 'Harga modal per unit satuan yang diterima — di sinilah harga beli produk sebenarnya dicatat (bukan di PO).', 'required' => true]
                ]
            ],
            'reports' => [
                'title' => 'Laporan Keuangan',
                'image' => 'guide_finance.png',
                'description' => 'Kumpulan laporan operasional dan keuangan apotek, tersebar di beberapa grup menu: Laporan Keuangan (/finance/*), Laporan Operasional (/reports/*), dan Akuntansi (/accounting/*) untuk pembukuan lebih detail (Buku Besar, Jurnal, Daftar Akun).',
                'screenshots' => [],
                'golden_rules' => [
                    'Hati-hati, ada **2 menu berbeda dengan nama sama "Neraca Saldo Awal"**: satu di grup Laporan Keuangan adalah laporan Trial Balance (/finance/trial-balance), satu lagi di grup Keuangan & Administrasi adalah halaman untuk MENGISI saldo awal (/finance/opening-balance). Perhatikan baik-baik menu mana yang Anda klik.',
                    'Tidak semua laporan bisa diekspor ke Excel — Laporan PPN, Arus Kas, Neraca, dan Riwayat Transaksi Produk **hanya tersedia dalam PDF**.',
                    'Laporan Laba Rugi punya opsi ekspor **dengan atau tanpa Perbandingan Periode** — pilih sesuai kebutuhan sebelum download.',
                    'Filter periode berbeda-beda per laporan: Laporan PPN pakai Bulan & Tahun, Laporan Stok pakai rentang **tanggal kadaluarsa** (bukan tanggal transaksi), sedangkan Aging Report (Hutang & Piutang) tidak punya filter tanggal sama sekali (selalu real-time).'
                ],
                'sub_menus' => [
                    ['name' => 'Laporan Laba Rugi', 'func' => 'Pendapatan dikurangi HPP dan Beban Operasional, per periode (/finance/profit-loss).'],
                    ['name' => 'Neraca (Standar)', 'func' => 'Posisi Aset, Liabilitas, dan Ekuitas per tanggal tertentu (/finance/balance-sheet).'],
                    ['name' => 'Laporan Arus Kas', 'func' => 'Pergerakan kas dari aktivitas operasional, investasi, dan pendanaan (/finance/cash-flow).'],
                    ['name' => 'Laporan PPN', 'func' => 'Rekapitulasi PPN keluaran, difilter per Bulan & Tahun (/finance/ppn-report).'],
                    ['name' => 'Hutang & Piutang (Aging Report)', 'func' => 'Daftar piutang pelanggan dan hutang supplier dikelompokkan per umur tunggakan (/finance/aging-report).'],
                    ['name' => 'Laporan Stok', 'func' => 'Daftar stok dengan filter status dan rentang tanggal kadaluarsa (/reports/stock).'],
                    ['name' => 'Laporan Penjualan', 'func' => 'Rekap transaksi penjualan, bisa difilter per tanggal (/reports/sales).'],
                    ['name' => 'Laporan Margin Produk', 'func' => 'Margin keuntungan per produk — bisa mode "potensial" (harga saat ini) atau "realized" (transaksi terjual) (/reports/product-margin).'],
                    ['name' => 'Buku Besar', 'func' => 'Mutasi tiap akun akuntansi secara detail (/accounting/ledger).'],
                    ['name' => 'Daftar Akun (COA)', 'func' => 'Chart of Accounts — semua akun akuntansi yang dipakai sistem (/accounting/accounts).'],
                    ['name' => 'Pengeluaran (Buku Biaya)', 'func' => 'Catatan pengeluaran/pemasukan lain-lain operasional apotek (/finance/expenses).']
                ],
                'buttons' => [
                    ['label' => 'Export (dropdown Excel/PDF)', 'func' => 'Tersedia di sebagian besar laporan — cek dulu apakah laporan yang dibuka mendukung Excel atau PDF saja.'],
                    ['label' => 'Bulan Ini / Bulan Lalu / Tahun Ini', 'func' => 'Tombol pintas periode yang tersedia di beberapa laporan keuangan (Laba Rugi, Neraca, Trial Balance, Arus Kas, PPN).'],
                    ['label' => 'Filter Tanggal/Periode', 'func' => 'Menentukan rentang data laporan — jenis filternya berbeda per laporan (lihat Aturan Penting).']
                ],
                'procedures' => [
                    ['title' => 'Melihat Laba Rugi', 'desc' => "1. Buka <a href=\"/finance/profit-loss\" class=\"text-blue-600 hover:underline font-bold\">Laporan Laba Rugi</a>.\n2. Pilih periode, atau pakai tombol pintas Bulan Ini/Bulan Lalu/Tahun Ini.\n3. Sistem otomatis menghitung Pendapatan dikurangi HPP dan Beban Operasional menjadi Laba Bersih.\n4. Klik **Export** untuk unduh PDF/Excel — bisa pilih dengan atau tanpa Perbandingan Periode."],
                    ['title' => 'Mengecek Hutang & Piutang Jatuh Tempo', 'desc' => "1. Buka <a href=\"/finance/aging-report\" class=\"text-blue-600 hover:underline font-bold\">Hutang & Piutang</a>.\n2. Gunakan tab untuk beralih antara **Hutang (AP)** dan **Piutang (AR)**.\n3. Data dikelompokkan otomatis per umur tunggakan (0-7, 8-15, 16-30, 31-45, 45+ hari).\n4. Aktifkan toggle untuk menampilkan tagihan yang sudah lunas kalau perlu."],
                    ['title' => 'Mengunduh Laporan Penjualan', 'desc' => "1. Buka <a href=\"/reports/sales\" class=\"text-blue-600 hover:underline font-bold\">Laporan Penjualan</a>.\n2. Filter tanggal yang diinginkan.\n3. Klik tombol **Export**, pilih format Excel atau PDF."],
                    ['title' => 'Mengecek Stok Mendekati Kadaluarsa', 'desc' => "1. Buka <a href=\"/reports/stock\" class=\"text-blue-600 hover:underline font-bold\">Laporan Stok</a>.\n2. Isi rentang **Tanggal Kadaluarsa** (bukan tanggal transaksi) untuk melihat batch yang akan expired dalam periode tersebut.\n3. Bisa juga difilter berdasarkan status (Semua/Menipis/Habis) dan Kategori."],
                    ['title' => 'Melihat Mutasi Akun (Buku Besar)', 'desc' => "1. Buka <a href=\"/accounting/ledger\" class=\"text-blue-600 hover:underline font-bold\">Buku Besar</a>.\n2. Pilih akun yang ingin dilihat detailnya.\n3. Sistem menampilkan seluruh mutasi debit/kredit akun tersebut beserta saldo berjalan (running balance)."]
                ],
                'form_fields' => [
                    ['name' => 'Rentang Tanggal', 'description' => 'Tanggal awal dan akhir untuk memfilter laporan (Laba Rugi, Penjualan, Margin Produk, dll).', 'required' => false],
                    ['name' => 'Bulan & Tahun', 'description' => 'Filter khusus untuk Laporan PPN, karena PPN direkap per periode bulanan.', 'required' => true],
                    ['name' => 'Rentang Tanggal Kadaluarsa', 'description' => 'Filter khusus Laporan Stok untuk melihat batch yang akan expired dalam rentang tersebut.', 'required' => false]
                ]
            ],
            'profile' => [
                'title' => 'Pengaturan Profil',
                'image' => 'guide_profile.png',
                'description' => 'Halaman /profile berisi 3 kartu terpisah (bukan tab): Informasi Profil, Keamanan Akun, dan Hapus Akun.',
                'screenshots' => [],
                'golden_rules' => [
                    'Gunakan **Password Kuat** dan jangan bagikan akun Anda kepada staf lain — tiap staf sebaiknya punya akun sendiri (lihat panduan Manajemen Sistem) supaya riwayat aktivitas tetap jelas siapa mengerjakan apa.',
                    'Foto profil dibatasi maksimal **1MB**, format gambar saja.',
                    'Mengubah Email akan mereset status verifikasi email akun Anda.',
                    'Hati-hati dengan kartu **Hapus Akun** — tindakan ini permanen dan meminta konfirmasi password sebelum akun benar-benar dihapus.'
                ],
                'sub_menus' => [
                    ['name' => 'Informasi Profil', 'func' => 'Mengubah Foto, Nama, dan Email akun Anda.'],
                    ['name' => 'Keamanan Akun', 'func' => 'Mengganti password dengan memasukkan password lama dan password baru.'],
                    ['name' => 'Hapus Akun', 'func' => 'Menghapus akun secara permanen setelah konfirmasi password (tindakan tidak bisa dibatalkan).']
                ],
                'buttons' => [
                    ['label' => 'Pilih Foto Baru', 'func' => 'Mengunggah foto profil baru (maks 1MB), langsung tampil pratinjau sebelum disimpan.'],
                    ['label' => 'Simpan', 'func' => 'Tersedia di kartu Informasi Profil dan Keamanan Akun — menerapkan perubahan masing-masing.'],
                    ['label' => 'Hapus Akun', 'func' => 'Tombol merah di kartu paling bawah, membuka modal konfirmasi password sebelum akun dihapus permanen.']
                ],
                'procedures' => [
                    ['title' => 'Mengubah Nama, Email, atau Foto', 'desc' => "1. Buka halaman <a href=\"/profile\" class=\"text-blue-600 hover:underline font-bold\">Profil</a>.\n2. Di kartu **Informasi Profil**, klik **Pilih Foto Baru** untuk ganti foto (opsional), atau langsung ubah kolom Nama/Email.\n3. Klik **Simpan** — muncul pesan \"Tersimpan.\" kalau berhasil."],
                    ['title' => 'Mengganti Password', 'desc' => "1. Scroll ke kartu **Keamanan Akun**.\n2. Isi Password Saat Ini, lalu Password Baru dan Konfirmasi Password Baru.\n3. Klik **Simpan**."],
                    ['title' => 'Menghapus Akun', 'desc' => "1. Scroll ke kartu **Hapus Akun** paling bawah.\n2. Klik tombol **Hapus Akun**.\n3. Masukkan password Anda saat ini di modal konfirmasi untuk memverifikasi.\n4. Konfirmasi — akun akan dihapus permanen dan Anda otomatis logout."]
                ],
                'form_fields' => [
                    ['name' => 'Password Saat Ini', 'description' => 'Sandi yang sedang aktif, dipakai untuk verifikasi sebelum mengganti password baru atau menghapus akun.', 'required' => true],
                    ['name' => 'Password Baru', 'description' => 'Kata sandi baru sesuai standar keamanan default Laravel (minimal 8 karakter).', 'required' => true],
                    ['name' => 'Foto Profil', 'description' => 'Gambar profil, maksimal ukuran file 1MB.', 'required' => false]
                ]
            ],
            'settings' => [
                'title' => 'Manajemen Sistem',
                'image' => 'guide_settings.png',
                'description' => 'Khusus Super Admin/Admin dengan izin terkait. Mengatur identitas toko, konfigurasi kasir, akun staf & hak akses, backup database, dan log aktivitas sistem.',
                'screenshots' => [],
                'golden_rules' => [
                    'Backup database berjalan **otomatis setiap hari jam 01:00** dan disimpan 30 hari terakhir — tapi tetap disarankan sesekali unduh manual dan simpan di tempat lain (komputer/cloud pribadi) sebagai jaga-jaga.',
                    '**Tidak ada tombol Restore di aplikasi** — kalau perlu memulihkan database dari file backup, harus dilakukan manual lewat phpMyAdmin/MySQL di luar aplikasi (hubungi developer/IT jika perlu bantuan).',
                    'Ada 4 role bawaan sistem yang tidak bisa dihapus/diganti nama: **super-admin, admin, kasir, gudang**. Kalau butuh peran lain (misal Apoteker), buat role baru sendiri di Manajemen Role dengan hak akses custom.',
                    'Selain lewat Role, hak akses juga bisa di-override per user secara individual di halaman edit user.',
                    'Kategori Pengeluaran diatur di menu tersendiri (**/finance/expense-categories**, grup Keuangan & Administrasi), bukan di halaman Pengaturan Toko.'
                ],
                'sub_menus' => [
                    ['name' => 'Pengaturan Toko', 'func' => 'Identitas Toko, Informasi Pembayaran & Footer Invoice, dan Pengaturan Pajak (/settings/store).'],
                    ['name' => 'Pengaturan POS', 'func' => 'Ukuran kertas struk, mode PPN, dan tarif PPN default kasir (/settings/pos).'],
                    ['name' => 'Manajemen User', 'func' => 'Daftar staf, aktif/nonaktifkan akun, dan fitur Impersonate (masuk sebagai user lain) khusus Super Admin (/admin/users).'],
                    ['name' => 'Manajemen Role', 'func' => 'Membuat/mengubah peran beserta matriks izin per menu (/admin/roles).'],
                    ['name' => 'Backup Database', 'func' => 'Membuat backup manual, mengunduh, atau menghapus file backup lama (/admin/backups).'],
                    ['name' => 'Log Aktivitas', 'func' => 'Riwayat semua aksi penting di sistem, bisa difilter per user/modul/tanggal (/admin/activity-log).']
                ],
                'buttons' => [
                    ['label' => 'Simpan Perubahan', 'func' => 'Tombol di bagian bawah Pengaturan Toko/POS untuk menerapkan semua perubahan sekaligus.'],
                    ['label' => 'Aktif / Nonaktif (toggle)', 'func' => 'Di Manajemen User, menonaktifkan akun staf tanpa perlu menghapusnya (staf tidak bisa lagi login). Anda tidak bisa menonaktifkan akun Anda sendiri.'],
                    ['label' => 'Impersonate', 'func' => 'Khusus Super Admin — masuk sementara sebagai user lain untuk membantu troubleshooting, ada tombol "Kembali" untuk keluar dari mode ini.'],
                    ['label' => 'Buat Backup Sekarang', 'func' => 'Memicu backup database manual kapan saja, di luar jadwal otomatis harian.'],
                    ['label' => 'Unduh / Hapus (Backup)', 'func' => 'Mengunduh file backup (.sql.gz) ke komputer Anda, atau menghapus file backup lama dari server.']
                ],
                'procedures' => [
                    ['title' => 'Mengubah Identitas & Pajak Toko', 'desc' => "1. Buka <a href=\"/settings/store\" class=\"text-blue-600 hover:underline font-bold\">Pengaturan Toko</a>.\n2. Bagian **Identitas Toko**: isi Nama Apotek, Alamat, Telepon, Email, NPWP, dan upload Logo Toko/Logo Login/Logo Sidebar (masing-masing terpisah).\n3. Bagian **Informasi Pembayaran & Footer Invoice**: isi Nama Bank, No. Rekening, Atas Nama, upload gambar QRIS, dan Catatan Kaki Struk.\n4. Bagian **Pengaturan Pajak**: pilih skema Manual atau UMKM Final (isi tarif PPN UMKM kalau pilih skema ini).\n5. Klik **Simpan Perubahan** di paling bawah — semua bagian tersimpan sekaligus."],
                    ['title' => 'Menambah Staf Baru', 'desc' => "1. Buka <a href=\"/admin/users\" class=\"text-blue-600 hover:underline font-bold\">Manajemen User</a>, klik **Tambah User**.\n2. Isi Nama, Email, Password.\n3. Pilih Role (super-admin/admin/kasir/gudang, atau role custom yang sudah dibuat).\n4. Atur juga izin per-menu individual kalau staf ini butuh akses khusus di luar role-nya.\n5. Klik Simpan — staf langsung bisa login."],
                    ['title' => 'Membuat Role/Peran Baru', 'desc' => "1. Buka <a href=\"/admin/roles\" class=\"text-blue-600 hover:underline font-bold\">Manajemen Role</a>, klik Tambah Role.\n2. Beri nama role (misal \"Apoteker\").\n3. Centang menu/izin apa saja yang boleh diakses role ini lewat matriks permission (bisa centang per grup sekaligus).\n4. Simpan — role baru ini langsung bisa dipilih saat menambah/edit user."],
                    ['title' => 'Membuat & Mengunduh Backup', 'desc' => "1. Buka <a href=\"/admin/backups\" class=\"text-blue-600 hover:underline font-bold\">Backup Database</a>.\n2. Klik **Buat Backup Sekarang** kalau ingin backup tambahan di luar jadwal otomatis.\n3. Klik **Unduh** pada file backup yang diinginkan, simpan di komputer/cloud pribadi Anda.\n4. File lebih dari 30 hari akan terhapus otomatis oleh sistem — unduh secara berkala kalau ingin menyimpan arsip lebih lama."]
                ],
                'form_fields' => [
                    ['name' => 'Role/Peran', 'description' => '4 role bawaan: super-admin, admin, kasir, gudang — atau role custom yang Anda buat sendiri di Manajemen Role.', 'required' => true],
                    ['name' => 'Skema Pajak', 'description' => 'Manual (isi PPN transaksi per kasus) atau UMKM Final (tarif PPN tetap sesuai aturan UMKM).', 'required' => true],
                    ['name' => 'Tarif PPN Default (POS)', 'description' => 'Persentase PPN default yang dipakai di Kasir, diatur di Pengaturan POS (bukan Pengaturan Toko).', 'required' => true]
                ]
            ],
            'user-manual' => [
                'title' => 'Buku Panduan Lengkap',
                'image' => 'guide_manual.png',
                'description' => 'Ringkasan alur penggunaan aplikasi apotek dari persiapan awal hingga operasional harian. Untuk detail lengkap tiap modul (tombol, field, dan langkah spesifik), buka panduan masing-masing modul — halaman ini hanya peta jalannya.',
                'screenshots' => [],
                'golden_rules' => [
                    'Ikuti urutan: **Setup Awal → Master Data → Saldo Awal → Procurement/Stok → baru Kasir (POS)** — jangan mulai jualan sebelum data dasar & saldo awal beres.',
                    'Harga Beli sebenarnya diinput saat **Penerimaan Barang** (bukan saat membuat PO), dan Tanggal Kadaluarsa wajib diisi tiap batch karena menentukan urutan FEFO.',
                    'Backup database berjalan **otomatis tiap hari jam 01:00** (disimpan 30 hari) — tapi tidak ada fitur Restore di aplikasi, jadi tetap unduh manual sesekali sebagai arsip pribadi.',
                    'Review **Laporan Laba Rugi** dan **Hutang & Piutang (Aging Report)** secara rutin, minimal tiap akhir bulan.'
                ],
                'sub_menus' => [
                    ['name' => 'Persiapan Awal', 'func' => 'Login pertama, Pengaturan Toko, Manajemen User, dan input Saldo Awal kas/bank/persediaan — lihat panduan "Setup Awal Aplikasi".'],
                    ['name' => 'Master Data', 'func' => 'Kategori, Master Satuan, Satuan & Konversi, Supplier, Pelanggan, dan Data Produk — lihat panduan "Manajemen Produk (Master)".'],
                    ['name' => 'Procurement', 'func' => 'Purchase Order dan Penerimaan Barang dari supplier, termasuk pembayaran hutang — lihat panduan "Pengadaan".'],
                    ['name' => 'Point of Sale', 'func' => 'Transaksi penjualan di Kasir: Tunai, QRIS, atau Tempo, plus transaksi draft — lihat panduan "Transaksi Kasir (POS)".'],
                    ['name' => 'Stok & Inventori', 'func' => 'Cek stok, kartu stok per batch, penyesuaian, dan retur penjualan/pembelian — lihat panduan "Stok & Inventori".'],
                    ['name' => 'Laporan & Akuntansi', 'func' => 'Dashboard, Laba Rugi, Neraca, Arus Kas, PPN, Aging Report, Buku Besar — lihat panduan "Laporan Keuangan".'],
                    ['name' => 'Administrasi', 'func' => 'Manajemen User & Role, Backup Database, Log Aktivitas — lihat panduan "Manajemen Sistem".']
                ],
                'buttons' => [
                    ['label' => 'Download PDF', 'func' => 'Mengunduh buku panduan lengkap dalam format PDF untuk dibaca offline atau dicetak.'],
                    ['label' => 'Print', 'func' => 'Mencetak halaman panduan yang sedang dibuka.']
                ],
                'procedures' => [
                    ['title' => 'Ringkasan: Setup Awal Aplikasi', 'desc' => "1. Login dengan akun Super Admin/Owner.\n2. Buka <a href=\"/settings/store\" class=\"text-blue-600 hover:underline font-bold\">Pengaturan Toko</a>, isi data apotek.\n3. Tambah staf di <a href=\"/admin/users\" class=\"text-blue-600 hover:underline font-bold\">Manajemen User</a>.\n4. Buka <a href=\"/finance/opening-balance\" class=\"text-blue-600 hover:underline font-bold\">Saldo Awal</a>, input Kas, Bank, Persediaan, dan Modal Awal, lalu kunci.\n5. Detail lengkap ada di panduan \"Setup Awal Aplikasi\"."],
                    ['title' => 'Ringkasan: Input Data Produk Pertama', 'desc' => "1. Buat Kategori di <a href=\"/master/categories\" class=\"text-blue-600 hover:underline font-bold\">Master → Kategori</a>.\n2. Pastikan nama Satuan yang dibutuhkan sudah ada di <a href=\"/master/units\" class=\"text-blue-600 hover:underline font-bold\">Master Satuan</a> (satuan tidak bisa diimpor Excel, harus dibuat manual).\n3. Tambah Supplier di <a href=\"/master/suppliers\" class=\"text-blue-600 hover:underline font-bold\">Master → Supplier</a>.\n4. Buat Produk di <a href=\"/products\" class=\"text-blue-600 hover:underline font-bold\">Master → Data Produk</a> — Barcode otomatis terbentuk saat mengetik nama.\n5. Atur konversi satuan (kalau perlu) di halaman Satuan & Konversi."],
                    ['title' => 'Ringkasan: Penerimaan Barang Pertama', 'desc' => "1. Buat PO di <a href=\"/procurement/purchase-orders\" class=\"text-blue-600 hover:underline font-bold\">Procurement → Purchase Order</a> (opsional, boleh dilewati).\n2. Buka <a href=\"/procurement/goods-receipts\" class=\"text-blue-600 hover:underline font-bold\">Procurement → Penerimaan Barang</a>, klik Tambah Penerimaan (atau Proses Penerimaan dari PO).\n3. Isi No. Surat Jalan dan Metode Pembayaran.\n4. Untuk tiap item, isi Qty, No. Batch, Tanggal Kadaluarsa, dan **Harga Beli** — ini yang menentukan modal produk.\n5. Simpan — stok otomatis bertambah."],
                    ['title' => 'Ringkasan: Transaksi Penjualan Pertama', 'desc' => "1. Buka <a href=\"/cashier\" class=\"text-blue-600 hover:underline font-bold\">Kasir (POS)</a>.\n2. Scan barcode atau cari nama produk (F2), klik untuk masuk keranjang.\n3. Atur qty/diskon/catatan per item bila perlu.\n4. Klik **Bayar Sekarang** (atau F9), pilih metode Tunai/QRIS/Tempo.\n5. Selesaikan pembayaran — struk otomatis siap dicetak."],
                    ['title' => 'Ringkasan: Melihat Laporan Keuangan', 'desc' => "1. Buka <a href=\"/finance/profit-loss\" class=\"text-blue-600 hover:underline font-bold\">Finance → Laba Rugi</a>.\n2. Pilih periode (atau pakai tombol pintas Bulan Ini/Bulan Lalu/Tahun Ini).\n3. Review Pendapatan, HPP, Beban, dan Laba Bersih.\n4. Export PDF/Excel kalau perlu untuk arsip."]
                ],
                'form_fields' => []
            ],
            'import-migration' => [
                'title' => 'Migrasi Data (Excel)',
                'image' => 'guide_migration.png',
                'description' => 'Import Excel BUKAN halaman tersendiri — tombolnya menempel di masing-masing halaman Master/Laporan yang relevan (Produk, Kategori, Supplier, Pelanggan, Stok, Daftar Akun, Kategori Pengeluaran, dan Omset Historis), masing-masing dengan template Excel-nya sendiri.',
                'screenshots' => [],
                'golden_rules' => [
                    'Selalu **Download Template Excel** dari tombol Import di halaman terkait — jangan pakai file lama atau buat format sendiri.',
                    'Jangan mengubah **Nama Kolom** (Header) pada baris pertama file Excel.',
                    'Import Produk dan Stok bersifat **upsert**: baris yang datanya sudah ada (dikenali dari barcode/nama) akan otomatis diperbarui, bukan gagal karena duplikat. Nama Kategori/Satuan yang belum ada di sistem juga otomatis dibuatkan.',
                    '**Tidak ada import untuk Satuan (Units)** — nama satuan (Strip, Box, dll) harus dibuat manual satu-satu di /master/units, karena hanya dipakai sebagai referensi oleh import Produk/Stok.',
                    'Kalau ada baris gagal, sebagian besar tipe import (Produk, Supplier, Stok, Akun, Omset) akan menyebutkan **nomor baris dan kolom yang bermasalah** langsung di pesan error — pakai info itu untuk perbaikan cepat.'
                ],
                'sub_menus' => [
                    ['name' => 'Import Produk', 'func' => 'Di halaman Daftar Produk (/products) — data obat, barcode, kategori, satuan, harga.'],
                    ['name' => 'Import Kategori', 'func' => 'Di halaman Kategori Produk (/master/categories).'],
                    ['name' => 'Import Supplier', 'func' => 'Di halaman Manajemen Supplier (/master/suppliers).'],
                    ['name' => 'Import Pelanggan', 'func' => 'Di halaman Manajemen Pelanggan (/master/customers) — bentuknya panel inline, bukan modal terpisah.'],
                    ['name' => 'Import Stok', 'func' => 'Di halaman Stok (/stock) — stok awal per produk beserta batch dan tanggal kadaluarsa.'],
                    ['name' => 'Import Daftar Akun (COA)', 'func' => 'Di halaman Daftar Akun (/accounting/accounts).'],
                    ['name' => 'Import Kategori Pengeluaran', 'func' => 'Di halaman Kategori Pengeluaran (/finance/expense-categories).'],
                    ['name' => 'Import Omset Historis', 'func' => 'Di bagian bawah halaman Laporan Penjualan (/reports/sales) — untuk mengisi data omset tahun/bulan lalu sebagai pembanding.']
                ],
                'buttons' => [
                    ['label' => 'Import / Import Excel', 'func' => 'Membuka modal (atau panel, khusus Pelanggan) berisi form upload untuk halaman tersebut.'],
                    ['label' => 'Download Template Excel', 'func' => 'Di dalam modal Import — mengunduh file contoh format yang benar khusus untuk data itu.'],
                    ['label' => 'Import (submit)', 'func' => 'Tombol final di dalam modal untuk memproses file yang sudah dipilih.']
                ],
                'procedures' => [
                    ['title' => 'Langkah Umum Migrasi Data', 'desc' => "1. Buka halaman data yang ingin diimpor (misal Master → Supplier).\n2. Klik tombol **Import Excel**.\n3. Di dalam modal, klik **Download Template Excel** untuk dapat format yang benar.\n4. Isi template tersebut (boleh copy-paste dari data lama Anda) — jangan ubah nama kolom header.\n5. Kembali ke aplikasi, klik **Import Excel** lagi, pilih file .xlsx/.xls yang sudah diisi.\n6. Klik **Import** dan tunggu notifikasi sukses."],
                    ['title' => 'Mengimpor Stok Awal', 'desc' => "1. Pastikan Produk sudah diimpor/dibuat lebih dulu.\n2. Buka <a href=\"/stock\" class=\"text-blue-600 hover:underline font-bold\">Stok</a>, klik **Import Excel**.\n3. Isi template: kolom produk, jumlah, No. Batch, dan Tanggal Kadaluarsa.\n4. Import — sistem otomatis membuat batch, memperbarui saldo Persediaan di Saldo Awal (kalau belum dikunci), dan memposting jurnal terkait."],
                    ['title' => 'Mengimpor Omset Historis', 'desc' => "1. Buka <a href=\"/reports/sales\" class=\"text-blue-600 hover:underline font-bold\">Laporan Penjualan</a>, cari bagian **Import Omset** di bawah.\n2. Download template, isi kolom Tanggal/Periode, Omset, HPP, dan Laba dari catatan lama Anda.\n3. Upload kembali — sistem membuat transaksi penjualan historis dan jurnal akuntansinya tanpa memengaruhi stok fisik saat ini (khusus untuk pembanding laporan, bukan transaksi nyata)."],
                    ['title' => 'Menangani Gagal Import', 'desc' => "1. Kalau ada baris error, sistem akan menampilkan pesan seperti \"Baris {nomor}.{kolom}: {keterangan error}\" untuk beberapa baris pertama yang bermasalah.\n2. Perbaiki data di file Excel Anda sesuai pesan tersebut (format nomor telepon, kode akun duplikat, dll).\n3. Simpan file, lalu ulangi proses Import — baris yang sudah berhasil sebelumnya tidak akan terduplikasi (sistem mendeteksi data yang sudah ada)."]
                ],
                'form_fields' => [
                    ['name' => 'File Excel', 'description' => 'File berformat .xlsx atau .xls sesuai Template yang diunduh dari halaman yang sama.', 'required' => true]
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
