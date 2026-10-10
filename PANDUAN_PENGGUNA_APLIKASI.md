# BUKU PANDUAN PENGGUNAAN APLIKASI (USER MANUAL)
## SISTEM INFORMASI MANAJEMEN APOTEK (SIM APOTEK)

---

## 1. Pendahuluan

Selamat datang di **Sistem Informasi Manajemen Apotek (SIM Apotek)**. Aplikasi ini dirancang khusus untuk memenuhi standar operasional apotek modern, mulai dari pelayanan kasir cepat (*Point of Sale*), manajemen persediaan obat berbasis **Batch & Kadaluarsa (FEFO)**, pengadaan barang, penagihan piutang pelanggan, hingga pembukuan akuntansi dan laporan laba rugi otomatis.

---

## 2. Langkah Setup Awal (Sebelum Operasional Dimulai)

Agar pembukuan apotek rapi dan tidak terjadi selisih saldo di kemudian hari, ikuti urutan setup awal berikut:

### Langkah 1: Mengatur Identitas Apotek
1. Masuk ke menu **Pengaturan** $\rightarrow$ **Pengaturan Toko**.
2. Masukkan **Nama Apotek**, **Alamat Lengkap**, dan **Nomor Telepon/WhatsApp**. Data ini akan otomatis tercetak pada kop struk kasir dan semua dokumen laporan PDF.
3. Masukkan **Tarif PPN Default** (misal: `11%` jika toko PKP, atau `0%` jika non-PKP).
4. Unggah **Logo Apotek** (format PNG/JPG transparan).
5. Klik **Simpan Pengaturan**.

### Langkah 2: Menyiapkan Rekening Bank Operasional
1. Buka menu **Akuntansi** $\rightarrow$ **Daftar Akun (COA)**.
2. Akun kas fisik dan akun standar sudah tersedia otomatis.
3. Jika apotek menerima pembayaran via transfer bank (BCA, Mandiri, BRI, dll), klik **Tambah Akun Bank**, masukkan nama bank dan nomor rekening, lalu klik **Simpan**.

### Langkah 3: Mengunci Saldo Awal (Opening Balance) — PENTING!
1. Buka menu **Keuangan** $\rightarrow$ **Saldo Awal**.
2. Masukkan jumlah uang tunai fisik yang ada di laci kasir pada kolom **Kas**.
3. Masukkan saldo masing-masing rekening bank pada kolom **Bank**.
4. Masukkan **Taksiran Nilai Persediaan** (total modal harga beli dari seluruh obat yang sudah ada di apotek saat sistem mulai dipakai).
5. Pada bagian Ekuitas, isi **Modal Awal** dengan angka yang **SAMA** dengan total (Kas + Bank + Persediaan) agar selisih Neraca menunjukkan angka `0 (Seimbang)`.
6. Klik **Konfirmasi & Kunci Saldo Awal**. Setelah dikunci, saldo awal akan menjadi pondasi neraca apotek.

### Langkah 4: Mengimpor Data Master (Gunakan Template Excel)
Lakukan import data master secara **berurutan** agar tidak terjadi kegagalan relasi:
1. **Kategori & Satuan Obat** (Box, Strip, Botol, Tablet).
2. **Data Supplier / PBF** (Pedagang Besar Farmasi).
3. **Data Pelanggan / Pasien Tetap**.
4. **Data Produk Obat** (Nama produk, kategori, satuan, harga jual, stok minimum).
5. **Stok Awal Obat** (Jumlah stok awal wajib mencantumkan **Nomor Batch** dan **Tanggal Kadaluarsa**).

---

## 3. Modul Kasir & Penjualan (Point of Sale / POS)

