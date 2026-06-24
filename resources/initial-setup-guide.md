# PANDUAN SETUP AWAL APLIKASI APOTEK

Panduan ini disusun untuk membantu Anda melakukan konfigurasi awal sistem apotek dari nol agar siap digunakan sepenuhnya secara operasional, kasir, inventori, dan akuntansi keuangan. 

Sangat disarankan mengikuti langkah-langkah di bawah ini secara berurutan guna menghindari ketidakseimbangan jurnal akuntansi (seperti saldo kas/bank/persediaan menjadi minus).

---

## DAFTAR ALUR SETUP AWAL (STEP-BY-STEP)

| Langkah | Aktivitas Setup | Deskripsi | Link Halaman Sistem |
| :---: | :--- | :--- | :--- |
| **1** | [Pengaturan Toko](#langkah-1-pengaturan-identitas-apotek-toko) | Mengisi profil apotek, alamat, telp, dan PPN untuk cetak struk kasir & kop laporan. | [/settings/store](http://localhost:8000/settings/store) |
| **2** | [Hak Akses Staf](#langkah-2-manajemen-pengguna-user-dan-hak-akses-staf) | Mendaftarkan akun login staf kasir, apoteker, dan gudang dengan peran (role) masing-masing. | [/admin/users](http://localhost:8000/admin/users) |
| **3** | [Rekening & Akun (COA)](#langkah-3-setup-rekening-bank-dan-daftar-akun-coa) | Menyiapkan daftar akun akuntansi dan menambahkan akun bank jika ada lebih dari 1 rekening. | [/accounting/accounts](http://localhost:8000/accounting/accounts) |
| **4** | [Saldo Awal (Opening Balance)](#langkah-4-pengisian-saldo-awal-opening-balance---wajib) | **WAJIB:** Mengisi kas awal laci, saldo bank awal, dan taksiran total nilai persediaan obat awal agar tidak minus. | [/finance/opening-balance](http://localhost:8000/finance/opening-balance) |
| **5** | [Import Data Master](#langkah-5-import-data-master-secara-massal-via-excel) | Mengimpor database Kategori, Supplier, Pelanggan, dan Katalog Obat menggunakan Excel. | [/products](http://localhost:8000/products) |
| **6** | [Import Omset Historis](#langkah-6-import-omset-historis-4-tahun-kebelakang) | Mengunggah ringkasan omset & HPP tahun lalu untuk visualisasi grafik & laporan profit-loss historis. | [/reports/sales](http://localhost:8000/reports/sales) |

---

## RINCIAN LANGKAH OPERASIONAL

### Langkah 1: Pengaturan Identitas Apotek (Toko)
Sebelum melakukan transaksi apapun, lengkapi profil apotek Anda:
* **Halaman Akses**: [Identitas Apotek / Pengaturan Toko (http://localhost:8000/settings/store)](http://localhost:8000/settings/store)
* **Instruksi**:
  1. Isi **Nama Apotek**, **Alamat Lengkap**, dan **Nomor Telepon**.
  2. Tentukan **PPN Default** (misal: `11` untuk 11%).
  3. Unggah **Logo Apotek** Anda.
  4. Klik **Simpan**. 
  *Data ini otomatis digunakan sebagai kop dokumen laporan PDF dan struk belanja kasir (thermal).*

### Langkah 2: Manajemen Pengguna (User) dan Hak Akses Staf
Daftarkan akun login untuk seluruh staf apotek:
* **Halaman Akses**: [Manajemen User (http://localhost:8000/admin/users)](http://localhost:8000/admin/users)
* **Instruksi**:
  1. Klik tombol **Tambah User**.
  2. Isi Nama Staf, Email, dan Password awal mereka.
  3. Pilih Peran (**Role**) yang sesuai:
     * **Kasir**: Hanya bisa mengakses layar Kasir (POS) dan riwayat struk penjualan.
     * **Staf Gudang**: Hanya bisa melihat inventori obat, mencatat stok opname, dan membuat Purchase Order.
     * **Admin / Owner**: Akses penuh ke laporan keuangan, backup, dan manajemen user.
  4. Klik **Simpan**.

### Langkah 3: Setup Rekening Bank dan Daftar Akun (COA)
Sesuaikan daftar rekening bank yang digunakan untuk menerima transfer/QRIS/pembayaran supplier:
* **Halaman Akses**: [Daftar Akun / Chart of Accounts (http://localhost:8000/accounting/accounts)](http://localhost:8000/accounting/accounts)
* **Instruksi**:
  1. Sistem telah menyediakan akun utama secara default seperti *Kas (1-1100)*, *Bank (1-1200)*, dan *Persediaan Obat (1-1400)*.
  2. Jika Anda memiliki lebih dari 1 rekening bank operasional (misal: Bank BCA dan Bank Mandiri), klik tombol **Tambah Akun Bank**.
  3. Sistem akan otomatis men-generate kode rekening baru (misal: `1-1201`, `1-1202`, dst). Isi Nama Bank terkait dan simpan.

### Langkah 4: Pengisian Saldo Awal (Opening Balance) - [WAJIB]
Langkah ini **sangat penting** agar nilai aset Anda (Kas, Bank, dan Persediaan Obat) tidak bernilai minus ketika penjualan dicatat:
* **Halaman Akses**: [Saldo Awal / Opening Balance (http://localhost:8000/finance/opening-balance)](http://localhost:8000/finance/opening-balance)
* **Instruksi**:
  1. **Kas**: Masukkan nominal uang tunai fisik yang ada di laci kasir (uang modal awal laci).
  2. **Bank**: Masukkan saldo nominal rekening bank apotek Anda saat ini.
  3. **Persediaan Obat**: Masukkan taksiran nilai total modal (harga beli/neto) seluruh stok obat fisik yang saat ini ada di apotek Anda.
  4. **Modal Awal (Ekuitas)**: Di bagian kredit/ekuitas, isi nominal **Modal Awal (3-1000)** sebesar total penjumlahan Kas + Bank + Persediaan agar neraca seimbang (selisih Balanced menunjukkan angka `0`).
  5. Klik **Konfirmasi & Kunci Saldo Awal**. Sistem akan memposting saldo awal ke jurnal akuntansi secara otomatis.

### Langkah 5: Import Data Master Secara Massal via Excel
Masukkan database obat-obatan dan kontak supplier Anda sekaligus menggunakan template Excel:
* **Halaman Akses**:
  * [Master Kategori (http://localhost:8000/master/categories)](http://localhost:8000/master/categories)
  * [Master Supplier (http://localhost:8000/master/suppliers)](http://localhost:8000/master/suppliers)
  * [Katalog Produk (http://localhost:8000/products)](http://localhost:8000/products)
* **Instruksi**:
  1. **Urutan Import**: Wajib mengimpor **Kategori** dan **Supplier** terlebih dahulu sebelum mengimpor **Produk**. Jika tidak, data produk akan error karena tidak menemukan relasi supplier.
  2. Pada halaman masing-masing, klik tombol **Import Excel** -> **Download Template**.
  3. Isi file Excel tersebut sesuai dengan kolom yang disediakan (jangan mengubah nama kolom/header di baris pertama).
  4. Simpan file Excel Anda, kembali ke halaman sistem, lalu pilih file tersebut dan klik **Import**.

### Langkah 6: Import Omset Historis (4 Tahun ke Belakang)
Jika Anda ingin melihat visualisasi perbandingan grafik keuangan dan laporan Laba Rugi dari tahun-tahun sebelumnya:
* **Halaman Akses**: [Laporan Penjualan (http://localhost:8000/reports/sales)](http://localhost:8000/reports/sales)
* **Instruksi**:
  1. Klik tombol **Import Omset** di pojok kanan atas.
  2. Klik **Download Template** untuk mendapatkan format Excel.
  3. Isi data keuangan historis per baris. Format kolom terdiri dari: `tanggal` (atau `tahun`), `omset` (total pendapatan), `hpp` (nilai COGS), dan `laba` (otomatis terisi jika kosong).
  4. Unggah kembali file Excel tersebut.
  *Sistem akan otomatis memposting jurnal penjualan historis dan menghitung laba kotor, HPP, serta laba bersih untuk laporan tahun-tahun berjalan.*

---

## PROSEDUR OPERASIONAL HARIAN (SETELAH SETUP)

1. **Penerimaan Barang (Barang Masuk / Pembelian)**:
   Akses halaman [Penerimaan Barang (http://localhost:8000/procurement/goods-receipts)](http://localhost:8000/procurement/goods-receipts) untuk mencatat barang masuk dari Supplier. Mengisi detail *No. Batch*, *Expired Date*, *Harga Beli*, dan *Qty* akan menambah stok obat fisik dan mendebit akun **Persediaan Obat (1-1400)** secara otomatis.
2. **Transaksi Kasir (Barang Keluar / Penjualan)**:
   Akses halaman [Kasir / POS (http://localhost:8000/cashier)](http://localhost:8000/cashier) untuk melayani pembeli. Ketika pembayaran diselesaikan, sistem otomatis mengurangi stok obat (metode FEFO berdasarkan batch kadaluarsa terdekat) dan mengkredit akun **Persediaan Obat (1-1400)** berdasarkan HPP obat tersebut.
