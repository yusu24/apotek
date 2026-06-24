# BUKU PANDUAN PENGGUNAAN APLIKASI APOTEK

## Pendahuluan
Selamat datang di Aplikasi Manajemen Apotek. Sistem ini dirancang untuk memudahkan operasional apotek Anda, mulai dari manajemen master data obat, transaksi kasir (POS), pengelolaan persediaan (stok), pengadaan barang (procurement), hingga laporan keuangan lengkap.

---

## Bab 1: Persiapan Awal Sistem (Setup)
Sebelum memulai operasional transaksi harian, pastikan Anda telah menyelesaikan langkah-langkah berikut:
1. **Pengaturan Identitas Apotek**: Masuk ke menu **Sistem > Pengaturan Toko** untuk mengisi Nama Apotek, Alamat, No. Telepon, PPN default, dan Logo. Informasi ini akan tercetak pada struk kasir dan kop laporan PDF.
2. **Setup Saldo Awal**: Masuk ke menu **Finance > Opening Balance** (Saldo Awal) untuk memasukkan saldo awal akun Kas & Bank Anda.
3. **Mengatur Hak Akses**: Masuk ke menu **Admin > Users** untuk mendaftarkan akun staf apoteker dan kasir Anda dengan hak akses (Role) yang sesuai.

---

## Bab 2: Manajemen Master Data
Master Data adalah pondasi utama sistem. Pastikan data diinput dengan lengkap dan terstruktur:
1. **Kategori Obat**: Kelompokkan obat (misal: Obat Bebas, Obat Keras, Alat Kesehatan) melalui menu **Master > Kategori**.
2. **Satuan & Konversi**: Setup satuan terkecil (eceran) seperti *Tablet*, *Botol*, atau *Pcs*, serta satuan besar seperti *Box* atau *Strip* beserta konversinya (misal: 1 Box = 100 Tablet) melalui menu **Master > Satuan**.
3. **Data Supplier & Pelanggan**: Tambahkan daftar Pemasok (Supplier/PBF) dan Pelanggan tetap Anda.
4. **Data Obat / Produk**: Input detail produk, barcode, kategori, satuan dasar, harga jual, dan stok pengingat minimal melalui menu **Katalog Produk**.

---

## Bab 3: Transaksi Kasir (Point of Sale)
Modul Kasir (POS) digunakan untuk melayani penjualan pelanggan:
1. **Pencarian Produk**: Scan barcode obat atau ketik nama obat pada kolom pencarian.
2. **Pengaturan Kuantitas & Catatan**: Sesuaikan jumlah (Qty) pembelian, diskon per item (jika ada), dan isi catatan instruksi aturan pakai (misal: 3x1 setelah makan) yang akan tampil di struk.
3. **Metode Pembayaran**:
   * **Tunai (Cash)**: Masukkan nominal uang yang diterima untuk menghitung nilai kembalian secara otomatis.
   * **Transfer / QRIS**: Pilih opsi pembayaran non-tunai.
   * **Tempo**: Pilih untuk penjualan kredit kepada pelanggan/instansi terdaftar.
4. **Penyelesaian**: Klik **Bayar Sekarang** dan cetak struk belanja thermal untuk pelanggan.

---

## Bab 4: Pengadaan & Penerimaan Barang
Alur pengadaan barang dari supplier (PBF):
1. **Purchase Order (PO)**: Buat dokumen pesanan pembelian ke supplier di menu **Procurement > Purchase Order**.
2. **Penerimaan Barang (Goods Receipt)**:
   * Saat barang datang, buka menu **Procurement > Penerimaan Barang**.
   * Pilih PO terkait (atau buat Penerimaan Langsung jika tanpa PO).
   * Input **Nomor Surat Jalan / Faktur** fisik dari supplier.
   * Pilih **Metode Bayar** (Cash, Transfer, atau Jatuh Tempo). Jika memilih **Transfer**, wajib memilih akun bank pengeluaran dana.
   * Input detail barang yang diterima: **Nomor Batch, Expired Date, Jumlah (Qty), dan Harga Beli (Neto)**.
   * Klik **Simpan** untuk menambahkan stok secara otomatis ke sistem gudang dan memposting jurnal pembelian.

---

## Bab 5: Pengelolaan Stok & Inventori
Mengelola persediaan fisik obat-obatan agar terpantau dengan baik:
1. **Metode FEFO**: Sistem otomatis mengurutkan penjualan berdasarkan kadaluarsa terdekat (First Expired, First Out) demi menghindari kerugian obat kadaluarsa.
2. **Stok Opname**: Lakukan pencocokan stok fisik dengan sistem secara berkala di menu **Stok & Inventori**. Jika terdapat selisih, gunakan tombol **Sesuaikan Stok** (Adjustment) untuk mengoreksi angka di sistem dengan menyertakan alasan yang jelas.
3. **Kartu Stok**: Gunakan fitur kartu stok di setiap halaman produk untuk mengaudit riwayat pergerakan masuk-keluar barang secara kronologis.

---

## Bab 6: Keuangan, Akuntansi & Laporan
Sistem ini dilengkapi modul akuntansi otomatis yang memposting jurnal untuk setiap aktivitas:
1. **Laba Rugi**: Menampilkan pendapatan kotor, Harga Pokok Penjualan (HPP), biaya pengeluaran, dan laba bersih apotek Anda.
2. **Pengeluaran Operasional**: Catat pengeluaran kas operasional (seperti biaya listrik, air, sewa, gaji staf) di menu **Finance > Expenses** agar langsung memotong saldo kas/bank dan dihitung dalam laporan keuangan.
3. **Neraca & Arus Kas**: Menyediakan posisi aset, kewajiban, modal apotek, serta arus kas masuk-keluar secara real-time.
4. **Laporan Pajak (PPN)**: Rekapitulasi otomatis PPN Masukan (dari pembelian) dan PPN Keluaran (dari penjualan) untuk laporan pajak bulanan apotek.

---

## Bab 7: Pemeliharaan & Keamanan Data
1. **Backup Data**: Lakukan backup database secara rutin melalui menu **Admin > Backups**. Unduh file backup (.sql) ke penyimpanan eksternal (Flashdisk/Google Drive) demi mencegah kehilangan data akibat kendala komputer.
2. **Activity Log**: Super Admin dapat memantau seluruh aktivitas staf melalui log audit untuk memastikan integritas operasional.