### 3.1. Melayani Penjualan Obat Tunai (Cash)
1. Buka menu **Kasir (POS)**.
2. Cari nama obat atau *scan* barcode produk.
3. Pilih satuan penjualan (misal: Eceran/Tablet atau Box).
4. Sistem otomatis memilih **Batch dengan tanggal kadaluarsa terdekat (FEFO)** yang stoknya masih tersedia.
5. Masukkan jumlah (qty) yang dibeli pelanggan.
6. Masukkan nominal uang yang dibayarkan pelanggan pada kolom **Bayar Tunai**.
7. Klik **Proses Transaksi / Cetak Struk**. Sistem otomatis mencetak nota dan memotong stok obat.

### 3.2. Melayani Penjualan Tempo (Kredit / Piutang)
Penjualan tempo digunakan untuk pelanggan tetap, klinik rekanan, atau instansi yang membayar belakangan:
1. Pada form kasir, wajib **Pilih Pelanggan** yang sudah terdaftar.
2. Pada bagian Metode Pembayaran, pilih **Tempo**.
3. Masukkan **Jangka Waktu Tempo** (default: 30 hari).
4. Jika pelanggan membayar uang muka (DP), masukkan nominalnya di kolom **Tunai**. Sisa tagihan otomatis dihitung sebagai **Piutang Usaha**.
5. Klik **Simpan Transaksi**.
6. Transaksi ini otomatis tercatat di menu **Hutang & Piutang (Aging Report)** untuk ditagih saat jatuh tempo.

### 3.3. Penjualan Non-Tunai (Transfer Bank & QRIS)
1. Pilih metode pembayaran **Transfer** atau **QRIS**.
2. Pilih rekening bank penerima.
3. Masukkan nominal bayar dan nomor referensi mutasi jika diperlukan, lalu selesaikan transaksi.

---

## 4. Modul Retur Penjualan (Pengembalian Obat dari Pembeli)

Jika terjadi kesalahan nota atau pelanggan mengembalikan obat:
1. Buka menu **Stok & Inventori** $\rightarrow$ **Retur Penjualan**.
2. Klik tombol **Tambah Retur**.
3. Cari nomor nota/invoice yang bersangkutan.
4. Tentukan jumlah barang yang dikembalikan dan alasan retur.
5. Klik **Simpan Retur**.

### Dampak Otomatis Sistem saat Retur Disimpan:
* **Stok Obat:** Otomatis bertambah kembali ke nomor batch asalnya.
* **Jika Nota Awal adalah Penjualan Tunai:** Sistem mencatat pengembalian dana tunai dari kasir ke pelanggan.
* **Jika Nota Awal adalah Penjualan Tempo:** Sistem otomatis **memotong sisa tagihan piutang pelanggan**. Jika retur mencakup seluruh nota, tagihan di daftar piutang otomatis berubah menjadi **Lunas/Selesai** dan hilang dari daftar penagihan aktif.

> **Tips Membaca Laporan Penjualan saat Terjadi Retur:**
> Jika nota dibuat kemarin (misal tanggal 7) lalu diretur hari ini (tanggal 8), maka pada Laporan Penjualan **hanya untuk tanggal 8**, nilai retur tersebut akan memotong penjualan hari itu. Bila penjualan hari ini lebih kecil daripada barang kemarin yang diretur, Penjualan Bersih harian akan tampak minus. Hal ini wajar dalam akuntansi harian. Untuk melihat pendapatan riil sebenarnya, ubah filter tanggal laporan menjadi rentang tanggal (misal: 07 s/d 08), maka nominalnya akan impas dan normal kembali.

---

## 5. Modul Pengadaan Barang (Procurement) & Pembelian

### 5.1. Surat Pesanan / Purchase Order (PO)
1. Buka menu **Pengadaan** $\rightarrow$ **Purchase Order**.
2. Klik **Buat PO Baru**, pilih supplier PBF yang dituju.
3. Tambahkan daftar obat dan jumlah yang ingin dipesan.
4. Cetak surat pesanan dalam format **A4** atau **NCR** untuk diserahkan ke sales PBF.

### 5.2. Penerimaan Barang dari Supplier (Surat Jalan / Faktur)
1. Saat pesanan obat fisik tiba dari supplier, buka menu **Pengadaan** $\rightarrow$ **Penerimaan Barang**.
2. Klik **Terima Barang** (bisa ditarik otomatis dari nomor PO).
3. Masukkan **Nomor Surat Jalan / Faktur PBF**.
4. Masukkan **Nomor Batch**, **Tanggal Kadaluarsa (Expired Date)**, dan **Harga Beli Riil** per obat.
5. Pilih metode pembayaran:
   * **Tunai / Transfer:** Jika langsung dibayar lunas saat barang datang.
   * **Tempo:** Jika pembayaran memiliki jangka waktu kredit. Sisa tagihan otomatis masuk ke daftar **Hutang Supplier (AP)**.
6. Klik **Simpan**. Stok obat bertambah dan kartu stok terupdate otomatis.

### 5.3. Pembayaran Hutang Supplier
1. Buka menu **Keuangan** $\rightarrow$ **Hutang & Piutang (Aging Report)** pada tab **Hutang Supplier (AP)**.
2. Cari surat jalan/faktur yang ingin dibayar, klik tombol **Bayar**.
3. Masukkan jumlah pembayaran, tanggal bayar, dan sumber kas/bank.
4. Klik **Simpan**. Sisa hutang berkurang dan status diperbarui otomatis.

---

## 6. Modul Stok, Inventori, dan Kadaluarsa

### 6.1. Pemantauan Stok & Sistem FEFO
* **Stok Menipis:** Obat yang berada di bawah batas stok minimum akan ditandai dengan lencana kuning/merah agar admin gudang segera membuat PO.
* **Sistem FEFO (First Expired First Out):** Sistem memprioritaskan penjualan obat yang tanggal kadaluarsanya paling dekat, meminimalisir risiko obat basi di rak apotek.
* **Alert Kadaluarsa:** Sistem memberikan peringatan visual untuk obat-obatan yang mendekati tanggal expired (30, 60, 90 hari ke depan).

### 6.2. Stock Opname & Penyesuaian Stok
1. Buka menu **Stok & Inventori** $\rightarrow$ **Penyesuaian Stok (Adjust)** pada produk yang dihitung.
2. Masukkan jumlah stok fisik sebenarnya di rak.
3. Masukkan alasan penyesuaian (misal: barang pecah, selisih hitung, obat rusak).
4. Klik **Simpan**. Sistem otomatis menyesuaikan saldo stok dan mencatat selisihnya pada jurnal akuntansi penyesuaian persediaan.

### 6.3. Kartu Stok Obat
Klik ikon **Riwayat Stok / Kartu Stok** pada produk untuk melihat jejak rekam pergerakan obat: dari pembelian mana barang masuk, kasir mana yang menjual, atau retur mana yang terjadi secara detail.

---

## 7. Modul Keuangan & Biaya Operasional (Expenses)

### 7.1. Mencatat Biaya Operasional
Gunakan modul ini untuk mencatat semua pengeluaran di luar pembelian obat:
1. Buka menu **Keuangan** $\rightarrow$ **Biaya / Pengeluaran (Expenses)**.
2. Klik **Tambah Pengeluaran**.
3. Pilih Kategori Biaya (misal: Biaya Listrik & Air, Gaji Karyawan, Biaya Kebersihan, Biaya Sewa Tempat, ATK Struk).
4. Pilih sumber dana pembayaran (Kas Laci atau Transfer Bank).
5. Masukkan nominal dan lampirkan catatan/bukti nota jika ada.
6. Klik **Simpan**. Pengeluaran ini otomatis memotong kas dan langsung terhitung pada **Laporan Laba Rugi**.

### 7.2. Manajemen Aset Tetap Apotek
1. Buka menu **Keuangan** $\rightarrow$ **Aset Tetap**.
2. Masukkan barang modal apotek (misal: Kulkas Vaksin, Komputer Kasir, Rak Etalase, AC).
3. Tentukan nilai perolehan dan masa manfaat ekonomisnya. Sistem akan menghitung penyusutan nilai aset secara otomatis setiap bulan.

---

## 8. Laporan-Laporan Manajemen & Ekspor

Seluruh laporan dapat difilter berdasarkan rentang tanggal dan diunduh dalam format **PDF siap cetak** atau **Excel (.xlsx)**:

1. **Laporan Penjualan:** Ringkasan omset kotor, potongan diskon, retur penjualan, dan penjualan riil per kasir atau metode pembayaran.
2. **Laporan Margin Produk:** Menganalisis keuntungan kotor dan persentase profit per item obat untuk mengetahui produk paling menguntungkan.
3. **Laporan Laba Rugi (Income Statement):** Mengkalkulasi Pendapatan Bersih dikurangi HPP (Modal Obat) menghasilkan Laba Kotor, lalu dikurangi Biaya Operasional menghasilkan **Laba Bersih Apotek**.
4. **Laporan Umur Hutang & Piutang (Aging Report):** Mengelompokkan jatuh tempo tagihan pelanggan dan hutang ke supplier dalam kategori usia tunggakan (0-7, 8-15, 16-30, 31-45, 45+ hari).
5. **Laporan Arus Kas (Cash Flow):** Menampilkan perputaran arus uang masuk dan keluar dari operasional, investasi, dan pendanaan apotek.
6. **Laporan PPN (Pajak Pertambahan Nilai):** Rekapitulasi PPN Masukan (dari faktur beli PBF) dan PPN Keluaran (dari penjualan kasir) beserta status Kurang/Lebih Bayar pajak.

---

## 9. Manajemen Pengguna, Hak Akses, & Keamanan

### 9.1. Tingkatan Hak Akses (Role Permission)
* **Super Admin / Owner:** Memiliki akses penuh ke seluruh menu, termasuk pengaturan toko, daftar akun bank, kunci saldo awal, manajemen gaji/beban, dan laporan laba rugi.
* **Admin:** Mengelola master data, penerimaan barang, stok opname, dan laporan operasional.
* **Apoteker:** Bertanggung jawab atas verifikasi resep, stok obat, penyesuaian batch, dan pengadaan obat ke PBF.
* **Kasir:** Terbatas hanya untuk membuka kasir POS, melayani pembayaran, mencetak struk, dan melihat riwayat transaksi shift kasir yang bersangkutan.
* **Gudang:** Khusus untuk memproses penerimaan barang fisik dari supplier dan mengecek stok di gudang.

### 9.2. Backup Database Rutin
1. Buka menu **Admin** $\rightarrow$ **Backup Database**.
2. Klik **Buat Backup Sekarang** untuk mengunduh arsip database apotek ke komputer lokal.
3. Sistem juga dikonfigurasi untuk mengirimkan salinan cadangan database otomatis ke email owner setiap minggu.

---

## 10. Lembar Pengesahan & Berita Acara Serah Terima (BAST)

Pada hari ini, ..................... Tanggal ..... Bulan .................. Tahun ............, telah diserahkan sistem aplikasi dan buku panduan operasional Sistem Informasi Manajemen Apotek (SIM Apotek) kepada pihak Klien:

| Pihak | Identitas & Peran |
|---|---|
| **PIHAK PERTAMA (Pengembang)** | Nama: ....................................................<br>Jabatan: Lead Developer / Konsultan Sistem |
| **PIHAK KEDUA (Klien / Pemilik Apotek)** | Nama: ....................................................<br>Jabatan: Pemilik Apotek (Owner) / Apoteker Penanggung Jawab |

Kedua belah pihak menyatakan bahwa sistem aplikasi telah selesai diserahterimakan, diuji fungsi, dan siap dioperasikan.

<br>

| PIHAK PERTAMA (Pengembang) | PIHAK KEDUA (Klien / Owner) |
|:---:|:---:|
| <br><br><br><br>____________________________<br>**( .................................................. )** | <br><br><br><br>____________________________<br>**( .................................................. )** |

